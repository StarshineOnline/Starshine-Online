<?php
if (file_exists('root.php'))
  include_once('root.php');
  	require_once('inc/fp.php');
	if(!isset($joueur)) { $joueur = new perso($_SESSION["ID"]); }; 		//-- Récupération du tableau contenant toutes les informations relatives au joueur
	$joueur->check_perso();
	$rose_div_hg = convert_in_pos($joueur->get_x()-1,$joueur->get_y()-1);
	$rose_div_h = convert_in_pos($joueur->get_x(),$joueur->get_y()-1);
	$rose_div_hd = convert_in_pos($joueur->get_x()+1,$joueur->get_y()-1);
	$rose_div_cg = convert_in_pos($joueur->get_x()-1,$joueur->get_y());
	$rose_div_c = convert_in_pos($joueur->get_x(),$joueur->get_y());
	$rose_div_cd = convert_in_pos($joueur->get_x()+1,$joueur->get_y());
	$rose_div_bg = convert_in_pos($joueur->get_x()-1,$joueur->get_y()+1);
	$rose_div_b = convert_in_pos($joueur->get_x(),$joueur->get_y()+1);
	$rose_div_bd = convert_in_pos($joueur->get_x()+1,$joueur->get_y()+1);
	
echo "<div id='rosedesvents'>
	   <div id='rose_div_hg' onclick=\"deplacement('hautgauche', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_hg."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_hg."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_h' onclick=\"deplacement('haut', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_h."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_h."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_hd' onclick=\"deplacement('hautdroite', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_hd."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_hd."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_cg' onclick=\"deplacement('gauche', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_cg."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_cg."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_c' onclick=\"deplacement('centre', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_c."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_c."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_cd' onclick=\"deplacement('droite', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_cd."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_cd."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_bg' onclick=\"deplacement('basgauche', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_bg."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_bg."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_b' onclick=\"deplacement('bas', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_b."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_b."').removeClassName('pos_over');\"></div>
	   <div id='rose_div_bd' onclick=\"deplacement('basdroite', cache_monstre, affiche_royaume);\" onmouseover=\"$('pos_".$rose_div_bd."').addClassName('pos_over');\" onmouseout=\"$('pos_".$rose_div_bd."').removeClassName('pos_over');\"></div>
</div>";
?>
