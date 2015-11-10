<?php // -*- mode: php; tab-width:2 -*-
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

include_once ('livre.php');
$tab_sort_jeu = explode(';', $joueur->get_comp_combat());
?>
<hr>
<?php
$i = 0;
$type = '';
$magies = array();
$magie = '';
$requete = "SELECT * FROM comp_combat GROUP BY comp_assoc";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	if($magie != $row['comp_assoc'])
	{
		$magie = $row['comp_assoc'];
		$magies[] = $row['comp_assoc'];
	}
}

?>
<div id="livre">
	<div id="livre_gauche_physique">
	<?php
	$k=1;
	foreach($magies as $magie)
	{
		echo '<div id="livre_physique_icone'.$k.'"><a href="competence.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" onmouseover="this.src = \'image/icone/'.$magie.'hover.png\'" onmouseout="this.src = \'image/'.$magie.'.png\'"/></a></div>';
		$k++;
	}
	?>
	</div>
	<div id="livre_haut"><h3>Livre</h3></div>
	<div id="livre_corps">
		<ul>
<?php
$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
$requete = "SELECT * FROM comp_combat ".$where." ORDER BY comp_assoc ASC, type ASC";
$req = $db->query($requete);
$magie = '';

while($row = $db->read_array($req))
{
	if($magie != $row['comp_assoc']) // Equivalent à : on change de catégorie
	{
		$magie = $row['comp_assoc'];
		echo '<li style="height:3px !important; background: none !important; margin-top:0px !important;"><strong>'.$Gtrad[$magie].'</strong></li>';
	}
	if(in_array($row['id'], $tab_sort_jeu))
	{
		$href = '';
		$cursor = '';
		$color = 'black';
		$echo = addslashes(description($row['description'], $row)); 
		echo '<li>
				<span style="float:left; width:150px; '.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href.'" onmouseover="return '.make_overlib($echo.'<br/><br/>Utilisable avec : '.implode(' - ', explode(';', $row['arme_requis']))).'" onmouseout="return nd();"><strong>'.$row['nom'].'</strong></span>'; 
?>
				<span class="xsmall" style="float: right; padding-right:15px;"><?php echo $row['mp']; ?> RM</span>
				<span style="position:relative;bottom:40px;left:133px;"><img src="image/interface/livres/ficellefavori.png" /></span>
				
		</li>
<?php
		$i++;
	}
}
?>  
		</ul>
	</div>
	<div id="livre_bat"></div>
</div>

<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
