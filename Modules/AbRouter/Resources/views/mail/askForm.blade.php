@component('mail::message')
## Name:
{{$name}}

## E-mail:
{{$email}}

## Message:
{{$message}}

@component('mail::button', ['url' => 'https://abrouter.com/'])
AbRouter
@endcomponent

@endcomponent
