<?php

namespace App\Livewire\Site;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Contactos')]
class Contact extends Component
{
    #[Validate('required|string|min:2|max:120')]
    public string $nome = '';

    #[Validate('required|email|max:160')]
    public string $email = '';

    #[Validate('required|string|min:3|max:160')]
    public string $assunto = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $mensagem = '';

    public bool $enviado = false;

    public function submit(): void
    {
        $this->validate();

        // Numa fase futura, esta mensagem pode ser persistida e/ou enviada por email.
        $this->enviado = true;

        $this->reset(['nome', 'email', 'assunto', 'mensagem']);
    }

    public function render()
    {
        return View::make('livewire.site.contact');
    }
}
