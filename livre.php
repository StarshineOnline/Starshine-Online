<?php
include('haut_ajax.php');
$joueur = recupperso($_SESSION['ID']);
echo'<h2>Les livres</h2>';
if($joueur['sort_jeu'] != '')
{
	echo '<a href="javascript:envoiInfo(\'sort.php\', \'information\');montre(\'\');"><img src="image/livredesort_icone.png" alt="Livre de sorts" style="vertical-align : middle;" title="Livre de sorts" /></a> ';
}
//Si le perso a des sort de combat affichage du lien vers sorts de combat
if($joueur['sort_combat'] != '')
{
	echo '<a href="javascript:envoiInfo(\'sort_combat.php\', \'information\');montre(\'\');"><img src="image/livredesortdecombat_icone.png" alt="Sorts de combat" style="vertical-align : middle;" title="Sorts de combat" /></a> ';
}
//Si le perso a des comp�tences de jeu affichage du lien vers compétences de jeu
if($joueur['comp_jeu'] != '')
{
	echo '<a href="javascript:envoiInfo(\'competence_jeu.php\', \'information\');montre(\'\');"><img src="image/competencehcombat.png" alt="Comp�tences hors combat" style="vertical-align : middle;" title="Comp�tences hors combat" /></a> ';
}
//Si le perso a des comp�tences de combat affichage du lien vers compétences de combat
if($joueur['comp_combat'] != '')
{
	echo '<a href="javascript:envoiInfo(\'competence.php\', \'information\');montre(\'\');"><img src="image/competence_icone.png" alt="Comp�tences de combat" style="vertical-align : middle;" title="Comp�tences de combat" /></a> ';
}
echo '<a href="javascript:envoiInfo(\'livre_recette.php\', \'information\');montre(\'\');"><img src="image/icone/livrederecette.png" alt="Livre de recettes" style="vertical-align : middle;" title="Livre de recettes" /></a>';

?>