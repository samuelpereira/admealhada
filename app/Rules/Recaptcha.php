<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('Confirma que não és um robô.');

            return;
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(3)
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException) {
            $fail('A verificação do reCAPTCHA falhou. Tenta novamente.');

            return;
        }

        if (! $response->ok() || $response->json('success') !== true) {
            $fail('A verificação do reCAPTCHA falhou. Tenta novamente.');
        }
    }
}
