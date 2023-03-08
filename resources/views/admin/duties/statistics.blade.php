<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>
</head>
<div id="download-popup">
	<div class="modal" id="download-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center pt-4">
						<i class="fa-solid fa-arrow-pointer fa-2x text-muted mb-3"></i>
						<div class="text-xl">Выберите тип отчёта</div>
						<div class="text-sm text-muted mb-3">для его скачивания</div>
					</div>
					<div style="margin-top: 45px; margin-bottom:45px;margin-left:65px;">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="radios" id="employments_radio" value="1">
							<label class="form-check-label" for="employments_radio">
								Трудоустройства, отклики и офферы
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="radios" id="res_vac_radio" value="2">
							<label class="form-check-label" for="res_vac_radio">
								Резюме и вакансии
							</label>
						</div>
					</div>
					<div>
						<span type="button" class="span-like-button" id="btn-download" data-bs-dismiss="modal">Скачать</span>
						<span type="button" class="span-like-button" id="btn-cancel-download" data-bs-dismiss="modal">Отмена</span>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-admin-layout>
		<div class="row">
			<div class="col-md-auto">
				<div class="stats-card px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="row">
						<div class="col-md-auto">
							<div class="text-sm text-muted">
								<span class="circle-blue"></span>
								<span class="pr-4">Резюме</span>
								<span class="circle-light"></span>
								<span>Вакансии</span>
							</div>
							<div id="resume_vacancy_graph"></div>
						</div>
						<div class="col-md-auto divider-vert">
							<div style="margin-top:40px;margin-left:20px;">
								<div class="text-sm text-muted">Количество резюме</div>
								<div class="flex">
									<div class="text-2xl">{{$curr_resume}}</div>
									@if($percent_resume > 0)
									<span class="text-xs green ml-2">+{{number_format($percent_resume, 2)}}%</span>
									@else
									<span class="text-xs red ml-2">{{number_format($percent_resume, 2)}}%</span>
									@endif
								</div>
								<div class="text-sm text-muted mt-4">Количество вакансий</div>
								<div class="flex">
									<div class="text-2xl">{{$curr_vacancy}}</div>
									@if($percent_vacancy > 0)
									<span class="text-xs green ml-2">+{{number_format($percent_vacancy, 2)}}%</span>
									@else
									<span class="text-xs red ml-2">{{number_format($percent_vacancy, 2)}}%</span>
									@endif
								</div>
								<div class="text-sm text-muted mt-4">Конкуренция</div>
								<div class="text-2xl">{{$rivalry}}</div>
								<div class="text-xs text-muted">резюме на вакансию</div>
							</div>
						</div>
					</div>
				</div>
				<div class="stats-card px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="row">
						<div class="col-md-auto">
							<div id="employment_graph"></div>
						</div>
						<div class="col-md-auto">
							<div class="text-sm text-muted">
								<span class="circle-blue"></span>
								<span class="pr-4">Офферы</span>
								<br>
								<span class="circle-light"></span>
								<span class="pr-4">Отклики</span>
								<br>
								<span class="circle-grey"></span>
								<span>Трудоустройства</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-auto">
				<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="row">
						<div class="col-md-auto"><i class="fa-regular fa-face-smile"></i></div>
						<div class="col-md-auto">
							<div class="text-md">Удовлетворённость</div>
							<div class="text-md" style="margin-top:-5px;">работодателей</div>
						</div>
						<div class="col-md-auto text-2xl mt-1">{{number_format($csat_employer)}}%</div>
					</div>
				</div>
				<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="row">
						<div class="col-md-auto"><i class="fa-regular fa-face-smile"></i></div>
						<div class="col-md-auto">
							<div class="text-md">Удовлетворённость</div>
							<div class="text-md" style="margin-top:-5px;">студентов</div>
						</div>
						<div class="col-md-auto text-2xl mt-1">{{number_format($csat_student)}}%</div>
					</div>
				</div>
				<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="row">
						<div class="col-md-auto"><i class="fa-solid fa-file-arrow-down"></i></div>
						<div class="col-md-auto">
							<div class="text-md">Скачать отчёты</div>
							<div class="text-sm text-muted">Выбор и скачивание</div>
						</div>
					</div>
				</div>
				<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<div class="text-md">Трудоустройства по категориям</div>
					<div id="donut_types" style="margin-top:-40px"></div>
					<div class="text-sm text-muted pt-3">
						<span class="circle-blue"></span>
						<span class="pr-4">Работа</span>
						<span class="circle-light"></span>
						<span class="pr-4">Стажировка</span>
						<span class="circle-grey"></span>
						<span>Практика</span>
					</div>
				</div>
				<!--
				<div class="second-sc px-6 py-4 shadow-sm sm:rounded-lg bg-white">
					<a class="row" href="/admin/university-statistics">
						<div class="col-md-auto"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
						<div class="col-md-auto">
							<div class="text-md">Рейтинг учебных заведений</div>
							<div class="text-sm text-muted">Просмотр</div>
						</div>
					</a>
				</div>
