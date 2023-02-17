<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
	<script src="{{asset('/js/range-functions.js')}}"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div id="offer-popup">
	<div class="modal" id="offer-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<i class="fa-solid fa-xmark"></i>
					<input type="hidden" id="student-email" />
					<p><span class="text-muted">Тема:</span><input autocomplete="off" class="mail-input" id="mail-topic" value="Приглашение на собеседование" placeholder="Тема письма" /></p>
					<div class="mail-hr"></div>
					<div class="select-div">
						<input autocomplete="off" type="hidden" id="popup-vacancy-id" />
						<span class="text-muted">Вакансия:</span><input autocomplete="off" id="select" class="chosen-value" type="text" value="">
						<ul class="value-list" id="value-list-1">
							@if (count($binded_vacancies) == 0)
							@foreach($employer_vacancies as $ev)
							<li value="{{$ev->id}}">{{$ev->profession->profession_name}}</li>
							@endforeach
							@else
							@foreach($enabeled_vacancies as $ev)
							<li value="{{$ev->id}}">{{$ev->profession_name}}</li>
							@endforeach
							@endif
						</ul>
					</div>
					<div class="mail-hr pb-3"></div>
					<textarea class="mail-textarea" id="mail-text"></textarea>
				</div>
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
		<section style="background-color:rgb(254, 254, 254)">
			<div class="row">
				<div class="col-md-8">
					<div class="row pt-3">
						<div class="col-md-auto">
							<div class="p-2 justify-content-center">
								@if (!$student->image)
								<div class="future-pic">
									<div>{{mb_substr($student->student_fio, 0, 1)}}</div>
								</div>
								@else
								<img class="pic" src="{{asset('/storage/images/'.$student->image)}}" />
								@endif
							</div>
						</div>
						<div class="col-md-auto mt-4">
							<h3 class="little-header-text">{{explode(" ", $student->student_fio)[0] . " " . explode(" ", $student->student_fio)[1]}}</h3>
							<div class="text-muted">{{$resume->profession->profession_name}}</div>
						</div>
					</div>
					<div class="tabs">
						@if($about_me)
						<a href="#about" class="click-tab-active click-tab"><i class="fa-regular fa-file-lines pt-2 mr-2"></i>Обо мне</a>
						@endif
						<a href="#hard_skills" class="click-tab"><i class="fa-solid fa-list pt-2 mr-2"></i>Навыки</a>
						<a href="#soft_skills" class="click-tab"><i class="fa-solid fa-list pt-2 mr-2"></i>Качества</a>
						@if (count($resume->work_experience) != 0)
						<a href="#experience" class="click-tab"><i class="fa-solid fa-table pt-2 mr-2"></i>Опыт работы</a>
						@endif
						@if (count($resume->education) != 0)
						<a href="#education" class="click-tab"><i class="fa-solid fa-user-graduate pt-2 mr-2"></i>Образование</a>
						@endif
						@if (count($resume->course) != 0)
						<a href="#course" class="click-tab"><i class="fa-solid fa-laptop pt-2 mr-2"></i>Курсы</a>
						@endif
						@if(count($employer_rates))
						<a href="#review" class="click-tab"><i class="fa-regular fa-comment pt-2 mr-2"></i>Отзывы</a>
						@endif
					</div>
					@if($about_me)
					<div id="about">
						<h3 class="little-header-text">Обо мне</h3>
						<div id="descript-field">{{$about_me}}</div>
						<script>
							$("#descript-field").html($("#descript-field").text())
						</script>
					</div>
					@else <div class="mt-4"></div>
					@endif
					<div id="hard_skills">
						<h3 class="little-header-text mb-4 mt-3">Мои навыки</h3>
						<div class="row rate-body">
							<div class="col-md-auto">
								<div id="review-rate-hard" class="review-rate"></div>
								<div class="review-count"></div>
							</div>
							<div class="col-md-auto">
								@foreach($student_skills as $ss)
								@if ($ss->skill_type == 1)
								<div class="bar-rate">
									<div class="skill-rate">{{$ss->skill_rate}}.0</div>
									<div class="progress" id="skill-bar-{{$ss->skill_id}}">
										<div class="bar"></div>
									</div>
									<div class="skill-name">{{$ss->skill_name}}</div>
								</div>
								@endif
								@endforeach
							</div>
						</div>
					</div>
					<div id="soft_skills">
						<h3 class="little-header-text mb-4 mt-3">Мои качества</h3>
						<div class="row rate-body">
							<div class="col-md-auto">
								<div id="review-rate-soft" class="review-rate"></div>
								<div class="review-count"></div>
							</div>
							<div class="col-md-auto">
								@foreach($student_skills as $ss)
								@if ($ss->skill_type == 0)
								<div class="bar-rate">
									<div class="skill-rate">{{$ss->skill_rate}}.0</div>
									<div class="progress" id="skill-bar-{{$ss->skill_id}}">
										<div class="bar"></div>
									</div>
									<div class="skill-name">{{$ss->skill_name}}</div>
								</div>
								@endif
								@endforeach
							</div>
						</div>
					</div>
					@if (count($resume->work_experience) != 0)
					<div id="experience">
						<h3 class="little-header-text">Опыт работы</h3>
						@foreach($resume->work_experience as $wexp)
						<div class="ready-div row">
							<div class="col-md-auto">
								@if ($wexp->work_title)
								<div class="sample-pic text-center">{{mb_substr($wexp->work_title, 0, 1)}}</div>
								@else
								<div class="sample-pic">{{mb_substr($wexp->company_name, 0, 1)}} / </div>
								@endif
							</div>
							<div class="col-md-auto">
								<p class="card-title">{{$wexp->work_title}} / {{date_format(new DateTime($wexp->date_start), 'Y')}} - {{date_format(new DateTime($wexp->date_end), 'Y')}}</p>
								@if ($wexp->work_title)
								<p class="card-name">{{$wexp->company_name}}, {{$wexp->location}}</p>
								@endif
								<p class="card-desc">{{$wexp->description}}</p>
							</div>
						</div>
						@endforeach
					</div>
					@endif
					@if (count($resume->education) != 0)
					<div id="education">
						<h3 class="little-header-text">Образование</h3>
						@foreach($resume->education as $wedu)
						<div class="ready-div row">
							<div class="col-md-auto">
								@if ($wedu->speciality_name)
								<div class="sample-pic text-center">{{mb_substr($wedu->speciality_name, 0, 1)}}</div>
								@else
								<div class="sample-pic">{{mb_substr($wedu->university_name, 0, 1)}} / </div>
								@endif
							</div>
							<div class="col-md-auto">
								<p class="card-title">{{$wedu->speciality_name}} / {{date_format(new DateTime($wedu->date_start), 'Y')}} - {{date_format(new DateTime($wedu->date_end), 'Y')}}</p>
								<p class="card-name">{{$wedu->university_name}}, {{$wedu->location}}</p>
								<p class="card-desc">{{$wedu->description}}</p>
							</div>
						</div>
						@endforeach
					</div>
					@endif
					@if (count($resume->course) != 0)
					<div id="course">
						<h3 class="little-header-text">Курсы</h3>
						@foreach($resume->course as $wcu)
						<div class="ready-div row">
							<div class="col-md-auto">
								@if ($wcu->course_name)
								<div class="sample-pic text-center">{{mb_substr($wcu->course_name, 0, 1)}}</div>
								@else
								<div class="sample-pic">{{mb_substr($wcu->platform_name, 0, 1)}} / </div>
								@endif
							</div>
							<div class="col-md-auto">
								<p class="card-title">{{$wcu->course_name}}</p>
								<p class="card-name">{{$wcu->platform_name}}</p>
							</div>
						</div>
						@endforeach
					</div>
					@endif
					@if(count($employer_rates))
					<div id="review">
						<h3 class="little-header-text">Отзывы</h3>
						<div class="row rate-body">
							<div class="col-md-auto">
								<div class="review-rate"></div>
								<div class="review-count"></div>
							</div>
							<div class="col-md-auto">
								@foreach($student_skills as $ss)
								<div class="bar-rate">
									<div class="skill-rate">{{$ss->skill_rate}}.0</div>
									<div class="progress" id="skill-bar-{{$ss->skill_id}}">
										<div class="bar"></div>
									</div>
									<div class="skill-name">{{$ss->skill_name}}</div>
								</div>
								@endforeach
							</div>
						</div>
						<div class="review-body">
							@foreach($reviews as $review)
							<div class="mb-4">
								<div class="row">
									<div class="col-md-auto">
										@if (!$review->image)
										<div class="review-future-pic future-pic">
											<div>{{mb_substr($review->name, 0, 1)}}</div>
										</div>
										@else
										<img class="review-pic pic" src="{{asset('/storage/images/'.$review->image)}}" />
										@endif
									</div>
									<div class="col-md-auto">
										<div class="review-employer-name">{{$review->name}}</div>
										<div class="review-employer-time">{{$review->review_updated_at}}</div>
									</div>
								</div>
								<div class="review-text">
									{{$review->text}}
								</div>
							</div>
							@endforeach
						</div>
					</div>
					@endif
				</div>
				<div class=" col-md-4">
					@if (count($employer_vacancies))
					@if (count($binded_vacancies) && count($binded_vacancies) != count($employer_vacancies))
					<button class="btn-offer button" onclick="click_to_do_offer('{{$student->student_fio}}', '{{$student->email}}')">Пригласить на собеседование</button>
					<div class="side-card" id="third-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Взаимодействия</h3>
						@if(count($binded_vacancies->where('type', 0)))
						<button class="button" id="view-response"><a href="/employer/all-vacancy-responses">Отклики по моим вакансиям</a></button>
						@endif
						@if(count($binded_vacancies->where('type', 1)))
						<button class="button my-offers" id="view-response"><a href="/employer/all-vacancy-offers">Мои офферы</a></button>
						@endif
					</div>
					@elseif(count($binded_vacancies))
					<div class="side-card" id="third-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Взаимодействия</h3>
						@if(count($binded_vacancies->where('type', 0)))
						<button class="button" id="view-response"><a href="/employer/all-vacancy-responses">Отклики по моим вакансиям</a></button>
						@endif
						@if(count($binded_vacancies->where('type', 1)))
						<button class="button my-offers" id="view-response"><a href="/employer/all-vacancy-offers">Мои офферы</a></button>
						@endif
					</div>
					@elseif(count($binded_vacancies) == 0)
					<button class="btn-offer button" onclick="click_to_do_offer('{{$student->student_fio}}', '{{$student->email}}')">Пригласить на собеседование</button>
					@endif
					@endif
					<div class="side-card" id="first-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Информация</h3>
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-solid fa-location-dot"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value">{{explode(",", $student->location)[0]}}</div>
									<div class="table-header">Местоположение</div>
								</div>
							</div>
						</div>
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-regular fa-address-book"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value contacts">{{$student->email}}</div>
									<div class="table-header">Контакты</div>
								</div>
							</div>
						</div>
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-solid fa-briefcase"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value">{{$resume->work_type->work_type_name}}</div>
									<div class="table-header">Тип работы</div>
								</div>
							</div>
						</div>
						<div class="side-info pb-5">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-regular fa-clock"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value">{{$resume->type_of_employment->type_of_employment_name}}</div>
									<div class="table-header">Вид занятости</div>
								</div>
							</div>
						</div>
					</div>
					<div class="side-card" id="second-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">О резюме</h3>
						@if($resume->status == 0)
						@php
						$currentDate = $resume->created_at;
						$_monthsList = array(
						".01." => "января",
						".02." => "февраля",
						".03." => "марта",
						".04." => "апреля",
						".05." => "мая",
						".06." => "июня",
						".07." => "июля",
						".08." => "августа",
						".09." => "сентября",
						".10." => "октября",
						".11." => "ноября",
						".12." => "декабря"
						);
						$_mD = date(".m.", strtotime($currentDate));
						$currentDate = date("d", strtotime($currentDate)) . " " . $_monthsList[$_mD] . " " . date("Y", strtotime($currentDate));
						@endphp
						<div class="side-info pb-4" id="date-publish">
							Опубликовано {{$currentDate}}
						</div>
						@else
						@php
						$currentDate = $resume->archived_at;
						$_monthsList = array(
						".01." => "января",
						".02." => "февраля",
						".03." => "марта",
						".04." => "апреля",
						".05." => "мая",
						".06." => "июня",
						".07." => "июля",
						".08." => "августа",
						".09." => "сентября",
						".10." => "октября",
						".11." => "ноября",
						".12." => "декабря"
						);
						$_mD = date(".m.", strtotime($currentDate));
						$currentDate = date("d", strtotime($currentDate)) . " " . $_monthsList[$_mD] . " " . date("Y", strtotime($currentDate));
						@endphp
						<div class="side-info pb-4" id="date-publish">
							Архивировано {{$currentDate}}
						</div>
						@endif
					</div>
				</div>
			</div>
		</section>
	</x-employer-layout>
