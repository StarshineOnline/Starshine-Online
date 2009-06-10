<?php
function recherche_objet($joueur, $id_objet)
{
	global $G_place_inventaire;
	$objet_d = decompose_objet($id_objet);
	$trouver=  false;
	//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
	$i = 0;
	while(($i < $G_place_inventaire) AND !$trouver)
	{
		$objet_i = decompose_objet($joueur['inventaire_slot'][$i]);
		if($objet_i['sans_stack'] == $objet_d['sans_stack'])
		{
			$trouver = true;
		}
		else $i++;
	}
	if($trouver)
	{
		if($objet_i['stack'] > 1) $return[0] = $objet_i['stack'];
		else $return[0] = 1;
		$return[1] = $i;
		return $return;
	}
	else return false;
}

//Recherche le nombre d'objet de ce type dans l'inventaire
function recherche_nb_objet($joueur, $id_objet)
{
	global $G_place_inventaire;
	$objet_d = decompose_objet($id_objet);
	$trouver=  false;
	//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
	$i = 0;
	$nb_objet = 0;
	while($i < $G_place_inventaire)
	{
		$objet_i = decompose_objet($joueur['inventaire_slot'][$i]);
		if($objet_i['sans_stack'] == $objet_d['sans_stack'])
		{
			if($objet_i['stack'] > 1) $nb_objet += $objet_i['stack'];
			else $nb_objet += 1;
		}
		else $i++;
	}
	if($nb_objet > 0)
	{
		return $return;
	}
	else return false;
}

function supprime_objet($joueur, $id_objet, $nombre)
{
	global $db;
	$i = $nombre;
	while($i > 0)
	{
		$objet = recherche_objet($joueur, $id_objet);
		//Vérification si objet "stacké"
		//print_r($objet);
		$stack = explode('x', $joueur['inventaire_slot'][$objet[1]]);
		if($stack[1] > 1) $joueur['inventaire_slot'][$objet[1]] = $stack[0].'x'.($stack[1] - 1);
		else array_splice($joueur['inventaire_slot'], $objet[1], 1);
		$i--;
	}
	$inventaire_slot = serialize($joueur['inventaire_slot']);
	$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
	//echo $requete;
	$req = $db->query($requete);
}

//Renvoi l'objet décomposé sous forme de tableau => stack, slot, enchantement, id, id_objet, sans_stack
function decompose_objet($objet)
{
	if($objet == 'lock')
	{
		return false;
	}
	else
	{
		$objet_dec = array();
		$decomp = explode('x', $objet);
		$objet_dec['sans_stack'] = $decomp[0];
		$objet_dec['stack'] = $decomp[1];
		$decomp = explode('e', $objet_dec['sans_stack']);
		$objet_dec['id'] = $decomp[0];
		$objet_dec['enchantement'] = $decomp[1];
		$decomp = explode('s', $objet_dec['id']);
		$objet_dec['id'] = $decomp[0];
		$objet_dec['slot'] = $decomp[1];
		$objet_dec['identifier'] = true;
		if($objet_dec['id'][0] != 'h')
		{
			$objet_dec['id_objet'] = substr($objet_dec['id'], 1);
			$objet_dec['categorie'] = $objet_dec['id'][0];
		}
		else
		{
			$objet_dec['id_objet'] = substr($objet_dec['id'], 2);
			$objet_dec['categorie'] = $objet_dec['id'][1];
			$objet_dec['identifier'] = false;
		}
		switch($objet_dec['categorie'])
		{
			case 'p' :
				$objet_dec['table_categorie'] = 'armure';
			break;
			case 'a' :
				$objet_dec['table_categorie'] = 'arme';
			break;
			case 'l' :
				$objet_dec['table_categorie'] = 'grimoire';
			break;
			case 'o' :
				$objet_dec['table_categorie'] = 'objet';
			break;
			case 'g' :
				global $db;
				$objet_dec['table_categorie'] = 'gemme';
				$requete = "SELECT * FROM gemme WHERE id = ".$objet_dec['id_objet'];
				$req = $db->query($requete);
				$objet_dec['valeurs'] = $db->read_assoc($req);
			break;
			case 'm' :
				$objet_dec['table_categorie'] = 'accessoire';
			break;
		}
		return $objet_dec;
	}
}

