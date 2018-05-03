<?php /* -*- mode: php -*- */
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
//if (!$check) exit (0);
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
if ($G_maintenance)
{
  echo 'Starshine-online est actuellement en cours de mise à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	if (array_key_exists('mode', $_GET))
	{
		$stop = true;
		switch ($_GET['mode'])
		{
		case 'new':
			?>
<form id="newpnj">
	 Nom: <input type="text" name="nom"/><br/>
	 Image: <input type="text" name="img"/>.png<br/>
	 X: <input type="text" name="x"/>
	 Y: <input type="text" name="y"/><br/>
	 Texte: <textarea name="txt" cols="80" rows="24"></textarea>
</form><input type="button" value="envoyer" onclick="doNewPnj()"/>
			<?php
			break;
		case 'donew':
			if ($_POST['txt'] == '' || $_POST['nom'] == '' || $_POST['x'] == ''
					|| $_POST['y'] == '' || $_POST['img'] == '') {
				echo '<strong>Les parametres sont obligatoires</strong>';
				break;
			}
			$txt = '\''.sSQL($_POST['txt']).'\'';
			$nom = '\''.sSQL($_POST['nom']).'\'';
			$db->query("insert into pnj (nom, x, y, texte, image) values ".
								 "($nom, $_POST[x], $_POST[y], $txt, '$_POST[img]')");
			echo "Operation effectuée";
			print_js_onload('window.location.href=window.location.href');
			break;
		case 'update':
			$txt = '\''.sSQL($_POST['txt']).'\'';
			$db->query("update pnj set x = $_POST[x], y = $_POST[y], ".
								 "texte = $txt, image = '$_POST[img]' where id = $_POST[id]");
			echo "Operation effectuée";
			break;
		}
		if ($stop) exit (0);
	}

  include_once(root.'admin/menu_admin.php');
  
  $req = $db->query('select * from pnj');
  
  echo '<table><tr><th>Nom / image</th><th>X / Y</th><th>Texte</th><th></th></tr>';

  while ($row = $db->read_object($req)) {
    echo "<tr><form id=\"pnj_$row->id\"><td>";
		echo "<img src=\"../image/pnj/$row->image.png\"> $row->nom<br/>";
		echo "<input type=\"text\" value=\"$row->image\" name=\"img\" size=\"12\">.png</td><td>";
    echo "<input type=\"text\" value=\"$row->x\" name=\"x\" size=\"4\"/>";
		echo "<input type=\"hidden\" value=\"$row->id\" name=\"id\"/>";
    echo "<input type=\"text\" value=\"$row->y\" name=\"y\" size=\"4\"/></td>";
    echo "<td><textarea name=\"txt\" cols=\"80\">$row->texte</textarea></td>";
    echo "<td><input type=\"button\" onclick=\"updatePNJ($row->id)\" value=\"Update\"/></td>";
    echo "</form></tr>";
  }

  echo '</table>';

	echo '<input type="button" onclick="newPNJ()" value="nouveau" />';

	// FIN !!
	echo '<div id="upd_res"></div>';
}
include_once(root.'admin/admin_bas.php');
?>