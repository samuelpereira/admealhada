<?php

namespace App\Services\Fpb;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class FpbGamesImporter
{
    protected const MESES = [
        'JAN' => 1, 'FEV' => 2, 'MAR' => 3, 'ABR' => 4, 'MAI' => 5, 'JUN' => 6,
        'JUL' => 7, 'AGO' => 8, 'SET' => 9, 'OUT' => 10, 'NOV' => 11, 'DEZ' => 12,
    ];

    /**
     * Fetch the FPB team page, parse it and upsert the games for the team.
     *
     * @return array{created:int, updated:int, deleted:int, total:int}
     */
    public function import(Team $team, bool $prune = true): array
    {
        if (! $team->fpb_url) {
            throw new RuntimeException("A equipa \"{$team->nome}\" não tem link FPB definido.");
        }

        $html = $this->fetch($team->fpb_url);
        $jogos = $this->parse($html);

        if (empty($jogos)) {
            throw new RuntimeException('Não foram encontrados jogos na página (estrutura inesperada ou página vazia).');
        }

        // O clube desta página é a equipa presente em (praticamente) todos os jogos.
        $clube = $this->detectClub($jogos);

        $created = 0;
        $updated = 0;
        $seenIds = [];

        foreach ($jogos as $jogo) {
            $emCasa = $jogo['home'] === $clube;
            $emFora = $jogo['away'] === $clube;

            // Ignora jogos onde o clube não participa (segurança).
            if (! $emCasa && ! $emFora) {
                continue;
            }

            $seenIds[] = $jogo['id'];

            $game = Game::firstOrNew([
                'team_id' => $team->id,
                'fpb_id' => $jogo['id'],
            ]);

            $temResultado = $jogo['pc'] !== null && $jogo['pf'] !== null;

            $attributes = [
                'adversario' => $emCasa ? $jogo['away'] : $jogo['home'],
                'data_hora' => $this->dataHoraImportada($game, $jogo),
                'local' => $jogo['loc'],
                'casa_fora' => $emCasa ? 'casa' : 'fora',
                'competicao' => $jogo['comp'],
                'pontos_casa' => $temResultado ? $jogo['pc'] : null,
                'pontos_fora' => $temResultado ? $jogo['pf'] : null,
                'estado' => $temResultado ? 'terminado' : 'agendado',
            ];

            $exists = $game->exists;
            $game->fill($attributes);
            $dirty = $game->isDirty();
            $game->save();

            if (! $exists) {
                $created++;
            } elseif ($dirty) {
                $updated++;
            }
        }

        $deleted = 0;

        if ($prune) {
            $deleted = Game::query()
                ->where('team_id', $team->id)
                ->whereNotNull('fpb_id')
                ->whereNotIn('fpb_id', $seenIds)
                ->delete();
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'total' => count($seenIds),
        ];
    }

    /**
     * Fetch the FPB team page and upsert the roster (players) for the team.
     *
     * @return array{created:int, updated:int, deleted:int, total:int}
     */
    public function importPlayers(Team $team, bool $prune = true): array
    {
        if (! $team->fpb_url) {
            throw new RuntimeException("A equipa \"{$team->nome}\" não tem link FPB definido.");
        }

        $html = $this->fetch($team->fpb_url);
        $jogadores = $this->parsePlayers($html);

        if (empty($jogadores)) {
            throw new RuntimeException('Não foi encontrado plantel na página (estrutura inesperada ou sem atletas).');
        }

        $created = 0;
        $updated = 0;
        $seenIds = [];

        foreach ($jogadores as $j) {
            $seenIds[] = $j['fpb_id'];

            $player = Player::firstOrNew([
                'team_id' => $team->id,
                'fpb_id' => $j['fpb_id'],
            ]);

            if (! $player->exists) {
                $player->fill([
                    'nome' => $j['nome'],
                    'numero' => $j['numero'] !== '' ? (int) $j['numero'] : null,
                    'posicao' => $j['posicao'] !== '' ? $j['posicao'] : null,
                    'papel' => 'jogador',
                    'ordem' => 0,
                    'ativo' => true,
                    'foto' => $j['foto_url'] ? $this->downloadImage($j['foto_url'], 'jogadores') : null,
                ]);
                $player->save();
                $created++;

                continue;
            }

            // Atualiza apenas dados oficiais; preserva foto, papel, ordem,
            // visibilidade e posição introduzidos manualmente (a FPB nem sempre os fornece).
            $player->nome = $j['nome'];
            $player->numero = $j['numero'] !== '' ? (int) $j['numero'] : null;

            if ($j['posicao'] !== '') {
                $player->posicao = $j['posicao'];
            }

            if ($player->isDirty()) {
                $player->save();
                $updated++;
            }
        }

        $deleted = 0;

        if ($prune) {
            $stale = Player::query()
                ->where('team_id', $team->id)
                ->whereNotNull('fpb_id')
                ->whereNotIn('fpb_id', $seenIds)
                ->get();

            foreach ($stale as $player) {
                $this->deleteLocalImage($player->foto);
                $player->delete();
                $deleted++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'total' => count($seenIds),
        ];
    }

    /**
     * A finished FPB fixture often has no kickoff time. Keep the time already stored.
     *
     * @param  array<string, mixed>  $jogo
     */
    protected function dataHoraImportada(Game $game, array $jogo): CarbonInterface
    {
        /** @var Carbon $parsed */
        $parsed = $jogo['data_hora'];

        if ($game->exists && $jogo['time'] === null) {
            return $game->data_hora->copy()->setDate($parsed->year, $parsed->month, $parsed->day);
        }

        return $parsed;
    }

    /**
     * Parse the roster section of the FPB team page.
     *
     * @return array<int, array<string, string>>
     */
    public function parsePlayers(string $html): array
    {
        $segments = explode('<div class="roster__player">', $html);
        array_shift($segments);

        $jogadores = [];

        foreach ($segments as $seg) {
            if (! preg_match('#/atletas/(\d+)#', $seg, $idm)) {
                continue;
            }

            preg_match('/header__heading">\s*(.*?)\s*<\/h2>/su', $seg, $nome);
            preg_match('/header_position">\s*(.*?)\s*<\/div>/su', $seg, $pos);
            preg_match('/header_jnumber">\s*(\d*)/su', $seg, $num);
            preg_match('/roster__player__bust"\s+src="([^"]*)"/su', $seg, $bust);

            if (empty($nome[1])) {
                continue;
            }

            $jogadores[$idm[1]] = [
                'fpb_id' => $idm[1],
                'nome' => $this->clean($nome[1]),
                'posicao' => isset($pos[1]) ? $this->clean($pos[1]) : '',
                'numero' => $num[1] ?? '',
                'foto_url' => $bust[1] ?? '',
            ];
        }

        return array_values($jogadores);
    }

    /**
     * Download a remote image to the public disk and return its stored path.
     */
    protected function downloadImage(string $url, string $directory): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);

