<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<x-employer-layout>
	@if (session()->get('title'))
	<script>
		create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -290, 'center');
	</script>
	@endif
	<p class="header-text" style="margin-left:110px;margin-top:20px;">Мои вакансии</p>
	<section class='center'>
		<ul id='tabs' class='nav nav-tabs' role='tablist'>
			@if ($all_vacancies)
			<li class='nav-item'>
				<a class='nav-link active' data-bs-toggle='tab' href='#all'><i class="fa-solid fa-bars"></i><span style='padding-left:8px' class='tabs-text'>Все</span><span class='round'>{{Auth::User()->all_vacancy->count()}}</span></a>
			</li>
			@endif
			@if ($active_vacancies)
			<li class='nav-item'>
				<a class='nav-link' data-bs-toggle='tab' href='#active'><i class='fa-solid fa-circle-check'></i><span style='padding-left:8px' class='tabs-text'>Активные</span><span class='round'>{{Auth::User()->active_vacancy->count()}}</span></a>
			</li>
			@endif
			@if ($archive_vacancies)
			<li class='nav-item'>
				<a class='nav-link' data-bs-toggle='tab' href='#archive'><i class='fa-solid fa-circle-xmark'></i><span style='padding-left:8px' class='tabs-text'>Архивные</span><span class='round'>{{Auth::User()->archived_vacancy->count()}}</span></a>
			</li>
			@endif
			<li>
				<button class="button add-vacancy">
					<a href="{{ route('employer.create-vacancy') }}">{{ __('+ Добавить') }}</a>
				</button>
			</li>
		</ul>
		<div class='tab-content'>
			@if ($all_vacancies)
			<div id='all' class='container tab-pane active'><br>
				<table id='all-vacancies' class='table table-hover'>
					<tr class='t-head'>
						<td>Профессия</td>
						<td>Вид занятости</td>
						<td>Тип работы</td>
						<td>Дата публикации</td>
						<td>Статус</td>
						<td></td>
					</tr>
					@foreach($all_vacancies as $vacancy)
					<tr>
						<td>{{$vacancy->profession->profession_name}}</td>
						<td>{{$vacancy->type_of_employment->type_of_employment_name}}</td>
						<td>{{$vacancy->work_type->work_type_name}}</td>
						<td>{{date_format(date_create($vacancy->created_at), 'd-m-Y')}}</td>
						@if ($vacancy->status == 0)
						<td><span class="vacancy-active"><i class="fa-regular fa-circle-check"></i>Активна</span></td>
						@else
						<td><span class="vacancy-archive"><i class="fa-regular fa-circle-xmark"></i>В архиве</span></td>
						@endif
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left" width="38">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										@if ($vacancy->status == 0)
										<x-dropdown-link class="edit-btn" href="edit-vacancy/{{$vacancy->id}}">
											Редактировать
										</x-dropdown-link>
										<x-dropdown-link class="archive-btn" id="archive-btn-all-{{$vacancy->id}}" onclick="click_to_archive(this.id)">
											Архивировать
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="vacancy-details/{{$vacancy->id}}">
											Просмотреть
										</x-dropdown-link>
										<x-dropdown-link class="find-btn" href="find-candidates/{{$vacancy->id}}">
											Подобрать кандидатов
										</x-dropdown-link>
										@if (count($vacancy->student_response))
										<x-dropdown-link class="view-btn" href="vacancy-responses/{{$vacancy->id}}">
											Отклики
										</x-dropdown-link>
										@endif
										@if (count($vacancy->employer_offer))
										<x-dropdown-link class="view-btn" href="vacancy-offers/{{$vacancy->id}}">
											Офферы
										</x-dropdown-link>
										@endif
										@else
										<x-dropdown-link class="unarchive-btn" id="unarchive-btn-all-{{$vacancy->id}}" onclick="click_to_unarchive(this.id)">
											Разархивировать
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="vacancy-details/{{$vacancy->id}}">
											Просмотреть
										</x-dropdown-link>
										@endif
									</x-slot>
								</x-dropdown>
							</div>
						</td>
					</tr>
					@endforeach
				</table>
			</div>
			@endif
			@if ($active_vacancies)
			<div id='active' class='container tab-pane fade'><br>
				<table id='active-vacancies' class='table table-hover'>
					<tr class='t-head'>
						<td>Профессия</td>
						<td>Вид занятости</td>
						<td>Тип работы</td>
						<td>Дата публикации</td>
						<td>Статус</td>
						<td></td>
					</tr>
					@foreach($active_vacancies as $vacancy)
					<tr>
						<td>{{$vacancy->profession->profession_name}}</td>
						<td>{{$vacancy->type_of_employment->type_of_employment_name}}</td>
						<td>{{$vacancy->work_type->work_type_name}}</td>
						<td>{{date_format(date_create($vacancy->created_at), 'd-m-Y')}}</td>
						<td><span class="vacancy-active"><i class="fa-regular fa-circle-check"></i>Активна</span></td>
						<td>

							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left" width="38">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										<x-dropdown-link class="edit-btn" href="edit-vacancy/{{$vacancy->id}}">
											Редактировать
										</x-dropdown-link>
										<x-dropdown-link class="archive-btn" id="archive-btn-active-{{$vacancy->id}}" onclick="click_to_archive(this.id)">
											Архивировать
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="vacancy-details/{{$vacancy->id}}">
											Просмотреть
										</x-dropdown-link>
										<x-dropdown-link class="find-btn" href="find-candidates/{{$vacancy->id}}">
											Подобрать кандидатов
										</x-dropdown-link>
										@if (count($vacancy->student_response))
										<x-dropdown-link class="view-btn" href="vacancy-responses/{{$vacancy->id}}">
											Отклики
										</x-dropdown-link>
										@endif
										@if (count($vacancy->employer_offer))
										<x-dropdown-link class="view-btn" href="vacancy-offers/{{$vacancy->id}}">
											Офферы
										</x-dropdown-link>
										@endif
									</x-slot>
								</x-dropdown>
							</div>
						</td>
					</tr>
					@endforeach
				</table>
			</div>
			@endif
			@if ($archive_vacancies)
			<div id='archive' class='container tab-pane fade'><br>
				<table id='archive-vacancies' class='table table-hover'>
					<tr class='t-head'>
						<td>Профессия</td>
						<td>Вид занятости</td>
						<td>Тип работы</td>
						<td>Дата публикации</td>
						<td>Статус</td>
						<td></td>
					</tr>

					@foreach($archive_vacancies as $vacancy)
					<tr>
						<td>{{$vacancy->profession->profession_name}}</td>
						<td>{{$vacancy->type_of_employment->type_of_employment_name}}</td>
						<td>{{$vacancy->work_type->work_type_name}}</td>
						<td>{{date_format(date_create($vacancy->created_at), 'd-m-Y')}}</td>
						<td><span class="vacancy-archive"><i class="fa-regular fa-circle-xmark"></i>В архиве</span></td>
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left" width="38">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										<x-dropdown-link class="unarchive-btn" id="unarchive-btn-archive-{{$vacancy->id}}" onclick="click_to_unarchive(this.id)">
											Разархивировать
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="vacancy-details/{{$vacancy->id}}">
											Просмотреть
										</x-dropdown-link>
									</x-slot>
								</x-dropdown>
							</div>
						</td>
					</tr>
					@endforeach

				</table>
			</div>
			@endif
		</div>
