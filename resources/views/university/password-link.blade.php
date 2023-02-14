@component('mail::message')
<p style="text-align:center">Чтобы войти на сайт, пройдите по ссылке ниже</p>
@component('mail::button', ['url' => $url])
Нажмите сюда
@endcomponent
@endcomponent