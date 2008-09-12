<?php // -*- tab-width:	 2 -*-
/**
* Utilise un grimoire
*/
function utilise_grimoire($id_objet, &$joueur) {
  global $db;
  $requete = "select * from grimoire where id = '$id_objet'";
  $req = $db->query($requete);
  $row = $db->read_assoc($req);
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
    } else {
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
	  // traite le cas particulier du grimoire pour les compétences lvl 1
	  if ($grimoire && $row['requis'] == '999') {
	    $row['requis'] == ''; // Pas de requis
	  }	  
	  if(in_array($row['requis'], $sort_jeu) OR $row['requis'] == '') {
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
?>