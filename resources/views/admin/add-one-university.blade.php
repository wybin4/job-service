<html>

<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>
</head>
<x-admin-layout>
	<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<h2 class="header-text text-center" style="margin-bottom:30px;">Зарегистрировать</h2>
			@if ($errors->any())
			@foreach ($errors->all() as $error)
			<script>
				create_notify('error', '{{$error}}');
			</script>
			@endforeach
			@endif
			@if (session()->get('title'))
			<script>
				create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}');
			</script>
			@endif
			<form method="POST" action="{{ url('admin/add-one-university') }}">
				@csrf
				<div>
					<x-label for="name" :value="__('Название образовательного учереждения')" />
					<x-input id="name" autocomplete="off" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
				</div>
				<div class="mt-4">
					<x-label for="email" :value="__('Email')" />
					<x-input id="email" autocomplete="off" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
				</div>
				<div class="flex items-center justify-end" style="margin-top:30px">
					<button class="ml-4 button">
						{{ __('Добавить') }}
					</button>
				</div>
			</form>
		</div>
	</div>
</x-admin-layout>