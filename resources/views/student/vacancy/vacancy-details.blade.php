<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/lvovich/dist/lvovich.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>@php function YearTextArg($year) {
	$year = abs($year);
	$t1 = $year % 10;
	$t2 = $year % 100;
	return ($t1 == 1 && $t2 != 11 ? "год" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2>= 20) ? "года" : "лет"));
		}@endphp
		<x-student-layout>
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
									@if (!$employer->image)
									<div class="future-pic">
										<div>{{mb_substr($employer->name, 0, 1)}}</div>
									</div>
									@else
									<img class="pic" src="{{asset('/storage/images/'.$employer->image)}}" />
									@endif
								</div>
							</div>
							<div class="col-md-auto mt-4">
								<div class="little-header-text">{{$profession->profession_name}}</div>
								<a class="text-muted" href="/student/employer/{{$employer->id}}">{{$employer->name}}</a>
							</div>
						</div>
						<div class="tabs">
							@if($parsed_desc)
							<a href="#about" class="click-tab-active click-tab"><i class="fa-regular fa-file-lines pt-2 mr-2"></i>Описание</a>
							@endif
							<a href="#skills" class="click-tab"><i class="fa-solid fa-list pt-2 mr-2"></i>Навыки</a>
							@if (count($related_vacancies) != 0)
							<a href="#related_vacancies" class="click-tab"><i class="fa-solid fa-table pt-2 mr-2"></i>Похожие вакансии</a>
							@endif
						</div>
						@if($parsed_desc)
						<div id="about">
							<h3 class="little-header-text">Описание</h3>
							<div id="descript-field">{{$parsed_desc}}</div>
							<script>
								$("#descript-field").html($("#descript-field").text())
							</script>
						</div>
						@else <div class="mt-4"></div>
						@endif
						<div id="skills">
							<div class="d-flex justify-content-start tag-area">
								@foreach($vacancy_skills as $vs)
								<div class="card-tag">{{$vs->skill_name}}</div>
								@endforeach
							</div>
						</div>
						@if(!count($related_vacancies))
						<div class="mb-5"></div>
						@endif
					</div>
					<div class="col-md-4">
						@if ($response_or_not || $offer_or_not)
						<div class="side-card" id="third-card">
							<h3 class="little-header-text ml-4 pt-4 pb-2">Взаимодействия</h3>
							@if($response_or_not)
							<button class="button" id="view-response"><a href="/student/my-responses">Мои отклики</a></button>
							@endif
							@if($offer_or_not)
							<button class="button" id="view-response"><a href="/student/all-offers">Офферы на моё резюме</a></button>
							@endif
						</div>
						@else
						<button class="button" id="btn-apply">Отклинуться</button>
						@endif
						<div class="side-card" id="first-card">
							<h3 class="little-header-text ml-4 pt-4 pb-2">Информация</h3>
							<div class="side-info">
								<div class="row">
									<div class="col-md-auto side-sample-pic">
										<i class="fa-solid fa-money-bill-wave"></i>
									</div>
									<div class="col-md-auto">
										<div class="col-value">@if ($vacancy->salary != 0) {{$vacancy->salary}}₽ @else Без оплаты @endif</div>
										<div class="table-header">Зарплата</div>
									</div>
								</div>
							</div>
							<div class="side-info">
								<div class="row">
									<div class="col-md-auto side-sample-pic">
										<i class="fa-solid fa-medal"></i>
									</div>
									<div class="col-md-auto">
										<div class="col-value">@if ($vacancy->work_experience != 0) {{$vacancy->work_experience . ' ' .YearTextArg($vacancy->work_experience)}} @else Без опыта @endif</div>
										<div class="table-header">Опыт работы</div>
									</div>
								</div>
							</div>
							<div class="side-info">
								<div class="row">
									<div class="col-md-auto side-sample-pic">
										<i class="fa-solid fa-location-dot"></i>
									</div>
									<div class="col-md-auto">
										<div class="col-value">@if ($vacancy->location) {{explode(",", $vacancy->location)[0]}} @else {{explode(",", Auth::guard('employer')->user()->location)[0]}} @endif</div>
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
										<div class="col-value contacts">@if ($vacancy->contacts) {{$vacancy->contacts}} @else {{Auth::guard('employer')->user()->email}} @endif</div>
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
										<div class="col-value">{{$vacancy->work_type->work_type_name}}</div>
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
										<div class="col-value">{{$vacancy->type_of_employment->type_of_employment_name}}</div>
										<div class="table-header">Вид занятости</div>
									</div>
								</div>
							</div>
						</div>
						<div class="side-card" id="second-card">
							<h3 class="little-header-text ml-4 pt-4 pb-2">О вакансии</h3>
							@php
							$currentDate = $vacancy->created_at;
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
								Опубликована {{$currentDate}}
							</div>
						</div>
					</div>
				</div>
				@if (count($related_vacancies))
				<div class="p-2 justify-content-center">
					<h3 class="little-header-text mb-3" id="related_vacancies">Похожие вакансии</h3>
					<div class="similar-vacancies-area">
						@foreach ($related_vacancies as $rel_vacancy)
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-1">
										@if (!App\Models\Employer::findOrFail($rel_vacancy->employer_id)->image)
										<div class="rel-future-pic">{{mb_substr(App\Models\Employer::findOrFail($rel_vacancy->employer_id)->name, 0, 1)}}</div>
										@else
										<img class="rel-pic" src="{{asset('/storage/images/'.App\Models\Employer::findOrFail($rel_vacancy->employer_id)->image)}}" />
										@endif
									</div>
									<div class="col-md-10">
										<h5 class="card-title font-semibold text-xl"><a target="_blank" href="/student/vacancy/{{$rel_vacancy->vacancy_id}}">{{$rel_vacancy->profession_name}}</a></h5>
										<span class="rel-hidden">
											<input type="hidden" value="{{$rel_vacancy->location}}" class="related-vacancy-loc" />
											<input type="hidden" value="{{App\Models\Employer::findOrFail($rel_vacancy->employer_id)->name}}" class="related-vacancy-employer-name" />
											<p class="card-name-loc"><a href="/student/employer/{{$rel_vacancy->employer_id}}">v_name & loc</a></p>
										</span>
										<p class="card-title font-semibold">{{$rel_vacancy->salary == 0 ? "Без оплаты" : $rel_vacancy->salary . "₽"}}</p>
									</div>
								</div>
								<div style="margin-left: 10px;">
									<p class="card-subtitle related-vacancy-desc">{{$rel_vacancy->description}}</p>
									<div class="d-flex justify-content-start tag-area">
										<div class="rel-card-tag">{{$rel_vacancy->type_of_employment_name}}</div>
										<div class="rel-card-tag">{{$rel_vacancy->work_type_name}}</div>
										<div class="rel-card-tag">{{$rel_vacancy->work_experience == 0 ? "Без опыта" : $rel_vacancy->work_experience . " " . YearTextArg($rel_vacancy->work_experience) }}</div>
									</div>
									<p class="card-subtitle related-vacancy-date">{{$rel_vacancy->vacancy_created_at}}</p>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
				@endif
			</section>
			<script>
				if (document.querySelector('section').offsetHeight < window.screen.height) {
					$("section").css("padding-bottom", "50px");
				}
			</script>
		</x-student-layout>
