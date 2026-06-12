@props([
    'model',
    'label' => null,
    'file' => null,
    'current' => null,
    'remove' => null,
    'hint' => null,
])

@php
    $previewUrl = $file ? $file->temporaryUrl() : ($current ? \Illuminate\Support\Facades\Storage::url($current) : null);
@endphp

<flux:field>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div class="flex items-start gap-4">
        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
            @if ($previewUrl)
                <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-cover">
            @else
                <flux:icon.photo class="h-8 w-8 text-zinc-400" />
            @endif
        </div>

        <div class="flex-1 space-y-2">
            <input
                type="file"
                wire:model="{{ $model }}"
                accept="image/*"
                class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-zinc-300"
            >
            @if ($hint)
                <flux:text size="sm" class="text-zinc-500">{{ $hint }}</flux:text>
            @endif
            <div wire:loading wire:target="{{ $model }}">
                <flux:text size="sm" class="text-brand-600">A carregar imagem…</flux:text>
            </div>
            @if ($remove && ($file || $current))
                <flux:button type="button" size="xs" variant="subtle" wire:click="{{ $remove }}" icon="trash">
                    Remover imagem
                </flux:button>
            @endif
        </div>
    </div>

    <flux:error name="{{ $model }}" />
</flux:field>
