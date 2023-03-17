@component('mail::message')
<p style="text-align:center">Чтобы войти на сайт и начать регистрировать студентов, пройдите по ссылке ниже</p>
@component('mail::button', ['url' => $url])
Нажмите сюда
@endcomponent
@endcomponent