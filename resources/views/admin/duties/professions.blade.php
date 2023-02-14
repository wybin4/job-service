<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="/js/selector.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.js"></script>
</head>
<div id="add-profession-area">
	<div class="modal" id="profession-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="little-header-text text-center">Добавить профессию</h2>
				</div>
				<div class="modal-body">
					<div id="add-profession-error"></div>
					<x-label :value="__('Название профессии')" />
					<x-input id="profession-id" autocomplete="off" type="text" style="width:400px" placeholder="Например: QA-инженер" />
					<div style="margin-top:20px">
						<x-label for="normal-select-1" :value="__('Область')" />
						<select id="normal-select-1" class="single-selector" placeholder-text="ㅤ">
							@foreach ($subspheres as $subsphere)
							<option value="{{$subsphere->id}}" class="select-dropdown__list-item">{{$subsphere->subsphere_of_activity_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-close-profession" data-bs-dismiss="modal">Отменить</span>
					<span type="button" class="span-like-button" id="btn-add-profession">Добавить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-admin-layout>
		<div id="top-panel">
			<div class="row">
				<div class="col-sm-5">
					<h2 class="header-text">Профессии</h2>
				</div>
				<div class="col-sm-3">
					<span id="add-profession" class="span-like-button">
						Добавить профессию
					</span>
				</div>
				<div class="col-sm-1">
					<span class="span-like-button search-btn">
						<i class="fa-solid fa-magnifying-glass"></i>
					</span>
				</div>
			</div>
		</div>
		<div id="search-area">
			<x-big-card>
				<input autocomplete="off" onkeyup="search_in_table()" id="search-in-table" class="input" />
			</x-big-card>
		</div>
		<x-big-card>
			<div style="min-width:900px;">
				<table class="table" id="table_professions">
					<thead>
						<tr>
							<th scope="col" class="header">Название професии</th>
							<th scope="col" class="header">Название области</th>
							<th scope="col" class="header">Действия</th>
							<th>ㅤ</th>
						</tr>
					</thead>
					<tbody>
						@csrf

						@foreach($exclude_professions as $profession)
						<tr id="row-{{$profession->id}}">
							<td id="profession-id-{{$profession->id}}">{{$profession->profession_name}}</td>
							<td id="subsphere-id-{{$profession->id}}">@foreach ($subspheres->where('id', $profession->subsphere_id) as $arr) {{$arr->subsphere_of_activity_name}}@endforeach</td>
							<td style="color:var(--link-hover-color);"><i class="fa-solid fa-lock"></i></td>
							<td></td>
						</tr>
						@endforeach
						@foreach($all_professions as $profession)
						<tr id="row-{{$profession->id}}">
							<td id="profession-id-{{$profession->id}}">{{$profession->profession_name}}</td>
							<td id="subsphere-id-{{$profession->id}}">@foreach ($subspheres->where('id', $profession->subsphere_id) as $arr) {{$arr->subsphere_of_activity_name}}@endforeach</td>
							<td><i id="btn-edit-{{$profession->id}}" class="fa-solid fa-pen-to-square pen-edit"></i><i id="btn-save-{{$profession->id}}" style="display:none" class="fa-regular fa-circle-check pen-save"></i></td>
							<td><i id="btn-delete-{{$profession->id}}" class="fa-solid fa-trash pen-delete"></i></td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<div id="pagination"></div>
			</div>
		</x-big-card>
		<div style="padding-bottom:40px"></div>
	</x-admin-layout>
</div>
<style>
	html {
		overflow-x: hidden;
	}

	.blur {
		transition: all 0.2s ease-in-out;
		filter: blur(3px);
	}

	.pen-edit,
	.pen-delete,
	.pen-save {
		cursor: pointer;
		color: var(--link-hover-color);
	}

	.select-dropdown,
	.select-dropdown__button {
		width: 400px !important;
	}

	table.table>thead>tr>th {
		border-color: transparent !important;

	}

	.header {
		color: rgb(31, 41, 55);
		font-weight: 700 !important;
		font-size: 14px;
	}

	td {
		border: none;
	}

	#top-panel {
		margin-top: 20px;
		margin-left: 300px;
	}

	#add-profession {
		cursor: pointer;
	}

	[contenteditable]:focus {
		outline: solid 1px var(--hover-border-color);
		border-radius: 3px;
		box-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--hover-box-shadow-color);
	}

	.simple-pagination ul {
		margin: 0 0 20px;
		padding: 0;
		list-style: none;
		text-align: center;
	}

	.simple-pagination li {
		display: inline-block;
		margin-right: 5px;

	}

	.simple-pagination li a,
	.simple-pagination li span {
		color: black;
		transition: all .3s;
		border: none;
		padding: 8px 16px;
		text-decoration: none;
		border-radius: 5px;
		font-size: 15px;
		background-color: transparent;
	}

	.simple-pagination li a:hover {
		background-color: var(--dot-color);
	}

	.simple-pagination .current {
		color: white;
		background-color: var(--link-hover-color);
		border-color: none;
	}

	.simple-pagination .prev.current,
	.simple-pagination .next.current {
		background: transparent;
		color: black;
	}

	#search-in-table {
		border: solid 1px var(--border-color) !important;
		border-radius: 8px !important;
		padding-left: 10px;
		width: 900px;
	}

	#search-in-table:active,
	#search-in-table:focus {
		border: solid 1px var(--hover-border-color) !important;
		box-shadow: var(--tw-ring-inset) 0 0 0 3px var(--hover-box-shadow-color) !important;
		outline: none !important;
	}

	.search-btn {
		padding: 10px 15px;
	}

	#search-area {
		display: none;
	}

	.single-selector::-webkit-scrollbar {
		width: 6px;
	}

	.single-selector::-webkit-scrollbar-thumb {
		background-color: var(--scrollbar-color);
		border-radius: 3px;
	}
