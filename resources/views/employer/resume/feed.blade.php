<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/js/bootstrap.min.js"></script>
	<script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/lvovich/dist/lvovich.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
	<script src="{{asset('/js/range-functions.js')}}"></script>


	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
	<x-employer-layout>
		@csrf
		<div id="search-div">
			<div class="ui-widget">
				<input autocomplete="off" id="search_profession_name" name="profession_name" placeholder="Поиск" value="{{request()->input('profession_name', old('profession_name'))}}">
			</div>
		</div>
		<div id="sort-div">
			<h2 class="text-xl font-bold">Фильтры<i class="fa-solid fa-xmark" id="clear-filters"></i></h2>
			<div class="sort-area">
				<p class="sort-label">Регион<i class="fa-solid fa-chevron-down sort-up"></i></p>
				<div class="sort-box" style="display:none">
					<input type="text" autocomplete="off" class="little-input" oninput="updateResult(this.value)">
					<div id="location-sort">
					</div>
				</div>
				<script>
					let requestUrl = 'https://raw.githubusercontent.com/pensnarik/russian-cities/master/russian-cities.json';
					let xhr = new XMLHttpRequest();

					xhr.open('GET', requestUrl, true);
					xhr.responseType = 'json';
					xhr.send()
					xhr.onload = function() {
						cities = xhr.response;
						cities = cities.map(city => city.name + ", " + city.subject);
						cities.sort(function(a, b) {
							if (a < b) {
								return -1;
							}
							if (a > b) {
								return 1;
							}
							return 0;
						});
						let list = '';
						let search = location.search.substring(1);
						if (search.length) {
							search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
								return key === "" ? value : decodeURIComponent(value)
							})
						}
						let new_loc_arr = [];
						if (search.location) {
							const loc_arr = search.location.split(',');
							for (let i = 0; i < loc_arr.length; i++) {
								if (i % 2 == 0) {
									new_loc_arr.push(loc_arr[i] + "," + loc_arr[i + 1]);
								}
							}
						}
						for (let i = 0; i < cities.length; i++) {
							if (new_loc_arr.includes(cities[i])) {
								list += `<div class="flex sort-checkbox loc-box">
						<input name="location" type="checkbox" class="checkbox" checked id="location_${i}" value="${cities[i]}" onchange="find_by_loc()">
						<x-label class="ml-2" for="location_${i}">${cities[i]}</x-label>
					</div>`;
							} else {
								list += `<div class="flex sort-checkbox loc-box">
						<input name="location" type="checkbox" class="checkbox" id="location_${i}" value="${cities[i]}" onchange="find_by_loc()">
						<x-label class="ml-2" for="location_${i}">${cities[i]}</x-label>
					</div>`;
							}
						}
						$("#location-sort").html(list);
					}

					function updateResult(query) {
						let resultList = document.querySelector("#location-sort");
						$(".loc-box").each(function() {
							$(this).hide();
						});
						cities.map(function(algo) {
							query.split(" ").map(function(word) {
								if (algo.toLowerCase().indexOf(word.toLowerCase()) != -1) {
									$(`#location_${cities.indexOf(algo)}`).parent(".loc-box").show();
								}
							})
						})
					}

					function find_by_loc() {
						let search = location.search.substring(1);
						if (search.length) {
							search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
								return key === "" ? value : decodeURIComponent(value)
							})
						}
						let locationIds = getIds("location");
						let href = "";
						if (locationIds.length) {
							href += locationIds;
							if (search) {
								search.location = href;
							} else {
								search = {};
								search.location = href;
							}
						} else {
							delete search.location;
						}

						search = Object.keys(search).map(function(key) {
							return key + '=' + search[key];
						}).join('&');
						window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
						loadData(1);
					}
				</script>
			</div>
			<div class="sort-area">
				<p class="sort-label">Отрасль<i class="fa-solid fa-chevron-down sort-up"></i></p>
				<div class="sort-box" style="display:none">
					@foreach ($spheres as $sphere)
					<div class="flex sort-checkbox">
						<input name="sphere" type="checkbox" class="checkbox" id="sphere_{{ $sphere->id }}" value="{{ $sphere->id }}" @if (in_array($sphere->id, explode(',', request()->input('sphere', old('sphere')))))
						checked @endif>
						<x-label for="sphere_{{ $sphere->id }}" class="ml-2">{{ $sphere->sphere_of_activity_name }}</x-label>
					</div>
					@endforeach
				</div>
			</div>
			<div class="sort-area">
				<p class="sort-label">Тип работы<i class="fa-solid fa-chevron-up sort-up"></i></p>
				<div class="sort-box">
					@foreach ($work_types as $work_type)
					<div class="flex sort-checkbox">
						<input name="work_type" type="checkbox" class="checkbox" id="work_type_{{ $work_type->id }}" value="{{ $work_type->id }}" @if (in_array($work_type->id, explode(',', request()->input('work_type', old('work_type')))))
						checked @endif>
						<x-label for="work_type_{{ $work_type->id }}" class="ml-2">{{ $work_type->work_type_name }}</x-label>
					</div>
					@endforeach
				</div>
			</div>

			<div class="sort-area">
				<p class="sort-label">Вид занятости<i class="fa-solid fa-chevron-up sort-up"></i></p>
				<div class="sort-box">
					@foreach ($type_of_employments as $type_of_employment)
					<div class="flex sort-checkbox">
						<input name="type_of_employment" type="checkbox" class="checkbox" id="type_of_employment_{{ $type_of_employment->id }}" value="{{ $type_of_employment->id }}" @if (in_array($type_of_employment->id, explode(',', request()->input('type_of_employment', old('type_of_employment')))))
						checked @endif>
						<x-label for="type_of_employment_{{ $type_of_employment->id }}" class="ml-2">{{ $type_of_employment->type_of_employment_name }}</x-label>
					</div>
					@endforeach
				</div>
			</div>
			<div class="sort-area">
				<p class="sort-label">Опыт работы<i class="fa-solid fa-chevron-up sort-up"></i></p>
				<div class="sort-box">
					@php $i = 0; @endphp
					@foreach ($work_exp as $exp)
					<div class="flex sort-checkbox">
						<input name="work_exp" type="radio" class="checkbox" id="work_exp_{{ $i }}" value="{{ $exp }}" @if (in_array($exp, explode(",", request()->input('work_exp', old('work_exp')))))
						checked @endif>
						<x-label for="work_exp_{{ $i }}" class="ml-2">
							@if ($exp == "0")
							Без опыта
							@elseif ($exp == "<1") До года @elseif ($exp==">3" ) Более 3 лет @else {{$exp . " года"}} @endif </x-label>
								@php $i++; @endphp
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div id="container">
			<div class="row" id="resumes_data"></div>
			<div class="ajax-load text-center" style="display:none">
				<i class="fa-solid fa-circle-notch"></i> Загружаем ещё резюме...
			</div>
			<div class="no-data text-center mb-4" style="display:none">
				<b>Резюме по вашему запросу больше не найдено</b>
			</div>
		</div>
	</x-employer-layout>
