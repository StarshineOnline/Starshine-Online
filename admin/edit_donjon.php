<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');
echo '<h1>Donjons</h1>';

$requete = "SELECT * FROM donjon";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_map_full.php?xmin='.$row['x_donjon'].'&amp;ymin='.$row['y_donjon'].'">'.$row['nom'].'</a><br />';
}

$requete = "SELECT * FROM donjon_entry_point";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_map_full.php?xmin='.$row['x'].'&amp;ymin='.$row['y'].'">'.$row['nom'].'</a><br />';
}

echo '<h1>Arènes</h1>';

$requete = "SELECT * FROM arenes";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_map_full.php?xmin='.($row['x'] - 1).'&amp;ymin='.($row['y'] - 1).'&amp;xmax='.($row['x'] + $row['size'] + 1).'&amp;ymax='.($row['y'] + $row['size'] + 1).'&amp;arene='.$row['nom'].'">'.$row['nom'].'</a><br />';
}

?>