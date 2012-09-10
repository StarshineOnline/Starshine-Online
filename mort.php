<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'haut.php');

$joueur = new perso($_SESSION['ID']);
$choix = $_GET['choix'];

if($choix == 1)
{
	$R = new royaume($Trace[$joueur->get_race()]['numrace']);
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
	$db->query($requete);
	//Ville
	$pv = 20;
	if ($R->is_raz()) $pv = 5;
	verif_mort($pv, 2, 43200, 2);
}
elseif($choix == 2)
{
	if(array_key_exists('rez', $_GET))
	{
		$requete = "SELECT pourcent, duree, malus, id_rez FROM rez WHERE id = '".sSQL($_GET['rez'])."' AND id_perso = ".$joueur->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if(is_array($row))
		{
			$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
			$db->query($requete);
			//Rez
			verif_mort($row['pourcent'], 3, $row['duree'], $row['malus']);
		
		$rezzeur = new perso($row['id_rez']);
		$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rez', '".$rezzeur->get_nom()."', '', NOW(), '', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
        $db->query($requete);
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$rezzeur->get_id().", 'rrez', '".$joueur->get_nom()."', '', NOW(), '', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
        $db->query($requete);
		}
	}
}
elseif($choix == 3)
{
	$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
	$db->query($requete);
  // ??
	$R = new royaume($Trace[$joueur->get_race()]['numrace']);
	$R->verif_hp();
	if($R->get_capitale_hp() > 0 || $R->is_raz()) $malus = 15;
	else $malus = 5;
	//Fort
	verif_mort($malus, 4, 43200, 2);
}
 
?>
<img src="image/pixel.gif" onload="window.location = 'interface.php';" />
