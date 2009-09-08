<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

$requete = "SELECT * FROM donjon";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_donjon2.php?xmin='.$row['x_donjon'].'&amp;ymin='.$row['y_donjon'].'">'.$row['nom'].'</a><br />';
}


?>