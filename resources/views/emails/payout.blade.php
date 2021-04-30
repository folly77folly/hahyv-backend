@component('mail::message')
Hello **{{ $mailDetails['username'] }}**,  {{-- use double space for line break --}}

@component('mail::panel')
{{ $mailDetails['body'] }}
@endcomponent

Click on the link below to access your account
@php
 $url =  env('BASE_URL','https://hahyv.netlify.app/')
@endphp
@component('mail::button', ['url' => $url, 'color' => 'success'])
Go to Hahyv
@endcomponent
Sincerely,  {{-- use double space for line break --}}
Hahyv team. {{-- use double space for line break --}}

@endcomponent