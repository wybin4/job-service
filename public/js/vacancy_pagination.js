// общие кнопки для пагинации
function pageButtons($pCount, $cur, $name) {
	let $prevDis = ($cur == 1) ? "disabled" : "",
		$nextDis = ($cur == $pCount) ? "disabled" : "",
		$buttons = "<input type='button' value=&laquo; onclick='sort_" + $name + "(" + ($cur - 1) + ")' " + $prevDis + ">";
	for ($i = 1; $i <= $pCount; $i++)
		$buttons += "<input type='button' id='" + $name + "-page-btn" + $i + "'value='" + $i + "' onclick='sort_" + $name + "(" + $i + ")'>";
	$buttons += "<input type='button' value=&raquo; onclick='sort_" + $name + "(" + ($cur + 1) + ")' " + $nextDis + ">";
	return $buttons;
}

// для пагинации по всем вакансиям
let $allVacanciesTable = document.getElementById("all-vacancies"),
	$n = 7,
	$i, $ii, $j = 1,
	$rowCountAll = $allVacanciesTable.rows.length,
	$tr_all = [],
	$th_all = ($allVacanciesTable.rows[(0)].outerHTML);
let $pageCountAll = Math.ceil($rowCountAll / $n);
if ($pageCountAll > 1) {
	for ($i = $j, $ii = 0; $i < $rowCountAll; $i++, $ii++)
		$tr_all[$ii] = $allVacanciesTable.rows[$i].outerHTML;
	$allVacanciesTable.insertAdjacentHTML("afterend", "<div id='pagination-buttons-all' style='margin-left:35px'></div");
	sort_all(1);
} 



// все заявки
function sort_all($p) {
	let $rows = $th_all,
		$s = (($n * $p) - $n);
	for ($i = $s; $i < ($s + $n) && $i < $tr_all.length; $i++)
		$rows += $tr_all[$i];

	$allVacanciesTable.innerHTML = $rows;
	document.getElementById("pagination-buttons-all").innerHTML = pageButtons($pageCountAll, $p, "all");
	document.getElementById("all-page-btn" + $p).setAttribute("class", "active-page-btn");
}

// для пагинации по принятым заявкам

$n = 7;
$i, $ii, $j = 1;
let $activeVacanciesTable = document.getElementById("active-vacancies"),
	$rowCountactive = $activeVacanciesTable.rows.length,
	$tr_active = [],
	$th_active = ($activeVacanciesTable.rows[(0)].outerHTML);
let $pageCountactive = Math.ceil($rowCountactive / $n);
if ($pageCountactive > 1) {
	for ($i = $j, $ii = 0; $i < $rowCountactive; $i++, $ii++)
		$tr_active[$ii] = $activeVacanciesTable.rows[$i].outerHTML;
	$activeVacanciesTable.insertAdjacentHTML("afterend", "<div id='pagination-buttons-active' style='margin-left:35px'></div");
	sort_active(1);
}



// принятые заявки
function sort_active($p) {
	let $rows = $th_active,
		$s = (($n * $p) - $n);
	for ($i = $s; $i < ($s + $n) && $i < $tr_active.length; $i++)
		$rows += $tr_active[$i];

	$activeVacanciesTable.innerHTML = $rows;
	document.getElementById("pagination-buttons-active").innerHTML = pageButtons($pageCountactive, $p, "active");
	document.getElementById("active-page-btn" + $p).setAttribute("class", "active-page-btn");
}

// для пагинации по непринятым заявкам
$n = 7;
$i, $ii, $j = 1;
let $archiveVacanciesTable = document.getElementById("archive-vacancies"),
	$pageCountArchive = $archiveVacanciesTable.rows.length,
	$tr_archive = [],
	$th_archive = ($archiveVacanciesTable.rows[(0)].outerHTML);
let $pageCountarchive = Math.ceil($pageCountArchive / $n);
if ($pageCountarchive > 1) {
	for ($i = $j, $ii = 0; $i < $pageCountArchive; $i++, $ii++)
		$tr_archive[$ii] = $archiveVacanciesTable.rows[$i].outerHTML;
	$archiveVacanciesTable.insertAdjacentHTML("afterend", "<div id='pagination-buttons-archive' style='margin-left:35px'></div");
	sort_archive(1);
}



// непринятые заявки
function sort_archive($p) {
	let $rows = $th_archive,
		$s = (($n * $p) - $n);
	for ($i = $s; $i < ($s + $n) && $i < $tr_archive.length; $i++)
		$rows += $tr_archive[$i];

	$archiveVacanciesTable.innerHTML = $rows;
	document.getElementById("pagination-buttons-archive").innerHTML = pageButtons($pageCountarchive, $p, "archive");
	document.getElementById("archive-page-btn" + $p).setAttribute("class", "active-page-btn");
}