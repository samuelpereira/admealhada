<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageField extends Component
{
    public ?string $previewUrl;

    public function __construct(
        public string $model,
        public ?string $label = null,
        public mixed $file = null,
        public mixed $current = null,
        public ?string $remove = null,
        public ?string $hint = null,
    ) {
        $this->previewUrl = $file instanceof TemporaryUploadedFile
            ? $file->temporaryUrl()
            : (filled($current) && is_string($current) ? Storage::url($current) : null);
    }

    public function render(): View
    {
        return view('components.admin.image-field');
    }
}
