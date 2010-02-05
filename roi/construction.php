<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');

//check_case('all');

if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cette page vous est interdit</p>';
else if(!array_key_exists('direction', $_GET))
{
	echo "<div id='affiche_minimap' style='float:right;'>";
	echo "</div>";
	echo "<div id='contruction'>";
	
	$req = $db->query("SELECT *, placement.royaume AS r, placement.type FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE (placement.type = 'drapeau' OR placement.type = 'arme_de_siege') AND placement.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()."");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste des drapeaux et Armes en Construction sur votre territoire</legend>";
		$boutique_class = 't1';
		echo "<ul>";		
		while($row = $db->read_assoc($req))
		{			
			$royaume_req = new royaume($row['r']);
			$tmp = transform_sec_temp($row['fin_placement'] - time())." avant fin de construction";
			echo "
			<li class='$boutique_class'>
				<span style='display:block;width:220px;float:left;'>";
			
				if ($row['type'] == 'arme_de_siege')
				{
					$batiment = new batiment($row['id_batiment']);
					
					echo "<img src='../image/batiment/".$batiment->get_image()."_04.png' style='width:19px;vertical-align: top;' alt='".$batiment->get_nom()."' />".$batiment->get_nom();
				}
				else
				{
					echo "<img src='../image/drapeaux/drapeau_".$royaume->get_id().".png' style='width:19px;vertical-align: top;' alt='Drapeau' />".$row['nom'];
				}
				echo "</span>
				<span style='display:block;width:100px;float:left;'>".$Gtrad[$royaume_req->get_race()]."</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}			
		}
	echo "</ul>";
	echo "</fieldset>";	
	}
	$req = $db->query("SELECT *, construction.royaume AS r, construction.type FROM construction LEFT JOIN map ON map.id = ((construction.y * 1000) + construction.x) WHERE construction.type = 'arme_de_siege' AND construction.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()."");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste Armes de sieges sur votre territoire</legend>";
		$boutique_class = 't1';
		echo "<ul>";		
		while($row = $db->read_assoc($req))
		{			
			$royaume_req = new royaume($row['r']);
			echo "
			<li class='$boutique_class'>
				<span style='display:block;width:220px;float:left;'>";
				$batiment = new batiment($row['id_batiment']);
				echo "<img src='../image/batiment/".$batiment->get_image()."_04.png' style='width:19px;vertical-align: top;' alt='".$batiment->get_nom()."' />".$batiment->get_nom();
				echo "</span>
				<span style='display:block;width:100px;float:left;'>".$Gtrad[$royaume_req->get_race()]."</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}			
		}
	echo "</ul>";
	echo "</fieldset>";	
	}
	
	$req = $db->query("SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$royaume->get_id()."");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos drapeaux sur territoire énnemi</legend>";	
		echo "<ul>";
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			$royaume_req = new royaume($row['r']);
			if (empty($Gtrad[$royaume_req->get_race()])){$nom = 'Neutre';}else{$nom = $Gtrad[$royaume_req->get_race()];}
			$tmp = transform_sec_temp($row['fin_placement'] - time())."avant fin de construction";
			echo "
			<li class='$boutique_class' onclick=\"minimap(".$row['x'].",".$row['y'].")\" onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'>
				<span style='display:block;width:420px;float:left;'>
					<img src='../image/drapeaux/drapeau_".$royaume->get_id().".png' style='width:19px;' alt='Drapeau' /> ".$row['nom']." chez les ".$nom."
				</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";			
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}						
		}
		echo "</ul>";
		echo "</fieldset>";
	}
	$requete = $db->query("SELECT id FROM construction WHERE royaume = ".$royaume->get_id()."");
	if ($db->num_rows($requete)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos batiments</legend>";	
		echo "<ul>";
		$boutique_class = 't1';		
		while($row = $db->read_assoc($requete))
		{
			$construction = new construction($row['id']);

			$tmp = "HP - ".$construction->get_hp();
			echo "
			<li class='$boutique_class'  onclick=\"minimap(".$construction->get_x().",".$construction->get_y().")\">
				<span style='display:block;width:320px;float:left;'>
					<img src='../image/batiment_low/".$construction->get_image()."_04.png' style='vertical-align : top;' title='".$construction->get_nom()."' /> ".$construction->get_nom();
			$batiment = new batiment($construction->get_id_batiment());
			
			//On peut l'upragder si il y a un suivant
			if($batiment->get_suivant())
			{
				$batiment_suivant = new batiment($batiment->get_suivant());
				
				if ($batiment_suivant->get_cond1() < (time() - $construction->get_date_construction()))
				{
					echo ' - <a href="construction.php?direction=up_construction&amp;id='.$row['id'].'" onclick="if(confirm(\'Voulez vous upgrader ce '.$construction->get_nom().' ?\')) return envoiInfo(this.href, \'message_confirm\'); else return false;">Upgrader - '.$batiment_suivant->get_cout().' stars</a>';
				}
				else
				{
					$tmp = transform_sec_temp($batiment_suivant->get_cond1() - (time() - $construction->get_date_construction()));
					echo "<span style='font-style: italic ;font-size:8pt;'> - update possible dans $tmp</span>";
				}
			}
			echo "</span>";
				
			//my_dump($batiment);
			//my_dump($construction);
			echo "<span style='display:block;width:100px;float:left;'> X : ".$construction->get_x()." - Y : ".$construction->get_y()."</span>";
			$longueur = round(100 * ($construction->get_hp() / $batiment->get_hp()), 2);
			echo "<img style='display:block;width:100px;float:left;height:6px;padding-top:5px;' src='genere_barre_hp.php?longueur=".$longueur."' alt='".$construction->get_hp()." / ".$batiment->get_hp()."' title='".$construction->get_hp()." / ".$batiment->get_hp()."'>";
			
			echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
					<a onclick=\"if(confirm('Voulez vous supprimer ce ".$construction->get_nom()." ?')) {return envoiInfo('construction.php?direction=suppr_construction&amp;id=".$construction->get_id()."', 'message_confirm');} else {return false;};\"><img src='../image/interface/croix_quitte.png'</a>
				</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}									
		}
		echo "</ul>";
		echo "</fieldset>";		
	}
}
elseif($_GET['direction'] == 'suppr_construction')
{
	$construction = new construction($_GET['id']);
	$requete = "SELECT type, royaume FROM construction WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
	if($db->query($requete))
	{
		echo '<h6>La construction a été correctement supprimée.</h6>';
		

		echo "<script type='text/javascript'>
			// <![CDATA[\n

			envoiInfo('construction.php','contenu_jeu');
				// ]]>
		  </script>";
		
		
		//On supprime un bourg au compteur
		if($row[0] == 'bourg')
		{
			supprime_bourg($row[1]);
		}
	}
	
}
elseif($_GET['direction'] == 'up_construction')
{
	$construction = new construction(sSQL($_GET['id']));
	$ancien_batiment = new batiment($construction->get_id_batiment());
	$batiment = new batiment($ancien_batiment->get_suivant());
	if($ancien_batiment->get_suivant() && $royaume->get_star() >= $batiment->get_cout() &&
		 $batiment->get_cond1() < (time() - $construction->get_date_construction()))
	{
		// On modifie la contruction
		$construction->set_id_batiment($batiment->get_id());
		$construction->set_nom($batiment->get_nom());
		$construction->set_image($batiment->get_image());
		$construction->set_date_construction(time());
		$construction->set_hp($batiment->get_hp());
		$construction->set_point_victoire($batiment->get_point_victoire());
		$construction->sauver();
		
		$royaume->set_star($royaume->get_star() - $batiment->get_cout());
		$royaume->sauver();
		echo '<h6>La construction a été correctement upgradée</h6>';
		/*
		//On migre les anciens extracteurs vers le nouveau bourg
		$requete = "UPDATE construction SET rechargement = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rechargement = ".sSQL($_GET['id']);
		$db->query($requete);
		$requete = "UPDATE placement SET rez = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rez = ".sSQL($_GET['id']);
		$db->query($requete);
			*/
	}
	else
	{
		echo "<h5>Construction impossible à upgrader</h5>";
	}


}
echo "</div>";
?>
