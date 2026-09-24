<x-mail::message>
# Mensagem do site

**{{ $nome }}** ({{ $email }}) escreveu:

{!! nl2br(e($mensagem)) !!}
</x-mail::message>
