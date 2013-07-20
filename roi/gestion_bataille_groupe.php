<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require_once('haut_roi.php');

$bataille = new bataille($_GET['id_bataille']);
if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cette page vous est interdite.</p>';
else if(array_key_exists('mission', $_GET))
{
	if($_GET['id_groupe'] != 0 AND $_GET['id_groupe'] != '' AND $_GET['mission'] != '' AND $_GET['mission'] != 0)
	{
		$mission = new bataille_groupe_repere();
		$mission->set_id_repere($_GET['mission']);
		$mission->set_id_groupe($_GET['id_groupe']);
		$mission->accepter = 0;
		$mission->sauver();
		
		//Si la bataille est déjà lancée
		if ($bataille->get_etat() == 1)
		{
			//On envoi un message au groupe
			$groupe = new bataille_groupe($mission->get_id_groupe());
			$titre = 'Mission pour la bataille : '.$bataille->get_nom();
			$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->get_nom().'[br]
			[bataille:'.$bataille->get_nom().'][br][br]';
			// Si le groupe n'a pas deja son thread pour cette bataille
			if($groupe->get_id_thread() == 0)
			{
				$thread = new messagerie_thread(0, $groupe->get_id_groupe(), 0, $joueur->get_id(), 1, null, $titre);
				$thread->sauver();
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($thread->id_thread, 0, $titre, $message, $groupe->get_id_groupe(), 1);
				$groupe->set_id_thread($thread->id_thread);
				$groupe->sauver();
			}
			else
			{
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($groupe->get_id_thread(), 0, $titre, $message, $groupe->get_id_groupe(), 1);
			}
		}
	}
}
else if(array_key_exists('suppr_mission', $_GET))
{
	$mission = new bataille_groupe_repere($_GET['suppr_mission']);
	$mission->supprimer();
	echo "<h6>Mission supprimée avec succès</h6>";
}
else
{
	$groupes = $bataille->get_groupes();
	$reperes_bataille = $bataille->get_reperes('tri_type');
	foreach($groupes as $groupe)
	{
		$reperes_groupe = $groupe->get_reperes();
		echo '<div class="bataille_groupe"><fieldset>
		<legend>'.$groupe->get_nom().'</legend>';
		foreach($reperes_groupe as $repere_groupe)
		{
			$repere_groupe->get_repere()->get_type();
			if($repere_groupe->accepter == 0) $accepter = 'En attente d\'être acceptée';
			else $accepter = 'Acceptée';
			echo 'Mission : '.$repere_groupe->get_repere()->get_repere_type()->get_nom().' - X : '.$repere_groupe->get_repere()->get_x().' / Y : '.$repere_groupe->get_repere()->get_y().' - '.$accepter.' - <a href="#" onClick="return envoiInfo(\'gestion_bataille_groupe.php?suppr_mission='.$repere_groupe->get_id().'\', \'information_onglet_bataille\');">X</a><br />';
		
		$id_mission[$repere_groupe->get_id_repere()] = 1;
		}

		//On peut proposer une mission
?>
			<div id="liste_mission<?php echo $groupe->get_id(); ?>">
				<select name="mission<?php echo $groupe->get_id(); ?>" id="mission<?php echo $groupe->get_id(); ?>">
				<?php
				// Si aucune mission existe, ou qu'elles sont toutes deja assignées au groupe
				if (count($reperes_bataille['action']) == 0 OR count($reperes_bataille['action']) == count($id_mission))
					echo '<option disabled="disabled">Aucune mission disponible.</option>';
				else
				{
					foreach($reperes_bataille['action'] as $mission)
					{
						$mission->get_type();
						// Si elle n'est pas deja assignée au groupe
						if ($id_mission[$mission->get_id()] != 1)
						{
						?>
						<option value="<?php echo $mission->get_id(); ?>"><?php echo $mission->get_repere_type()->get_nom(); ?> en <?php echo $mission->get_x(); ?> / <?php echo $mission->get_y(); ?></option>
						<?php
						}
					}
				}
				?>
				</select>
				<input type="button" onclick="envoiInfo('gestion_bataille_groupe.php?id_bataille=<?php echo $bataille->get_id(); ?>&id_groupe=<?php echo $groupe->get_id(); ?>&mission='+$('#mission<?php echo $groupe->get_id(); ?>').val(), 'liste_mission<?php echo $groupe->get_id(); ?>');" value="Ok" />
			</div>
			<?php
		//On recupere le thread de messagerie
		if ($groupe->get_id_thread() != 0)
		{
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$thread = new messagerie_thread($groupe->get_id_thread());
			$thread_non_lu = $messagerie->get_thread_non_lu($thread->id_thread);
		?>
			<a href="#" <?php if($thread_non_lu>0) {echo "style='font-weight: bold;' ";} ?> id="thread_<?php echo $thread->id_thread; ?>" onclick="affichePopUp('messagerie.php?id_thread=<?php echo $thread->id_thread; ?>');">Messagerie (<?php echo $thread_non_lu; ?>)</a>
		<?php
		}
		
		echo '</fieldset></div>';
		unset($id_mission);
	}
}
?>
