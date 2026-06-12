@props([
    'model',
    'label' => null,
])

<flux:field>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        wire:ignore
        x-data="adminRichText($wire, '{{ $model }}')"
        class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="flex flex-wrap items-center gap-1 border-b border-zinc-200 p-1.5 dark:border-zinc-700">
            <button type="button" @click="exec('bold')" class="rounded px-2 py-1 text-sm font-bold text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Negrito">B</button>
            <button type="button" @click="exec('italic')" class="rounded px-2 py-1 text-sm italic text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Itálico">I</button>
            <button type="button" @click="exec('underline')" class="rounded px-2 py-1 text-sm underline text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Sublinhado">U</button>
            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700"></span>
            <button type="button" @click="exec('formatBlock', '<h2>')" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Título">H2</button>
            <button type="button" @click="exec('formatBlock', '<h3>')" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Subtítulo">H3</button>
            <button type="button" @click="exec('formatBlock', '<p>')" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Parágrafo">P</button>
            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700"></span>
            <button type="button" @click="exec('insertUnorderedList')" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Lista">&bull; Lista</button>
            <button type="button" @click="exec('insertOrderedList')" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Lista numerada">1. Lista</button>
            <button type="button" @click="link()" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800" title="Link">Link</button>
            <button type="button" @click="exec('removeFormat')" class="rounded px-2 py-1 text-sm text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800" title="Limpar formatação">Limpar</button>
        </div>

        <div
            x-ref="editor"
            contenteditable="true"
            @input="sync()"
            @blur="sync()"
            class="article min-h-[16rem] max-w-none px-4 py-3 text-sm focus:outline-none dark:text-zinc-100"
        ></div>
    </div>

    <flux:error name="{{ $model }}" />
</flux:field>
