<head>
	<script src="/js/selector.js"></script>
</head>

<x-student-layout>
	@foreach($category as $val)
	@if($val->id == $resume->profession->subsphere_id)
	@php
	$category_name = $val->subsphere_of_activity_name
	@endphp
	@endif
	@endforeach
	@foreach($category as $val)
	@if($val->id == $resume->profession->subsphere_id)
	@php
	$sphere_id = $val->sphere_id
	@endphp
	@endif
	@endforeach
	@foreach($sphere as $val)
	@if($val->id == $sphere_id)
	@php
	$sphere_name = $val->sphere_of_activity_name
	@endphp
	@endif
	@endforeach
	<x-big-card>
		<x-auth-session-status class="mb-4" :status="session('status')" />
		<x-auth-validation-errors class="mb-4" :errors="$errors" />
		<form method="POST" action="{{ route('student.alter-resume') }}">
			@csrf
			<div id="profession_id">
				<input type="hidden" id="profession_id_input" name="profession_id" value="{{$resume->profession_id}}" />
			</div>
			<div>
				<x-label for="select-1" :value="__('Сфера деятельности')" style="margin-top:20px" />
				<div class="select-div">
					<x-label for="select-1" :value="__('Сфера деятельности')" />

					<input autocomplete="off" id="select-1" class="chosen-value" type="text" placeholder="{{$sphere_name}}">
					<ul class="value-list" id="value-list-1">
						@foreach($sphere as $val)
						<li value="{{ $val->id}}" {{ ((isset($sphere_id) && $sphere_id == $val->id)? "selected":"") }}>{{$val->sphere_of_activity_name}}</li>
						@endforeach
					</ul>
				</div>
			</div>
			<div id="select-2-div">
				<x-label for="select-2" :value="__('Категория')" style="margin-top:20px" />
				<div class="select-div">
					<x-label for="select-2" :value="__('Категория')" />

					<input autocomplete="off" id="select-2" class="chosen-value" type="text" placeholder="{{$category_name}}">
					<ul class="value-list" id="value-list-2">
						@foreach($category as $val)
						@if ($val->sphere_id == $sphere_id)
						<li value="{{ $val->id}}" {{ ((isset($resume->profession->subsphere_id) && $resume->profession->subsphere_id == $val->id)? "selected":"") }}>{{$val->subsphere_of_activity_name}}</li>
						@endif
						@endforeach
					</ul>
				</div>
			</div>
			<div id="select-3-div">
				<x-label for="select-3" :value="__('Профессия')" style="margin-top:20px" />
				<div class="select-div">
					<x-label for="select-3" :value="__('Профессия')" />

					<input autocomplete="off" id="select-3" class="chosen-value" type="text" placeholder="{{ $resume->profession->profession_name }}">
					<ul class="value-list" id="value-list-3">
						@foreach($profession as $val)
						@if($val->subsphere_id == $resume->profession->subsphere_id)
						<li value="{{ $val->id}}" {{ ((isset($resume->profession_id) && $resume->profession_id == $val->id)? "selected":"") }}>{{$val->profession_name}}</li>
						@endif
						@endforeach
					</ul>
				</div>
			</div>
			<div style="margin-top:20px">
				<x-label for="normal-select-2" :value="__('Вид занятости')" />
				<select id="normal-select-2" name="type_of_employment" placeholder-text="{{$resume->type_of_employment->type_of_employment_name}}">
					@foreach($type_of_employment as $val)
					<option value="{{ $val->id}}" {{ ((isset($resume->type_of_employment) && $resume->type_of_employment->id== $val->id)? "selected":"") }}>{{$val->type_of_employment_name}}</option>
					@endforeach
				</select>
			</div>
			<div style="margin-top:20px">
				<x-label for="normal-select-3" :value="__('Тип работы')" />
				<select id="normal-select-3" name="work_type" placeholder-text="{{$resume->work_type->work_type_name}}">
					@foreach($work_type as $val)
					<option value="{{ $val->id}}" {{ ((isset($resume->work_type) && $resume->work_type->id== $val->id)? "selected":"") }}>{{$val->work_type_name}}</option>
					@endforeach
				</select>
			</div>
			<div style="margin-top:20px;">
				<x-label for="about_me" :value="__('Обо мне')" />
				<textarea id="about_me" type="text" name="about_me" style="resize:none;height:150px;" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{$resume->about_me}}</textarea>
			</div>
			<div class="flex items-center justify-end mt-4">
				<x-button class="ml-4">
					{{ __('Редактировать резюме') }}
				</x-button>
			</div>
		</form>
	</x-big-card>
</x-student-layout>

