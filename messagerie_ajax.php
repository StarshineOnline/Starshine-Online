message_complet<?php
if(array_key_exists('javascript', $_GET)) include('inc/fp.php');
include('fonction/messagerie.inc.php');

if (!isset($_GET['id_thread']) AND !array_key_exists('action', $_GET))
{
	$_GET['action'] = 'groupe';
}
if(!array_key_exists('action', $_GET))
{
	if (isset($_GET['id_thread']))
	{
		$id_thread = $_GET['id_thread'];
		$messagerie = new messagerie($joueur['ID']);
		$messagerie->get_thread($id_thread);
		echo '<h3 style="text-align : center;">'.$messagerie->thread->messages[0]->titre.' / <a href="envoimessage.php?id_type=r'.$messagerie->thread->id_thread.'" onclick="return envoiInfo(this.href, \'information\')">Répondre</a></h3>';
		foreach($messagerie->thread->messages as $message)
		{
			$message_affiche = message_affiche($message, $joueur['ID'], $messagerie->thread->messages[0]->titre);
			?>
			<div id="message<?php echo $message->id_message; ?>" class="message_complet">
			<?php
			echo $message_affiche;
			?>
			</div>
			<?php
		}
		$messagerie->set_thread_lu($id_thread);
	}
}
else
{
	$id_mess = $_GET['ID'];
	$affiche_threads = false;
	switch($_GET['action'])
	{
		//Confirmation de suppression d'un message
		case 'del' :
			echo 'Voulez vous vraiment effacer ce message ?<br />
			<a href="messagerie.php?ID='.$id_mess.'&amp;action=delc" onclick="return envoiInfo(this.href, \'information\')">Oui</a> / <a href="messagerie.php" onclick="return envoiInfo(this.href, \'information\')">Non</a>';
		break;
		//Suppression d'un message
		case 'delc' :
			$message = new messagerie_message($id_mess);
			$message->supprimer();
		break;
		case 'groupe' :
			$affiche_threads = true;
			$type_thread = 'groupe';
		break;
		case 'perso' :
			$affiche_threads = true;
			$type_thread = 'perso';
		break;
	}

	if($affiche_threads)
	{
		$groupe = recupgroupe($joueur['groupe'], '');
		$messagerie = new messagerie($joueur['ID']);
		$messagerie->get_threads($type_thread, 'ASC', true, 1);
		
		//Affichage des messages
		?>
		<table width="95%" class="information_case">
		<tr>
			<td>
			</td>
			<td>
				Titre
			</td>
			<td>
				Par
			</td>
			<td>
				Date
			</td>
			<td>
			</td>
		</tr>
		<?php
		foreach($messagerie->threads as $key => $thread)
		{
			$date = $thread->messages[0]->date;
			if($thread->important) $style = 'font-weight : bold;';
			else $style = '';
			$thread_non_lu = $messagerie->get_thread_non_lu($thread->id_thread);
			if($thread_non_lu > 0) $texte_thread_non_lu = '('.$thread_non_lu.')';
			else $texte_thread_non_lu = '';
			$options = '';
			if($groupe['leader'] && $type_thread == 'groupe')
			{
				if($thread->important) $important_etat = 0;
				else $important_etat = 1;
				$options = '<a href="thread_modif?id_thread='.$thread->id_thread.'&important='.$important_etat.'" onclick="return envoiInfo(this.href, \'\');">(i)</a>';
			}
			if(($groupe['leader'] && $type_thread == 'groupe') OR ($thread->id_auteur == $joueur['ID'] && !array_key_exists(1, $thread->messages)))
			{
				$options .= '<a href="thread_modif.php?id_thread='.$thread->id_thread.'&suppr=1" onclick="if(confirm(\'Si vous supprimez ce message, tous les messages à l\\\'intérieur seront supprimés !\')) return envoiInfo(this.href, \'information\'); else return false;">(X)</a>';
			}
			else $options = '';
			?>
		<tr>
			<td>
				<?php echo $texte_thread_non_lu; ?>
			</td>
			<td>
				<?php
				//Si le titre est trop long je le coupe pour que ça casse pas ma mise en page qui déchire ta soeur en deux
				$titre = htmlspecialchars(stripslashes($thread->messages[0]->titre));
				if(strlen($titre)>=30) 
				{
					$titre = mb_substr($titre,0,30) . "...";
				}
				?>
				<a href="messagerie.php?id_thread=<?php echo $thread->id_thread; ?>" onclick="return envoiInfo(this.href, 'information'); return false;" style="<?php echo $style; ?>">
				<?php echo $titre; ?></a>
			</td>
			<td>
				<?php
				echo $thread->messages[0]->nom_auteur;
				?>
			</td>
			<td style="font-size : 0.9em;">
				<?php echo $date; ?>
			</td>
			<td>
				<?php echo $options; ?>
			</td>
		</tr>
				<?php
		}
		?>
		</table>
		<?php
	}
}

?>
