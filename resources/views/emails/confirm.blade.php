@component('mail::message')
    # Hola {{$user->name}}

    Has modificado tu correo electronico. Por favor verificalo mediante este boton

    @component('mail::button', ['url' => route('verify', $user->verification_token)])
        Confirmar mi cuenta
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent