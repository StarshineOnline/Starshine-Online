<?php
if (file_exists('root.php'))
  include_once('root.php');

if(array_key_exists('javascript', $_GET))
{
	include_once(root.'inc/fp.php');
	$joueur = new perso($_SESSION['ID']);
	$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
	$non_lu = $messagerie->get_non_lu();
}
?>
<a href="messagerie.php" onclick="return envoiInfo(this.href, 'information'); return false;">Groupe (<?php echo $non_lu['groupe']; ?>)</a> <a href="messagerie.php?action=perso" onclick="envoiInfo(this.href, 'information'); return false;">Perso (<?php echo $non_lu['perso']; ?>)</a>