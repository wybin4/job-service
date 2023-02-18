<!DOCTYPE html>
<html>

<head>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
</head>
<x-university-layout>
	<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<h2 class="header-text text-center">Зарегестрировать студентов</h2>
			<p class="text-muted text-center mt-2" style="margin-bottom:30px;">Файл должен быть в формате xls или xlsx и иметь следующую структуру</p>
			@if(session()->has('errors'))
			@foreach ($errors->all() as $error)
			<script>
				create_notify('error', 'Регистрация студентов', '{{$error}}');
			</script>
			@endforeach
			@endif
			@if(session('message'))
			<script>
				create_notify('success', 'Регистрация студентов', '{{ session("message") ?? "Успешно зарегестрировали студентов"}}');
			</script>
			@endif
			<img src="{{ asset('/storage/app_images/how_it_looks_like.png') }}" />
			<form method="POST" action="{{ url('university/add-many') }}" enctype="multipart/form-data">
				@csrf
				<div class="mt-4 mb-3">
					<x-label for="students-file" />
					<input autocomplete="off" name="students-file" id="students-file" type="file" class="block mt-1 w-full" />
					<span id="file-btn" class="span-like-button">Загрузить файл</span>
				</div>
				<div class="flex items-center justify-end mt-4" style="display:none;" id="add-students">
					<button class="button">
						{{ __('Добавить студентов') }}
					</button>
				</div>
			</form>
		</div>
	</div>
</x-university-layout>
<style>
	#file-btn,
	#file-btn:active,
	#file-btn:focus,
	input[type="file"] {
		border: none;
		outline: none;
	}

	input[type="file"] {
		display: none;
	}

	#file-btn {
		margin-left: 120px;
	}
</style>
<script>
	$('#file-btn').on('click', function() {
		$('#students-file').trigger('click');
	});
	document.getElementById('students-file').addEventListener('change', function() {
		if (this.value) {
			$('#file-btn').hide();
			$("#add-students").show();
		}
	});
</script>

</html>