            if (! $response->successful() || $response->body() === '') {
                return null;
            }

            $ext = pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $path = $directory.'/'.Str::random(40).'.'.$ext;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Delete a locally stored image (ignores external URLs).
     */
    protected function deleteLocalImage(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://', '/']) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Determine the club this page belongs to: the team that takes part in
     * the most fixtures (it appears in every game of its own page).
     *
     * @param  array<int, array<string, mixed>>  $jogos
     */
    public function detectClub(array $jogos): string
    {
        $counts = [];

        foreach ($jogos as $jogo) {
            $counts[$jogo['home']] = ($counts[$jogo['home']] ?? 0) + 1;
            $counts[$jogo['away']] = ($counts[$jogo['away']] ?? 0) + 1;
        }

        arsort($counts);

        return (string) array_key_first($counts);
    }

    /**
     * Download the raw HTML of the FPB team page.
     */
    public function fetch(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            'Accept-Language' => 'pt-PT,pt;q=0.9',
        ])->timeout(30)->retry(2, 500)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Falha ao obter a página FPB (HTTP {$response->status()}).");
        }

        return $response->body();
    }

    /**
     * Parse the FPB team page HTML into a list of fixtures.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $html): array
    {
        preg_match_all('/<h3 class="date">\s*(.*?)\s*<\/h3>/su', $html, $dateMatches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $games = [];
        $count = count($dateMatches);

        for ($i = 0; $i < $count; $i++) {
            $offset = $dateMatches[$i][0][1];
            $end = $i + 1 < $count ? $dateMatches[$i + 1][0][1] : strlen($html);
            $block = substr($html, $offset, $end - $offset);

            if (! preg_match('/(\d{1,2})\s+([A-Z]{3})\s+(20\d\d)/u', $dateMatches[$i][1][0], $dm)) {
                continue;
            }

            $dia = (int) $dm[1];
            $mes = self::MESES[$dm[2]] ?? null;
            $ano = (int) $dm[3];

            if (! $mes) {
                continue;
            }

            $segments = explode('href="/ficha-de-jogo?internalID=', $block);
            array_shift($segments);

            foreach ($segments as $seg) {
                if (! preg_match('/^(\d+)/', $seg, $idm)) {
                    continue;
                }
                $id = $idm[1];

                preg_match_all('/<span class="fullName">\s*(.*?)\s*<\/span>/su', $seg, $names);
                if (count($names[1]) < 2) {
                    continue;
                }
                $home = $this->clean($names[1][0]);
                $away = $this->clean($names[1][1]);

                preg_match_all('/results_text[^>]*>\s*(\d+)/su', $seg, $scores);
                preg_match('/class="hour[^"]*">\s*<h3>\s*(\d{1,2})H(\d{2})/su', $seg, $hour);
                preg_match('/location-wrapper.*?<b>\s*(.*?)\s*<\/b>/su', $seg, $loc);
                preg_match('/class="competition">\s*<span>\s*(.*?)\s*<\/span>/su', $seg, $comp);

                $game = $games[$id] ?? [
                    'id' => $id,
                    'ano' => $ano, 'mes' => $mes, 'dia' => $dia,
                    'home' => $home, 'away' => $away,
                    'loc' => null, 'comp' => null,
                    'pc' => null, 'pf' => null, 'time' => null,
                ];

                if (isset($loc[1])) {
                    $game['loc'] = str_replace(' ,', ', ', $this->clean($loc[1]));
                }
                if (isset($comp[1])) {
                    $parts = explode('|', $this->clean($comp[1]));
                    $game['comp'] = trim(end($parts));
                }
                if (count($scores[1]) >= 2 && $game['pc'] === null) {
                    $game['pc'] = (int) $scores[1][0];
                    $game['pf'] = (int) $scores[1][1];
                }
                if ($hour && ! $game['time']) {
                    $game['time'] = sprintf('%02d:%s', (int) $hour[1], $hour[2]);
                }

                $games[$id] = $game;
            }
        }

        return array_map(function ($g) {
            $time = $g['time'] ?? '00:00';
            $g['data_hora'] = Carbon::create($g['ano'], $g['mes'], $g['dia'], (int) substr($time, 0, 2), (int) substr($time, 3, 2));

            return $g;
        }, array_values($games));
    }

    protected function clean(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value));
    }
}
