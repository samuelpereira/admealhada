<div>
    <x-admin.header :title="$team ? 'Editar equipa' : 'Nova equipa'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.teams.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:input wire:model="nome" label="Nome da equipa" required autofocus />

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:select wire:model="season_id" label="Época" required>
                    @foreach ($epocas as $epoca)
                        <flux:select.option value="{{ $epoca->id }}">{{ $epoca->name }}{{ $epoca->is_current ? ' (actual)' : '' }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="sport_id" label="Modalidade" required>
                    @foreach ($sports as $sport)
                        <flux:select.option value="{{ $sport->id }}">{{ $sport->nome }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="escalao" label="Escalão" placeholder="Ex.: Sub-16, Seniores" required />

                <flux:select wire:model="genero" label="Género">
                    <flux:select.option value="M">Masculino</flux:select.option>
                    <flux:select.option value="F">Feminino</flux:select.option>
                    <flux:select.option value="X">Misto</flux:select.option>
                </flux:select>

                <flux:input type="number" wire:model="ordem" label="Ordem" min="0" description="Define a ordenação no site." />
            </div>

            <flux:input wire:model="competicao" label="Competição" placeholder="Ex.: CN2, Distrital" />

            <div>
                <flux:input wire:model="fpb_url" label="Link de importação (FPB)" type="url"
                    placeholder="https://www.fpb.pt/equipa/equipa_XXXXX/"
                    description="Página da equipa na FPB. Importa o calendário e resultados." />

                @if ($team)
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <flux:button type="button" variant="filled" icon="arrow-down-tray"
                            wire:click="importarFpb"
                            wire:loading.attr="disabled"
                            wire:target="importarFpb">
                            <span wire:loading.remove wire:target="importarFpb">Importar jogos da FPB</span>
                            <span wire:loading wire:target="importarFpb">A importar…</span>
                        </flux:button>
                        <flux:button type="button" variant="filled" icon="user-group"
                            wire:click="importarPlantelFpb"
                            wire:loading.attr="disabled"
                            wire:target="importarPlantelFpb">
                            <span wire:loading.remove wire:target="importarPlantelFpb">Importar plantel da FPB</span>
                            <span wire:loading wire:target="importarPlantelFpb">A importar…</span>
                        </flux:button>
                    </div>
                    <flux:text size="sm" class="mt-2 text-zinc-500">Atualiza jogos e plantel. Pode correr sempre que houver alterações.</flux:text>
                @else
                    <flux:text size="sm" class="mt-2 text-zinc-500">Guarde a equipa para poder importar da FPB.</flux:text>
                @endif
            </div>

            <flux:textarea wire:model="descricao" label="Descrição" rows="4" />
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <x-admin.image-field
                    model="foto"
                    label="Foto da equipa"
                    :file="$foto"
                    :current="$fotoAtual"
                    remove="removerFoto"
                    hint="JPG/PNG até 4MB." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.teams.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>
</div>
