<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	$ips = array();
	$time = time();
	$duree_jour = 3600 * 24;
	if(!array_key_exists('jour', $_GET)) $jour = 7;
	else $jour = $_GET['jour'];
	$time -= $duree_jour * $jour;
	?>
<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Personnage ayant la même IP (durant les <?php echo $jour; ?> derniers jours)
	</div>

			<a href="admin_2.php?jour=7">7 jours</a> | <a href="admin_2.php?jour=15">15 jours</a> | <a href="admin_2.php?jour=30">30 jours</a>
	<?php
	//Recherche des personnage ayant la même IP
	$requete = "SELECT * , COUNT(*) as tot FROM log_connexion WHERE message = 'Ok' AND time >= ".$time." GROUP BY id_joueur, ip";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		if(!in_array($row['ip'], $ips)) $ips[$row['ip']][$row['id_joueur']] = $row['tot'];
	}
	$i = 0;
	$count = count($ips);
	$keys = array_keys($ips);
	while($i < $count)
	{
		$countj = count($ips[$keys[$i]]);
		if($countj >= 2)
		{
			echo '<h3>Liste des personnages pour l\'IP : '.$keys[$i].'</h3>
			<ul class="ville">';
			$j = 0;
			$keysj = array_keys($ips[$keys[$i]]);
			while($j < $countj)
			{
				$perso = new perso($keysj[$j]);
				echo '<li><a href="admin_joueur.php?direction=info_joueur&amp;id='.$perso->get_id().'">'.$perso->get_nom().'</a> <span class="xsmall">('.$ips[$keys[$i]][$keysj[$j]].' connexion)</span></li>';
				$j++;
			}
			echo '</ul>';
		}
		$i++;
	}
}
	?>
		</div>
	<?php
	include_once(root.'bas.php');

?>
