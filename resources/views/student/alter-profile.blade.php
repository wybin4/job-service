<!DOCTYPE html>
<html>

<head>
	<meta name="_token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>

</head>
<x-student-layout>

	<x-big-card>
		<div style="width:690px;">
			<h2 class="header-text">Редактирование профиля</h2>
			<div class="row" style="margin-top: 20px;">
				<div class="col-md-auto">
					@if (!Auth::guard('student')->user()->image)
					<div class="future-pic">{{mb_substr(Auth::User()->student_fio, 0, 1)}}</div>
					@else
					<img class="pic" src="{{asset('/storage/images/'.Auth::guard('student')->user()->image)}}" />
					@endif
					<p id="photo-container">
						<input type="file" name="image" class="image" id="file">
						<button id="image-btn" style="margin-left:23px;">Изменить</button>
					<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title font-bold text-2xl" id="modalLabel">Обрезка изображения</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-#photo-container">
									<div class="img-container">
										<div class="row">
											<div class="col-md-8">
												<img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
											</div>
											<div class="col-md-4">
												<div class="preview"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="button" data-dismiss="modal">Отменить</button>
									<button type="button" class="button" id="crop">Обрезать и сохранить</button>
								</div>
							</div>
						</div>
					</div>
					</p>
				</div>
				<div class="col-md-auto">
					<div style="margin-top:20px" class="row">
						<div class="col-md-auto">
							<x-label :value="__('Фамилия')" />
							<x-input autocomplete="off" id="surname" type="text" class="form-data" style="width:241px;" value="{{ explode(' ', Auth::guard('student')->user()->student_fio)[0]}}" />
						</div>
						<div class="col-md-auto">
							<x-label :value="__('Имя')" />
							<x-input autocomplete="off" id="firstname" type="text" class="form-data" value="{{ explode(' ', Auth::guard('student')->user()->student_fio)[1]}}" style="width:241px;" />
						</div>
					</div>
					<div style="margin-top:20px">
						<x-label :value="__('Отчество')" />
						<x-input autocomplete="off" id="fathername" type="text" class="form-data" value="{{ explode(' ', Auth::guard('student')->user()->student_fio)[2]}}" style="width:510px;" />
					</div>
					<div style="margin-top:20px">
						<x-label :value="__('Email')" />
						<x-input autocomplete="off" id="email" type="email" class="form-data" value="{{ Auth::guard('student')->user()->email }}" style="width:510px;" />
					</div>
					<div class="select-div" style="margin-top:20px">
						<x-label :value="__('Местоположение')" />
						<input autocomplete="off" id="select" name="location" class="chosen-value" type="text" value="{{ Auth::guard('student')->user()->location }}">
						<ul class="value-list" id="value-list-1"></ul>
					</div>
					@if (Auth::User()->resume)
					<div style="margin-top:20px;margin-left:-5px;margin-bottom:30px;">
						<label for="newsletter_subscription" class="inline-flex items-center">
							<input name="newsletter_subscription" id="newsletter_subscription" type="checkbox" class="form-data checkbox ml-1" @if(Auth::guard('student')->user()->newsletter_subscription) checked @endif />
							<span class="ml-2 text-sm text-gray-600">{{ __('Уведомления о новых вакансиях') }}</span>
						</label>
					</div>
					@else
					<div style="margin-top:20px;">
						@endif
					</div>
				</div>
			</div>
	</x-big-card>
	<x-big-card>
		<form method="POST" action="{{ route('student.alter-password') }}" style="width:690px">
			<h2 class="header-text">Безопасность и пароли</h2>
			@if(session()->has('success'))
			<script>
				create_notify('success', 'Изменение пароля', '{{session()->get("success")}}', 20);
			</script>
			@elseif(session()->has('errors'))
			<x-errors class="mb-4" :errors="$errors" style="margin-top:10px;" />
			@endif
			@csrf
			<div style="margin-top:30px">
				<x-label :value="__('Новый пароль')" />
				<x-input autocomplete="off" id="password" name="password" type="password" style="width:650px;" required />
			</div>
			<div style="margin-top:20px">
				<x-label :value="__('Повторить пароль')" />
				<x-input autocomplete="off" id="repeat-password" name="repeat_password" type="password" style="width:650px;" required />
			</div>
			<div class="d-flex justify-content-center" style="margin-top:20px;">
				<button class="button" style="margin-top:20px">Сохранить</button>
			</div>
		</form>
	</x-big-card>
