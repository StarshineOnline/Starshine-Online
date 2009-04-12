<?php
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
		if(array_key_exists('page', $_GET))
		{
			$page = $_GET['page'];
		}
		else $page = 'last';
		$messagerie = new messagerie($joueur['ID']);
		$messagerie->get_thread($id_thread, 'all', 'ASC', $page, 10);
		echo '<h3 style="text-align : center;">'.htmlspecialchars(stripslashes($messagerie->thread->messages[0]->titre)).' / <a href="envoimessage.php?id_type=r'.$messagerie->thread->id_thread.'" onclick="return envoiInfo(this.href, \'information\')">Répondre</a></h3>';
		//Affichage des pages
		$message_total = $messagerie->thread->get_message_total($joueur['ID']);
		$page_max = ceil($message_total / 10);
		if($page == 'last') $page = $page_max;
		if($page > 1) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page - 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_prev" title="Revenir à la page précédente"></span></a>';
		if($page < $page_max) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page + 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_next" title="Allez à la page suivante"></span></a>';
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
		?>
		<img src="image/pixel.gif" onLoad="envoiInfo('menu_carteville.php?javascript=oui', 'carteville');" />
		<img src="image/pixel.gif" onLoad="envoiInfo('messagerie_menu_onglet.php?javascript=oui', 'messagerie_onglet');" />
		<form method="post" id="formMessage" action="envoimessage.php?id_type=r<?php echo $messagerie->thread->id_thread; ?>">
		<textarea name="message" id="message" cols="53" rows="7"></textarea>
		<br />
		<input type="button" onclick="envoiFormulaire('formMessage', 'information');" value="Envoyer" />
		
		</form>
	<?php
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
		echo "<div id='messagerie_liste'>";
		$groupe = recupgroupe($joueur['groupe'], '');
		$messagerie = new messagerie($joueur['ID']);
		$messagerie->get_threads($type_thread, 'ASC', true, 1);
		
		//Affichage des messages
		?>
		<ul>
			<li>
			<span class='titre'>
				Titre
			</span>
			<span class='par'>
				Interlocuteur
			</span>
			<span class='date'>
				Date
			</span>
			</li>

		<?php
		foreach($messagerie->threads as $key => $thread)
		{
			$message_total = $thread->get_message_total($joueur['ID']);
			if($message_total > 0)
			{
				$date = date("d-m H:i", strtotime($thread->dernier_message));
				//Recherche du destinataire
				if($thread->id_dest != 0)
				{
					if($thread->id_dest != $joueur['ID']) $id_interlocuteur = $thread->id_dest;
					else $id_interlocuteur = $thread->id_auteur;
					$interlocuteur = recupperso_essentiel($id_interlocuteur);
					$nom_interlocuteur = $interlocuteur['nom'];
				}
				else $nom_interlocuteur = 'groupe';
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
					$options .= '<a href="thread_modif.php?id_thread='.$thread->id_thread.'&suppr=1" onclick="if(confirm(\'Si vous supprimez ce message, tous les messages à l\\\'intérieur seront supprimés !\')) return envoiInfo(this.href, \'thread_'.$thread->id_thread.'\'); else return false;" title="Supprimer"><span class="del" style="float : right;"></span></a>';
				}
				else $options = '';
				?>
				<li id="thread_<?php echo $thread->id_thread; ?>">
					<span class='titre'>
				<?php echo $texte_thread_non_lu; ?>

				<?php
				//Si le titre est trop long je le coupe pour que ça casse pas ma mise en page qui déchire ta soeur en deux
				$titre = htmlspecialchars(stripslashes($thread->messages[0]->titre));
				if(strlen($titre)>=30) 
				{
					$titre = mb_substr($titre,0,30) . "...";
				}
				$titre .= ' ('.$message_total.')';
				?>
				<a href="messagerie.php?id_thread=<?php echo $thread->id_thread; ?>" onclick="return envoiInfo(this.href, 'information'); return false;" style="<?php echo $style; ?>">
				<?php echo $titre; ?></a>
					</span>
					<span class='par'>
						<?php
						echo $nom_interlocuteur;
						?>
					</span>
					<span class='date'>
						<?php echo $date; ?>
						<?php echo $options; ?>
					</span>
				</li>
				<?php
			}
		}
		?>
		</ul>
		<?php
	}
}

?>
