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
let $skillsTable = document.getElementById("table_skills"),
	$n = 7,
	$rowCountskills = $skillsTable.rows.length,
	$tr_skills = [],
	$i, $ii, $j = 1,
	$th_skills = ($skillsTable.rows[(0)].outerHTML);
let $pageCountskills = Math.ceil($rowCountskills / $n);
if ($pageCountskills > 1) {
	for ($i = $j, $ii = 0; $i < $rowCountskills; $i++, $ii++)
		$tr_skills[$ii] = $skillsTable.rows[$i].outerHTML;
	$skillsTable.insertAdjacentHTML("afterend", "<div id='pagination-buttons-skills' style='margin-left:35px'></div");
	sort_skills(1);
}
function sort_skills($p) {
	let $rows = $th_skills,
		$s = (($n * $p) - $n);
	for ($i = $s; $i < ($s + $n) && $i < $tr_skills.length; $i++)
		$rows += $tr_skills[$i];

	$skillsTable.innerHTML = $rows;
	document.getElementById("pagination-buttons-skills").innerHTML = pageButtons($pageCountskills, $p, "skills");
	document.getElementById("skills-page-btn" + $p).setAttribute("class", "active-page-btn");
}