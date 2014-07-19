<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php // -*- tab-width:	 2 -*-
/**
* Utilise un grimoire
*/
/*function utilise_grimoire($id_objet, &$joueur, &$interface) {
  global $db;
  $requete = "select * from grimoire where id = '$id_objet'";
  $req = $db->query($requete);
  $row = $db->read_assoc($req);
	if (isset($row['classe_requis']) && $row['classe_requis'] != '') {
		$classes_autorisees = explode(';', $row['classe_requis']);
		if (!in_array($joueur['classe'], $classes_autorisees)) {
      $interface->add_message('Impossible de lire ce grimoire : il n\'est pas destiné à votre classe', false);
			return false;
		}
	}
	if (isset($row['comp_jeu']) && $row['comp_jeu'] != '') {
    return apprend_competence('comp_jeu', $row['comp_jeu'], 
															$joueur, null, true, $interface);
  }
  elseif (isset($row['comp_combat']) && $row['comp_combat'] != '') {
    return apprend_competence('comp_combat', $row['comp_combat'], 
															$joueur, null, true, $interface);
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
		if ($row2['permet'] <= $joueur['competences'][$row['comp_perso_competence']]) {
			$interface->add_message('Impossible d\'entraîner cette compétence : vous en connaissez toutes les arcanes', false);
			return false;
		} else {
			$newval = min($row2['permet'], 
										($joueur['competences'][$row['comp_perso_competence']] + 
										 $row['comp_perso_valueadd']));
			$requete = 'update comp_perso set valeur='.$newval.' where id_comp = '.
				$row['comp_perso_id'].' and competence = \''.
				$row['comp_perso_competence'].'\' and id_perso = '.$joueur->get_id();
			$db->query($requete);
			$interface->add_message('Compétence entraînée');
			return true;
    }
  }
  elseif (isset($row['sort_combat']) && $row['sort_combat'] != '') {
    return apprend_sort('sort_combat', $row['sort_combat'], 
															$joueur, null, true, $interface);
  }
  elseif (isset($row['sort_jeu']) && $row['sort_jeu'] != '') {
    return apprend_sort('sort_jeu', $row['sort_jeu'], 
															$joueur, null, true, $interface);
  }
  else {
    $interface->add_message('Grimoire incorrect: aucune compétence définie', false);
    return false;
  }
  return false;
}*/

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
function apprend_competence($ecole, $id_competence, &$joueur, $R, $grimoire, &$interface=null)
{
	global $db, $Gtrad;
	$requete = "SELECT * FROM ".$ecole." WHERE id = '$id_competence'";
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if ($grimoire)
	{
		$taxe = 0;
		$cout = 0;
	}
    else
    {
		$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
		$cout = $row['prix'] + $taxe;
    }
	if ($joueur->get_star() >= $cout)
	{
		$get = 'get_'.$row['carac_assoc'];
		if($joueur->$get() >= $row['carac_requis'])
		{
			$get = 'get_'.$row['comp_assoc'];
			if($joueur->$get() >= $row['comp_requis'])
			{
				$get = 'get_'.$ecole;
				$sort_jeu = explode(';', $joueur->$get());
				if(!in_array($row['id'], $sort_jeu))
				{
					if(in_array($row['requis'], $sort_jeu) OR $row['requis'] == '' OR ($grimoire && $row['requis'] == '999'))
					{
						// cas particulier du grimoire pour les compétences lvl 1 (requis 999)
						if($sort_jeu[0] == '') $sort_jeu = array();
						$sort_jeu[] = $row['id'];
						$set = 'set_'.$ecole;
						$joueur->$set(implode(';', $sort_jeu));
						$joueur->set_star($joueur->get_star() - $cout);
						$joueur->sauver();
						//Récupération de la taxe
						if($taxe > 0)
						{
							$R->set_star($R->get_star() + $taxe);
							$R->sauver();
							$requete = "UPDATE argent_royaume SET ecole_combat = ecole_combat + ".$taxe." WHERE race = '".$R->get_race()."'";
							$db->query($requete);
						}
            interf_alerte::enregistre(interf_alerte::msg_succes, 'Compétence apprise !');
						return true;
					}
				  else
			      interf_alerte::enregistre(interf_alerte::msg_erreur,'Vous devez connaitre une autre compétence avant d\'apprendre celle ci');
				}
				else
			  	interf_alerte::enregistre(interf_alerte::msg_erreur,'Vous connaissez déjà cet compétence');
      }
      else
        interf_alerte::enregistre(interf_alerte::msg_erreur,'Vous n\'avez pas assez en '.$Gtrad[$row['comp_assoc']]);
    }
    else
      interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez en '.$row['carac_assoc']);
  }
  else
    interf_alerte::enregistre(interf_alerte::msg_erreur,'Vous n\'avez pas assez de Stars');
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
function apprend_sort($ecole, $id_sort, &$joueur, $R, $grimoire, &$interface=null)
{
	global $db, $Trace;
	$requete = "SELECT * FROM ".$ecole." WHERE id = '$id_sort'";
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if ($grimoire)
	{
		$taxe = 0;
		$cout = 0;
	}
    else
    {
		$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
		$cout = $row['prix'] + $taxe;
    }
	if ($joueur->get_star() >= $cout)
	{
		if($joueur->get_incantation() >= ($row['incantation'] * $joueur->get_facteur_magie()))
		{
			$get = 'get_'.$row['comp_assoc'];
			if($joueur->$get() >= round($row['comp_requis'] * $joueur->get_facteur_magie() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10))))
			{
				$get_ecole = 'get_'.$ecole;
				$sort_jeu = explode(';', $joueur->$get_ecole());
				if(!in_array($row['id'], $sort_jeu))
				{
					$joueur_sorts = explode(';', $joueur->$get_ecole());
					if($row['requis'] == '' OR in_array($row['requis'], $joueur_sorts) OR ($grimoire && $row['requis'] == '999'))
					{
						if($joueur_sorts[0] == '') $joueur_sorts = array();
						$joueur_sorts[] = $row['id'];
						$set = 'set_'.$ecole;
						$joueur->$set(implode(';', $joueur_sorts));
						$joueur->set_star($joueur->get_star() - $cout);
						$joueur->sauver();
						//Récupération de la taxe
						if($taxe > 0)
						{
							$R->set_star($R->get_star() + $taxe);
							$R->sauver();
							$requete = "UPDATE argent_royaume SET ecole_magie = ecole_magie + ".$taxe." WHERE race = '".$R->get_race()."'";
							$db->query($requete);
						}
						interf_alerte::enregistre(interf_alerte::msg_succes, 'Sort appris !');
						return true;
					}
					else
            interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous devez connaitre un autre sort pour apprendre celui-ci');
				}
				else
          interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous possédez déjà ce sort');
			}
			else
        interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez en '.traduit($row['comp_assoc']), false);
		}
		else
      $interface->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas assez en incantation');
	}
	else
    $interface->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas assez de Stars');
	return false;
}
?>