<?php
include('haut.php');
for($i = 1; $i < 151; $i++)
{
	for($j = 1; $j < 151; $j++)
	{
		$requete = "INSERT INTO map VALUES (".(($i * 1000) + $j).", 1, 0, 0, 0)";
		$db->query($requete);
	}
}
?>