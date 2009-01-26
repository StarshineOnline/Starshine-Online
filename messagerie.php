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
<div style="text-align : center;">
	<a href="messagerie.php" onclick="return envoiInfo(this.href, 'information'); return false;">Groupe (<?php echo $non_lu['groupe']; ?>)</a> | <a href="messagerie.php?action=perso" onclick="envoiInfo(this.href, 'information'); return false;">Perso (<?php echo $non_lu['perso']; ?>)</a><br />
</div>
<div id="liste_message">
<?php
include('messagerie_ajax.php');
check_undead_players();
?>
</div>
</fieldset>