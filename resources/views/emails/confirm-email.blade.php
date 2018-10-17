@component('mail::message')
# One last step

We need you to confirm your email address to prove that you are human!

@component('mail::button', ['url' =>url('/register/confirm?token='.$user->confirmation_token)])
Confirm Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
