<?php
//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include('inc/fp.php');
//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$joueur_id = $_SESSION['ID'];
}
$joueur = recupperso($joueur_id);
//Filtre
if(array_key_exists('filtre', $_GET)) $filtre_url = '&amp;filtre='.$_GET['filtre'];
else $filtre_url = '';
$W_case = 1000 * $joueur['y'] + $joueur['x'];
$W_requete = 'SELECT * FROM map WHERE ID ='.$W_case;
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
//Switch des actions
if(!$visu AND isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'desequip' :
			if(desequip($_GET['partie'], $joueur))
			{
			}
			else
			{
				echo $G_erreur;
			}
		break;
		case 'equip' :
			if(equip_objet($joueur['inventaire_slot'][$_GET['key_slot']], $joueur))
			{
				$joueur = recupperso($joueur['ID']);
				//On supprime l'objet de l'inventaire
				array_splice($joueur['inventaire_slot'], $_GET['key_slot'], 1);
				$inventaire_slot = serialize($joueur['inventaire_slot']);
				$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
				$req = $db->query($requete);
			}
			else
			{
				echo $G_erreur;
			}
		break;
		case 'utilise' :
			if($_GET['type'] == 'fort' OR $_GET['type'] == 'tour' OR $_GET['type'] == 'bourg' OR $_GET['type'] == 'mur' OR $_GET['type'] == 'arme_de_siege')
			{
				if ($W_row['type'] != 1)
				{
					//Cherche infos sur l'objet
					$requete = "SELECT *, batiment.id AS batiment_id, batiment.nom AS batiment_nom  FROM objet_royaume RIGHT JOIN batiment ON batiment.id = objet_royaume.id_batiment WHERE objet_royaume.id = ".sSQL($_GET['id_objet']);
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					if($R['diplo'] == 127 OR $_GET['type'] == 'arme_de_siege')
					{
						//On vérifie si ya pas déjà un batiment en construction
						$requete = "SELECT id FROM placement WHERE x = ".$joueur['x']." AND y = ".$joueur['y'];
						$req = $db->query($requete);
						if($db->num_rows <= 0)
						{
							//On vérifie si ya pas déjà un batiment
							$requete = "SELECT id FROM construction WHERE x = ".$joueur['x']." AND y = ".$joueur['y'];
							$req = $db->query($requete);
							if($db->num_rows <= 0)
							{
								//Positionnement de la construction
								$distance = calcul_distance(convert_in_pos($Trace[$joueur['race']]['spawn_x'], $Trace[$joueur['race']]['spawn_y']), ($W_case));
								$time = time() + ($row['temps_construction'] * $distance);
								if($_GET['type'] == 'arme_de_siege')
								{
									$time = time() + $row['temps_construction'];
									$rez = 0;
								}
								else $rez = $row['bonus4'];
								$requete = "INSERT INTO placement (type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez) VALUES('".sSQL($_GET['type'])."', '".$joueur['x']."', '".$joueur['y']."', '".$Trace[$joueur['race']]['numrace']."', ".time().", '".$time."', '".$row['batiment_id']."', '".$row['hp']."', '".$row['batiment_nom']."', '".$rez."')";
								$db->query($requete);
								//On supprime l'objet de l'inventaire
								array_splice($joueur['inventaire_slot'], $_GET['key_slot'], 1);
								$inventaire_slot = serialize($joueur['inventaire_slot']);
								$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								echo $row['nom'].' posé avec succès';
							}
							else
							{
								echo 'Il y a déjà un batiment sur cette case !';
							}
						}
						else
						{
							echo 'Il y a déjà un batiment en construction sur cette case !';
						}
					}
					else
					{
						echo 'Vous ne pouvez poser un fort uniquement sur un territoire qui vous appartient';
					}
				}
				else
				{
					echo 'Vous ne pouvez pas poser de fort sur une ville';
				}
			}
			switch($_GET['type'])
			{
				case 'drapeau' :
					if ($W_row['type'] != 1)
					{
						//Cherche infos sur l'objet
						$requete = "SELECT *, batiment.id AS batiment_id  FROM objet_royaume RIGHT JOIN batiment ON batiment.id = objet_royaume.id_batiment WHERE objet_royaume.id = ".sSQL($_GET['id_objet']);
						$req = $db->query($requete);
						$row = $db->read_assoc($req);

						//Si terrain neutre ou pas a nous ET que c'est pas dans un donjon
						if((($R['diplo'] > 6 && $R['diplo'] != 127) OR $R['nom'] == 'Neutre') AND !is_donjon($joueur['x'], $joueur['y']))
						{
							//On vérifie si ya pas déjà un batiment en construction
							$requete = "SELECT id FROM placement WHERE x = ".$joueur['x']." AND y = ".$joueur['y'];
							$req = $db->query($requete);
							if($db->num_rows <= 0)
							{
								//On vérifie si ya pas déjà un batiment
								$requete = "SELECT id FROM construction WHERE x = ".$joueur['x']." AND y = ".$joueur['y'];
								$req = $db->query($requete);
								if($db->num_rows <= 0)
								{
									//Positionnement du drapeau
									$distance = calcul_distance(convert_in_pos($Trace[$joueur['race']]['spawn_x'], $Trace[$joueur['race']]['spawn_y']), ($W_case));
									$time = time() + ($row['temps_construction'] * $distance);
									$requete = "INSERT INTO placement VALUES('', 'drapeau', '".$joueur['x']."', '".$joueur['y']."', '".$Trace[$joueur['race']]['numrace']."', ".time().", '".$time."', '".$row['batiment_id']."', '".$row['hp']."', 'drapeau', 0)";
									$db->query($requete);
									//On supprime l'objet de l'inventaire
									array_splice($joueur['inventaire_slot'], $_GET['key_slot'], 1);
									$inventaire_slot = serialize($joueur['inventaire_slot']);
									$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
									$req = $db->query($requete);
									echo 'Drapeau posé avec succès';
								}
								else
								{
									echo 'Il y a déjà un batiment sur cette case !';
								}
							}
							else
							{
								echo 'Il y a déjà un batiment en construction sur cette case !';
							}
						}
						else
						{
							echo 'Vous ne pouvez poser un drapeau uniquement sur les royaumes avec lesquels vous êtes en guerre';
						}
					}
					else
					{
						echo 'Vous ne pouvez pas poser de drapeau sur une ville';
					}
				break;
				case 'identification' :
					$fin = false;
					$i = 0;
					$count = count($joueur['inventaire_slot']);
					while(!$fin AND $i < $count)
					{
						//echo $joueur['inventaire_slot'][$i];
						$stack = explode('x', $joueur['inventaire_slot'][$i]);
						$id_objet = $stack[0];
						if(mb_substr($joueur['inventaire_slot'][$i], 0, 1) == 'h')
						{
							$augmentation = augmentation_competence('identification', $joueur, 3);
							if ($augmentation[1] == 1)
							{
								$joueur['identification'] = $augmentation[0];
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['identification'].' en identification</span><br />';
							}
							//echo $id_objet;
							$requete = "SELECT * FROM gemme WHERE id = ".mb_substr($id_objet, 2);
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							$player = rand(0, $joueur['identification']);
							$thing = rand(0, pow(10, $row['niveau']));
							//echo $joueur['identification'].' / '.pow(10, $row['niveau']).' ---- '.$player.' VS '.$thing;
							//Si l'identification réussie
							if($player > $thing)
							{
								//On remplace la gemme par celle identifiée
								$gemme = mb_substr($joueur['inventaire_slot'][$i], 1);
								$joueur['inventaire_slot'][$i] = $gemme;
								echo 'Identification réussie !<br />Votre gemme est une '.$row['nom'];
								mail('masterob1@chello.fr', 'Starshine Test - Identification réussie', $joueur['nom'].' a identifié '.$row['nom']);
							}
							else
							{
								echo 'L\'identification n\'a pas marché...';
							}
							//On supprime l'objet de l'inventaire
							//Vérification si objet "stacké"
							$stack = explode('x', $joueur['inventaire_slot'][$_GET['key_slot']]);
							if($stack[1] > 1) $joueur['inventaire_slot'][$_GET['key_slot']] = $stack[0].'x'.($stack[1] - 1);
							else array_splice($joueur['inventaire_slot'], $_GET['key_slot'], 1);
							$inventaire_slot = serialize($joueur['inventaire_slot']);
							$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."', identification = ".$joueur['identification']." WHERE ID = ".$joueur['ID'];
							$req = $db->query($requete);
							$fin = true;
						}
						$i++;
					}
				break;
				case 'potion_vie' :
					$stack = explode('x', $joueur['inventaire_slot'][$_GET['key_slot']]);
					$id_objet = $stack[0];
					supprime_objet($joueur, $id_objet, 1);
					$id_objet = mb_substr($id_objet, 1);
					$requete = "SELECT effet, nom FROM objet WHERE id = ".$id_objet;
					$req = $db->query($requete);
					$row = $db->read_row($req);
					$joueur['hp'] += $row[0];
					if($joueur['hp'] > floor($joueur['hp_max'])) $joueur['hp'] = floor($joueur['hp_max']);
					echo 'Vous utilisez une '.$row[1].' elle vous redonne '.$row[0].' points de vie<br />';
					?>
					<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
					<?php
					$requete = "UPDATE perso SET hp = ".$joueur['hp']." WHERE ID = ".$joueur['ID'];
					$db->query($requete);
				break;
				case 'parchemin_pa' :
					$stack = explode('x', $joueur['inventaire_slot'][$_GET['key_slot']]);
					$id_objet = $stack[0];
					supprime_objet($joueur, $id_objet, 1);
					$id_objet = mb_substr($id_objet, 1);
					$requete = "SELECT effet, nom FROM objet WHERE id = ".$id_objet;
					$req = $db->query($requete);
					$row = $db->read_row($req);
					$joueur['pa'] += $row[0];
					if($joueur['pa'] > floor($G_PA_max)) $joueur['pa'] = floor($G_PA_max);
					echo 'Vous utilisez un '.$row[1].'<br />';
					?>
					<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
					<?php
					$requete = "UPDATE perso SET pa = ".$joueur['pa']." WHERE ID = ".$joueur['ID'];
					$db->query($requete);
				break;
				case 'parchemin_tp' :
					$stack = explode('x', $joueur['inventaire_slot'][$_GET['key_slot']]);
					$id_objet = $stack[0];
					$id_objet_reel = mb_substr($id_objet, 1);
					$requete = "SELECT effet, nom FROM objet WHERE id = ".$id_objet_reel;
					$req = $db->query($requete);
					$row = $db->read_row($req);
					//Calcul de la distance entre le point où est le joueur et sa ville natale
					$distance = detection_distance($W_case, convert_in_pos($Trace[$joueur['race']]['spawn_x'], $Trace[$joueur['race']]['spawn_y']));
					if($row[0] >= $distance)
					{
						supprime_objet($joueur, $id_objet, 1);
						//Téléportation du joueur
						echo 'Vous utilisez un '.$row[1].'<br />';
						?>
						<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
						<?php
						$requete = "UPDATE perso SET x = ".$Trace[$joueur['race']]['spawn_x'].", y = ".$Trace[$joueur['race']]['spawn_y']." WHERE ID = ".$joueur['ID'];
						$db->query($requete);
					}
					else
					{
						echo 'Vous êtes trop loin de la ville pour utiliser ce parchemin.';
					}
				break;
				case 'objet_quete' :
					$stack = explode('x', $joueur['inventaire_slot'][$_GET['key_slot']]);
					$id_objet = $stack[0];
					$id_objet_reel = mb_substr($id_objet, 1);
					if($id_objet_reel == 27)
					{
						?>
						Journal du mage Demtros - Unité aigle -<br />
						< Le journal est en très mauvais état, certaines pages sont déchirées, ou rendues illisibles par l'humidité et des taches de sang ><br />
						<br />
						12 Dulfandal : Nous approchons enfin de la cité de Myriandre. Je ne peux expliquer le trouble qui me saisi depuis plusieurs jours mais je sais que cela à un rapport avec les ruines de cette ville. Le malaise que je ressens est presque palpable, mais impossible d'en déterminer la cause. J'en ai fais part a Frankriss mais malgré toute la considération de ce dernier, je sens bien qu'ils me pense juste un peu nerveux...<br />
						Je vais prendre deux tours de garde ce soir, de toute façon, je n'arriverais pas à dormir.<br />
						<br />
						13 Dulfandal : Nous voici enfin arrivés, Achen et les autres sont partis en reconnaissance. Je scanne l'espace astral dans l'espoir de déterminer d'où peux provenir la perturbation que je ressens mais en vain.. Je sens malgré tout qu'il y a quelque chose d'anormal.<br />
						<br />
						14 Dulfandal : Achen est mort, Dubs est l'agonie, dans son délire il est quand même parvenu à nous dire ce qui c'est passé, il semblerait qu'il ai été attaqués par des goules surgie de nulle part, Dubs a réussi à s'enfuir mais pas achen. il nous dis que les goules ont disparues d'un coup... frankriss est préoccupé, mais il gère la situation avec son sang froid coutumier. Nous allons former un groupe d'assaut pour aller récupérer Achen, ou ce qu'il en reste... L'unité aigle ne laisse jamais un compagnon derrière elle.<br />
						Je ne sais pas ce qui se passe dans cette ville mais j'ai un mauvais pressentiment.<br />
						15 Dulfandal : le sort était camouflé, c'est pour ça que je l'ai pas perçu, celui qui l'a lancer doit être un mage exceptionnel pour arriver à camoufler une telle portion de terrain... nous nous déplaçons les épées sorties et près au combat, l'ennemi peut surgir de n'importe ou. Je n'arrive pas a démêler la trame du sort, trop puissant pour moi ... ( taches de sang )<br />
						Il faut que j'écrive ( taches de sang ) sachent ce qui se passe ( taches de sang ), un nécromancien ( la suite est déchirée ).<br />
						<?php
					}
				break;
			}
		break;
		case 'vente' :
			$id_objet = $_GET['id_objet'];
			$categorie = mb_substr($id_objet, 0, 1);
			//Rétrocompatibilité avec anciens inventaires
			if($categorie != 'a' AND $categorie != 'p' AND $categorie != 'o' AND $categorie != 'g' AND $categorie != 'm')
			{
				$categorie = 'p';
			}
			else $id_objet = mb_substr($id_objet, 1);
			switch ($categorie)
			{
				//Si c'est une arme
				case 'a' :
					$requete = "SELECT * FROM arme WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$partie = 'main_droite';
				break;
				//Si c'est une protection
				case 'p' :
					$requete = "SELECT * FROM armure WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$partie = $row['type'];
				break;
				case 'o' :
					$requete = "SELECT * FROM objet WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
				break;
				case 'g' :
					$requete = "SELECT * FROM gemme WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$row['prix'] = pow(15, $row['niveau']) * 10;
				break;
				case 'm' :
					$requete = "SELECT * FROM accessoire WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
				break;
			}
			$prix = floor($row['prix'] / $G_taux_vente);
			supprime_objet($joueur, $joueur['inventaire_slot'][$_GET['key_slot']], 1);
			$requete = "UPDATE perso SET star = star + ".$prix." WHERE ID = ".$joueur['ID'];
			$req = $db->query($requete);
		break;
		case 'ventehotel' :
			//On vérifie qu'il a moins de 10 objets en vente actuellement
			$requete = "SELECT COUNT(*) FROM hotel WHERE id_vendeur = ".$joueur['ID'];
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$objet_max = 10;
			$bonus_craft = ceil($joueur['craft'] / 5);
			$objet_max += $bonus_craft;
			if($row[0] >= $objet_max)
			{
				echo 'Vous avez déjà '.$objet_max.' objets ou plus en vente.';
			}
			else
			{
				$objet = decompose_objet($joueur['inventaire_slot'][$_GET['key_slot']]);
				$categorie = $objet['categorie'];
				switch ($categorie)
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'main_droite';
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$row['prix'] = pow(8, ($row['niveau'] + 1)) * 10;
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
				}
				$modif_prix = 1;
				if($objet['slot'] > 0)
				{
					$modif_prix = 1 + ($objet['slot'] / 5);
				}
				if($objet['slot'] == '0')
				{
					$modif_prix = 0.9;
				}
				if($objet['enchantement'] > '0')
				{
					$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
					$req = $db->query($requete);
					$row_e = $db->read_assoc($req);
					$modif_prix = 1 + ($row_e['niveau'] / 2);
				}
				$prix = floor((2 * $row['prix']) * $modif_prix / $G_taux_vente);
				$prixmax = $prix * 10;
				echo '
				<h2>Inventaire</h2>
			<div style="font-size : 0.9em;">
				<form method="get" name="formulaire" action="javascript:envoiInfo(\'inventaire.php\', \'information\');">
					Mettre en vente à l\'hotel des ventes pour <input type="text" name="prix" value="'.$prix.'" onchange="formulaire.comm.value = formulaire.prix.value * '.($R['taxe'] / 100).';" onkeyup="formulaire.comm.value = formulaire.prix.value * '.($R['taxe'] / 100).';" /> Stars<br />
					Taxe : <input type="text" name="comm" value="'.($prix * $R['taxe'] / 100).'" disabled="true" /><br />
					Maximum = '.$prixmax.' stars.<br />
					<input type="hidden" name="action" value="ventehotel2" />
					<input type="button" name="btnSubmit" value="Mettre en vente" onclick="javascript:envoiInfo(\'inventaire.php?action=ventehotel2&amp;key_slot='.$_GET['key_slot'].'&amp;prix=\' + formulaire.prix.value + \'&amp;max='.$prixmax.'&amp;comm=\' + formulaire.comm.value, \'information\');" />
				</form>';
				exit();
			}
		break;
		case 'ventehotel2' :
			$comm = $_GET['comm'];
			if($_GET['prix'] > $_GET['max'])
			{
				echo 'Vous voulez vendre cet objet trop chère, le commissaire priseur n\'en veut pas !<br />';
			}
			else
			{
				if($_GET['prix'] > 0)
				{
					if($joueur['star'] >= $comm)
					{
						$objet = $joueur['inventaire_slot'][$_GET['key_slot']];
						$objet_d = decompose_objet($objet);
						switch ($objet_d['categorie'])
						{
							//Si c'est une arme
							case 'a' :
								$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = 'main_droite';
								$objet_id = $objet;
							break;
							//Si c'est une protection
							case 'p' :
								$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = $row['type'];
								$objet_id = $objet;
							break;
							case 'o' :
								$requete = "SELECT * FROM objet WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet_d['id'];
							break;
							case 'g' :
								$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$row['prix'] = pow(15, $row['niveau']) * 10;
								$objet_id = $objet;
							break;
							case 'm' :
								$requete = "SELECT * FROM accessoire WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet;
							break;
						}
						$prix = $_GET['prix'];
						supprime_objet($joueur, $joueur['inventaire_slot'][$_GET['key_slot']], 1);
						$requete = "UPDATE perso SET star = star - ".$comm." WHERE ID = ".$joueur['ID'];
						$req = $db->query($requete);
						$requete = "INSERT INTO hotel VALUES (NULL, '".$objet_id."', ".$joueur['ID'].", ".sSQL($_GET['prix']).", 1, '".$R['race']."', ".time().")";
						$req = $db->query($requete);
						$requete = 'UPDATE royaume SET star = star + '.$comm.' WHERE ID = '.$R['ID'];
						$db->query($requete);
						$requete = "UPDATE argent_royaume SET hv = hv + ".$comm." WHERE race = '".$R['race']."'";
						$db->query($requete);
						$message_mail = $joueur['nom']." vend ".$objet." pour ".$_GET['prix']." stars. Commission : ".$comm." stars";;
						mail('masterob1@chello.fr', 'Starshine - Dépot HV', $message_mail);
					}
					else
					{
						echo 'Vous n\'avez pas assez de stars pour payer la commission';
					}
				}
				else
				{
					echo 'Pas de prix négatif ou nul !';
				}
			}
		break;
		case 'slot' :
			$craft = $joueur['craft'];
			if($joueur['race'] == 'scavenger') $craft = round($craft * 1.45);
			if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $craft = round($craft * (1 + ($joueur['accessoire']['effet'] / 100)));
			$chance_reussite1 = pourcent_reussite($craft, 10);
			$chance_reussite2 = pourcent_reussite($craft, 30);
			$chance_reussite3 = pourcent_reussite($craft, 100);
			echo 'Quel niveau d\'enchassement voulez vous ?
			<ul>
				<li><a href="javascript:envoiInfo(\'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=1'.$filtre_url.'\', \'information\');">Niveau 1</a> <span class="small">('.$chance_reussite1.'% de chances de réussite)</span></li>
				<li><a href="javascript:envoiInfo(\'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=2'.$filtre_url.'\', \'information\');">Niveau 2</a> <span class="small">('.$chance_reussite2.'% de chances de réussite)</span></li>
				<li><a href="javascript:envoiInfo(\'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=3'.$filtre_url.'\', \'information\');">Niveau 3</a> <span class="small">('.$chance_reussite3.'% de chances de réussite)</span></li>
			</ul>';
		break;
		case 'slot2' :
			if($joueur['pa'] >= 10)
			{
				$objet = decompose_objet($joueur['inventaire_slot'][$_GET['key_slot']]);
				switch($_GET['niveau'])
				{
					case '1' :
						$difficulte = 10;
					break;
					case '2' :
						$difficulte = 30;
					break;
					case '3' :
						$difficulte = 100;
					break;
				}
				$craft = $joueur['craft'];
				if($joueur['race'] == 'scavenger') $craft = round($craft * 1.45);
				if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $craft = round($craft * (1 + ($joueur['accessoire']['effet'] / 100)));
				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
				if($craftd >= $diff)
				{
					//Craft réussi
					echo 'Réussite !<br />';
					$objet['slot'] = $_GET['niveau'];
				}
				else
				{
					//Craft échec
					echo 'Echec... L\'objet ne pourra plus être enchassable<br />';
					$objet['slot'] = 0;
				}
				$augmentation = augmentation_competence('craft', $joueur, 3);
				if ($augmentation[1] == 1)
				{
					$joueur['craft'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['craft'].' en fabrication d\'objets</span><br />';
					$requete = "UPDATE perso SET craft = ".$joueur['craft']." WHERE ID = ".$joueur['ID'];
					$req = $db->query($requete);
				}
				$objet_r = recompose_objet($objet);
				$joueur['inventaire_slot'][$_GET['key_slot']] = $objet_r;
				$inventaire_slot = serialize($joueur['inventaire_slot']);
				$joueur['pa'] -= 10;
				$requete = "UPDATE perso SET pa = ".$joueur['pa'].", inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
				$req = $db->query($requete);
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
		case 'enchasse' :
			$gemme = decompose_objet($joueur['inventaire_slot'][$_GET['key_slot']]);
			$requete = "SELECT * FROM gemme WHERE id = ".$gemme['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			switch($row['type'])
			{
				case 'arme' :
					$type = 'a';
				break;
				case 'armure' :
					$type = 'p';
				break;
				case 'accessoire' :
					$type = 'm';
				break;
			}
			switch($row['niveau'])
			{
				case 1 :
					$difficulte = 10;
				break;
				case 2 :
					$difficulte = 30;
				break;
				case 3 :
					$difficulte = 100;
				break;
			}
			$craft = $joueur['craft'];
			if($joueur['race'] == 'scavenger') $craft = round($craft * 1.45);
			if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $craft = round($craft * (1 + ($joueur['accessoire']['effet'] / 100)));
			echo 'Dans quel objet voulez vous enchasser cette gemme de niveau '.$row['niveau'].' ?
			<ul>';
			//Recherche des objets pour enchassement possible
			$i = 0;
			while($i <= $G_place_inventaire)
			{
				if($joueur['inventaire_slot'][$i] != '')
				{
					$objet_i = decompose_objet($joueur['inventaire_slot'][$i]);
					//echo '<br />'.$joueur['inventaire_slot'][$i].'<br />';
					if($objet_i['identifier'])
					{
						if($objet_i['categorie'] == 'a') $table = 'arme';
						elseif($objet_i['categorie'] == 'p') $table = 'armure';
						elseif($objet_i['categorie'] == 'm') $table = 'accessoire';
						$requete = "SELECT type FROM ".$table." WHERE id = ".$objet_i['id_objet'];
						$req_i = $db->query($requete);
						$row_i = $db->read_row($req_i);
						$check = true;
						$j = 0;
						$parties = explode(';', $row['partie']);
						$count = count($parties);
						while(!$check AND $j < $count)
						{
							if($parties[$j] == $row_i[0]) $check = true;
							//echo $parties[$j].' '.$row_i[0].'<br />';
							$j++;
						}
						if($check AND ($objet_i['categorie'] == $type) AND ($objet_i['slot'] >= $row['niveau']))
						{
							$nom = nom_objet($joueur['inventaire_slot'][$i]);
							$chance_reussite = pourcent_reussite($craft, $difficulte);
							//On peut mettre la gemme
							echo '<li><a href="javascript:envoiInfo(\'inventaire.php?action=enchasse2&amp;key_slot='.$_GET['key_slot'].'&amp;key_slot2='.$i.'&amp;niveau='.$row['niveau'].$filtre_url.'\', \'information\');">'.$nom.' / slot niveau '.$objet_i['slot'].'</a> <span class="xsmall">'.$chance_reussite.'% de chance de réussite</span></li>';
						}
					}
				}
				$i++;
			}
			echo '
			</ul>';
		break;
		case 'enchasse2' :
			if($joueur['pa'] >= 20)
			{
				$craft = $joueur['craft'];
				if($joueur['race'] == 'scavenger') $craft = round($craft * 1.45);
				if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $craft = round($craft * (1 + ($joueur['accessoire']['effet'] / 100)));
				$gemme = decompose_objet($joueur['inventaire_slot'][$_GET['key_slot']]);
				$objet = decompose_objet($joueur['inventaire_slot'][$_GET['key_slot2']]);
				switch($_GET['niveau'])
				{
					case '1' :
						$difficulte = 10;
					break;
					case '2' :
						$difficulte = 30;
					break;
					case '3' :
						$difficulte = 100;
					break;
				}
				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
				$gemme_casse = false;
				if($craftd >= $diff)
				{
					//Craft réussi
					echo 'Réussite !<br />';
					$objet['enchantement'] = $gemme['id_objet'];
					$objet['slot'] = 0;
					$gemme_casse = true;
				}
				else
				{
					//Craft échec
					//66% chance objet plus enchassable, 34% gemme disparait
					$rand = rand(1, 100);
					if($rand <= 34)
					{
						echo 'Echec... la gemme a cassé...<br />';
						$gemme_casse = true;
					}
					else
					{
						echo 'Echec... L\'objet ne pourra plus être enchassable...<br />';
						$objet['slot'] = 0;
					}
				}
				$augmentation = augmentation_competence('craft', $joueur, 3);
				if ($augmentation[1] == 1)
				{
					$joueur['craft'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['craft'].' en fabrication d\'objets</span><br />';
					$requete = "UPDATE perso SET craft = ".$joueur['craft']." WHERE ID = ".$joueur['ID'];
					$req = $db->query($requete);
				}
				$objet_r = recompose_objet($objet);
				$joueur['inventaire_slot'][$_GET['key_slot2']] = $objet_r;
				$inventaire_slot = serialize($joueur['inventaire_slot']);
				$joueur['pa'] -= 20;
				$requete = "UPDATE perso SET pa = ".$joueur['pa'].", inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
				$req = $db->query($requete);
				if($gemme_casse) supprime_objet($joueur, $joueur['inventaire_slot'][$_GET['key_slot']], 1);
				$joueur = recupperso($joueur['ID']);
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
	}
	refresh_perso();
}
$joueur = recupperso($joueur_id);
$tab_loc = array();

$tab_loc[0]['loc'] = 'accessoire';
$tab_loc[0]['type'] = 'accessoire';
$tab_loc[1]['loc'] = 'tete';
$tab_loc[1]['type'] = 'armure';
$tab_loc[2]['loc'] = 'cou';
$tab_loc[2]['type'] = 'armure';

$tab_loc[3]['loc'] = 'main_droite';
$tab_loc[3]['type'] = 'arme';
$tab_loc[4]['loc'] = 'torse';
$tab_loc[4]['type'] = 'armure';
$tab_loc[5]['loc'] = 'main_gauche';
$tab_loc[5]['type'] = 'arme';

$tab_loc[6]['loc'] = 'main';
$tab_loc[6]['type'] = 'armure';
$tab_loc[7]['loc'] = 'ceinture';
$tab_loc[7]['type'] = 'armure';
$tab_loc[8]['loc'] = 'doigt';
$tab_loc[8]['type'] = 'armure';

$tab_loc[9]['loc'] = ' ';
$tab_loc[9]['type'] = 'vide';
$tab_loc[10]['loc'] = 'jambe';
$tab_loc[10]['type'] = 'armure';
$tab_loc[11]['loc'] = 'dos';
$tab_loc[11]['type'] = 'armure';

$tab_loc[12]['loc'] = ' ';
$tab_loc[12]['type'] = 'vide';
$tab_loc[13]['loc'] = 'chaussure';
$tab_loc[13]['type'] = 'armure';
$tab_loc[14]['loc'] = ' ';
$tab_loc[14]['type'] = 'vide';
?>

<h2>Inventaire</h2>



<table cellspacing="3" width="100%" style="background: url('image/666.png') center no-repeat;">

<?php
$color = 2;
$compteur=0;
foreach($tab_loc as $loc)
{
	if (($compteur % 3) == 0)
		{
			echo '<tr style="height : 55px;">';
		}
		if ($loc['type']=='vide')
		{
			echo '<td>';
		}
		else
		{
			echo '<td class="inventaire2">';
		}
		

		
		if($joueur['inventaire']->$loc['loc'] != '')
		{
			$objet = decompose_objet($joueur['inventaire']->$loc['loc']);
			switch($loc['type'])
			{
				case 'arme' :
					if($joueur['inventaire']->$loc['loc'] != 'lock')
					{
						$requete = "SELECT * FROM `arme` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						echo '<img src="image/arme/arme'.$row['id'].'.png" style="float : left;" />'.$Gtrad[$loc['loc']].'<br />'; 
						echo '<strong>'.$row['nom'].'</strong>';
					}
					else
					{
						echo 'Lock';
					}
				break;
				case 'armure' :
					$requete = "SELECT * FROM `armure` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					echo '<img src="image/armure/'.$loc['loc'].'/'.$loc['loc'].$row['id'].'.png" style="float : left;" />'.$Gtrad[$loc['loc']].'<br />'; 
					echo '<strong>'.$row['nom'].'</strong>';
					
				break;
				case 'accessoire' :
					$requete = "SELECT * FROM `accessoire` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					echo '<img src="image/accessoire/accessoire'.$row['id'].'.png" style="float : left;" />Accessoire<br />'; 
					echo '<strong>'.$row['nom'].'</strong>';
				break;
			}
			if($objet['slot'] > 0)
			{
				echo '<br /><span class="xsmall">Slot niveau '.$objet['slot'].'</span>';
			}
			if($objet['slot'] == '0')
			{
				echo '<br /><span class="xsmall">Slot impossible</span>';
			}
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
		}
		else
		{
			echo $Gtrad[$loc['loc']];
		}
		
		
		if($joueur['inventaire']->$loc['loc'] != '' AND $joueur['inventaire']->$loc['loc'] != 'lock')
		{
			switch($loc['type'])
			{
				case 'arme' :
					if($loc['loc'] == 'main_droite')
					{
						echo '<br />Dégats : '.$joueur['arme_droite'];
					}
					else
					{
						if($row['type'] == 'dague')	echo '<br />Dégats : '.$joueur['arme_gauche'];
						else echo '<br />Dégats absorbés : '.$row['degat'];
					}
				break;
				case 'armure' :
					echo '<br />PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
		}

		if(!$visu AND $joueur['inventaire']->$loc['loc'] != '' AND $joueur['inventaire']->$loc['loc'] != 'lock')
		{
		?>
			<br /><a href="javascript:envoiInfo('inventaire.php?action=desequip&amp;partie=<?php echo $loc['loc'].$filtre_url; ?>', 'information');">Déséquiper</a>
		<?php
		}
		
	echo '</td>';
	if ((($compteur + 1) % 3) == 0)
	{ 
		echo '</tr>';
	}
$compteur++;
}
?>
</table>
<?php
if(!$visu)
{
	$check = 'checked="checked"';
	 if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre'];
	 else $filtre = 'utile';
?>
<p>Place restante dans l'inventaire : <?php echo ($G_place_inventaire - count($joueur['inventaire_slot'])) ?> / <?php echo $G_place_inventaire;?></p>
<input type="radio" <?php if($filtre == 'utile') echo $check; ?> onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=utile', 'inventaire_slot')" name="filtre_inventaire_slot" /> Utile - <input type="radio" <?php if($filtre == 'arme') echo $check; ?> onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=arme', 'inventaire_slot')" name="filtre_inventaire_slot" /> Arme - <input type="radio" <?php if($filtre == 'armure') echo $check; ?> onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=armure', 'inventaire_slot')" name="filtre_inventaire_slot" /> Armure - <input type="radio" <?php if($filtre == 'autre') echo $check; ?> onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=autre', 'inventaire_slot')" name="filtre_inventaire_slot" /> Autre 
<div id="inventaire_slot">
	<?php
	require_once('inventaire_slot.php');
	?>
</div>
<?php
}
?>
