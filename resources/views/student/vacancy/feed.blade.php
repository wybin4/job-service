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

	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
	<x-student-layout>
		@csrf
		<div id="search-div">
			<div class="ui-widget">
				<input id="search_profession_name" name="profession_name" placeholder="Поиск" value="{{request()->input('profession_name', old('profession_name'))}}">
			</div>
		</div>
		<div id="sort-div">
			<h2 class="text-xl font-bold">Фильтры<i class="fa-solid fa-xmark" id="clear-filters"></i></h2>
			<div class="sort-area">
				<p class="sort-label">Регион<i class="fa-solid fa-chevron-down sort-up"></i></p>
				<div class="sort-box" style="display:none">
					<input type="text" class="little-input" oninput="updateResult(this.value)">
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
						window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
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
			<div class="box sort-area">
				<p class="sort-label">Зарплата<i class="fa-solid fa-chevron-up sort-up"></i></p>
				<div class="sort-box">
					<div class="slider" data-value-0="#first" data-value-1="#second"></div>
					<div class="values">
						<div><span id="first" name="min-salary"></span>К₽</div> - <div><span id="second" name="max-salary"></span>К₽</div>
					</div>
				</div>
			</div>
		</div>
		<div id="container">
			<div class="row" id="vacancies_data"></div>
			<div class="ajax-load text-center" style="display:none">
				<i class="fa-solid fa-circle-notch"></i> Загружаем ещё вакансии...
			</div>
			<div class="no-data text-center mb-4" style="display:none">
				<b>Вакансий по вашему запросу больше не найдено</b>
			</div>
		</div>
	</x-student-layout>
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

	#vacancies_data {
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

	/*salary slider */
	.box {
		max-width: 200px;
		padding-top: 10px;
	}

	.box .values div {
		display: inline-block;
		vertical-align: top;
	}

	.slider {
		--primary: var(--slider-color);
		--handle: #fff;
		--handle-active: white;
		--handle-hover: white;
		--handle-border: 2px solid var(--primary);
		--line: #cdd9ed;
		--line-active: var(--primary);
		height: 23px;
		width: 100%;
		position: relative;
		pointer-events: none;
	}

	.slider .ui-slider-handle {
		--y: 0;
		--background: var(--handle);
		cursor: grab;
		-webkit-tap-highlight-color: transparent;
		top: 0;
		width: 23px;
		height: 23px;
		transform: translateX(-50%);
		position: absolute;
		outline: none;
		display: block;
		pointer-events: auto;
	}

	.slider .ui-slider-handle div {
		width: 20px;
		/*размер шариков*/
		height: 20px;
		border-radius: 50%;
		transition: background 0.4s ease;
		transform: translateY(calc(var(--y) * 1px));
		border: var(--handle-border);
		background: var(--background);
	}

	.slider .ui-slider-handle:hover {
		--background: var(--handle-hover);
	}

	.slider .ui-slider-handle:active {
		--background: var(--handle-active);
		cursor: grabbing;
	}

	.slider svg {
		--stroke: var(--line);
		display: block;
		height: 83px;
	}

	.slider svg path {
		fill: none;
		stroke: var(--stroke);
		stroke-width: 1;
	}

	.slider .active,
	.slider>svg {
		position: absolute;
		top: -32px;
		/*где все кроме шариков*/
		height: 83px;
	}

	.slider>svg {
		left: 0;
		width: 100%;
	}

	.slider .active {
		position: absolute;
		overflow: hidden;
		left: calc(var(--l) * 1px);
		right: calc(var(--r) * 1px);
	}

	.slider .active svg {
		--stroke: var(--line-active);
		position: relative;
		left: calc(var(--l) * -1px);
		right: calc(var(--r) * -1px);
	}

	.slider .active svg path {
		stroke-width: 2;
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
	moment.locale('ru');

	function ajax_success(response) {
		$('.ajax-load').hide();
		lastPage = response.vacancies.last_page;
		console.log(response);
		let html = '';
		for (let i = 0; i < response.vacancies.data.length; i++) {
			const location = response.vacancies.data[i].location.split(',')[0];
			let experience;
			if (response.vacancies.data[i].work_experience == 0) {
				experience = "Без опыта";
			} else experience = `${response.vacancies.data[i].work_experience} ` + plural(response.vacancies.data[i].work_experience, declension);
			let salary;
			if (response.vacancies.data[i].salary == 0) {
				salary = "Без оплаты";
			} else salary = response.vacancies.data[i].salary + "₽";
			let date = moment.duration(moment().diff(response.vacancies.data[i].vacancy_created_at)).humanize();
			let line = response.vacancies.data[i].vacancy_description ? response.vacancies.data[i].vacancy_description : " ";
			const re = /(?<marks>[`]|\*{1,3}|_{1,3}|~{2})(?<inmarks>.*?)\1|\[(?<link_text>.*)\]\(.*\)/g;
			const fixed_desc = line.replace(re, "$<inmarks>$<link_text>").replace(/\*/g, '').replace(/\#/g, '').replace(/\</g, '');
			html += `
              <div class="col-md-4 mb-4" >
                <div class="card">
		<div class="card-body">`;
			if (response.vacancies.data[i].image === null) {
				html += `<div class="future-pic">${response.vacancies.data[i].employer_name[0]}</div>`;
			} else html += `<img class="pic" src="{{asset('/storage/images/` + response.vacancies.data[i].image + `')}}" />`;
			html += `<h5 class="card-title mt-3 font-semibold text-xl"><a target="_blank" href="vacancy/${response.vacancies.data[i].vacancy_id}">${response.vacancies.data[i].profession_name}</a></h5>
		<p class="card-name-loc"><a href="/student/employer/${response.vacancies.data[i].employer_id}">${response.vacancies.data[i].employer_name} в ${lvovich.cityIn(location)}</a></p>
		<p class="card-title font-semibold">${salary}</p>
		<p class="card-subtitle">${fixed_desc}</p>
		<div class="d-flex justify-content-start tag-area">
			<div class="card-tag">${response.vacancies.data[i].type_of_employment_name}</div>
			<div class="card-tag">${response.vacancies.data[i].work_type_name}</div>
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
					$('#vacancies_data').append(ajax_success(response));
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
				$('#vacancies_data').html(ajax_success(response));
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
		let sphereIds = getIds("sphere");
		let href = "";
		if (sphereIds.length) {
			href += sphereIds;
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
		window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
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
		window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
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
		window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
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
		window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
		loadData(1);
	});

	//////
	//salary slider

	const min_salary = '{{$min_salary}}' / 1000;
	const max_salary = '{{$max_salary}}' / 1000;
	let filter_salary = "{{request()->input('salary', old('salary'))}}".split(',').length > 1 ? "{{request()->input('salary', old('salary'))}}".split(',') : ['10000', max_salary * 1000 / 2];
	$('.slider').each(function(e) {

		var slider = $(this),
			width = slider.width(),
			handle,
			handleObj;

		let svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		svg.setAttribute('viewBox', '0 0 ' + width + ' 83');

		slider.html(svg);
		slider.append($('<div>').addClass('active').html(svg.cloneNode(true)));

		slider.slider({
			range: true,
			values: [filter_salary[0] / 1000, filter_salary[1] / 1000],
			min: min_salary,
			step: 5,
			minRange: 10,
			max: max_salary,
			create(event, ui) {

				slider.find('.ui-slider-handle').append($('<div />'));

				$(slider.data('value-0')).html(slider.slider('values', 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));
				$(slider.data('value-1')).html(slider.slider('values', 1).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));
				$(slider.data('range')).html((slider.slider('values', 1) - slider.slider('values', 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));

				setCSSVars(slider);

			},
			start(event, ui) {

				$('body').addClass('ui-slider-active');

				handle = $(ui.handle).data('index', ui.handleIndex);
				handleObj = slider.find('.ui-slider-handle');

			},
			change(event, ui) {
				setCSSVars(slider);
			},
			slide(event, ui) {

				let min = slider.slider('option', 'min'),
					minRange = slider.slider('option', 'minRange'),
					max = slider.slider('option', 'max');

				if (ui.handleIndex == 0) {
					if ((ui.values[0] + minRange) >= ui.values[1]) {
						slider.slider('values', 1, ui.values[0] + minRange);
					}
					if (ui.values[0] > max - minRange) {
						return false;
					}
				} else if (ui.handleIndex == 1) {
					if ((ui.values[1] - minRange) <= ui.values[0]) {
						slider.slider('values', 0, ui.values[1] - minRange);
					}
					if (ui.values[1] < min + minRange) {
						return false;
					}
				}

				$(slider.data('value-0')).html(ui.values[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));
				$(slider.data('value-1')).html(ui.values[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));
				$(slider.data('range')).html((slider.slider('values', 1) - slider.slider('values', 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '&thinsp;'));

				setCSSVars(slider);

			},
			stop(event, ui) {

				$('body').removeClass('ui-slider-active');

				let duration = .6,
					ease = Elastic.easeOut.config(1.08, .44);

				TweenMax.to(handle, duration, {
					'--y': 0,
					ease: ease
				});

				TweenMax.to(svgPath, duration, {
					y: 42,
					ease: ease
				});

				handle = null;
				/// место, где нужно записать range в url
				let search = location.search.substring(1);
				if (search.length) {
					search = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key, value) {
						return key === "" ? value : decodeURIComponent(value)
					})
				}
				let salary = $("span[name='min-salary']").text() + "000," + $("span[name='max-salary']").text() + "000";
				let href = "";
				if (salary.length) {
					href += salary;
				}
				if (search) {
					search.salary = href;
				} else {
					search = {};
					search.salary = href;
				}
				search = Object.keys(search).map(function(key) {
					return key + '=' + search[key];
				}).join('&');
				window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
				location.reload();
			}
		});

		var svgPath = new Proxy({
			x: null,
			y: null,
			b: null,
			a: null
		}, {
			set(target, key, value) {
				target[key] = value;
				if (target.x !== null && target.y !== null && target.b !== null && target.a !== null) {
					slider.find('svg').html(getPath([target.x, target.y], target.b, target.a, width));
				}
				return true;
			},
			get(target, key) {
				return target[key];
			}
		});

		svgPath.x = width / 2;
		svgPath.y = 42;
		svgPath.b = 0;
		svgPath.a = width;

		$(document).on('mousemove touchmove', e => {
			if (handle) {

				let laziness = 4,
					max = 24,
					edge = 52,
					other = handleObj.eq(handle.data('index') == 0 ? 1 : 0),
					currentLeft = handle.position().left,
					otherLeft = other.position().left,
					handleWidth = handle.outerWidth(),
					handleHalf = handleWidth / 2,
					y = e.pageY - handle.offset().top - handle.outerHeight() / 2,
					moveY = (y - laziness >= 0) ? y - laziness : (y + laziness <= 0) ? y + laziness : 0,
					modify = 1;

				moveY = (moveY > max) ? max : (moveY < -max) ? -max : moveY;
				modify = handle.data('index') == 0 ? ((currentLeft + handleHalf <= edge ? (currentLeft + handleHalf) / edge : 1) * (otherLeft - currentLeft - handleWidth <= edge ? (otherLeft - currentLeft - handleWidth) / edge : 1)) : ((currentLeft - (otherLeft + handleHalf * 2) <= edge ? (currentLeft - (otherLeft + handleWidth)) / edge : 1) * (slider.outerWidth() - (currentLeft + handleHalf) <= edge ? (slider.outerWidth() - (currentLeft + handleHalf)) / edge : 1));
				modify = modify > 1 ? 1 : modify < 0 ? 0 : modify;

				if (handle.data('index') == 0) {
					svgPath.b = currentLeft / 2 * modify;
					svgPath.a = otherLeft;
				} else {
					svgPath.b = otherLeft + handleHalf;
					svgPath.a = (slider.outerWidth() - currentLeft) / 2 + currentLeft + handleHalf + ((slider.outerWidth() - currentLeft) / 2) * (1 - modify);
				}

				svgPath.x = currentLeft + handleHalf;
				svgPath.y = moveY * modify + 42;

				handle.css('--y', moveY * modify);

			}
		});

	});

	function getPoint(point, i, a, smoothing) {
		let cp = (current, previous, next, reverse) => {
				let p = previous || current,
					n = next || current,
					o = {
						length: Math.sqrt(Math.pow(n[0] - p[0], 2) + Math.pow(n[1] - p[1], 2)),
						angle: Math.atan2(n[1] - p[1], n[0] - p[0])
					},
					angle = o.angle + (reverse ? Math.PI : 0),
					length = o.length * smoothing;
				return [current[0] + Math.cos(angle) * length, current[1] + Math.sin(angle) * length];
			},
			cps = cp(a[i - 1], a[i - 2], point, false),
			cpe = cp(point, a[i - 1], a[i + 1], true);
		return `C ${cps[0]},${cps[1]} ${cpe[0]},${cpe[1]} ${point[0]},${point[1]}`;
	}

	function getPath(update, before, after, width) {
		let smoothing = .16,
			points = [
				[0, 42],
				[before <= 0 ? 0 : before, 42],
				update,
				[after >= width ? width : after, 42],
				[width, 42]
			],
			d = points.reduce((acc, point, i, a) => i === 0 ? `M ${point[0]},${point[1]}` : `${acc} ${getPoint(point, i, a, smoothing)}`, '');
		return `<path d="${d}" />`;
	}

	function setCSSVars(slider) {
		let handle = slider.find('.ui-slider-handle');
		slider.css({
			'--l': handle.eq(0).position().left + handle.eq(0).outerWidth() / 2,
			'--r': slider.outerWidth() - (handle.eq(1).position().left + handle.eq(1).outerWidth() / 2)
		});
	}

	////

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
				window.history.pushState("Details", "Title", "/student/vacancy-feed?" + search);
				loadData(1);
			},
		});
	});

	////чистим фильтры
	$("#clear-filters").click(function() {
		window.history.pushState("Details", "Title", "/student/vacancy-feed");
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