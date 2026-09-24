<?php

namespace App\Livewire\Site;

use App\Mail\ContactMessage;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

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

    public function submit(string $recaptcha = ''): void
    {
        $this->validate();

        $captcha = validator(
            ['recaptcha' => $recaptcha],
            ['recaptcha' => ['required', 'bail', new Recaptcha]],
            ['recaptcha.required' => 'Confirma que não és um robô.'],
        );

        if ($captcha->fails()) {
            $this->dispatch('recaptcha-reset');

            throw new ValidationException($captcha);
        }

        $destino = config('site.contact.form_email');

        if (! is_string($destino) || $destino === '') {
            $this->addError('mensagem', 'Não foi possível enviar a mensagem. Tenta novamente mais tarde.');
            $this->dispatch('recaptcha-reset');

            return;
        }

        try {
            Mail::to($destino)->send(new ContactMessage(
                nome: $this->nome,
                email: $this->email,
                assunto: $this->assunto,
                mensagem: $this->mensagem,
            ));
        } catch (Throwable $exception) {
            report($exception);

            $this->addError('mensagem', 'Não foi possível enviar a mensagem. Tenta novamente mais tarde.');
            $this->dispatch('recaptcha-reset');

            return;
        }

        $this->enviado = true;

        $this->reset(['nome', 'email', 'assunto', 'mensagem']);
        $this->dispatch('recaptcha-reset');
    }

    public function render(): View
    {
        return view('livewire.site.contact');
    }
}
