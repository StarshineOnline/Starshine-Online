<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

session_start();
$pnjs = array('Minotaure');
$is_admin = ($_SESSION["admin_db_auth"] == 'admin') ? true : false;

if (array_key_exists('controle', $_GET)) { 
	if ($is_admin || array_key_exists($_GET['controle'], $pnjs)) {
		header('Location: ../interface.php');
		$_SESSION['nom'] = $_GET['controle'];
		$req = $db->query('select id from perso where nom = \''.
											sSQL($_GET['controle']).'\'');
		$row = $db->read_object($req);
		$_SESSION['ID'] = $row->id;
		exit(0);
	}
	else {
		security_block(URL_MANIPULATION);
	}
}

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');
echo '<div style="margin-left: 200px">';

$requete = 'select nom,race from perso';
if (!$is_admin) {
	$p = array();
	foreach ($pnjs as $pp) $p[] = '\''.sSQL($pp).'\'';
	$strpnjs = implode(',', $p);
	$requete .= " where nom in ($strpnjs)";
}
$req = $db->query($requete);

echo '<table id="perso"><thead><tr><th>Nom</th><th>Race</th></tr></thead><tbody>';
while ($row = $db->read_object($req)) {
	echo "<tr><td><a href=\"?controle=$row->nom\">$row->nom</td><td>$row->race</td></tr>\n";
}
echo '</tbody></table>';
print_js_onload('$("#perso").dataTable({"sPaginationType": "full_numbers"});');

echo '</div>';
include_once(root.'admin/admin_bas.php');

?>