</body>
<style>
	a:hover {
		color: black;
	}

	#container {
		margin-left: 230px;
		width: 1200px;
		position: absolute;
	}

	#search_div {
		position: absolute;
	}

	#sort-div {
		position: absolute;
		margin-left: 20px;
		width: 180px;
		top: 170px;
	}

	.sort-label {
		margin-top: 20px;
		margin-bottom: 10px;
	}

	.sort-box {
		display: block;
		width: 180px;
	}

	#location-sort {
		height: 150px;
		overflow-y: scroll;
	}

	#location-sort::-webkit-scrollbar {
		width: 6px;
		background-color: white;
	}

	#location-sort::-webkit-scrollbar-thumb {
		background-color: var(--scrollbar-color);
		border-radius: 3px;
	}

	.fa-chevron-up,
	.fa-chevron-down,
	.fa-xmark {
		position: absolute;
		right: 0px;
		margin-top: 3px;
		cursor: pointer;
	}

	.fa-chevron-up,
	.fa-chevron-down {
		color: #a1a4a9;
		margin-top: 5px;
	}

	.box .values span,
	.box .values div {
		font-size: 14px;
	}

	.sort-checkbox {
		margin-top: 5px;
	}

	html {
		background-color: #f3f4f6;
	}

	#resumes_data {
		margin-top: 40px;
	}

	.card {
		border-radius: 8px !important;
		height: 420px;
	}

	.card-body {
		padding: 30px;
	}

	.future-pic {
		font-size: 24px;
		display: table-cell;
		vertical-align: middle;
		text-align: center;
	}

	.pic,
	.future-pic {
		width: 60px;
		height: 60px;
	}

	.card-name-loc {
		color: grey;
		font-size: 14px;
		margin-top: -5px;
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

	.card-tag {
		margin-right: 10px;
		border-radius: 8px;
		padding: 3px 10px;
		white-space: nowrap;
		margin-top: 10px;
	}

	.card-tag:first-child {
		background-color: #F9EEFC;
		color: #C152E0;
	}

	.card-tag:nth-child(2) {
		background-color: #fde0d8;
		color: #f67451;
	}

	.card-tag:nth-child(3) {
		background-color: #E8EEEB;
		color: #6b9080;
	}

	.tag-area {
		flex-wrap: wrap;
	}

	html {
		box-sizing: border-box;
		-webkit-font-smoothing: antialiased;
	}

	* {
		box-sizing: inherit;
	}
</style>
<script>
	let pages = 2;
	let current_page = 0;
	let bool = false;
	let lastPage;
	$(window).scroll(function() {
		let height = $(document).height();
		if ($(window).scrollTop() + $(window).height() >= height && bool == false && lastPage > pages - 2) {
			bool = true;
			$('.ajax-load').show();
			lazyLoad(pages)
				.then(() => {
					bool = false;
					pages++;
					if (pages - 2 == lastPage) {
						$('.no-data').show();
					}
				})
		}
	})

	function plural(number, titles) {
		cases = [2, 0, 1, 1, 1, 2];
		return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
	}
	const declension = ['год', 'года', 'лет'];
	const declension_month = ['месяц', 'месяца', 'месяцев'];
	moment.locale('ru');

	function ajax_success(response) {
		$('.ajax-load').hide();
		lastPage = response.resumes.last_page;
		console.log(response);



		/*let rating = [];
		let unfiltered_er = response.employer_rates; //оценки работодателей из бд
		let student_rates = response.self_rates; //оценки студентов из бд
		//группируем оценки работодателей по resume_id
		let er = unfiltered_er.reduce((r, i) => {
			r[i.resume_id] = r[i.resume_id] || [];
			r[i.resume_id].push(i);
			return r;
		}, {});
		let filtered_er = [];
		for (var key in er) {
			filtered_er.push(er[key]);
		}
		//проходим по сгруппированным оценкам работодателей
		for (let j = 0; j < filtered_er.length; j++) {
			let employer_rates = [];

			let rates = new Set(filtered_er[j].map(r => r.skill_id)); //уникальные skill_id
			rates = Array.from(rates);
			let arr;
			// получаем employer_rates с отдельными skill_id и массивом оценок к нему
			for (let i = 0; i < rates.length; i++) {
				//выбираем оценки одного и того же навыка разными работодателями в одном и том же резюме
				arr = filtered_er[j].filter((r) => {
					return r.skill_id == rates[i]
				});
				//если оценок больше одной, то рассчет тренда по методу наименьших квадратов
				if (arr.map(a => a.skill_rate).length > 1) {
					employer_rates.push([rates[i],
						//также преобразуем время в дни
						get_trend([arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.skill_rate)])
					]);
				} else { //если оценка только одна, то тренда не будет
					employer_rates.push([rates[i],
						[arr.map(a => Math.trunc(new Date(a.updated_at).getTime() / (1000 * 3600 * 24))), arr.map(a => a.skill_rate)]
					]);
				}
			}
			// получаем ema для employer_rates
			let employer_ema = [];
			employer_rates.forEach(function(rate) {
				employer_ema.push([rate[0], get_ema(rate[1][1])])
			})
			let selfs = [];
			let weighted_self;
			//сравниваем ema по скиллу с самооценкой
			for (let i = 0; i < employer_ema.length; i++) {
				weighted_self = get_diff(employer_ema[i][1], student_rates[i].skill_rate);
				weighted_self = [employer_ema[i][0], weighted_self * student_rates[i].skill_rate];
				selfs.push(weighted_self);
			}
			const employer_average = get_average(employer_ema); //средняя оценка работодателей
			const self_average = get_average(selfs); //средняя оценка студента
			rating.push([filtered_er[j][0].resume_id, employer_average * 0.8 + self_average * 0.2]);
		}
		const used_resumes = rating.map(r => r[0]); //выбираем резюме с оценками работодателей
		const unfiltered_selfs = student_rates.filter((rate) => {
			return !used_resumes.includes(rate.resume_id); //и получаем все остальные для использования
		})
		//группируем по resume_id
		let sr = unfiltered_selfs.reduce((r, i) => {
			r[i.resume_id] = r[i.resume_id] || [];
			r[i.resume_id].push(i);
			return r;
		}, {});
		let filtered_self = [];
		for (let key in sr) {
			filtered_self.push(sr[key]);
		}
		let self_rating = [];
		for (let i = 0; i < filtered_self.length; i++) {
			self_rating.push([filtered_self[i][0].resume_id,
				filtered_self[i].reduce((acc, number) => acc + number.skill_rate, 0) /
				filtered_self[i].length * 0.5
			]);
		}
		rating = rating.concat(self_rating);
		const rate_order = rating.sort(compare).reverse().map(r => r[0]);
		let ordered_resumes = response.resumes.data;
		ordered_resumes = ordered_resumes.sort((a, b) => rate_order.indexOf(a.resume_id) - rate_order.indexOf(b.resume_id));
		console.log(rating)*/


		let html = '';
		for (let i = 0; i < response.resumes.data.length; i++) {
			let experience;
			let work_exps = response.work_exps;
			work_exps = work_exps.filter(x => {
				return x[0] == response.resumes.data[i].resume_id
			})[0];

			if (work_exps) {
				if (work_exps[1] >= 1) {
					experience = `${work_exps[1]} ` + plural(work_exps[1], declension);
				} else experience = `${work_exps[2]} ` + plural(work_exps[2], declension_month);
			} else {
				experience = "Без опыта";
			}
			let date = moment.duration(moment().diff(response.resumes.data[i].resume_created_at)).humanize();
			let line = response.resumes.data[i].about_me ? response.resumes.data[i].about_me : " ";
			const re = /(?<marks>[`]|\*{1,3}|_{1,3}|~{2})(?<inmarks>.*?)\1|\[(?<link_text>.*)\]\(.*\)/g;
			const fixed_about_me = line.replace(re, "$<inmarks>$<link_text>").replace(/\*/g, '').replace(/\#/g, '').replace(/\</g, '');
			let name = response.resumes.data[i].student_fio.split(' ')[0] + " " + response.resumes.data[i].student_fio.split(' ')[1];
			html += `
              <div class="col-md-4 mb-4" >
                <div class="card">
		<div class="card-body">`;
			if (response.resumes.data[i].image === null) {
				html += `<div class="future-pic">${response.resumes.data[i].student_fio[0]}</div>`;
			} else html += `<img class="pic" src="{{asset('/storage/images/` + response.resumes.data[i].image + `')}}" />`;
			html += `<h5 class="card-title mt-3 font-semibold text-xl"><a target="_blank" href="resume/${response.resumes.data[i].resume_id}">${response.resumes.data[i].profession_name}</a></h5>
		<p class="card-name-loc">${name}</p>
		<p class="card-title font-semibold">${response.resumes.data[i].location.split(',')[0]}</p>
		<p class="card-subtitle">${fixed_about_me}</p>
		<div class="d-flex justify-content-start tag-area">
			<div class="card-tag">${response.resumes.data[i].type_of_employment_name}</div>
			<div class="card-tag">${response.resumes.data[i].work_type_name}</div>
			<div class="card-tag">${experience}</div>
		</div>
		<p class="card-subtitle">${date} назад</p>
	</div>
     </div>
</div>`;
		}
		return html;

	}

	function lazyLoad(page) {
		let url = "";
		if ($(location).attr('search').length) {
			url = $(location).attr('search') + "&";
		} else url = "?";
		return new Promise((resolve, reject) => {
			$.ajax({
				url: url + 'page=' + page,
				type: 'GET',
				beforeSend: function() {
					$('.ajax-load').show();
				},
				success: function(response) {
					$('#resumes_data').append(ajax_success(response));
					resolve();
				}
			});
		})
	}
	loadData(1);

	function loadData(page) {
		let url = "";
		if ($(location).attr('search').length) {
			url = $(location).attr('search') + "&";
		} else url = "?";

		$.ajax({
			url: url + 'page=' + page,
			type: 'GET',
			beforeSend: function() {
				$('.ajax-load').show();
			},
			success: function(response) {
				$('#resumes_data').html(ajax_success(response));
			}

		});
	}



	/////
	/////

	function getIds(checkboxName) {
		let checkBoxes = document.getElementsByName(checkboxName);
		let ids = Array.prototype.slice.call(checkBoxes)
			.filter(ch => ch.checked == true)
			.map(ch => ch.value);
		return ids;
	}
	$('input[name="sphere"]').on('change', function(e) {
		e.preventDefault();

		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		let workTypeIds = getIds("sphere");
		let href = "";
		if (workTypeIds.length) {
			href += workTypeIds;
			if (search) {
				search.sphere = href;
			} else {
				search = {};
				search.sphere = href;
			}
		} else {
			delete search.sphere;
		}

		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
		loadData(1);
	});
	$('input[name="work_type"]').on('change', function(e) {
		e.preventDefault();

		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		let workTypeIds = getIds("work_type");
		let href = "";
		if (workTypeIds.length) {
			href += workTypeIds;
			if (search) {
				search.work_type = href;
			} else {
				search = {};
				search.work_type = href;
			}
		} else {
			delete search.work_type;
		}

		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
		loadData(1);
	});
	$('input[name="type_of_employment"]').on('change', function(e) {

		e.preventDefault();

		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		let typeOfEmploymentIds = getIds("type_of_employment");
		let href = "";
		if (typeOfEmploymentIds.length) {
			href += typeOfEmploymentIds;
			if (search) {
				search.type_of_employment = href;
			} else {
				search = {};
				search.type_of_employment = href;
			}
		} else {
			delete search.type_of_employment;
		}
		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
		loadData(1);
	});
	$('input[name="work_exp"]').on('change', function(e) {

		e.preventDefault();

		let search = location.search.substring(1);
		if (search.length) {
			search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
				return key === "" ? value : decodeURIComponent(value)
			})
		}
		let workExpIds = getIds("work_exp");
		let href = "";
		if (workExpIds.length) {
			href += workExpIds;
			if (search) {
				search.work_exp = href;
			} else {
				search = {};
				search.work_exp = href;
			}
		} else {
			delete search.work_exp;
		}
		search = Object.keys(search).map(function(key) {
			return key + '=' + search[key];
		}).join('&');
		window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
		loadData(1);
	});


	/////
	$('.sort-label').on('click', function() {
		$(this).children('.fa-solid').toggleClass('fa-chevron-up fa-chevron-down');
		if ($(this).closest(".sort-area").find(".sort-box").css("display") == "block") {
			$(this).closest(".sort-area").find(".sort-box").hide();
		} else {
			$(this).closest(".sort-area").find(".sort-box").show();
		}
	})


	/////поле поиска по профессии
	$(function() {
		var availableTags = <?php echo json_encode($professions); ?>;
		availableTags = availableTags.map(val => {
			return val.profession_name;
		})
		$("#search_profession_name").autocomplete({
			source: availableTags,
			select: function(event, ui) {
				let search = location.search.substring(1);
				if (search.length) {
					search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
						return key === "" ? value : decodeURIComponent(value)
					})
				}
				let profName = ui['item']['value'];
				let href = "";
				if (profName.length) {
					href += profName;
					if (search) {
						search.profession_name = href;
					} else {
						search = {};
						search.profession_name = href;
					}
				} else {
					delete search.profession_name;
				}
				search = Object.keys(search).map(function(key) {
					return key + '=' + search[key];
				}).join('&');
				window.history.pushState("Details", "Title", "/employer/resume-feed?" + search);
				loadData(1);
			},
		});
	});

	////чистим фильтры
	$("#clear-filters").click(function() {
		window.history.pushState("Details", "Title", "/employer/resume-feed");
		location.reload();
	})
</script>

</html>
<style>
	.ui-autocomplete {
		border: solid 1px rgba(165, 180, 252, 0.7);
		width: 1200px;
		border-top: none !important;
	}

	.ui-helper-hidden-accessible {
		display: none;
	}

	.ui-menu .ui-menu-item-wrapper {
		position: relative;
		height: 40px;
		background-color: white;
		padding-left: 10px;
		padding-top: 10px;
	}

	.ui-menu .ui-state-focus,
	.ui-menu .ui-state-active {
		background-color: rgb(238, 242, 255);
	}

	#search_profession_name {
		margin-left: 230px;
		display: block;
		margin-top: 30px;
		width: 1200px;
		border-radius: 8px;
		border: solid 1px #D1D5DB;
		outline: none !important;
		height: 40px;
		padding: 1rem;

	}

	#search_profession_name:active,
	#search_profession_name:focus {
		box-shadow: var(--tw-ring-inset) 0 0 0 3px rgba(199, 210, 254, 0.5) !important;
		border: solid 1px rgb(165 180 252) !important;
		outline: none !important;
	}
</style>