</div>
<style>
	html {
		overflow-x: hidden;
	}

	section {
		height: 100vh;
	}

	.future-pic {
		font-size: 32px;
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
		width: 80px;
		height: 80px;
		margin-left: 110px;
	}

	.tabs {
		margin-left: 90px;
		margin-top: 30px;
	}

	.click-tab {
		padding: 10px 15px;
		background-color: white;
		border-radius: 20px;
		transition: 0.5s;
		margin-left: 20px;
	}

	.click-tab:hover {
		color: var(--link-hover-color) !important;
	}

	#about {
		margin-left: 120px;
		margin-top: 50px;
	}

	#experience,
	#education,
	#course,
	#review {
		margin-left: 120px;
		margin-top: 30px;
		margin-bottom: 30px;
	}

	#skills,
	#soft_skills,
	#hard_skills {
		margin-left: 120px;
		width: 900px;
	}

	.self-rate {
		color: var(--link-hover-color);
	}

	#descript-field {
		margin-top: 10px;
	}

	.tag-area {
		flex-wrap: wrap;
	}

	.card-tag {
		margin-right: 5px;
		padding: 10px 15px;
		border-radius: 20px;
		white-space: nowrap;
		margin-top: 5px;
		background-color: var(--dot-color);
	}

	.ready-div {
		margin-top: 20px;
	}

	.sample-pic {
		width: 40px;
		height: 40px;
		border-radius: 100px;
		background-color: var(--dot-color);
		color: var(--link-hover-color);
		display: table-cell;
		vertical-align: middle;
	}

	.side-sample-pic {
		border-radius: 100px;
		width: 40px;
		height: 40px;
		margin-left: 20px;
	}

	.side-sample-pic i {
		margin-top: 11px;
	}

	.card-title {
		font-size: 18px;
		width: 600px;
	}

	.card-name {
		color: grey;
		font-size: 15px;
		width: 600px;
	}

	.card-desc {
		margin-top: 10px;
		width: 600px;
	}

	.side-card {
		background-color: white;
		color: black;
		border: solid 1px #e7e8ea;
		border-radius: 20px;
		min-width: 260px;
		max-width: 300px;
		padding: 0 10px;
	}

	.btn-offer {
		margin-top: 40px;
		margin-left: 50px
	}

	.side-info {
		margin-top: 10px;
	}

	#date-publish {
		margin-left: 20px;
	}

	.table-header {
		color: grey;
		font-size: 13px;
		margin-top: -5px;
	}

	/** */

	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	.mail-hr {
		margin: 10px 0px;
		border-bottom: solid 1px #D1D5DB;
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

	.fa-xmark {
		margin-left: 450px;
		margin-top: 5px;
		margin-bottom: 5px;
		cursor: pointer;
	}

	.select-div,
	.chosen-value,
	.value-list {
		width: 400px !important;
	}

	/** */
	#first-card,
	#second-card,
	#third-card {
		margin-top: 30px;
	}

	#view-response {
		margin: 10px 0px 20px 20px;
	}

	.my-offers {
		padding-left: 75px !important;
		padding-right: 75px !important;
	}


	/** */
	.rate-body {
		margin-top: 20px;
	}

	.review-rate {
		color: var(--link-hover-color);
		font-size: 36px;
		font-weight: bold;
		text-align: center;
	}

	.review-count {
		color: grey;
	}

	.progress {
		width: 250px;
		height: 8px;
		background-color: var(--dot-color);
	}

	.bar {
		height: 8px;
		background-color: var(--link-hover-color);
	}

	.skill-name {
		margin-top: -6px;
		margin-left: 20px;
	}

	.bar-rate {
		display: flex;
		margin-top: 8px;
	}

	.skill-rate {
		margin-top: -6px;
		margin-right: 14px;
	}

	/** */

	.review-future-pic,
	.review-pic {
		width: 40px;
		height: 40px;
		font-size: 16px;
	}

	.review-employer-name,
	.review-employer-time {
		margin-left: -9px;
	}

	.review-employer-name {
		font-weight: bold;
	}

	.review-employer-time {
		font-size: 15px;
		color: grey;
		margin-top: -5px;
	}

	.review-body {
		margin-left: -120px;
		margin-top: 20px;
	}

	.review-text {
		margin-left: 110px;
		margin-top: 20px;
	}
