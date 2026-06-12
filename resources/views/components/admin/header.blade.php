@props([
    'title',
    'subtitle' => null,
])

<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <flux:heading size="xl">{{ $title }}</flux:heading>
        @if ($subtitle)
            <flux:subheading>{{ $subtitle }}</flux:subheading>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
