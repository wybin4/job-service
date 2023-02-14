	<!DOCTYPE html>
	<html>

	<head>
		<script src="https://cdn.jsdelivr.net/npm/lvovich/dist/lvovich.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	</head>
	@php function YearTextArg($year) {
	$year = abs($year);
	$t1 = $year % 10;
	$t2 = $year % 100;
	return ($t1 == 1 && $t2 != 11 ? "год" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2>= 20) ? "года" : "лет"));
		}@endphp
		<x-employer-layout>
			<section style="background-color:rgb(254, 254, 254)">
				<div class="row">
					<div class="col-md-8">
						<div class="row pt-3">
							<div class="col-md-auto">
								<div class="p-2 justify-content-center">
									@if (!Auth::guard('employer')->user()->image)
									<div class="future-pic">
										<div>{{mb_substr(Auth::guard('employer')->user()->name, 0, 1)}}</div>
									</div>
									@else
									<img class="pic" src="{{asset('/storage/images/'.Auth::guard('employer')->user()->image)}}" />
									@endif
								</div>
							</div>
							<div class="col-md-auto mt-4">
								<div class="row">
									<div class="col-md-auto">
										<h3 class="little-header-text">{{Auth::guard('employer')->user()->name}}</h3>
									</div>
									<div class="col-md-auto">
										@if($vacancy->status == 0)
										<div class="dot"></div>
										@else
										<div class="dot-red"></div>
										@endif
									</div>
								</div>
								<div class="text-muted">{{$vacancy->profession->profession_name}}</div>
							</div>
						</div>
						<div class="tabs">
							@if($description)
							<a href="#about" class="click-tab-active click-tab"><i class="fa-regular fa-file-lines pt-2 mr-2"></i>Описание</a>
							@endif
							<a href="#skills" class="click-tab"><i class="fa-solid fa-list pt-2 mr-2"></i>Навыки</a>
						</div>
						@if($description)
						<div id="about">
							<h3 class="little-header-text">Описание</h3>
							<div id="descript-field">{{$description}}</div>
							<script>
								$("#descript-field").html($("#descript-field").text())
								$("#descript-field").html($("#descript-field").html().replace(/\<br>/g, ''));
							</script>
						</div>
						@else <div class="mt-4"></div>
						@endif
						<div id="skills">
							<div class="d-flex justify-content-start tag-area pb-5">
								@foreach($vacancy_skills as $vs)
								<div class="card-tag">{{$vs->skill_name}}</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-md-4">
						@if ($vacancy->status == 0)
						<button class="button btn-archive" id="btn-archive-{{$vacancy->id}}">Архивировать</button>
						@else
						<button class="button btn-unarchive" id="btn-unarchive-{{$vacancy->id}}">Разрхивировать</button>
						@endif
						@if ($vacancy->status == 0)
						@if(count(App\Models\Vacancy::find($vacancy->id)->student_response) && $vacancy->status == 0)
						<div class="side-card" id="second-card">
							<h3 class="little-header-text ml-4 pt-4 pb-2">Отклики</h3>
							<button class="button view-btn"><a href="/employer/vacancy-responses/{{$vacancy->id}}">Просмотреть</a></button>
						</div>
						@endif
						@if(count(App\Models\Vacancy::find($vacancy->id)->employer_offer) && $vacancy->status == 0)
						<div class="side-card" id="second-card">
							<h3 class="little-header-text ml-4 pt-4 pb-2">Офферы</h3>
							<button class="button view-btn"><a href="/employer/vacancy-offers/{{$vacancy->id}}">Просмотреть</a></button>
						</div>
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
										<div class="col-value">@if ($vacancy->location) {{explode(",", $vacancy->location)[0]}} @else {{explode(",", Auth::guard('employer')->user()->location)[0]}} @endif</div>
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
										<i class="fa-regular fa-clock"></i>
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
			</section>
			<script>
				if (document.querySelector('section').offsetHeight < window.screen.height) {
					$("section").css("padding-bottom", "50px");
				}
			</script>
		</x-employer-layout>
		<style>
			html,
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
			#second-card {
				margin-top: 30px;
			}

			.view-btn {
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

			.btn-archive,
			.btn-unarchive {
				margin-top: 40px;
				margin-left: 280px
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
		</style>
		<script>
			$(".btn-archive").on('click', function() {
				let id = $(this).attr('id').split('-');
				id = id[id.length - 1];
				$.ajax({
					url: '{{ route("employer.archive-vacancy") }}',
					type: "POST",
					data: {
						'vacancy_id': id
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Архивировали вакансию!")
						window.location.href = "{{ route('employer.dashboard') }}";
					},
					error: function(msg) {
						console.log("Не получилось архивировать вакансию")
					}
				});
			})
			$(".btn-unarchive").on('click', function() {
				let id = $(this).attr('id').split('-');
				id = id[id.length - 1];
				$.ajax({
					url: '{{ route("employer.unarchive-vacancy") }}',
					type: "POST",
					data: {
						'vacancy_id': id
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Разрхивировали вакансию!")
						window.location.href = "{{ route('employer.dashboard') }}";
					},
					error: function(msg) {
						console.log("Не получилось разархивировать вакансию")
					}
				});
			})
		</script>

	</html>