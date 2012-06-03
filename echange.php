<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
?>
<fieldset>
<legend>Effectuer un échange</legend>
<?php
$joueur = new perso($_SESSION['ID']);
$joueur->restack_objet();
//Si un identifiant d'echange est passé alors on récupère les infos sur cet échange
if(array_key_exists('id_echange', $_GET))
{
	$echange = recup_echange(sSQL($_GET['id_echange'], SSQL_INTEGER));
	$receveur = new perso($echange['id_j2']);
	//Vérification si le joueur fait parti du donneur ou receveur
	if($joueur->get_id() != $echange['id_j1'] AND $joueur->get_id() != $echange['id_j2'])
	{
		?>
		Vous ne faîtes pas parti de cet échange...
		<?php
		exit();
	}
	else
	{
		$j1 = new perso($echange['id_j1']);
		$j2 = new perso($echange['id_j2']);
	}
}
//Sinon c'est le début d'un echange
else
{
	$W_ID = sSQL($_GET['id_joueur'], SSQL_INTEGER);
	$receveur = new perso($W_ID);
	$j1 = new perso($joueur->get_id());
	$j2 = new perso($W_ID);
}



//Si on commence un nouvel échange
if(array_key_exists('nouvel_echange', $_GET))
{
	//On créé l'échange
	$requete = "INSERT INTO echange(id_j1, id_j2, statut, date_debut, date_fin, message_j1, message_j2) VALUES(".$joueur->get_id().", ".$receveur->get_id().", 'creation', ".time().", ".(time() + 100000).", '', '')";
	$db->query($requete);
	$echange = recup_echange($db->last_insert_id());
}

//Si début d'un echange
if(!isset($echange))
{
	$W_ID = sSQL($_GET['id_joueur'], SSQL_INTEGER);
	$receveur = new perso($W_ID);
	echo '<div class="information_case">';
	//On demande au joueurs si il veut faire un échange ou en récupérer un ancien
	$echanges = recup_echange_perso($joueur->get_id(), $receveur->get_id());
	//Il y a déjà eu des échanges
	if(count($echanges) > 0)
	{
		//Listing des échanges
		?>
			<ul>
		<?php
		foreach($echanges as $echange_liste)
		{
			?>
				<li><a href="echange.php?id_echange=<?php echo $echange_liste['id_echange']; ?>" onclick="return envoiInfo(this.href, 'information');">Echange ID : <?php echo $echange_liste['id_echange']; ?> - <?php echo $echange_liste['statut']; ?></a></li>
			<?php
		}
		?>
					</ul>
					<br />
					<a href="echange.php?id_joueur=<?php echo $W_ID; ?>&amp;nouvel_echange=true" onclick="return envoiInfo(this.href, 'information');">Débuter un nouvel échange avec ce joueur.</a>
				</div>

		<?php
	}
	//Sinon on lui demande si il veut en créer un nouveau
	else
	{
		?>
			Vous n'avez actuellement aucun échange en cours avec ce joueur.<br />
			<br />
			<a href="echange.php?id_joueur=<?php echo $W_ID; ?>&amp;nouvel_echange=true" onclick="return envoiInfo(this.href, 'information');">Débuter un nouvel échange avec ce joueur.</a>
		</div>
		<?php
	}

}

