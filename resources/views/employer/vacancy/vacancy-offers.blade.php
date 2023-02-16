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
		<div class="row">
			<div class="col-md-8">
				<p class="medium-text mt-4" style="margin-left:160px;">{{$vacancy->profession->profession_name}}</p>
				<p class="little-header-text" style="margin-top:-10px;margin-left:160px;color:var(--link-hover-color)">мои офферы</p>
			</div>
			<div class="col-md-3">
				<button class="button search">
					<a href="/employer/vacancy-details/{{$vacancy->id}}">{{ __('Просмотреть вакансию') }}</a>
				</button>
			</div>
		</div>
		<section class='center'>
			<table class='table table-hover' id="one-offer-table">
				<thead>
					<tr class='t-head'>
						<td>ФИО студента</td>
						<td>Профессия</td>
						<td>Статус</td>
						<td>Дата</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					@foreach($interactions as $offer)
					<tr>
						<td>{{$offer->student_fio}}</td>
						<td>{{App\Models\Profession::find($offer->resume_profession_id)->profession_name}}</td>
						@if ($offer->student_offer_status == 0)
						<td><span class="work-status not-considered">Не рассмотрен</span></td>
						@elseif($offer->student_offer_status == 1)
						<td><span class="work-status rejected">Отказ от собеседования</span></td>
						@elseif($offer->student_offer_status == 2)
						<td><span class="work-status interview">Собеседование</span></td>
						@elseif($offer->student_offer_status == 3)
						<td><span class="work-status work">Принят на работу</span></td>
						@elseif($offer->student_offer_status == 4)
						<td><span class="work-status rejected">Не принят на работу</span></td>
						@elseif($offer->student_offer_status == 7)
						<td><span class="work-status rejected">Отказ от приёма на работу</span></td>
						@elseif($offer->student_offer_status == 8)
						<td><span class="work-status work">Принят на работу и оценён</span></td>
						@elseif($offer->student_offer_status == 9)
						<td><span class="work-status rejected">Уволен</span></td>
						@endif
						<td>{{date_format(date_create($offer->student_offer_created_at), 'd.m.Y')}}</td>
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left" width="78">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										@if($offer->student_offer_status == 2)
										<x-dropdown-link class="work-btn" id="work-btn-{{$offer->student_offer_id}}">
											Принять на работу
										</x-dropdown-link>
										<x-dropdown-link class="reject-btn" id="reject-btn-{{$offer->student_offer_id}}">
											Отказать в приёме
										</x-dropdown-link>
										@endif
										@if($offer->student_offer_status == 3)
										<x-dropdown-link class="rate-btn" href="/employer/student-rate-page?student_id={{$offer->student_id}}&vacancy_id={{$offer->vacancy_id}}">
											Оценить
										</x-dropdown-link>
										<x-dropdown-link class="dismiss-with-marks-btn" id="dismiss-with-marks-btn-{{$offer->student_offer_id}}">
											<input type="hidden" class="student-mark" value="{{$offer->student_id}}" />
											<input type="hidden" class="vacancy-mark" value="{{$offer->vacancy_id}}" />
											<div>Уволить</div>
										</x-dropdown-link>
										@endif
										@if($offer->student_offer_status == 8)
										<x-dropdown-link class="rate-btn" href="/employer/student-rate-page-edit?student_id={{$offer->student_id}}&vacancy_id={{$offer->vacancy_id}}">
											Изменить оценки
										</x-dropdown-link>
										<x-dropdown-link class="dismiss-btn" id="dismiss-btn-{{$offer->student_offer_id}}">
											Уволить
										</x-dropdown-link>
										@endif
										<x-dropdown-link class="view-btn" href="/employer/vacancy-details/{{$offer->vacancy_id}}">
											Просмотреть вакансию
										</x-dropdown-link>
										<x-dropdown-link class="view-btn" href="/employer/resume/{{$offer->student_resume_id}}">
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
	</x-employer-layout>
</div>
<style>
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
	.work-btn,
	.reject-btn,
	.dismiss-btn,
	.dismiss-with-marks-btn {
		cursor: pointer;
	}

	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
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
		const link = `/employer/student-rate-page?student_id=${student_id}&vacancy_id=${vacancy_id}&dismiss_student=true`;
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
		let items = $("#one-offer-table tbody tr");
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