@component('mail::message')
<p>Была добавлена новая вакансия для резюме "{{$profession_name}}"</p>
<div class="hr"></div>
<style>
	.hr {
		margin: 10px 0px;
		border-bottom: solid 1px #D1D5DB;
	}
</style>
<a href="{{$url}}">{{$vacancy->profession->profession_name}}</a><br>
@if ($vacancy->salary != 0)
<p>{{$vacancy->salary}}₽</p>
@else
<p>Без оплаты</p>
@endif

<p>{{$vacancy->employer->name}}<br>{{date_format(date_create($vacancy->created_at), 'd.m.Y')}}</p>
@endcomponent