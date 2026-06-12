<?php

namespace App\Livewire\Admin\News;

use App\Livewire\Concerns\GeneratesSlugs;
use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\News;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Notícia')]
class NewsForm extends Component
{
    use GeneratesSlugs, HandlesImageUpload, WithFileUploads;

    public ?News $news = null;

    public string $titulo = '';

    public string $resumo = '';

    public string $corpo = '';

    public string $categoria = 'Clube';

    public bool $destaque = false;

    public ?string $publicado_em = null;

    public $capa = null;

    public ?string $capaAtual = null;

    public array $categorias = ['Clube', 'Competição', 'Formação', 'Eventos'];

    public function mount(?News $news = null): void
    {
        if ($news && $news->exists) {
            $this->news = $news;
            $this->titulo = $news->titulo;
            $this->resumo = $news->resumo ?? '';
            $this->corpo = $news->corpo ?? '';
            $this->categoria = $news->categoria;
            $this->destaque = $news->destaque;
            $this->publicado_em = $news->publicado_em?->format('Y-m-d\TH:i');
            $this->capaAtual = $news->capa;
        }
    }

    protected function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'resumo' => ['nullable', 'string', 'max:500'],
            'corpo' => ['required', 'string'],
            'categoria' => ['required', 'string', 'max:50'],
            'destaque' => ['boolean'],
            'publicado_em' => ['nullable', 'date'],
            'capa' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function removerCapa(): void
    {
        $this->capa = null;

        if ($this->capaAtual) {
            $this->deleteImage($this->capaAtual);
            $this->capaAtual = null;

            if ($this->news) {
                $this->news->update(['capa' => null]);
            }
        }
    }

    public function save()
    {
        $validated = $this->validate();

        $data = [
            'titulo' => $validated['titulo'],
            'resumo' => $validated['resumo'] ?? null,
            'corpo' => $validated['corpo'],
            'categoria' => $validated['categoria'],
            'destaque' => $validated['destaque'],
            'publicado_em' => $validated['publicado_em'] ? Carbon::parse($validated['publicado_em']) : null,
            'slug' => $this->uniqueSlug($this->titulo, 'news', $this->news?->id),
        ];

        if ($this->capa) {
            $this->deleteImage($this->capaAtual);
            $data['capa'] = $this->storeImage($this->capa, 'noticias');
        }

        if ($this->news) {
            $this->news->update($data);
        } else {
            News::create($data);
        }

        Flux::toast(variant: 'success', text: 'Notícia guardada.');

        return $this->redirectRoute('admin.news.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.news.form');
    }
}
