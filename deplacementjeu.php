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
	if ($interface_3D){	$type = 'troisd';}else{$type = 'normal';	}
echo "<div id='rosedesvents'>
	   <div id='rose_div_hg' onclick=\"deplacement('hautgauche', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_hg."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_hg."').removeClass('pos_over');\"></div>
	   <div id='rose_div_h' onclick=\"deplacement('haut', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_h."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_h."').removeClass('pos_over');\"></div>
	   <div id='rose_div_hd' onclick=\"deplacement('hautdroite', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_hd."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_hd."').removeClass('pos_over');\"></div>
	   <div id='rose_div_cg' onclick=\"deplacement('gauche', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_cg."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_cg."').removeClass('pos_over');\"></div>
	   <div id='rose_div_c' onclick=\"deplacement('centre', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_c."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_c."').removeClass('pos_over');\"></div>
	   <div id='rose_div_cd' onclick=\"deplacement('droite', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_cd."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_cd."').removeClass('pos_over');\"></div>
	   <div id='rose_div_bg' onclick=\"deplacement('basgauche', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_bg."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_bg."').removeClass('pos_over');\"></div>
	   <div id='rose_div_b' onclick=\"deplacement('bas', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_b."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_b."').removeClass('pos_over');\"></div>
	   <div id='rose_div_bd' onclick=\"deplacement('basdroite', '$type', show_only);\" onmouseover=\"$('#pos_".$rose_div_bd."').addClass('pos_over');\" onmouseout=\"$('#pos_".$rose_div_bd."').removeClass('pos_over');\"></div>
</div>";
?>
