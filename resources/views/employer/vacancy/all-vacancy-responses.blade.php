<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.js"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>

</head>
<div id="interview-popup">
	<div class="modal" id="interview-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="text-xl text-center">Сопроводительная информация</h2>
				</div>
				<div class="modal-body">
					<p id="edit-errors"></p>
					<input type="hidden" id="hidden-id" />
					<textarea id="interview-data" type="text" placeholder="Собеседование будет проводиться 24.12.22 в 15:30..."></textarea>
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="close-modal" data-bs-dismiss="modal">Отмена</span>
					<span type="button" class="span-like-button" id="btn-interview-data" data-bs-dismiss="modal">Пригласить</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="marks-popup">
	<div class="modal" id="marks-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="text-xl text-center">Оценить студента перед увольнением?</h2>
				</div>
				<div class="modal-footer">
					<input type="hidden" id="hidden-status-id" />
					<span type="button" class="span-like-button" id="close-marks-modal" data-bs-dismiss="modal">Не оценивать</span>
					<span type="button" class="span-like-button" id="open-marks-modal" data-bs-dismiss="modal"><a>Оценить</a></span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-employer-layout>
		@if (session()->get('title'))
		<script>
			create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -280, 'center');
		</script>
		@endif
		@if (count($interactions))
		<div class="row">
			<div class="col-md-8">
				<p class="medium-text mt-4" style="margin-left:160px;">Отклики на мои вакансии</p>
			</div>
			<div class="col-md-3">
				@if (Auth::User()->active_vacancy)
				<button class="button search">
					<a href="{{ route('employer.vacancies-list') }}">{{ __('Мои вакансии') }}</a>
				</button>
				@endif
			</div>
		</div>
		<section class='center'>
			<table class='table table-hover' id="all-responses-table">
				<thead>
					<tr class='t-head'>
						<td>Вакансия</td>
						<td>ФИО студента</td>
						<td>Профессия</td>
						<td>Навыки</td>
						<td>Статус</td>
						<td>Дата</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					@foreach($interactions as $response)
					<tr>
						<td>{{App\Models\Profession::find($response->vacancy_profession_id)->profession_name}}</td>
						<td>{{$response->student_fio}}</td>
						<td>{{App\Models\Profession::find($response->resume_profession_id)->profession_name}}</td>
						<td class="student_skills_area">
							@php $student_skill_names = App\Models\StudentSkill::where('resume_id', $response->student_resume_id)
							->join('skills', 'skills.id', '=', 'student_skills.skill_id')
							->where('skill_type', 1)
							->get()
							@endphp
							@php $j = 0; @endphp
							@foreach ($student_skill_names as $shs)
							@if ($j < 4) <span class="student_skill">{{$shs->skill_name}}</span>
								@endif
								@php $j++; @endphp
								@endforeach
								@if ($j >= 4)
								<span class="student_skill">+1</span>
								@endif
						</td>
						@if ($response->student_response_status == 0)
						<td><span>Не рассмотрен</span></td>
						@elseif($response->student_response_status == 1)
						<td><span>Отказ</span></td>
						@elseif($response->student_response_status == 2)
						<td><span>Собеседование</span></td>
						@elseif($response->student_response_status == 3)
						<td><span>Принят на работу</span></td>
						@elseif($response->student_response_status == 4)
						<td><span>Не принят на работу</span></td>
						@elseif($response->student_response_status == 7)
						<td><span>Отказ от приёма на работу</span></td>
						@elseif($response->student_response_status == 8)
						<td><span>Принят на работу и оценён</span></td>
						@elseif($response->student_response_status == 9)
						<td><span>Уволен</span></td>
						@endif
						<td>{{date_format(date_create($response->student_response_created_at), 'd.m.Y')}}</td>
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left" width="78">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										@if ($response->student_response_status == 0)
										<x-dropdown-link class="reject-btn" id="reject-btn-{{$response->student_response_id}}">
											Отказать в собеседовании
										</x-dropdown-link>
										<x-dropdown-link class="interview-btn" id="interview-btn-{{$response->student_response_id}}">
											Пригласить на собеседование
										</x-dropdown-link>
										@elseif ($response->student_response_status == 2)
										<x-dropdown-link class="work-btn" id="work-btn-{{$response->student_response_id}}">
											Принять на работу
										</x-dropdown-link>
										<x-dropdown-link class="reject-before-btn" id="reject-btn-{{$response->student_offer_id}}">
											Отказать в приёме
										</x-dropdown-link>
										@endif
										@if ($response->student_response_status == 3)
										<x-dropdown-link class="rate-btn" href="student-rate-page?student_id={{$response->student_id}}&vacancy_id={{$response->vacancy_id}}">
											Оценить
										</x-dropdown-link>
										@endif
										@if ($response->student_response_status == 8)
										<x-dropdown-link class="rate-btn" href="student-rate-page-edit?student_id={{$response->student_id}}&vacancy_id={{$response->vacancy_id}}">
											Изменить оценки
										</x-dropdown-link>
										<x-dropdown-link class="dismiss-btn" id="dismiss-btn-{{$response->student_response_id}}">
											Уволить
										</x-dropdown-link>
										@endif
										@if ($response->student_response_status == 3)
										<x-dropdown-link class="dismiss-with-marks-btn" id="dismiss-with-marks-btn-{{$response->student_response_id}}">
											<input type="hidden" class="student-mark" value="{{$response->student_id}}" />
											<input type="hidden" class="vacancy-mark" value="{{$response->vacancy_id}}" />
											<div>Уволить</div>
										</x-dropdown-link>
										@endif
										<x-dropdown-link class="view-btn" href="vacancy-details/{{$response->vacancy_id}}">
											Просмотреть вакансию
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="resume/{{$response->student_resume_id}}">
											Просмотреть резюме
										</x-dropdown-link>
									</x-slot>
								</x-dropdown>
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
				<script>
					$('.student_skill').each(function(i, elem) {
						if (i % 3 == 0)
							$(this).addClass("first-skill");
						else if (i % 3 == 1)
							$(this).addClass("second-skill");
						else if (i % 3 == 2)
							$(this).addClass("third-skill");
					})
				</script>
			</table>
			<div id="pagination"></div>
		</section>
		@else
		<div style="height:100vh;background-color:white">
			<div class="first-div text-center">

				<h1 class="big-text">На ваши вакансии ещё никто не откликнулся</h1>
			</div>
		</div>
		@endif
	</x-employer-layout>
