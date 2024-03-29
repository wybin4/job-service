<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
	<x-student-layout>
		<section style="background-color:rgb(254, 254, 254)">
			<div class="row">
				<div class="col-md-8">
					<div class="row pt-3">
						<div class="col-md-auto">
							<div class="p-2 justify-content-center">
								@if (!Auth::guard('student')->user()->image)
								<div class="future-pic">
									<div>{{mb_substr(Auth::User()->student_fio, 0, 1)}}</div>
								</div>
								@else
								<img class="pic" src="{{asset('/storage/images/'.Auth::guard('student')->user()->image)}}" />
								@endif
							</div>
						</div>
						<div class="col-md-auto mt-4">
							<div class="row">
								<div class="col-md-auto">
									<h3 class="little-header-text">{{explode(" ", Auth::User()->student_fio)[0] . " " . explode(" ", Auth::User()->student_fio)[1]}}</h3>
								</div>
								<div class="col-md-auto">
									@if($resume->status == 0)
									<div class="dot"></div>
									@else
									<div class="dot-red"></div>
									@endif
								</div>
							</div>
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
					</div>
					@if($about_me)
					<div id="about">
						<h3 class="little-header-text">Обо мне</h3>
						<div id="descript-field">{{$about_me}}</div>
						<script>
							$("#descript-field").html($("#descript-field").text())
							$("#descript-field").html($("#descript-field").html().replace(/\<br>/g, ''));
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
						@foreach($work_experience as $wexp)
						<div class="ready-div row">
							<div class="col-md-auto">
								@if ($wexp->work_title)
								<div class="sample-pic text-center">{{mb_substr($wexp->work_title, 0, 1)}}</div>
								@else
								<div class="sample-pic">{{mb_substr($wexp->company_name, 0, 1)}} / </div>
								@endif
							</div>
							<div class="col-md-auto">
								@if ($wexp->date_start != '0-00-00')
								<p class="card-title">{{$wexp->work_title}} / {{date_format(new DateTime($wexp->date_start), 'm.Y')}} - {{date_format(new DateTime($wexp->date_end), 'm.Y')}}</p>
								@else
								<p class="card-title">{{$wexp->work_title}}</p>
								@endif
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
								@if ($wedu->date_start != '0-00-00')
								<p class="card-title">{{$wedu->speciality_name}} / {{date_format(new DateTime($wedu->date_start), 'm.Y')}} - {{date_format(new DateTime($wedu->date_end), 'm.Y')}}</p>
								@else
								<p class="card-title">{{$wedu->speciality_name}}</p>
								@endif 
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
				</div>
				<div class="col-md-4">
					<div class="side-card second-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Действия</h3>
						@if ($resume->status == 0)
						<button class="button btn-edit"><a href="/student/edit-resume/{{$resume->id}}">Редактировать</a></button>

						<button class="button btn-archive">Архивировать</button>
						@else
						<button class="button btn-unarchive" id="unarchive-btn-{{$resume->id}}">Разархивировать</button>
						@endif
					</div>
					<div class="side-card" id="first-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Информация</h3>
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-solid fa-location-dot"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value">{{explode(",", Auth::User()->location)[0]}}</div>
									<div class="table-header">Местоположение</div>
								</div>
							</div>
						</div>
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-solid fa-id-card"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value contacts">{{Auth::User()->email}}</div>
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
									<i class="fa-solid fa-user-clock"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value">{{$resume->type_of_employment->type_of_employment_name}}</div>
									<div class="table-header">Вид занятости</div>
								</div>
							</div>
						</div>
					</div>
					<div class="side-card second-card">
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
	</x-student-layout>
</body>
<style>
	body {
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
	#course {
		margin-left: 120px;
		margin-top: 30px;
		margin-bottom: 30px;
	}

	#soft_skills,
	#hard_skills {
		margin-left: 120px;
		width: 900px;
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
		margin-top: -10px;
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

	#first-card,
	.second-card {
		margin-top: 30px;
	}

	.btn-edit,
	.btn-archive,
	.btn-unarchive {
		margin: 10px 0px 5px 20px;
	}

	.btn-edit:last-child,
	.btn-archive:last-child,
	.btn-unarchive:last-child {
		margin-bottom: 30px;
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

	.dot,
	.dot-red {
		margin-top: 6px;
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

	/** */
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

	.skill-rate {
		margin-top: -6px;
		margin-right: 14px;
	}

	.bar-rate {
		display: flex;
		margin-top: 8px;
	}
</style>
<script>
	$(".btn-archive").on('click', function() {

		$.ajax({
			url: '{{ route("student.archive-resume") }}',
			type: "GET",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Архивировали резюме!");
			},
			error: function(msg) {
				console.log("Не получилось архивировать резюме")
			}
		});
		window.location.href = "{{URL::to('student/dashboard')}}"
	})
	$(".btn-unarchive").on('click', function() {
		let id = $(this).attr('id').split('-');
		id = id[id.length - 1];
		$.ajax({
			url: '{{ route("student.unarchive-resume") }}',
			type: "POST",
			data: {
				'resume_id': id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Разархивировали резюме!")
			},
			error: function(msg) {
				console.log("Не получилось разархивировать резюме")
			}
		});
		window.location.href = "{{URL::to('student/dashboard')}}"
	})
	////
	///
	///	
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