</style>
<script>
	let sr = <?php echo json_encode($student_skills); ?>;
	sr = sr.filter((r) => {
		return r.skill_type == 1;
	})
	let student_rates = [];
	let employer_rates = [];
	// получаем student_rates с отдельными skill_id и массивом оценок к нему
	if (sr.length) {
		for (let i = 0; i < sr.length; i++) {
			student_rates.push([sr[i].skill_id, sr[i].skill_rate]);
		}
	}
	const er = <?php echo json_encode($employer_rates); ?>;
	if (er.length) {
		let rates = new Set(er.map(r => r.skill_id));
		rates = Array.from(rates);
		let arr;
		// получаем employer_rates с отдельными skill_id и массивом оценок к нему
		for (let i = 0; i < rates.length; i++) {
			arr = er.filter((r) => {
				return r.skill_id == rates[i]
			});
			if (arr.length > 1) {
				employer_rates.push([rates[i],
					get_trend([arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.skill_rate)])
				]);
			} else {
				employer_rates.push([rates[i],
					[arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.skill_rate)]
				])
			}
		}
		// получаем ema для employer_rates
		let employer_ema = [];
		employer_rates.forEach(function(rate) {
			employer_ema.push([rate[0], get_ema(rate[1][1])])
		})

		$(".progress").each(function() {
			let skill_id = $(this).attr('id');
			skill_id = skill_id.split('-').pop();
			let skill = employer_ema.filter((ee) => {
				return ee[0] == skill_id;
			})
			$(this).find('.bar').css('width', skill[0][1] / 5 * 100 + '%');
		})
		const sum = employer_ema.reduce((acc, number) => acc + number[1], 0);
		const length = employer_ema.length;
		$(".review-rate").text((sum / length).toFixed(2));
		const count_employers = er.length / employer_ema.length;
		$(".review-count").text(count_employers + ' ' + getNoun(count_employers, 'работодатель', 'работодателя', 'работодателей'));
	}
	$(".review-employer-time").each(function() {
		let date = moment.duration(moment().diff($(this).text())).humanize();
		$(this).text(date + ' назад');
	})

	function click_to_do_offer(student_name, student_email) {
		let inputField1 = document.getElementById('select');
		let dropdown1 = document.getElementById("value-list-1");
		let dropdownArray1 = [...dropdown1.querySelectorAll('li')];

		function closeDropdown(dropdown) {
			dropdown.classList.remove('open');
		}
		closeDropdown(dropdown1);

		inputField1.addEventListener('input', () => {
			let valueArray1 = [];
			dropdownArray1.forEach(item => {
				valueArray1.push(item.textContent);
			});
			dropdown1.classList.add('open');
			let inputValue = inputField1.value.toLowerCase();
			let valueSubstring;
			if (inputValue.length > 0) {
				for (let j = 0; j < valueArray1.length; j++) {
					if (!(inputValue.substring(0, inputValue.length) === valueArray1[j].substring(0, inputValue.length).toLowerCase())) {
						dropdownArray1[j].classList.add('closed');
					} else {
						dropdownArray1[j].classList.remove('closed');
					}
				}
			} else {
				for (let i = 0; i < dropdownArray1.length; i++) {
					dropdownArray1[i].classList.remove('closed');
				}
			}
		});
		dropdownArray1.forEach(item => {
			item.addEventListener('click', (evt) => {
				inputField1.value = item.textContent;

				$('#offer-modal').find('.mail-textarea').text(`${student_name}, добрый день!

В настоявшее время в нашей компании открыта вакансия "${item.textContent}". Ваше резюме нас заинтересовало, и мы хотели бы пригласить Вас участвовать в конкурсе на эту позицию.

В случае, если у Вас появится вопросы, Вы можете связаться с нами по адресу {{Auth::guard('employer')->user()->email}}. Хорошего дня!`)
				$("#popup-vacancy-id").val(item.value);
				dropdownArray1.forEach(dropdown1 => {
					dropdown1.classList.add('closed');
				});
			});
		})

		inputField1.addEventListener('focus', () => {
			dropdown1.classList.remove('open');
			inputField1.placeholder = 'Поиск';
			dropdown1.classList.add('open');
			dropdownArray1.forEach(dropdown1 => {
				dropdown1.classList.remove('closed');
			});
		});

		inputField1.addEventListener('blur', () => {
			dropdown1.classList.remove('open');
		});


		student_name = student_name.split(' ')[1];
		$('#offer-modal').find('.mail-textarea').text(`${student_name}, добрый день!

В настоявшее время в нашей компании открыта вакансия. Ваше резюме нас заинтересовало, и мы хотели бы пригласить Вас участвовать в конкурсе на эту позицию.

В случае, если у Вас появится вопросы, Вы можете связаться с нами по адресу {{Auth::guard('employer')->user()->email}}. Хорошего дня!`)
		$('#offer-modal').find('#student-email').val(student_email);
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
		const vacancy_id = $("#popup-vacancy-id").val();
		$.ajax({
			url: '{{ route("employer.send-offer") }}',
			type: "POST",
			data: {
				'vacancy_id': vacancy_id,
				'student_id': "{{$student->id}}",
				'student_email': student_email,
				'topic': topic,
				'text': text
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Отправили оффер!")
			},
			error: function(msg) {
				console.log("Не получилось отправить оффер")
			}

		});
		location.reload();

	})
	let skill_rates = <?php echo json_encode($student_skills); ?>;
	$(".progress").each(function() {
		let skill_id = $(this).attr('id');
		skill_id = skill_id.split('-').pop();
		let skill = skill_rates.filter((ee) => {
			return ee.skill_id == skill_id;
		})
		skill = skill[0];
		$(this).find('.bar').css('width', skill.skill_rate / 5 * 100 + '%');
	})
	let hard = skill_rates.filter((sr) => {
		return sr.skill_type == 1;
	})
	let soft = skill_rates.filter((sr) => {
		return sr.skill_type == 0;
	})
	const sum_soft = soft.reduce(function(sum, elem) {
		return sum + elem.skill_rate;
	}, 0);
	const sum_hard = hard.reduce(function(sum, elem) {
		return sum + elem.skill_rate;
	}, 0);
	$("#review-rate-soft").text((sum_soft / soft.length).toFixed(2));
	$("#review-rate-hard").text((sum_hard / hard.length).toFixed(2));
	$(".review-count").text('В среднем');
</script>

</html>