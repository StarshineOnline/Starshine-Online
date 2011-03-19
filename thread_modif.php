<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
include_once(root.'fonction/messagerie.inc.php');

$joueur = new perso($_SESSION['ID']);
$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());

if(isset($_GET['id_thread']))
{
	$id_thread = $_GET['id_thread'];
	$_GET['id_thread'] = '';
	$thread = new messagerie_thread($id_thread);

	if(array_key_exists('important', $_GET))
	{
		$thread->important = $_GET['important'];
	}
	elseif(array_key_exists('suppr', $_GET) && $_GET['suppr'] == 1)
	{
		$thread->supprimer(true);
	}
	elseif(array_key_exists('masq', $_GET) && $_GET['masq'] == 1)
	{
		$messagerie->set_thread_masque($thread->id_thread);
	}
}
else
{
	if(array_key_exists('lu_all', $_GET))
	{
		$messagerie->set_thread_lu_all();
		?>
		<script type="text/javascript">
			refresh('messagerie.php','information');
		</script>
		<?php
	}
}
?>