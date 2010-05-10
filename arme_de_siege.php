<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

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
$requete = "SELECT * FROM batiment WHERE id = ".$arme->get_id_batiment();
$req = $db->query($requete);
$row_b = $db->read_assoc($req);
?>
	<div id="carte">
	<fieldset>
		<legend><?php echo $row_b['nom']; ?></legend>
<?php

$W_distance = detection_distance(convert_in_pos($arme->get_x(), $arme->get_y()), $joueur->get_pos());

if($W_distance == 0)
{
	echo 'Position - X : '.$arme->get_x().' - Y : '.$arme->get_y().'<br />';
	echo 'Distance de tir : '.$row_b['bonus4'].' case.<br />';
	echo 'Temps avant de pouvoir tirer : ';
	$pat = false;
	if($arme->get_rechargement() > time()) echo transform_sec_temp($arme->get_rechargement() - time());
	else
	{
		echo 'Pret à tirer !';
		$pat = true;
	}
	echo '<br />';
	$x_min = $joueur->get_x() - $row_b['bonus4'];
	$x_max = $joueur->get_x() + $row_b['bonus4'];
	$y_min = $joueur->get_y() - $row_b['bonus4'];
	$y_max = $joueur->get_y() + $row_b['bonus4'];
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
				$pos = convert_in_pos($row_bp['x'], $row_bp['y']);

				echo "<li style='clear:both;' onmouseover=\"$('#pos_".$pos."').addClass('pos_over');\" onmouseout=\"$('#pos_".$pos."').removeClass('pos_over');\"><span style='float:left;width:110px;'>".$row_bp['nom']." </span><span style='float:left;width:105px;'> X : ".$row_bp['x']." - Y : ".$row_bp['y']."</span>";
				if($pat && $joueur->grade->get_rang() >= $row_b['bonus6'] && $joueur->get_pa() >= 10) echo ' - <a href="attaque.php?type=siege&table=construction&id_arme_de_siege='.$arme->get_id().'&id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège (10 PA)</a></li>';
			}
		}
		echo '</ul>';
	}
	$requete = "SELECT * FROM placement WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	if($db->num_rows > 0)
	{
		echo '<h4><span class="titre_info">Batîments en construction à portée</span></h4>';
		echo '<ul>';
		while($row_bp = $db->read_assoc($req_bp))
		{
			if($row_bp['royaume'] != $Trace[$joueur->get_race()]['numrace'] && $row_bp['id'] != $arme->get_id())
			{
				echo '<li>'.$row_bp['nom'].' - X : '.$row_bp['x'].' - Y : '.$row_bp['y'];
				if($pat && $joueur->grade->get_rang() >= $row_b['bonus6'] && $joueur->get_pa() >= 10) echo ' - <a href="attaque.php?table=placement&amp;type=siege&amp;id_arme_de_siege='.$arme->get_id().'&amp;id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a></li>';
			}
		}
		echo '</ul>';
	}
	$requete = 'SELECT map.x as x, map.y as y, nom FROM map LEFT JOIN royaume ON map.royaume = royaume.id WHERE map.x >= '.$x_min.' AND map.x <= '.$x_max.' AND map.y >= '.$y_min.' AND map.y <= '.$y_max.' AND type = 1 ';
					//'AND royaume.race != "'.$joueur->get_race().'"';
	$req_v = $db->query($requete);
	$row_v = $db->read_assoc($req_v);
	if($db->num_rows > 0)
	{
		echo '<h4><span class="titre_info">Ville à portée</span></h4>';
		echo $row_v['nom'];
		$id = convert_in_pos($row_v['x'], $row_v['y']);
		if($pat && $joueur->grade->get_rang() >= $row_b['bonus6']) {
			echo ' - <a href="attaque.php?type=ville&amp;id_arme_de_siege='
				.$arme->get_id().'&id_ville='.$id
				.'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a>';
		}
	}
}
?>
</fieldset>
	</div>
