<div>
    <x-admin.header :title="$news ? 'Editar notícia' : 'Nova notícia'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.news.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:input wire:model="titulo" label="Título" required autofocus />
                <flux:textarea wire:model="resumo" label="Resumo" rows="2" description="Breve introdução apresentada nas listagens." />
                <x-admin.rich-text model="corpo" label="Corpo" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:select wire:model="categoria" label="Categoria">
                    @foreach ($categorias as $cat)
                        <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="datetime-local" wire:model="publicado_em" label="Publicar em"
                    description="Deixe vazio para guardar como rascunho." />

                <flux:checkbox wire:model="destaque" label="Notícia em destaque" />

                <x-admin.image-field
                    model="capa"
                    label="Imagem de capa"
                    :file="$capa"
                    :current="$capaAtual"
                    remove="removerCapa"
                    hint="JPG/PNG até 4MB." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.news.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>
</div>