//Validation d'étapes
if(array_key_exists('valid_etape', $_GET))
{
	switch($echange['statut'])
	{
		case 'creation' :
			//Ajout des stars dans la bdd
			if(echange_objet_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $echange['id_echange'], $joueur->get_id()))
			{
				$echange = recup_echange($echange['id_echange']);
			}
			//On passe l'échange en mode proposition
			$requete = "UPDATE echange SET statut = 'proposition' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
			if($db->query($requete))
			{
				//On envoi un message au gars
				$titre = $joueur->get_nom().' vous propose un échange';
				$message = mysql_escape_string($joueur->get_nom().' vous propose un échange[br]
				Pour voir ce qu\'il vous propose cliquez ici : [echange:'.$_GET['id_echange'].']');
				$id_groupe = 0;
				$id_dest = 0;
				$id_thread = 0;
				$id_dest = $receveur->get_id();
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe, 0, true);
				echo '<h6>Votre proposition a bien été envoyée</h6>';
				
				unset($echange);
			}
		break;
		case 'proposition' :
			if($j1->get_id() == $joueur->get_id())
				break;
			//Ajout des stars dans la bdd
			if(echange_objet_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $echange['id_echange'], $joueur->get_id()))
			{
				$echange = recup_echange($echange['id_echange']);
			}
			//On passe l'échange en mode finalisation
			$requete = "UPDATE echange SET statut = 'finalisation' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
			if($db->query($requete))
			{
				//On envoi un message au gars
				$titre = $joueur->get_nom().' vous propose un échange';
				$message = mysql_escape_string($joueur->get_nom().' vous propose un échange[br]
				Pour voir ce qu\'il vous propose cliquez ici : [echange:'.$_GET['id_echange'].']');
				$id_groupe = 0;
				$id_dest = 0;
				$id_thread = 0;
				$id_dest = $j1->get_id();
				$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
				$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe, 0, true);
				
				//C'est ok
				echo '<h6>Votre proposition a bien été envoyée</h6>';
				unset($echange);
			}
		break;
		case 'finalisation' :
			//Finalisation de l'échange donc vérifications
			//Les joueurs doivent être a moins de deux cases l'un de l'autre
			$pos1 = convert_in_pos($j1->get_x(), $j1->get_y());
			$pos2 = convert_in_pos($j2->get_x(), $j1->get_y());
			if(detection_distance($pos1, $pos2) > 1)
			{
				echo '<h5>Vous êtes trop loin pour finaliser cet échange</h5>';
			}
			//Vérification que les joueurs ont bien les objets dans leur inventaire
			else
			{
				if(verif_echange(sSQL($_GET['id_echange'], SSQL_INTEGER), $j1->get_id(), $j2->get_id()))
				{
					//On compte les objets de chaque personne
					$i = 0;
					$nb_objet['j1'] = 0;
					$nb_objet['j2'] = 0;
					$count = count($echange['objet']);
					while($i < $count)
					{
						if($j1->get_id() == $echange['objet'][$i]['id_j'])
							$nb_objet['j1']++;
						else
							$nb_objet['j2']++;
						$i++;
					}
					
					$check = true;
					//Vérification qu'ils ont bien assez de place
					if($G_place_inventaire - count($j1->get_inventaire_slot_partie()) < ($nb_objet['j2'] - $nb_objet['j1']))
					{
						$check = false;
						echo '<h5>'.$j1->get_nom().' n\'a pas assez de place dans son inventaire</h5>';
					}
					if($G_place_inventaire - count($j2->get_inventaire_slot_partie()) < ($nb_objet['j1'] - $nb_objet['j2']))
					{
						$check = false;
						echo '<h5>'.$j2->get_nom().' n\'a pas assez de place dans son inventaire</h5>';
					}
					
					if($check)
					{
						//On supprime tous les objets
						$i = 0;
						$count = count($echange['objet']);
						while($i < $count)
						{
							if($j1->get_id() == $echange['objet'][$i]['id_j'])
							{
								$j1->supprime_objet($echange['objet'][$i]['objet'], 1);
							} 
							else
							{
								$j2->supprime_objet($echange['objet'][$i]['objet'], 1);
							}
							$i++;
						}
						//On donne tous les objets
						$i = 0;
						$count = count($echange['objet']);
						while($i < $count)
						{
							if($j1->get_id() == $echange['objet'][$i]['id_j'])
							{
								$j2->prend_objet($echange['objet'][$i]['objet']);
							} 
							else 
							{
								$j1->prend_objet($echange['objet'][$i]['objet']);
							}
							$i++;
						}
						//On échange les stars
						
						$j1->set_star(intval($echange['star'][$j1->get_id()]['objet']));
						$j2->set_star(intval($echange['star'][$j2->get_id()]['objet']));
						$j1star = $j1->get_star() - $j2->get_star();
						$j2star = $j2->get_star() - $j1->get_star();
						$requete = "UPDATE perso SET star = star - ".$j1star." WHERE ID = ".$j1->get_id();
						$db->query($requete);
						$requete = "UPDATE perso SET star = star - ".$j2star." WHERE ID = ".$j2->get_id();
						$db->query($requete);
						//On met a jour le statut de l'échange
						//On passe l'échange en mode fini
						$requete = "UPDATE echange SET statut = 'fini' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
						if($db->query($requete))
						{
							//On envoi un message au gars
							$titre = 'Finalisation de l\'échange avec '.$joueur->get_nom();
							$message = mysql_escape_string($joueur->get_nom().' a finalisé l\'échange');
							$id_groupe = 0;
							$id_dest = 0;
							$id_thread = 0;
							$id_dest = $j2->get_id();
							$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
							$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe, 0, true);		
							
							//C'est ok
							echo '<h6>L\'échange s\'est déroulé avec succès</h6>';
							
							// Augmentation du compteur de l'achievement
							$achiev = $j1->get_compteur('objets_echanges');
							$achiev->set_compteur($achiev->get_compteur() + $nb_objet['j1']);
							$achiev->sauver();
							
							// Augmentation du compteur de l'achievement
							$achiev = $j2->get_compteur('objets_echanges');
							$achiev->set_compteur($achiev->get_compteur() + $nb_objet['j2']);
							$achiev->sauver();
							
							if($nb_objet['j2'] == 0 AND $echange['star'][$j1->get_id()]['objet'] > 0)
							{
								// Augmentation du compteur de l'achievement
								$achiev = $j1->get_compteur('donner_stars');
								$achiev->set_compteur($achiev->get_compteur() + $echange['star'][$j1->get_id()]['objet']);
								$achiev->sauver();
							}
							if($nb_objet['j1'] == 0 AND $echange['star'][$j2->get_id()]['objet'] > 0)
							{
								// Augmentation du compteur de l'achievement
								$achiev = $j2->get_compteur('donner_stars');
								$achiev->set_compteur($achiev->get_compteur() + $echange['star'][$j2->get_id()]['objet']);
								$achiev->sauver();
							}
							
							unset($echange);
						}
					}
					
					
				}
				else
				{
					echo '<h5>Il manque un ou plusieurs objets a un joueur pour finaliser l\'échange</h5>';
				}
				
			}
		break;
	}
}

