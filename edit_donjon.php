<?php

include('haut.php');
$cfg["sql"]['host'] = "localhost";
$cfg["sql"]['user'] = "starshine";
$cfg["sql"]['pass'] = "ilove50";
$cfg["sql"]['db'] = "starshine";
$db = new db();

$requete = "SELECT * FROM donjon";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<a href="edit_donjon2.php?xmin='.$row['x_donjon'].'&amp;ymin='.$row['y_donjon'].'">'.$row['nom'].'</a><br />';
}

?>