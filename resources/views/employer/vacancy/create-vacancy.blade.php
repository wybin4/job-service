<!DOCTYPE html>
<html>

<head>
	<script src="/js/selector.js"></script>
	<script src="/js/multi-select.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
	<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
	<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>
	<script src="{{asset('/js/toast.js')}}"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div id="add-hard-skill-area"></div>
<div id="add-soft-skill-area"></div>
<div id="add-profession-area"></div>
<div id="blurable-content">
	<x-employer-layout>
		@if (session()->get('title'))
		<script>
			create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}');
		</script>
		@endif
		@if ($errors->any())
		@foreach ($errors->all() as $error)
		<script>
			create_notify('error', 'Ошибка добавления вакансии', '{{$error}}');
		</script>
		@endforeach
		@endif
		<x-big-card>
			<form method="POST" action="{{ route('employer.create-vacancy') }}">
				<h2 class="header-text text-center">Добавление вакансии</h2>
				@csrf
				<div id="profession_id"></div>
				<div>
					<x-label for="select-1" :value="__('Сфера деятельности')" style="margin-top:20px" />
					<div class="select-div">
						<input autocomplete="off" id="select-1" class="chosen-value" type="text" value="">
						<ul class="value-list" id="value-list-1">
							@foreach($sphere as $val) <li value="{{ $val->id}}" selected>{{ $val->sphere_of_activity_name}}</li>
							@endforeach
						</ul>
					</div>
				</div>

				<div id="select-2-div" style="display:none">
					<x-label for="select-2" :value="__('Категория')" style="margin-top:20px" />
					<div class="select-div">
						<input autocomplete="off" id="select-2" class="chosen-value" type="text" value="">
						<ul class="value-list" id="value-list-2">
							@foreach($category as $val) <li class="li-2" value="{{ $val->id}}" selected>{{ $val->subsphere_of_activity_name}}</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div id="select-3-div" style="display:none">
					<x-label for="select-3" :value="__('Профессия')" style="margin-top:20px" />
					<div class="select-div">
						<input autocomplete="off" id="select-3" class="chosen-value" type="text" value="">
						<ul class="value-list" id="value-list-3">
							@foreach($profession as $val) <li class="li-3" value="{{ $val->id}}" selected>{{ $val->profession_name}}</li>
							@endforeach
						</ul>
					</div>
					<div>
						<p id="add-profession" style="cursor:pointer;font-size:13px;" class="ml-500 text-indigo-700 dark:text-indigo-500">Не нашли подходящую профессию?</p>
					</div>
				</div>
				<div style="margin-top:20px;">
					<x-label for="work_experience" :value="__('Опыт работы')" />
					<x-input autocomplete="off" id="work_experience" type="number" name="work_experience" value="" />
					<div class="block mt-2">
						<label for="without_work_experience" class="inline-flex items-center">
							<input autocomplete="off" id="without_work_experience" type="checkbox" class="checkbox">
							<span class="ml-2 text-sm text-gray-600">{{ __('Без опыта') }}</span>
						</label>
					</div>
				</div>
				<div style="margin-top:20px;">
					<x-label for="salary" :value="__('Месячная зарплата')" />
					<x-input autocomplete="off" id="salary" type="number" name="salary" value="" />
					<div class="block mt-2">
						<label for="without_salary" class="inline-flex items-center">
							<input autocomplete="off" id="without_salary" type="checkbox" class="checkbox" name="remember">
							<span class="ml-2 text-sm text-gray-600">{{ __('Неоплачиваемая работа') }}</span>
						</label>
					</div>
				</div>
				<div id="select-4-div">
					<x-label for="select-4" :value="__('Местоположение')" style="margin-top:20px" />
					<div class="select-div">
						<input autocomplete="off" id="select-4" class="chosen-value" type="text" name="location" value="{{Auth::guard('employer')->user()->location}}">
						<ul class="value-list" id="value-list-4">
						</ul>
					</div>
				</div>
				<div style="margin-top:20px">
					<x-label for="hard_skills" :value="__('Навыки')" style="margin-top:20px" />
					<select class="input" name="hard_skills[]" id="hard_skills" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
						@foreach($skill as $val)
						@if($val->skill_type == 1)
						<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
						@endif
						@endforeach
					</select>
					<div>
						<p id="add-hard-skill" style="cursor:pointer;font-size:13px;" class="ml-500 text-indigo-700 dark:text-indigo-500">Не нашли подходящий навык?</p>
					</div>
				</div>
				<div style="margin-top:20px">
					<x-label for="soft_skills" :value="__('Качества')" style="margin-top:20px" />
					<select style="width:580px" name="soft_skills[]" id="soft_skills" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
						@foreach($skill as $val)
						@if($val->skill_type == 0)
						<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
						@endif
						@endforeach
					</select>
					<div>
						<p id="add-soft-skill" style="cursor:pointer;font-size:13px;" class="ml-500 text-indigo-700 dark:text-indigo-500">Не нашли подходящее качество?</p>
					</div>
				</div>
				<div style="margin-top:20px">
					<x-label for="normal-select-2" :value="__('Вид занятости')" />
					<select id="normal-select-2" class="single-selector" name="type_of_employment" placeholder-text="ㅤ">
						@foreach($type_of_employment as $val)
						<option value="{{ $val->id}}" class="select-dropdown__list-item">{{ $val->type_of_employment_name}}</option>
						@endforeach
					</select>
				</div>
				<div style="margin-top:20px">
					<x-label for="normal-select-3" :value="__('Тип работы')" />
					<select id="normal-select-3" class="single-selector" name="work_type" placeholder-text="ㅤ">
						@foreach($work_type as $val)
						<option value="{{ $val->id}}" class="select-dropdown__list-item">{{ $val->work_type_name}}</option>
						@endforeach
					</select>
				</div>
				<div style="margin-top:20px;">
					<x-label for="contacts" :value="__('Контакты')" />
					<x-input autocomplete="off" id="contacts" class="input" type="email" name="contacts" value="{{Auth::guard('employer')->user()->email}}" />
				</div>
				<div style="margin-top:20px;width:var(--input-width);">
					<x-label for="description" :value="__('Описание вакансии')" />
					<textarea id="description" type="text" name="description"></textarea>
					<script>
						const easyMDE = new EasyMDE({
							element: document.getElementById('description'),
							autoDownloadFontAwesome: false,
							toolbar: [{
									name: "preview",
									action: EasyMDE.togglePreview,
									className: "fa fa-eye no-disable",
									title: 'Превью'
								},
								"|",
								{
									name: "bold",
									action: EasyMDE.toggleBold,
									className: "fa fa-bold",
									title: "Жирный",
								},
								{
									name: "italic",
									action: EasyMDE.toggleItalic,
									className: "fa fa-italic",
									title: "Курсив",
								}, {
									name: "heading",
									action: EasyMDE.toggleHeading2,
									className: "fa fa-header",
									title: "Заголовок",
								},
								'|',
								{
									name: "ordered-list",
									action: EasyMDE.toggleOrderedList,
									className: "fa fa-list-ol",
									title: "Маркированный список",
								}, {
									name: "unordered-list",
									action: EasyMDE.toggleUnorderedList,
									className: "fa fa-list-ul",
									title: "Неупорядоченный список",
								},
								'|',
								{
									name: "link",
									action: EasyMDE.drawLink,
									className: "fa fa-link",
									title: "Ссылка",
								},
								'|',
								{
									name: "undo",
									action: EasyMDE.undo,
									className: "fa fa-undo",
									title: "Назад",
								},
								{
									name: "redo",
									action: EasyMDE.redo,
									className: "fa fa-redo",
									title: "Вперёд",
								},
							],
							placeholder: 'Введите описание',
							maxHeight: '400px'
						});
					</script>
				</div>
				<div class="flex items-center justify-end mt-4">

					<button class="ml-4 button">
						{{ __('Добавить') }}
					</button>
				</div>
			</form>
		</x-big-card>
	</x-employer-layout>
