<?php
if (file_exists('../root.php'))
  include_once('../root.php');
require_once('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if (isset($_GET['id_thread']))
	{
echo '<div id="messagerie" style="width: 400px; color: black;">';
		$id_thread = $_GET['id_thread'];
		if(array_key_exists('page', $_GET))
		{
			$page = $_GET['page'];
		}
		else $page = 'last';
		$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
		$messagerie->get_thread($id_thread, 'all', 'ASC', $page, 10);
		$messagerie->thread->get_titre();
		echo '<h3 style="text-align:center; clear:both; color: white;">'.htmlspecialchars(stripslashes($messagerie->thread->titre)).'</h3>';
		//Affichage des pages
		$message_total = $messagerie->thread->get_message_total($joueur->get_id());
		$page_max = ceil($message_total / 10);
		if($page == 'last') $page = $messagerie->thread->page;
		echo "<p class='pagination'>";
		if($page > 1)
		{
			echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page - 1).'" onclick="return envoiInfo(this.href, \'messagerie\');"><span class="message_prev" title="Revenir à la page précédente"></span></a>';
		} 
		else
		{
			echo '<span style="height:20px; width:20px; display:block; float:left;"></span>';
		}
		$page = ($page == 0) ? 1 : $page;
		echo '<span class="pages">'.$page.' / '.$page_max.'</span>';
		if($page < $page_max) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page + 1).'" onclick="return envoiInfo(this.href, \'messagerie\');"><span class="message_next" title="Allez à la page suivante"></span></a>';
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
			echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page - 1).'" onclick="return envoiInfo(this.href, \'messagerie\');"><span class="message_prev" title="Revenir à la page précédente"></span></a>';
		} 
		else
		{
			echo '<span style="height:20px; width:20px; display:block; float:left;"></span>';
		}
		$page = ($page == 0) ? 1 : $page;
		echo '<span class="pages">'.$page.' / '.$page_max.'</span>';
		if($page < $page_max) echo '<a href="messagerie.php?id_thread='.$messagerie->thread->id_thread.'&amp;page='.($page + 1).'" onclick="return envoiInfo(this.href, \'messagerie\');"><span class="message_next" title="Allez à la page suivante"></span></a>';
		echo "</p>";
		
		$messagerie->set_thread_lu($id_thread);
		?>
		<form method="post" id="formMessage" action="../envoimessage.php?id_type=r<?php echo $messagerie->thread->id_thread; ?>">
		<textarea name="message" id="message" cols="53" rows="7"></textarea>
		<br />
		<input type="button" onclick="envoiInfo('../envoimessage.php?id_type=r<?php echo $messagerie->thread->id_thread; ?>&message='+encodeURIComponent($('#message').val()), 'messagerie');" value="Envoyer" />
		
		</form>
<?php
echo "</div>";
}
?>