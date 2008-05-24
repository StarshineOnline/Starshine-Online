<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
include('menu_admin.php');

$requete = "SELECT * FROM donjon";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_donjon2.php?xmin='.$row['x_donjon'].'&amp;ymin='.$row['y_donjon'].'">'.$row['nom'].'</a><br />';
}


?>