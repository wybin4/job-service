<!DOCTYPE html>
<html>

<head>
	<meta name="_token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="{{asset('/js/petrovich.js')}}"></script>
	<!---->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/jquery.barrating.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/themes/fontawesome-stars.min.css" rel="stylesheet" />

</head>
<x-employer-layout>
	<form method="POST" action="/employer/edit-student-rate">
		@csrf
		<input type="hidden" value="{{$student->id}}" name="student_id" />
		<input type="hidden" value="{{$vacancy_id}}" name="vacancy_id" />
		<div class="text-center header-text mt-4">Оцените качества студента</div>
		<div>
			<x-big-card>
				<div class="skill-rate-card-hidden" style="display:none;width:490px;">
					<div class="row">
						<div class="col-md-auto">
							<div class="number-circle">1</div>
						</div>
						<div class="col-md-auto">
							<div class="rate-head">Навыки студента</div>
						</div>
					</div>
				</div>
				<div class="skill-rate-card">
					<div class="row">
						<div class="col-md-auto">
							<div class="number-circle">1</div>
						</div>
						<div class="col-md-auto">
							<div class="rate-head">Навыки студента</div>
							<div class="text-muted" style="margin-left:-10px;">Как вы оцениваете навыки <span class="student-name genitive">{{$student->student_fio}}</span>?</div>
						</div>
					</div>
					<div class="mt-2">
						<div id="rate-area"></div>
						<span class="span-like-button btn-next">Далее</span>
					</div>
				</div>
			</x-big-card>
			<x-big-card>
				<div class="review-card-hidden" style="width:490px;">
					<div class="row">
						<div class="col-md-auto">
							<div class="number-circle">2</div>
						</div>
						<div class="col-md-auto">
							<div class="rate-head">Мнение о студенте</div>
						</div>
					</div>
				</div>
				<div class="review-card" style="display:none;">
					<div class="row">
						<div class="col-md-auto">
							<div class="number-circle">2</div>
						</div>
						<div class="col-md-auto">
							<div class="rate-head">Мнение о студенте</div>
							<div class="text-muted" style="margin-left:-10px;">Что вы думаете о <span class="student-name prepositional">{{$student->student_fio}}</span>?</div>
						</div>
					</div>
					<textarea name="description">{{$description->text}}</textarea>
					<div class="mt-2">
						<button class="button btn-rate" type="submit">Оценить</button>
					</div>
				</div>
			</x-big-card>
		</div>
	</form>
</x-employer-layout>

</html>

<style>
	textarea {
		width: 490px;
		margin: 20px 0;
	}

	.btn-next {
		margin-left: 400px;
	}

	.btn-rate {
		margin-left: 382px;

	}

	.number-circle {
		height: 30px;
		width: 30px;
		background-color: var(--link-hover-color);
		color: white;
		border-radius: 50%;
		font-size: 16px;
		display: table-cell;
		vertical-align: middle;
		text-align: center;
	}

	.rate-head {
		margin-top: 2px;
		margin-left: -10px;
		font-size: 18px;
		font-weight: bold;
	}


	.br-theme-fontawesome-stars .br-widget a::after {
		color: var(--dot-color) !important;
	}

	.br-theme-fontawesome-stars .br-widget a.br-active:after {
		color: var(--link-hover-color) !important;
	}

	.br-theme-fontawesome-stars .br-widget a.br-selected:after {
		color: var(--link-hover-color) !important;
	}

	#rate-area {
		display: flex;
		flex-wrap: wrap;
		flex-direction: row;
		justify-content: start;
		min-height: 140px;
		width: 490px;
		margin: auto;
		margin-top: 30px;
	}

	.rate-block {
		width: 150px;
		height: 50px;
	}
</style>
<script>
	let name = $(".student-name").text().split(' ');
	let gender = new SexByRussianName(name[0], name[1], name[2]);
	if (gender.get_gender()) {
		gender = 'male';
	} else gender = 'female';
	var person = {
		gender: gender,
		first: name[1],
		last: name[0]
	};
	$(".genitive").text(petrovich(person, 'genitive').last + " " + petrovich(person, 'genitive').first)
	$(".prepositional").text(petrovich(person, 'prepositional').last + " " + petrovich(person, 'prepositional').first)

	////
	///
	let hard_skills = <?php echo json_encode($student_skills); ?>;
	let rated_skills = <?php echo json_encode($need_skills); ?>;
	for (let i = 0; i < hard_skills.length; i++) {
		const id = `rating-${hard_skills[i].skill_id}`;
		let text = `<div class="rate-block">
						<x-label for="${id}">${hard_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
		$('#rate-area').append(text);
		$(function() {
			$('#' + id).barrating({
				theme: 'fontawesome-stars'
			});
			$('#' + id).barrating('set', rated_skills[i].skill_rate);
		});
	}
	$(".btn-next").on('click', function() {
		$(".skill-rate-card-hidden").show();
		$(".skill-rate-card").hide();
		$(".review-card-hidden").hide();
		$(".review-card").show();
	})
</script>