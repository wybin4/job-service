<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="{{asset('/js/range-functions.js')}}"></script>
</head>
<x-student-layout>

	@if (session()->get('title'))
	<script>
		create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -290, 'center');
	</script>
	@endif
	@if (count($vacancies))
	<div class="row">
		<div class="col-md-auto">
			<p class="font-bold text-4xl" style="margin-left:130px;margin-top:20px;"><a href="/student/resume-details">{{$resume->profession->profession_name}}</a></p>
			<p class="text-muted" style="margin-left:130px;">{{date_format(date_create($resume->created_at), 'd-m-Y')}}</p>
		</div>
		<div class="col-md-auto">
			@if($resume->status == 0)
			<div class="dot"></div>
			@else
			<div class="dot-red"></div>
			@endif
		</div>
		<div class="col-md-auto">
			<button class="button search-vacancy">
				<a href="{{ route('student.vacancy-feed') }}">Поиск по вакансиям</a>
			</button>
		</div>
	</div>
	<section class='center'>
		<table id='candidates-table' class='table table-hover'>
			<thead>
				<tr class='t-head'>
					<td>Компания</td>
					<td>Оценка компании</td>
					<td>Опыт работы</td>
					<td>Навыки</td>
					<td>Статус</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				@php function YearTextArg($year) {
				$year = abs($year);
				$t1 = $year % 10;
				$t2 = $year % 100;
				return ($t1 == 1 && $t2 != 11 ? "год" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2>= 20) ? "года" : "лет"));
					}@endphp
					@php function MonthTextArg($month) {
					$month = abs($month);
					$t1 = $month % 10;
					$t2 = $month % 100;
					return ($t1 == 1 && $t2 != 11 ? "месяц" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2>= 20) ? "месяца" : "месяцев"));
						}@endphp
						@php $i = 0; @endphp
						@foreach($vacancies as $vacancy)
						<tr>
							<td>
								<div class="row">
									<div class="col-md-3">
										@if (!$vacancy->image)
										<div class="future-pic">
											<div>{{mb_substr($vacancy->employer_name, 0, 1)}}</div>
										</div>
										@else
										<img class="pic" src="{{asset('/storage/images/'.$vacancy->image)}}" />
										@endif
									</div>
									<div class="col-md-9 mt-1">
										<a class="font-bold" href="/student/employer/{{$vacancy->employer_id}}">{{$vacancy->employer_name}}</a>
										<div class="text-gray-500">{{$vacancy->profession_name}}</div>
									</div>
								</div>
							</td>
							<td id="rate-{{$vacancy->employer_id}}">—</td>
							@if($vacancy->work_experience > 0)
							<td>{{ $vacancy->work_experience . " " . YearTextArg($vacancy->work_experience) }}</td>
							@else
							<td>Без опыта</td>
							@endif
							@php $i++; @endphp
							<td class="vacancy_skills_area">
								@php $vacancy_skills = App\Models\VacancySkill::where('vacancy_id', $vacancy->vacancy_id)
								->join('skills', 'skills.id', '=', 'vacancy_skills.skill_id')
								->get()
								@endphp
								@php $j = 0; @endphp
								@foreach ($vacancy_skills as $vs)
								@if ($j < 4) <div class="vacancy_skill">{{$vs->skill_name}}</div>
									@endif
									@php $j++; @endphp
									@endforeach
									@if ($j >= 4)
									<div class="vacancy_skill">+1</div>
									@endif
							</td>
							<td>
								@php $status = $vacancy_order[array_search($vacancy->vacancy_id, array_column($vacancy_order, 0))][1]; @endphp
								@if ($status == 3)
								<div class="regular-match match">Средняя совместимость</div>
								@elseif ($status == 4)
								<div class="regular-match match">Высокая совместимость</div>
								@elseif ($status == 5)
								<div class="excellent-match match">Наивысшая совместимость</div>
								@endif
							</td>
							<td>
								<div class="hidden sm:flex sm:items-center sm:ml-4">
									<x-dropdown align="left" width="78">
										<x-slot name="trigger">
											<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
												<div>⋮</div>
											</button>
										</x-slot>
										<x-slot name="content">
											<x-dropdown-link class="view-btn" href="/student/vacancy/{{$vacancy->vacancy_id}}" target="_blank">
												Просмотреть вакансию
											</x-dropdown-link>
											<x-dropdown-link class="response-btn" id="response-btn-{{$vacancy->vacancy_id}}">
												Откликнуться
											</x-dropdown-link>
										</x-slot>
									</x-dropdown>
								</div>
							</td>
						</tr>
						@endforeach
			</tbody>
		</table>
		<div id="pagination"></div>
	</section>
	<script>
		let er = <?php echo json_encode($employer_rates); ?>;
		er = group_by(er, "employer_id");
		for (let k in er) {
			let employer_rates = [];
			if (er[k].length) {
				let rates = new Set(er[k].map(r => r.quality_id));
				rates = Array.from(rates);
				let arr;
				// получаем employer_rates с отдельными quality_id и массивом оценок к нему
				for (let i = 0; i < rates.length; i++) {
					arr = er[k].filter((r) => {
						return r.quality_id == rates[i]
					});
					if (arr.length > 1) {
						employer_rates.push([rates[i],
							get_trend([arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.quality_rate)])
						]);
					} else {
						employer_rates.push([rates[i],
							[arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.quality_rate)]
						])
					}
				}
				// получаем ema для employer_rates
				let employer_ema = [];
				employer_rates.forEach(function(rate) {
					employer_ema.push([rate[0], get_ema(rate[1][1])])
				})
				const sum = employer_ema.reduce((acc, number) => acc + number[1], 0);
				const length = employer_ema.length;
				$("#rate-" + er[k][0].employer_id).text((sum / length).toFixed(2));
			}
		}
	</script>
	@else
	<div style="height:100vh;background-color:white">
		<div class="first-div text-center">

			<h1 class="big-text">Подходящих вакансий не найдено. Воспользуйтесь <span class="big-indigo-text">общим поиском</span>.</h1>
			<span class="big-indigo-underline"></span>
			<p id="popular"><span class="text-muted font-bold examples-p">Популярные запросы:</span>
				<span>
					@php $i = 1; @endphp
					@foreach($popular_professions as $pp)
					@if ($i == count($popular_professions))
					<a href="/student/vacancy-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}</a>
					@else
					<a href="/student/vacancy-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}, </a>
					@endif
					@php $i++; @endphp
					@endforeach
				</span>
			</p>

		</div>
	</div>
	@endif