function enchant($gemme_id, $var)
{
	global $db;
	$requete = "SELECT * FROM gemme WHERE id = ".$gemme_id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$enchants = explode(';', $row['enchantement_type']);
	$effets = explode(';', $row['enchantement_effet']);
	$i = 0;
	while($i < count($enchants))
	{
		switch($enchants[$i])
		{
		case 'degat' :
			$var['arme_degat'] += $effets[$i];
			break;
		case 'critique' :
			$var['enchantement'][$enchants[$i]]['effet'] = $effets[$i];
			$var['enchantement'][$enchants[$i]]['type'] = $enchants[$i];
			break;
		case 'hp' :
			$var['hp_max'] += $effets[$i];
			break;
		case 'mp' :
			$var['mp_max'] += $effets[$i];
			break;
		case 'reserve' :
			$var['reserve'] += $effets[$i];
			break;
		case 'pp' :
			$var['PP'] += $effets[$i];
			break;
			/* On ne peut pas le faire comme ca car le matos est pas charge en entier
		case 'pourcent_pp' :
			$var['PP'] = $var['PP'] + ceil($var['PP'] * $effets[$i] / 100);
			break; */
		case 'pm' :
			$var['PM'] += $effets[$i];
			break;
			/* On ne peut pas le faire comme ca car le matos est pas charge en entier
		case 'pourcent_pm' :
			$var['PM'] = $var['PM'] + ceil($var['PM'] * $effets[$i] / 100);
			break; */
		case 'portee' :
			//echo 'plop';
			$var['arme_distance'] += $effets[$i];
			break;
		case 'star' :
			$var['chance_star'] = $effets[$i];
			break;
		case 'esquive' : /* gemmes de compétence: bonus ignoré à la montée */
		case 'melee' : 
		case 'distance' :
    case 'incantation' :
			$var[$enchants[$i]] += $effets[$i];
			if (!isset($var['bonus_ignorables'][$enchants[$i]])) {
				$var['bonus_ignorables'][$enchants[$i]] = 0;
			}
			$var['bonus_ignorables'][$enchants[$i]] += $effets[$i];
			break;
    default: /* gemmes ayant un effect ponctuel */
			if (isset($var['enchantement'][$enchants[$i]])) {
				$var['enchantement'][$enchants[$i]]['gemme_id'] .= ';'.$gemme_id;
				$var['enchantement'][$enchants[$i]]['effet'] += $effets[$i];
			}
			else {
				$var['enchantement'][$enchants[$i]]['gemme_id'] = $gemme_id; // pour la stack d'effets
				$var['enchantement'][$enchants[$i]]['effet'] = $effets[$i]; // pour utilisation classique
			}
		}
		$i++;
	}
	return $var;
}

function enchant_description($gemme_id)
{
	global $db;
	$requete = "SELECT description FROM gemme WHERE id = ".$gemme_id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	return $row['description'];
}

function recompose_objet($objet)
{
	$objet_rec = $objet['id'];
	if(!is_null($objet['enchantement'])) $objet_rec .= 'e'.$objet['enchantement'];
	elseif(!is_null($objet['slot'])) $objet_rec .= 's'.$objet['slot'];
	if(!is_null($objet['stack'])) $objet_rec .= 'x'.$objet['stack'];
	return $objet_rec;
}

