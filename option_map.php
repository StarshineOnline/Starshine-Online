<?php // -*- mode: php -*-
if (file_exists('root.php')) include_once('root.php');

require_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$roy_val = $joueur->get_option('affiche_royaume') ? 0 : 1;
$mons_val = $joueur->get_option('cache_monstre') ? 0 : 1;
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
	break;

  case 'affiche_royaumes':
  {
		$val = sSQL($_GET['val'], SSQL_INTEGER);
		$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
			$_SESSION['ID'].", 'affiche_royaume', $val)";
		$db->query($requete);
		print_js_onload("deplacement('centre');");
		$roy_val = $roy_val ? 0 : 1;
	}
	break;

  case 'cache_monstre':
  {
		$val = sSQL($_GET['val'], SSQL_INTEGER);
		$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
			$_SESSION['ID'].", 'cache_monstre', '$val')";
		$db->query($requete);
		print_js_onload("deplacement('centre');");
		$mons_val = $mons_val ? 0 : 1;
	}
	break;

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
<ul class='option_map'>
<?php
if (isset($G_use_atmosphere) && $G_use_atmosphere) { ?>
					  <li><a href="option_map.php?action=atm&amp;effet=sky&amp;val=<?php echo $atm_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', 'normal', show_only); return false;">effets </a></li>
					  <li><a href="option_map.php?action=atm&amp;effet=time&amp;val=<?php echo $atm_all_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', 'normal', show_only); return false;">temps</a></li>
<?php } ?>
					  <li><a href="option_map.php?action=affiche_royaumes&amp;val=<?php echo $roy_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', 'normal', show_only); return false;">royaumes</a></li>
					  <li><a href="option_map.php?action=cache_monstre&amp;val=<?php echo $mons_val; ?>" onclick="envoiInfo(this.href, 'cluetip-inner'); deplacement('centre', 'normal', show_only); return false;">monstres</a></li>
</ul>