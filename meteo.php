<?php
if (file_exists('root.php')) include_once('root.php');

require_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
switch($_GET['action'])
{
	case 'atm' :
	{
		$requete = false;
		$val = sSQL($_GET['val']);
		switch ($_GET['effet'])
		{
			case 'sky':
				$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
				$_SESSION['ID'].", 'desactive_atm', $val)";
			break;
			case 'time':
				$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
				$_SESSION['ID'].", 'desactive_atm_all', $val)";
			break;
			default:
				echo "<h5>Erreur de parametre</h5>";
			break;
		}
		$db->query($requete);
	}
}
$atm_val = 1;
$atm_all_val = 1;
$q = $db->query("select nom, valeur from options where ".
				"id_perso = ".$joueur->get_id()." and nom in ".
				"('desactive_atm', 'desactive_atm_all')");
if ($q)
{
	while ($row = $db->read_row($q))
	{
		switch ($row[0])
		{
			case 'desactive_atm':
				$atm_val = $row[1] ? 0 : 1;
			break;
			case 'desactive_atm_all':
				$atm_all_val = $row[1] ? 0 : 1;
			break;
		}
	}
}
$atm_verb = $atm_val ? 'Désactiver' : 'Activer';
$atm_all_verb = $atm_all_val ? 'Désactiver <strong>tous</strong>' : 'Activer';
?>
<ul>
<?php
if (isset($G_use_atmosphere) && $G_use_atmosphere) { ?>
					  <li><a href="meteo.php?action=atm&amp;effet=sky&amp;val=<?php echo $atm_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', cache_monstre, affiche_royaume,'normal', show_only); return false;"><?php echo $atm_verb; ?> les effets atmospheriques</a></li>
					  <li><a href="meteo.php?action=atm&amp;effet=time&amp;val=<?php echo $atm_all_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', cache_monstre, affiche_royaume,'normal', show_only); return false;"><?php echo $atm_all_verb; ?> les effets atmosphériques et liés à l'heure</a></li>
<?php } ?>
</ul>