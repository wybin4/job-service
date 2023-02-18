<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
</head>
<x-university-layout>
	<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<h2 class="header-text text-center" style="margin-bottom:30px;">Зарегестрировать студента</h2>
			@if(session()->has('errors'))
			@foreach ($errors->all() as $error)
			<script>
				create_notify('error', 'Регистрация студентов', '{{$error}}', 20);
			</script>
			@endforeach
			@endif
			@if(session('message'))
			<script>
				create_notify('success', 'Регистрация студента', '{{ session("message") ?? "Успешно зарегестрировали студента"}}');
			</script>
			@endif
			<form method="POST" action="{{ url('university/add-one') }}">
				@csrf
				<div>
					<x-label for="student_fio" :value="__('ФИО')" />
					<x-input autocomplete="off" id="student_fio" class="block mt-1 w-full" type="text" name="student_fio" :value="old('student_fio')" required autofocus />
				</div>
				<div class="mt-4">
					<x-label for="email" :value="__('Email')" />
					<x-input autocomplete="off" id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
				</div>
				<div class="flex items-center justify-end mt-4">
					<button class="ml-4 button">
						{{ __('Добавить студента') }}
					</button>
				</div>
			</form>
		</div>
	</div>
</x-university-layout>