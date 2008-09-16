<?php // -*- tab-width:	 2 -*-
/**
* Utilise un grimoire
*/
function utilise_grimoire($id_objet, &$joueur) {
  global $db;
  $requete = "select * from grimoire where id = '$id_objet'";
  $req = $db->query($requete);
  $row = $db->read_assoc($req);
	if (isset($row['classe_requis']) && $row['classe_requis'] != '') {
		$classes_autorisees = explode(';', $row['classe_requis']);
		if (!in_array($joueur['classe'], $classes_autorisees)) {
			echo '<h5>Impossible de lire ce grimoire : il n\'est pas destiné à votre classe</h5>';
			return false;
		}
	}
	if (isset($row['comp_jeu']) && $row['comp_jeu'] != '') {
    return apprend_competence('comp_jeu', $row['comp_jeu'], 
															$joueur, null, true);
  }
  elseif (isset($row['comp_combat']) && $row['comp_combat'] != '') {
    return apprend_competence('comp_combat', $row['comp_combat'], 
															$joueur, null, true);
  }
  elseif (isset($row['comp_perso_id']) && $row['comp_perso_id'] != '') {
    if (!isset($joueur['competences'][$row['comp_perso_competence']])) {
			echo '<h5>Impossible d\'entraîner cette compétence : vous ne la connaissez pas</h5>';
			return false;
    }
		$requete2 = "select permet from classe_permet where id_classe = (".
			'select id from classe where nom = \''.$joueur['classe'].'\') and '.
			'competence =\''.$row['comp_perso_competence'].'\'';
		$req2 = $db->query($requete2);
		$row2 = $db->read_assoc($req2);
		if ($row2['permet'] <=
				$joueur['competences'][$row['comp_perso_competence']]) {
			echo '<h5>Impossible d\'entraîner cette compétence : vous en connaissez toutes les arcanes</h5>';
			return false;
		} else {
			$newval = min($row2['permet'], 
										($joueur['competences'][$row['comp_perso_competence']] + 
										 $row['comp_perso_valueadd']));
			$requete = 'update comp_perso set valeur='.$newval.' where id_comp = '.
				$row['comp_perso_id'].' and competence = \''.
				$row['comp_perso_competence'].'\' and id_perso = '.$joueur['ID'];
			$db->query($requete);
			echo '<h6>Compétence entraînée</h6>';
			return true;
    }
  }
  elseif (isset($row['sort_combat']) && $row['sort_combat'] != '') {
    return apprend_sort('sort_combat', $row['sort_combat'], 
															$joueur, null, true);
  }
  elseif (isset($row['sort_jeu']) && $row['sort_jeu'] != '') {
    return apprend_sort('sort_jeu', $row['sort_jeu'], 
															$joueur, null, true);
  }
  else {
    echo '<h5>Grimoire incorrect: aucune compétence définie</h5>';
    return false;
  }
  return false;
}