-->
			</div>
		</div>
	</x-admin-layout>
</div>
<style>
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	#btn-download,
	#btn-cancel-download {
		display: block;
		width: 350px;
		margin: 20px auto;
		text-align: center;
	}

	/* */
	.fa-magnifying-glass-chart,
	.fa-file-arrow-down {
		color: var(--link-hover-color);
		cursor: pointer;
		padding: 13px 15px;
		background-color: var(--future-pic-color);
		border-radius: 8px;
	}

	.divider-vert {
		border-left: solid 2px #E8E9EB;
	}

	.fa-regular {
		color: var(--link-hover-color);
		background-color: var(--future-pic-color);
		padding: 12px;
		border-radius: 8px;
	}

	.stats-card {
		margin-top: 20px;
		margin-left: 120px;
		width: 91%;
	}

	.second-sc {
		margin-top: 20px;
		margin-left: 60px;

	}

	html {
		overflow-x: hidden;
	}

	.green {
		color: var(--excellent-text-color);
		margin-top: 10px;
	}

	.red {
		color: var(--low-text-color);
		margin-top: 10px;
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

	/** */
	#resume_vacancy_graph {
		font: 12px Arial;
	}

	#resume_vacancy_graph path {
		stroke: grey;
		stroke-width: 2;
		fill: none;
	}

	.axis path,
	.axis line {
		fill: none;
		stroke: grey;
		stroke-width: 1;
		shape-rendering: crispEdges;
	}

	.circle-blue {
		background-color: var(--link-hover-color);
		width: 6px;
		height: 6px;
		border-radius: 50%;
		display: inline-block;
		vertical-align: 2px;
		margin-right: 10px;
	}

	.circle-light {
		background-color: var(--hover-border-color);
		width: 6px;
		height: 6px;
		border-radius: 50%;
		display: inline-block;
		vertical-align: 2px;
		margin-right: 10px;
	}

	.circle-grey {
		background-color: var(--text-underline-color);
		width: 6px;
		height: 6px;
		border-radius: 50%;
		display: inline-block;
		vertical-align: 2px;
		margin-right: 10px;
	}
