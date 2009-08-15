<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$cible = new perso($_GET['id_joueur']);
$W_distance = detection_distance($cible->get_pos(), $joueur->get_pos());
$tab_sort_jeu = explode(';', $joueur->get_sort_jeu());
?>
<fieldset>
	<legend>Livre de Sorts</legend>
<?php
if (isset($_GET['ID']))
{
	$sort = new sort_jeu($_GET['ID']);
	
	if($W_distance > $sort->get_portee())
	{
		echo 'Vous êtes trop loin pour lancer ce sort !';
	}
	else
	{
		$sortpa_base = $sort->get_pa();
		$sortmp_base = $sort->get_mp();
		$sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
		$sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur->get_buff())) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($joueur->get_pa() < $sortpa)
		{
			echo 'Pas assez de PA';
		}
		elseif($joueur->get_mp() < $sortmp)
		{
			echo 'Pas assez de mana';
		}
		elseif($joueur->get_hp() <= 0)
		{
			echo 'Vous êtes mort';
		}
		else
		{
			switch($row['type'])
			{
				case 'vie' :
					$de_degat_sort = de_soin($joueur[$row['carac_assoc']], $row['effet']);
					$i = 0;
					while($i < count($de_degat_sort))
					{
						if ($i > 0) echo ' + ';
						echo '1D'.$de_degat_sort[$i];
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
					if($cible->get_hp() > 0)
					{
						if($cible->get_hp() < $cible['hp_max'])
						{
							if($soin > (floor($cible['hp_max'] - $cible->get_hp()))) $soin = floor($cible['hp_max']) - $cible->get_hp();
							echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
							$joueur->set_pa($joueur->get_pa() - $sortpa);
							$joueur->set_mp($joueur->get_mp() - $sortmp);
							$cible->set_hp($cible->get_hp() + $soin);
							if($soin > 0)
							{
								$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
								$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
								if ($augmentation[1] == 1)
								{
									$joueur->set_incantation($augmentation[0]);
									echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
								}
								$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_vie', $sortpa_base, $sortmp_base);
								$augmentation = augmentation_competence('sort_vie', $joueur, $difficulte_sort);
								if ($augmentation[1] == 1)
								{
									$joueur['sort_vie'] = $augmentation[0];
									echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['sort_vie'].' en '.$Gtrad['sort_vie'].'</span><br />';
								}
								sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', 'sort_vie'));
								$requete = "UPDATE perso SET hp = '".$cible->get_hp()."' WHERE ID = '".sSQL($_GET['id_joueur'])."'";
								$req = $db->query($requete);
								//Insertion du soin dans les journaux des 2 joueurs
								$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'soin', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), ".$soin.", 0, ".$joueur['x'].", ".$joueur['y'].")";
								$db->query($requete);
								$requete = "INSERT INTO journal VALUES(NULL,  ".sSQL($_GET['id_joueur']).", 'rsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur['x'].", ".$joueur['y'].")";
								$db->query($requete);
							}
						}
						else
						{
							echo 'La cible a toute sa vie<br />';
						}
						echo '<a href="sort_joueur.php?ID='.$_GET['ID'].'&amp;id_joueur='.$_GET['id_joueur'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau ce sort</a>';
					}
					else
					{
						echo 'Le joueur est mort.';
					}
				break;
				case 'debuff_aveuglement' : case 'debuff_enracinement' : case 'debuff_desespoir' : case 'debuff_ralentissement' : case 'lente_agonie' :
					$cible = new perso($_GET['id_joueur']);
					//Test d'esquive du sort
					$protecion = $cible['volonte'] * $cible->get_pm() / 3;
					if(array_key_exists('bulle_sanctuaire', $cible->get_buff())) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
					if(array_key_exists('bulle_dephasante', $cible->get_buff())) $protection *= $cible->get_buff('bulle_dephasante','effet');
					$attaque = rand(0, ($joueur->get_volonte() * $joueur[$row['comp_assoc']]));
					$defense = rand(0, $protection);
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					if ($attaque > $defense)
					{
						$duree = $row['duree'];
						if(array_key_exists('souffrance_extenuante', $joueur->get_buff())) $duree = $duree * $joueur->get_buff('buff_souffrance_extenuante','effet');
						//Mis en place du debuff
						if(lance_buff($row['type'], $_GET['id_joueur'], $row['effet'], $row['effet2'], $duree, $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0))
						{
							echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible->get_nom().'<br />';
							//Insertion du debuff dans les journaux des 2 joueurs
							$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
							$db->query($requete);
							$requete = "INSERT INTO journal VALUES(NULL,  ".sSQL($_GET['id_joueur']).", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
							$db->query($requete);
						}
						else
						{
							echo $cible->get_nom().' bénéficit d\'un debuff plus puissant<br />';
						}
					}
					else
					{
						echo $cible->get_nom().' resiste a votre sort !<br />';
				 	}
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
				break;
				case 'maladie_amorphe' : case 'maladie_degenerescence' : case 'maladie_mollesse' :
					$adversaire = recupperso($_GET['id_joueur']);
					$adversaire['id_joueur'] = $adversaire['ID'];
					if($adversaire['groupe'] != 0)
					{
						$cible_s = recupgroupe($adversaire['groupe'], $joueur ['x'].'-'.$joueur ['y']);
						$cibles = $cible_s['membre'];
					}
					else
					{
						$cibles[] = $adversaire;
					}
					foreach($cibles as $cible)
					{
						if($cible['distance'] <= 7)
						{
							$cible = recupperso($cible['id_joueur']);
							//Test d'esquive du sort
							$protection = $cible['volonte'] * $cible->get_pm() / 3;
							if(array_key_exists('bulle_sanctuaire', $cible->get_buff())) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
							if(array_key_exists('bulle_dephasante', $cible->get_buff())) $protection *= $cible->get_buff('bulle_dephasante','effet');
							$attaque = rand(0, ($joueur->get_volonte() * $joueur[$row['comp_assoc']]));
							$defense = rand(0, $protection);
							if ($attaque > $defense)
							{
								$duree = $row['duree'];
								if(array_key_exists('souffrance_extenuante', $joueur->get_buff())) $duree = $duree * $joueur->get_buff('souffrance_extenuante','effet');
								//Mis en place du debuff
								if(lance_buff($row['type'], $cible->get_id(), $row['effet'], $row['effet2'], $duree, $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0))
								{
									echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible->get_nom().'<br />';
									//Insertion du debuff dans les journaux des 2 joueurs
									$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
									$db->query($requete);
									$requete = "INSERT INTO journal VALUES(NULL,  ".sSQL($_GET['id_joueur']).", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
									$db->query($requete);
								}
								else
								{
									echo $cible->get_nom().' bénéficit d\'un debuff plus puissant<br />';
								}
							}
							else
							{
								echo $cible->get_nom().' resiste a votre sort !<br />';
				 			}
			 			}
			 		}
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
				break;
				case 'rez' :
					$cible = new perso($_GET['id_joueur']);
					//On vérifie que le joueur est bien mort !
					if($cible->get_hp() <= 0)
					{
						//On vérifie si le joueur n'a pas déjà une rez plus efficace d'active
						$requete = "SELECT pourcent FROM rez WHERE id_perso = ".$cible->get_id();
						$req_pourcent = $db->query($requete);
						$pourcent_max = 0;
						while($row_pourcent = $db->read_assoc($req_pourcent))
						{
							if($row_pourcent['pourcent'] > $pourcent_max) $pourcent_max = $row_pourcent['pourcent'];
						}
						if($row['effet'] > $pourcent_max)
						{
							$joueur->set_pa($joueur->get_pa() - $sortpa);
							$joueur->set_mp($joueur->get_mp() - $sortmp);
							//Mis en place de la résurection
							$requete = "INSERT INTO rez VALUES('', ".$cible->get_id().", ".$joueur->get_id().", '".$joueur->get_nom()."', ".$row['effet'].", ".$row['effet2'].", ".$row['duree'].", NOW())";
							$db->query($requete);
							//Augmentation des compétences
							$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
							$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
							if ($augmentation[1] == 1)
							{
								$joueur->set_incantation($augmentation[0]);
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
							}
							$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
							$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
							if ($augmentation[1] == 1)
							{
								$joueur[$row['comp_assoc']] = $augmentation[0];
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
							}
							//Mis à jour du joueur
							sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
							echo 'Résurrection bien lancée.';
						}
						else
						{
							echo 'Le joueur bénéficie d\'une résurrection plus puissante.';
						}
					}
					else
					{
						echo 'Le joueur n\'est pas mort';
					}
				break;
				case 'buff_critique' : case 'buff_evasion' : case 'buff_bouclier' : case 'buff_sacrifice' : case 'buff_inspiration' : case 'buff_force' : case 'buff_armure_glace' : case 'buff_barriere' : case 'buff_bouclier_sacre' : case 'buff_colere' : case 'buff_epine' : case 'buff_meditation' : case 'buff_rage_vampirique' : case 'buff_rapidite' : case 'buff_concentration' : case 'buff_furie_magique' : case 'buff_surpuissance' : case 'bouclier_feu' : case 'bouclier_terre' : case 'bouclier_eau' : case 'bulle_sanctuaire' :
					//Mis en place du buff
					$cible = new perso($_GET['id_joueur']);
					if(lance_buff($row['type'], $_GET['id_joueur'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible->get_buff()), $cible['rang_grade']))
					{
						$joueur->set_pa($joueur->get_pa() - $sortpa);
						$joueur->set_mp($joueur->get_mp() - $sortmp);
						echo $cible->get_nom().' a bien reçu le buff<br />';
						//Augmentation des compétences
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
						$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur->set_incantation($augmentation[0]);
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
						}
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
						$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur[$row['comp_assoc']] = $augmentation[0];
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
						}
						//Mis à jour du joueur
						sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
						//Insertion du buff dans les journaux des 2 joueurs
						$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'buff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
						$db->query($requete);
						$requete = "INSERT INTO journal VALUES(NULL,  ".sSQL($_GET['id_joueur']).", 'rbuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur['x'].", ".$joueur['y'].")";
						$db->query($requete);
					}
					else
					{
						if($G_erreur == 'puissant') echo $cible->get_nom().' bénéficie d\'un buff plus puissant<br />';
						else echo $cible->get_nom().' a trop de buffs.<br />';
					}
				break;
				case "guerison" :	{ //-- Guérison
										$cible = recupperso($_GET["id_joueur"]);
										//-- Suppression d'un debuff au hasard
										$debuff_tab = array();
										foreach($cible["debuff"] as $debuff)
										{
											if($debuff["supprimable"] == 1) { $debuff_tab[count($debuff_tab)] = $debuff["id"]; };
										}
										if(count($debuff_tab) > 0)
										{
											$joueur->set_pa($joueur->get_pa() - $sortpa);
											$joueur->set_mp($joueur->get_mp() - $sortmp);
										
											$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
											{//-- Augmentation des compétences
												$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
												$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
												if ($augmentation[1] == 1)
												{
													$joueur->set_incantation($augmentation[0]);
													echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
												}
												$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
												$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
												if ($augmentation[1] == 1)
												{
													$joueur[$row['comp_assoc']] = $augmentation[0];
													echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
												}
											}
											//-- Mis à jour du joueur
											$db->query("UPDATE perso SET mp='".$joueur->get_mp()."', pa='".$joueur->get_pa()."' WHERE ID = ".$joueur->get_id().";");
										}
										else { echo "Impossible de lancer de lancer le sort. Le joueur n&apos;a aucun debuff.<br/>"; };
											
										echo "<a href=\"sort_joueur.php?ID=".$_GET["ID"]."&amp;id_joueur=".$_GET['id_joueur']."\" onclick=\"return envoiInfo(this.href, 'information')\">Utilisez a nouveau cette compétence</a>";	
									}
									break;
			}
		}
	}
	echo '<br /><a href="sort_joueur.php?id_joueur='.$_GET['id_joueur'].'" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
}
else
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris VALUES('', ".sSQL($_GET['id']).", ".$joueur->get_id().")";
				$db->query($requete);
			break;
			case 'delfavoris' :
				$requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur->get_id();
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
		echo '<a href="sort_joueur.php?tri='.$magie.'&amp;id_joueur='.$_GET['id_joueur'].'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
	}
	if(array_key_exists('tri', $_GET)) $where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\''; else $_GET['tri'] = 'favoris';
	if($_GET['tri'] == 'favoris')
	{
		$requete = "SELECT * FROM sort_jeu WHERE id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = ".$joueur->get_id().") AND (cible = 4 OR cible = 2) ORDER BY comp_assoc ASC, type ASC";
	}
	else
	{
		$requete = "SELECT * FROM sort_jeu ".$where." AND (cible = 4 OR cible = 2) ORDER BY comp_assoc ASC, type ASC";
	}
	$req = $db->query($requete);
	$magie = '';
	echo '<table width="97%" class="information_case">';
	while($row = $db->read_array($req))
	{
		$sortpa = round($row['pa'] * $joueur->get_facteur_magie());
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur->get_buff())) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$type = '';
			echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
		}

		if(in_array($row['id'], $tab_sort_jeu))
		{
			$incanta = $row['incantation'];
			$image = image_sort($row['type']);
			?>
			<div style="z-index: 3;">
			<tr>
			<?php
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			if($row['cible'] == 2 OR $row['cible'] == 4)
			{
				$href = 'envoiInfo(\'sort_joueur.php?ID='.$row['id'].'&amp;id_joueur='.$_GET['id_joueur'].'\', \'information\')';
				$color = 'blue';
				$cursor = 'cursor : pointer;';
			}
			else
			{
				$href = '';
				$cursor = '';
				$color = 'black';
			}
			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>" onmousemove="afficheInfo('info_<?php echo $i; ?>', 'block', event, 'centre');" onmouseout="afficheInfo('info_<?php echo $i; ?>', 'none', event );"><?php echo $row['nom']; ?></span>
				<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
				<?php
				echo description($row['description'], $row).'<br /><span class="xmall">Incantation : '.$incanta.'</span>';
				?>
				</div>
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
			?>
			</div>
			<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
			<?php
			echo description($row['description'], $row);
			if($_GET['tri'] == 'favoris') echo ' <td><a href="sort_joueur.php?action=delfavoris&amp;id='.$row['id'].'&amp;id_joueur='.$_GET['id_joueur'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="sort_joueur.php?action=favoris&amp;id='.$row['id'].'&amp;id_joueur='.$_GET['id_joueur'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}
	}
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</fieldset>
