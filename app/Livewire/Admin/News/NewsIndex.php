<?php

namespace App\Livewire\Admin\News;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\News;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Notícias')]
class NewsIndex extends Component
{
    use HandlesImageUpload, WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(News $news): void
    {
        $this->deleteImage($news->capa);
        $news->delete();

        Flux::toast(variant: 'success', text: 'Notícia eliminada.');
    }

    public function render(): View
    {
        $news = News::query()
            ->when($this->search, fn ($query) => $query->where('titulo', 'like', "%{$this->search}%"))
            ->orderByRaw('publicado_em IS NULL DESC')
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('livewire.admin.news.index', ['news' => $news]);
    }
}
