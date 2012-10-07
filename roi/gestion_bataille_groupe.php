<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require_once('haut_roi.php');

$bataille = new bataille($_GET['id_bataille']);
if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cheater</p>';
else if(array_key_exists('mission', $_GET))
{
	if($_GET['id_groupe'] != 0 AND $_GET['id_groupe'] != '')
	{
		$mission = new bataille_groupe_repere();
		$mission->id_repere = $_GET['mission'];
		$mission->id_groupe = $_GET['id_groupe'];
		$mission->accepter = 0;
		$mission->sauver();
		
		//Si la bataille est déjà lancée
		if ($bataille->etat == 1)
		{
			//On envoi un message au groupe
			$groupe = new bataille_groupe($mission->id_groupe);
			$titre = 'Mission pour la bataille : '.$bataille->nom;
			$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->nom.'[br]
			[bataille:'.$bataille->nom.'][br][br]';
			// Si le groupe n'a pas deja son thread pour cette bataille
			if($groupe->id_thread == 0)
			{
				$thread = new messagerie_thread(0, $groupe->id_groupe, 0, $joueur->get_id(), 1, null, $titre);
				$thread->sauver();
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($thread->id_thread, 0, $titre, $message, $groupe->id_groupe, 1);
				$groupe->id_thread = $thread->id_thread;
				$groupe->sauver();
			}
			else
			{
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($groupe->id_thread, 0, $titre, $message, $groupe->id_groupe, 1);
			}
		}
	}
	else echo 'Erreur du numéro du groupe ?!';
}
else if(array_key_exists('suppr_mission', $_GET))
{
	$mission = new bataille_groupe_repere($_GET['suppr_mission']);
	$mission->supprimer();
	echo "<h6>Mission supprimée avec succès</h6>";
}
else
{
	$bataille->get_groupes();
	$bataille->get_reperes('tri_type');
	
	foreach($bataille->groupes as $groupe)
	{
		$groupe->get_reperes();
		echo '<div class="bataille_groupe"><fieldset>
		<legend>'.$groupe->get_nom().'</legend>';
		foreach($groupe->reperes as $repere)
		{
			$repere->get_repere();
			$repere->repere->get_type();
			if($repere->accepter == 0) $accepter = 'En attente d\'être acceptée';
			else $accepter = 'Acceptée';
			echo 'Mission : '.$repere->repere->repere_type->nom.' X : '.$repere->repere->x.' / Y : '.$repere->repere->y.' - '.$accepter.' - <a href="#" onClick="return envoiInfo(\'gestion_bataille_groupe.php?suppr_mission='.$repere->id.'\', \'information_onglet_bataille\');">X</a><br />';
		
		$id_mission[$repere->id_repere] = 1;
		}

		//On peut proposer une mission
?>
			<div id="liste_mission<?php echo $groupe->id; ?>">
				<select name="mission<?php echo $groupe->id; ?>" id="mission<?php echo $groupe->id; ?>">
				<?php
				// Si aucune mission existe, ou qu'elles sont toutes deja assignées au groupe
				if (count($bataille->reperes['action']) == 0 OR count($bataille->reperes['action']) == count($id_mission))
					echo '<option disabled="disabled">Aucune mission disponible.</option>';
				else
				{
					foreach($bataille->reperes['action'] as $mission)
					{
						$mission->get_type();
						// Si elle n'est pas deja assignée au groupe
						if ($id_mission[$mission->id] != 1)
						{
						?>
						<option value="<?php echo $mission->id; ?>"><?php echo $mission->repere_type->nom; ?> en <?php echo $mission->x; ?> / <?php echo $mission->y; ?></option>
						<?php
						}
					}
				}
				?>
				</select>
				<input type="button" onclick="envoiInfo('gestion_bataille_groupe.php?id_bataille=<?php echo $bataille->id; ?>&id_groupe=<?php echo $groupe->id; ?>&mission='+$('#mission<?php echo $groupe->id; ?>').val(), 'liste_mission<?php echo $groupe->id; ?>');" value="Ok" />
			</div>
			<?php
		//On recupere le thread de messagerie
		if ($groupe->id_thread != 0)
		{
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$thread = new messagerie_thread($groupe->id_thread);
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
