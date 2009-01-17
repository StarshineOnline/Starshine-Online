<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

$position = convert_in_pos($joueur['x'], $joueur['y']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$verif_ville = verif_ville($joueur['x'], $joueur['y']);
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($position).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$_SESSION['position'] = $position;
?>
	<?php 
	include('ville_bas.php');
	if($verif_ville AND $R['diplo'] == 127)
	{
		if(array_key_exists('id_construction', $_GET))
		{
			$construction = new terrain_construction($_GET['id_construction']);
			$batiment = $construction->get_batiment();
			switch($batiment->type)
			{
				case 'coffre' :
					echo 'C\'est un coffre';
				break;
			}
			$requete = "SELECT id, point_structure FROM terrain_batiment WHERE type = '".$batiment->type."' AND requis = ".$batiment->id;
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_assoc($req);
				echo '<br /><a href="terrain.php?id_upgrade='.$row['id'].'" onclick="return envoiInfo(this.href, \'carte\');">Améliorer ('.$row['point_structure'].'points de structure)</a>';
			}
		}
		elseif(array_key_exists('id_upgrade', $_GET))
		{
			$batiment = new terrain_batiment($_GET['id_upgrade']);
			?>
			Améliorer en : <?php echo $batiment->nom; ?><br />
			<?php echo $batiment->point_structure; ?>  sont nécessaire à l'amélioration de ce batiment.<br />
			<br />
			Combien voulez vous rémunérer chaque point de structure construit ?<br />
			<input type="text" id="star_point" nom="star_point" value="10" onkeyup="$('total').value = $('star_point').value * <?php echo $batiment->point_structure; ?>;" /> stars par points <input type="button" value="valider" onclick="envoiInfo('terrain.php?upgrade=<?php echo $batiment->id; ?>&amp;star_point=' + $('star_point').value, 'carte');"/><br />
			Total : <input type="text" value="<?php echo ($batiment->point_structure * 10); ?>" id="total" />
			<?php
		}
		elseif(array_key_exists('upgrade', $_GET))
		{
			$terrain = new terrain();
			$terrain = $terrain->recoverByIdJoueur($joueur['ID']);
			$batiment = new terrain_batiment($_GET['upgrade']);
			$cout_total = $batiment->point_structure * $_GET['star_point'];
			if($cout_total > 0)
			{
				if($joueur['star'] >= $cout_total)
				{
					if($batiment->type != 'agrandissement')
					{
						$requete = "SELECT id FROM terrain_construction WHERE id_terrain = ".$terrain->id." AND id_batiment = ".$batiment->requis;
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$construction = new construction($row['id']);
						$bat_requis = $construction->get_batiment();
						$nb_case = $batiment->nb_case - $bat_requis->nb_case;
					}
					if(($nb_case <= $terrain->place_restante()) OR $batiment->type == 'agrandissement')
					{
						//On lance le chantier
						$chantier = new terrain_chantier();
						$chantier->id_batiment = $batiment->id;
						$chantier->id_terrain = $terrain->id;
						$chantier->star_point = $_GET['star_point'];
						if($batiment->type != 'agrandissement') $chantier->upgrade_id_construction = $row['id'];
						$chantier->sauver();
						//On supprime les stars du joueur
						$requete = "UPDATE perso SET star = star - ".$cout_total." WHERE ID = ".$joueur['ID'];
						$db->query($requete);
						$taxe = floor(($chantier->star_point * $batiment->point_structure) * $R['taxe'] / 100);
						//On donne les stars au royaume
						$requete = "UPDATE royaume SET star = star + ".$taxe." WHERE ID = ".$R['ID'];
						$db->query($requete);
					}
					else echo '<h5>Vous n\'avez pas assez de place</h5>';
				}
				else echo '<h5>Vous n\'avez pas assez de stars</h5>';
			}
		}
		elseif(array_key_exists('construire', $_GET))
		{
			$terrain = new terrain();
			$terrain = $terrain->recoverByIdJoueur($joueur['ID']);
			$batiment = new terrain_batiment($_GET['construire']);
			$cout_total = $batiment->point_structure * $_GET['star_point'];
			if($cout_total > 0)
			{
				if($joueur['star'] >= $cout_total)
				{
					if($batiment->nb_case <= $terrain->place_restante())
					{
						//On lance le chantier
						$chantier = new terrain_chantier();
						$chantier->id_batiment = $batiment->id;
						$chantier->id_terrain = $terrain->id;
						$chantier->star_point = $_GET['star_point'];
						$chantier->sauver();
						//On supprime les stars du joueur
						$requete = "UPDATE perso SET star = star - ".$cout_total." WHERE ID = ".$joueur['ID'];
						$db->query($requete);
						$taxe = floor(($chantier->star_point * $batiment->point_structure) * $R['taxe'] / 100);
						//On donne les stars au royaume
						$requete = "UPDATE royaume SET star = star + ".$taxe." WHERE ID = ".$R['ID'];
						$db->query($requete);
					}
					else echo '<h5>Vous n\'avez pas assez de place</h5>';
				}
				else echo '<h5>Vous n\'avez pas assez de stars</h5>';
			}
		}
		else
		{
			$terrain = new terrain();
			$terrain = $terrain->recoverByIdJoueur($joueur['ID']);
			$constructions = $terrain->get_constructions();
			$chantiers = $terrain->get_chantiers();
			$upgrade = true;
			$types = array();
			foreach($chantiers as $chantier)
			{
				$batiment = $chantier->get_batiment();
				if($batiment->type == 'agrandissement') $upgrade = false;
				$chantiers_echo .= ucwords($batiment->type).' ('.$chantier->star_point.' stars par point) => '.$chantier->point.' / '.$batiment->point_structure.'<br />';
				$types[] = "'".$batiment->type."'";
			}
			//On cherche si on peut upgrader le terrain
			if($terrain->nb_case < 5 && $upgrade)
			{
				$requete = "SELECT id, point_structure FROM terrain_batiment WHERE type = 'agrandissement' AND requis = 2";
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$upgrade = ' - <a href="terrain.php?id_upgrade='.$row['id'].'" onclick="return envoiInfo(this.href, \'carte\');">+1 case ('.$row['point_structure'].'points de structure)</a>';
			}
			else $upgrade = '';
			echo 'Place restante : '.$terrain->place_restante().' / '.$terrain->nb_case.$upgrade.'<br />';
			foreach($terrain->constructions as $construction)
			{
				$batiment = $construction->get_batiment();
				$types[] = "'".$batiment->type."'";
				echo '<a href="terrain.php?id_construction='.$construction->id.'" onclick="return envoiInfo(this.href, \'carte\');">'.ucwords($batiment->type).'</a><br />';
			}
			echo 'Liste des batiments en construction :<br />';
			echo $chantiers_echo;
			$implode_types = implode(', ', $types);
			$requete = "SELECT id, nom, point_structure FROM terrain_batiment WHERE requis = 0 AND type NOT IN (".$implode_types.") AND nb_case <= ".$terrain->place_restante();
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				echo 'Construire : 
				<select id="construction">';
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?> (<?php echo $row['point_structure']; ?> point structure)</option>
					<?php
				}
				?>
				</select><br />
				Combien voulez vous rémunérer chaque point de structure construit ?<br />
				<input type="text" id="star_point" nom="star_point" value="10" onkeyup="$('total').value = $('star_point').value * <?php echo $batiment->point_structure; ?>;" /> stars par points<br />
				Total : <input type="text" value="<?php echo ($batiment->point_structure * 10); ?>" id="total" />
				<input type="button" value="Valider" onclick="envoiInfo('terrain.php?construire=' + $('construction').value + '&amp;star_point=' + $('star_point').value, 'carte');" />
				<?php
			}
		}
	}
	?>