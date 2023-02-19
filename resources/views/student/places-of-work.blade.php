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
<x-student-layout>
	@if (session()->get('title'))
	<script>
		create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -290, 'center');
	</script>
	@endif
	@if (count($places_of_work))
	<div class="row">
		<div class="col-md-8">
			<p class="medium-text mt-4" style="margin-left:160px;">Мои места работы</p>
		</div>
	</div>
	<section class='center'>
		<table class='table table-hover' id="all-works-table">
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
				@foreach($places_of_work as $place_of_work)
				<tr id="row-{{$place_of_work->interaction_id}}">
					<td class="work_title">{{App\Models\Profession::find($place_of_work->vacancy_profession_id)->profession_name}}</td>
					<td class="company_name">{{$place_of_work->name}}</td>
					<td class="date_start">{{date_format(date_create($place_of_work->hired_at), 'd.m.Y')}}</td>
					<input type="hidden" class="date_end" value="{{date_format(date_create($place_of_work->date_end), 'd.m.Y')}}" />
					<input type="hidden" class="company_location" value="{{$place_of_work->company_location}}" />
					@if($place_of_work->work_status == 3)
					<td><span class="work-status work">Работаю</span></td>
					@elseif($place_of_work->work_status == 8)
					<td><span class="work-status work">Работаю</span></td>
					@elseif($place_of_work->work_status == 9)
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
									@if(!in_array($place_of_work->vacancy_id, $vacancies_with_rate))
									<x-dropdown-link class="rate-btn" href="employer-rate-page?employer_id={{$place_of_work->employer_id}}&vacancy_id={{$place_of_work->vacancy_id}}">
										Оценить
									</x-dropdown-link>
									@else
									<x-dropdown-link class="rate-btn" href="employer-rate-page-edit?employer_id={{$place_of_work->employer_id}}&vacancy_id={{$place_of_work->vacancy_id}}">
										Изменить оценки
									</x-dropdown-link>
									@endif
									@if($place_of_work->work_status == 9 && in_array($place_of_work->vacancy_id, $work_experiences))
									<x-dropdown-link class="add-exp" id="exp-{{$place_of_work->interaction_id}}">
										Добавить как опыт в резюме
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
			<h1 class="big-text">Вы не устраивались на работу с помощью нашего сервиса</h1>
		</div>
	</div>
	@endif
</x-student-layout>
<style>
	.add-exp {
		cursor: pointer;
	}

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
</style>
<script>
	function paginate() {
		let items = $("#all-works-table tbody tr");
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
	$(".add-exp").on('click', function() {
		let id = $(this).attr('id');
		id = id.split("-");
		id = id[id.length - 1];
		let form = new FormData();
		form.append('company_name', $(`#row-${id}`).find(".company_name").text());
		form.append('work_title', $(`#row-${id}`).find(".work_title").text());
		form.append('date_start', $(`#row-${id}`).find(".date_start").text());
		form.append('date_end', $(`#row-${id}`).find(".date_end").val());
		form.append('company_location', $(`#row-${id}`).find(".company_location").val());
		$.ajax({
			url: '{{ route("student.add-experience") }}',
			type: "POST",
			data: form,
			cache: false,
			contentType: false,
			processData: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Добавили опыт!");
			},
			error: function(msg) {
				console.log("Не получилось добавить опыт")
			}
		});
		location.reload();
	})
</script>