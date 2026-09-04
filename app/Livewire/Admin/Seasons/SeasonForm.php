<?php

namespace App\Livewire\Admin\Seasons;

use App\Models\Season;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Época')]
class SeasonForm extends Component
{
    public ?Season $season = null;

    public string $name = '';

    public function mount(?Season $season = null): void
    {
        if ($season && $season->exists) {
            $this->season = $season;
            $this->name = $season->name;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('seasons', 'name')->ignore($this->season?->id),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! preg_match('/^(\d{4})\/(\d{4})$/', $value, $parts)) {
                        return;
                    }

                    if ((int) $parts[2] !== (int) $parts[1] + 1) {
                        $fail('A época deve ser um ano seguido, no formato 2025/2026.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Indica o nome da época.',
            'name.regex' => 'A época deve ter o formato 2025/2026.',
            'name.unique' => 'Já existe uma época com este nome.',
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $attributes = Season::attributesForName($this->name);

        if ($this->season) {
            $this->season->update($attributes);
        } else {
            Season::create($attributes);
        }

        Flux::toast(variant: 'success', text: 'Época guardada.');

        return $this->redirectRoute('admin.seasons.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.seasons.form');
    }
}
