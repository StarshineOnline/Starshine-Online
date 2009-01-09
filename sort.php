<?php
include ('livre.php');
$tab_sort_jeu = explode(';', $joueur['sort_jeu']);
if(array_key_exists('tri', $_GET)) $tris = $_GET['tri']; else $tris = 'favoris';
?>
<hr>
<?php
if($joueur['groupe'] != 0) $groupe_joueur = recupgroupe($joueur['groupe'], $joueur['x'].'-'.$joueur['y']); else $groupe_joueur = false;
if (isset($_GET['ID']))
{
	$requete = "SELECT * FROM sort_jeu WHERE id = ".sSQL($_GET['ID']);
	$req = $db->query($requete);

	$row = $db->read_array($req);
	if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes') $groupe = true; else $groupe = false;
	$sortpa_base = $row['pa'];
	$sortmp_base = $row['mp'];
	$sortpa = round($row['pa'] * $joueur['facteur_magie']);
	$sortmp = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
	//Réduction du cout par concentration
	if(array_key_exists('buff_concentration', $joueur['buff'])) $sortmp = ceil($sortmp * (1 - ($joueur['buff']['buff_concentration']['effet'] / 100)));
	//Coût en MP * 1.5 si sort de groupe
	if($groupe) $sortmp = ceil($sortmp * 1.5);
	$action = false;
	if($groupe_joueur AND $groupe)
	{
		$cibles = array();
		foreach($groupe_joueur['membre'] as $membre)
		{
			//On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
			if($membre['distance'] <= 7) $cibles[] = $membre['id_joueur'];
		}
	}
	else
	{
		$cibles = array($joueur['ID']);
	}
	if($joueur['pa'] < $sortpa)
	{
		echo '<h5>Pas assez de PA</h5>';
	}
	elseif($joueur['mp'] < $sortmp)
	{
		echo '<h5>Pas assez de mana</h5>';
	}
	elseif($joueur['hp'] <= 0)
	{
		echo '<h5>Vous êtes mort</h5>';
	}
	else
	{
		switch($row['type'])
		{
			case 'vie' :
				$soin_total = 0;
				foreach($cibles as $cible)
				{
					$cible_s = recupperso($cible);
					if($cible_s['hp'] > 0)
					{
						if($cible_s['hp'] < floor($cible_s['hp_max']))
						{
							$action = true;
							$de_degat_sort = de_soin($joueur[$row['carac_assoc']], $row['effet']);
							$i = 0;
							$de_degat_sort2 = array();
							while($i < count($de_degat_sort))
							{
								$de_degat_sort2[$de_degat_sort[$i]] += 1;
								$i++;
							}
							$i = 0;
							$keys = array_keys($de_degat_sort2);
							while($i < count($de_degat_sort2))
							{
								if ($i > 0) echo ' + ';
								echo $de_degat_sort2[$keys[$i]].'D'.$keys[$i];
								$i++;
							}
							echo '<br />';
							$soin = 0;
							$i = 0;
							while($i < count($de_degat_sort))
							{
								$soin += rand(1, $de_degat_sort[$i]);
								$i++;
							}
							if($soin > (floor($cible_s['hp_max']) - $cible_s['hp'])) $soin = floor($cible_s['hp_max']) - $cible_s['hp'];
							echo 'Vous soignez '.$cible_s['nom'].' de '.$soin.' HP<br />';
							$soin_total += $soin;
							$cible_s['hp'] = $cible_s['hp'] + $soin;
							$requete = "UPDATE perso SET hp = '".$cible_s['hp']."' WHERE ID = '".$cible_s['ID']."'";
							$req = $db->query($requete);
							if($groupe)
							{
								//Insertion du soin de groupe dans le journal de la cible
								$requete = "INSERT INTO journal VALUES('', ".$cible_s['ID'].", 'rgsoin', '".$cible_s['nom']."', '".$joueur['nom']."', NOW(), ".$soin.", 0, ".$joueur['x'].", ".$joueur['y'].")";
								$db->query($requete);
							}
						}
						else
						{
							echo 'La cible a toute sa vie<br />';
						}
					}
					else
					{
						echo $cible_s['nom'].' est mort.<br />';
					}
				}
				$joueur['pa'] = $joueur['pa'] - $sortpa;
				$joueur['mp'] = $joueur['mp'] - $sortmp;
				if($action)
				{
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_vie', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('sort_vie', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['sort_vie'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['sort_vie'].' en '.$Gtrad['sort_vie'].'</span><br />';
					}
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', sort_vie = '".$joueur['sort_vie']."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
					//Insertion du soin de groupe dans le journal du lanceur
					if($groupe)
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'gsoin', '".$joueur['nom']."', 'groupe', NOW(), ".$soin_total.", 0, ".$joueur['x'].", ".$joueur['y'].")";
					}
					else
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'soin', '".$joueur['nom']."', '".$joueur['nom']."', NOW(), ".$soin_total.", 0, ".$joueur['x'].", ".$joueur['y'].")";
					}
					$db->query($requete);
				}
				if($groupe) $groupe_href = '&amp;groupe=yes'; else $groupe_href = '';
				echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau ce sort</a>';
			break;
			case 'body_to_mind' :
				if($joueur['hp'] > $row['effet'])
				{
					$sorthp = $row['effet'];
					$sortmp = $row['effet2'];
					if($sortmp > (floor($joueur['mp_max']) - $joueur['mp'])) $sortmp = floor($joueur['mp_max']) - $joueur['mp'];
					echo 'Vous utilisez '.$sorthp.' HP pour convertir en '.$sortmp.' MP<br />';
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] + $sortmp;
					$joueur['hp'] = $joueur['hp'] - $sorthp;
					if($sortmp > 0)
					{
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sorthp);
						$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur['incantation'] = $augmentation[0];
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
						}
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_mort', $sortpa_base, $sorthp);
						$augmentation = augmentation_competence('sort_mort', $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur['sort_mort'] = $augmentation[0];
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['sort_mort'].' en '.$Gtrad['sort_mort'].'</span><br />';
						}
						$requete = "UPDATE perso SET hp = '".$joueur['hp']."', mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', sort_mort = '".$joueur['sort_mort']."' WHERE ID = '".$_SESSION['ID']."'";
						$req = $db->query($requete);
					}
					echo '<a href="sort.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau ce sort</a>';
				}
				else
				{
					echo 'Vous n\'avez pas assez de points de vie.';
				}
			break;
			case 'teleport' :
				if($joueur['hp'] > 0)
				{
					$joueur['x'] = $Trace[$joueur['race']]['spawn_x'];
					$joueur['y'] = $Trace[$joueur['race']]['spawn_y'];
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_element', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('sort_element', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['sort_element'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['sort_element'].' en '.$Gtrad['sort_element'].'</span><br />';
					}
					$requete = "UPDATE perso SET x = '".$joueur['x']."', y = '".$joueur['y']."', mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', sort_element = '".$joueur['sort_element']."' WHERE ID = '".$_SESSION['ID']."'";
					if($db->query($requete)) echo 'Vous vous êtes téléporté dans votre capitale
					<img src="image/pixel.gif" onLoad="envoiInfo(\'deplacement.php\', \'centre\');" />';
				}
				else
				{
					echo 'Le joueur est mort.';
				}
			break;
			case 'repos_sage' :
				//On vérifie qu'il a pas déjà le debuff
				if(!array_key_exists('repos_sage', $joueur['debuff']))
				{
					//Mis en place du debuff
					lance_buff($row['type'], $joueur['ID'], 1, 0, $row['duree'], $row['nom'], 'Vous ne pouvez plus attaquer ni lancer le sort repos du sage', 'perso', 1, 0, 0);
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] + $row['effet'];
					if($joueur['mp'] > $joueur['mp_max']) $joueur['mp'] = $joueur['mp_max'];
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, 1);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, 1);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', ".$row['comp_assoc']." = '".$joueur[$row['comp_assoc']]."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
				}
				else
				{
					echo 'Vous êtes déjà reposé';
				}
			break;
			case 'buff_critique' : case 'buff_evasion' : case 'buff_bouclier' : case 'buff_sacrifice' : case 'buff_inspiration' : case 'buff_force' : case 'buff_armure_glace' : case 'buff_barriere' : case 'buff_bouclier_sacre' : case 'buff_colere' : case 'buff_epine' : case 'buff_meditation' : case 'buff_rage_vampirique' : case 'buff_rapidite' : case 'buff_concentration' : case 'buff_furie_magique' : case 'buff_surpuissance' : case 'bouclier_feu' : case 'bouclier_terre' : case 'bouclier_eau' : case 'souffrance_extenuante' : case 'bulle_sanctuaire' : case 'bulle_dephasante' :
				foreach($cibles as $cible)
				{
					$cible_s = recupperso($cible);
					//Mis en place du buff
					if(lance_buff($row['type'], $cible_s['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible_s['buff']), $cible_s['rang_grade']))
					{
						$action = true;
						echo $cible_s['nom'].' a bien reçu le buff<br />';
						//Insertion du buff dans le journal de la cible
						if($groupe)
						{
							$requete = "INSERT INTO journal VALUES('', ".$cible_s['ID'].", 'rgbuff', '".$cible_s['nom']."', '".$joueur['nom']."', NOW(), '".$row['nom']."', 0, 0, 0)";
						}
						$db->query($requete);
					}
					else
					{
						if($G_erreur == 'puissant') echo $cibles_s.' bénéficie d\'un buff plus puissant<br />';
						else echo $cible_s['nom'].' a trop de buffs.<br />';
					}
				}
				if($action)
				{
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', ".$row['comp_assoc']." = '".$joueur[$row['comp_assoc']]."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
					if($groupe)
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'gbuff', '".$joueur['nom']."', 'groupe', NOW(), '".$row['nom']."', 0, 0, 0)";
						$db->query($requete);
					}
					else
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'buff', '".$joueur['nom']."', '".$joueur['nom']."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
					}
				}
			break;
			case 'engloutissement' : case 'deluge' : case 'blizzard' : case 'orage_magnetique' :
				//Récupération de tous les joueurs de la case
				$requete = "SELECT ID FROM perso WHERE x = ".$joueur['x']." AND y = ".$joueur['y']." AND statut = 'actif'";
				$req_j = $db->query($requete);
				$cibles = array();
				while($row_j = $db->read_row($req_j))
				{
					$cible = recupperso($row_j[0]);
					//Si c'est pas le joueur
					if($cible['ID'] != $joueur['ID'])
					{
						//Test d'esquive du sort
						$protecion = $cible['volonte'] * $cible['PM'] / 3;
						if(array_key_exists('bulle_sanctuaire', $cible['buff'])) $protection *= $cible['buff']['bulle_sanctuaire']['effet'];
						if(array_key_exists('bulle_dephasante', $cible['buff'])) $protection *= $cible['buff']['bulle_dephasante']['effet'];
						$attaque = rand(0, ($joueur['volonte'] * $joueur[$row['comp_assoc']]));
						$defense = rand(0, $protection);
						if ($attaque > $defense)
						{
							//Mis en place du debuff pour tous
							if(lance_buff($row['type'], $cible['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0))
							{
								echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible['nom'].'<br />';
								//Insertion du debuff dans les journaux des 2 joueurs
								$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'debuff', '".$joueur['nom']."', '".$cible['nom']."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
								$db->query($requete);
								$requete = "INSERT INTO journal VALUES('', ".$cible['ID'].", 'rdebuff', '".$cible['nom']."', '".$joueur['nom']."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
								$db->query($requete);
							}
							else
							{
								echo 'Il bénéficit d\'un debuff plus puissant<br />';
							}
							//Suppression de MP pour orage magnétique
							if($row['type'] == 'orage_magnetique')
							{
								if(array_key_exists('orage_magnetique', $cible['debuff']))
								{
									echo $cible['nom'].' Est déjà sous cet effet<br />';
								}
								else
								{
									//Réduction des mp de la cible
									$cible['mp'] -= $cible['mp_max'] * $row['effet'] / 100;
									if($cible['mp'] < 0) $cible['mp'] = 0;
									$requete = "UPDATE perso SET mp = ".$cible['mp']." WHERE ID = ".$cible['ID'];
									$db->query($requete);
								}
							}
						}
						else
						{
							echo $cible['nom'].' resiste a votre sort !<br />';
						}
					}
				}
				$joueur['pa'] = $joueur['pa'] - $sortpa;
				$joueur['mp'] = $joueur['mp'] - $sortmp;
				//Augmentation des compétences
				$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
				$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
				if ($augmentation[1] == 1)
				{
					$joueur['incantation'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
				}
				$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
				$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
				if ($augmentation[1] == 1)
				{
					$joueur[$row['comp_assoc']] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
				}
				//Mis à jour du joueur
				$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', ".$row['comp_assoc']." = '".$joueur[$row['comp_assoc']]."' WHERE ID = '".$_SESSION['ID']."'";
				$req = $db->query($requete);
			break;
			case "guerison" :
				$check = false;
				//-- Suppression d'un debuff au hasard
				foreach($cibles as $cible)
				{
					$cible_s = recupperso($cible);
					$debuff_tab = array();
					foreach($cible_s["debuff"] as $debuff)
					{
						if($debuff["type"] != "debuff_rez" AND $debuff["type"] != "repos_sage" AND $debuff["type"] != "repos_interieur")
						{
							$debuff_tab[] = $debuff["id"];
						};
					}
					if(count($debuff_tab) > 0)
					{
						$requete = "DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";";
						$db->query($requete);
						$check = true;
					}
					else
					{
						echo "Impossible de lancer de lancer le sort. ".addslashes($cible_s['nom'])." n&apos;a aucun debuff.<br/>";
					};
				}
				if($check)
				{
					$joueur["pa"] = $joueur["pa"] - $sortpa;
					$joueur["mp"] = $joueur["mp"] - $sortmp;
					//-- Mis à jour du joueur
					$db->query("UPDATE perso SET mp='".$joueur["mp"]."', pa='".$joueur["pa"]."' WHERE ID = ".$joueur["ID"].";");
					//-- Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
				}

				echo "<a href=\"" onclick="return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utilisez a nouveau cette compétence</a>";	
			break;
			case "esprit_sacrifie" :	 //-- Esprit Sacrifié
				//-- Suppression d'un debuff au hasard
				if(count($joueur["buff"]) > 0)
				{
					$debuff_tab = array();
					foreach($joueur["debuff"] as $debuff)
					{
						if($debuff["type"] != "debuff_rez" AND $debuff["type"] != "repos_sage" AND $debuff["type"] != "repos_interieur") { $debuff_tab[count($debuff_tab)] = $debuff["id"]; };
					}
					if(count($debuff_tab) > 0)
					{	
						$joueur["pa"] = $joueur["pa"] - $sortpa;
						$joueur["mp"] = $joueur["mp"] - $sortmp;
						
						$buff_tab = array();
						foreach($joueur["buff"] as $buff)
						{
							if($debuff["type"] != "debuff_rez" OR $debuff["type"] != "repos_sage" OR $debuff["type"] != "repos_interieur") { $buff_tab[count($buff_tab)] = $buff["id"]; };
						}
						
						$db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
						$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
						{//-- Augmentation des compétences
							$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
							$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
							if ($augmentation[1] == 1)
							{
								$joueur['incantation'] = $augmentation[0];
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
							}
							$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
							$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
							if ($augmentation[1] == 1)
							{
								$joueur[$row['comp_assoc']] = $augmentation[0];
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
							}
						}
						//-- Mis à jour du joueur
						$db->query("UPDATE perso SET mp='".$joueur["mp"]."', pa='".$joueur["pa"]."' WHERE ID='".$_SESSION["ID"]."';");
					}
					else { echo "Impossible de lancer de lancer le sort. Vous n&apos;a aucun debuff.<br/>"; };
				}
				else { echo "Impossible de lancer de lancer le sort. Vous n&apos;a aucun buff.<br/>"; };
					
				echo "<a href=\"" onclick="return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utilisez a nouveau cette compétence</a>";	
			
			break;
		}
	}
	echo '<br /><a href="sort.php" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
}
else
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris VALUES('', ".sSQL($_GET['id']).", ".$joueur['ID'].")";
				$db->query($requete);
			break;
			case 'delfavoris' :
				$requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur['ID'];
				$db->query($requete);
			break;
		}
	}
	$i = 0;
	$type = '';
	$magies = array('favoris');
	$magie = '';
	$requete = "SELECT * FROM sort_jeu GROUP BY comp_assoc";
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
		echo '<a href="sort.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
	}
	if(array_key_exists('tri', $_GET)) $where = 'AND comp_assoc = \''.$_GET['tri'].'\''; else $_GET['tri'] = 'favoris';
	if($_GET['tri'] == 'favoris')
	{
		$requete = "SELECT * FROM sort_jeu WHERE type != 'rez' AND id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = ".$joueur['ID'].") ORDER BY comp_assoc ASC, type ASC";
	}
	else
	{
		$requete = "SELECT * FROM sort_jeu WHERE type != 'rez' ".$where." ORDER BY comp_assoc ASC, type ASC";
	}
	$req = $db->query($requete);
	$magie = '';
	echo '<table width="97%" class="information_case">';
	while($row = $db->read_array($req))
	{	
		$sortpa = round($row['pa'] * $joueur['facteur_magie']);
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur['buff'])) $sortmp = ceil($sortmp * (1 - ($joueur['buff']['buff_concentration']['effet'] / 100)));
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$type = '';
			echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
		}

		if(in_array($row['id'], $tab_sort_jeu))
		{
			$image = image_sort($row['type']);
			$incanta = $row['incantation'];
			echo '
			<div style="z-index: 3;">
				<tr>';
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			if($row['cible'] == 1 OR $row['cible'] == 8 OR $row['cible'] == 2)
			{
				$href = 'envoiInfo(\'sort.php?ID='.$row['id'].'\', \'information\')';
				$href2 = 'envoiInfo(\'sort.php?ID='.$row['id'].'&amp;groupe=yes\', \'information\')';
				$color = '#444';
				$cursor = 'cursor : pointer;';
			}
			else
			{
				$href = '';
				$href2 = '';
				$cursor = '';
				$color = 'black';
			}

			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>; text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>; return nd();" onmouseover="return <?php echo make_overlib(description($row['description'], $row).'<br/><span class=&quot;xmall&quot;>Incantation : '.$incanta.'</span>'); ?>" onmouseout="return nd();"> <strong><?php echo $row['nom']; ?></strong></span>
			</td>
			<?php
			echo '
			<td>
				<span class="xsmall"> '.$sortpa.' PA 
			</td>
			<td>
				'.$sortmp.' MP
			</td> 
			<td>';
			if($row['cible'] == 2)
			{
				$keys = array_keys($joueur['competences']);
				if(in_array('sort_groupe', $keys) || in_array('sort_groupe_'.$row['comp_assoc'], $keys)) echo ' <span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href2.'">(groupe - '.ceil($sortmp * 1.5).' MP)</span>';
			}
			if($_GET['tri'] == 'favoris') echo ' <td><a href="sort.php?action=delfavoris&amp;id='.$row['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="sort.php?action=favoris&amp;id='.$row['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}

	}
	echo '</table>';
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
