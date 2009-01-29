<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
$messagerie = new messagerie($joueur['ID']);
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
			$titre_messagerie = ' personelle';
		break;
	}
}
?>
<fieldset>
<legend>Messagerie <?php echo $titre_messagerie; ?></legend>
<div id="messagerie_onglet" style="text-align : center;">
	<?php
	include('messagerie_menu_onglet.php');
	?>
</div>
<div id="liste_message">
<?php
include('messagerie_ajax.php');
check_undead_players();
?>
</div>
</fieldset>