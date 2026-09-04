<?php

namespace App\View\Components\Site;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SponsorsCarousel extends Component
{
    /**
     * @var Collection<int, mixed>
     */
    public Collection $track;

    /**
     * @param  Collection<int, mixed>  $sponsors
     */
    public function __construct(public Collection $sponsors)
    {
        $this->track = collect();

        if ($sponsors->isEmpty()) {
            return;
        }

        while ($this->track->count() < 12) {
            $this->track = $this->track->concat($sponsors);
        }
    }

    public function render(): View
    {
        return view('components.site.sponsors-carousel');
    }
}