</style>
<script>
	function max_date(all_dates) {
		var max_dt = all_dates[0],
			max_dtObj = new Date(all_dates[0]);
		all_dates.forEach(function(dt, index) {
			if (new Date(dt) > max_dtObj) {
				max_dt = dt;
				max_dtObj = new Date(dt);
			}
		});
		return max_dt;
	}

	function min_date(all_dates) {
		var max_dt = all_dates[0],
			max_dtObj = new Date(all_dates[0]);
		all_dates.forEach(function(dt, index) {
			if (new Date(dt) < max_dtObj) {
				max_dt = dt;
				max_dtObj = new Date(dt);
			}
		});
		return max_dt;
	}
	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	let resume = <?php echo json_encode($resume); ?>;
	let vacancy = <?php echo json_encode($vacancy); ?>;
	let dataset = [];
	let r_dates = resume.map(function(r) {
		return r["year"] + "-" + r["month_numb"];
	})
	let v_dates = vacancy.map(function(r) {
		return r["year"] + "-" + r["month_numb"];
	})
	let dates = Array.from(new Set(v_dates.concat(r_dates)));
	dates.forEach(function(date) {
		let vac = vacancy.find(vac => {
			const d = vac["year"] + "-" + vac["month_numb"];
			return d == date;
		});
		vac = !vac ? 0 : vac["data"];
		let res = resume.find(res => {
			const d = res["year"] + "-" + res["month_numb"];
			return d == date;
		});
		res = !res ? 0 : res["data"];
		dataset.push({
			'date': date,
			'resume': res,
			'vacancy': vac,
		});
	})
	let second_dataset = dataset;
	let margin = {
		top: 30,
		right: 50,
		bottom: 30,
		left: 50
	};
	let svgWidth = 600;
	let svgHeight = 300;
	let graphWidth = svgWidth - margin.left - margin.right;
	let graphHeight = svgHeight - margin.top - margin.bottom;
	d3.timeFormatDefaultLocale({
		"dateTime": "%A, %e %B %Y г. %X",
		"date": "%d.%m.%Y",
		"time": "%H:%M:%S",
		"periods": ["AM", "PM"],
		"days": ["воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота"],
		"shortDays": ["вс", "пн", "вт", "ср", "чт", "пт", "сб"],
		"months": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
		"shortMonths": ["янв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек"]
	})
	let parseDate = d3.timeParse("%Y-%m");
	let x = d3.scaleTime().range([0, graphWidth]);
	let y = d3.scaleLinear().range([graphHeight, 0]);
	let z = d3.scaleOrdinal(["#3965f5", "#9eb3fa"]); // for colours
	let xAxis = d3.axisBottom().scale(x).ticks(10);
	let yAxis = d3.axisLeft().scale(y).ticks(10);

	// // Need to create the lines manually for each bit of data
	let line = d3.line()
		.x(function(d) {
			// console.log(d.date);
			return x(d.date);
		})
		.y(function(d) {
			return y(d.y);
		});

	// Creates the SVG area within the div on the dom 
	// Just doing this once 
	let svg = d3.select("#resume_vacancy_graph")
		.append("svg")
		.attr("width", svgWidth)
		.attr("height", svgHeight)
	let g = svg.append("g")
		.attr("transform",
			"translate(" + margin.left + "," + margin.top + ")")
		.call(d3.zoom().on("zoom", function() {
			svg.attr("transform", d3.event.transform)
		}));

	// Add the X Axis
	g.append("g").attr("class", "x axis")
		.attr("transform", "translate(0," + graphHeight + ")")
		.call(xAxis);
	// Add the Y Axis
	g.append("g")
		.attr("class", "y axis")
		.call(yAxis);
	// text label for the y axis
	g.append("text")
		.attr("transform", "rotate(-90)")
		.attr("y", 0 - margin.left)
		.attr("x", 0 - (graphHeight / 2))
		.attr("dy", "1em")
		.style("text-anchor", "middle")
		.text("Количество");


	function drawGraph(dataset) {

		let pathData = []
		//assume 2 paths
		pathData.push([])
		pathData.push([])

		// Pass in the data here 
		dataset.forEach(function(d) {

			let path0 = {}
			let path1 = {}

			path0.date = parseDate(d.date)
			path1.date = parseDate(d.date)

			path0.y = +d.vacancy
			path1.y = +d.resume

			pathData[0].push(path0)
			pathData[1].push(path1)

		});

		x.domain(d3.extent(dataset, function(d) {
			return parseDate(d.date);
		}));
		y.domain([
			d3.min(dataset, function(d) {
				return Math.min(d.vacancy, d.resume)
			}),
			d3.max(dataset, function(d) {
				return Math.max(d.vacancy, d.resume)
			})
		]);

		svg.selectAll('.x.axis').call(xAxis);
		svg.selectAll('.y.axis').call(yAxis);

		let lines = g.selectAll(".path")
			.data(pathData);

		lines.exit().remove();

		let enter = lines.enter()
			.append("path")
			.attr("class", "path")
			.style("stroke", (d, i) => z(i))

		let merge = enter.merge(lines)
			.attr("d", line)

	}
	window.onload = drawGraph(dataset);


	////
	///////
	////
	/////
	////
	let offers = <?php echo json_encode($offers); ?>;
	let responses = <?php echo json_encode($responses); ?>;
	let employments = <?php echo json_encode($employments); ?>;
	let maximum = Math.max(Math.max(...offers.map(function(o) {
		return o["data"]
	})), Math.max(...responses.map(function(r) {
		return r["data"]
	})), Math.max(...employments.map(function(e) {
		return e["data"]
	})));
	dataset = [];
	let o_dates = offers.map(function(o) {
		return o["year"] + "-" + o["month_numb"];
	})
	let res_dates = responses.map(function(r) {
		return r["year"] + "-" + r["month_numb"];
	})
	let e_dates = employments.map(function(e) {
		return e["year"] + "-" + e["month_numb"];
	})
	let arr = res_dates.concat(o_dates);
	dates = Array.from(new Set(e_dates.concat(arr)));
	dates.forEach(function(date) {
		let off = offers.find(off => {
			const d = off["year"] + "-" + off["month_numb"];
			return d == date;
		});
		off = !off ? 0 : off["data"];
		let resp = responses.find(resp => {
			const d = resp["year"] + "-" + resp["month_numb"];
			return d == date;
		});
		resp = !resp ? 0 : resp["data"];
		let empl = employments.find(empl => {
			const d = empl["year"] + "-" + empl["month_numb"];
			return d == date;
		});
		empl = !empl ? 0 : empl["data"];
		dataset.push({
			key: new Date(date),
			values: [{
					grpName: 'Офферы',
					grpValue: off
				},
				{
					grpName: 'Отклики',
					grpValue: resp
				},
				{
					grpName: 'Трудоустройства',
					grpValue: empl
				}
			]
		});
	})
	dataset.sort(function(a, b) {
		return b.key - a.key;
	});
	dates.sort(function(a, b) {
		return new Date(b) - new Date(a);
	});
	dates.reverse();
	dataset.reverse();
	/*const dataset = [{
			key: new Date(2018, 12),
			values: [{
					grpName: 'Team1',
					grpValue: 26
				},
				{
					grpName: 'Team2',
					grpValue: 15
				},
				{
					grpName: 'Team3',
					grpValue: 48
				}
			]
		}
	];*/
	margin = {
			top: 20,
			right: 20,
			bottom: 30,
			left: 40
		},
		width = 400 - margin.left - margin.right,
		height = 200 - margin.top - margin.bottom;



	let x0 = d3.scaleBand().rangeRound([0, width], .5).padding(0.25);
	let x1 = d3.scaleBand();
	y = y = d3.scaleLinear()
		.domain([0, maximum])
		.range([height, 0]);
	z = d3.scaleOrdinal(["#3965f5", "#9eb3fa", "rgba(199, 210, 254, 0.7)"]); // for colours
	xAxis = d3.axisBottom().scale(x0)
		.tickFormat(function(date) {
			if (d3.timeYear(date) < date) {
				return d3.timeFormat('%B')(date);
			} else {
				return d3.timeFormat('%Y')(date);
			}
		})
		.tickValues(dataset.map(d => d.key));

	yAxis = d3.axisLeft().scale(y);

	let svg_2 = d3.select('#employment_graph').append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	let categoriesNames = dataset.map(function(d) {
		return d.key;
	});
	let rateNames = dataset[0].values.map(function(d) {
		return d.grpName;
	});

	x0.domain(categoriesNames);
	x1.domain(rateNames).rangeRound([0, x0.bandwidth()]);
	y.domain([0, d3.max(dataset, function(key) {
		return d3.max(key.values, function(d) {
			return d.grpValue;
		});
	})]);

	svg_2.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);


	svg_2.append("g")
		.attr("class", "y axis")
		.style('opacity', '0')
		.call(yAxis)
		.append("text")
		.attr("transform", "rotate(-90)")
		.attr("y", 6)
		.attr("dy", ".71em")
		.style("text-anchor", "end")
		.style('font-weight', 'bold')
		.text("Value");

	svg_2.select('.y').style('opacity', '1');

	let slice = svg_2.selectAll(".slice")
		.data(dataset)
		.enter().append("g")
		.attr("class", "g")
		.attr("transform", function(d) {
			return "translate(" + x0(d.key) + ",0)";
		});

	slice.selectAll("rect")
		.data(function(d) {
			return d.values;
		})
		.enter().append("rect")
		.attr("width", x1.bandwidth() - 2.5)
		.attr("x", function(d) {
			return x1(d.grpName);
		})
		.style("fill", (d, i) => z(i))
		.attr("y", function(d) {
			return y(0);
		})
		.attr("height", function(d) {
			return height - y(0);
		})



	slice.selectAll("rect")
		.style("opacity", "1")
		.attr("y", function(d) {
			return y(d.grpValue);
		})
		.attr("height", function(d) {
			return height - y(d.grpValue);
		});
	const tooltip = d3.select("#employment_graph")
		.append("div")
		.style("opacity", 0)
		.attr("class", "tooltip")
		.style("background-color", "black")
		.style("color", "white")
		.style("border-radius", "5px")
		.style("padding", "10px")

	// A function that change this tooltip when the user hover a point.
	// Its opacity is set to 1: we can now see it. Plus it set the text and position of tooltip depending on the datapoint (d)
	const showTooltip = function(event, d) {
		tooltip
			.transition()
			.duration(100)
			.style("opacity", 1)
		tooltip
			.html(d.grpName + " - " + d.grpValue)
			.style("left", (event.x) + "px")
			.style("top", (event.y) / 2 + "px")
	}
	const moveTooltip = function(event, d) {
		tooltip
			.style("left", (event.x) - 35 + "px")
			.style("top", (event.y) / 2 + 180 + "px")
	}
	// A function that change this tooltip when the leaves a point: just need to set opacity to 0 again
	const hideTooltip = function(event, d) {
		tooltip
			.transition()
			.duration(100)
			.style("opacity", 0)
	}

	// append the bar rectangles to the svg element
	svg_2.selectAll("rect")
		// Show tooltip on hover
		.on("mouseover", showTooltip)
		.on("mousemove", moveTooltip)
		.on("mouseleave", hideTooltip)
	/////
	///////
	/////

	///////
	/////
	$(".fa-file-arrow-down").on("click", function() {
		$('#download-modal').show();

		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$("#btn-cancel-download").click(function() {
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			$('#download-modal').hide();
		})
		////
		///
		$("#btn-download").on("click", function() {
			const type = $('input[name="radios"]:checked').val();
			if (type == 1) {
				let dowloaded_dataset = [];
				dataset.forEach(function(ds) {
					dowloaded_dataset.push({
						date: ds.key,
						offers: ds.values[0].grpValue,
						responses: ds.values[1].grpValue,
						employments: ds.values[2].grpValue,
					})
				});
				const filename = 'report' + Date.now() + '.xlsx';
				let ws = XLSX.utils.json_to_sheet(dowloaded_dataset);
				let wb = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(wb, ws, "Report");
				XLSX.writeFile(wb, filename);
			} else if (type == 2) {
				second_dataset = second_dataset.map(function(sd) {
					return {
						date: new Date(sd["date"]),
						resumes: sd["resume"],
						vacancies: sd["vacancy"],
					}
				})
				const filename = 'report' + Date.now() + '.xlsx';
				let ws = XLSX.utils.json_to_sheet(second_dataset);
				let wb = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(wb, ws, "Report");
				XLSX.writeFile(wb, filename);
			}
		})
	})
	/////
	///////
	/////
	///////
	let data = <?php echo json_encode($employments_category); ?>;
	let summ = data.map(function(v) {
			return v.val
		})
		.reduce(function(a, b) {
			return a + b
		});
	data = data.map(function(v) {
		return {
			name: v.name,
			val: (v.val / summ * 100).toFixed(2)
		};
	})

	margin = {
		top: 40,
		right: 40,
		bottom: 40,
		left: 40
	};

	width = 500 - margin.right - margin.left;
	height = 300 - margin.top - margin.bottom;
	let radius = 200;

	let svg_3 = d3.select('#donut_types')
		.append('svg')
		.attr('width', width)
		.attr('height', height)
		.append('g')
		.attr('transform', 'translate(' + width / 2 + ',' + height + ')');

	svg_3.append('g')
		.attr('class', 'slices');
	svg_3.append('g')
		.attr('class', 'labels');
	svg_3.append('g')
		.attr('class', 'lines');

	let color = d3.scaleOrdinal(["#3965f5", "#9eb3fa", "rgba(199, 210, 254, 0.7)"]);

	let pie = d3.pie()
		.sort(null)
		.value(function(d) {
			return d.val;
		})
		.startAngle(-90 * (Math.PI / 180))
		.endAngle(90 * (Math.PI / 180));

	// donut chart arc
	let arc = d3.arc()
		.innerRadius(radius - 100)
		.outerRadius(radius - 50);

	slice = svg_3.select('.slices')
		.selectAll('path.slice')
		.data(pie(data));

	slice.enter()
		.append('path')
		.on("mouseover", function(d, i) {
			svg_3.append("text")
				.attr("dy", "-3em")
				.style("text-anchor", "middle")
				.style("font-size", 16)
				.attr("class", "label")
				.style("fill", function(d, i) {
					return "black";
				})
				.html(i.data.name);
			svg_3.append("text")
				.attr("dy", "-1.5em")
				.style("text-anchor", "middle")
				.style("font-size", 16)
				.attr("class", "label")
				.style("fill", function(d, i) {
					return "black";
				})
				.html(i.data.val + "%");
		})
		.on("mouseout", function(d) {
			svg_3.select(".label").remove();
			svg_3.select(".label").remove();
		})
		.attr('d', arc)
		.attr('fill', function(d) {
			return color(d.data.name);
		})
		.attr('class', 'slice');

	// label arc
	let labelArc = d3.arc()
		.innerRadius(radius * 0.9)
		.outerRadius(radius * 0.9);

	let labels = svg_3.select('.labels')
		.selectAll('text')
		.data(pie(data));
</script>