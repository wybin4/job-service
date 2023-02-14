@component('mail::message')
<p style="text-align:center">Чтобы войти на сайт и начать искать сотрудников, пройдите по ссылке ниже</p>
@component('mail::button', ['url' => $url])
Нажмите сюда
@endcomponent
@endcomponent