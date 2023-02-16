<!DOCTYPE html>
<html>

<head>
	<script src="/js/selector.js"></script>
	<script src="/js/multi-select.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
		<x-big-card>
			<form method="POST" action="{{ route('employer.edit-vacancy') }}">
				<input type="hidden" name="vacancy_id" value="{{$vacancy->id}}" />
				<h2 class="header-text text-center">Редактирование вакансии</h2>
				<x-errors class="mb-4 mt-3" :errors="$errors" />
				@csrf
				<div id="profession_id"></div>
				<div style="margin-top:20px">
					<x-label :value="__('Сфера деятельности')" />
					<input class="input noneditable-input" value="{{$sphere->sphere_of_activity_name}}" readonly />
				</div>
				<div>
					<x-label :value="__('Категория')" style="margin-top:20px" />
					<input class="input noneditable-input" value="{{$category->subsphere_of_activity_name}}" readonly />
				</div>
				<div>
					<x-label :value="__('Профессия')" style="margin-top:20px" />
					<input class="input noneditable-input" value="{{$profession->profession_name}}" readonly />
				</div>

				<div style="margin-top:20px;">
					<x-label for="work_experience" :value="__('Опыт работы')" />
					<x-input autocomplete="off" id="work_experience" type="number" name="work_experience" value="{{$vacancy->work_experience}}" />
					<div class="block mt-2">
						<label for="without_work_experience" class="inline-flex items-center">
							<input autocomplete="off" id="without_work_experience" type="checkbox" class="checkbox" @if($vacancy->work_experience == 0) checked @endif>
							<span class="ml-2 text-sm text-gray-600">{{ __('Без опыта') }}</span>
						</label>
					</div>
				</div>
				<div style="margin-top:20px;">
					<x-label for="salary" :value="__('Месячная зарплата')" />
					<x-input autocomplete="off" id="salary" type="number" name="salary" value="{{$vacancy->salary}}" />
					<div class="block mt-2">
						<label for="without_salary" class="inline-flex items-center">
							<input autocomplete="off" id="without_salary" type="checkbox" class="checkbox" name="remember" @if($vacancy->salary == 0) checked @endif>
							<span class="ml-2 text-sm text-gray-600">{{ __('Неоплачиваемая работа') }}</span>
						</label>
					</div>
				</div>
				<div>
					<x-label :value="__('Местоположение')" style="margin-top:20px" />
					<input class="input noneditable-input" value="{{$vacancy->location}}" readonly />
				</div>
				<div style="margin-top:20px">
					<x-label for="hard_skills" :value="__('Добавить навыки')" style="margin-top:20px" />
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
					<x-label for="soft_skills" :value="__('Добавить качества')" style="margin-top:20px" />
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
					<x-label :value="__('Вид занятости')" />
					<input class="input noneditable-input" value="{{$type_of_employment->type_of_employment_name}}" readonly />
				</div>
				<div style="margin-top:20px">
					<x-label :value="__('Тип работы')" />
					<input class="input noneditable-input" value="{{$work_type->work_type_name}}" readonly />
				</div>
				<div style="margin-top:20px;">
					<x-label for="contacts" :value="__('Контакты')" />
					<x-input autocomplete="off" id="contacts" class="input" type="email" name="contacts" value="{{$vacancy->contacts}}" />
				</div>
				<div style="margin-top:20px;width:var(--input-width);">
					<x-label for="description" :value="__('Описание вакансии')" />
					<textarea id="description" type="text" name="description">{{$vacancy->description}}</textarea>
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
						{{ __('Сохранить') }}
					</button>
				</div>
			</form>
		</x-big-card>
	</x-employer-layout>
</div>
<script>
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
		<x-input id="skill-name" type="text" style="width:400px" placeholder="${placeholder}"/>
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

	if (parseInt("{{$vacancy->salary}}") == 0) {
		$("#salary").hide();
		$("#salary").val(0);
	}
	if (parseInt("{{$vacancy->work_experience}}") == 0) {
		$("#work_experience").hide();
		$("#work_experience").val(0);
	}
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


	/** */

	.noneditable-input {
		border: solid 1px var(--border-color) !important;
		border-radius: 8px !important;
		padding-left: 12px;
	}

	.noneditable-input:active,
	.noneditable-input:focus {
		border: solid 1px var(--hover-border-color) !important;
		box-shadow: var(--tw-ring-inset) 0 0 0 3px var(--hover-box-shadow-color) !important;
		outline: none !important;
	}
</style>

</html>