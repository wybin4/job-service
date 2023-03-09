<x-student-layout>
	<div style="display:flex;background-color:white;padding-bottom:20px">
		<div style="width:90vh;margin:70px 90px">
			<img id="error-img" src="{{ asset('/storage/app_images/error_404.svg') }}" alt="error-img">
		</div>
		<div class="text-col">
			<h1 class="big-text">Ошибка 404</h1>
			<h1 class="header-text">{{$text}}</h1>
			<button class="button"><a href="/student/dashboard">Вернуться на главную</a></button>
		</div>
	</div>
</x-student-layout>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

	* {
		overflow-y: hidden;
	}

	.text-col {
		display: table-cell;
		margin-top: 180px;
		margin-left: 40px;
	}

	.text-col .button {
		margin-top: 50px;
	}

	.big-text {
		font-size: 55px;
		font-family: 'Montserrat';
		font-weight: 600;
	}

	.header-text {
		font-size: 25px;
		color: grey;
		margin-top: 10px;
		width: 540px;
	}
</style>