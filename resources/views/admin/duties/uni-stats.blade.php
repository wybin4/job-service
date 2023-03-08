<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<x-admin-layout>
	<p class="header-text text-center pt-4 pb-3">Рейтинг учебных заведений</p>
	<section class='center pt-3'>
		<table id='my-responses-table' class='table table-hover'>
			<thead>
				<tr class='t-head text-muted'>
					<td>Место</td>
					<td>Название учебного заведения</td>
					<td>Востребованность студентов у работодателей</td>
					<td>Средняя оценка студентов</td>
					<td>Итоговый балл</td>
				</tr>
			</thead>
			<tbody>
				@foreach($universities as $university)
				<tr>
					<td>{{$university["place"]}}</td>
					<td>{{$university["name"]}}</td>
					<td>{{number_format($university["demand"], 2)}}</td>
					<td>{{number_format($university["rate"], 2)}}</td>
					<td>{{number_format($university["total"], 2)}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</section>
</x-admin-layout>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

	.first-div {
		margin: 0 210px;
		padding-top: 170px;
	}

	.big-text {
		font-size: 52px;
		font-family: 'Montserrat';
		font-weight: 600;
	}

	.big-text:first-child {
		padding-top: 50px;
	}

	/** */
	html {
		overflow-x: hidden;
	}

	.center {
		background-color: white;
		margin: 30px auto 0 auto;
		border-radius: 10px;
		min-height: 600px;
		width: 1200px;
	}

	.table {
		width: 1100px !important;
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

	html {
		overflow-x: hidden;
	}
</style>

</html>