</div>
<style>
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

	.student_skill {
		display: inline-block;
		margin-top: 3px;
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
	.interview-btn,
	.work-btn,
	.reject-btn,
	.reject-before-btn,
	.dismiss-btn,
	.dismiss-with-marks-btn {
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

	/** */
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	#interview-data {
		width: 470px;
		margin-bottom: 40px;
	}

	.modal-body {
		height: 210px;
	}
</style>

</html>
<script>
	$(".reject-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("employer.change-status") }}',
			type: "POST",
			data: {
				'id': id,
				'status': 1,
				'text': 'Успешно отправили отказ в собеседовании',
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
	$(".interview-btn").click(function() {
		$('#blurable-content').addClass("blur");
		$('#interview-modal').show();
		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$("#hidden-id").val(id);
		$("#close-modal").on('click', function() {
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			$('#interview-modal').hide();
			$("#interview-data").val("");
			$('.modal-body').css('height', '210px');
			$('#edit-errors').empty();
		})
		$("#btn-interview-data").on('click', function() {
			if ($("#interview-data").val()) {
				let id = $("#hidden-id").val();
				$.ajax({
					url: '{{ route("employer.change-status") }}',
					type: "POST",
					data: {
						'id': id,
						'status': 2,
						'text': 'Успешно отправили оффер кандидату',
						'title': 'Отправка оффера',
						'interview_data': $("#interview-data").val()
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Отправили оффер!");
					},
					error: function(msg) {
						console.log("Не получилось отправить оффер")
					}
				});
				location.reload();

			} else {
				create_notify('error', 'Ошибка отправки оффера', 'Напишите сопроводительный текст', 30);

			}
		})
	})
	$(".work-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("employer.change-status") }}',
			type: "POST",
			data: {
				'id': id,
				'status': 3,
				'text': 'Успешно приняли кандидата на работу',
				'title': 'Принятие на работу'

			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Приняли на работу!");
			},
			error: function(msg) {
				console.log("Не получилось принять на работу")
			}
		});
		location.reload();
	})
	$(".reject-before-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("employer.change-status") }}',
			type: "POST",
			data: {
				'id': id,
				'status': 4,
				'text': 'Успешно отправили отказ кандидату',
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
	$(".dismiss-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("employer.change-status") }}',
			type: "POST",
			data: {
				'id': id,
				'status': 9,
				'text': 'Успешно уволили',
				'title': 'Увольнение'
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Уволили!");
			},
			error: function(msg) {
				console.log("Не получилось уволить")
			}
		});
		location.reload();

	})
	$(".dismiss-with-marks-btn").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$("#hidden-status-id").val(id);
		$('#blurable-content').addClass("blur");
		$('#marks-modal').show();
		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		const student_id = $(this).find('.student-mark').val();
		const vacancy_id = $(this).find('.vacancy-mark').val();
		const link = `student-rate-page?student_id=${student_id}&vacancy_id=${vacancy_id}&dismiss_student=true`;
		$('#open-marks-modal a').attr('href', link)
		$("#close-marks-modal").on('click', function() {
			const id = $("#hidden-status-id").val();
			$.ajax({
				url: '{{ route("employer.change-status") }}',
				type: "POST",
				data: {
					'id': id,
					'status': 9,
					'text': 'Успешно уволили',
					'title': 'Увольнение'
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(data) {
					console.log("Уволили!");
				},
				error: function(msg) {
					console.log("Не получилось уволить")
				}
			});
			location.reload();
		})
	})

	function paginate() {
		let items = $("#all-responses-table tbody tr");
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
</script>