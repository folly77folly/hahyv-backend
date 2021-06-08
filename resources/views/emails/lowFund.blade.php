@component('mail::message')
Hello **Admin**,  {{-- use double space for line break --}}

@component('mail::panel')
Users Can no longer withdraw from Wallets Africa Wallet as the fund is low. Kindly treat as urgent  
@endcomponent

Click on the link below to access your account
@php
 $url =  env('ADMIN_BASE_URL','https://admin-hahyv.netlify.app/login')
@endphp
@component('mail::button', ['url' => $url, 'color' => 'success'])
Go to Hahyv
@endcomponent
Sincerely,  {{-- use double space for line break --}}
Hahyv BOT. {{-- use double space for line break --}}

@endcomponent