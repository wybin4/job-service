<!DOCTYPE html>
<html>

<head>
	<meta name="_token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="{{asset('/js/petrovich.js')}}"></script>
	<!---->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/jquery.barrating.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/themes/fontawesome-stars.min.css" rel="stylesheet" />

</head>
<div id="choose-hard-skills-popup">
	<div class="modal" id="choose-hard-skills-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="text-xl text-center">Выбрать навыки</h2>
				</div>
				<div class="modal-body hard-body" style="max-height:150px">
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-select-hard" data-bs-dismiss="modal">Добавить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="choose-soft-skills-popup">
	<div class="modal" id="choose-soft-skills-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="text-xl text-center">Выбрать качества</h2>
				</div>
				<div class="modal-body soft-body" style="max-height:150px">
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-select-soft" data-bs-dismiss="modal">Добавить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-employer-layout>
		<form method="POST" action="/employer/rate-a-student">
			@csrf
			<input type="hidden" value="{{$student->id}}" name="student_id" />
			<input type="hidden" value="{{$vacancy_id}}" name="vacancy_id" />
			<input type="hidden" value="{{$dismiss_student}}" name="dismiss_student" />
			<div class="text-center header-text mt-4">Оцените студента</div>
			<div>
				<x-big-card>
					<div class="hard-skill-rate-card-hidden" style="display:none;width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">1</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Навыки студента</div>
							</div>
						</div>
					</div>
					<div class="hard-skill-rate-card">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">1</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Навыки студента</div>
								<div class="text-muted" style="margin-left:-10px;">Как вы оцениваете навыки <span class="student-name genitive">{{$student->student_fio}}</span>?</div>
							</div>
						</div>
						<div class="mt-2">
							<div id="rate-area-hard"></div>
							<div class="d-flex justify-content-end">
								<div class="button create-hard-skills">Не хватает навыков</div>
								<div class="button btn-next-soft ml-4">Далее</div>
							</div>
						</div>
					</div>
				</x-big-card>
				<x-big-card>
					<div class="soft-skill-rate-card-hidden" style="width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">2</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Качества студента</div>
							</div>
						</div>
					</div>
					<div class="soft-skill-rate-card" style="display:none;width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">2</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Качества студента</div>
								<div class="text-muted" style="margin-left:-10px;">Как вы оцениваете качества <span class="student-name genitive">{{$student->student_fio}}</span>?</div>
							</div>
						</div>
						<div class="mt-2">
							<div id="rate-area-soft"></div>
							<div class="d-flex justify-content-end">
								<div class="button create-soft-skills">Не хватает качеств</div>
								<div class="button btn-next-review ml-4">Далее</div>
							</div>
						</div>
					</div>
				</x-big-card>
				<x-big-card>
					<div class="review-card-hidden" style="width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">3</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Мнение о студенте</div>
							</div>
						</div>
					</div>
					<div class="review-card" style="display:none">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">3</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Мнение о студенте</div>
								<div class="text-muted" style="margin-left:-10px;">Что вы думаете о <span class="student-name prepositional">{{$student->student_fio}}</span>?</div>
							</div>
						</div>
						<textarea name="description"></textarea>
						<div class="mt-2">
							<button class="button btn-rate" type="submit">Оценить</button>
						</div>
					</div>
				</x-big-card>
				<div class="pb-4"></div>
			</div>
		</form>
	</x-employer-layout>
</div>

</html>

