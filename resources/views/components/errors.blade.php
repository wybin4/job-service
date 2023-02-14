@props(['errors'])

@if ($errors->any())
<div {{ $attributes }}>
	@foreach ($errors->all() as $error)
	<div class="alert alert-danger">{{ $error }}</div>
	@endforeach
</div>
@endif