<?php

namespace App\Livewire\Admin\Players;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Player;
use App\Models\Team;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Jogador')]
class PlayerForm extends Component
{
    use HandlesImageUpload, WithFileUploads;

    public ?Player $player = null;

    public ?int $team_id = null;

    public string $nome = '';

    public ?string $numero = null;

    public string $posicao = '';

    public ?int $ano_nascimento = null;

    public string $papel = 'jogador';

    public int $ordem = 0;

    public $foto = null;

    public ?string $fotoAtual = null;

    public array $papeis = ['jogador', 'treinador', 'treinador-adjunto', 'delegado', 'fisioterapeuta'];

    public function mount(?Player $player = null, ?string $team = null): void
    {
        if ($player && $player->exists) {
            $this->player = $player;
            $this->team_id = $player->team_id;
            $this->nome = $player->nome;
            $this->numero = $player->numero;
            $this->posicao = $player->posicao ?? '';
            $this->ano_nascimento = $player->ano_nascimento;
            $this->papel = $player->papel;
            $this->ordem = $player->ordem ?? 0;
            $this->fotoAtual = $player->foto;
        } else {
            $this->team_id = $team ? (int) $team : Team::query()->orderBy('ordem')->value('id');
        }
    }

    protected function rules(): array
    {
        return [
            'team_id' => ['required', 'exists:teams,id'],
            'nome' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:10'],
            'posicao' => ['nullable', 'string', 'max:50'],
            'ano_nascimento' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y'))],
            'papel' => ['required', 'string', 'max:50'],
            'ordem' => ['integer', 'min:0'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function removerFoto(): void
    {
        $this->foto = null;

        if ($this->fotoAtual) {
            $this->deleteImage($this->fotoAtual);
            $this->fotoAtual = null;

            if ($this->player) {
                $this->player->update(['foto' => null]);
            }
        }
    }

    public function save()
    {
        $validated = $this->validate();

        $data = collect($validated)->except('foto')->toArray();

        if ($this->foto) {
            $this->deleteImage($this->fotoAtual);
            $data['foto'] = $this->storeImage($this->foto, 'jogadores');
        }

        if ($this->player) {
            $this->player->update($data);
        } else {
            Player::create($data);
        }

        Flux::toast(variant: 'success', text: 'Jogador guardado.');

        return $this->redirectRoute('admin.players.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.players.form', [
            'teams' => Team::orderBy('ordem')->orderBy('nome')->get(),
        ]);
    }
}
