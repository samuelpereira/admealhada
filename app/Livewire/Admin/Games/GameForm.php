<?php

namespace App\Livewire\Admin\Games;

use App\Models\Game;
use App\Models\Team;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Jogo')]
class GameForm extends Component
{
    public ?Game $game = null;

    public ?int $team_id = null;

    public string $adversario = '';

    public ?string $data_hora = null;

    public string $local = '';

    public string $casa_fora = 'casa';

    public string $competicao = '';

    public ?int $jornada = null;

    public string $estado = 'agendado';

    public ?int $pontos_casa = null;

    public ?int $pontos_fora = null;

    /** @var array<int, string> */
    public array $estados = ['agendado', 'terminado', 'adiado', 'cancelado'];

    public function mount(?Game $game = null): void
    {
        if ($game && $game->exists) {
            $this->game = $game;
            $this->team_id = $game->team_id;
            $this->adversario = $game->adversario;
            $this->data_hora = $game->data_hora->format('Y-m-d\TH:i');
            $this->local = $game->local ?? '';
            $this->casa_fora = $game->casa_fora;
            $this->competicao = $game->competicao ?? '';
            $this->jornada = $game->jornada;
            $this->estado = $game->estado;
            $this->pontos_casa = $game->pontos_casa;
            $this->pontos_fora = $game->pontos_fora;
        } else {
            $this->team_id = Team::query()->forAdminLists()->value('id');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'team_id' => ['required', 'exists:teams,id'],
            'adversario' => ['required', 'string', 'max:255'],
            'data_hora' => ['required', 'date'],
            'local' => ['nullable', 'string', 'max:255'],
            'casa_fora' => ['required', 'in:casa,fora'],
            'competicao' => ['nullable', 'string', 'max:255'],
            'jornada' => ['nullable', 'integer', 'min:1', 'max:50'],
            'estado' => ['required', 'in:agendado,terminado,adiado,cancelado'],
            'pontos_casa' => ['nullable', 'integer', 'min:0', 'max:300'],
            'pontos_fora' => ['nullable', 'integer', 'min:0', 'max:300'],
        ];
    }

    public function save(): mixed
    {
        $validated = $this->validate();
        $validated['data_hora'] = Carbon::parse($validated['data_hora']);

        if ($validated['estado'] !== 'terminado') {
            $validated['pontos_casa'] = null;
            $validated['pontos_fora'] = null;
        }

        if ($this->game) {
            $this->game->update($validated);
        } else {
            Game::create($validated);
        }

        Flux::toast(variant: 'success', text: 'Jogo guardado.');

        return $this->redirectRoute('admin.games.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.games.form', [
            'teams' => Team::query()->forAdminLists()->get(),
        ]);
    }
}
