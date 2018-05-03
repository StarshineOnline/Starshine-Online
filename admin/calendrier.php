<?php //  -*- tab-width:2; mode: php  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
if ($G_maintenance) die('maintenance');
include_once(root.'admin/menu_admin.php');

$req = $db->query("select * from calendrier order by date desc");

?>
<table>
<tr><th>Date</th><th>Intervalle</th><th>SQL</th><th>code</th><th>Script</th><th>id_manuel</th></tr>
<?php

if ($req) 
	while ($row = $db->read_object($req)) {
		$c = $row->done ? 'cal_activf' : 'cal_inactif';
		echo '<tr class="'.$c.'"><td>'.$row->date.'</td><td>'.$row->next.'</td><td>';
		if ($row->sql) 
			echo ' - <img src="../image/icone/mobinfo.png" alt="SQL" '.
				print_tooltip($row->sql).' />';
		echo '</td><td>';
		if ($row->eval) 
			echo ' - <img src="../image/icone/mobinfo.png" alt="Code" '.
				print_tooltip($row->eval).' />';
		echo '</td><td>';
		if ($row->script) 
			echo ' - <img src="../image/icone/mobinfo.png" alt="script" '.
				print_tooltip($row->script).' />';
		echo "</td><td>$row->id_manuel</td></tr>\n";
	}

?>
</table>
<?php
print_tooltipify();

