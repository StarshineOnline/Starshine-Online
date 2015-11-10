<?php // -*- mode: php; tab-width:2 -*-
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

include_once ('livre.php');
$tab_sort_jeu = explode(';', $joueur->get_sort_combat());
?>
<hr>
<?php
$i = 0;
$type = '';
$magies = array();
$magie = '';
$requete = "SELECT * FROM sort_combat GROUP BY comp_assoc";
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
	<div id="livre_gauche_magique">
<?php
$k=1;
foreach($magies as $magie)
{
	echo '<div id="livre_magique_icone'.$k.'"><a href="sort_combat.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" style="vertical-align : middle;" onmouseover="this.src = \'image/icone/'.$magie.'hover.png\'" onmouseout="this.src = \'image/icone_'.$magie.'.png\'" /></a></div>';
	$k++;
}
?>
	</div>
	<div id="livre_haut"><h3>Livre</h3></div>
	<div id="livre_corps">
		<ul>
<?php
$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
$requete = "SELECT * FROM sort_combat ".$where." ORDER BY comp_assoc ASC, type ASC, nom ASC";
$req = $db->query($requete);
$magie = '';

while($row = $db->read_array($req))
{
	if($magie != $row['comp_assoc'])
	{
		$magie = $row['comp_assoc'];
		echo '<li style="height:3px !important; background: none !important; margin-top:0px !important;"><strong>'.$Gtrad[$magie].'</strong></li>';
	}
	if(in_array($row['id'], $tab_sort_jeu))
	{
		if($Gtrad[$row['type']] != $Gtrad[$type])
		{
			$type = $row['type'];
			echo '<h3>'.$Gtrad[$row['type']].'</h3>';
		}
		
		$href = '';
		$cursor = '';
		$color = 'black';
		$phrase = '';
		$echo = addslashes(description($row['description'], $row)).'<br />Incantation : '.$row['incantation'].'';
		
		echo '<li>
			<span style="float:left; width:150px; '.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href.'" onmouseover="return '.make_overlib($echo).'" onmouseout="return nd();"><strong>'.$row['nom'].'</strong></span>'; 
			?>
			<span class="xsmall" style="float: right; padding-right:15px;"><?php echo round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10))); ?> RM</span>
		<?php
		$sort_de_degat = array('degat_feu', 'degat_nature', 'drain_vie', 'degat_froid', 'degat_mort', 'degat_vent', 'degat_terre', 'pacte_sang'
		, 'brisement_os', 'lapidation', 'globe_foudre', 'vortex_vie', 'vortex_mana', 'putrefaction', 'embrasement', 'sphere_glace');
		if(in_array($row['type'], $sort_de_degat))
		{
			if($row['type'] == 'drain_vie' OR $row['type'] == 'vortex_vie' OR $row['type'] == 'vortex_mana') $j = $joueur->get_comp($row['carac_assoc']) - 2;
			else
			{
				$j = $joueur->get_comp($row['carac_assoc']);
			}
			$de_degat_sort = de_degat($j, $row['effet']);
			$ide = 0;
			$des = '';
			while($ide < count($de_degat_sort))
			{
				if ($ide > 0) $des .= ' + ';
				$des .= '1D'.$de_degat_sort[$ide];
				$ide++;
			}
			$phrase = $des.' dégâts';
		}
		echo '<span class="small">'.$phrase.'</span>';
		echo '</li>';
		$i++;
	}
}
?>
</ul>
	</div>
	<div id="livre_bat"></div>
</div>

<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
