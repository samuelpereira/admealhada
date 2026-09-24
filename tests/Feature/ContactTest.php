<?php

use App\Livewire\Site\Contact;
use App\Mail\ContactMessage;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('the contact page shows the recaptcha widget without the secret', function () {
    config([
        'services.recaptcha.site_key' => 'site-key',
        'services.recaptcha.secret' => 'test-secret',
    ]);

    $this->get(route('contactos'))
        ->assertOk()
        ->assertSee('site-key', false)
        ->assertSee('https://www.google.com/recaptcha/api.js', false)
        ->assertSee('https://policies.google.com/privacy', false)
        ->assertDontSee('test-secret', false);
});

test('accepts the message when Google confirms the visitor', function () {
    config([
        'services.recaptcha.secret' => 'test-secret',
        'site.contact.form_email' => 'clube@example.com',
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
    ]);
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'Ana Silva')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', 'token-ok')
        ->assertHasNoErrors()
        ->assertSet('enviado', true)
        ->assertSet('nome', '');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'token-ok';
    });

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
        return $mail->hasTo('clube@example.com')
            && $mail->hasReplyTo('ana@example.com')
            && $mail->assunto === 'Inscrição'
            && $mail->mensagem === 'Quero inscrever o meu filho na formação.';
    });
});

test('rejects the message when Google refuses the token', function () {
    config([
        'services.recaptcha.secret' => 'test-secret',
        'site.contact.form_email' => 'clube@example.com',
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false]),
    ]);
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'Ana Silva')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', 'token-bad')
        ->assertHasErrors('recaptcha')
        ->assertSet('enviado', false)
        ->assertSet('nome', 'Ana Silva');

    Mail::assertNothingSent();
});

test('rejects the message when the recaptcha token is missing', function () {
    config(['site.contact.form_email' => 'clube@example.com']);

    Http::preventStrayRequests();
    Http::fake();
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'Ana Silva')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', '')
        ->assertHasErrors('recaptcha')
        ->assertSet('enviado', false);

    Http::assertNothingSent();
    Mail::assertNothingSent();
});

test('does not call Google when the message fields are invalid', function () {
    config(['site.contact.form_email' => 'clube@example.com']);

    Http::preventStrayRequests();
    Http::fake();
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'A')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', 'token-ok')
        ->assertHasErrors('nome')
        ->assertSet('enviado', false);

    Http::assertNothingSent();
    Mail::assertNothingSent();
});

test('rejects the message when Google cannot be reached', function () {
    config(['services.recaptcha.secret' => 'test-secret']);

    Http::preventStrayRequests();
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::failedConnection(),
    ]);
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'Ana Silva')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', 'token-ok')
        ->assertHasErrors('recaptcha')
        ->assertSet('enviado', false);

    Mail::assertNothingSent();
});

test('does not accept the message when the form address is missing', function () {
    config([
        'services.recaptcha.secret' => 'test-secret',
        'site.contact.form_email' => '',
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
    ]);
    Mail::fake();

    Livewire::test(Contact::class)
        ->set('nome', 'Ana Silva')
        ->set('email', 'ana@example.com')
        ->set('assunto', 'Inscrição')
        ->set('mensagem', 'Quero inscrever o meu filho na formação.')
        ->call('submit', 'token-ok')
        ->assertHasErrors('mensagem')
        ->assertSet('enviado', false)
        ->assertSet('nome', 'Ana Silva');

    Mail::assertNothingSent();
});

test('the contact email escapes the message body', function () {
    $mail = new ContactMessage(
        nome: 'Ana Silva',
        email: 'ana@example.com',
        assunto: 'Inscrição',
        mensagem: "<script>alert('xss')</script>",
    );

    $mail->assertDontSeeInHtml("<script>alert('xss')</script>", false);
    $mail->assertSeeInText('Ana Silva');
    $mail->assertSeeInText("alert('xss')");
    $mail->assertDontSeeInText('Obrigado');
});
