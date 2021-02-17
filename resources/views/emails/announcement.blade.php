{{-- <!DOCTYPE html>
<html>
<head>
	<title>{{ $mailDetails['title'] }}</title>
</head>
<body>
<h2>Hello {{ $mailDetails['name'] }}</h2>
<p>Hello {{ $mailDetails['body'] }}</p>
</body>
</html> --}}

@component('mail::message')
Hello **{{$name}}**,  {{-- use double space for line break --}}

@component('mail::panel')
{{ $mailDetails['body'] }}
@endcomponent

Click on the link below
@component('mail::button', ['url' => env('BASE_URL','https://hahyv.netlify.app/'), 'color' => 'success'])
Go to Hahyv
@endcomponent
Sincerely,  {{-- use double space for line break --}}
Hahyv team. {{-- use double space for line break --}}

@endcomponent