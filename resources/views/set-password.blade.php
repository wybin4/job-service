<x-guest-layout>
	<x-slot name="logo">
		<a href="/">
			<x-application-logo class="w-20 h-20 fill-current text-gray-500" />
		</a>
	</x-slot>


	<div class="text-center font-medium text-green-600">{{ session('message') ?? ""}}</div>
	<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<x-auth-validation-errors class="mb-4" :errors="$errors" />
			<form method="POST" action="{{ parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) }}">

				@csrf
				<div>
					<x-label for="password" :value="__('Пароль')" />
					<x-input id="password" class="block mt-1 w-full" type="password" name="password" :value="old('password')" required autofocus />
				</div>

				<div class="flex items-center justify-end mt-4">
					<button class="button">
						{{ __('Установить пароль') }}
					</button>
				</div>
			</form>
		</div>
	</div>
</x-guest-layout>