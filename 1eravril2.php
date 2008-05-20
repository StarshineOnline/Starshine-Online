<?php
include('haut.php');
$requete = "SELECT ID FROM perso WHERE statut = 'actif' AND LEVEL >=5 AND hp > 0 AND x =24 AND y =209";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$requete = "INSERT INTO `titre_honorifique` ( `id` , `id_perso` , `titre` ) VALUES (NULL , ".$row['ID'].", 'Survivant du poisson')";
	//$db->query($requete);
}
$file = file('1eravril.txt');
$persos = explode(';', $file[0]);
foreach($persos as $perso)
{
	$stats = explode(',', $perso);
	$stats[0] = $stats[0] * 1;
	if($stats[0] != 0)
	{
		$requete = "UPDATE perso SET hp = ".$stats[1].", mp = ".$stats[2].", x = ".$stats[3].", y = ".$stats[4]." WHERE ID = ".$stats[0];
		//$db->query($requete);
	}
}
?>