<style>
	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	textarea {
		width: 490px;
		margin: 20px 0;
	}


	.btn-rate {
		margin-left: 382px;

	}

	.number-circle {
		height: 30px;
		width: 30px;
		background-color: var(--link-hover-color);
		color: white;
		border-radius: 50%;
		font-size: 16px;
		display: table-cell;
		vertical-align: middle;
		text-align: center;
	}

	.rate-head {
		margin-top: 2px;
		margin-left: -10px;
		font-size: 18px;
		font-weight: bold;
	}


	.br-theme-fontawesome-stars .br-widget a::after {
		color: var(--dot-color) !important;
	}

	.br-theme-fontawesome-stars .br-widget a.br-active:after {
		color: var(--link-hover-color) !important;
	}

	.br-theme-fontawesome-stars .br-widget a.br-selected:after {
		color: var(--link-hover-color) !important;
	}

	#rate-area-hard,
	#rate-area-soft {
		display: flex;
		flex-wrap: wrap;
		flex-direction: row;
		justify-content: start;
		min-height: 140px;
		width: 490px;
		margin: auto;
		margin-top: 30px;
	}

	.rate-block {
		width: 150px;
		height: 50px;
	}

	.button {
		cursor: pointer;
	}
</style>
<script>
	let name = $(".student-name").text().split(' ');
	let gender = new SexByRussianName(name[0], name[1], name[2]);
	if (gender.get_gender()) {
		gender = 'male';
	} else gender = 'female';
	var person = {
		gender: gender,
		first: name[1],
		last: name[0]
	};
	$(".genitive").text(petrovich(person, 'genitive').last + " " + petrovich(person, 'genitive').first)
	$(".prepositional").text(petrovich(person, 'prepositional').last + " " + petrovich(person, 'prepositional').first)

	////
	///
	let skills = <?php echo json_encode($student_skills); ?>;
	hard_skills = skills.filter((hs) => {
		return hs.skill_type == 1;
	})
	soft_skills = skills.filter((ss) => {
		return ss.skill_type == 0;
	})
	for (let i = 0; i < hard_skills.length; i++) {
		const id = `rating-${hard_skills[i].skill_id}`;
		let text = `<div class="rate-block">
						<input type="hidden" value="${hard_skills[i].skill_id}" name="skill_id[]"/>
						<x-label for="${id}">${hard_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
		$('#rate-area-hard').append(text);

		$(function() {
			$('#' + id).barrating({
				theme: 'fontawesome-stars'
			});
		});
	}

	for (let i = 0; i < soft_skills.length; i++) {
		const id = `rating-${soft_skills[i].skill_id}`;
		let text = `<div class="rate-block">
						<input type="hidden" value="${soft_skills[i].skill_id}" name="skill_id[]"/>
						<x-label for="${id}">${soft_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
		$('#rate-area-soft').append(text);

		$(function() {
			$('#' + id).barrating({
				theme: 'fontawesome-stars'
			});
		});
	}

	$(".btn-next-review").on('click', function() {
		$(".soft-skill-rate-card-hidden").show();
		$(".soft-skill-rate-card").hide();
		$(".review-card-hidden").hide();
		$(".review-card").show();
	})
	$(".btn-next-soft").on('click', function() {
		$(".hard-skill-rate-card-hidden").show();
		$(".hard-skill-rate-card").hide();
		$(".soft-skill-rate-card-hidden").hide();
		$(".soft-skill-rate-card").show();
	})
	$(".create-hard-skills").on('click', function() {
		$('#choose-hard-skills-modal').show(); //запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$(".hard-body").empty();
		let multi = `<select class="hard-multi" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
						@foreach($skill as $val)
						@if($val->skill_type == 1)
						<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
						@endif
						@endforeach
					</select>`;
		$(".hard-body").append(multi);
		MultiselectDropdown(window.MultiselectDropdownOptions, '450px');

		$("#btn-select-hard").on('click', function() {
			$('#choose-hard-skills-modal').hide();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			let new_skills = $(".hard-multi").val();
			new_skills = new_skills.map((r) => {
				return parseInt(r);
			})

			let all_skills = <?php echo json_encode($skill); ?>;
			const max_id = Math.max(...all_skills.map(as => {
				return as.id
			}));
			new_skills = all_skills.filter((as) => {
				return new_skills.includes(as.id);
			})
			for (let i = 0; i < new_skills.length; i++) {
				const id = `rating-${i + max_id}`;
				let text = `<div class="rate-block">
						<input type="hidden" value="${new_skills[i].id}" name="skill_id[]"/>
						<x-label for="${id}">${new_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
				$('#rate-area-hard').append(text);
				$(function() {
					$('#' + id).barrating({
						theme: 'fontawesome-stars'
					});
				});
			}
		})
	})
	$(".create-soft-skills").on('click', function() {
		$('#choose-soft-skills-modal').show(); //запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$(".soft-body").empty();
		let multi = `<select class="soft-multi" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
						@foreach($skill as $val)
						@if($val->skill_type == 0)
						<option value="{{ $val->id}}">{{ $val->skill_name}}</option>
						@endif
						@endforeach
					</select>`;
		$(".soft-body").append(multi);
		MultiselectDropdown(window.MultiselectDropdownOptions, '450px');

		$("#btn-select-soft").on('click', function() {
			$('#choose-soft-skills-modal').hide();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			let new_skills = $(".soft-multi").val();
			new_skills = new_skills.map((r) => {
				return parseInt(r);
			})

			let all_skills = <?php echo json_encode($skill); ?>;
			let max_id = Math.max(...all_skills.map(as => {
				return as.id
			}));
			max_id += max_id;
			new_skills = all_skills.filter((as) => {
				return new_skills.includes(as.id);
			})
			for (let i = 0; i < new_skills.length; i++) {
				const id = `rating-${i + max_id}`;
				let text = `<div class="rate-block">
						<input type="hidden" value="${new_skills[i].id}" name="skill_id[]"/>
						<x-label for="${id}">${new_skills[i].skill_name}</x-label>
						<select id="${id}" name="skill_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
				$('#rate-area-soft').append(text);
				$(function() {
					$('#' + id).barrating({
						theme: 'fontawesome-stars'
					});
				});
			}
		})
	})

	function MultiselectDropdown(options, width = '680px') {
		let config = {
			search: true,
			width: width,
			height: '15rem',
			placeholder: '',
			txtSelected: 'выбрано',
			txtAll: 'Выбрать всё',
			txtRemove: 'Удалить',
			txtSearch: 'Поиск',
			...options
		};

		function newEl(tag, attrs) {
			let e = document.createElement(tag);
			if (attrs !== undefined) Object.keys(attrs).forEach(k => {
				if (k === 'class') {
					Array.isArray(attrs[k]) ? attrs[k].forEach(o => o !== '' ? e.classList.add(o) : 0) : (attrs[k] !== '' ? e.classList.add(attrs[k]) : 0)
				} else if (k === 'style') {
					Object.keys(attrs[k]).forEach(ks => {
						e.style[ks] = attrs[k][ks];
					});
				} else if (k === 'text') {
					attrs[k] === '' ? e.innerHTML = '&nbsp;' : e.innerText = attrs[k]
				} else e[k] = attrs[k];
			});
			return e;
		}


		document.querySelectorAll("select[multiple]").forEach((el, k) => {

			let div = newEl('div', {
				class: 'multiselect-dropdown',
				style: {
					width: config.width ?? el.clientWidth + 'px',
					padding: config.style?.padding ?? ''
				}
			});
			el.style.display = 'none';
			el.parentNode.insertBefore(div, el.nextSibling);
			let listWrap = newEl('div', {
				class: 'multiselect-dropdown-list-wrapper'
			});
			let list = newEl('div', {
				class: 'multiselect-dropdown-list',
				style: {
					height: config.height
				}
			});
			let search = newEl('input', {
				class: ['multiselect-dropdown-search'].concat([config.searchInput?.class ?? 'form-control']),
				style: {
					width: '100%',
					display: el.attributes['multiselect-search']?.value === 'true' ? 'block' : 'none'
				},
				placeholder: config.txtSearch
			});
			listWrap.appendChild(search);
			div.appendChild(listWrap);
			listWrap.appendChild(list);

			el.loadOptions = () => {
				list.innerHTML = '';

				if (el.attributes['multiselect-select-all']?.value == 'true') {
					let op = newEl('div', {
						class: 'multiselect-dropdown-all-selector'
					})
					let ic = newEl('input', {
						class: 'checkbox',
						type: 'checkbox'
					});
					op.appendChild(ic);
					op.appendChild(newEl('label', {
						text: config.txtAll
					}));

					op.addEventListener('click', () => {
						op.classList.toggle('checked');
						op.querySelector("input").checked = !op.querySelector("input").checked;

						let ch = op.querySelector("input").checked;
						list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")
							.forEach(i => {
								if (i.style.display !== 'none') {
									i.querySelector("input").checked = ch;
									i.optEl.selected = ch
								}
							});

						el.dispatchEvent(new Event('change'));
					});
					ic.addEventListener('click', (ev) => {
						ic.checked = !ic.checked;
					});
					el.addEventListener('change', (ev) => {
						let itms = Array.from(list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")).filter(e => e.style.display !== 'none')
						let existsNotSelected = itms.find(i => !i.querySelector("input").checked);
						if (ic.checked && existsNotSelected) ic.checked = false;
						else if (ic.checked == false && existsNotSelected === undefined) ic.checked = true;
					});

					list.appendChild(op);
				}

				Array.from(el.options).map(o => {
					let op = newEl('div', {
						class: o.selected ? 'checked' : '',
						optEl: o
					})
					let ic = newEl('input', {
						class: 'checkbox',
						type: 'checkbox',
						checked: o.selected
					});
					op.appendChild(ic);
					op.appendChild(newEl('label', {
						text: o.text
					}));

					op.addEventListener('click', () => {
						op.classList.toggle('checked');
						op.querySelector("input").checked = !op.querySelector("input").checked;
						op.optEl.selected = !!!op.optEl.selected;
						el.dispatchEvent(new Event('change'));
					});
					ic.addEventListener('click', (ev) => {
						ic.checked = !ic.checked;
					});
					o.listitemEl = op;
					list.appendChild(op);
				});
				div.listEl = listWrap;

				div.refresh = () => {
					div.querySelectorAll('span.optext, span.placeholder').forEach(t => div.removeChild(t));
					let sels = Array.from(el.selectedOptions);
					if (sels.length > (el.attributes['multiselect-max-items']?.value ?? 5)) {
						div.appendChild(newEl('span', {
							class: ['optext', 'maxselected'],
							text: sels.length + ' ' + config.txtSelected
						}));
					} else {
						sels.map(x => {
							let c = newEl('span', {
								class: 'optext',
								text: x.text,
								srcOption: x
							});
							if ((el.attributes['multiselect-hide-x']?.value !== 'true'))
								c.appendChild(newEl('span', {
									class: 'optdel',
									text: '🗙',
									title: config.txtRemove,
									onclick: (ev) => {
										c.srcOption.listitemEl.dispatchEvent(new Event('click'));
										div.refresh();
										ev.stopPropagation();
									}
								}));

							div.appendChild(c);
						});
					}
					if (0 == el.selectedOptions.length) div.appendChild(newEl('span', {
						class: 'placeholder',
						text: el.attributes['placeholder']?.value ?? config.placeholder
					}));
				};
				div.refresh();
			}
			el.loadOptions();

			search.addEventListener('input', () => {
				list.querySelectorAll(":scope div:not(.multiselect-dropdown-all-selector)").forEach(d => {
					let txt = d.querySelector("label").innerText.toUpperCase();
					d.style.display = txt.includes(search.value.toUpperCase()) ? 'block' : 'none';
				});
			});

			div.addEventListener('click', () => {
				div.listEl.style.display = 'block';
				search.focus();
				search.select();
			});

			document.addEventListener('click', function(event) {
				if (!div.contains(event.target)) {
					listWrap.style.display = 'none';
					div.refresh();
				}
			});
		});
	}
</script>