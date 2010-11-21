<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cette page vous est interdit</p>';
elseif(array_key_exists('action', $_GET))
{
	$action = new point_victoire_action($_GET['action']);
	if($royaume->get_point_victoire() >= $action->get_cout())
	{
		$persos = "SELECT id FROM perso WHERE race = '".$royaume->get_race()."' AND statut = 'actif'";
		switch($action->get_type())
		{
			case 'famine' :
				$requete = "DELETE FROM buff WHERE type = 'famine' AND id_perso IN ($persos)";
				$db->query($requete);
				echo '<h6>La famine a bien été supprimée</h6>';
			break;
			case 'remove_buff' :
				$requete = "DELETE FROM buff WHERE type = '".$action->get_effet()."' AND id_perso IN ($persos)";
				$db->query($requete);
				echo '<h6>'.$action->get_effet().' a bien été supprimé(e)</h6>';
			break;
			case 'remove_buffs' :
				$requete = "DELETE FROM buff WHERE type IN (".$action->get_effet().") AND id_perso IN ($persos)";
				$db->query($requete);
				echo '<h6>'.$action->get_effet().' a bien été supprimé(e)</h6>';
			break;
			case 'buff' :
			  $duree = 3600 * 24 * 31;
			  $requete = "INSERT INTO buff(`type`, `effet`, `effet2`, `fin`, `duree`, `id_perso`, `nom`, `description`, `debuff`, `supprimable`)
								SELECT '".$action->get_type_buff()."', ".$action->get_effet().", 0, ".(time()+$duree).", ".$duree.", id, '".$action->get_nom()."', '".addslashes($action->get_description())."', 1, 0
                FROM ($persos) persos";
				$db->query($requete);
			  echo '<h6>Votre royaume bénéficie maintenant du buff : '.$action->get_nom().'</h6>';
			break;
		}
		$royaume->set_point_victoire($royaume->get_point_victoire() - $action->get_cout());
		$royaume->sauver();
	}
	else echo '<h5>Vous n\'avez pas assez de point de victoire</h5>';
}
else
{
?>
<div id='point_victoire'>
	Point de victoire : <?php echo $royaume->get_point_victoire(); ?>
	<table>
	<tr>
		<td>Nom</td>
		<td>Coût</td>
		<td>Description</td>
	</tr>
	<?php
	$actions = point_victoire_action::create(0, 0);
	foreach($actions as $action)
	{
		if($action->get_type() == 'buff') $description = 'Buff l\'ensemble des joueurs de votre royaume avec : "'.$action->get_description().'"';
		else $description = $action->get_description();
		echo '
		<tr>
			<td><a href="point_victoire.php?action='.$action->get_id().'" onclick="return envoiInfo(this.href, \'contenu_jeu\');">'.$action->get_nom().'</a></td>
			<td>'.$action->get_cout().'</td>
			<td>'.$description.' </td>
		</tr>';
	}
	?>
	</table>
</div>
<?php
}
?>
