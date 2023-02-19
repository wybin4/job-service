<!DOCTYPE html>
<html>

<head>
	<meta name="_token" content="{{ csrf_token() }}">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<!---->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/jquery.barrating.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/themes/fontawesome-stars.min.css" rel="stylesheet" />

</head>
<div id="choose-qualities-popup">
	<div class="modal" id="choose-qualities-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="text-xl text-center">Выбрать критерии</h2>
				</div>
				<div class="modal-body quality-body" style="max-height:150px">
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-select-quality" data-bs-dismiss="modal">Добавить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-student-layout>
		<form method="POST" action="/student/rate-an-employer">
			@csrf
			<input type="hidden" value="{{$employer->id}}" name="employer_id" />
			<input type="hidden" value="{{$vacancy_id}}" name="vacancy_id" />
			<div class="text-center header-text mt-4">Оцените вакансию и работодателя</div>
			<div>
				<x-big-card>
					<div class="qualities-rate-card-hidden" style="display:none;width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">1</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Критерии оценки компании</div>
							</div>
						</div>
					</div>
					<div class="qualities-rate-card" style="width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">1</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Критерии оценки компании</div>
								<div class="text-muted" style="margin-left:-10px;">Что вы думаете о компании?</div>
							</div>
						</div>
						<div class="mt-2">
							<div id="rate-area-quality"></div>
							<div class="d-flex justify-content-end">
								<div class="button create-qualities">Выбрать критерии</div>
								<div class="button btn-next-review ml-4">Далее</div>
							</div>
						</div>
					</div>
				</x-big-card>
				<x-big-card>
					<div class="review-card-hidden" style="width:490px;">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">2</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Мнение о вакансии и работодателе</div>
							</div>
						</div>
					</div>
					<div class="review-card" style="display:none">
						<div class="row">
							<div class="col-md-auto">
								<div class="number-circle">2</div>
							</div>
							<div class="col-md-auto">
								<div class="rate-head">Мнение о вакансии и работодателе</div>
								<div class="text-muted" style="margin-left:-10px;">Что вы думаете о данной вакансии и работодателе?</div>
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
	</x-student-layout>
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

	#rate-area-quality {
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
	////
	///
	$(".btn-next-review").on('click', function() {
		$(".qualities-rate-card-hidden").show();
		$(".qualities-rate-card").hide();
		$(".review-card-hidden").hide();
		$(".review-card").show();
	})
	$(".create-qualities").on('click', function() {
		$('#choose-qualities-modal').show(); //запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$(".quality-body").empty();
		let multi = `<select class="quality-multi" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3">
						@foreach($qualities as $val)
						<option value="{{ $val->id}}">{{ $val->quality_name}}</option>
						@endforeach
					</select>`;
		$(".quality-body").append(multi);
		MultiselectDropdown(window.MultiselectDropdownOptions, '450px');

		$("#btn-select-quality").on('click', function() {
			$('#choose-qualities-modal').hide();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur");
			let new_qualities = $(".quality-multi").val();
			new_qualities = new_qualities.map((r) => {
				return parseInt(r);
			})

			let all_qualities = <?php echo json_encode($qualities); ?>;
			const max_id = Math.max(...all_qualities.map(as => {
				return as.id
			}));
			new_qualities = all_qualities.filter((as) => {
				return new_qualities.includes(as.id);
			})
			for (let i = 0; i < new_qualities.length; i++) {
				const id = `rating-${i + max_id}`;
				let text = `<div class="rate-block">
						<input type="hidden" value="${new_qualities[i].id}" name="quality_id[]"/>
						<x-label for="${id}">${new_qualities[i].quality_name}</x-label>
						<select id="${id}" name="quality_rate[]">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>`;
				$('#rate-area-quality').append(text);
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