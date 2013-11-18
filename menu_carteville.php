<?php
if (file_exists('root.php')) include_once('root.php');
include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
if((is_ville($joueur->get_x(), $joueur->get_y()) == 1) AND (!array_key_exists('ville', $_GET) OR (array_key_exists('ville', $_GET) AND $_GET['ville'] == 'no')))
{
	return_ville('<img src="image/interface/affiche_ville.png" alt="Accéder à la ville" />', $joueur->get_pos());
}
else
{
	?>
	<a class="rose_a" onclick="envoiInfo('deplacement.php', 'centre')"><img src="image/interface/affiche_carte.png" alt="Afficher la carte" /></a>
	<?php
}
if(!($joueur->est_mort()))
{
require_once('menu_mess.php');
}
?>
