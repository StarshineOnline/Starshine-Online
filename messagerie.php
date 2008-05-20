<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
?>
<h2>Messagerie</h2>
<a href="javascript:envoiInfo('messagerie.php', 'information');">Réception</a> | <a href="javascript:envoiInfo('messagerie.php?action=envoi', 'information');">Envoi</a> | <a href="javascript:if(confirm('Voulez vous supprimer ces messages ?')) checkCase();">Supprimer</a><br />
<div id="liste_message">
<?php
include('messagerie_ajax.php');
check_undead_players();
?>
</div>