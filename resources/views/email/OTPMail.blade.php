Hello {{ $userName }},

<p>Use this code to complete your verification process.</p>

<strong style="font-size: 1.2em; color: #0088cc;">{{ $otp }}</strong>

<p>If you didn't request this OTP, please ignore this email.</p>

Thanks, <br>

{{ config('app.name') }}