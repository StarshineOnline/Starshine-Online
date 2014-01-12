<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
// Infos sur un objet
if( $action == 'infos' )
{
  $interf->creer_infos_objet($_GET['id']);
  exit;
}

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
$joueur = new perso($joueur_id);

switch( $action )
{
case 'princ':
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_equip($joueur, $_GET['page'], !$visu) );
  exit;
case 'sac':
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_sac($joueur, $_GET['slot'], !$visu) );
  exit;
case 'hotel_vente':
  $princ = $interf->creer_vente_hotel($joueur, $_GET['objet']);
  exit;
}

//Filtre
if(array_key_exists('filtre', $_GET))
{
  $filtre = $_GET['filtre'];
  $filtre_url = '&amp;filtre='.$_GET['filtre'];
}
else
{
  $filtre = 'utile';
  $filtre_url = '&amp;filtre=utile';
}
$W_requete = 'SELECT royaume, type, info FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

$princ = $interf->creer_princ_droit('Inventaire du Personnage');
//Switch des actions
if( !$visu && $action )
{
  verif_mort($joueur, 1);
	switch($action)
	{
		case 'desequip' :
			if(!$joueur->desequip($_GET['partie']))
        $princ->add_message($G_erreur, false);
		break;
		case 'equip' :
			if($joueur->equip_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'])))
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
				$joueur->sauver();
			}
			else
				$princ->add_message($G_erreur, false);
		break;
		case 'utilise' :
			switch($_GET['type'])
			{
				case 'drapeau' :
				break;
				case 'identification' :
				break;
			case 'grimoire':
				break;
			default:
				error_log('Utilisation d\'un objet invalide: '.$_GET['type']);
			}
		break;
		//Dépot de l'objet au dépot militaire
		case 'depot' :
		break;
		case 'vente' :
		break;
		case 'ventehotel' :
		break;
		case 'ventehotel2' :
		break;
		case 'slot' :
		break;
		case 'slot2' :
		break;
		case 'enchasse' :
			$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
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
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}

			// Gemme de fabrique : augmente de effet % le craft
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			$opt = new interf_menu('Dans quel objet voulez vous enchâsser cette gemme de niveau '.$row['niveau'].' ?', '', '');
      $princ->add($opt);
			//Recherche des objets pour enchassement possible
			$i = 0;
			while($i <= $G_place_inventaire)
			{
				if($joueur->get_inventaire_slot_partie($i) != '')
				{
					$objet_i = decompose_objet($joueur->get_inventaire_slot_partie($i));
					//echo '<br />'.$joueur->get_inventaire_slot()[$i].'<br />';
					if($objet_i['identifier'] AND $objet_i['categorie'] != 'r')
					{
						if($objet_i['categorie'] == 'a') $table = 'arme';
						elseif($objet_i['categorie'] == 'p') $table = 'armure';
						elseif($objet_i['categorie'] == 'm') $table = 'accessoire';
						elseif($objet_i['categorie'] == 'o') $table = 'objet';
						elseif($objet_i['categorie'] == 'g') $table = 'gemme';
						else {
							print_debug("table introuvable pour $objet_i[categorie]");
							$i++;
							continue;
						}
						$requete = "SELECT type FROM ".$table." WHERE id = ".$objet_i['id_objet'];
						$req_i = $db->query($requete);
						$row_i = $db->read_row($req_i);
						$check = true;
						$j = 0;
						$parties = explode(';', $row['partie']);
						$count = count($parties);
						if (strlen($row['partie']) > 0) $check = false;
						while(!$check AND $j < $count)
						{
							if($parties[$j] == $row_i[0]) $check = true;
							//echo $parties[$j].' '.$row_i[0].'<br />';
							$j++;
						}
						if($check AND ($objet_i['categorie'] == $type) AND ($objet_i['slot'] >= $row['niveau']))
						{
							$nom = nom_objet($joueur->get_inventaire_slot_partie($i));
							$chance_reussite = pourcent_reussite($craft, $difficulte);
							//On peut mettre la gemme
							$elt = new interf_bal_cont('li');
        			$opt->add($elt);
        			$lien = new interf_bal_smpl('a', $nom.' / slot niveau '.$objet_i['slot']);
        			$lien->set_attribut('href', 'inventaire.php?action=enchasse2&amp;key_slot='.$_GET['key_slot'].'&amp;key_slot2='.$i.'&amp;niveau='.$row['niveau'].$filtre_url);
        			$lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
        			$elt->add($lien);
        			$elt->add( new interf_txt(' ') );
        			$elt->add( new interf_bal_smpl('span', $chance_reussite.'% de chance de réussite', false, 'xsmall') );
        			unset($elt, $lien);
						}
					}
				}
				$i++;
			}
			echo '
			</ul>';
		break;
		case 'enchasse2' :
			if($joueur->get_pa() >= 20)
			{
				$craft = $joueur->get_forge();
				if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
				if($joueur->get_accessoire() !== false)
				{
					$accessoire = $joueur->get_accessoire();
					if($accessoire->type == 'fabrication')
						$craft = round($craft * (1 + ($accessoire->effet / 100)));
				}

				$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot2']));
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

				// Gemme de fabrique : augmente de effet % le craft
				if ($joueur->get_enchantement()!== false &&
						$joueur->is_enchantement('forge')) {
					$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
				}

				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				/*echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';*/
				$gemme_casse = false;
				if($craftd >= $diff)
				{
					//Craft réussi
          $princ->add( new interf_txt('Réussite !') );
          $princ->add( new interf_bal_smpl('br') );
					$objet['enchantement'] = $gemme['id_objet'];
					$objet['slot'] = 0;
					$gemme_casse = true;
					
					// Augmentation du compteur de l'achievement
					$achiev = $joueur->get_compteur('objets_slotted');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}
				else
				{
					//Craft échec
					//66% chance objet plus enchassable, 34% gemme disparait
					$rand = rand(1, 100);
					if($rand <= 34)
					{
            $princ->add( new interf_txt('Echec… la gemme a cassé…') );
            $princ->add( new interf_bal_smpl('br') );
						$gemme_casse = true;
					}
					else
					{
            $princ->add( new interf_txt('Echec… L\'objet ne pourra plus être enchassable…') );
            $princ->add( new interf_bal_smpl('br') );
						$objet['slot'] = 0;
					}
				}
				$augmentation = augmentation_competence('forge', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_forge($augmentation[0]);
					$joueur->sauver();
				}
				$objet_r = recompose_objet($objet);
				$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot2']);
				$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
				$joueur->set_pa($joueur->get_pa() - 20);
				$joueur->sauver();
				if($gemme_casse) $joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
			}
			else
			{
        $princ->add_message('Vous n\'avez pas assez de PA.', false);
			}
		break;
	}
	refresh_perso();
}

$perso = new perso($joueur_id);
$invent = $interf->creer_inventaire($perso, 'inventaire.php', $filtre);
$princ->add($invent);
$invent->set_contenu('perso', !$visu);
$invent->affiche_slots();

// Augmentation du compteur de l'achievement
$achiev = $joueur->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
