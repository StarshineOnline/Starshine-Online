<?php
include ('livre.php');
$tab_sort_jeu = explode(';', $joueur['comp_combat']);
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
foreach($magies as $magie)
{
	echo '<a href="javascript:envoiInfo(\'competence.php?tri='.$magie.'\', \'information\');"><img src="image/'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'"/></a> ';
}
$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
$requete = "SELECT * FROM comp_combat ".$where." ORDER BY comp_assoc ASC, type ASC";
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
		echo '<div style="z-index: 3;">';
		$href = '';
		$cursor = '';
		$color = 'black';
		echo '
		<tr>
			<td>
				<span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href.'" onmousemove="afficheInfo(\'info_'.$i.'\', \'block\', event, \'xmlhttprequest\');" onmouseout="afficheInfo(\'info_'.$i.'\', \'none\', event );"> <strong>'.$row['nom'].'</strong></span>';
				?>
				<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
				<?php
				echo description($row['description'], $row);
				?>
				</div>
			</td>
			<td>
				<span class="xsmall"> utilise <?php echo $row['mp']; ?> Réserve Mana</span>
			</td>
			<td>
				<span class="small">Utilisable avec : <?php echo implode(' - ', explode(';', $row['arme_requis'])); ?></span>
			</td>
		</tr>
		</div>
		<?php
		$i++;
	}
}
echo '</table>';
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />