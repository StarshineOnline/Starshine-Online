<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'haut_ajax.php');
$joueur = new perso($_SESSION['ID']);
verif_mort($joueur, 1);
?>
<fieldset>
	<legend>Les livres</legend>
<?php
if($joueur->get_sort_jeu() != '')
{
	echo '<a href="sort.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/interface/livres/iconesorthorscombat.png" alt="Livre de sorts" style="vertical-align : middle;" title="Livre de sorts" onmouseover="this.src = \'image/interface/livres/iconesorthorscombat_hover.png\'" onmouseout="this.src = \'image/interface/livres/iconesorthorscombat.png\'" /></a> ';
}
//Si le perso a des sort de combat affichage du lien vers sorts de combat
if($joueur->get_sort_combat() != '')
{
	echo '<a href="sort_combat.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/interface/livres/iconesortcombat.png" alt="Sorts de combat" style="vertical-align : middle;" title="Sorts de combat" onmouseover="this.src = \'image/interface/livres/iconesortcombat_hover.png\'" onmouseout="this.src = \'image/interface/livres/iconesortcombat.png\'" /></a> ';
}
//Si le perso a des compétences de jeu affichage du lien vers compétences de jeu
if($joueur->get_comp_jeu() != '')
{
	echo '<a href="competence_jeu.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/interface/livres/iconecompentecehorscombat.png" alt="Compétences hors combat" style="vertical-align : middle;" title="Compétences hors combat" onmouseover="this.src = \'image/interface/livres/iconecompentecehorscombat_hover.png\'" onmouseout="this.src = \'image/interface/livres/iconecompentecehorscombat.png\'" /></a> ';
}
//Si le perso a des compétences de combat affichage du lien vers compétences de combat
if($joueur->get_comp_combat() != '')
{
	echo '<a href="competence.php" onclick="return envoiInfo(this.href, \'information\');"><img src="image/interface/livres/iconecompentececombat.png" alt="Compétences de combat" style="vertical-align : middle;" title="Compétences de combat" onmouseover="this.src = \'image/interface/livres/iconecompentececombat_hover.png\'" onmouseout="this.src = \'image/interface/livres/iconecompentececombat.png\'" /></a> ';
}
?>
<a href="livre_recette.php" onclick="return envoiInfo(this.href, 'information');"><img src="image/interface/livres/iconerecette.png" alt="Livre de recettes" style="vertical-align : middle;" title="Livre de recettes" onmouseover="this.src = 'image/interface/livres/iconerecette_hover.png'" onmouseout="this.src = 'image/interface/livres/iconerecette.png'" /></a>