</x-employer-layout>
<script src="/js/vacancy_pagination.js"></script>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
	#order-count {
		font-size: 22px;
		padding-top: 20px;
		padding-left: 30px;
	}

	.center {
		background-color: white;
		margin: 20px auto 0 auto;
		border-radius: 10px;
		height: 600px;
		width: 1300px;
	}

	body {
		background-color: rgba(165, 180, 252, 0.7) !important;
	}

	.table {
		width: 1200px !important;
		font-size: 14px;
		margin-left: auto;
		margin-right: auto;
	}

	tr:hover {
		background-color: rgb(251, 251, 254);
	}

	td {
		height: 50px;
		vertical-align: middle;
	}

	.t-head {
		font-size: 13px;
	}

	.status {
		background-color: rgba(165, 180, 252, 0.7);
		padding: 2px 5px;
		border-radius: 5px;
	}


	input[type="button"] {
		transition: all .3s;
		border: none;
		padding: 8px 16px;
		text-decoration: none;
		border-radius: 5px;
		font-size: 15px;
	}

	input[type="button"]:not(.active-page-btn) {
		background-color: transparent;
	}

	.active-page-btn {
		background-color: var(--link-hover-color);
		color: white;
	}

	input[type="button"]:hover:not(.active-page-btn) {
		background-color: var(--dot-color);
	}

	.round {
		padding: 1px 10px;
		border-radius: 10px;
		font-size: 13px;
		margin-left: 8px;
		color: white;
		font-family: 'Segoe UI';
	}

	#tabs .nav-item {
		background-color: var(--tabs-color);
	}

	#tabs .nav-item .tabs-text,
	#tabs .nav-item i {
		color: var(--dot-active-color);
	}

	#tabs .nav-item .round {
		background-color: var(--dot-active-color);
	}

	/*active*/
	#tabs .nav-item .active .tabs-text,
	#tabs .nav-item .active i {
		color: var(--link-hover-color);
	}

	#tabs .active .round {
		background-color: var(--link-hover-color);
	}

	/* */

	.dropbtn {
		border: none;
		cursor: pointer;
		font-weight: 700;
	}

	.archive-btn,
	.edit-btn,
	.unarchive-btn {
		cursor: pointer;
	}

	.show {
		display: block;
	}

	.vacancy-active,
	.vacancy-archive {
		background-color: var(--dot-color);
		border-radius: 8px;
		padding: 5px 7px;
	}

	.vacancy-active i,
	.vacancy-archive i {
		margin-right: 7px;
	}

	.vacancy-archive i {
		color: var(--cross-red-color);
	}

	.vacancy-active i {
		color: var(--check-green-color);
	}

	.add-vacancy {
		margin-left: 650px;
		margin-top: 2px;
	}

	.add-vacancy a {
		color: white !important;
		text-decoration: none;
	}
</style>
<script>
	function click_to_archive(id) {
		let vacancy_id = id.split('-');
		vacancy_id = vacancy_id[vacancy_id.length - 1];
		$.ajax({
			url: '{{route("employer.archive-vacancy")}}',
			type: "POST",
			data: {
				'vacancy_id': vacancy_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Архивировали вакансию!")
			},
			error: function(msg) {
				console.log("Не получилось архивировать вакансию")
			}
		});
		location.reload();

	}

	function click_to_unarchive(id) {
		let vacancy_id = id.split('-');
		vacancy_id = vacancy_id[vacancy_id.length - 1];
		$.ajax({
			url: '{{route("employer.unarchive-vacancy")}}',
			type: "POST",
			data: {
				'vacancy_id': vacancy_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Разархивировали вакансию!")
			},
			error: function(msg) {
				console.log("Не получилось разархивировать вакансию")
			}
		});
		location.reload();

	}
</script>

</html>