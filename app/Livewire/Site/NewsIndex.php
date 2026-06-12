<?php

namespace App\Livewire\Site;

use App\Models\News;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.site')]
#[Title('Notícias')]
class NewsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'categoria')]
    public string $categoria = '';

    public function filtrar(string $categoria): void
    {
        $this->categoria = $categoria;
        $this->resetPage();
    }

    public function render(): View
    {
        $categorias = News::query()
            ->publicadas()
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $noticias = News::query()
            ->publicadas()
            ->when($this->categoria !== '', fn ($q) => $q->where('categoria', $this->categoria))
            ->paginate(9);

        return view('livewire.site.news-index', [
            'noticias' => $noticias,
            'categorias' => $categorias,
        ]);
    }
}
