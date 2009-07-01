<?php
require('haut_roi.php');

check_case('all');

if($joueur['rang_royaume'] != 6)
	echo '<p>Cheater</p>';
else if(!array_key_exists('direction', $_GET))

{
	echo "<div id='affiche_minimap' style='float:right;'>";
	
	echo "</div>";
	echo "<div id='contruction'>";
	
	$requete = "SELECT *, placement.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume != ".$R['ID']." AND map.royaume = ".$R['ID'];
	$req = $db->query($requete);
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste des drapeaux ennemis sur votre territoire</legend>";
		$boutique_class = 't1';
		echo "<ul>";		
		while($row = $db->read_assoc($req))
		{			
			$Royaume = get_royaume_info($joueur['race'], $row['r']);
			$tmp = transform_sec_temp($row['fin_placement'] - time())." avant fin de construction";
			echo "
			<li class='$boutique_class' onclick=\"minimap(".$row['x'].",".$row['y'].")\" onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'>
				<span style='display:block;width:40px;float:left;'>
					<img src='../image/drapeaux/drapeau_".$R['ID'].".png' style='width:19px;' alt='Drapeau' />".$row['nom']."
				</span>
				<span style='display:block;width:100px;float:left;'>".$Gtrad[$Royaume['race']]."</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
				<span style='display:block;width:30px;float:left;cursor:pointer;' onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'><img src='../image/icone/mobinfo.png' alt='Avoir les informations' title='Avoir les informations' /></span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}			
		}
	echo "</ul>";
	echo "</fieldset>";	
	}
	$requete = "SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$R['ID'];
	$req = $db->query($requete);
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos drapeaux sur territoire énnemi</legend>";	
		echo "<ul>";
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			$Royaume = get_royaume_info($joueur['race'], $row['r']);			
			if (empty($Gtrad[$Royaume['race']])){$nom = 'Neutre';}else{$nom = $Gtrad[$Royaume['race']];}
			$tmp = transform_sec_temp($row['fin_placement'] - time())."avant fin de construction";
			echo "
			<li class='$boutique_class' onclick=\"minimap(".$row['x'].",".$row['y'].")\" onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'>
				<span style='display:block;width:420px;float:left;'>
					<img src='../image/drapeaux/drapeau_".$R['ID'].".png' style='width:19px;' alt='Drapeau' /> ".$row['nom']." chez les ".$nom."
				</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";			
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}						
		}
		echo "</ul>";
		echo "</fieldset>";
	}
	$requete = "SELECT * FROM construction WHERE royaume = ".$R['ID'];
	$req = $db->query($requete);
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos batiments</legend>";	
		echo "<ul>";
		$boutique_class = 't1';		
		while($row = $db->read_assoc($req))
		{
			$tmp = "HP - ".$row['hp'];
			echo "
			<li class='$boutique_class'  onclick=\"minimap(".$row['x'].",".$row['y'].")\" onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'>
				<span style='display:block;width:420px;float:left;'>
					<img src='../image/batiment_low/".$row['image']."_04.png' style='vertical-align : top;' title='".$row['nom']."' /> ".$row['nom']."
				</span>
				<span style='display:block;width:100px;float:left;'> X : ".$row['x']." - Y : ".$row['y']."</span>
				<span style='display:block;width:30px;float:left;cursor:pointer;'>
					<a href='construction.php?direction=suppr_construction&amp;id=".$row['id']."' onclick=\"if(confirm('Voulez vous supprimer ce ".$row['nom']." ?')) return envoiInfo(this.href, \'conteneur\'); else return false;\"><img src='../image/interface/croix_quitte.png'</a>
				</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}									
			if($row['type'] == 'bourg')
			{
				$bat = recupbatiment($row['id_batiment'], 'none');
				//On peut l'upragder
				if($bat['nom'] != 'Bourg')
				{
					$bat_suivant = recupbatiment(($row['id_batiment'] + 1), 'none');
					echo ' - <a href="construction.php?direction=up_construction&amp;id='.$row['id'].'" onclick="if(confirm(\'Voulez vous upgrader ce '.$row['nom'].' ?\')) return envoiInfo(this.href, \'conteneur\'); else return false;">Upgrader - '.$bat_suivant['cout'].' stars</a>';
				}
			}
		}
		echo "</ul>";
		echo "</fieldset>";		
	}
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
	echo '<a href="construction.php" onclick="return envoiInfo(this.href, \'conteneur\');">Retour à la liste des drapeaux et constructions</a>';
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
			$bourg_id = $db->last_insert_id();
			$requete = "UPDATE royaume SET star = star - ".$bat['cout']." WHERE ID = ".$R['ID'];
			$db->query($requete);
			echo 'La construction a été correctement upgradée.';
		}
		//On migre les anciens extracteurs vers le nouveau bourg
		$requete = "UPDATE construction SET rechargement = ".$bourg_id." WHERE type = 'mine' AND rechargement = ".$_GET['id'];
		$db->query($requete);
		$requete = "UPDATE placement SET rez = ".$bourg_id." WHERE type = 'mine' AND rez = ".$_GET['id'];
		$db->query($requete);
	}
}
echo "</div>";
?>