<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->get_grade();

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT * FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);

//Informations sur le batiment
$arme = new construction($_GET['id_construction']);
$batiment = new batiment($arme->get_id_batiment());
?>
	<div id="carte">
	<fieldset>
		<legend><?php echo $batiment->get_nom(); ?></legend>
<?php

$W_distance = detection_distance(convert_in_pos($arme->get_x(), $arme->get_y()), $joueur->get_pos());

if($W_distance <= 3)
{
	echo 'Position - X : '.$arme->get_x().' - Y : '.$arme->get_y().'<br />';
	echo 'Distance de tir : '.$batiment->get_bonus('portee').' case.<br />';
	echo 'Temps avant de pouvoir tirer : ';
	$peut_tirer = false;
	if($arme->peut_attaquer())
	{
		echo 'Pret à tirer !';
		$peut_tirer = true;
	}
	else
		echo transform_sec_temp($arme->get_rechargement() - time());
	echo '<br />';
  if( $joueur->grade->get_rang() < $batiment->get_bonus('rang_manip') )
	 $peut_tirer = false;
  if( $joueur->get_pa() < $cout_pa )
	 $peut_tirer = false;
  if( $joueur->grade->get_rang() < $batiment->get_bonus('rang_manip') )
	 $peut_tirer = false;
	
	$cout_pa = $arme->get_cout_attaque($joueur);
	if( $joueur->get_pa() < $cout_pa )
	{
		echo 'Pas assez de PA pour tirer<br />';
		$pat = false;
	}
	
	$x_min = $joueur->get_x() - $batiment->get_bonus('portee');
	$x_max = $joueur->get_x() + $batiment->get_bonus('portee');
	$y_min = $joueur->get_y() - $batiment->get_bonus('portee');
	$y_max = $joueur->get_y() + $batiment->get_bonus('portee');
	
	$requete = "SELECT * FROM construction WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);

	echo '<ul>';
	if($db->num_rows > 0)
	{
		echo '<h4><span class="titre_info">Batîments à portée</span></h4>';
		while($row_bp = $db->read_assoc($req_bp))
		{
			if($row_bp['royaume'] != $Trace[$joueur->get_race()]['numrace'] && $row_bp['id'] != $arme->get_id())
			{
				$royaume_arme = new royaume($row_bp['royaume']);
				$rel = position_relative(array($joueur->get_x(),$joueur->get_y()), array($row_bp['x'], $row_bp['y']));
				$pos = 'rel_'.$rel[0].'_'.$rel[1];

				echo "<li style='clear:both;' onmouseover=\"$('#pos_".$pos."').addClass('pos_over');\" onmouseout=\"$('#pos_".$pos."').removeClass('pos_over');\"><span style='float:left;width:110px;'>".$row_bp['nom']." ".$royaume_arme->get_race()."</span><span style='float:left;width:90px;'> X : ".$row_bp['x']." - Y : ".$row_bp['y']."</span>";
				if($peut_tirer && $W_distance == 0) echo ' - <a href="attaque.php?type=siege&table=construction&id_arme_de_siege='.$arme->get_id().'&id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège ('.$cout_pa.' PA)</a></li>';
			}
		}
		echo '</ul>';
	}
	
	$requete = "SELECT * FROM placement WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	if($db->num_rows > 0)
	{
		echo '<h4><span class="titre_info">Bâtiments en construction à portée</span></h4>';
		echo '<ul>';
		while($row_bp = $db->read_assoc($req_bp))
		{
			if($row_bp['royaume'] != $Trace[$joueur->get_race()]['numrace'] && $row_bp['id'] != $arme->get_id())
			{
				$royaume_arme = new royaume($row_bp['royaume']);
				$rel = position_relative(array($joueur->get_x(),$joueur->get_y()), array($row_bp['x'], $row_bp['y']));
				$pos = 'rel_'.$rel[0].'_'.$rel[1];
				
				echo "<li style='clear:both;' onmouseover=\"$('#pos_".$pos."').addClass('pos_over');\" onmouseout=\"$('#pos_".$pos."').removeClass('pos_over');\"><span style='float:left;width:110px;'>".$row_bp['nom']." ".$royaume_arme->get_race()."</span><span style='float:left;width:90px;'>X : ".$row_bp['x']." - Y : ".$row_bp['y']."</span>";
				if($peut_tirer && $W_distance == 0) echo ' - <a href="attaque.php?table=placement&amp;type=siege&amp;id_arme_de_siege='.$arme->get_id().'&amp;id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège ('.$cout_pa.' PA)</a></li>';
			}
		}
		echo '</ul>';
	}
	
	$requete = 'SELECT map.x as x, map.y as y, nom, race FROM map LEFT JOIN royaume ON map.royaume = royaume.id WHERE map.x >= '.$x_min.' AND map.x <= '.$x_max.' AND map.y >= '.$y_min.' AND map.y <= '.$y_max.' AND type = 1 AND royaume.fin_raz_capitale = 0 AND royaume.race != "'.$joueur->get_race().'"';
					//'AND royaume.race != "'.$joueur->get_race().'"';
	$req_v = $db->query($requete);
	$row_v = $db->read_assoc($req_v);
	if($db->num_rows > 0)
	{
    $roy_perso = new royaume( $Trace[$joueur->get_race()]['numrace'] );
    $diplo = $roy_perso->get_diplo($row_v['race']);
    if( $diplo >= 8 )
    {
  		echo '<h4><span class="titre_info">Ville à portée</span></h4>';
  		echo $row_v['nom'];
  		$id = convert_in_pos($row_v['x'], $row_v['y']);
  		if($peut_tirer && $W_distance == 0) {
  			echo ' - <a href="attaque.php?type=ville&amp;id_arme_de_siege='
  				.$arme->get_id().'&id_ville='.$id
  				.'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège ('.$cout_pa.' PA)</a>';
      }
		}
	}
}
?>
</fieldset>
	</div>
