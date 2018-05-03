<?php //  -*- tab-width:2; mode: php  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');

if( !array_key_exists('droits', $_SESSION) or !(((int)$_SESSION['droits']) & (joueur::droit_concept | joueur::droit_graph)))
{
  header("HTTP/1.1 403 Forbidden");
  die('<h1>Forbidden</h1>');
}

$admin = true;
$textures = false;

$customHead = 'css:../css/texture.css~../css/texture_low.css~../css/interfacev2.css~../css/admin.css~../css/prototip.css~//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css;script://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js~//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js~../javascript/fonction.js~../javascript/jquery/jquery.cluetip.min.js~../javascript/jquery/jquery.dataTables.min.js~admin.js~../javascript/jquery/jquery.ui.datepicker-fr.js;title:StarShine Admin';

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

global $db;
$selected = array_key_exists('id_monstre', $_REQUEST) ? (int)$_REQUEST['id_monstre'] : null;

if ($selected && array_key_exists('add', $_REQUEST)) {
	$db->param_query("insert into monster_quote(id_monstre, rarete, quote) values(?,?,?)",
									 array($selected, $_REQUEST['rarete'], $_REQUEST['quote']), 'iss');
}
if ($selected && array_key_exists('delete', $_REQUEST)) {
	$db->param_query("delete from monster_quote where id = ?",
									 array($_REQUEST['delete']), 'i');
}

$stm = $db->param_query("select id, nom, lib from monstre order by nom");
echo '<select id="monstre" onchange="doChangeMonstre(this)">';
if (!$selected) echo '<option value=""></option>';
while ($monstre = $db->stmt_read_object($stm)) {
	echo '<option value="'.$monstre->id.'"';
	if ($selected == $monstre->id) echo ' selected="selected"';
	echo '>'.$monstre->nom.'</option>';
}
echo '</select>';

?>
<script>
function doChangeMonstre(e) {
	if (e.value)
		window.location='?id_monstre=' + e.value;
}
</script>
<?php

if ($selected) {
	echo '<table><tr><th>Rareté</th><th>Paroles</th><th>Action</th></tr>';
	$stp = $db->param_query("select * from monster_quote where id_monstre = ? order by rarete asc",
													array($selected), 'i');
	while ($quote = $db->stmt_read_object($stp)) {
		echo '<tr><td>'.$quote->rarete.'</td><td>'.htmlentities($quote->quote).'</td><td><a href="?id_monstre='.
			$selected.'&amp;delete='.$quote->id.'">Supprimer</a></td></tr>';
	}
	echo '</table>';
	?>
<hr />
<form action="?id_monstre=<?php echo $selected ?>" method="post">
	 <table>
	   <tr><th>Ajouter</th></tr>
	   <tr>
	     <td><label for="irarete">Rareté</label></td>
       <td><input id="irarete" type="text" name="rarete" /></td>
		 </tr>
		 <tr>
       <td><label for="iquote">Dernières paroles</label></td>
       <td><textarea id="iquote" name="quote"></textarea></td>
     </tr>
     <tr>
       <td></td>
       <td><input value="ajouter" type="submit" /><input type="hidden" name="add" value="add" /></td>
		 </tr>
  </table>
</form>
<?php
}

