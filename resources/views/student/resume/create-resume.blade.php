<!DOCTYPE html>
<html>

<head>
	<script src="/js/multi-select.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
	<x-student-layout>
		@if (session()->get('title'))
		<script>
			create_notify('success', '{{session()->get("title")}}', '{{session()->get("text")}}');
		</script>
		@endif
		<div class="tabs-div">
			<div class="tab" id="add-resume"><i class="fa-solid fa-file-circle-plus"></i><span style="padding-left:10px">Добавление резюме</span></div>
			<div class="tab" id="rate-skills"><i class="fa-regular fa-star"></i><span style="padding-left:10px">Оценка навыков</span></div>
		</div>
		<x-big-card>
			<x-errors class="mb-4" :errors="$errors" />
			<form method="POST" action="{{ route('student.create-resume') }}">
				<div id="rating-card" style="display:none">
					<h2 class="medium-text text-center">Оценка навыков</h2>
					<p class="block font-medium text-sm text-muted" style="margin-top:5px">Оцените ваши навыки, чтобы сделать резюме более релевантным</p>
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
					<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
					<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/jquery.barrating.min.js"></script>
					<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/themes/fontawesome-stars.min.css" rel="stylesheet" />

					<div id="rate-area"></div>
					<div class="d-flex justify-content-center" style="margin-top:20px;">
						<input name="newsletter_subscription" id="newsletter_subscription" type="checkbox" class="checkbox" />
						<x-label for="newsletter_subscription" class="ml-2">Подписаться на уведомления о новых вакансиях</x-label>
					</div>
					<div class="d-flex justify-content-center" style="margin-top:20px;">
						<button class="ml-4 button">
							{{ __('Добавить резюме') }}
						</button>
					</div>
					<style>
						.br-theme-fontawesome-stars .br-widget a::after {
							color: var(--dot-color) !important;
						}

						.br-theme-fontawesome-stars .br-widget a.br-active:after {
							color: var(--link-hover-color) !important;
						}

						.br-theme-fontawesome-stars .br-widget a.br-selected:after {
							color: var(--link-hover-color) !important;
						}
					</style>
				</div>
				<div id="resume-card">
					<h2 class="medium-text pb-2 text-center">Добавление резюме</h2>

					@csrf
					<div id="profession_id" style="margin-top:20px"></div>
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

					<div id="select-2-div" style="display:none;margin-top:20px">
						<div class="select-div">
							<x-label for="select-2" :value="__('Категория')" />

							<input autocomplete="off" id="select-2" class="chosen-value" type="text" value="">
							<ul class="value-list" id="value-list-2">
								@foreach($category as $val) <li class="li-2" value="{{ $val->id}}" selected>{{ $val->subsphere_of_activity_name}}</li>
								@endforeach
							</ul>
						</div>
					</div>
					<div id="select-3-div" style="display:none;margin-top:20px">
						<div class="select-div">
							<x-label for="select-3" :value="__('Профессия')" />

							<input autocomplete="off" id="select-3" class="chosen-value" type="text" value="">
							<ul class="value-list" id="value-list-3">
								@foreach($profession as $val) <li class="li-3" value="{{ $val->id}}" selected>{{ $val->profession_name}}</li>
								@endforeach
							</ul>
						</div>
						<div>
							<p id="add-profession" style="cursor:pointer;font-size:13px;" class="selected-text">Не нашли подходящую профессию?</p>
						</div>
					</div>
					<div style="margin-top:20px">
						<x-label for="hard_skills" :value="__('Навыки')" style="margin-top:20px" />
						<select name="skills[]" id="hard_skills" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
							@foreach($skill as $val)
							@if($val->skill_type == 1)
							<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
							@endif
							@endforeach
						</select>
						<div>
							<p id="add-hard-skill" style="cursor:pointer;font-size:13px;" class="selected-text">Не нашли подходящий навык?</p>
						</div>
					</div>
					<div style="margin-top:20px">
						<x-label for="soft_skills" :value="__('Качества')" style="margin-top:20px" />
						<select class="input" name="skills[]" id="soft_skills" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
							@foreach($skill as $val)
							@if($val->skill_type == 0)
							<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
							@endif
							@endforeach
						</select>
						<div>
							<p id="add-soft-skill" style="cursor:pointer;font-size:13px;" class="selected-text">Не нашли подходящее качество?</p>
						</div>
					</div>
					<div style="margin-top:20px">
						<x-label for="normal-select-2" :value="__('Вид занятости')" />
						<select id="normal-select-2" class="normal-single-selector" name="type_of_employment" placeholder-text="ㅤ">
							@foreach($type_of_employment as $val)
							<option value="{{ $val->id}}" class="select-dropdown__list-item">{{ $val->type_of_employment_name}}</option>
							@endforeach
						</select>
					</div>
					<div style="margin-top:20px">
						<x-label for="normal-select-3" :value="__('Тип работы')" />
						<select id="normal-select-3" class="normal-single-selector" name="work_type" placeholder-text="ㅤ">
							@foreach($work_type as $val)
							<option value="{{ $val->id}}" class="select-dropdown__list-item">{{ $val->work_type_name}}</option>
							@endforeach
						</select>
					</div>
					<div id="work-exp-div" class="card-div">
						<x-label for="btn-add-work-exp">Опыт работы</x-label>
						<p id="btn-add-work-exp" style="margin-top:10px;cursor:pointer;"><i style="margin-top:10px;" class="fa-solid fa-briefcase"></i><span style="padding-left:10px">Добавить сведения об опыте работы</span></p>
						<div id="area-add-work-exp" style="display:none">
							<div style="margin-top:20px">
								<x-label :value="__('Компания/организация')" />
								<x-input autocomplete="off" id="add_company_name" type="text" autofocus class="card-input" />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Местоположение')" />
								<x-input autocomplete="off" id="add_company_location" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Должность')" />
								<x-input autocomplete="off" id="add_work_title" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label>Начало<span style="padding-left:240px">Окончание</span></x-label>
								<div id="add-work-time-area"></div>
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Подробности')" />
								<textarea id="add_work_description" type="text"></textarea>
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="save-work-exp">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-work-exp">
									Отменить
								</span>
							</div>
						</div>
						<!---->
						<div id="area-edit-work-exp" style="display:none">
							<input autocomplete="off" type="hidden" id="id-work-edit" />
							<div style="margin-top:20px">
								<x-label :value="__('Компания/организация')" />
								<x-input autocomplete="off" id="edit_company_name" type="text" autofocus class="card-input" />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Местоположение')" />
								<x-input autocomplete="off" id="edit_company_location" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Должность')" />
								<x-input autocomplete="off" id="edit_work_title" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label>Начало<span style="padding-left:240px">Окончание</span></x-label>
								<div id="edit-work-time-area"></div>
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Подробности')" />
								<textarea id="edit_work_description" type="text"></textarea>
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="edit-work-exp">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-editable-work-exp">
									Отменить
								</span>
							</div>
						</div>
					</div>
					<!---->
					<div id="edu-div" class="card-div">
						<x-label for="btn-add-edu">Образование</x-label>
						<p id="btn-add-edu" style="margin-top:10px;cursor:pointer;"><i style="margin-top:10px" class="fa-solid fa-graduation-cap"></i><span style="padding-left:10px">Добавить сведения об образовании</span></p>
						<div id="area-add-edu" style="display:none">
							<div style="margin-top:20px">
								<x-label :value="__('Название образовательной организации')" />
								<x-input autocomplete="off" id="add_university_name" type="text" class="card-input" autofocus />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Местоположение')" />
								<x-input autocomplete="off" id="add_edu_location" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Специальность')" />
								<x-input autocomplete="off" id="add_speciality_name" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label>Начало<span style="padding-left:240px">Окончание</span></x-label>
								<div id="add-edu-time-area"></div>
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Подробности')" />
								<textarea id="add_edu_description" type="text" autofocus></textarea>
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="save-edu">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-edu">
									Отменить
								</span>
							</div>
						</div>
						<div id="area-edit-edu" style="display:none">
							<input type="hidden" id="id-edu-edit" />
							<div style="margin-top:20px">
								<x-label :value="__('Название образовательной организации')" />
								<x-input autocomplete="off" id="edit_university_name" type="text" class="card-input" autofocus />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Местоположение')" />
								<x-input autocomplete="off" id="edit_edu_location" class="block mt-1 w-full" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Специальность')" />
								<x-input autocomplete="off" id="edit_speciality_name" class="block mt-1 w-full" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<x-label>Начало<span style="padding-left:240px">Окончание</span></x-label>
								<div id="edit-edu-time-area"></div>
							</div>
							<div style="margin-top:20px">
								<x-label :value="__('Подробности')" />
								<textarea id="edit_edu_description" type="text" autofocus></textarea>
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="edit-edu">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-editable-edu">
									Отменить
								</span>
							</div>
						</div>
					</div>
					<!---->
					<div id="course-div" class="card-div">
						<x-label for="btn-add-course">Курсы</x-label>
						<p id="btn-add-course" style="margin-top:10px;cursor:pointer;"><i style="margin-top:10px" class="fa-solid fa-chalkboard-user"></i><span style="padding-left:10px">Добавить сведения о пройденных курсах</span></p>
						<div id="area-add-course" style="display:none">
							<div style="margin-top:20px">
								<x-label :value="__('Название платформы')" />
								<x-input autocomplete="off" id="add_platform_name" type="text" class="card-input" autofocus />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Название курса')" />
								<x-input autocomplete="off" id="add_course_name" class="block mt-1 w-full" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="save-course">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-course">
									Отменить
								</span>
							</div>
						</div>
						<div id="area-edit-course" style="display:none">
							<input type="hidden" id="id-course-edit" />
							<div style="margin-top:20px">
								<x-label :value="__('Название платформы')" />
								<x-input autocomplete="off" id="edit_platform_name" type="text" class="card-input" autofocus />
							</div style="margin-top:20px">
							<div style="margin-top:20px">
								<x-label :value="__('Название курса')" />
								<x-input autocomplete="off" id="edit_course_name" class="block mt-1 w-full" class="card-input" type="text" autofocus />
							</div>
							<div style="margin-top:20px">
								<span class="span-like-button" id="edit-course">
									Сохранить
								</span>
								<span class="span-like-button ml-4" id="delete-editable-course">
									Отменить
								</span>
							</div>
						</div>
					</div>
					<div style="margin-top:20px;width:var(--input-width);">
						<x-label for="about_me" :value="__('Обо мне')" />
						<textarea id="about_me" type="text" name="about_me"></textarea>
						<script>
							const easyMDE = new EasyMDE({
								element: document.getElementById('about_me'),
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
						<span class="span-like-button ml-4" id="next-page">
							Далее
						</span>
					</div>
				</div>
			</form>
		</x-big-card>
	</x-student-layout>
</div>
<script>
	function open_rate_area() {
		$('#rate-area').empty();
		const hard_skills = $('#hard_skills').val();
		const soft_skills = $('#soft_skills').val();
		let skills = [];
		if (hard_skills && hard_skills.length) {
			skills.push(...hard_skills)
		}
		if (soft_skills && soft_skills.length) {
			skills.push(...soft_skills)
		}
		if (skills.length && $('#select-1').val() && $('#select-2').val() && $('#select-3').val()) {
			let all_skills = <?php echo json_encode($skill); ?>;
			all_skills = all_skills.filter(val => {
				return skills.includes(String(val.id))
			})
			for (let i = 0; i < skills.length; i++) {
				const id = `rating-${skills[i]}`;
				$('#rate-area').append(`
						<div class="rate-div">
						<x-label for="${id}">${all_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
						</div>\n
						`);
				$(function() {
					$('#' + id).barrating({
						theme: 'fontawesome-stars'
					});
				});
			}
			$('#resume-card').hide();
			$('#rating-card').show();
			$('#rate-skills').css('background-color', 'var(--active-tab)');
			$('#rate-skills').css('padding', '10px 10px 10px 5px');
			$('#rate-skills').css('border-radius', '8px');
			$('#add-resume').css('background-color', 'transparent');
			$('#add-resume').css('padding', '0px');
			$('#add-resume').css('border-radius', '0px');
		} else if (!$('#select-1').val()) { //если не указана сфера деят
			$('html,body').scrollTop(0);
			create_notify('error', 'Ошибка добавления резюме', 'Добавьте сферу деятельности');
		} else if (!$('#select-2').val()) { //если не указана категория
			$('html,body').scrollTop(0);
			create_notify('error', 'Ошибка добавления резюме', 'Добавьте категорию');
		} else if (!$('#select-3').val()) { //если не указана профессия
			$('html,body').scrollTop(0);
			create_notify('error', 'Ошибка добавления резюме', 'Добавьте профессию');
		} else if (!skills.length) {
			$('html,body').scrollTop(0);
			create_notify('error', 'Ошибка добавления резюме', 'Добавьте навыки');

		}
	}
	$('#next-page').on('click', open_rate_area);

	////

	////
	function createSingleSelect() {
		var select = document.getElementsByClassName('normal-single-selector'),
			liElement,
			ulElement,
			optionValue,
			iElement,
			optionText,
			selectDropdown,
			elementParentSpan;

		for (var select_i = 0, len = select.length; select_i < len; select_i++) {
			//console.log('selects init');

			select[select_i].style.display = 'none';
			wrapElement(document.getElementById(select[select_i].id), document.createElement('div'), select_i, select[select_i].getAttribute('placeholder-text'));

			for (var i = 0; i < select[select_i].options.length; i++) {
				liElement = document.createElement("li");
				optionValue = select[select_i].options[i].value;
				optionText = document.createTextNode(select[select_i].options[i].text);
				liElement.className = 'select-dropdown__list-item';
				liElement.setAttribute('data-value', optionValue);
				liElement.appendChild(optionText);
				ulElement.appendChild(liElement);

				liElement.addEventListener('click', function() {
					displyUl(this);
				}, false);
			}
		}

		function wrapElement(el, wrapper, i, placeholder) {
			el.parentNode.insertBefore(wrapper, el);
			wrapper.appendChild(el);

			document.addEventListener('click', function(e) {
				let clickInside = wrapper.contains(e.target);
				if (!clickInside) {
					let menu = wrapper.getElementsByClassName('select-dropdown__list');
					menu[0].classList.remove('active');
				}
			});

			var buttonElement = document.createElement("button"),
				spanElement = document.createElement("span"),
				spanText = document.createTextNode(placeholder);
			iElement = document.createElement("i");
			ulElement = document.createElement("ul");

			wrapper.className = 'select-dropdown select-dropdown--' + i;
			buttonElement.className = 'select-dropdown__button select-dropdown__button--' + i;
			buttonElement.setAttribute('data-value', '');
			buttonElement.setAttribute('type', 'button');
			spanElement.className = 'select-dropdown select-dropdown--' + i;
			iElement.className = 'zmdi zmdi-chevron-down';
			ulElement.className = 'select-dropdown__list select-dropdown__list--' + i;
			ulElement.id = 'select-dropdown__list-' + i;

			wrapper.appendChild(buttonElement);
			spanElement.appendChild(spanText);
			buttonElement.appendChild(spanElement);
			buttonElement.appendChild(iElement);
			wrapper.appendChild(ulElement);
		}

		function displyUl(element) {

			if (element.tagName == 'BUTTON') {
				selectDropdown = element.parentNode.getElementsByTagName('ul');
				for (var i = 0, len = selectDropdown.length; i < len; i++) {
					selectDropdown[i].classList.toggle("active");
				}
			} else if (element.tagName == 'LI') {
				var selectId = element.parentNode.parentNode.getElementsByTagName('select')[0];
				selectElement(selectId.id, element.getAttribute('data-value'));
				elementParentSpan = element.parentNode.parentNode.getElementsByTagName('span');
				element.parentNode.classList.toggle("active");
				elementParentSpan[0].textContent = element.textContent;
				elementParentSpan[0].parentNode.setAttribute('data-value', element.getAttribute('data-value'));
			}

		}

		function selectElement(id, valueToSelect) {
			var element = document.getElementById(id);
			element.value = valueToSelect;
			element.setAttribute('selected', 'selected');
		}
		var buttonSelect = document.getElementsByClassName('select-dropdown__button');
		for (var i = 0, len = buttonSelect.length; i < len; i++) {
			buttonSelect[i].addEventListener('click', function(e) {
				e.preventDefault();
				displyUl(this);
			}, false);
		}
	}
	createSingleSelect();

	function createSelect() {
		var select = document.getElementsByClassName('single-selector'),
			liElement,
			ulElement,
			optionValue,
			iElement,
			optionText,
			selectDropdown,
			elementParentSpan;

		for (var select_i = 0, len = select.length; select_i < len; select_i++) {
			//console.log('selects init');

			select[select_i].style.display = 'none';
			wrapElement(document.getElementById(select[select_i].id), document.createElement('div'), select_i, select[select_i].getAttribute('placeholder-text'));

			for (var i = 0; i < select[select_i].options.length; i++) {
				liElement = document.createElement("li");
				optionValue = select[select_i].options[i].value;
				optionText = document.createTextNode(select[select_i].options[i].text);
				liElement.className = 'select-dropdown__list-item';
				liElement.setAttribute('data-value', optionValue);
				liElement.appendChild(optionText);
				ulElement.appendChild(liElement);

				liElement.addEventListener('click', function() {
					displyUl(this);
				}, false);
			}
		}

		function wrapElement(el, wrapper, i, placeholder) {
			el.parentNode.insertBefore(wrapper, el);
			wrapper.appendChild(el);

			document.addEventListener('click', function(e) {
				let clickInside = wrapper.contains(e.target);
				if (!clickInside) {
					let menu = wrapper.getElementsByClassName('select-dropdown__list');
					menu[0].classList.remove('active');
				}
			});

			var buttonElement = document.createElement("button"),
				spanElement = document.createElement("span"),
				spanText = document.createTextNode(placeholder);
			iElement = document.createElement("i");
			ulElement = document.createElement("ul");

			wrapper.className = 'select-dropdown select-dropdown--' + i;
			buttonElement.className = 'select-dropdown__button select-dropdown__button--' + i;
			buttonElement.setAttribute('data-value', '');
			buttonElement.setAttribute('type', 'button');
			spanElement.className = 'select-dropdown select-dropdown--' + i;
			iElement.className = 'zmdi zmdi-chevron-down';
			ulElement.className = 'select-dropdown__list select-dropdown__list--' + i;
			ulElement.id = 'select-dropdown__list-' + i;

			wrapper.appendChild(buttonElement);
			spanElement.appendChild(spanText);
			buttonElement.appendChild(spanElement);
			buttonElement.appendChild(iElement);
			wrapper.appendChild(ulElement);
		}

		function displyUl(element) {

			if (element.tagName == 'BUTTON') {
				selectDropdown = element.parentNode.getElementsByTagName('ul');
				for (var i = 0, len = selectDropdown.length; i < len; i++) {
					selectDropdown[i].classList.toggle("active");
				}
			} else if (element.tagName == 'LI') {
				var selectId = element.parentNode.parentNode.getElementsByTagName('select')[0];
				selectElement(selectId.id, element.getAttribute('data-value'));
				elementParentSpan = element.parentNode.parentNode.getElementsByTagName('span');
				element.parentNode.classList.toggle("active");
				elementParentSpan[0].textContent = element.textContent;
				elementParentSpan[0].parentNode.setAttribute('data-value', element.getAttribute('data-value'));
			}

		}

		function selectElement(id, valueToSelect) {
			var element = document.getElementById(id);
			element.value = valueToSelect;
			element.setAttribute('selected', 'selected');
		}
		var buttonSelect = document.getElementsByClassName('select-dropdown__button');
		for (var i = 0, len = buttonSelect.length; i < len; i++) {
			buttonSelect[i].addEventListener('click', function(e) {
				e.preventDefault();
				displyUl(this);
			}, false);
		}
	}

	function edit_smth() {
		$("#add-work-time-area").empty();
		$("#edit-work-time-area").empty();
		$("#add-edu-time-area").empty();
		$("#edit-edu-time-area").empty();

		$("#add-work-time-area").append('<select id="add-normal-select-4" class="single-selector" placeholder-text="Месяц">');
		$("#add-work-time-area").append('<select id="add-normal-select-5" class="single-selector" placeholder-text="Год">');
		$("#add-work-time-area").append('<select id="add-normal-select-6" class="single-selector" placeholder-text="Месяц">');
		$("#add-work-time-area").append('<select id="add-normal-select-7" class="single-selector" placeholder-text="Год">');

		$("#edit-work-time-area").append('<select id="edit-normal-select-4" class="single-selector" placeholder-text="Месяц">');
		$("#edit-work-time-area").append('<select id="edit-normal-select-5" class="single-selector" placeholder-text="Год">');
		$("#edit-work-time-area").append('<select id="edit-normal-select-6" class="single-selector" placeholder-text="Месяц">');
		$("#edit-work-time-area").append('<select id="edit-normal-select-7" class="single-selector" placeholder-text="Год">');

		$("#add-edu-time-area").append('<select id="add-normal-select-8" class="single-selector" placeholder-text="Месяц">');
		$("#add-edu-time-area").append('<select id="add-normal-select-9" class="single-selector" placeholder-text="Год">');
		$("#add-edu-time-area").append('<select id="add-normal-select-10" class="single-selector" placeholder-text="Месяц">');
		$("#add-edu-time-area").append('<select id="add-normal-select-11" class="single-selector" placeholder-text="Год">');

		$("#edit-edu-time-area").append('<select id="edit-normal-select-8" class="single-selector" placeholder-text="Месяц">');
		$("#edit-edu-time-area").append('<select id="edit-normal-select-9" class="single-selector" placeholder-text="Год">');
		$("#edit-edu-time-area").append('<select id="edit-normal-select-10" class="single-selector" placeholder-text="Месяц">');
		$("#edit-edu-time-area").append('<select id="edit-normal-select-11" class="single-selector" placeholder-text="Год">');

		for (let i = 5; i < 12; i += 2) {
			add_year('#add-normal-select-' + i);
			add_year('#edit-normal-select-' + i);
		}
		for (let i = 4; i < 11; i += 2) {
			add_month('#add-normal-select-' + i);
			add_month('#edit-normal-select-' + i);
		}
	}
	$('#btn-add-work-exp').click(function() {
		edit_smth();
		$('#btn-add-work-exp').hide();
		$('#area-add-work-exp').show();
		createSelect();
	});

	function reset_work_values(status) {
		$('#' + status + '_company_name').val("");
		$('#' + status + '_company_location').val("");
		$('#' + status + '_work_title').val("");
		$('#' + status + '-normal-select-4').val("00");
		$('#' + status + '-normal-select-5').val("0");
		$('#' + status + '-normal-select-6').val("00");
		$('#' + status + '-normal-select-7').val("0");
		$('#' + status + '_work_description').val("");
	}

	function delete_work_exp() {
		$('#btn-add-work-exp').show();
		$('#area-add-work-exp').hide();
		reset_work_values('add');
	}

	function create_work_view(company_name, work_title, work_year_date_start, work_year_date_end, work_month_date_start, work_month_date_end, company_location) {
		let ready = `<p class="card-name">${company_name}</p>
			<p class="card-title">${work_title}</p>
			`;
		let date = ``;
		if (work_year_date_start !== "0" && work_year_date_end !== "0") {
			if (work_month_date_start !== "00" && work_month_date_end !== "00") {
				date = `<p><span class="card-date">${work_month_date_start}/${work_year_date_start} - ${work_month_date_end}/${work_year_date_end}</span>`;
			} else date = `<p><span class="card-date">${work_year_date_start} - ${work_year_date_end}</span>`;
		}
		if (company_location !== "" && date !== "") {
			ready += date;
			ready += ` | <span class="card-location">${company_location}</span></p>`
		} else if (company_location !== "" && date == "") {
			ready += `<p><span class="card-location">${company_location}</span></p>`;
		} else if (company_location == "" && date !== "") {
			ready += date;
			ready += '</p>';
		}
		return ready;
	}

	$('#delete-work-exp').click(delete_work_exp);
	$('#save-work-exp').click(function() {
		$('#btn-add-work-exp').show();
		$('#area-add-work-exp').hide();
		if ($('#add_company_name').val() !== "" || $('#add_work_title').val() !== "") {
			const company_name = $('#add_company_name').val();
			const work_title = $('#add_work_title').val();
			let company_location = $('#add_company_location').val();
			let work_year_date_start = $('#add-normal-select-5').val();
			let work_year_date_end = $('#add-normal-select-7').val();
			let work_month_date_start = $('#add-normal-select-4').val();
			let work_month_date_end = $('#add-normal-select-6').val();
			let work_date_start = work_year_date_start + "-" + work_month_date_start + "-00";
			let work_date_end = work_year_date_end + "-" + work_month_date_end + "-00";
			let work_description = $('#add_work_description').val();
			//
			let ready_work_experience = `
			<div class="input-hidden-area">
				<input type="hidden" name="company_name[]" value="${company_name}" class="hidden-input"/>
				<input type="hidden" name="company_location[]" value="${company_location}" class="hidden-input"/>
				<input type="hidden" name="work_title[]" value="${work_title}" class="hidden-input"/>
				<input type="hidden" name="work_date_start[]" value="${work_date_start}" class="hidden-input"/>
				<input type="hidden" name="work_date_end[]" value="${work_date_end}" class="hidden-input"/>
				<input type="hidden" name="work_description[]" value="${work_description}"/>
			</div>
			<div class="row">
			<div class="col-md-10 work-view">`;
			ready_work_experience += create_work_view(company_name, work_title, work_year_date_start, work_year_date_end, work_month_date_start, work_month_date_end, company_location);
			const id_edit = $('.work-exp-edit').length;
			ready_work_experience += `
			</div>
				<div class="col-md-2">
					<i class="fa-solid fa-pen work-exp-edit" id="work-exp-edit-${id_edit}" style="margin-top:3px;cursor:pointer;margin-left:10px;"></i>
					<i class="fa-solid fa-trash work-exp-delete" style="margin-top:3px;cursor:pointer;margin-left:5px;"></i>
				</div>
			</div>`;
			$(`<div class="ready-div" id="ready-work-div-${id_edit}">${ready_work_experience}</div>`).insertBefore('#work-exp-div #btn-add-work-exp');
			$('.work-exp-delete').on('click', function() {
				$(this).closest('.ready-div').remove();
			})
			$('.work-exp-edit').on('click', function() {
				const our_div = $(this).closest('.ready-div');
				let id = ($(this).attr('id')).split('-');
				id = id[id.length - 1];
				$("#id-work-edit").val(id);
				const values = our_div.find(".input-hidden-area");
				let company_name = values.children('input[name="company_name[]"]').val();
				let company_location = values.children('input[name="company_location[]"]').val();
				let work_title = values.children('input[name="work_title[]"]').val();
				let work_date_start = values.children('input[name="work_date_start[]"]').val();
				let work_date_end = values.children('input[name="work_date_end[]"]').val();
				let work_month_start = work_date_start.split('-')[1];
				let work_year_start = work_date_start.split('-')[0];
				let work_month_end = work_date_end.split('-')[1];
				let work_year_end = work_date_end.split('-')[0];
				let work_description = values.children('input[name="work_description[]"]').val();
				$('#btn-add-work-exp').hide();
				$('#area-edit-work-exp').show();
				our_div.hide();
				$('#edit_company_name').val(company_name);
				$('#edit_company_location').val(company_location);
				$('#edit_work_title').val(work_title);
				edit_smth();
				$(`#edit-normal-select-4`).attr("placeholder-text", $(`#edit-normal-select-4 option[value="${work_month_start}"]`).text());
				$(`#edit-normal-select-5`).attr("placeholder-text", $(`#edit-normal-select-5 option[value="${work_year_start}"]`).text());
				$(`#edit-normal-select-6`).attr("placeholder-text", $(`#edit-normal-select-6 option[value="${work_month_end}"]`).text());
				$(`#edit-normal-select-7`).attr("placeholder-text", $(`#edit-normal-select-7 option[value="${work_year_end}"]`).text());

				$(`#edit-normal-select-4 option[value="${work_month_start}"]`).attr("selected", true);
				$(`#edit-normal-select-5 option[value="${work_year_start}"]`).attr("selected", true);
				$(`#edit-normal-select-6 option[value="${work_month_end}"]`).attr("selected", true);
				$(`#edit-normal-select-7 option[value="${work_year_end}"]`).attr("selected", true);
				createSelect();
				$('#edit_work_description').val(work_description);
				//
				//
				$('#delete-editable-work-exp').click({
						our_div: our_div,
					},
					function() {
						our_div.show();
						$('#btn-add-work-exp').show();
						$('#area-edit-work-exp').hide();
						reset_work_values('edit');
					})
				//

			})
		}
		reset_work_values('add');
	});

	function add_year(selector_name) {
		const selector = $(selector_name);
		selector.append('<option class="select-dropdown__list-item" value="0">Год</option>');
		for (let i = new Date().getFullYear(); i > 1940; i--) {
			selector.append('<option class="select-dropdown__list-item" value="' + i + '">' + i + '</option>');
		}
	}

	function add_month(selector_name) {
		const selector = $(selector_name);
		selector.append('<option value="00">Месяц</option>');
		selector.append('<option value="01">Январь</option>');
		selector.append('<option value="02">Февраль</option>');
		selector.append('<option value="03">Март</option>');
		selector.append('<option value="04">Апрель</option>');
		selector.append('<option value="05">Май</option>');
		selector.append('<option value="06">Июнь</option>');
		selector.append('<option value="07">Июль</option>');
		selector.append('<option value="08">Август</option>');
		selector.append('<option value="09">Сентябрь</option>');
		selector.append('<option value="10">Октябрь</option>');
		selector.append('<option value="11">Ноябрь</option>');
		selector.append('<option value="12">Декабрь</option>');
	}

	$('#edit-work-exp').click(function() {
		const id = $("#id-work-edit").val();
		const our_div = $(`#ready-work-div-${id}`);
		const values = our_div.find(".input-hidden-area");
		const work_view = our_div.find(".work-view");
		company_name = $('#edit_company_name').val();
		work_title = $('#edit_work_title').val();
		company_location = $('#edit_company_location').val();
		work_year_date_start = $('#edit-normal-select-5').val();
		work_year_date_end = $('#edit-normal-select-7').val();
		work_month_date_start = $('#edit-normal-select-4').val();
		work_month_date_end = $('#edit-normal-select-6').val();
		work_date_start = work_year_date_start + "-" + work_month_date_start + "-00";
		work_date_end = work_year_date_end + "-" + work_month_date_end + "-00";
		work_description = $('#edit_work_description').val();
		work_view.empty();
		work_view.append(create_work_view(company_name, work_title, work_year_date_start, work_year_date_end, work_month_date_start, work_month_date_end, company_location));
		/////
		values.children('input[name="company_name[]"]').val(company_name);
		values.children('input[name="company_location[]"]').val(company_location);
		values.children('input[name="work_title[]"]').val(work_title)
		values.children('input[name="work_date_start[]"]').val(work_date_start)
		values.children('input[name="work_date_end[]"]').val(work_date_end)
		values.children('input[name="work_description[]"]').val(work_description)
		//////
		//reset_work_values('edit');
		our_div.show();
		$('#btn-add-work-exp').show();
		$('#area-edit-work-exp').hide();
	})
	add_year('#add-normal-select-5');
	add_year('#add-normal-select-7');
	add_year('#edit-normal-select-5');
	add_year('#edit-normal-select-7');
	add_year('#add-normal-select-9');
	add_year('#add-normal-select-11');
	add_year('#edit-normal-select-9');
	add_year('#edit-normal-select-11');
	//
	//
	$('#btn-add-edu').click(function() {
		edit_smth();

		$('#btn-add-edu').hide();
		$('#area-add-edu').show();
		createSelect();

	});

	function reset_edu_values(status) {
		$('#' + status + '_university_name').val("");
		$('#' + status + '_edu_location').val("");
		$('#' + status + '_speciality_name').val("");
		$('#' + status + '-normal-select-8').val("");
		$('#' + status + '-normal-select-9').val("");
		$('#' + status + '-normal-select-10').val("");
		$('#' + status + '-normal-select-11').val("");
		$('#' + status + '_edu_description').val("");
	}

	$('#delete-edu').click(function() {
		$('#btn-add-edu').show();
		$('#area-add-edu').hide();
		//
		reset_edu_values('add');
	});

	function create_edu_view(university_name, speciality_name, edu_year_date_start, edu_year_date_end, edu_month_date_start, edu_month_date_end, edu_location) {
		let ready = `<p class="card-name">${university_name}</p>
			<p class="card-title">${speciality_name}</p>
			`;
		let date = ``;
		if (edu_year_date_start !== "0" && edu_year_date_end !== "0") {
			if (edu_month_date_start !== "00" && edu_month_date_end !== "00") {
				date = `<p class="card-date">${edu_month_date_start}/${edu_year_date_start} - ${edu_month_date_end}/${edu_year_date_end}`;
			} else date = `<p class="card-date">${edu_year_date_start} - ${edu_year_date_end}`;
		}
		if (edu_location !== "" && date !== "") {
			ready += date;
			ready += ` | ${edu_location}</p>`
		} else if (edu_location !== "" && date == "") {
			ready += `<p>${edu_location}</p>`;
		} else if (edu_location == "" && date !== "") {
			ready += date;
			ready += '</p>';
		}
		return ready;
	}

	$('#save-edu').click(function() {
		$('#btn-add-edu').show();
		$('#area-add-edu').hide();
		if ($('#add_university_name').val() !== "" || $('#add_speciality_name').val() !== "") {
			let university_name = $('#add_university_name').val();
			let speciality_name = $('#add_speciality_name').val();
			let edu_location = $('#add_edu_location').val();
			let edu_year_date_start = $('#add-normal-select-9').val();
			let edu_year_date_end = $('#add-normal-select-11').val();
			let edu_month_date_start = $('#add-normal-select-8').val();
			let edu_month_date_end = $('#add-normal-select-10').val();
			let edu_date_start = edu_year_date_start + "-" + edu_month_date_start + "-00";
			let edu_date_end = edu_year_date_end + "-" + edu_month_date_end + "-00";

			let edu_description = $('#add_edu_description').val();
			//
			let ready_edu_experience = `
			<div class="input-hidden-area">
				<input type="hidden" name="university_name[]" value="${university_name}" class="hidden-input"/>
				<input type="hidden" name="edu_location[]" value="${edu_location}" class="hidden-input"/>
				<input type="hidden" name="speciality_name[]" value="${speciality_name}" class="hidden-input"/>
				<input type="hidden" name="edu_date_start[]" value="${edu_date_start}" class="hidden-input"/>
				<input type="hidden" name="edu_date_end[]" value="${edu_date_end}" class="hidden-input"/>
				<input type="hidden" name="edu_description[]" value="${edu_description}"/>
			</div>
			<div class="row">
			<div class="col-md-10 edu-view">`;
			ready_edu_experience += create_edu_view(university_name, speciality_name, edu_year_date_start, edu_year_date_end, edu_month_date_start, edu_month_date_end, edu_location);
			const id_edit = $('.edu-edit').length;
			ready_edu_experience += `
			</div>
				<div class="col-md-2">
					<i class="fa-solid fa-pen edu-edit" id="edu-edit-${id_edit}" style="margin-top:3px;cursor:pointer;margin-left:10px;"></i>
					<i class="fa-solid fa-trash edu-delete" style="margin-top:3px;cursor:pointer;margin-left:5px;"></i>
				</div>
			</div>`;

			$(`<div class="ready-div" id="ready-edu-div-${id_edit}">${ready_edu_experience}</div>`).insertBefore('#edu-div #btn-add-edu');
			$('.edu-delete').on('click', function() {
				$(this).closest('.ready-div').remove();
			})
			$('.edu-edit').on('click', function() {
				const our_div = $(this).closest('.ready-div');
				let id = ($(this).attr('id')).split('-');
				id = id[id.length - 1];
				$("#id-edu-edit").val(id);
				const values = our_div.find(".input-hidden-area");
				let university_name = values.children('input[name="university_name[]"]').val();
				let speciality_name = values.children('input[name="speciality_name[]"]').val();
				let edu_location = values.children('input[name="edu_location[]"]').val();
				let edu_date_start = values.children('input[name="edu_date_start[]"]').val();
				let edu_date_end = values.children('input[name="edu_date_end[]"]').val();
				let edu_month_start = edu_date_start.split('-')[1];
				let edu_year_start = edu_date_start.split('-')[0];
				let edu_month_end = edu_date_end.split('-')[1];
				let edu_year_end = edu_date_end.split('-')[0];
				let edu_description = values.children('input[name="edu_description[]"]').val();
				///
				$('#btn-add-edu').hide();
				$('#area-edit-edu').show();
				our_div.hide();
				$('#edit_university_name').val(university_name);
				$('#edit_edu_location').val(edu_location);
				$('#edit_speciality_name').val(speciality_name);
				edit_smth();

				$(`#edit-normal-select-8`).attr("placeholder-text", $(`#edit-normal-select-8 option[value="${edu_month_start}"]`).text());
				$(`#edit-normal-select-9`).attr("placeholder-text", $(`#edit-normal-select-9 option[value="${edu_year_start}"]`).text());
				$(`#edit-normal-select-10`).attr("placeholder-text", $(`#edit-normal-select-10 option[value="${edu_month_end}"]`).text());
				$(`#edit-normal-select-11`).attr("placeholder-text", $(`#edit-normal-select-11 option[value="${edu_year_end}"]`).text());

				$(`#edit-normal-select-8 option[value="${edu_month_start}"]`).attr("selected", true);
				$(`#edit-normal-select-9 option[value="${edu_year_start}"]`).attr("selected", true);
				$(`#edit-normal-select-10 option[value="${edu_month_end}"]`).attr("selected", true);
				$(`#edit-normal-select-11 option[value="${edu_year_end}"]`).attr("selected", true);
				createSelect();
				$('#edit_edu_description').val(edu_description);
				//
				//
				$('#delete-editable-edu').click({
						our_div: our_div,
					},
					function() {
						our_div.show();
						$('#btn-add-edu').show();
						$('#area-edit-edu').hide();
						reset_edu_values('edit');
					})
				//
			})
		}
		reset_edu_values('add');
	});
	$('#edit-edu').click(function() {
		const id = $("#id-edu-edit").val();
		const our_div = $(`#ready-edu-div-${id}`);
		const values = our_div.find(".input-hidden-area");
		const edu_view = our_div.find(".edu-view");
		university_name = $('#edit_university_name').val();
		speciality_name = $('#edit_speciality_name').val();
		edu_location = $('#edit_edu_location').val();
		edu_year_date_start = $('#edit-normal-select-9').val();
		edu_year_date_end = $('#edit-normal-select-11').val();
		edu_month_date_start = $('#edit-normal-select-8').val();
		edu_month_date_end = $('#edit-normal-select-10').val();
		edu_date_start = edu_year_date_start + "-" + edu_month_date_start + "-00";
		edu_date_end = edu_year_date_end + "-" + edu_month_date_end + "-00";
		edu_description = $('#edit_edu_description').val();
		edu_view.empty();
		edu_view.append(create_edu_view(university_name, speciality_name, edu_year_date_start, edu_year_date_end, edu_month_date_start, edu_month_date_end, edu_location));
		/////
		values.children('input[name="university_name[]"]').val(university_name)
		values.children('input[name="speciality_name[]"]').val(speciality_name)
		values.children('input[name="edu_location[]"]').val(edu_location)
		values.children('input[name="edu_date_start[]"]').val(edu_date_start)
		values.children('input[name="edu_date_end[]"]').val(edu_date_end)
		values.children('input[name="edu_description[]"]').val(edu_description)
		//////
		//reset_edu_values('edit');
		our_div.show();
		$('#btn-add-edu').show();
		$('#area-edit-edu').hide();
	})
	//
	//
	function create_course_view(platform_name, course_name) {
		let ready = `<p class="card-name">${platform_name}</p>
			<p class="card-title">${course_name}</p>`;
		return ready;
	}

	function reset_course_values(status) {
		$('#' + status + '_platform_name').val("");
		$('#' + status + '_course_name').val("");

	}
	$('#btn-add-course').click(function() {
		$('#btn-add-course').hide();
		$('#area-add-course').show();
	});
	$('#delete-course').click(function() {
		$('#btn-add-course').show();
		$('#area-add-course').hide();
		//
		reset_course_values('add');
	});
	$('#save-course').click(function() {
		$('#btn-add-course').show();
		$('#area-add-course').hide();
		if ($('#add_platform_name').val() !== "" || $('#add_course_name').val() !== "") {
			const platform_name = $('#add_platform_name').val();
			const course_name = $('#add_course_name').val();
			//
			let ready_course = `
			<div class="input-hidden-area">
				<input type="hidden" name="platform_name[]" value="${platform_name}" class="hidden-input"/>
				<input type="hidden" name="course_name[]" value="${course_name}" class="hidden-input"/>
			</div>`;
			ready_course += `<div class="row">
			<div class="col-md-10 course-view">`;
			ready_course += create_course_view(platform_name, course_name);
			const id_edit = $('.course-edit').length;
			ready_course += `
			</div>
				<div class="col-md-2">
					<i class="fa-solid fa-pen course-edit" id="edu-edit-${id_edit}" style="margin-top:3px;cursor:pointer;margin-left:10px;"></i>
					<i class="fa-solid fa-trash course-delete" style="margin-top:3px;cursor:pointer;margin-left:5px;"></i>
				</div>
			</div>`;
			$(`<div class="ready-div" id="ready-course-div-${id_edit}">${ready_course}</div>`).insertBefore('#course-div #btn-add-course');

			$('.course-delete').on('click', function() {
				$(this).closest('.ready-div').remove();
			})
			$('.course-edit').on('click', function() {
				const our_div = $(this).closest('.ready-div')
				let id = ($(this).attr('id')).split('-');
				id = id[id.length - 1];
				$("#id-course-edit").val(id);
				const values = our_div.find(".input-hidden-area");
				let platform_name = values.children('input[name="platform_name[]"]').val();
				let course_name = values.children('input[name="course_name[]"]').val();
				///
				$('#btn-add-course').hide();
				$('#area-edit-course').show();
				our_div.hide();
				$('#edit_platform_name').val(platform_name);
				$('#edit_course_name').val(course_name);
				//
				//
				$('#delete-editable-course').click({
						our_div: our_div,
					},
					function() {
						our_div.show();
						$('#btn-add-course').show();
						$('#area-edit-course').hide();
						reset_course_values('edit');
					})
				const course_view = our_div.find(".course-view");
				//

			})
			reset_course_values('add');
		}
	});
	$('#edit-course').click(function() {
		const id = $("#id-course-edit").val();
		const our_div = $(`#ready-course-div-${id}`);
		const values = our_div.find(".input-hidden-area");
		const course_view = our_div.find(".course-view");

		platform_name = $('#edit_platform_name').val();
		course_name = $('#edit_course_name').val();
		course_view.empty();
		course_view.append(create_course_view(platform_name, course_name));
		/////
		values.children('input[name="platform_name[]"]').val(platform_name)
		values.children('input[name="course_name[]"]').val(course_name)
		//////
		//reset_course_values('edit');
		our_div.show();
		$('#btn-add-course').show();
		$('#area-edit-course').hide();
	})
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
	/////
	/////
	/////////
	///////

	$('#rate-skills').on('click', open_rate_area);
	$('#add-resume').click(function() {
		$('#resume-card').show();
		$('#rating-card').hide();
		$('#add-resume').css('background-color', 'var(--active-tab)');
		$('#add-resume').css('padding', '10px 10px 10px 5px');
		$('#add-resume').css('border-radius', '8px');
		$('#rate-skills').css('background-color', 'transparent');
		$('#rate-skills').css('padding', '0px');
		$('#rate-skills').css('border-radius', '0px');
	});

	////
	/////
	////

	function create_popup(status, placeholder) {
		const name = status == "навык" ? status + "a" : status == "качество" ? status.substring(0, status.length - 1) + "a" : status.substring(0, status.length - 1) + "и";
		const popup = `<div class="modal" id="skill-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="little-header-text">Добавить ${status}</h2>
      </div>
      <div class="modal-body">
        <x-label :value="__('Название ${name}')" />
		<x-input id="skill-name" type="text" style="width:400px" placeholder="${placeholder}" autocomplete="off"/>
      </div>
      <div class="modal-footer">
	  <div style="margin-top:20px">
	<span class="span-like-button" type="button" id="btn-close-skill" data-bs-dismiss="modal">
		Отменить
	</span>
	<span class="span-like-button ml-4" type="button" id="btn-add-skill">
	Добавить
	</span>
</div>
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
					url: '{{ route("student.add-skill") }}',
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
					url: '{{ route("student.add-skill") }}',
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
					url: '{{ route("student.add-profession") }}',
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
</script>
<style>
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	#area-add-work-exp .select-dropdown__button,
	#area-edit-edu .select-dropdown__button,
	#area-edit-work-exp .select-dropdown__button,
	#area-add-edu .select-dropdown__button {
		width: 130px !important;
	}

	.card-div {
		width: 680px;
	}

	.card-div,
	#area-add-work-exp,
	#area-edit-work-exp,
	#area-add-edu,
	#area-edit-edu,
	#area-add-course,
	#area-edit-course {
		margin-top: 20px;
		border: 1px solid var(--border-color);
		padding: 20px;
		border-radius: 8px;
	}

	#add-work-time-area,
	#edit-work-time-area,
	#add-edu-time-area,
	#edit-edu-time-area {
		display: flex;
		justify-content: space-around;
		width: 580px;
		margin-left: -5px;
	}

	.ready-div {
		border: 1px solid var(--border-color);
		padding: 10px;
		margin: 8px 0;
		border-radius: 8px;
	}

	.card-name {
		text-transform: uppercase;
		font-weight: 700;
	}

	.card-date {
		font-size: 12px;
	}

	.tabs-div {
		position: absolute;
		left: 30px;
	}

	.tab {
		margin-top: 25px;
	}

	.tab:first-child {
		background-color: var(--active-tab);
		padding: 10px 10px 10px 5px;
		border-radius: 8px;
	}

	.rate-div {
		margin-top: 20px;
	}

	.selected-text {
		color: var(--text-selection-color);
	}
</style>

</html>