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
</head>
<div id="interview-popup">
	<div class="modal" id="interview-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<i class="fa-solid fa-xmark" style="margin-left:450px;margin-bottom:10px"></i>

					<h2 class="text-xl interview-with"></h2>
					<h2 class="text-muted vacancy-is" style="font-size:16px;margin-bottom:30px;"></h2>

					<div id="about-interview"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-student-layout>
		@if (session()->get('title'))
		<script>
			create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -290, 'center');
		</script>
		@endif

		@if (count($interactions))
		<div class="row">
			<div class="col-md-8">
				<p class="medium-text mt-4" style="margin-left:160px;">Мои отклики</p>
			</div>
			<div class="col-md-3">
				@if (Auth::User()->resume)
				<button class="button search">
					<a href="{{ route('student.vacancy-feed') }}">{{ __('Поиск по вакансиям') }}</a>
				</button>
				@endif
			</div>
		</div>
		<section class='center'>
			<table id='my-responses-table' class='table table-hover'>
				<thead>
					<tr class='t-head'>
						<td>Вакансия</td>
						<td>Компания</td>
						<td>Статус</td>
						<td>Дата отклика</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					@foreach($interactions as $response)
					<tr>
						<td>{{$response->vacancy->profession->profession_name}}</td>
						<td>{{$response->vacancy->employer->name}}</td>
						@if ($response->status == 0)
						<td><span class="work-status not-considered">Не рассмотрен</span></td>
						@elseif($response->status == 1)
						<td><span class="work-status rejected">Отказ</span></td>
						@elseif($response->status == 2)
						<td><span class="work-status interview">Собеседование</span></td>
						@elseif($response->status == 3)
						<td><span class="work-status work">Работаю</span></td>
						@elseif($response->status == 4)
						<td><span class="work-status rejected">Не принят на работу</span></td>
						@elseif($response->status == 7)
						<td><span class="work-status rejected">Отказался от вакансии</span></td>
						@elseif($response->status == 8)
						<td><span class="work-status work">Работаю</span></td>
						@elseif($response->status == 9)
						<td><span class="work-status rejected">Уволен</span></td>
						@endif
						<td>{{date_format(date_create($response->created_at), 'd.m.Y')}}</td>
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										@if ($response->status == 2)
										<x-dropdown-link class="reject-after-btn" id="reject-after-btn-{{$response->id}}">
											Отказаться от вакансии
										</x-dropdown-link>
										<x-dropdown-link class="interview-data-btn" id="interview-data-btn-{{$response->id}}">
											О собеседовании
										</x-dropdown-link>
										@endif
										@if(!in_array($response->vacancy_id, $vacancies_with_rate) && in_array($response->status, [3, 8, 9]))
										<x-dropdown-link class="rate-btn" href="employer-rate-page?employer_id={{$response->vacancy->employer_id}}&vacancy_id={{$response->vacancy_id}}">
											Оценить
										</x-dropdown-link>
										@elseif(in_array($response->status, [3, 8, 9]))
										<x-dropdown-link class="rate-btn" href="employer-rate-page-edit?employer_id={{$response->vacancy->employer_id}}&vacancy_id={{$response->vacancy_id}}">
											Изменить оценки
										</x-dropdown-link>
										@endif
										<x-dropdown-link class="view-btn" href="vacancy/{{$response->vacancy->id}}">
											Просмотреть вакансию
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
		@else
		<div style="height:100vh;background-color:white">
			<div class="first-div text-center">

				<h1 class="big-text">Вы не откликнулись ни на одну вакансию</h1>
			</div>
		</div>
		@endif
	</x-student-layout>
</div>
<style>
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	#interview-data {
		width: 470px;
		margin-bottom: 40px;
	}

	.modal-body {
		padding: 20px;
		padding-bottom: 40px;
	}

	.fa-xmark,
	.interview-data-btn {
		cursor: pointer;
	}

	/** */
	@import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

	.first-div {
		margin: 0 210px;
		padding-top: 170px;
	}

	.big-text {
		font-size: 52px;
		font-family: 'Montserrat';
		font-weight: 600;
	}

	.big-text:first-child {
		padding-top: 50px;
	}

	/** */
	html {
		overflow-x: hidden;
	}

	.center {
		background-color: white;
		margin: 30px auto 0 auto;
		border-radius: 10px;
		height: 600px;
		width: 1300px;
	}

	.table {
		width: 1100px !important;
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

	.search {
		margin-left: 120px;
		margin-top: 30px;
	}

	/* */

	.dropbtn {
		border: none;
		cursor: pointer;
		font-weight: 700;
	}

	.view-btn,
	.reject-after-btn {
		cursor: pointer;
	}

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

</html>
<script>
	function paginate() {
		let items = $("#my-responses-table tbody tr");
		let numItems = items.length;
		let perPage = 10;
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
	$(".reject-after-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("student.change-status") }}',
			type: "POST",
			data: {
				'id': id,
				'status': 7,
				'text': 'Успешно отправили отказ от приёма на работу',
				'title': 'Отправка отказа'
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Отправили отказ!");
			},
			error: function(msg) {
				console.log("Не получилось отправить отказ")
			}
		});
		location.reload();

	})
	$(".interview-data-btn").click(function() {
		$('#blurable-content').addClass("blur");
		$('#interview-modal').show();
		//запрещаем скролл
		$('html, body').css({
			'overflow-y': 'hidden',
			'overflow-x': 'hidden',
			height: '100%'
		});
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		let interactions = <?php echo json_encode($interactions); ?>;
		interactions = interactions.filter((int) => {
			return int.id == id
		})
		let vacancies = <?php echo json_encode($vacancies); ?>;
		vacancies = vacancies.filter((vac) => {
			return vac.id == interactions[0].vacancy_id
		})
		let employers = <?php echo json_encode($employers); ?>;
		employers = employers.filter((emp) => {
			return emp.id == vacancies[0].employer_id
		})
		let professions = <?php echo json_encode($professions); ?>;
		professions = professions.filter((prof) => {
			return prof.id == vacancies[0].profession_id
		})
		$("#about-interview").text(interactions[0].data.interview_data);
		$(".interview-with").text('Собеседование в "' + employers[0].name + '"');
		$(".vacancy-is").text('По вакансии "' + professions[0].profession_name + '"');
		$(".fa-xmark").on('click', function() {
			$('html, body').css({
				'overflow-y': 'auto',
				'overflow-x': 'hidden',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			$('#interview-modal').hide();
		})
	})
</script>