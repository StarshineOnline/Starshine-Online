<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);;

$joueur->check_perso();

$position = convert_in_pos($joueur->get_x(), $joueur->get_y());

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$verif_ville = verif_ville($joueur->get_x(), $joueur->get_y());
$W_requete = 'SELECT * FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);
//$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);
$_SESSION['position'] = $position;
?>
<fieldset>
<?php
if($W_row['type'] == 1)
{
	//-- On verifie que le joueur est bien sur la ville ($W_distance)
	echo "<script type='text/javascript'>return nd();</script>";
	echo "<legend>
		   <a href=\"ville.php\" onclick=\"return envoiInfo(this.href, 'centre');\">".$R->get_nom()."</a> >
		   <a href=\"terrain.php\" onclick=\"return envoiInfo(this.href, 'carte');\"> Votre terrain </a>
		  </legend>";
	include_once(root."ville_bas.php");
	?>
<div class="ville_test">
	<?php
	if($verif_ville AND $R->get_diplo($joueur->get_race()) == 127)
	{
		if(array_key_exists('id_construction', $_GET))
		{
			$construction = new terrain_construction($_GET['id_construction']);
			$batiment = $construction->get_batiment();
			switch($batiment->type)
			{
				case 'chambre' :
				break;
				case 'grenier' :
					if(array_key_exists('famine', $_GET))
					{
						if($joueur->get_pa() >= 10)
						{
							$check = false;
							foreach($joueur['debuff'] as $key => $debuff)
							{
								if($debuff['type'] == 'famine')
								{
									$check = true;
									$id_buff = $debuff['id'];
									$key_debuff = $key;
								}
							}
							if($check)
							{
								//Si effet = 1 on supprime le debuff
								if($joueur['debuff'][$key_debuff]['effet'] <= 1)
								{
									$requete = "DELETE FROM buff WHERE id = ".$id_buff;
								}
								//Sinon on réduit
								else
								{
									$requete = "UPDATE buff SET effet = effet - 1 WHERE id = ".$id_buff;
								}
								$db->query($requete);
								$requete = "UPDATE perso SET pa = pa - 10 WHERE ID = ".$joueur->get_id();
								$db->query($requete);
								refresh_perso();
								echo '<h6>Famine réduite de 1%</h6>';
							}
							echo '<h5>Vous n\'avez pas de famine</h5>';
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de PA</h5>';
						}
					}
					echo '<a href="terrain.php?id_construction='.$construction->id.'&amp;famine" onclick="return envoiInfo(this.href, \'carte\');">Réduire de 1% la famine (10 PA)</a>';
				break;
				case 'coffre' :
					$coffre = new coffre($construction->id);
					$coffre_inventaire = $coffre->get_coffre_inventaire();
					//On dépose un objet dans le coffre
					if(array_key_exists('depose', $_GET))
					{
						if(count($coffre_inventaire) < $batiment->effet)
						{
							$item = $joueur->get_inventaire_slot_partie($_GET['depose']);
							$objet = decompose_objet($item);
							//On le met dans le coffre
							$coffre->depose_objet($objet);
							//On supprime l'objet
							$joueur->supprime_objet($item, 1);

							$coffre_inventaire = $coffre->get_coffre_inventaire();
							$joueur->check_perso();
						}
						else echo '<h5>Vous n\'avez pas assez de place dans le coffre</h5>';
					}
					if(array_key_exists('prend', $_GET))
					{
						$item = $coffre_inventaire[$_GET['prend']];
						if($joueur->prend_objet($item->objet))
						{
							$item->moins();
							$coffre_inventaire = $coffre->get_coffre_inventaire();
							$joueur->check_perso();
						}
						else echo '<h5>'.$G_erreur.'</h5>';
					}
					echo '<h3>Place restante : '.($batiment->effet - count($coffre_inventaire)).' / '.$batiment->effet.'</h3>
					<h3>Contenu du coffre</h3>
					';
					foreach($coffre_inventaire as $key => $item)
					{
						$nom = nom_objet($item->objet);
						if($item->nombre > 1) $stack = ' X'.$item->nombre;
						else $stack = '';
						echo $nom.$stack.'<a href="terrain.php?id_construction='.$construction->id.'&amp;prend='.$key.'" onclick="return envoiInfo(this.href, \'carte\');">Prendre</a><br />';
					}
					echo '
					<h3>Votre inventaire</h3>';
					foreach($joueur->get_inventaire_slot_partie() as $key => $item)
					{
						$objet = decompose_objet($item);
						$nom = nom_objet($objet['id']);
						if($objet['stack'] != '') $stack = ' X'.$objet['stack'];
						else $stack = '';
						echo $nom.$stack.' <a href="terrain.php?id_construction='.$construction->id.'&amp;depose='.$key.'" onclick="return envoiInfo(this.href, \'carte\');">Déposer dans votre coffre</a><br />';
					}
				break;
				case 'laboratoire' :
					$types = array();
					//on cherche si il a des instruments
					$requete = "SELECT id, id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE id_laboratoire = ".$construction->id;
					$req = $db->query($requete);
					while($row = $db->read_assoc($req))
					{
						$types[] = "'".$row['type']."'";
						$instrument = new terrain_laboratoire($row);
						$instru = $instrument->get_instrument();
						echo $instru->nom;
						$requete = "SELECT id, nom, prix FROM craft_instrument WHERE requis = ".$instrument->id_instrument;
						$req_i = $db->query($requete);
						if($db->num_rows > 0)
						{
							$taxe = 1 + ($R->get_taxe_diplo($joueur->get_race()) / 100);
							while($row_i = $db->read_assoc($req_i))
							{
								$prix = round($row_i['prix'] * $taxe);
								echo ' <a href="terrain.php?upgrade_instrument='.$row_i['id'].'&amp;labo='.$construction->id.'" onclick="return envoiInfo(this.href, \'carte\');">améliorer en '.$row_i['nom'].' pour '.$prix.' stars</a>';
							}
						}
						echo '<br />';
					}
					echo 'Acheter :<br />';
					$implode_types = implode(', ', $types);
					if($implode_types != '') $not_in = ' AND type NOT IN ('.$implode_types.')';
					else $not_in = '';
					$requete = "SELECT id, nom, prix FROM craft_instrument WHERE requis = 0".$not_in;
					$req = $db->query($requete);
					$taxe = 1 + ($R->get_taxe_diplo($joueur->get_race()) / 100);
					while($row = $db->read_assoc($req))
					{
						$prix = round($row['prix'] * $taxe);
						echo '<a href="terrain.php?achat='.$row['id'].'&amp;labo='.$construction->id.'" onclick="return envoiInfo(this.href, \'carte\');">'.$row['nom'].' ('.$prix.' stars)</a><br />';
					}
				break;
				case 'ecurie' :
					//Le joueur dépose une créature dans l'écurie
					if(array_key_exists('d', $_GET))
					{
						$joueur->pet_to_ecurie($_GET['d'], 2, $batiment->effet);
					}
					//Le joueur reprend une créature de l'écurie
					if(array_key_exists('r', $_GET))
					{
						$joueur->pet_from_ecurie($_GET['r']);
					}
					$joueur->get_pets(true);
					$joueur->get_ecurie_self(true);
					?>
					<h3>Créatures dans votre écurie (<?php echo $joueur->nb_pet_ecurie_self(); ?> / <?php echo $batiment->effet; ?>)</h3>
					<ul>
					<?php
					foreach($joueur->ecurie_self as $pet)
					{
						$pet->get_monstre();
						?>
						<li>
							<?php echo $pet->get_nom(); ?> - <?php echo $pet->monstre->get_nom(); ?> -- HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?> <a href="terrain.php?id_construction=<?php echo $construction->id; ?>&r=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte');"><img src="image/icone/reprendre.png" alt="Reprendre" title="Reprendre" style="width : 16px; height : 16px; vertical-align : top;" /></a>
						</li>
						<?php
					}
					?>
					</ul>
					<h3>Créatures sur vous (<?php echo $joueur->nb_pet(); ?> / <?php echo $joueur->get_comp('max_pet'); ?>)</h3>
					<ul>
					<?php
					foreach($joueur->pets as $pet)
					{
						$pet->get_monstre();
						?>
						<li>
							<?php echo $pet->get_nom(); ?> - <?php echo $pet->monstre->get_nom(); ?> -- HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?> <a href="terrain.php?id_construction=<?php echo $construction->id; ?>&d=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte');"><img src="image/icone/deposer.png" alt="Déposer" title="Déposer" style="width : 16px; height : 16px; vertical-align : top;" /></a>
						</li>
						<?php
					}
					?>
					</ul>
					<?php
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
			<input type="text" id="star_point" nom="star_point" value="10" onkeyup="$('#total').val($('#star_point').val() * <?php echo $batiment->point_structure; ?>);" /> stars par points <input type="button" value="valider" onclick="envoiInfo('terrain.php?upgrade=<?php echo $batiment->id; ?>&amp;star_point=' + $('#star_point').val(), 'carte');"/><br />
			Total : <input type="text" value="<?php echo ($batiment->point_structure * 10); ?>" id="total" />
			<?php
		}
		elseif(array_key_exists('upgrade', $_GET))
		{
			$terrain = new terrain();
			$terrain = $terrain->recoverByIdJoueur($joueur->get_id());
			$batiment = new terrain_batiment($_GET['upgrade']);
			$star_point = ceil($_GET['star_point']);
			$cout_total = $batiment->point_structure * $star_point;
			if($cout_total > 0)
			{
				if($joueur->get_star() >= $cout_total)
				{
					if($batiment->type != 'agrandissement')
					{
						$requete = "SELECT id FROM terrain_construction WHERE id_terrain = ".$terrain->id." AND id_batiment = ".$batiment->requis;
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$construction = new terrain_construction($row['id']);
						$bat_requis = new terrain_batiment($construction->get_id_batiment());
						$nb_case = $batiment->nb_case - $bat_requis->nb_case;
					}
					if(($nb_case <= $terrain->place_restante()) OR $batiment->type == 'agrandissement')
					{
						//On lance le chantier
						$chantier = new terrain_chantier();
						$chantier->id_batiment = $batiment->id;
						$chantier->id_terrain = $terrain->id;
						$chantier->star_point = $star_point;
						if($batiment->type != 'agrandissement') $chantier->upgrade_id_construction = $row['id'];
						$chantier->sauver();
						//On supprime les stars du joueur
						$joueur->add_star(-$cout_total);
						$joueur->sauver();
						$taxe = floor(($chantier->star_point * $batiment->point_structure) * $R->get_taxe_diplo($joueur->get_race()) / 100);
						//On donne les stars au royaume
						$requete = "UPDATE royaume SET star = star + ".$taxe." WHERE ID = ".$R->get_id();
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
			$terrain = $terrain->recoverByIdJoueur($joueur->get_id());
			$batiment = new terrain_batiment($_GET['construire']);
			$star_point = ceil($_GET['star_point']);
			$cout_total = $batiment->point_structure * $star_point;
			if($cout_total > 0)
			{
				if($joueur->get_star() >= $cout_total)
				{
					if($batiment->nb_case <= $terrain->place_restante())
					{
						//On lance le chantier
						$chantier = new terrain_chantier();
						$chantier->id_batiment = $batiment->id;
						$chantier->id_terrain = $terrain->id;
						$chantier->star_point = $star_point;
						$chantier->sauver();
						//On supprime les stars du joueur
                        $joueur->set_star($joueur->get_star() - $cout_total);
                        $joueur->sauver();
						$taxe = floor(($chantier->star_point * $batiment->point_structure) * $R->get_taxe_diplo($joueur->get_race()) / 100);
						//On donne les stars au royaume
						$requete = "UPDATE royaume SET star = star + ".$taxe." WHERE ID = ".$R->get_id();
						$db->query($requete);
						echo '<h6>Le chantier a commencé !</h6>
						<a href="ville.php" onclick="return envoiInfo(this.href, \'carte\');">Retour à la ville</a>';
					}
					else echo '<h5>Vous n\'avez pas assez de place</h5>';
				}
				else echo '<h5>Vous n\'avez pas assez de stars</h5>';
			}
		}
		elseif(array_key_exists('achat', $_GET))
		{
			$instrument = new craft_instrument($_GET['achat']);
			$taxe = round($R->get_taxe_diplo($joueur->get_race()) * $instrument->prix / 100);
			$prix = $instrument->prix + $taxe;
			if($prix > 0)
			{
				if($joueur->get_star() >= $prix)
				{
					$laboratoire = new terrain_laboratoire();
					$laboratoire->id_laboratoire = $_GET['labo'];
					$laboratoire->id_instrument = $instrument->id;
					$laboratoire->type = $instrument->type;
					$laboratoire->sauver();
					$joueur->set_star($joueur->get_star() - $prix);
					$joueur->sauver();
					$R->set_star($R->get_star() + $prix);
					$R->sauver();
				}
				else echo '<h5>Vous n\'avez pas assez de stars</h5>';
			}
		}
		elseif(array_key_exists('upgrade_instrument', $_GET))
		{
			$instrument = new craft_instrument($_GET['upgrade_instrument']);
			$taxe = round($R->get_taxe_diplo($joueur->get_race()) * $instrument->prix / 100);
			$prix = $instrument->prix + $taxe;
			if($prix > 0)
			{
				if($joueur->get_star() >= $prix)
				{
					$requete = "SELECT id, id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE type = '".$instrument->type."' AND id_laboratoire = ".$_GET['labo'];
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$laboratoire = new terrain_laboratoire($row);
					$laboratoire->id_instrument = $instrument->id;
					$laboratoire->sauver();
					$joueur->set_star($joueur->get_star() - $prix);
					$joueur->sauver();
					$R->set_star($R->get_star() + $prix);
					$R->sauver();
				}
				else echo '<h5>Vous n\'avez pas assez de stars</h5>';
			}
		}
		else
		{
			$terrain = new terrain();
			$terrain = $terrain->recoverByIdJoueur($joueur->get_id());
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
				$requete = "SELECT id, point_structure FROM terrain_batiment WHERE type = 'agrandissement' AND requis = ".$terrain->nb_case;
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
			if($implode_types != '') $not_in = "AND type NOT IN (".$implode_types.")";
			else $not_in = '';
			$requete = "SELECT id, nom, point_structure FROM terrain_batiment WHERE requis = 0 ".$not_in." AND nb_case <= ".$terrain->place_restante();
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
				<input type="text" id="star_point" nom="star_point" value="10" onkeyup="$('total').val($('#star_point').val() * <?php echo $batiment->point_structure; ?>);" /> stars par points<br />
				<input type="button" value="Valider" onclick="envoiInfo('terrain.php?construire=' + $('#construction').val() + '&amp;star_point=' + $('#star_point').val(), 'carte');" />
				<?php
			}
			?>
			<?php
		}
	}
	else
		echo 'Ce n\'est pas votre royaume.';
	?>
</div>
<?php
}
?>
</fieldset>