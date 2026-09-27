<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class GalleryThumbnail
{
    public const MAX_EDGE = 480;

    /**
     * Store the original upload and a smaller JPEG used by gallery grids.
     *
     * @return array{original: string, thumbnail: string|null}
     */
    public static function store(TemporaryUploadedFile $file, string $directory): array
    {
        $original = $file->store($directory, 'public');

        if ($original === false) {
            throw new RuntimeException('Failed to store uploaded image.');
        }

        return [
            'original' => $original,
            'thumbnail' => self::fromStoredPath($original),
        ];
    }

    public static function fromStoredPath(string $sourcePath): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($sourcePath)) {
            return null;
        }

        $image = self::read($disk->path($sourcePath));

        if (! $image instanceof \GdImage) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $longest = max($width, $height);
        $scale = $longest > self::MAX_EDGE ? self::MAX_EDGE / $longest : 1;
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($thumbnail === false) {
            return null;
        }

        $white = imagecolorallocate($thumbnail, 255, 255, 255);

        if ($white === false) {
            return null;
        }

        imagefill($thumbnail, 0, 0, $white);
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $relative = trim(dirname($sourcePath), '.').'/thumbs/'.pathinfo($sourcePath, PATHINFO_FILENAME).'.jpg';
        $disk->makeDirectory(dirname($relative));

        $written = imagejpeg($thumbnail, $disk->path($relative), 82);

        return $written ? $relative : null;
    }

    private static function read(string $absolutePath): ?\GdImage
    {
        $contents = file_get_contents($absolutePath);

        if ($contents === false) {
            return null;
        }

        $image = imagecreatefromstring($contents);

        return $image instanceof \GdImage ? $image : null;
    }
}
