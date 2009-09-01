<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
elseif(array_key_exists('action', $_GET))
{
	$action = new point_victoire_action($_GET['action']);
	if($royaume->get_point_victoire() >= $action->get_cout())
	{
		switch($action->get_type())
		{
			case 'famine' :
				$ids = array();
				$requete = "SELECT buff.id FROM perso LEFT JOIN buff ON buff.id_perso = perso.id WHERE buff.type = 'famine' AND race = '".$royaume->get_race()."'";
				$req = $db->query($requete);
				while($row = $db->read_row($req))
				{
					$ids[] = $row[0];
				}
				$ids_implode = implode(', ', $ids);
				$requete = "DELETE FROM buff WHERE id IN (".$ids_implode.")";
				$db->query($requete);
			break;
			case 'buff' :
				$duree = 3600 * 24 * 31;
				//On sélectionne tous les joueurs de ce royaume
				$requete = "SELECT id FROM perso WHERE race = '".$royaume->get_race()."' AND statut = 'actif'";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$requete = "INSERT INTO buff(`type`, `effet`, `effet2`, `fin`, `duree`, `id_perso`, `nom`, `description`, `debuff`, `supprimable`)
								VALUES('".$action->get_type_buff()."', ".$action->get_effet().", 0, ".(time()+$duree).", ".$duree.", ".$row['id'].", '".$action->get_nom()."', '".addslashes($action->get_description())."', 1, 0)";
				}
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
