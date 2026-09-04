<?php

namespace App\View\Components\Site;

use App\Support\MediaUrl;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Media extends Component
{
    /**
     * @var list<string>
     */
    private const GRADIENTS = [
        'from-brand-700 via-brand-900 to-rim-700',
        'from-brand-950 via-brand-700 to-rim-500',
        'from-zinc-900 via-brand-800 to-rim-600',
        'from-rim-600 via-brand-700 to-brand-950',
        'from-brand-800 via-zinc-900 to-rim-700',
        'from-brand-600 via-rim-600 to-brand-950',
    ];

    public ?string $resolvedSrc;

    public string $gradient;

    public function __construct(
        public mixed $src = null,
        public ?string $label = null,
        public mixed $seed = 0,
        public bool $icon = true,
    ) {
        $this->resolvedSrc = MediaUrl::resolve($src);
        $this->gradient = $this->gradientFor($seed);
    }

    public function render(): View
    {
        return view('components.site.media');
    }

    private function gradientFor(mixed $seed): string
    {
        $idx = is_numeric($seed) ? (int) $seed : crc32((string) $seed);

        return self::GRADIENTS[$idx % count(self::GRADIENTS)];
    }
}
