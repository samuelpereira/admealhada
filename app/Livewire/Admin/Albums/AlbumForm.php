<?php

namespace App\Livewire\Admin\Albums;

use App\Livewire\Concerns\GeneratesSlugs;
use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Album;
use App\Models\Team;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Galeria')]
class AlbumForm extends Component
{
    use GeneratesSlugs, HandlesImageUpload, WithFileUploads;

    public ?Album $album = null;

    public ?int $team_id = null;

    public string $titulo = '';

    public string $descricao = '';

    public ?string $data = null;

    /** @var TemporaryUploadedFile|null */
    public $capa = null;

    public ?string $capaAtual = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $novasFotos = [];

    /** @var array<int, string|null> */
    public array $legendas = [];

    public function mount(?Album $album = null): void
    {
        if ($album && $album->exists) {
            $this->album = $album;
            $album->load('photos');
            $this->team_id = $album->team_id;
            $this->titulo = $album->titulo;
            $this->descricao = $album->descricao ?? '';
            $this->data = $album->data?->format('Y-m-d');
            $this->capaAtual = $album->capa;

            foreach ($album->photos as $photo) {
                $this->legendas[$photo->id] = $photo->legenda;
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'team_id' => ['nullable', 'exists:teams,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data' => ['nullable', 'date'],
            'capa' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function removerCapa(): void
    {
        $this->capa = null;

        if ($this->capaAtual) {
            $this->deleteImage($this->capaAtual);
            $this->capaAtual = null;

            if ($this->album) {
                $this->album->update(['capa' => null]);
            }
        }
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        $data = [
            'team_id' => $validated['team_id'] ?: null,
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'] ?? null,
            'data' => $validated['data'] ? Carbon::parse($validated['data']) : null,
            'slug' => $this->uniqueSlug($this->titulo, 'albums', $this->album?->id),
        ];

        if ($this->capa) {
            $this->deleteImage($this->capaAtual);
            $data['capa'] = $this->storeImage($this->capa, 'galerias');
            $this->capaAtual = $data['capa'];
            $this->capa = null;
        }

        if ($this->album) {
            $this->album->update($data);
        } else {
            $this->album = Album::create($data);
        }

        Flux::toast(variant: 'success', text: 'Galeria guardada.');

        return $this->redirectRoute('admin.albums.edit', $this->album, navigate: true);
    }

    public function guardarFotos(): void
    {
        if (! $this->album) {
            Flux::toast(variant: 'danger', text: 'Guarde primeiro a galeria.');

            return;
        }

        $this->validate([
            'novasFotos' => ['required', 'array'],
            'novasFotos.*' => ['image', 'max:6144'],
        ]);

        $ordem = (int) $this->album->photos()->max('ordem');

        foreach ($this->novasFotos as $file) {
            $ordem++;
            $this->album->photos()->create([
                'caminho' => $this->storeImage($file, 'galerias'),
                'ordem' => $ordem,
            ]);
        }

        $this->novasFotos = [];
        $this->refreshLegendas();

        Flux::toast(variant: 'success', text: 'Fotos adicionadas.');
    }

    public function updateLegenda(int $photoId): void
    {
        $photo = $this->album?->photos()->find($photoId);

        if ($photo) {
            $photo->update(['legenda' => $this->legendas[$photoId] ?? null]);
        }
    }

    public function definirCapa(int $photoId): void
    {
        $photo = $this->album?->photos()->find($photoId);

        if ($photo && $photo->caminho) {
            $this->album->update(['capa' => $photo->caminho]);
            $this->capaAtual = $photo->caminho;
            Flux::toast(text: 'Capa definida.');
        }
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = $this->album?->photos()->find($photoId);

        if (! $photo) {
            return;
        }

        if ($this->album->capa === $photo->caminho) {
            $this->album->update(['capa' => null]);
            $this->capaAtual = null;
        }

        $this->deleteImage($photo->caminho);
        $photo->delete();

        $this->refreshLegendas();

        Flux::toast(variant: 'success', text: 'Foto removida.');
    }

    protected function refreshLegendas(): void
    {
        $this->legendas = $this->album->photos()->pluck('legenda', 'id')->toArray();
    }

    public function render(): View
    {
        return view('livewire.admin.albums.form', [
            'teams' => Team::orderBy('ordem')->orderBy('nome')->get(),
            'fotos' => $this->album ? $this->album->photos()->get() : collect(),
        ]);
    }
}