</div>
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
	//////

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
			$('#select-2-div').show();
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
					$('#select-3-div').show();
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

							const profession_id = `<input type="hidden" name="profession_id" value="${item.value}" />`;
							document.getElementById('profession_id').innerHTML += profession_id;


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

	/*dropdownArray2.forEach(item => {
		item.addEventListener('click', (evt) => {
			inputField2.value = item.textContent;
			dropdownArray2.forEach(dropdown2 => {
				dropdown2.classList.add('closed');
			});
		});
	})*/

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

	////////

	let requestUrl = 'https://raw.githubusercontent.com/pensnarik/russian-cities/master/russian-cities.json';
	let xhr = new XMLHttpRequest();

	xhr.open('GET', requestUrl, true);
	xhr.responseType = 'json';
	xhr.send()

	xhr.onload = function() {
		let cities = xhr.response;
		let list = '';
		for (let i = 0; i < cities.length; i++) {
			list += '<li class="li-4" value="' + i + 1 + '">' + cities[i].name + ", " + cities[i].subject + '</li>\n';
		}
		$("#value-list-4").html(list);
		//// dummy code for selector-4

		let inputField4 = document.getElementById('select-4');
		let dropdown4 = document.getElementById("value-list-4");
		let dropdownArray4 = [...dropdown4.querySelectorAll('li')];

		function closeDropdown(dropdown) {
			dropdown.classList.remove('open');
		}
		closeDropdown(dropdown4);

		inputField4.addEventListener('input', () => {
			let valueArray4 = [];
			dropdownArray4.forEach(item => {
				valueArray4.push(item.textContent);
			});
			dropdown4.classList.add('open');
			let inputValue = inputField4.value.toLowerCase();
			let valueSubstring;
			if (inputValue.length > 0) {
				for (let j = 0; j < valueArray4.length; j++) {
					if (!(inputValue.substring(0, inputValue.length) === valueArray4[j].substring(0, inputValue.length).toLowerCase())) {
						dropdownArray4[j].classList.add('closed');
					} else {
						dropdownArray4[j].classList.remove('closed');
					}
				}
			} else {
				for (let i = 0; i < dropdownArray4.length; i++) {
					dropdownArray4[i].classList.remove('closed');
				}
			}
		});
		dropdownArray4.forEach(item => {
			item.addEventListener('click', (evt) => {
				inputField4.value = item.textContent;
				dropdownArray4.forEach(dropdown4 => {
					dropdown4.classList.add('closed');
				});
			});
		})

		inputField4.addEventListener('focus', () => {
			dropdown4.classList.remove('open');
			inputField4.placeholder = 'Поиск';
			dropdown4.classList.add('open');
			dropdownArray4.forEach(dropdown4 => {
				dropdown4.classList.remove('closed');
			});
		});

		inputField4.addEventListener('blur', () => {
			dropdown4.classList.remove('open');
		});
	}

	/////
	////

	function create_popup(status, placeholder) {
		const name = status == "навык" ? status + "a" : status == "навык" ? status.substring(0, status.length - 1) + "a" : status.substring(0, status.length - 1) + "и";
		const popup = `<div class="modal" id="skill-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="text-2xl font-bold text-center">Добавить ${status}</h2>
      </div>
      <div class="modal-body">
        <x-label :value="__('Название ${name}')" />
		<x-input id="skill-name" type="text" style="width:400px" placeholder="${placeholder}" autocomplete="off"/>
      </div>
      <div class="modal-footer">
        <span type="button" class="span-like-button" id="btn-close-skill" data-bs-dismiss="modal">Отменить</span>
        <span type="button" class="span-like-button" id="btn-add-skill">Добавить</span>
      </div>
    </div>
  </div>
</div>`;
		return popup;
	}

	function show_popup() {
		$('#skill-modal').show();
		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$('#btn-close-skill').click(function() {
			$('#skill-modal').remove();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur")
		})
	}
	$('#add-hard-skill').click(function() {
		let popup = create_popup("навык", "Например: JavaFX, Node.js");
		$('#add-hard-skill-area').append(popup);
		show_popup();
		$('#btn-add-skill').click(function() {
			const skill_name = $('#skill-name').val();
			let all_hard_skills = <?php echo json_encode($skill); ?>;
			all_hard_skills = all_hard_skills.filter(val => {
				return val.skill_type === 1
			})
			all_hard_skills = all_hard_skills.filter(val => {
				return val.skill_name.toLowerCase() == String(skill_name).toLowerCase()
			})
			if (all_hard_skills.length == 0 && skill_name != "") {
				// добавляем скилл
				$.ajax({
					url: '{{ route("employer.add-skill") }}',
					type: "POST",
					data: {
						'skill_name': skill_name,
						'skill_type': 1
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Добавили навык!")
					},
					error: function(msg) {
						console.log("Не получилось добавить навык")
					}
				});
				// перезагружаем страницу, чтобы применить изменения
				location.reload();
			} else if (all_hard_skills.length !== 0) {
				create_notify('error', 'Ошибка добавления навыка', 'Такой навык уже существует', 30);
			} else if (skill_name == "") {
				create_notify('error', 'Ошибка добавления навыка', 'Поле не может быть пустым', 30);
			}
		})
	})

	$('#add-soft-skill').click(function() {
		let popup = create_popup("качество", "Например: дар убеждения, креативность");
		$('#add-soft-skill-area').append(popup);
		show_popup();
		$('#btn-add-skill').click(function() {
			const skill_name = $('#skill-name').val();
			let all_soft_skills = <?php echo json_encode($skill); ?>;
			all_soft_skills = all_soft_skills.filter(val => {
				return val.skill_type === 0
			})
			all_soft_skills = all_soft_skills.filter(val => {
				return val.skill_name.toLowerCase() == String(skill_name).toLowerCase()
			})
			if (all_soft_skills.length == 0 && skill_name != "") {
				// добавляем скилл
				$.ajax({
					url: '{{ route("employer.add-skill") }}',
					type: "POST",
					data: {
						'skill_name': skill_name,
						'skill_type': 0
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Добавили качество!")
					},
					error: function(msg) {
						console.log("Не получилось добавить качество")
					}
				});
				// перезагружаем страницу, чтобы применить изменения
				location.reload();
			} else if (all_soft_skills.length !== 0) {
				create_notify('error', 'Ошибка добавления качества', 'Такое качество уже существует', 30);
			} else if (skill_name == "") {
				create_notify('error', 'Ошибка добавления качества', 'Поле не может быть пустым', 30);
			}
		})
	})

	$('#add-profession').click(function() {
		let popup = create_popup("профессию", "Например: UX/UI-дизайнер, Разработчик на Node.js");
		$('#add-profession-area').append(popup);
		show_popup();
		$('#btn-add-skill').click(function() {
			const profession_name = $('#skill-name').val();
			let category = <?php echo json_encode($category); ?>;
			category = category.filter(val => {
				return val.subsphere_of_activity_name == $('#select-2').val()
			})
			let all_professions = <?php echo json_encode($profession); ?>;
			all_professions = all_professions.filter(val => {
				return val.profession_name.toLowerCase() == String(profession_name).toLowerCase()
			})
			if (all_professions.length == 0 && profession_name != "") {
				// добавляем скилл
				$.ajax({
					url: '{{ route("employer.add-profession") }}',
					type: "POST",
					data: {
						'profession_name': profession_name,
						'subsphere_id': category[0].id
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						console.log("Добавили профессию!")
					},
					error: function(msg) {
						console.log("Не получилось добавить профессию")
					}
				});
				// перезагружаем страницу, чтобы применить изменения
				location.reload();
			} else if (all_professions.length !== 0) {
				create_notify('error', 'Ошибка добавления профессии', 'Такая профессия уже существует', 30);
			} else if (profession_name == "") {
				create_notify('error', 'Ошибка добавления профессии', 'Поле не может быть пустым', 30);
			}
		})
	})
	$('#without_salary').click(function() {
		if ($('#without_salary').is(':checked')) {
			$("#salary").hide();
			$("#salary").val(0);
		} else {
			$("#salary").show();
			$("#salary").val();
		}
	})
	$('#without_work_experience').click(function() {
		if ($('#without_work_experience').is(':checked')) {
			$("#work_experience").hide();
			$("#work_experience").val(0);
		} else {
			$("#work_experience").show();
			$("#work_experience").val();
		}
	})
</script>
<style>
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	.form-check-input,
	.label::after,
	.form-check-input::after {
		border: 1px solid #D1D5DB;
	}

	.form-check-input:checked {
		background-color: rgb(129 140 248) !important;
		border: solid 1px rgb(129 140 248);
	}

	.form-check-label {
		padding-top: 3px;
	}

	.form-check-input:focus {
		color: black;
		outline: 0;
		border: 1px solid #D1D5DB !important;
		box-shadow: none !important
	}
</style>

</html>