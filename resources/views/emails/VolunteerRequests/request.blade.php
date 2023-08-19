@component('mail::message')
# Hello, {{ $station->title }}.

New volunteer request for your Station.

Name - {{ $volunteer->volunteer_name }}
Phone - {{ $volunteer->volunteer_phone }}
@if ($volunteer->message)
Message - {{ $volunteer->message }}
@endif

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
