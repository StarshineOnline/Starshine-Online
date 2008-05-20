<?php
include ('livre.php');
$tab_sort_jeu = explode(';', $joueur['sort_combat']);
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
foreach($magies as $magie)
{
	echo '<a href="javascript:envoiInfo(\'sort_combat.php?tri='.$magie.'\', \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
}
$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
$requete = "SELECT * FROM sort_combat ".$where." ORDER BY comp_assoc ASC, type ASC, nom ASC";
$req = $db->query($requete);
$magie = '';
echo '<table width="97%" class="information_case">';
while($row = $db->read_array($req))
{
	if($magie != $row['comp_assoc'])
	{
		$magie = $row['comp_assoc'];
		echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
	}
	if(in_array($row['id'], $tab_sort_jeu))
	{
		if($Gtrad[$row['type']] != $Gtrad[$type])
		{
			$type = $row['type'];
			echo '<h3>'.$Gtrad[$row['type']].'</h3>';
		}
		?>
		<div style="z-index: 3;">
		<?php
		$href = '';
		$cursor = '';
		$color = 'black';
		$phrase = '';
		?>
		<tr>
			<td>
				<span style="<?php echo $cursor; ?>text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>" onmousemove="afficheInfo('info_<?php echo $i; ?>', 'block', event, 'xmlhttprequest');" onmouseout="afficheInfo('info_<?php echo $i; ?>', 'none', event );"><strong><?php echo $row['nom']; ?></strong></span>
				<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
				<?php
					echo description($row['description'], $row).'<br /><span class="xmall">Incantation : '.$row['incantation'].'</span>';
				?>
				</div>
			</td>
			<td>
				<span class="xsmall"> utilise <?php echo round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10))); ?> Réserve Mana</span>
			</td>
		<?php
		$sort_de_degat = array('degat_feu', 'degat_nature', 'drain_vie', 'degat_froid', 'degat_mort', 'degat_vent', 'degat_terre', 'pacte_sang'
		, 'brisement_os', 'lapidation', 'globe_foudre', 'vortex_vie', 'vortex_mana', 'putrefaction', 'embrasement', 'sphere_glace');
		if(in_array($row['type'], $sort_de_degat))
		{
			if($row['type'] == 'drain_vie') $j = $joueur[$row['carac_assoc']] - 2;
			else $j = $joueur[$row['carac_assoc']];
			$de_degat_sort = de_degat($j, $row['effet']);
			$ide = 0;
			$des = '';
			while($ide < count($de_degat_sort))
			{
				if ($ide > 0) $des .= ' + ';
				$des .= '1D'.$de_degat_sort[$ide];
				$ide++;
			}
			$phrase = 'Inflige '.$des.' dégats';
		}
		echo '<td><span class="small">'.$phrase.'</span></td>';
		echo '</tr>';
		?>
		</div>
		<?php
		$i++;
	}
}
echo '</table>';
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />