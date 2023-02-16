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
		@if (count($my_workers))
		<div class="row">
			<div class="col-md-8">
				<p class="medium-text mt-4" style="margin-left:160px;">Мои работники</p>
			</div>
		</div>
		<section class='center'>
			<table class='table table-hover' id="all-workers-table">
				<thead>
					<tr class='t-head'>
						<td>Должность</td>
						<td>Компания</td>
						<td>Дата начала</td>
						<td>Статус</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					@foreach($my_workers as $my_worker)
					<tr id="row-{{$my_worker->interaction_id}}">
						<td class="work_title">{{$my_worker->profession_name}}</td>
						<td class="company_name">{{$my_worker->name}}</td>
						<td class="date_start">{{date_format(date_create($my_worker->hired_at), 'd.m.Y')}}</td>
						<input type="hidden" class="date_end" value="{{date_format(date_create($my_worker->date_end), 'd.m.Y')}}" />
						<input type="hidden" class="company_location" value="{{$my_worker->company_location}}" />
						@if($my_worker->work_status == 3)
						<td><span class="work-status work">Работает</span></td>
						@elseif($my_worker->work_status == 8)
						<td><span class="work-status work">Работает и оценён</span></td>
						@elseif($my_worker->work_status == 9)
						<td><span class="work-status rejected">Уволен</span></td>
						@endif
						<td>
							<div class="hidden sm:flex sm:items-center sm:ml-4">
								<x-dropdown align="left">
									<x-slot name="trigger">
										<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
											<div>⋮</div>
										</button>
									</x-slot>
									<x-slot name="content">
										@if($my_worker->work_status == 3)
										<x-dropdown-link class="rate-btn" href="student-rate-page?student_id={{$my_worker->student_id}}&vacancy_id={{$my_worker->vacancy_id}}">
											Оценить
										</x-dropdown-link>
										<x-dropdown-link class="dismiss-with-marks-btn" id="dismiss-with-marks-btn-{{$my_worker->student_offer_id}}">
											<input type="hidden" class="student-mark" value="{{$my_worker->student_id}}" />
											<input type="hidden" class="vacancy-mark" value="{{$my_worker->vacancy_id}}" />
											<div>Уволить</div>
										</x-dropdown-link>
										@endif
										@if($my_worker->work_status == 8)
										<x-dropdown-link class="rate-btn" href="student-rate-page-edit?student_id={{$my_worker->student_id}}&vacancy_id={{$my_worker->vacancy_id}}">
											Изменить оценки
										</x-dropdown-link>
										<x-dropdown-link class="dismiss-btn" id="dismiss-btn-{{$my_worker->student_offer_id}}">
											Уволить
										</x-dropdown-link>
										@endif
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
				<h1 class="big-text">Вы не нанимали работников с помощью нашего сервиса</h1>
			</div>
		</div>
		@endif
	</x-employer-layout>
</div>
<style>
	html {
		overflow-x: hidden;
	}

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

	.student_skill {
		display: inline-block;
		margin-top: 3px;
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
	.interview-btn,
	.work-btn,
	.reject-btn,
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

	.rate-btn,
	.dismiss-btn,
	.dismiss-with-marks-btn {
		cursor: pointer;
	}
</style>
<script>
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
		let items = $("#all-workers-table tbody tr");
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