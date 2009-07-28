<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'haut_ajax.php');
$joueur = new perso($_SESSION['ID']);
?>
<fieldset>
	<legend>Les livres</legend>
<?php
if($joueur->get_sort_jeu() != '')
{
	echo '<a href="sort.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/livredesort_icone.png" alt="Livre de sorts" style="vertical-align : middle;" title="Livre de sorts" /></a> ';
}
//Si le perso a des sort de combat affichage du lien vers sorts de combat
if($joueur->get_sort_combat() != '')
{
	echo '<a href="sort_combat.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/livredesortdecombat_icone.png" alt="Sorts de combat" style="vertical-align : middle;" title="Sorts de combat" /></a> ';
}
//Si le perso a des compétences de jeu affichage du lien vers compétences de jeu
if($joueur->get_comp_jeu() != '')
{
	echo '<a href="competence_jeu.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/competencehcombat.png" alt="Compétences hors combat" style="vertical-align : middle;" title="Compétences hors combat" /></a> ';
}
//Si le perso a des compétences de combat affichage du lien vers compétences de combat
if($joueur->get_comp_combat() != '')
{
	echo '<a href="competence.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/competence_icone.png" alt="Compétences de combat" style="vertical-align : middle;" title="Compétences de combat" /></a> ';
}
?>
<a href="livre_recette.php" onclick="return envoiInfo(this.href, 'information');"><img src="image/icone/livrederecette.png" alt="Livre de recettes" style="vertical-align : middle;" title="Livre de recettes" /></a>