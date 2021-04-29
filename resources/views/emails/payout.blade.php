@component('mail::message')
Hello **{{ $mailDetails['username'] }}**,  {{-- use double space for line break --}}

@component('mail::panel')
{{ $mailDetails['body'] }}
@endcomponent

Click on the link below to access your account
@component('mail::button', ['url' => {{ env('BASE_URL','https://hahyv.netlify.app/') }}, 'color' => 'success'])
Go to Hahyv
@endcomponent
Sincerely,  {{-- use double space for line break --}}
Hahyv team. {{-- use double space for line break --}}

@endcomponent