</style>
<script>
	$(".pen-edit").on('click', function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$(`#profession-id-${id}`).attr('contenteditable', "true");
		$(`#profession-id-${id}`).focus();
		$(this).hide();
		$(`#btn-save-${id}`).show();

	});
	$(".pen-delete").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$(`#row-${id}`).remove();
		$.ajax({
			url: '{{ route("admin.delete-profession") }}',
			type: "POST",
			data: {
				'id': id,
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Удалили профессию!")
			},
			error: function(msg) {
				console.log("Не получилось удалить профессию")
			}
		});
	});
	$(".pen-save").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$(`#profession-id-${id}`).attr('contenteditable', "false");
		$(this).hide();
		$(`#btn-edit-${id}`).show();
		const new_profession_name = $(`#profession-id-${id}`).text();
		$.ajax({
			url: '{{ route("admin.edit-profession") }}',
			type: "POST",
			data: {
				'id': id,
				'profession_name': new_profession_name,
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Отредактировали профессию!")
			},
			error: function(msg) {
				console.log("Не получилось отредактировать профессию")
			}
		});
	});
	$("#add-profession").on('click', function() {
		$('#profession-modal').show();
		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$('#btn-close-profession').click(function() {
			$('#profession-modal').hide();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur")
		})
	})
	$('#btn-add-profession').click(function() {
		const profession_name = $('#profession-id').val();
		const subsphere_id = $('#normal-select-1').val();
		let all_professions = <?php echo json_encode($professions); ?>;
		all_professions = all_professions.filter(val => {
			return val.profession_name.toLowerCase() == String(profession_name).toLowerCase()
		})
		if (all_professions.length == 0 && profession_name != "") {
			$.ajax({
				url: '{{ route("admin.add-profession") }}',
				type: "POST",
				data: {
					'profession_name': profession_name,
					'subsphere_id': subsphere_id
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
			$('#add-profession-error').append(`<div class="alert alert-danger">Такая профессия уже существует</div>`);
		} else if (profession_name == "") {
			$('#add-profession-error').append(`<div class="alert alert-danger">Поле не может быть пустым</div>`);
		}
	})
	$("#search_input").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#table_professions tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});

	function paginate() {
		let items = $("#table_professions tbody tr").filter(":not(.hidden-el)");
		let numItems = items.length;
		let perPage = 10;
		items.slice(perPage).hide();
		$("#pagination").pagination({
			items: numItems,
			itemsOnPage: perPage,
			cssStyle: "light-theme",
			prevText: "«",
			nextText: "»",
			onPageClick: function(pageNumber) {
				let showFrom = perPage * (pageNumber - 1);
				let showTo = showFrom + perPage;
				items.hide()
					.slice(showFrom, showTo).show();

			}
		});
	}
	paginate();
	/**поиск по таблице */
	function search_in_table() {
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search-in-table");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_professions");
		tr = table.getElementsByTagName("tr");

		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					tr[i].classList.remove("hidden-el");

				} else {
					tr[i].classList.add("hidden-el");
					tr[i].style.display = "none";
				}
			}
		}

		/**пагинация */
		paginate();
	}

	$(".search-btn").on('click', function() {
		if ($("#search-area").css("display") == "none") {
			$("#search-area").show();
		} else {
			$("#search-area").hide();
			$("#search-in-table").val("");
		}

	})
</script>

</html>