</body>
<style>
	body {
		overflow-x: hidden;
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

	#related_vacanciess {
		margin-left: 110px;
		margin-top: 30px;
	}

	#skills {
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
	}

	.card-name {
		margin-top: -10px;
		color: grey;
		font-size: 15px;
	}

	.card-desc {
		margin-top: 10px;
	}

	.side-card {
		margin-left: 120px;
		background-color: white;
		color: black;
		border: solid 1px #e7e8ea;
		border-radius: 20px;
		min-width: 260px;
		max-width: 300px;
		padding: 0 10px;
	}

	#first-card,
	#second-card,
	#third-card {
		margin-top: 30px;
	}

	#view-response {
		margin: 10px 0px 20px 20px;
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

	#btn-apply,
	#btn-my-responses {
		margin-top: 40px;
		margin-left: 280px
	}

	/**похожие вакансии */
	#related_vacancies {
		padding-left: 120px;
		margin-top: 50px;
	}

	.similar-vacancies-area {
		padding-left: 120px;
	}

	.rel-future-pic {
		background-color: var(--future-pic-color);
		border-radius: 100%;
		color: var(--future-pic-text-color);
		font-size: 24px;
		display: table-cell;
		vertical-align: middle;
		text-align: center;
	}

	.rel-pic,
	.rel-future-pic {
		width: 60px;
		height: 60px;
		margin-top: 6px;
		margin-bottom: 10px;
	}

	.card-subtitle {
		color: grey;
		font-size: 14px;
		padding-top: 10px;
		margin: 0;
		height: 70px;
		-webkit-line-clamp: 3;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.card-name-loc {
		color: grey;
		font-size: 14px;
		margin-top: -5px;
	}

	.rel-card-tag {
		margin-right: 10px;
		border-radius: 8px;
		padding: 3px 10px;
		white-space: nowrap;
		margin-top: 10px;
	}

	.rel-card-tag:first-child {
		background-color: #F9EEFC;
		color: #C152E0;
	}

	.rel-card-tag:nth-child(2) {
		background-color: #fde0d8;
		color: #f67451;
	}

	.rel-card-tag:nth-child(3) {
		background-color: #E8EEEB;
		color: #6b9080;
	}

	.tag-area {
		flex-wrap: wrap;
	}

	.card-body {
		padding: 30px;
	}

	.card {
		border-radius: 8px !important;
		height: 290px;
		width: 1000px;
		margin-bottom: 20px;
	}
</style>
<script>
	$("#btn-apply").click(function() {
		let form = new FormData();
		form.append('vacancy_id', "{{$vacancy->id}}");
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

	$(".related-vacancy-loc").each(function(index) {
		$(this).val(lvovich.cityIn($(this).val().split(', ')[0]));
	});
	$(".related-vacancy-employer-name").each(function(index) {
		$(this).parent(".rel-hidden").find(".related-vacancy-loc").val()
		$(this).parent(".rel-hidden").find(".card-name-loc a").text($(this).val() + " в " + $(this).parent(".rel-hidden").find(".related-vacancy-loc").val());
	});
	moment.locale('ru');

	$(".related-vacancy-date").each(function(index) {
		$(this).text(moment.duration(moment().diff($(this).text())).humanize() + " назад");
	});
	$(".related-vacancy-desc").each(function(index) {
		let line = $(this).text() ? $(this).text() : " ";
		const re = /(?<marks>[`]|\*{1,3}|_{1,3}|~{2})(?<inmarks>.*?)\1|\[(?<link_text>.*)\]\(.*\)/g;
		const fixed_desc = line.replace(re, "$<inmarks>$<link_text>").replace(/\*/g, '').replace(/\#/g, '').replace(/\</g, '');
		$(this).text(fixed_desc);
	});
</script>