//Ajout d'un objet a l'échange en cours
if(array_key_exists('ajout_objet', $_GET))
{
	//Ajout de l'objet dans la bdd
	if(echange_objet_ajout($_GET['ajout_objet'], 'objet', $echange['id_echange'], $joueur->get_id()))
	{
		$echange = recup_echange($echange['id_echange']);
	}
}

//Suppression d'un objet a l'échange en cours
if(array_key_exists('suppr_objet', $_GET))
{
	//Ajout de l'objet dans la bdd
	if(echange_objet_suppr(sSQL($_GET['suppr_objet'], SSQL_INTEGER)))
	{
		array_splice($echange['objet'], $_GET['index_objet'], 1);
	}
}

if(isset($echange))
{
?>
<h3>Echange avec <?php echo $receveur->get_nom(); ?> - N° : <?php echo $echange['id_echange']; ?> - <?php echo $echange['statut']; ?></h3>
<div class="information_case">
<?php
	if(($echange['statut'] == 'proposition') OR ($echange['statut'] == 'finalisation') OR $echange['statut'] == 'fini')
	{
		?>
		Proposition de <?php echo $j1->get_nom(); ?> :
		<div>
		Stars : <?php echo $echange['star'][$j1->get_id()]['objet']; ?><br />
		Objets :
		<ul>
			<?php
			if(is_array($echange['objet']))
			{
				$i = 0;
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $j1->get_id())
					{
					?>
					<li><?php if ($echange['objet'][$keys[$i]]['objet'][0] == 'h') { $nom = 'Objet non indentifié';}
								else { $nom = nom_objet($echange['objet'][$keys[$i]]['objet']);}
						echo $nom; ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
	}
	if($echange['statut'] == 'finalisation' OR $echange['statut'] == 'fini')
	{
		?>
		<br />
		Proposition de <?php echo $j2->get_nom(); ?> :
		<div>
		Stars : <?php echo $echange['star'][$j2->get_id()]['objet']; ?><br />
		Objets :
		<ul>
			<?php
			$i = 0;
			if(is_array($echange['objet']))
			{
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $j2->get_id())
					{
					?>
					<li><?php 
					
					if ($echange['objet'][$keys[$i]]['objet'][0] == 'h') { $nom = 'Objet non indentifié';}
								else { $nom = nom_objet($echange['objet'][$keys[$i]]['objet']);}
						echo $nom; ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
		if($echange['id_j1'] == $joueur->get_id() AND $echange['statut'] != 'fini')
		{
		?>
		<input type="button" value="Finir l'échange" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true', 'information');" />
		<?php
		}
	}
	elseif(($echange['statut'] == 'creation' AND $echange['id_j1'] == $joueur->get_id()) OR ($echange['statut'] == 'proposition' AND $echange['id_j2'] == $joueur->get_id()))
	{
		$j1_bonus = recup_bonus($j1->get_id());
		$j2_bonus = recup_bonus($j2->get_id());
		$echange_star = false;
		$echange_objet = false;
		//Si mode création alors on check pour j1 peut donner et j2 peut recevoir
		if($echange['statut'] == 'creation')
		{
			if(array_key_exists(3, $j1_bonus) AND array_key_exists(1, $j2_bonus)) $echange_star = true;
			if(array_key_exists(4, $j1_bonus) AND array_key_exists(2, $j2_bonus)) $echange_objet = true;
		}
		elseif($echange['statut'] == 'proposition')
		{
			if(array_key_exists(3, $j2_bonus) AND array_key_exists(1, $j1_bonus)) $echange_star = true;
			if(array_key_exists(4, $j2_bonus) AND array_key_exists(2, $j1_bonus)) $echange_objet = true;
		}
	?>
Vous proposez :
<div>
	<form method="post" action="envoiInfoPostData('echange.php?direction=echange&amp', 'information', 'message=' + message);">
		Stars : <input type="text" name="star" id="star" value="0" <?php if(!$echange_star) echo 'disabled="true"'; ?> /><br />
		Objets :
		<ul>
			<?php
			if(is_array($echange['objet']))
			{
				$i = 0;
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $joueur->get_id())
					{
					?>
					<li><?php 
					if ($echange['objet'][$keys[$i]]['objet'][0] == 'h') { $nom = 'Objet non indentifié';}
								else { $nom = nom_objet($echange['objet'][$keys[$i]]['objet']);}
					echo $nom; ?> <a href="echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;suppr_objet=<?php echo $echange['objet'][$keys[$i]]['id_echange_objet']; ?>&amp;index_objet=<?php echo $keys[$i]; ?>" onclick="return envoiInfo(this.href, 'information');">X</a></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
			<?php
			if($echange_objet)
			{
				$options = '';
				//On affiche la liste des objets échangeables par ce joueur
				if($joueur->get_inventaire_slot_partie() != '')
				{
					foreach($joueur->get_inventaire_slot_partie() as $invent)
					{
						if($invent !== 0 AND $invent != '')
						{
							$objet_d = decompose_objet($invent);
							//Si ca n'est pas un objet de royaume
							$check = true;
							if($objet_d['categorie'] != 'r' AND $check)
							{
								if (!$objet_d['identifier']) { $nom = 'Objet non indentifié';}
								else { $nom = nom_objet($objet_d['id']);}
								
								$options .= '
							<option value="'.$objet_d['sans_stack'].'">'.$nom.'</option>
								';
							}
						}
					}
				}
				if($options != '')
				{
				?>
				<select name="objet" id="objet">
				<?php echo $options; ?>
				</select>
				<input type="button" value="Ajouter" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;ajout_objet=' + document.getElementById('objet').value, 'information');"><br />
				<?php
				}
			}
			?>
				<br />
				<input type="button" value="Proposer ces éléments" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true&amp;star=' + document.getElementById('star').value, 'information');" />
	</form>
</div>
<?php
	}
	elseif($echange['statut'] == 'creation' AND $echange['id_j2'] == $joueur->get_id())
	{
		echo 'Un échange est en train d\'être créé';
	}
	elseif($echange['statut'] == 'proposition' AND $echange['id_j1'] == $joueur->get_id())
	{
		echo 'Votre proposition est étudiée par le joueur';
	}
}
echo '</div>';
if($echange['statut'] != 'annule' AND isset($echange) AND $echange['statut'] != 'fini')
{
?>
<div class="information_case"><input type="button" onclick="if(confirm('Voulez vous supprimer cet échange ?')) envoiInfo('liste_echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;annule=ok', 'information');" value="Supprimer l'échange" /></div>
<?php
}
?>
</legend>
