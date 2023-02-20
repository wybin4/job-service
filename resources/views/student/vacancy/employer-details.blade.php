<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/lvovich/dist/lvovich.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
	<script src="{{asset('/js/range-functions.js')}}"></script>
</head>
@php function YearTextArg($year) {
$year = abs($year);
$t1 = $year % 10;
$t2 = $year % 100;
return ($t1 == 1 && $t2 != 11 ? "год" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2>= 20) ? "года" : "лет"));
	}@endphp
	<x-student-layout>
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
							<h3 class="little-header-text">{{$employer->name}}</h3>
							<div class="text-muted">{{$employer->location}}</div>
						</div>
					</div>
					<div class="tabs">
						@if($parsed_desc)
						<a href="#parsed_desc" class="click-tab-active click-tab"><i class="fa-regular fa-file-lines pt-2 mr-2"></i>Описание</a>
						@endif
						@if(count($employer_rates))
						<a href="#review" class="click-tab"><i class="fa-regular fa-comment pt-2 mr-2"></i>Отзывы</a>
						@endif
						@if (count($latest_vacancies))
						<a href="#latest_vacancies" class="click-tab"><i class="fa-solid fa-list pt-2 mr-2"></i>Последние вакансии</a>
						@endif
					</div>
					@if($parsed_desc)
					<div id="parsed_desc">
						<h3 class="little-header-text">Описание</h3>
						<div id="descript-field">{{$parsed_desc}}</div>
						<script>
							$("#descript-field").html($("#descript-field").text())
						</script>
					</div>
					@else <div class="mt-4"></div>
					@endif
					@if(count($employer_rates))
					<div id="review">
						<h3 class="little-header-text">Отзывы</h3>
						<div class="row rate-body">
							<div class="col-md-auto">
								<div class="review-rate"></div>
								<div class="stud-review-count"></div>
							</div>
							<div class="col-md-auto employer-rates">
							</div>
						</div>
						<div class="review-body">
							@foreach($reviews as $review)
							<div class="mb-4">
								<div class="row">
									<div class="col-md-auto">
										@if (!$review->image)
										<div class="review-future-pic future-pic">
											<div>{{mb_substr($review->student_fio, 0, 1)}}</div>
										</div>
										@else
										<img class="review-pic pic" src="{{asset('/storage/images/'.$review->image)}}" />
										@endif
									</div>
									<div class="col-md-auto">
										<div class="review-student-name">{{explode(" ", $review->student_fio)[0]}} {{explode(" ", $review->student_fio)[1]}}</div>
										<div class="review-student-time">{{$review->review_updated_at}}</div>
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
				<div class="col-md-4">
					<div class="side-card" id="first-card">
						<h3 class="little-header-text ml-4 pt-4 pb-2">Информация</h3>
						@if (count($latest_vacancies))
						<div class="side-info">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-regular fa-clock"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value" id="latest-date">{{$latest_vacancies[0]->vacancy_created_at}}</div>
									<div class="table-header">Дата последней публикации</div>
								</div>
							</div>
						</div>
						<script>
							moment.locale('ru');
							$("#latest-date").text(moment.duration(moment().diff($("#latest-date").text())).humanize() + " назад");
						</script>
						@endif
						<div class="side-info mb-4">
							<div class="row">
								<div class="col-md-auto side-sample-pic">
									<i class="fa-regular fa-address-book"></i>
								</div>
								<div class="col-md-auto">
									<div class="col-value contacts">{{$employer->email}}</div>
									<div class="table-header">Контакты</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@if (count($latest_vacancies))
				<div class="p-2 justify-content-center">
					<h3 class="little-header-text mb-3" id="latest_vacancies">Последние вакансии</h3>
					<div class="similar-vacancies-area">
						@foreach ($latest_vacancies as $last_vacancy)
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-1">
										@if (!App\Models\Employer::findOrFail($last_vacancy->employer_id)->image)
										<div class="lat-future-pic">{{mb_substr(App\Models\Employer::findOrFail($last_vacancy->employer_id)->name, 0, 1)}}</div>
										@else
										<img class="lat-pic" src="{{asset('/storage/images/'.App\Models\Employer::findOrFail($last_vacancy->employer_id)->image)}}" />
										@endif
									</div>
									<div class="col-md-10">
										<h5 class="card-title font-semibold text-xl"><a target="_blank" href="/student/vacancy/{{$last_vacancy->vacancy_id}}">{{$last_vacancy->profession_name}}</a></h5>
										<span class="lat-hidden">
											<input type="hidden" value="{{$last_vacancy->location}}" class="latest-vacancy-loc" />
											<input type="hidden" value="{{App\Models\Employer::findOrFail($last_vacancy->employer_id)->name}}" class="latest-vacancy-employer-name" />
											<p class="card-name-loc"><a href="/student/employer/{{$last_vacancy->employer_id}}">v_name & loc</a></p>
										</span>
										<p class="card-title font-semibold">{{$last_vacancy->salary == 0 ? "Без оплаты" : $last_vacancy->salary . "₽"}}</p>
									</div>
								</div>
								<div style="margin-left: 10px;">
									<p class="card-subtitle latest-vacancy-desc">{{$last_vacancy->description}}</p>
									<div class="d-flex justify-content-start tag-area">
										<div class="lat-card-tag">{{$last_vacancy->type_of_employment_name}}</div>
										<div class="lat-card-tag">{{$last_vacancy->work_type_name}}</div>
										<div class="lat-card-tag">{{$last_vacancy->work_experience == 0 ? "Без опыта" : $last_vacancy->work_experience . " " . YearTextArg($last_vacancy->work_experience) }}</div>
									</div>
									<p class="card-subtitle latest-vacancy-date">{{$last_vacancy->vacancy_created_at}}</p>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
				@endif
			</div>
		</section>
		<script>
			if (document.querySelector('section').offsetHeight < window.screen.height) {
				$("section").css("padding-bottom", "50px");
			}
		</script>
	</x-student-layout>
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

		#parsed_desc,
		#review {
			margin-left: 120px;
			margin-top: 50px;
			width: 900px;
		}

		#review {
			margin-bottom: -40px;
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

		#first-card {
			margin-top: 120px;
		}

		.side-info {
			margin-top: 10px;
			margin-left: 20px;
		}


		.table-header {
			color: grey;
			font-size: 13px;
			margin-top: -5px;
		}

		/* последние вакансии */
		#latest_vacancies {
			padding-left: 120px;
			margin-top: 50px;
		}

		.similar-vacancies-area {
			padding-left: 120px;
		}

		.lat-future-pic {
			background-color: var(--future-pic-color);
			border-radius: 100px;
			color: var(--future-pic-text-color);
			font-size: 24px;
			display: table-cell;
			vertical-align: middle;
			text-align: center;
		}

		.lat-pic,
		.lat-future-pic {
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

		.lat-card-tag {
			margin-right: 10px;
			border-radius: 8px;
			padding: 3px 10px;
			white-space: nowrap;
			margin-top: 10px;
		}

		.lat-card-tag:first-child {
			background-color: #F9EEFC;
			color: #C152E0;
		}

		.lat-card-tag:nth-child(2) {
			background-color: #fde0d8;
			color: #f67451;
		}

		.lat-card-tag:nth-child(3) {
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

		/*** */

		.rate-body {
			margin-top: 20px;
		}

		.review-rate {
			color: var(--link-hover-color);
			font-size: 36px;
			font-weight: bold;
			text-align: center;
		}

		.stud-review-count {
			color: grey;
		}

		.self-progress,
		.emp-progress {
			border-radius: 8px;
			width: 250px;
			height: 8px;
			background-color: var(--dot-color);
		}

		.bar {
			border-radius: 8px;
			height: 8px;
			background-color: var(--link-hover-color);
		}

		.quality-name {
			margin-top: -6px;
			margin-left: 20px;
		}

		.bar-rate {
			display: flex;
			margin-top: 8px;
		}

		.quality-rate {
			margin-top: -6px;
			margin-right: 14px;
		}

		/** */

		.review-future-pic,
		.review-pic {
			margin-left: 0px;
			width: 40px;
			height: 40px;
			font-size: 16px;
		}

		.review-student-name,
		.review-student-time {
			margin-left: -9px;
		}

		.review-student-name {
			font-weight: bold;
		}

		.review-student-time {
			font-size: 15px;
			color: grey;
			margin-top: -5px;
		}

		.review-body {
			margin-top: 20px;
		}

		.review-text {
			margin-top: 20px;
		}
	</style>

</html>
<script>
	$(".latest-vacancy-loc").each(function(index) {
		$(this).val(lvovich.cityIn($(this).val().split(', ')[0]));
	});
	$(".latest-vacancy-employer-name").each(function(index) {
		$(this).parent(".lat-hidden").find(".latest-vacancy-loc").val()
		$(this).parent(".lat-hidden").find(".card-name-loc a").text($(this).val() + " в " + $(this).parent(".lat-hidden").find(".latest-vacancy-loc").val());
	});
	moment.locale('ru');

	$(".latest-vacancy-date").each(function(index) {
		$(this).text(moment.duration(moment().diff($(this).text())).humanize() + " назад");
	});
	$(".latest-vacancy-desc").each(function(index) {
		let line = $(this).text() ? $(this).text() : " ";
		const re = /(?<marks>[`]|\*{1,3}|_{1,3}|~{2})(?<inmarks>.*?)\1|\[(?<link_text>.*)\]\(.*\)/g;
		const fixed_desc = line.replace(re, "$<inmarks>$<link_text>").replace(/\*/g, '').replace(/\#/g, '').replace(/\</g, '');
		$(this).text(fixed_desc);
	});



	/** */


	let employer_rates = [];
	const er = <?php echo json_encode($employer_rates); ?>;
	if (er.length) {
		let rates = new Set(er.map(r => r.quality_id));
		rates = Array.from(rates);
		let arr;
		// получаем employer_rates с отдельными quality_id и массивом оценок к нему
		for (let i = 0; i < rates.length; i++) {
			arr = er.filter((r) => {
				return r.quality_id == rates[i]
			});
			if (arr.length > 1) {
				employer_rates.push([rates[i],
					get_trend([arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.quality_rate)])
				]);
			} else {
				employer_rates.push([rates[i],
					[arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.quality_rate)]
				])
			}
		}
		// получаем ema для employer_rates
		let employer_ema = [];
		employer_rates.forEach(function(rate) {
			employer_ema.push([rate[0], get_ema(rate[1][1])])
		})
		for (let i = 0; i < employer_ema.length; i++) {
			let quality = er.filter((e) => {
				return e.quality_id == employer_ema[i][0];
			})[0];
			let text = `
					<div class="bar-rate">
			<div class="quality-rate">${employer_ema[i][1].toFixed(2)}</div>
			<div class="emp-progress" id="emp-progress-${quality.quality_id}">
				<div class="bar"></div>
			</div>
			<div class="quality-name">${quality.quality_name}</div>
		</div>
			`;
			$(".employer-rates").append(text);
			$(`#emp-progress-${quality.quality_id}`).find('.bar').css('width', employer_ema[i][1] / 5 * 100 + '%');

		}
		const sum = employer_ema.reduce((acc, number) => acc + number[1], 0);
		const length = employer_ema.length;
		const students_count = <?php echo json_encode($students_count); ?>;
		$(".review-rate").text((sum / length).toFixed(2));
		$(".stud-review-count").text(students_count + ' ' + getNoun(students_count, 'студент', 'студента', 'студентов'));
	}
	$(".review-student-time").each(function() {
		let date = moment.duration(moment().diff($(this).text())).humanize();
		$(this).text(date + ' назад');
	})
</script>