</x-student-layout>
<script>
	let requestUrl = 'https://raw.githubusercontent.com/pensnarik/russian-cities/master/russian-cities.json';
	let xhr = new XMLHttpRequest();

	xhr.open('GET', requestUrl, true);
	xhr.responseType = 'json';
	xhr.send()

	xhr.onload = function() {
		let cities = xhr.response;
		let list = '';
		for (let i = 0; i < cities.length; i++) {
			list += '<li class="li-1" value="' + i + 1 + '">' + cities[i].name + ", " + cities[i].subject + '</li>\n';
		}
		$("#value-list-1").html(list);
		//// dummy code for selector-1

		let inputField1 = document.getElementById('select');
		let dropdown1 = document.getElementById("value-list-1");
		let dropdownArray1 = [...dropdown1.querySelectorAll('li')];

		function closeDropdown(dropdown) {
			dropdown.classList.remove('open');
		}
		closeDropdown(dropdown1);

		inputField1.addEventListener('input', () => {
			let valueArray1 = [];
			dropdownArray1.forEach(item => {
				valueArray1.push(item.textContent);
			});
			dropdown1.classList.add('open');
			let inputValue = inputField1.value.toLowerCase();
			let valueSubstring;
			if (inputValue.length > 0) {
				for (let j = 0; j < valueArray1.length; j++) {
					if (!(inputValue.substring(0, inputValue.length) === valueArray1[j].substring(0, inputValue.length).toLowerCase())) {
						dropdownArray1[j].classList.add('closed');
					} else {
						dropdownArray1[j].classList.remove('closed');
					}
				}
			} else {
				for (let i = 0; i < dropdownArray1.length; i++) {
					dropdownArray1[i].classList.remove('closed');
				}
			}
		});
		dropdownArray1.forEach(item => {
			item.addEventListener('click', (evt) => {
				inputField1.value = item.textContent;
				dropdownArray1.forEach(dropdown1 => {
					dropdown1.classList.add('closed');
				});

				let form = new FormData();
				form.append('surname', $("#surname").val());
				form.append('firstname', $("#firstname").val());
				form.append('fathername', $("#fathername").val());
				form.append('email', $("#email").val());
				form.append('location', $("input[name='location']").val());
				form.append('newsletter_subscription', $("#newsletter_subscription").val());
				$.ajax({
					url: '{{ route("student.alter-profile") }}',
					type: "POST",
					data: form,
					cache: false,
					contentType: false,
					processData: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Изменили профиль!")
						create_notify('success', 'Редактирование профиля', 'Изменения сохранены', 20);

					},
					error: function(msg) {
						if (msg.responseJSON.message) {
							$.each(msg.responseJSON.errors, function(key, value) {
								create_notify('error', 'Редактирование профиля', value);
							});
						}
						console.log("Не получилось изменить профиль")
					}
				});

			});
		})

		inputField1.addEventListener('focus', () => {
			dropdown1.classList.remove('open');
			inputField1.placeholder = 'Поиск';
			dropdown1.classList.add('open');
			dropdownArray1.forEach(dropdown1 => {
				dropdown1.classList.remove('closed');
			});
		});

		inputField1.addEventListener('blur', () => {
			dropdown1.classList.remove('open');
		});
	}
	///

	//отправляем данные
	$('.form-data').change(function() {
		const location = $("input[name='location']").val();
		xhr.open('GET', requestUrl, true);
		xhr.responseType = 'json';
		xhr.send()

		xhr.onload = function() {
			let cities = xhr.response;
			cities = cities.map(city => city.name + ", " + city.subject)
			if (cities.includes(location)) {
				let form = new FormData();
				form.append('surname', $("#surname").val());
				form.append('firstname', $("#firstname").val());
				form.append('fathername', $("#fathername").val());
				form.append('email', $("#email").val());
				form.append('location', $("input[name='location']").val());
				form.append('newsletter_subscription', $("#newsletter_subscription").val());
				$.ajax({
					url: '{{ route("student.alter-profile") }}',
					type: "POST",
					data: form,
					cache: false,
					contentType: false,
					processData: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						create_notify('success', 'Редактирование профиля', 'Изменения сохранены', 20);
						console.log("Изменили профиль!")
					},
					error: function(msg) {
						if (msg.responseJSON.message) {
							$.each(msg.responseJSON.errors, function(key, value) {
								create_notify('error', 'Редактирование профиля', value);
							});
						}
						console.log("Не получилось изменить профиль")
					}
				});
			}
		}
	});
	////
	////
	console.log("{{storage_path() . '\app\public\images\\'}}")
	////фоточка
	$(document).ready(function() {
		$('#image-btn').on('click', function() {
			$('#file').trigger('click');
		});
		let $modal = $('#modal');
		let image = document.getElementById('image');
		let cropper;

		function getRoundedCanvas(sourceCanvas) {
			let canvas = document.createElement('canvas');
			let context = canvas.getContext('2d');
			let width = sourceCanvas.width;
			let height = sourceCanvas.height;

			canvas.width = width;
			canvas.height = height;
			context.imageSmoothingEnabled = true;
			context.drawImage(sourceCanvas, 0, 0, width, height);
			context.globalCompositeOperation = 'destination-in';
			context.beginPath();
			context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
			context.fill();
			return canvas;
		}
		$("#photo-container").on("change", ".image", function(e) {
			let files = e.target.files;
			var name = files[0].name;
			var ext = name.split('.').pop().toLowerCase();
			if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
				create_notify('error', 'Редактирование профиля', 'Неверный формат файла');
			} else {
				var oFReader = new FileReader();
				oFReader.readAsDataURL(files[0]);
				var f = document.getElementById("file").files[0];
				var fsize = f.size || f.fileSize;
				if (fsize > 2000000) {
					create_notify('error', 'Редактирование профиля', 'Слишком большое изображение');
				} else {
					let done = function(url) {
						image.src = url;
						$modal.modal('show');
					};
					let reader;
					let file;
					let url;
					if (files && files.length > 0) {
						file = files[0];
						if (URL) {
							done(URL.createObjectURL(file));
						} else if (FileReader) {
							reader = new FileReader();
							reader.onload = function(e) {
								done(reader.result);
							};
							reader.readAsDataURL(file);
						}
					}
				}
			}

		});
		$modal.on('shown.bs.modal', function() {
			cropper = new Cropper(image, {
				aspectRatio: 1,
				viewMode: 3,
				preview: '.preview'
			});
		}).on('hidden.bs.modal', function() {
			cropper.destroy();
			cropper = null;
		});
		$("#crop").click(function() {
			canvas = cropper.getCroppedCanvas();
			canvas = getRoundedCanvas(canvas);
			canvas.toBlob(function(blob) {
				url = URL.createObjectURL(blob);
				let reader = new FileReader();
				reader.readAsDataURL(blob);
				reader.onloadend = function() {
					let base64data = reader.result;
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "/student/alter-profile",
						data: {
							'_token': $('meta[name="_token"]').attr('content'),
							'image': base64data
						},
						success: function(data) {
							$modal.hide();
						}
					});
					//location.reload();
				}
			});
		})
	});

	/////
	////
	//
</script>
<style>
	.image-btn,
	.image-btn:active,
	.image-btn:focus,
	input[type="file"] {
		border: none;
		outline: none;
	}

	button:active,
	button:hover,
	button:focus {
		outline: 0 !important;
		outline-offset: 0 !important;
		box-shadow: none !important;
	}

	.future-pic {
		font-size: 52px;
		display: table-cell;
		vertical-align: middle;
		text-align: center;
	}

	.pic,
	.future-pic {
		width: 120px;
		height: 120px;
		margin: 20px 40px 20px 0px;
	}

	input[type="file"] {
		display: none;
	}

	img {
		display: block;
		max-width: 100%;
	}

	.preview {
		overflow: hidden;
		margin: 10px;
		border-radius: 50%;
		width: 160px;
		height: 160px;
	}

	.modal-lg {
		max-width: 1000px !important;
	}

	.cropper-view-box,
	.cropper-face {
		border-radius: 50%;
	}

	.cropper-view-box {
		outline: 0;
		box-shadow: 0 0 0 1px #39f;
	}
</style>

</html>