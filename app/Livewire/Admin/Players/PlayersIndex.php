<?php

namespace App\Livewire\Admin\Players;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Jogadores')]
class PlayersIndex extends Component
{
    use HandlesImageUpload;

    #[Url(as: 'epoca')]
    public string $epoca = '';

    #[Url]
    public string $team = '';

    public function mount(): void
    {
        if ($this->epoca === '') {
            $current = Season::current();
            $this->epoca = $current === null ? '' : $current->slug;
        }

        $this->forgetTeamOutsideSeason();
    }

    public function updatedEpoca(): void
    {
        $this->forgetTeamOutsideSeason();
    }

    public function delete(Player $player): void
    {
        $this->deleteImage($player->foto);
        $player->delete();

        Flux::toast(variant: 'success', text: 'Jogador eliminado.');
    }

    public function render(): View
    {
        $players = Player::query()
            ->with('team')
            ->when($this->epoca !== '', fn ($query) => $query->whereHas(
                'team',
                fn ($teamQuery) => $teamQuery->whereHas(
                    'season',
                    fn ($seasonQuery) => $seasonQuery->where('slug', $this->epoca),
                ),
            ))
            ->when($this->team !== '', fn ($query) => $query->where('team_id', $this->team))
            ->orderBy('team_id')
            ->orderBy('ordem')
            ->orderBy('numero')
            ->get();

        return view('livewire.admin.players.index', [
            'players' => $players,
            'teams' => $this->teamsForSeason(),
            'epocas' => Season::query()->orderByDesc('starts_at')->get(),
        ]);
    }

    /**
     * @return Collection<int, Team>
     */
    private function teamsForSeason(): Collection
    {
        return Team::query()
            ->forAdminLists()
            ->when($this->epoca !== '', fn ($query) => $query->whereHas(
                'season',
                fn ($seasonQuery) => $seasonQuery->where('slug', $this->epoca),
            ))
            ->get();
    }

    private function forgetTeamOutsideSeason(): void
    {
        if ($this->team === '') {
            return;
        }

        $belongsToSeason = $this->teamsForSeason()->contains(
            fn (Team $team): bool => (string) $team->id === $this->team,
        );

        if (! $belongsToSeason) {
            $this->team = '';
        }
    }
}
