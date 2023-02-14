@component('mail::message')
<p style="text-align:center">{{ $text }}</p>
@component('mail::button', ['url' => $url])
Посмотреть вакансию
@endcomponent
@endcomponent