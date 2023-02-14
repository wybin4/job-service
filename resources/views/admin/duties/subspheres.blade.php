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
<div id="add-subsphere-area">
	<div class="modal" id="subsphere-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="little-header-text text-center">Добавить область</h2>
				</div>
				<div class="modal-body">
					<div id="add-subsphere-error"></div>
					<x-label :value="__('Название области')" />
					<x-input autocomplete="off" id="subsphere-id" type="text" style="width:400px" placeholder="Например: мобильная разработка" />
					<div style="margin-top:20px">
						<x-label for="normal-select-1" :value="__('Сфера')" />
						<select id="normal-select-1" class="single-selector" placeholder-text="ㅤ">
							@foreach ($spheres as $sphere)
							<option value="{{$sphere->id}}" class="select-dropdown__list-item">{{$sphere->sphere_of_activity_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<span type="button" class="span-like-button" id="btn-close-subsphere" data-bs-dismiss="modal">Отменить</span>
					<span type="button" class="span-like-button" id="btn-add-subsphere">Добавить</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="blurable-content">
	<x-admin-layout>
		<div id="top-panel">
			<div class="row">
				<div class="col-sm-6">
					<h2 class="header-text">Области</h2>
				</div>
				<div class="col-sm-2">
					<span id="add-subsphere" class="span-like-button">
						Добавить область
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
				<table class="table" id="table_subspheres">
					<thead>
						<tr>
							<th scope="col" class="header">Название области</th>
							<th scope="col" class="header">Название сферы</th>
							<th scope="col" class="header">Действия</th>
							<th>ㅤ</th>
						</tr>
					</thead>
					<tbody>
						@csrf

						@foreach($all_subspheres as $subsphere)
						<tr id="row-{{$subsphere->id}}">
							<td id="subsphere-id-{{$subsphere->id}}">{{$subsphere->subsphere_of_activity_name}}</td>
							<td id="sphere-id-{{$subsphere->id}}">@foreach ($spheres->where('id', $subsphere->sphere_id) as $arr) {{$arr->sphere_of_activity_name}}@endforeach</td>
							<td style="color:var(--link-hover-color);"><i class="fa-solid fa-lock"></i></td>
							<td></td>
						</tr>
						@endforeach
						@foreach($exclude_subspheres as $subsphere)
						<tr id="row-{{$subsphere->id}}">
							<td id="subsphere-id-{{$subsphere->id}}">{{$subsphere->subsphere_of_activity_name}}</td>
							<td id="sphere-id-{{$subsphere->id}}">@foreach ($spheres->where('id', $subsphere->sphere_id) as $arr) {{$arr->sphere_of_activity_name}}@endforeach</td>
							<td><i id="btn-edit-{{$subsphere->id}}" class="fa-solid fa-pen-to-square pen-edit"></i><i id="btn-save-{{$subsphere->id}}" style="display:none" class="fa-regular fa-circle-check pen-save"></i></td>
							<td><i id="btn-delete-{{$subsphere->id}}" class="fa-solid fa-trash pen-delete"></i></td>
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

	#add-subsphere {
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
		$(`#subsphere-id-${id}`).attr('contenteditable', "true");
		$(`#subsphere-id-${id}`).focus();
		$(this).hide();
		$(`#btn-save-${id}`).show();

	});
	$(".pen-delete").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$(`#row-${id}`).remove();
		$.ajax({
			url: '{{ route("admin.delete-subsphere") }}',
			type: "POST",
			data: {
				'id': id,
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Удалили область!")
			},
			error: function(msg) {
				console.log("Не получилось удалить область")
			}
		});
	});
	$(".pen-save").click(function() {
		let id = ($(this).attr('id')).split('-');
		id = id[id.length - 1];
		$(`#subsphere-id-${id}`).attr('contenteditable', "false");
		$(this).hide();
		$(`#btn-edit-${id}`).show();
		const new_subsphere_of_activity_name = $(`#subsphere-id-${id}`).text();
		$.ajax({
			url: '{{ route("admin.edit-subsphere") }}',
			type: "POST",
			data: {
				'id': id,
				'subsphere_of_activity_name': new_subsphere_of_activity_name,
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data) {
				console.log("Отредактировали область!")
			},
			error: function(msg) {
				console.log("Не получилось отредактировать область")
			}
		});
	});
	$("#add-subsphere").on('click', function() {
		$('#subsphere-modal').show();
		//запрещаем скролл
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
		//добавляем блюр
		$('#blurable-content').addClass("blur");
		$('#btn-close-subsphere').click(function() {
			$('#subsphere-modal').hide();
			// восстанавливаем скролл
			$('html, body').css({
				overflow: 'auto',
				height: 'auto'
			});
			//убираем блюр
			$('#blurable-content').removeClass("blur")
		})
	})
	$('#btn-add-subsphere').click(function() {
		const subsphere_of_activity_name = $('#subsphere-id').val();
		const sphere_id = $('#normal-select-1').val();
		let all_subspheres = <?php echo json_encode($subspheres); ?>;
		all_subspheres = all_subspheres.filter(val => {
			return val.subsphere_of_activity_name.toLowerCase() == String(subsphere_of_activity_name).toLowerCase()
		})
		if (all_subspheres.length == 0 && subsphere_of_activity_name != "") {
			$.ajax({
				url: '{{ route("admin.add-subsphere") }}',
				type: "POST",
				data: {
					'subsphere_of_activity_name': subsphere_of_activity_name,
					'sphere_id': sphere_id
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(data) {
					console.log("Добавили область!")
				},
				error: function(msg) {
					console.log("Не получилось добавить область")
				}
			});
			// перезагружаем страницу, чтобы применить изменения
			location.reload();
		} else if (all_subspheres.length !== 0) {
			$('#add-subsphere-error').append(`<div class="alert alert-danger">Такая область уже существует</div>`);
		} else if (subsphere_of_activity_name == "") {
			$('#add-subsphere-error').append(`<div class="alert alert-danger">Поле не может быть пустым</div>`);
		}
	})
	$("#search_input").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#table_subspheres tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});

	function paginate() {
		let items = $("#table_subspheres tbody tr").filter(":not(.hidden-el)");
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
		table = document.getElementById("table_subspheres");
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