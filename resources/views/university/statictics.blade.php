<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>
<x-university-layout>
	@if (!$no_stats)
	<div class="row choose-period">
		@if ($stats == "month")
		<div class="col-md-auto month-stat chosen text-muted">Месяц</div>
		<div class="col-md-auto year-stat text-muted">Год</div>
		<div class="col-md-auto text-muted"><i class="fa-regular fa-calendar"></i>{{$month_ago}}–{{date_format($today, 'd.m.y')}}</div>
		@else
		<div class="col-md-auto month-stat text-muted">Месяц</div>
		<div class="col-md-auto year-stat chosen text-muted">Год</div>
		<div class="col-md-auto text-muted"><i class="fa-regular fa-calendar"></i>{{$year_ago}}–{{date_format($today, 'd.m.y')}}</div>
		@endif
	</div>
	<div class="row">
		<div class="col-md-auto">
			<div class="stats-card px-6 py-4 shadow-sm sm:rounded-lg bg-white">
				<div class="row">
					<div class="col-md-auto card-div">
						<div class="text-sm text-muted">Резюме</div>
						<div class="text-2xl pt-2">{{count($uni_resumes)}}</div>
						@if($percent_resumes > 0)
						<div class="text-sm green">+{{number_format($percent_resumes, 2)}}%</div>
						@else
						<div class="text-sm red">{{number_format($percent_resumes, 2)}}%</div>
						@endif
					</div>
					<div class="col-md-auto card-div">
						<div class="text-sm text-muted">Трудоустройств</div>
						<div class="text-2xl pt-2">{{$current_interactions}}</div>
						@if($percent_interactions > 0)
						<div class="text-sm green">+{{number_format($percent_interactions, 2)}}%</div>
						@else
						<div class="text-sm red">{{number_format($percent_interactions, 2)}}%</div>
						@endif
					</div>
					<div class="col-md-auto card-div">
						<div class="text-sm text-muted">Оценка студентов</div>
						<div class="text-2xl pt-2">{{number_format($current_uni_rates, 2)}}</div>
						@if($rate_percent > 0)
						<div class="text-sm green">+{{number_format($rate_percent, 2)}}%</div>
						@else
						<div class="text-sm red">{{number_format($rate_percent, 2)}}%</div>
						@endif
					</div>
				</div>
			</div>
			<div class="stats-card px-6 py-4 shadow-sm sm:rounded-lg bg-white">
				<div class="text-xl">Метрики</div>
				<div style="width:500px !important;margin-top:-60px;margin-bottom:-60px;">
					<canvas id="marksChart" width="300" height="300"></canvas>
				</div>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.0/chart.min.js"></script>
				<script>
					var marksCanvas = document.getElementById("marksChart");
					let dataset = <?php echo json_encode($spider); ?>;
					let labels = dataset.map(function($el) {
						return $el[0];
					});
					let datas = dataset.map(function($el) {
						return $el[1];
					});
					console.log(labels)
					const data = {
						labels: labels,
						datasets: [{
							data: datas,
							fill: true,
							backgroundColor: 'rgba(165, 180, 252, 0.3)',
							borderColor: '#3965f5',
							pointBackgroundColor: '#3965f5',
							pointBorderColor: '#fff',
							pointHoverBackgroundColor: '#fff',
							pointHoverBorderColor: '#3965f5'
						}]
					};
					var radarChart = new Chart(marksCanvas, {
						type: 'radar',
						data: data,
						options: {
							elements: {
								line: {
									borderWidth: 3
								}
							},
							plugins: {
								legend: {
									display: false
								}
							}
						},
					});
				</script>
			</div>
		</div>
		<div class="col-md-auto">
			<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
				<a class="row" href="/university/total-statistics">
					<div class="col-md-auto"><i class="fa-solid fa-marker"></i></div>
					<div class="col-md-auto">
						<div class="text-md">Общий балл</div>
						<div class="text-sm text-muted">В статистике учебных заведений</div>
					</div>
					<div class="col-md-auto text-2xl mt-1">{{number_format($rating, 2)}}</div>
				</a>
			</div>
			<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
				<div class="text-xl">Результативность</div>
				@if ($current_uni_with_work)
				<p class="doughnut-text">{{$current_uni_with_work / count($uni_resumes) * 100}}%</p>
				<div style="width:300px;margin-top:-60px;margin-bottom:-60px;"><canvas width="500" id="myChart"></canvas></div>
				<script>
					var ctx = document.getElementById("myChart").getContext('2d');
					dataset = "{{$current_uni_with_work}},{{count($uni_resumes)}}";
					dataset = dataset.split(",");
					var myChart = new Chart(ctx, {
						type: 'doughnut',
						data: {
							labels: ["Не нашли работу", "Нашли работу"],
							datasets: [{
								data: [dataset[1] - dataset[0], dataset[0]],
								borderColor: ['rgba(165, 180, 252, 0.3)', '#3965f5'],
								backgroundColor: ['rgba(165, 180, 252, 0.3)', '#3965f5'],
								borderWidth: 1
							}]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: {
									display: false
								}
							},
						},
					});
				</script>
				@endif
			</div>
		</div>
	</div>
	@endif
</x-university-layout>
<style>
	.fa-marker {
		color: var(--link-hover-color);
		background-color: var(--future-pic-color);
		padding: 12px;
		border-radius: 8px;
	}

	.stats-card {
		margin-top: 20px;
		margin-left: 180px;
		width: 87%;
	}

	.second-sc {
		margin-top: 20px;
		margin-left: 100px;

	}

	.choose-period {
		margin-top: 20px;
		margin-left: 170px;
	}

	html {
		overflow-x: hidden;
		overflow-y: hidden;
	}

	.green {
		color: var(--excellent-text-color);
	}

	.red {
		color: var(--low-text-color);
	}

	.year-stat,
	.month-stat {
		cursor: pointer;
	}

	.chosen {
		color: var(--link-hover-color) !important;
		text-decoration: underline;
		text-underline-offset: 5px;
	}

	.fa-calendar {
		padding-right: 8px;
		color: var(--link-hover-color);
	}

	.card-div:not(:last-child) {
		border-right: solid 2px #f3f4f6;
	}

	.card-div {
		padding-left: 20px;
		padding-right: 90px;
	}
</style>
<script>
	$(".month-stat").on("click", function() {
		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		if (search) {
			search.stats = "month";
		} else {
			search = {};
			search.stats = "month";
		}
		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/university/statistics?" + search);
		location.reload();
	})
	$(".year-stat").on("click", function() {
		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		if (search) {
			search.stats = "year";
		} else {
			search = {};
			search.stats = "year";
		}
		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/university/statistics?" + search);
		location.reload();
	})
</script>