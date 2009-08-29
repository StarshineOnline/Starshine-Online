<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'haut.php');

$joueur = new perso($_SESSION['ID']);
$choix = $_GET['choix'];
if($choix == 1)
{
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
	$db->query($requete);
	//Fort
	verif_mort(20, 2, 43200, 2);
}
elseif($choix == 2)
{
	if(array_key_exists('rez', $_GET))
	{
		$requete = "SELECT pourcent, duree, malus FROM rez WHERE id = '".sSQL($_GET['rez'])."' AND id_perso = ".$joueur->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if(is_array($row))
		{
			$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
			$db->query($requete);
			//Rez
			verif_mort($row['pourcent'], 3, $row['duree'], $row['malus']);
		}
	}
}
elseif($choix == 3)
{
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
	$db->query($requete);
	$R = new royaume($Trace[$joueur->get_race()]['numrace']);
	$R->verif_hp();
	if($R->get_capitale_hp() > 0) $malus = 15;
	else $malus = 5;
	//Ville
	verif_mort($malus, 4, 43200, 2);
}
?>
<img src="image/pixel.gif" onload="window.location = 'jeu2.php';" />
