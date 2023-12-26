@component('mail::message')
# {{request()->header('Lang') == 'ar' ? "مرحبا, $name" : "Hello, $name"}}

{{ $message }}


{{request()->header('Lang') == 'ar' ? "اطيب الاماني," : "Best Regards,"}}<br>
<br>
{{ config('app.name') }}
@endcomponent
