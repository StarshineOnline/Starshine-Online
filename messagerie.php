<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

if(!array_key_exists('ID', $_SESSION) || empty($_SESSION['ID']))
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	exit();
}
$joueur = new perso($_SESSION['ID']);
$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
$non_lu = $messagerie->get_non_lu();
if (!isset($_GET['id_thread']) AND !array_key_exists('action', $_GET))
{
	$titre_messagerie = ' de groupe';
}
elseif(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'groupe' :
			$titre_messagerie = ' de groupe';
		break;
		case 'perso' :
			$titre_messagerie = ' personnelle';
		break;
		case 'echange' :
			$titre_messagerie = ' des échanges';
		break;
	}
}
?>
<fieldset>
<legend>Messagerie <?php echo $titre_messagerie; ?></legend>
<div id="messagerie_onglet">
	<?php
	include_once(root.'messagerie_menu_onglet.php');
	?>
</div>
<div id="liste_message">
<?php
include_once(root.'messagerie_ajax.php');
check_undead_players();
?>
</div>
</fieldset>