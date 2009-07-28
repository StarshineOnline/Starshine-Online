<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'haut.php');
for($i = 1; $i <= 12; $i++)
{
	if($i != 5)
	{
		$requete = "INSERT INTO `construction_ville` ( `id` , `id_royaume` , `id_batiment` ) VALUES (NULL , '".$i."', '1');";
		$db->query($requete);
		$requete = "INSERT INTO `construction_ville` ( `id` , `id_royaume` , `id_batiment` ) VALUES (NULL , '".$i."', '2');";
		$db->query($requete);
		$requete = "INSERT INTO `construction_ville` ( `id` , `id_royaume` , `id_batiment` ) VALUES (NULL , '".$i."', '3');";
		$db->query($requete);
		$requete = "INSERT INTO `construction_ville` ( `id` , `id_royaume` , `id_batiment` ) VALUES (NULL , '".$i."', '4');";
		$db->query($requete);
	}
}
?>