/**
* Cette fonction permet d'apprendre une compétence, gérant les prérequis, les
* grimoires, les taxes.
* @param ecole l'école/type de compétence à apprendre
* @param id_competence l'id de la compétence à apprendre
* @param joueur le joueur qui apprend
* @param R le royaume à qui on paye la taxe
* @param grimoire true si on apprend depuis un grimoire(gratis), false sinon
* @return true si on a pu apprendre la compétence, false sinon
*/
function apprend_competence($ecole, $id_competence, &$joueur, $R, $grimoire) {
  global $db;
  $requete = "SELECT * FROM ".$ecole." WHERE id = '$id_competence'";
  $req = $db->query($requete);
  $row = $db->read_array($req);
  if ($grimoire) {
    $taxe = 0;
    $cout = 0;
  } else {
    $taxe = ceil($row['prix'] * $R['taxe'] / 100);
    $cout = $row['prix'] + $taxe;
  }
  if ($joueur['star'] >= $cout) {
    if($joueur[$row['carac_assoc']] >= $row['carac_requis']) {
      if($joueur[$row['comp_assoc']] >= $row['comp_requis']) {
	$sort_jeu = explode(';', $joueur[$ecole]);
	if(!in_array($row['id'], $sort_jeu)) {	  
	  if(in_array($row['requis'], $sort_jeu) OR $row['requis'] == ''
			 OR ($grimoire && $row['requis'] == '999')) {
			// cas particulier du grimoire pour les compétences lvl 1 (requis 999)
	    if($sort_jeu[0] == '') $sort_jeu = array();
	    $sort_jeu[] = $row['id'];
	    $joueur[$ecole] = implode(';', $sort_jeu);
	    $joueur['star'] = $joueur['star'] - $cout;
	    $requete = "UPDATE perso SET star = ".$joueur['star'].", ".$ecole.
	      " = '".$joueur[$ecole]."' WHERE ID = ".$_SESSION['ID'];
	    $req = $db->query($requete);
	    //Récupération de la taxe
	    if($taxe > 0) {
	      $requete = 'UPDATE royaume SET star = star + '.$taxe.
		' WHERE ID = '.$R['ID'];
	      $db->query($requete);
	      $requete = 
		"UPDATE argent_royaume SET ecole_combat = ecole_combat + ".
		$taxe." WHERE race = '".$R['race']."'";
	      $db->query($requete);
	    }
	    echo '<h6>Compétence apprise !</h6>';
	    return true;
	  }
	  else {
	    echo '<h5>Vous devez connaitre une autre compétence avant d\'apprendre celle ci</h5>';
	  }
	}
	else {
	  echo '<h5>Vous connaissez déjà cet compétence</h5>';
	}
      }
      else {
	echo '<h5>Vous n\'avez pas assez en '.$Gtrad[$row['comp_assoc']].'</h5>';
      }
    }
    else {
      echo '<h5>Vous n\'avez pas assez en '.$row['carac_assoc'].'</h5>';
    }
  }
  else{
    echo '<h5>Vous n\'avez pas assez de Stars</h5>';
  }
  return false;
}

/**
* Cette fonction permet d'apprendre un sort -- ce qui marche comme une
* compétence -- gérant les prérequis, les grimoires, les taxes.
* @param ecole l'école/type de compétence à apprendre
* @param id_competence l'id de la compétence à apprendre
* @param joueur le joueur qui apprend
* @param R le royaume à qui on paye la taxe
* @param grimoire true si on apprend depuis un grimoire(gratis), false sinon
* @return true si on a pu apprendre la compétence, false sinon
*/
function apprend_sort($ecole, $id_sort, &$joueur, $R, $grimoire) {
  global $db, $Trace;
	$requete = "SELECT * FROM $ecole WHERE id = '$id_sort'";
	$req = $db->query($requete);
	$row = $db->read_array($req);
  if ($grimoire) {
    $taxe = 0;
    $cout = 0;
  } else {
		$taxe = ceil($row['prix'] * $R['taxe'] / 100);
		$cout = $row['prix'] + $taxe;
	}
	if ($joueur['star'] >= $cout) {
		if($joueur['incantation'] >= ($row['incantation'] * $joueur['facteur_magie'])) {
			if($joueur[$row['comp_assoc']] >= round($row['comp_requis'] * $joueur['facteur_magie'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)))) {
				$sort_jeu = explode(';', $joueur[$ecole]);
				if(!in_array($row['id'], $sort_jeu)) {
					$joueur_sorts = explode(';', $joueur[$ecole]);
					if($row['requis'] == '' OR in_array($row['requis'], $joueur_sorts)) {
						if($sort_jeu[0] == '') $sort_jeu = array();
						$sort_jeu[] = $row['id'];
						$joueur[$ecole] = implode(';', $sort_jeu);
						$joueur['star'] = $joueur['star'] - $cout;
						$requete = "UPDATE perso SET star = ".$joueur['star'].", ".$ecole." = '".$joueur[$ecole]."' WHERE ID = ".$_SESSION['ID'];
						$req = $db->query($requete);
						//Récupération de la taxe
						if($taxe > 0) {
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET ecole_magie = ecole_magie + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h6>Sort appris !</h6>';
						return true;
					}
					else {
						echo '<h5>Vous devez connaitre un autre sort pour apprendre celui-ci</h5>';
					}
				}
				else {
					echo '<h5>Vous possédez déjà ce sort</h5>';
				}
			}
			else {
				echo '<h5>Vous n\'avez pas assez en '.traduit($row['comp_assoc']).'</h5>';
			}
		}
		else {
			echo '<h5>Vous n\'avez pas assez en incantation</h5>';
		}
	}
	else {
		echo '<h5>Vous n\'avez pas assez de Stars</h5>';
	}
	return false;
}
?>