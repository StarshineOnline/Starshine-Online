<?php
require('haut_roi.php');

$bataille = new bataille($_GET['id_bataille']);
if($joueur['rang'] != 7)
	echo '<p>Cheater!</p>';
else if(array_key_exists('mission', $_GET))
{
	if($_GET['id_groupe'] != 0 AND $_GET['id_groupe'] != '')
	{
		$mission = new bataille_groupe_repere();
		$mission->id_repere = $_GET['mission'];
		$mission->id_groupe = $_GET['id_groupe'];
		$mission->accepter = 0;
		$mission->sauver();
		//On envoi un message au chef du groupe
		$messagerie = new messagerie($joueur['ID']);
		$titre = 'Mission pour la bataille : '.$bataille->nom;
		$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->nom;
		$messagerie->envoi_message(0, 4867, $titre, $message, 0, 1);
	}
	else echo 'Erreur du numéro du groupe ?!';
}
else
{
	$bataille->get_groupes();
	$bataille->get_reperes('tri_type');
	
	foreach($bataille->groupes as $groupe)
	{
		$groupe->get_reperes();
		echo '<div class="bataille_groupe">
		'.$groupe->get_nom().'<br />';
		foreach($groupe->reperes as $repere)
		{
			$repere->get_repere();
			$repere->repere->get_type();
			if($repere->accepter == 0) $accepter = 'En attente d\'être acceptée';
			else $accepter = 'Acceptée';
			echo 'Mission : '.$repere->repere->repere_type->nom.' X : '.$repere->repere->x.' / Y : '.$repere->repere->y.' - '.$accepter.'<br />';
		}
		//On peut proposer une mission
		if(count($groupe->reperes) == 0)
		{
			?>
			<div id="liste_mission<?php echo $groupe->id; ?>">
				<select name="mission<?php echo $groupe->id; ?>" id="mission<?php echo $groupe->id; ?>">
				<?php
				foreach($bataille->reperes['action'] as $mission)
				{
					$mission->get_type();
					?>
					<option value="<?php echo $mission->id; ?>"><?php echo $mission->repere_type->nom; ?> en <?php echo $mission->x; ?> / <?php echo $mission->y; ?></option>
					<?php
				}
				?>
				</select> <input type="button" onclick="envoiInfo('gestion_bataille_groupe.php?id_bataille=<?php echo $bataille->id; ?>&id_groupe=<?php echo $groupe->id; ?>&mission=' + $('mission<?php echo $groupe->id; ?>').value, 'liste_mission<?php echo $groupe->id; ?>');" value="Ok" />
			</div>
			<?php
		}
		echo '</div>';
	}
}
?>