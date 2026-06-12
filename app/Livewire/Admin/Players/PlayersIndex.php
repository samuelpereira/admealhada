<?php

namespace App\Livewire\Admin\Players;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Player;
use App\Models\Team;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Jogadores')]
class PlayersIndex extends Component
{
    use HandlesImageUpload;

    #[Url]
    public string $team = '';

    public function delete(Player $player): void
    {
        $this->deleteImage($player->foto);
        $player->delete();

        Flux::toast(variant: 'success', text: 'Jogador eliminado.');
    }

    public function render()
    {
        $players = Player::query()
            ->with('team')
            ->when($this->team !== '', fn ($query) => $query->where('team_id', $this->team))
            ->orderBy('team_id')
            ->orderBy('ordem')
            ->orderBy('numero')
            ->get();

        return view('livewire.admin.players.index', [
            'players' => $players,
            'teams' => Team::orderBy('ordem')->orderBy('nome')->get(),
        ]);
    }
}