<script>
	/////
	////
	////
	let inputField1 = document.getElementById('select-1');
	let dropdown1 = document.getElementById("value-list-1");
	let dropdownArray1 = [...dropdown1.querySelectorAll('li')];

	//////


	inputField2 = document.getElementById('select-2');
	dropdown2 = document.getElementById("value-list-2");
	dropdownArray2 = [...dropdown2.querySelectorAll('li')];

	///////
	inputField3 = document.getElementById('select-3');
	dropdown3 = document.getElementById("value-list-3");
	dropdownArray3 = [...dropdown3.querySelectorAll('li')];
	///////


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

			//получаем категории для соответствующей сферы
			console.log('hiii - 1');
			let category = <?php echo json_encode($category); ?>;
			category = category.filter(val => {
				return val.sphere_id == item.value
			})
			let list = ``;
			for (let i = 0; i < category.length; i++) {
				list += `<li class="li-2" value="` + category[i].id + `" selected>` + category[i].subsphere_of_activity_name + `</li>\n`;
			}
			dropdown2.innerHTML = list;
			dropdownArray2 = [...dropdown2.querySelectorAll('li')];
			dropdownArray2.forEach(item => {
				item.addEventListener('click', (evt) => {
					inputField2.value = item.textContent;
					console.log('hiii - 2');
					//получаем профессии для соответствующей категории
					let profession = <?php echo json_encode($profession); ?>;
					profession = profession.filter(val => {
						return val.subsphere_id == item.value
					})
					let list = ``;
					for (let i = 0; i < profession.length; i++) {
						list += `<li class="li-3" value="` + profession[i].id + `" selected>` + profession[i].profession_name + `</li>\n`;
					}
					dropdown3.innerHTML = list;
					dropdownArray3 = [...dropdown3.querySelectorAll('li')];
					dropdownArray3.forEach(item => {
						item.addEventListener('click', (evt) => {
							inputField3.value = item.textContent;
							console.log('hiii - 3');

							document.getElementById('profession_id_input').value = item.value;

							dropdownArray3.forEach(dropdown3 => {
								dropdown3.classList.add('closed');
							});
						});
					})
					//
					dropdownArray2.forEach(dropdown2 => {
						dropdown2.classList.add('closed');
					});
				});
			})
			dropdownArray1.forEach(dropdown1 => {
				dropdown1.classList.add('closed');
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

	/////

	closeDropdown(dropdown2);

	inputField2.addEventListener('input', () => {
		let valueArray2 = [];
		dropdownArray2.forEach(item => {
			valueArray2.push(item.textContent);
		});
		dropdown2.classList.add('open');
		let inputValue = inputField2.value.toLowerCase();
		let valueSubstring;
		if (inputValue.length > 0) {
			for (let j = 0; j < valueArray2.length; j++) {
				if (!(inputValue.substring(0, inputValue.length) === valueArray2[j].substring(0, inputValue.length).toLowerCase())) {
					dropdownArray2[j].classList.add('closed');
				} else {
					dropdownArray2[j].classList.remove('closed');
				}
			}
		} else {
			for (let i = 0; i < dropdownArray2.length; i++) {
				dropdownArray2[i].classList.remove('closed');
			}
		}
	});
	dropdownArray2.forEach(item => {
		item.addEventListener('click', (evt) => {
			inputField2.value = item.textContent;
			console.log('hiii - 2');
			dropdownArray2.forEach(dropdown2 => {
				dropdown2.classList.add('closed');
			});
		});
	})

	inputField2.addEventListener('focus', () => {
		dropdown1.classList.remove('open');
		inputField2.placeholder = 'Поиск';
		dropdown2.classList.add('open');
		dropdownArray2.forEach(dropdown2 => {
			dropdown2.classList.remove('closed');
		});
	});

	inputField2.addEventListener('blur', () => {
		dropdown2.classList.remove('open');
	});

	////////

	closeDropdown(dropdown3);

	inputField3.addEventListener('input', () => {
		let valueArray3 = [];
		dropdownArray3.forEach(item => {
			valueArray3.push(item.textContent);
		});
		dropdown3.classList.add('open');
		let inputValue = inputField3.value.toLowerCase();
		let valueSubstring;
		if (inputValue.length > 0) {
			for (let j = 0; j < valueArray3.length; j++) {
				if (!(inputValue.substring(0, inputValue.length) === valueArray3[j].substring(0, inputValue.length).toLowerCase())) {
					dropdownArray3[j].classList.add('closed');
				} else {
					dropdownArray3[j].classList.remove('closed');
				}
			}
		} else {
			for (let i = 0; i < dropdownArray3.length; i++) {
				dropdownArray3[i].classList.remove('closed');
			}
		}
	});

	dropdownArray3.forEach(item => {
		item.addEventListener('click', (evt) => {
			inputField3.value = item.textContent;
			dropdownArray3.forEach(dropdown3 => {
				dropdown3.classList.add('closed');
			});
		});
	})

	inputField3.addEventListener('focus', () => {
		inputField3.placeholder = 'Поиск';
		dropdown3.classList.add('open');
		dropdownArray3.forEach(dropdown3 => {
			dropdown3.classList.remove('closed');
		});
	});

	inputField3.addEventListener('blur', () => {
		dropdown3.classList.remove('open');
	});
</script>
<style>
	::placeholder {
		color: black !important;
		opacity: 1;
	}

	:-ms-input-placeholder {
		color: black !important;
	}

	::-ms-input-placeholder {
		color: black !important;
	}
</style>