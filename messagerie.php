<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
$messagerie = new messagerie($joueur['ID']);
$non_lu = $messagerie->get_non_lu();
?>
<h2>Messagerie</h2>
<div style="text-align : center;">
	<a href="messagerie.php" onclick="return envoiInfo(this.href, 'information'); return false;">Groupe (<?php echo $non_lu['groupe']; ?>)</a> | <a href="messagerie.php?action=perso" onclick="envoiInfo(this.href, 'information'); return false;">Perso (<?php echo $non_lu['perso']; ?>)</a><br />
</div>
<div id="liste_message">
<?php
include('messagerie_ajax.php');
check_undead_players();
?>
</div>