<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'haut.php');

$joueur = recupperso($_SESSION['ID']);
$choix = $_GET['choix'];
if($choix == 1)
{
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur['ID'];
	$db->query($requete);
	//Fort
	verif_mort(20, 2, 43200, 2);
}
elseif($choix == 2)
{
	if(array_key_exists('rez', $_GET))
	{
		$requete = "SELECT pourcent, duree, malus FROM rez WHERE id = '".sSQL($_GET['rez'])."' AND id_perso = ".$joueur['ID'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if(is_array($row))
		{
			$requete = "DELETE FROM rez WHERE id_perso = ".$joueur['ID'];
			$db->query($requete);
			//Rez
			verif_mort($row['pourcent'], 3, $row['duree'], $row['malus']);
		}
	}
}
elseif($choix == 3)
{
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur['ID'];
	$db->query($requete);
	//Ville
	verif_mort(15, 4, 43200, 2);
}
?>
<img src="image/pixel.gif" onload="window.location = 'jeu2.php';" />