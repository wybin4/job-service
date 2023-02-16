<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<x-student-layout>
	<div class="row" style="background-color:white;height:100vh;padding:40px;">
		<div class="col-md-3">
			<div class="col-title font-bold mb-2">Отклики</div>
			<a class="row btn-area" href="{{ url('/student/my-responses') }}">
				<div class="col-md-auto btn-i">
					<i class="fa-solid fa-reply"></i>
				</div>
				<div class="col-md-auto">
					<div class="btn-title">Мои отклики</div>
					<div class="btn-text">Просмотр статуса</div>
				</div>
			</a>
		</div>
		<div class="col-md-3">
			<div class="col-title font-bold mb-2">Офферы</div>
			<a class="row btn-area" href="{{ url('/student/all-offers') }}">
				<div class="col-md-auto btn-i">
					<i class="fa-solid fa-share"></i>
				</div>
				<div class="col-md-auto">
					<div class="btn-title">Офферы на моё резюме</div>
					<div class="btn-text">Просмотр</div>
				</div>
			</a>
		</div>
		<div class="col-md-3">
			<div class="col-title font-bold mb-2">Другое</div>
			<a class="row btn-area" href="{{ url('/student/places-of-work') }}">
				<div class="col-md-auto btn-i">
					<i class="fa-solid fa-building"></i>
				</div>
				<div class="col-md-auto">
					<div class="btn-title">Мои места работы</div>
					<div class="btn-text">Просмотр</div>
				</div>
			</a>
		</div>
	</div>
</x-student-layout>
<style>
	html {
		overflow-x: hidden;
	}

	.btn-area {
		cursor: pointer;
		padding: 15px 5px;
		margin-bottom: 5px;
		border-radius: 20px;
	}

	.btn-area:hover {
		background-color: var(--dot-color);
		transition: 0.8s;
	}

	.btn-i {
		padding: 9px;
		color: var(--link-hover-color);
		background-color: var(--dot-color);
		border-radius: 20px;
		margin-left: 10px;
		width: 40px;
		height: 40px;
		display: table-cell;
		text-align: center;
	}

	.btn-title {
		font-weight: 600;
	}

	.btn-text {
		font-size: 14px;
		color: grey;
		margin-top: -5px;
	}
</style>

</html>