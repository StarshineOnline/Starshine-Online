<?php
if (file_exists('root.php'))
  include_once('root.php');

if(array_key_exists('javascript', $_GET)) include_once(root.'inc/fp.php');
include_once(root.'fonction/messagerie.inc.php');
$joueur = new perso($_SESSION['ID']);

if (!isset($_GET['id_thread']) AND !array_key_exists('action', $_GET))
{
	if ($joueur->get_groupe()=='0')
	{
		$_GET['action'] = 'perso';
	}
	else
	{
		$_GET['action'] = 'groupe';
	}
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
		$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
		$messagerie->get_thread($id_thread, 'all', 'ASC', $page, 10);
		$messagerie->thread->get_titre();
		echo '<h3 style="text-align : center;clear:both;">'.htmlspecialchars(stripslashes($messagerie->thread->titre)).' </h3>';
		//Affichage des pages
		$message_total = $messagerie->thread->get_message_total($joueur->get_id());
		$page_max = ceil($message_total / 10);
		if($page == 'last') $page = $messagerie->thread->page;
		echo "<p class='pagination'>";
		if($page > 1)
		{
			echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page - 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_prev" title="Revenir à la page précédente"></span></a>';
		} 
		else
		{
			echo '<span style="height:20px; width:20px; display:block; float:left;"></span>';
		}
		$page = ($page == 0) ? 1 : $page;
		echo '<span class="pages">'.$page.' / '.$page_max.'</span>';
		if($page < $page_max) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page + 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_next" title="Allez à la page suivante"></span></a>';
		echo "</p>";
		foreach($messagerie->thread->messages as $message)
		{
			$message_affiche = message_affiche($message, $joueur->get_id(), $messagerie->thread->messages[0]->titre);
			?>
			<div id="message<?php echo $message->id_message; ?>" class="message_complet">
			<?php
			echo $message_affiche;
			?>
			</div>
			<?php
		}
		echo "<p class='pagination'>";
		if($page > 1)
		{
			echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page - 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_prev" title="Revenir à la page précédente"></span></a>';
		} 
		else
		{
			echo '<span style="height:20px; width:20px; display:block; float:left;"></span>';
		}
		$page = ($page == 0) ? 1 : $page;
		echo '<span class="pages">'.$page.' / '.$page_max.'</span>';
		if($page < $page_max) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page + 1).'" onclick="return envoiInfo(this.href, \'information\');"><span class="message_next" title="Allez à la page suivante"></span></a>';
		echo "</p>";
		
		$messagerie->set_thread_lu($id_thread);
		?>
		<img src="image/pixel.gif" onLoad="envoiInfo('menu_carteville.php?javascript=oui', 'carteville');" />
		<img src="image/pixel.gif" onLoad="envoiInfo('messagerie_menu_onglet.php?javascript=oui', 'messagerie_onglet');" />
		<form method="post" id="formMessage" action="envoimessage.php?id_type=r<?php echo $messagerie->thread->id_thread; ?>">
		<textarea name="message" id="message" cols="53" rows="7"></textarea>
		<br />
		<input type="button" onclick="envoiInfo('envoimessage.php?id_type=r<?php echo $messagerie->thread->id_thread; ?>&message='+encodeURIComponent($('#message').val()), 'information');" value="Envoyer" />
		
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
		case 'echange' :
			$affiche_threads = true;
			$type_thread = 'echange';
		break;
	}

	if($affiche_threads)
	{
		echo "<div id='messagerie_liste'>";
		$groupe = new groupe($joueur->get_groupe(), '');
		$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
		$messagerie->get_threads($type_thread, 'ASC', false, 1);
		
		//Affichage des messages
		?>
		<ul>
			<li class='head'>
			<span class='titre'>
				Titre
			</span>
			<span class='msg'>
				Msg
			</span>
			<span class='par'>
				Interlocuteur
			</span>
			<span class='date'>
				Date
				<a href="thread_modif.php?lu_all=1" onclick="if(confirm('Etes vous sur de vouloir marquer tous les messages commu lus ?')) return envoiInfo(this.href, 'information'); else return false;" title="Lire"><span class="msg_voir" style="float: right;"></span></a>
			</span>
			</li>

		<?php
		$message_total_total = 0;
		foreach($messagerie->threads as $key => $thread)
		{
			$message_total = $thread->get_message_total($joueur->get_id());
			// Si y'a au moins un message dans le thread, et que ce thread n'est pas masqué par le joueur
			if($message_total > 0 AND $message_total != $messagerie->get_thread_masque($thread->id_thread))
			{
				$message_total_total += $message_total;
				$date = date("d-m H:i", strtotime($thread->dernier_message));
				//Recherche du destinataire
				if($thread->id_dest != 0)
				{
					if($thread->id_dest != $joueur->get_id()) $id_interlocuteur = $thread->id_dest;
					else $id_interlocuteur = $thread->id_auteur;
					$interlocuteur = recupperso_essentiel($id_interlocuteur);
					$nom_interlocuteur = $interlocuteur['nom'];
				}
				else $nom_interlocuteur = 'groupe';
				if($thread->important == 1) $style = 'font-weight : bold;';
				else $style = '';
				$thread_non_lu = $messagerie->get_thread_non_lu($thread->id_thread);
				if($thread_non_lu > 0) $texte_thread_non_lu = '('.$thread_non_lu.')';
				else $texte_thread_non_lu = '';
				$options = '';
				if($groupe->get_leader() && $type_thread == 'groupe')
				{
					if($thread->important) $important_etat = 0;
					else $important_etat = 1;
					//$options = '<a href="thread_modif?id_thread='.$thread->id_thread.'&important='.$important_etat.'" onclick="return envoiInfo(this.href, \'\');">(i)</a>';
				}
				//Masquage
				if(($groupe->get_leader() == $joueur->get_id() && $type_thread == 'groupe') OR ($thread->id_auteur == $joueur->get_id()) OR ($thread->id_dest == $joueur->get_id()))
				{
					$options .= '<a href="thread_modif.php?id_thread='.$thread->id_thread.'&masq=1" onclick="if(confirm(\'Etes vous sur de vouloir masquer ce message ?\')) return envoiInfo(this.href, \'thread_'.$thread->id_thread.'\'); else return false;" title="Masquer"><span class="masq" style="float: right;"></span></a>';
				}
				else $options = '';
				if(($groupe->get_leader() == $joueur->get_id() && $type_thread == 'groupe') OR ($thread->id_auteur == $joueur->get_id() && $message_total <= 1))
				{
					$options .= '<a href="thread_modif.php?id_thread='.$thread->id_thread.'&suppr=1" onclick="if(confirm(\'Si vous supprimez ce message, tous les messages à l\\\'intérieur seront supprimés !\')) return envoiInfo(this.href, \'thread_'.$thread->id_thread.'\'); else return false;" title="Supprimer"><span class="del" style="float : right;"></span></a>';
				}
				?>
				<li <?php if($thread_non_lu>0) {echo "style='font-weight: bold;' ";} ?> id="thread_<?php echo $thread->id_thread; ?>" class="<?php echo $class;?>">
					<span class='titre' onclick="envoiInfo('messagerie.php?id_thread=<?php echo $thread->id_thread; ?>', 'information');">

				<?php
				//Si le titre est trop long je le coupe pour que ça casse pas ma mise en page qui déchire ta soeur en deux
				$titre = htmlspecialchars(stripslashes($thread->titre));
				if($titre == '')
				{
					$thread->get_messages(1, 'ASC');
					$titre = htmlspecialchars(stripslashes($thread->messages[0]->titre));
				}
				if(strlen($titre)>=27) 
				{
					$titre = mb_substr($titre,0,27) . "...";
				}
	
				?>
				<?php echo $titre; ?>
				<?php echo $texte_thread_non_lu; ?>

					</span>
					<span class='msg'>
					<?php echo $message_total; ?>
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
				if ($class=='t1'){$class='t2';}else{$class='t1';}
			}
		}
		?>
		</ul>
		<?php
		if($type_thread == 'perso' AND $message_total_total >= 500)
		{
			// Augmentation du compteur de l'achievement
			$achiev = $joueur->get_compteur('messages');
			$achiev->set_compteur($message_total_total);
			$achiev->sauver();
			
		}
	}
}

?>