</x-student-layout>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

	.first-div {
		margin: 0 210px;
		padding-top: 150px;
	}

	.big-text {
		font-size: 52px;
		font-family: 'Montserrat';
		font-weight: 600;
	}

	.big-text:first-child {
		padding-top: 50px;
	}

	.big-indigo-text {
		color: var(--text-selection-color);
		z-index: 20;
		position: relative;
	}

	.big-indigo-underline {
		width: 443px;
		height: 30px;
		background-color: var(--text-underline-color);
		position: absolute;
		top: 365px;
		left: 758px;
		z-index: 0;
	}


	/** */
	.view-btn,
	.response-btn {
		cursor: pointer;
	}

	#order-count {
		font-size: 22px;
		padding-top: 20px;
		padding-left: 30px;
	}

	.center {
		background-color: white;
		margin: 5px auto 0 auto;
		border-radius: 10px;
		height: 600px;
		width: 1300px;
	}

	body {
		background-color: rgba(165, 180, 252, 0.7) !important;
	}

	.table {
		margin-top: 20px;
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
		height: 50px;
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
		background-color: #7883DF !important;
		color: #fff;
	}

	input[type="button"]:hover:not(.active-page-btn) {
		background-color: rgba(165, 180, 252, 0.2);
	}

	.round {
		padding: 1px 10px;
		border-radius: 10px;
		font-size: 13px;
		margin-left: 8px;
		color: white;
		font-family: 'Segoe UI';
	}

	/* */

	.dropbtn {
		border: none;
		cursor: pointer;
		font-weight: 700;
	}

	.show {
		display: block;
	}

	.future-pic {
		font-size: 20px;
		display: flex;
		align-items: center;
	}

	.future-pic div {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}

	.pic,
	.future-pic {
		width: 50px;
		height: 50px;
	}

	td {
		display: table-cell !important;
	}

	.vacancy_skills_area {
		display: flex;
		justify-content: space-between;
	}

	.vacancy_skill {
		margin-top: 5px;
		display: inline-block;
		margin-right: 3px;
		border-radius: 8px;
		padding: 3px 10px;
		background-color: var(--dot-color);
		color: var(--link-hover-color);
	}

	.low-match {
		background-color: var(--rejected-status-color);
		color: var(--rejected-text-color);
	}

	.regular-match {
		color: var(--regular-text-color);
		background-color: var(--regular-background-color);
	}

	.excellent-match {
		background-color: var(--excellent-background-color);
		color: var(--excellent-text-color);
	}

	/** */
	html {
		overflow-x: hidden !important;
	}

	.dot,
	.dot-red {
		margin-top: 33px;
		margin-left: -10px;
		display: flex;
		width: 18px;
		height: 18px;
		border-radius: 50%;
	}

	.dot::after,
	.dot-red::after {
		content: '';
		display: block;
		margin: auto;
		width: 7px;
		height: 7px;
		border-radius: 50%;
	}

	a {
		color: black;
		text-decoration: none;
	}

	a:hover {
		color: black;
	}

	.search-vacancy {
		padding: 7px 15px;
		border-radius: 8px;
		margin-left: 670px;
		margin-top: 30px;
	}

	.search-vacancy a {
		color: white !important;
	}


	/* */
	/** */
	.simple-pagination ul {
		margin: 0 0 20px;
		padding: 0;
		list-style: none;
		text-align: center;
	}

	.simple-pagination li {
		display: inline-block;
		margin-right: 5px;

	}

	.simple-pagination li a,
	.simple-pagination li span {
		color: black;
		transition: all .3s;
		border: none;
		padding: 8px 16px;
		text-decoration: none;
		border-radius: 5px;
		font-size: 15px;
		background-color: transparent;
	}

	.simple-pagination li a:hover {
		background-color: var(--dot-color);
	}

	.simple-pagination .current {
		color: white;
		background-color: var(--link-hover-color);
		border-color: none;
	}

	.simple-pagination .prev.current,
	.simple-pagination .next.current {
		background: transparent;
		color: black;
	}

	#pagination {
		margin-top: 35px;
	}

	html {
		overflow-x: hidden;
	}
</style>
<script>
	function paginate() {
		let items = $("#candidates-table tbody tr");
		let numItems = items.length;
		let perPage = 5;
		items.slice(perPage).hide();
		if (numItems < perPage) {
			$("#pagination").hide();
		} else {
			$("#pagination").show();
		}
		$("#pagination").pagination({
			items: numItems,
			itemsOnPage: perPage,
			cssStyle: "light-theme",
			prevText: "«",
			nextText: "»",
			onPageClick: function(pageNumber) {
				let showFrom = perPage * (pageNumber - 1);
				let showTo = showFrom + perPage;
				items.hide()
					.slice(showFrom, showTo).show();
			}
		});
	}
	paginate();
	$(".response-btn").click(function() {
		let id = $(this).attr('id');
		id = id.split('-');
		id = id[id.length - 1];
		let form = new FormData();
		form.append('vacancy_id', id);

		$.ajax({
			url: '{{ route("student.student-response") }}',
			type: "POST",
			data: form,
			cache: false,
			contentType: false,
			processData: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Отправили отклик!");
			},
			error: function(msg) {
				console.log("Не получилось отправить отклик")
			}
		});
		location.reload();
	})
</script>

</html>