<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\Fpb\FpbGamesImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Throwable;

class ImportFpbGames extends Command
{
    protected $signature = 'fpb:import
        {team? : ID ou slug da equipa (omitir para importar todas as equipas com link FPB)}
        {--keep-removed : Não eliminar registos importados que já não constam na FPB}
        {--games-only : Importar apenas o calendário/resultados}
        {--players-only : Importar apenas o plantel}';

    protected $description = 'Importa o calendário, resultados e plantel das equipas a partir da página da FPB';

    public function handle(FpbGamesImporter $importer): int
    {
        $teams = $this->resolveTeams();

        if ($teams->isEmpty()) {
            $this->warn('Nenhuma equipa com link FPB definido.');

            return self::SUCCESS;
        }

        $prune = ! $this->option('keep-removed');
        $doGames = ! $this->option('players-only');
        $doPlayers = ! $this->option('games-only');
        $erros = 0;

        foreach ($teams as $team) {
            $this->line("→ {$team->nome}");

            if ($doGames) {
                try {
                    $s = $importer->import($team, $prune);
                    $this->info(sprintf('  Jogos:   %d · %d novos · %d atualizados · %d removidos', $s['total'], $s['created'], $s['updated'], $s['deleted']));
                } catch (Throwable $e) {
                    $erros++;
                    $this->error('  Jogos:   '.$e->getMessage());
                }
            }

            if ($doPlayers) {
                try {
                    $s = $importer->importPlayers($team, $prune);
                    $this->info(sprintf('  Plantel: %d · %d novos · %d atualizados · %d removidos', $s['total'], $s['created'], $s['updated'], $s['deleted']));
                } catch (Throwable $e) {
                    $erros++;
                    $this->error('  Plantel: '.$e->getMessage());
                }
            }
        }

        return $erros > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return Collection<int, Team>
     */
    protected function resolveTeams()
    {
        $arg = $this->argument('team');

        if ($arg !== null) {
            $team = Team::query()
                ->where('id', $arg)
                ->orWhere('slug', $arg)
                ->first();

            if (! $team) {
                $this->error("Equipa \"{$arg}\" não encontrada.");

                return collect();
            }

            return collect([$team]);
        }

        return Team::query()->whereNotNull('fpb_url')->orderBy('ordem')->get();
    }
}
