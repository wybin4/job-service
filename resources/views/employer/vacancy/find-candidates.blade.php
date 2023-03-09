<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
</head>
@if ($resume_order_ids)
<div id="offer-popup">
	<div class="modal" id="offer-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<i class="fa-solid fa-xmark"></i>
					<input type="hidden" id="student-email" />
					<p><span class="text-muted">Тема:</span><input autocomplete="off" class="mail-input" id="mail-topic" value="Приглашение на собеседование" placeholder="Тема письма" /></p>
					<div class="hr"></div>
					<textarea class="mail-textarea" id="mail-text"></textarea>
				</div>
				<input type="hidden" id="hidden-student-id" />
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-send-offer" data-bs-dismiss="modal">Отправить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-employer-layout>
		@if (session()->get('title'))
		<script>
			create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}', -290, 'center');
		</script>
		@endif
		<div class="row">
			<div class="col-md-auto">
				<p class="font-bold text-4xl" style="margin-left:130px;margin-top:20px;"><a href="/employer/vacancy-details/{{$vacancy->id}}">{{$vacancy->profession->profession_name}}</a></p>
				<p class="text-muted" style="margin-left:130px;margin-top:-15px;">{{date_format(date_create($vacancy->created_at), 'd-m-Y')}}</p>
			</div>
			<div class="col-md-auto">
				@if($vacancy->status == 0)
				<div class="dot"></div>
				@else
				<div class="dot-red"></div>
				@endif
			</div>
			<div class="col-md-auto">
				<button class="button search-resume">
					<a href="{{ route('employer.resume-feed') }}">Поиск по резюме</a>
				</button>
			</div>
		</div>
		<section class='center'>
			<table id='candidates' class='table table-hover'>
				<tr class='t-head'>
					<td>Кандидат</td>
					<td>Опыт работы</td>
					<td>Навыки</td>
					<td>Статус</td>
					<td></td>
				</tr>
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
						@foreach($students as $student)
						<tr>
							<td>
								<div class="row">
									<div class="col-md-3">
										@if (!$student->image)
										<div class="future-pic">
											<div>{{mb_substr($student->student_fio, 0, 1)}}</div>
										</div>
										@else
										<img class="pic" src="{{asset('/storage/images/'.$student->image)}}" />
										@endif
									</div>
									<div class="col-md-9 mt-1">
										<div class="font-bold">{{explode(" ", $student->student_fio)[0] . " " . explode(" ", $student->student_fio)[1]}}</div>
										<div class="text-gray-500">{{$student->profession_name}}</div>
									</div>
								</div>
							</td>
							@if (array_search($student->resume_id, array_column($work_exps, 0)) !== false)
							@if ($work_exps[array_search($student->resume_id, array_column($work_exps, 0))][1] >= 1)
							@php $curr_year_exp = $work_exps[array_search($student->resume_id, array_column($work_exps, 0))][1]; @endphp
							<td>{{ $curr_year_exp . " " . YearTextArg($curr_year_exp) }}</td>
							@else
							@php $curr_month_exp = $work_exps[array_search($student->resume_id, array_column($work_exps, 0))][2]; @endphp
							<td>{{ $curr_month_exp . " " . MonthTextArg($curr_month_exp) }}</td>
							@endif
							@else
							<td>Без опыта</td>
							@endif
							@php $i++; @endphp
							<td class="student_skills_area">
								@php $student_skills = App\Models\StudentSkill::where('resume_id', $student->resume_id)
								->join('skills', 'skills.id', '=', 'student_skills.skill_id')
								->get()
								@endphp
								@php $j = 0; @endphp
								@foreach ($student_skills as $shs)
								@if ($j < 4) <span class="student_skill">{{$shs->skill_name}}</span>
									@endif
									@php $j++; @endphp
									@endforeach
									@if ($j >= 4)
									<span class="student_skill">+1</span>
									@endif
							</td>
							<td>
								@php $status = $resume_order[array_search($student->resume_id, array_column($resume_order, 0))][1]; @endphp
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
											<x-dropdown-link class="view-btn" href="/employer/resume/{{$student->resume_id}}" target="_blank">
												Просмотреть резюме
											</x-dropdown-link>
											<x-dropdown-link class="offer-btn" id="offer-btn-{{$vacancy->id}}" onclick="click_to_do_offer(this.id, '{{$student->student_fio}}', '{{$student->email}}', '{{$student->student_id}}')">
												Пригласить на собеседование
											</x-dropdown-link>
										</x-slot>
									</x-dropdown>
								</div>
							</td>
						</tr>
						@endforeach
			</table>
		</section>
	</x-employer-layout>
