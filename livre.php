<?php
include('haut_ajax.php');
$joueur = recupperso($_SESSION['ID']);
echo'<h2>Les livres</h2>';
if($joueur['sort_jeu'] != '')
{
	echo '<a href="sort.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/livredesort_icone.png" alt="Livre de sorts" style="vertical-align : middle;" title="Livre de sorts" /></a> ';
}
//Si le perso a des sort de combat affichage du lien vers sorts de combat
if($joueur['sort_combat'] != '')
{
	echo '<a href="sort_combat.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/livredesortdecombat_icone.png" alt="Sorts de combat" style="vertical-align : middle;" title="Sorts de combat" /></a> ';
}
//Si le perso a des compétences de jeu affichage du lien vers compÃ©tences de jeu
if($joueur['comp_jeu'] != '')
{
	echo '<a href="competence_jeu.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/competencehcombat.png" alt="Compétences hors combat" style="vertical-align : middle;" title="Compétences hors combat" /></a> ';
}
//Si le perso a des compétences de combat affichage du lien vers compÃ©tences de combat
if($joueur['comp_combat'] != '')
{
	echo '<a href="competence.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/competence_icone.png" alt="Compétences de combat" style="vertical-align : middle;" title="Compétences de combat" /></a> ';
}
echo '<a href="livre_recette.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/livrederecette.png" alt="Livre de recettes" style="vertical-align : middle;" title="Livre de recettes" /></a>';

?>