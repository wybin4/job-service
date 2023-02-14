<x-university-layout>
	<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<h2 class="header-text text-center" style="margin-bottom:30px;">Зарегестрировать студента</h2>
			<x-errors class="mb-4" :errors="$errors" />
			@if(session('message'))
			<div class="alert alert-success mb-3" role="alert">
				{{ session('message') ?? "Успех!"}}
			</div>
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