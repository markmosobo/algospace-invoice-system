<x-mail::message>
# Verify Your Email

Click the button below to verify your email address.

<x-mail::button :url="$verificationUrl">
Verify Email
</x-mail::button>

This link will expire in 60 minutes.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>