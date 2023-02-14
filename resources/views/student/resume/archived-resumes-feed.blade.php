<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<x-student-layout>
	<div class="row">
		<div class="col-md-8">
			<p class="medium-text mt-4" style="margin-left:160px;">Архив</p>
		</div>
		<div class="col-md-3">
			@if (Auth::User()->resume)
			<button class="button add-current">
				<a href="{{ route('student.archive-resume') }}">{{ __('+ Добавить текущее') }}</a>
			</button>
			@endif
		</div>
	</div>
	<section class='center'>
		<table id='archived-resumes' class='table table-hover'>
			<tr class='t-head'>
				<td>Профессия</td>
				<td>Вид занятости</td>
				<td>Тип работы</td>
				<td>Дата архивации</td>
				<td>Навыки</td>
				<td></td>
			</tr>
			@foreach($archived_resumes as $resume)
			<tr>
				<td>{{$resume->profession->profession_name}}</td>
				<td>{{$resume->type_of_employment->type_of_employment_name}}</td>
				<td>{{$resume->work_type->work_type_name}}</td>
				<td>{{date_format(date_create($resume->archived_at), 'd-m-Y')}}</td>
				<td class="student_skills_area">
					@php $student_hard_skills = App\Models\StudentSkill::where('resume_id', $resume->id)
					->join('skills', 'skills.id', '=', 'student_skills.skill_id')
					->where('skill_type', 1)
					->get()
					@endphp
					@php $j = 0; @endphp
					@foreach ($student_hard_skills as $shs)
					@if ($j < 4) <span class="student_skill">{{$shs->skill_name}}</span>
						@endif
						@php $j++; @endphp
						@endforeach
						@if ($j >= 4)
						<span class="student_skill">+1</span>
						@endif
				</td>
				<td>
					<div class="hidden sm:flex sm:items-center sm:ml-4">
						<x-dropdown align="left" width="38">
							<x-slot name="trigger">
								<button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
									<div>⋮</div>
								</button>
							</x-slot>
							<x-slot name="content">
								<x-dropdown-link class="unarchive-btn" id="unarchive-btn-{{$resume->id}}">
									Разархивировать
								</x-dropdown-link>
								<x-dropdown-link class="view-btn" href="resume/{{$resume->id}}">
									Просмотреть
								</x-dropdown-link>
							</x-slot>
						</x-dropdown>
					</div>
				</td>
			</tr>
			@endforeach
			<script>
				$('.student_skill').each(function(i, elem) {
					if (i % 3 == 0)
						$(this).addClass("first-skill");
					else if (i % 3 == 1)
						$(this).addClass("second-skill");
					else if (i % 3 == 2)
						$(this).addClass("third-skill");
				})
			</script>
		</table>
	</section>
</x-student-layout>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
	html {
		overflow-x: hidden;
	}

	.center {
		background-color: white;
		margin: 30px auto 0 auto;
		border-radius: 10px;
		height: 600px;
		width: 1300px;
	}

	.table {
		width: 1200px !important;
		font-size: 14px;
		margin-left: auto;
		margin-right: auto;
	}

	tr:hover {
		background-color: rgb(251, 251, 254);
	}

	td {
		height: 50px;
		vertical-align: middle;
	}

	.t-head {
		font-size: 13px;
	}

	/* */

	.dropbtn {
		border: none;
		cursor: pointer;
		font-weight: 700;
	}

	.add-current {
		margin-left: 120px;
		margin-top: 30px;
	}

	.add-current a {
		color: white !important;
	}

	.unarchive-btn,
	.view-btn {
		cursor: pointer;
	}
</style>
<script>
	$(".unarchive-btn").on('click', function() {
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
				location.reload();
			},
			error: function(msg) {
				console.log("Не получилось разархивировать резюме")
			}
		});
	})
</script>

</html>