<?php
require('haut_roi.php');

check_case('all');
if(!array_key_exists('direction', $_GET))
{
	echo '<h3>Liste des drapeaux ennemis sur votre territoire</h3>';
	$requete = "SELECT *, placement.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume != ".$R['ID']." AND map.royaume = ".$R['ID'];
	$req = $db->query($requete);
	echo '<table  style="width:100%;">';
	while($row = $db->read_assoc($req))
	{
		$Royaume = get_royaume_info($joueur['race'], $row['r']);
		echo '
		<tr>
			<td style="width:33%;">
				<img src="../image/drapeau.gif" style="vertical-align : top;" title="Drapeau" alt="Drapeau" /> '.$row_b['nom'].'
			<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
				'.transform_sec_temp($row['fin_placement'] - time()).' avant fin de construction 
			</div>
			</td>
			<td style="width:33%;">
				'.$Gtrad[$Royaume['race']].'
			</td>
			<td style="width:33%;">
				X : '.$row['x'].' - Y : '.$row['y'].'
			</td>
		</tr>';
	}
	echo '</table>';
	echo '<h3>Liste de vos drapeaux sur territoire énnemi</h3>';
	$requete = "SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$R['ID'];
	$req = $db->query($requete);
	echo '<table  style="width:100%;">';
	while($row = $db->read_assoc($req))
	{
		$Royaume = get_royaume_info($joueur['race'], $row['r']);
		echo '
		<tr>
			<td style="width:33%;">
				<span onmousemove="afficheInfo(\'info_'.$row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$row['id'].'\', \'none\', event, \'centre\');"><img src="../image/drapeau.gif" style="vertical-align : top;" title="Drapeau" alt="Drapeau" /> '.$row_b['nom'].' chez '.$Gtrad[$Royaume['race']].'</span>
				<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
					'.transform_sec_temp($row['fin_placement'] - time()).' avant fin de construction 
				</div>
			</td>
			<td style="width:33%;">
				X : '.$row['x'].' - Y : '.$row['y'].'
			</td>
		</tr>';
	}
	echo '</table>';
	echo '<h3>Liste de vos batiments</h3>';
	$requete = "SELECT * FROM construction WHERE royaume = ".$R['ID'];
	$req = $db->query($requete);
	echo '<table  style="width:100%;">';
	while($row = $db->read_assoc($req))
	{
		echo '
		<tr>
			<td style="width:33%;">
				<span onmousemove="afficheInfo(\'info_'.$row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$row['id'].'\', \'none\', event, \'centre\');"><img src="../image/mini_fortin.png" style="vertical-align : top;" title="'.$row['nom'].'" alt="'.$row['nom'].'" /> '.$row['nom'].'</span> </td><td style="width:33%;"> X : '.$row['x'].' - Y : '.$row['y'].'
				<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
					HP - '.$row['hp'].' 
				</div>
			</td>
			<td style="width:33%;">
				<a href="javascript:if(confirm(\'Voulez vous supprimer ce '.$row['nom'].' ?\')) envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=suppr_construction&amp;id='.$row['id'].'">Supprimer</a>
			</td>
		</tr>';
		if($row['type'] == 'bourg')
		{
			$bat = recupbatiment($row['id_batiment'], 'none');
			//On peut l'upragder
			if($bat['nom'] != 'Bourg')
			{
				$bat_suivant = recupbatiment(($row['id_batiment'] + 1), 'none');
				echo ' - <a href="javascript:if(confirm(\'Voulez vous upgrader ce '.$row['nom'].' ?\')) envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=up_construction&amp;id='.$row['id'].'">Upgrader - '.$bat_suivant['cout'].' stars</a>';
			}
		}
	}
	echo '</table>';
}
elseif($_GET['direction'] == 'suppr_construction')
{
	$requete = "SELECT type, royaume FROM construction WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
	if($db->query($requete))
	{
		echo 'La construction a été correctement supprimée.';
		//On supprime un bourg au compteur
		if($row[0] == 'bourg')
		{
			supprime_bourg($row[1]);
		}
	}
	echo '<a href="gestion_royaume.php?poscase='.$W_case.'&amp;direction=drapeau">Retour à la liste des drapeaux et constructions</a>';
}
elseif($_GET['direction'] == 'up_construction')
{
	$requete = "SELECT x, y, type, id_batiment, royaume FROM construction WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$bat = recupbatiment(($row['id_batiment'] + 1), 'none');
	//Si le royaume a assez de stars
	if($R['star'] >= $bat['cout'])
	{
		//On supprime l'ancien bourg
		$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
		$db->query($requete);
		//On place le nouveau
		$requete = "INSERT INTO construction VALUES('', ".$bat['id'].", ".$row['x'].", ".$row['y'].", ".$row['royaume'].", ".$bat['hp_max'].", '".$bat['nom']."', '".$row['type']."', 0, 0, '".$Gtrad[$bat['nom']]."')";
		if($db->query($requete))
		{
			$requete = "UPDATE royaume SET star = star - ".$bat['cout']." WHERE ID = ".$R['ID'];
			$db->query($requete);
			echo 'La construction a été correctement upgradée.';
		}
	}
}
?>