function nom_objet($id_objet)
{
	global $db;
	$objet = decompose_objet($id_objet);
	$id_objet = $objet['id'];
	$nom = '';
	switch($objet['categorie'])
	{
		case 'a' :
			$table = 'arme';
		break;
		case 'p' :
			$table = 'armure';
		break;
		case 'o' :
			$table = 'objet';
		break;
		case 'g' :
			$table = 'gemme';
		break;
		case 'm' :
			$table = 'accessoire';
		break;
		case 'l' :
			$table = 'grimoire';
		break;
	}
	$requete = "SELECT nom FROM ".$table." WHERE id = ".$objet['id_objet'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	return $row[0];
}

function description_objet($id_objet)
{
	global $db, $Gtrad;
	$objet = decompose_objet($id_objet);
	$id_objet = $objet['id'];
	$description = '';
	switch($objet['categorie'])
	{
		case 'a' :
			$requete = "SELECT * FROM arme WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> <tr> <td> Nombre de mains </td> <td> '.count(explode(';', $row['mains'])).' </td> </tr> <tr> <td> Dégats </td> <td> '.$row['degat'].' </td> </tr> <tr> <td> Force nécessaire </td> <td> '.$row['forcex'].' </td> </tr> <tr> <td> Portée </td> <td> '.$row['distance_tir'].' </td> </tr> '; if($row['type'] == 'arc') { $description .= ' <tr> <td> Tir à distance </td> <td> '.$row['distance'].' </td> </tr>'; } elseif($row['type'] == 'baton') { $description .= ' <tr> <td> Augmentation<br /> lancement de sorts </td> <td> '.$row['var1'].'% </td> </tr>'; } $description .= ' <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'p' :
			$requete = "SELECT * FROM armure WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$Gtrad[$row['type']].' </td> </tr> <tr> <td> PP </td> <td> '.$row['PP'].' </td> </tr> <tr> <td> PM </td> <td> '.$row['PM'].' </td> </tr> <tr> <td> Force nécessaire </td> <td> '.$row['forcex'].' </td> </tr> <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'm' :
			$requete = "SELECT * FROM accessoire WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$Gtrad[$row['type']].' </td> </tr> <tr> <td> Effet </td> <td> '.description($row['description'], $row).' </td> </tr> <tr> <td> Puissance nécessaire </td> <td> '.$row['puissance'].' </td> </tr> <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'o' :
			$requete = "SELECT * FROM objet WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			if($row['pa'] > 0) $pa = '<tr><td>PA<br /></td><td>'.$row['pa'].'</td></tr>';
			else $pa = '';
			if($row['mp'] > 0) $mp = '<tr><td>MP<br /></td><td>'.$row['mp'].'</td></tr>';
			else $mp = '';
			$description .= '<strong>'.$row['nom'].'</strong><br /><table><tr><td>Type</td><td>'.$Gtrad[$row['type']].'</td></tr><tr><td>Stack</td><td>'.$row['stack'].'</td></tr><tr><td>Description</td></tr><tr><td>'.addslashes(description($row['description'], $row)).'</td></tr><tr><td>Prix HT<br /><span class=\\\'xsmall\\\'>(en magasin)</span></td><td>'.$row['prix'].'</td></tr>'.$pa.$mp.'</table>';
		break;
		case 'r' :
			$requete = "SELECT * FROM objet_royaume WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			$description .= '<strong>'.$row['nom'].'</strong><br /><table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> </table>';
		break;
		case 'h' :
			$description = 'Objet non identifié';
		break;
		case 'g' :
			$requete = "SELECT * FROM gemme WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			$description .= '<strong>'.$row['nom'].'</strong><br /><table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> <tr> <td> Niveau </td> <td> '.$row['niveau'].' </td> </tr> <tr> <td> Description </td> </tr> <tr> <td> '.description($row['description'], $keys).' </td> </tr> </table>';
		break;
	case 'l' :
	  $requete = "SELECT * FROM grimoire WHERE id = ".$objet['id_objet'];
	  $req = $db->query($requete);
	  $row = $db->read_assoc($req);
	  $description = '<strong>'.$row['nom'].
	    '</strong><br />';
	  if (isset($row['comp_jeu'])) {
	    $table = 'comp_jeu';
	    $id_comp = $row['comp_jeu'];
	    $type = "la compétence";
	  }
	  elseif (isset($row['comp_combat'])) {
	    $table = 'comp_combat';
	    $id_comp = $row['comp_combat'];
	    $type = "la compétence";
	  }
	  elseif (isset($row['sort_jeu'])) {
	    $table = 'sort_jeu';
	    $id_comp = $row['sort_jeu'];
	    $type = "le sort";
	  }
	  elseif (isset($row['sort_combat'])) {
	    $table = 'sort_combat';
	    $id_comp = $row['sort_combat'];
	    $type = "le sort";
	  }
	  if (isset($row['comp_perso_competence'])) {
	    $description .= 'Entraîne la compétence '.
	      traduit($row['comp_perso_competence']).
	      ' de '.$row['comp_perso_valueadd'];
	  }
	  else {
	    $requete2 = "SELECT * from $table where id ='$id_comp'";
	    $req2 = $db->query($requete2);
	    $row2 = $db->read_assoc($req2);
	    $description .= 'Apprend '.$type.' '.$row2['nom'].'<br />';
	    if (isset($row2['requis']) && $row2['requis'] != '999'
		&& $row2['requis'] != '') {
	      $rqs = explode(';', $row2['requis']);
	      foreach ($rqs as $rq) {
		$requete3 = "SELECT nom from $table where id ='$rq'";
		$req3 = $db->query($requete3);
		$row3 = $db->read_assoc($req3);
		$description .= '<br />Requiert '.$type.' '.$row3['nom'];
	      }
	    }
	    if ($row2['carac_requis'] > 0) {
		$description .= '<br />Requiert '.traduit($row2['carac_assoc']).
		  ' à '.$row2['carac_requis'];
	    }
	    if ($row2['comp_requis'] > 0) {
	      $requis = $row2['comp_requis'];
	      if ($type == 'le sort') {
		global $joueur;
		global $Trace;
		$requis = round($row2['comp_requis'] * $joueur['facteur_magie'] * 
				(1 - (($Trace[$joueur['race']]['affinite_'.$row2['comp_assoc']] - 5)
				      / 10)));
	      }
	      $description .= '<br />Requiert '.traduit($row2['comp_assoc']).' à '.$requis;
	    }
	    if (isset($row2['incantation']) && $row2['incantation'] != 0) {
	      global $joueur;
	      $description .= '<br />Requiert '.traduit('incantation').
		' à '.($row2['incantation'] * $joueur['facteur_magie']);
	    }
	  }

	  if (isset($row['classe_requis'])) {
	    $description .= '<br />Reservé aux ';
	    $classes = explode(';', $row['classe_requis']);
	    $virgule = false;
	    foreach ($classes as $c) {
	      if ($virgule) $description .= ', ';
	      else $virgule = true;
	      $description .= pluriel($c);
	    }
	  }
	}
	if($objet['enchantement'] != '') $description .= '<br />Enchantement : '.enchant_description($objet['enchantement']);
	return $description;
}

function equip_objet($objet, $joueur)
{
	global $db, $G_erreur;
	$equip = false;
	$conditions = array();
	if($objet_d = decompose_objet($objet))
	{
		//print_r($objet_d);
		$id_objet = $objet_d['id_objet'];
		$categorie = $objet_d['categorie'];
		switch ($categorie)
		{
			//Si c'est une arme
			case 'a' :
				$requete = "SELECT * FROM arme WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				if($row['type'] == 'baton')
				{
					$conditions[0]['attribut']	= 'coef_incantation';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				elseif($row['type'] == 'bouclier')
				{
					$conditions[0]['attribut']	= 'coef_blocage';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				else
				{
					$conditions[0]['attribut']	= 'coef_melee';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				$conditions[1]['attribut']	= 'coef_distance';
				$conditions[1]['valeur']	= $row['forcex'] * $row['distance'];
				$type = explode(';', $row['mains']);
				$type = $type[0];
				$mains = $row['mains'];
			break;
			//Si c'est une protection
			case 'p' :
				$requete = "SELECT * FROM armure WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$conditions[0]['attribut']	= 'force';
				$conditions[0]['valeur']	= $row['forcex'];
				$type = $row['type'];
			break;
			//Si c'est un accessoire
			case 'm' :
				$requete = "SELECT * FROM accessoire WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$conditions[0]['attribut']	= 'puissance';
				$conditions[0]['valeur']	= $row['puissance'];
				$type = 'accessoire';
			break;
		}
		
		//Vérification des conditions
		if (is_array($conditions))
		{
			$i = 0;
			while ($i < count($conditions))
			{
				if ($joueur[$conditions[$i]['attribut']] < $conditions[$i]['valeur'])
				{
					$G_erreur = 'Vous n\'avez pas assez en '.$conditions[$i]['attribut'].'<br />';
					return false;
				}
				$i++;
			}
		}
		
		//Si c'est une dague main gauche, vérifie qu'il a aussi une dague en main droite
		if($type == 'main_gauche' AND $row['type'] == 'dague')
		{
			if($joueur['inventaire']->main_droite === 0)
			{
			}
			else
			{
				$main_droite = decompose_objet($joueur['inventaire']->main_droite);
				$requete = "SELECT * FROM arme WHERE ID = ".$main_droite['id_objet'];
				//Récupération des infos de l'objet
				$req_md = $db->query($requete);
				$row_md = $db->read_array($req_md);
				if($row['type'] == 'dague')
				{
					if($row_md['type'] != 'dague')
					{
						$G_erreur = 'L\'arme équipée en main droite n\'est pas une dague<br />';
						return false;
					}
				}
				elseif(count(explode(';', $row_md['mains'])) > 1)
				{
					$G_erreur = 'Vous devez enlever votre arme à 2 mains pour porter cet objet<br />';
					return false;
				}
			}
		}
		//Vérifie si il a une dague en main gauche et si c'est le cas et que l'arme n'est pas une dague, on désequipe
		if($type == 'main_droite' AND $row['type'] != 'dague')
		{
			if($joueur['inventaire']->main_gauche === 0 OR $joueur['inventaire']->main_gauche == '')
			{
			}
			else
			{
				if($main_gauche = decompose_objet($joueur['inventaire']->main_gauche))
				{
					$requete = "SELECT * FROM arme WHERE ID = ".$main_gauche['id_objet'];
					//Récupération des infos de l'objet
					$req_mg = $db->query($requete);
					$row_mg = $db->read_array($req_mg);
					if($row_mg['type'] == 'dague')
					{
						desequip('main_gauche', $joueur);
						$joueur = recupperso($joueur['ID']);
					}
				}
				else
				{
				}
			}
		}
		
		$desequip = true;
		if($categorie == 'a')
		{
			$mains = explode(';', $mains);
			$type = $mains[0];
			$count = count($mains);
		}
		//Verifie si il a déjà un objet de ce type sur lui
		if ($type != '')
		{
			//Desequipement
			if($categorie == 'a')
			{
				$i = 0;
				while($desequip AND $i < $count)
				{
					if($joueur['inventaire']->$mains[$i] === 'lock' AND $joueur['inventaire']->main_droite !== 0)
					{
						desequip('main_droite', $joueur);
					}
					$joueur = recupperso($joueur['ID']);
					$desequip = desequip($mains[$i], $joueur);
					$joueur = recupperso($joueur['ID']);
					$i++;
				}
			}
			else
			{
				$desequip = desequip($type, $joueur);
				$joueur = recupperso($joueur['ID']);
			}
		}
		
		if($desequip)
		{
			//On équipe
			$joueur['inventaire']->$type = $objet;
			if($categorie == 'a' AND $count == 2) $joueur['inventaire']->main_gauche = 'lock';
			$inventaire = serialize($joueur['inventaire']);
			$inventaire_slot = serialize($joueur['inventaire_slot']);
			$requete = "UPDATE perso SET inventaire = '".$inventaire."', inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
			$req = $db->query($requete);
			return true;
		}
		else
		{
			return false;
		}
	}
	else return false;
}

function desequip($type, $joueur)
{
	global $db, $G_erreur, $G_place_inventaire;
	if($joueur['inventaire']->$type !== 0 AND $joueur['inventaire']->$type != '')
	{
		$trouver = false;
		$i = 0;
		//Recherche un emplacement libre
		while(($i < $G_place_inventaire) AND !$trouver)
		{
			if($joueur['inventaire_slot'][$i] === 0 OR $joueur['inventaire_slot'][$i] == '')
			{
				$trouver = true;
			}
			else $i++;
		}
		//Inventaire plein
		if(!$trouver)
		{
			$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
			return $false;
		}
		else
		{
			//On enlève l'objet de l'emplacement pour le mettre dans l'inventaire
			if($type == 'main_droite')
			{
				if($joueur['inventaire']->main_gauche == 'lock') $joueur['inventaire']->main_gauche = 0;
			}
			if($joueur['inventaire']->$type != 'lock')
			{
				$joueur['inventaire_slot'][$i] = $joueur['inventaire']->$type;
			}
			$joueur['inventaire']->$type = 0;
			$inventaire = serialize($joueur['inventaire']);
			$inventaire_slot = serialize($joueur['inventaire_slot']);
			$requete = "UPDATE perso SET inventaire = '".$inventaire."', inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
			$req = $db->query($requete);
			return true;
		}
	}
	return true;
}

//Récupère les données d'un echange
function recup_echange($id_echange)
{
	global $db;
	$echange = array();
	$echange['objet'] = array();
	$requete = "SELECT * FROM echange WHERE id_echange = ".$id_echange;
	if($req_e = $db->query($requete))
	{
		$echange = $db->read_assoc($req_e);
		$requete = "SELECT * FROM echange_objet WHERE id_echange = ".$id_echange;
		$req_o = $db->query($requete);
		while($row_o = $db->read_assoc($req_o))
		{
			if($row_o['type'] == 'objet')
			{
				$echange['objet'][] = $row_o;
			}
			else
			{
				$echange['star'][$row_o['id_j']] = $row_o;
			}
		}
	}
	return $echange;
}

//Récupère tous les échanges entre 2 joueurs
function recup_echange_perso($joueur, $receveur)
{
	global $db;
	$echanges = array();
	$requete = "SELECT id_echange, statut FROM echange WHERE ((id_j1 = ".$joueur." AND id_j2 = ".$receveur.") OR (id_j1 = ".$receveur." AND id_j2 = ".$joueur.")) AND statut <> 'fini' AND statut <> 'annule'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[] = $row;
	}
	return $echanges;
}

//Récupère tous les échanges d'un perso avec option de tri
function recup_tout_echange_perso($joueur_id, $tri = 'id_echange DESC')
{
	global $db;
	$echanges = array();
	$requete = "SELECT id_echange, statut, id_j1, id_j2 FROM echange WHERE (id_j1 = ".$joueur_id." OR id_j2 = ".$joueur_id.") AND statut <> 'annule' ORDER BY ".$tri;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[] = $row;
	}
	return $echanges;
}

//Récupère tous les échanges d'un perso avec option de tri
function recup_tout_echange_perso_ranger($joueur_id, $tri = 'id_echange DESC')
{
	global $db;
	$echanges = array();
	$requete = "SELECT id_echange, statut, id_j1, id_j2 FROM echange WHERE (id_j1 = ".$joueur_id." OR id_j2 = ".$joueur_id.") AND statut <> 'annule' ORDER BY ".$tri;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[$row['statut']][] = $row;
	}
	return $echanges;
}

//Ajoute un objet a l'échange (star ou objet)
function echange_objet_ajout($id_objet, $type, $id_echange, $id_joueur)
{
	global $db;
	if(verif_echange_joueur($id_echange, $id_joueur, $id_objet, $type))
	{
		$requete = "INSERT INTO echange_objet(id_echange, id_j, type, objet) VALUES (".$id_echange.", ".$id_joueur.", '".$type."', '".$id_objet."')";
		if($db->query($requete)) return true; else return false;
	}
	else return false;
}

//Supprime un objet a l'échange
function echange_objet_suppr($id_objet_echange)
{
	global $db;
	$requete = "DELETE FROM echange_objet WHERE id_echange_objet = ".$id_objet_echange;
	if($db->query($requete)) return true; else return false;
}

function verif_echange_joueur($id_echange, $id_joueur, $id_objet = 0, $type_objet = 0)
{
	$joueur = recupperso($id_joueur);
	$echange = recup_echange($id_echange);
	//Vérification des objets
	if($id_objet !== 0 && $type_objet == 'objet') $echange['objet'][] = array('id_j' => $id_joueur, 'objet' => $id_objet);
	$echange_objets = array();
	$invent_objets = array();
	foreach($echange['objet'] as $objet)
	{
		if($objet['id_j'] == $id_joueur) $echange_objets[$objet['objet']]++;
	}
	if($joueur['inventaire_slot'] != '')
	{
		foreach($joueur['inventaire_slot'] as $invent)
		{
			$invent_d = decompose_objet($invent);
			if($invent_d['stack'] == '') $invent_d['stack'] = 1;
			$invent_objets[$invent_d['sans_stack']] += $invent_d['stack'];
		}
	}
	$check = true;
	foreach($echange_objets as $key => $objet_nb)
	{
		if($invent_objets[$key] < $objet_nb) $check = false;
	}
	//Vérification des stars
	if($type_objet == 'star')
	{
		//Si il a assez de stars	
		if($joueur['star'] >= $id_objet)
		{
			//Si ya déjà des stars, on les suppriment
			if(array_key_exists('star', $echange) && array_key_exists($id_joueur, $echange['star']))
			{
				echange_objet_suppr($echange['star'][$id_joueur]['id_echange_objet']);
			}
		}
		else $check = false;
	}
	elseif(array_key_exists('star', $echange) && array_key_exists($id_joueur, $echange['star']) && $joueur['star'] < intval($echange['star'][$id_joueur]['id_objet'])) $check = false;
	return $check;
}

function verif_echange($id_echange, $id_j1, $id_j2)
{
	if(verif_echange_joueur($id_echange, $id_j1) && verif_echange_joueur($id_echange, $id_j2)) return true;
	else return false;
}

function check_utilisation_objet($joueur, $objet)
{
	global $db;
	$id_objet = $objet['id'];
	//On chope les infos de l'objet
	$requete = "SELECT pa, mp FROM objet WHERE id = ".$objet['id_objet'];
	$req_o = $db->query($requete);
	$row_o = $db->read_assoc($req_o);
	//On vérifie les PA / MP
	if($joueur['pa'] >= $row_o['pa'])
	{
		if($joueur['mp'] >= $row_o['pm'])
		{
			supprime_objet($joueur, $id_objet, 1);
			$id_objet = mb_substr($id_objet, 1);
			return true;
		}
		else echo '<h5>Vous n\'avez pas assez de MP</h5>';
	}
	else echo '<h5>Vous n\'avez pas assez de PA</h5>';
	return false;
}

?>