</div>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
	.view-btn,
	.offer-btn {
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

	.archive-btn,
	.unarchive-btn {
		cursor: pointer;
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
		margin-right: -50px;
	}

	.student_skill {
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

	/* */
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	.mail-input {
		outline: none !important;
		width: 410px;
		margin-left: 8px;
	}

	.mail-textarea {
		outline: none !important;
		width: 460px;
		border: none !important;
		resize: none;
		height: 280px;
	}

	.mail-textarea::-webkit-scrollbar {
		width: 6px;
	}

	.mail-textarea::-webkit-scrollbar-thumb {
		background-color: rgba(165, 180, 252, 0.7);
		border-radius: 3px;
	}

	.mail-textarea:focus {
		outline: none !important;
		border: none !important;
		box-shadow: none !important;
	}

	.hr {
		margin-top: -10px;
		margin-bottom: 10px;
		border-bottom: solid 1px #D1D5DB;
		margin-left: 0;
	}

	.fa-xmark {
		margin-left: 450px;
		margin-top: 5px;
		margin-bottom: 5px;
		cursor: pointer;
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

	.search-resume {
		padding: 7px 15px;
		border-radius: 8px;
		margin-left: 670px;
		margin-top: 30px;
	}

	.search-resume a {
		color: white !important;
	}
</style>
<script>
	// общие кнопки для пагинации
	function pageButtons($pCount, $cur, $name) {
		let $prevDis = ($cur == 1) ? "disabled" : "",
			$nextDis = ($cur == $pCount) ? "disabled" : "",
			$buttons = "<input type='button' value=&laquo; onclick='sort_" + $name + "(" + ($cur - 1) + ")' " + $prevDis + ">";
		for ($i = 1; $i <= $pCount; $i++)
			$buttons += "<input type='button' id='" + $name + "-page-btn" + $i + "'value='" + $i + "' onclick='sort_" + $name + "(" + $i + ")'>";
		$buttons += "<input type='button' value=&raquo; onclick='sort_" + $name + "(" + ($cur + 1) + ")' " + $nextDis + ">";
		return $buttons;
	}

	// для пагинации по всем вакансиям
	let $allVacanciesTable = document.getElementById("candidates"),
		$n = 7,
		$i, $ii, $j = 1,
		$rowCountAll = $allVacanciesTable.rows.length,
		$tr_all = [],
		$th_all = ($allVacanciesTable.rows[(0)].outerHTML);
	let $pageCountAll = Math.ceil($rowCountAll / $n);
	if ($pageCountAll > 1) {
		for ($i = $j, $ii = 0; $i < $rowCountAll; $i++, $ii++)
			$tr_all[$ii] = $allVacanciesTable.rows[$i].outerHTML;
		$allVacanciesTable.insertAdjacentHTML("afterend", "<div id='pagination-buttons-all' style='margin-left:35px'></div");
		sort_all(1);
	}



	// все заявки
	function sort_all($p) {
		let $rows = $th_all,
			$s = (($n * $p) - $n);
		for ($i = $s; $i < ($s + $n) && $i < $tr_all.length; $i++)
			$rows += $tr_all[$i];

		$allVacanciesTable.innerHTML = $rows;
		document.getElementById("pagination-buttons-all").innerHTML = pageButtons($pageCountAll, $p, "all");
		document.getElementById("all-page-btn" + $p).setAttribute("class", "active-page-btn");
	}

	window.onclick = function(event) {
		if (!event.target.matches('.dropbtn')) {

			var dropdowns = document.getElementsByClassName("dropdown-content");
			var i;
			for (i = 0; i < dropdowns.length; i++) {
				var openDropdown = dropdowns[i];
				if (openDropdown.classList.contains('show')) {
					openDropdown.classList.remove('show');
				}
			}
		}
	}

	function click_to_drop(id) {
		let vacancy_id = id.split('-');
		vacancy_id = vacancy_id[vacancy_id.length - 1];
		$(`#dot-dropdown-${vacancy_id}`).addClass('show');
	}

	function click_to_do_offer(id, student_name, student_email, student_id) {
		student_name = student_name.split(' ')[1];
		$('#offer-modal').find('.mail-textarea').text(`${student_name}, добрый день!

В настоявшее время в нашей компании открыта вакансия "{{$vacancy->profession->profession_name}}". Ваше резюме нас заинтересовало, и мы хотели бы пригласить Вас участвовать в конкурсе на эту позицию.

В случае, если у Вас появится вопросы, Вы можете связаться с нами по адресу {{Auth::guard('employer')->user()->email}}. Хорошего дня!`)
		$('#offer-modal').find('#student-email').val(student_email);
		$('#offer-modal').find('#hidden-student-id').val(student_id);

		$('#offer-modal').show();

		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
	}
	$(".fa-xmark").click(function() {
		$('html, body').css({
			overflow: 'auto',
			height: 'auto'
		});
		//убираем блюр
		$('#blurable-content').removeClass("blur");
		$('#offer-modal').hide();
	})
	$("#btn-send-offer").click(function() {
		$('html, body').css({
			overflow: 'auto',
			height: 'auto'
		});
		//убираем блюр
		$('#blurable-content').removeClass("blur");
		$('#offer-modal').hide();

		const topic = $('#offer-modal').find("#mail-topic").val();
		const text = $('#offer-modal').find("#mail-text").val();
		const student_email = $('#offer-modal').find("#student-email").val();
		const student_id = $('#offer-modal').find("#hidden-student-id").val();
		console.log(student_id)
		const vacancy_id = "{{$vacancy->id}}";
		$.ajax({
			url: '{{ route("employer.send-offer") }}',
			type: "POST",
			data: {
				'vacancy_id': vacancy_id,
				'student_id': student_id,
				'student_email': student_email,
				'topic': topic,
				'text': text
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
	})
</script>
@else
<x-employer-layout>
	<div style="height:100vh;background-color:white">
		<div class="first-div text-center">

			<h1 class="big-text">Подходящих резюме не найдено. Воспользуйтесь <span class="big-indigo-text">общим поиском</span>.</h1>
			<span class="big-indigo-underline"></span>
			<p id="popular" class="mt-3"><span class="text-muted font-bold examples-p">Популярные запросы:</span>
				<span>
					@php $i = 1; @endphp
					@foreach($popular_professions as $pp)
					@if ($i == count($popular_professions))
					<a href="/employer/resume-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}</a>
					@else
					<a href="/employer/resume-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}, </a>
					@endif
					@php $i++; @endphp
					@endforeach
				</span>
			</p>
		</div>
	</div>
</x-employer-layout>
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
</style>
@endif

</html>