<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HandlesImageUpload
{
    /**
     * Store an uploaded image on the public disk and return its path.
     */
    protected function storeImage(TemporaryUploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Delete an image from the public disk if it exists.
     */
    protected function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
