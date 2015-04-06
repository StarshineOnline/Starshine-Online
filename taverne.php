<?php // -*- mode: php; tab-width:2 -*-
/**
* @file taverne.php
* Taverne
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
if( $action == 'infos' )
{
	$service = new taverne($_GET['id']);
	///@todo passer par $G_interf
  new interf_infos_popover($service->get_noms_infos(), $service->get_valeurs_infos());
  exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///@todo à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// @todo logguer triche
$case = new map_case(array('x' => $perso->get_x(), 'y' => $perso->get_y()));
if( !$case->is_ville(true, 'taverne') )
	exit();

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	switch( $_GET['type'] )
	{
	case 'repos':
		$interf_princ->add( $G_interf->creer_taverne($R, $case) );
		exit;
	case 'quetes':
		$interf_princ->add( $G_interf->creer_tbl_quetes($R, 'taverne') );
		exit;
	case 'bar':
		$interf_princ->add( $G_interf->creer_taverne_bar($R) );
		exit;
	case 'jeux':
		$interf_princ->add( $G_interf->creer_taverne_jeux($R) );
		exit;
	}
}

switch($action)
{
case 'achat':
	/// TODO : à vérifier
	$requete = "SELECT * FROM taverne WHERE id = ".sSQL($_GET['id'], SSQL_INTEGER);
	$req_taverne = $db->query($requete);
	$row_taverne = $db->read_array($req_taverne);
	$taxe = ceil($row_taverne['star'] * $R->get_taxe_diplo($perso->get_race()) / 100);
	$cout = $row_taverne['star'] + $taxe;
	if ($perso->get_star() >= $cout)
	{
		if($perso->get_pa() >= $row_taverne['pa'])
		{
			$valid = true;
			$bloque_regen = false;
			if($row_taverne['pute'] == 1)
			{
				$debuff = false;
				$buff = false;
				$honneur_need = $row_taverne['honneur'] + (($row_taverne['honneur_pc'] * $perso->get_honneur()) / 100);
				if($perso->get_honneur() >= $honneur_need)
				{
					$perso->set_honneur($perso->get_honneur() - $honneur_need);
				}
				else $perso->set_honneur(0);
			
        $texte .= pute_effets($perso, $honneur_need);
      }
			if($valid)
			{
				$perso->set_star($perso->get_star() - $cout);
				$perso->set_pa($perso->get_pa() - $row_taverne['pa']);
				if(!$bloque_regen)
				{
					$perso->set_hp($perso->get_hp() + $row_taverne['hp'] + floor($row_taverne['hp_pc'] * $perso->get_hp_maximum() / 100));
					if ($perso->get_hp() > $perso->get_hp_maximum()) $perso->set_hp(floor($perso->get_hp_maximum()));
					$perso->set_mp($perso->get_mp() + $row_taverne['mp'] + floor($row_taverne['mp_pc'] * $perso->get_mp_maximum() / 100));
					if ($perso->get_mp() > $perso->get_mp_maximum()) $perso->set_mp(floor($perso->get_mp_maximum()));
				}
				$perso->sauver();
				//Récupération de la taxe
				if($taxe > 0)
				{
					$R->add_star_taxe($taxe, 'taverne');
					$R->sauver();
				}
				/// @todo séparer le texte & vérifier les dépassement en hauteur
				interf_alerte::enregistre(interf_alerte::msg_succes, 'La taverne vous remercie de votre achat !<br />'.$texte);
				$interf_princ->maj_perso();
				
				if($row_taverne['pa'] == 12 AND $row_taverne['pute'] == 0) // Equivaut à "c'est un repas"
				{
					// Augmentation du compteur de l'achievement
					$achiev = $perso->get_compteur('stars_en_repas');
					$achiev->set_compteur($achiev->get_compteur() + $cout);
					$achiev->sauver();
					
					// Augmentation du compteur de l'achievement
					$achiev = $perso->get_compteur('nbr_repas');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}
			}
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA');
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de Stars');
	break;
case 'boire':
	$bar = $G_interf->creer_taverne_bar($R);
	/// @todo loguer triche
	if( $bar->ivresse && $bar->ivresse->get_effet() >= 100  )
		exit;
	// Augmentation de l'ivresse ?
	if( !comp_sort::test_de(100, $perso->get_constitution()) )
	{
		$duree = 6 * 3600;
		if( $bar->ivresse )
		{
			$bar->ivresse->set_effet( $bar->ivresse->get_effet() + 1 );
			$bar->ivresse->set_fin( time() + $duree );
			$bar->ivresse->set_description('Vous avez '.$bar->ivresse->get_effet().'% de risques de vous tromper d\'action.');
		}
		else
			$bar->ivresse = new buff(0, $perso->get_id(), 'ivresse', 1, 0, $duree, time()+$duree, 'Ivresse', 'Vous avez 1% de risques de vous tromper d\'action.', 1);
		// ($id = 0, $id_perso=0, $type='', $effet=0, $effet2=0, $duree=0, $fin=0, $nom='', $description='', $debuff=0, $supprimable=0)
		$bar->ivresse->sauver();
		$bar->gain_ivresse();
	}
	$perso->add_pa(-1);
	$perso->add_star(-1);
	$perso->sauver();
	$interf_princ->maj_perso();
	// quêtes / rumeurs
	$de = rand(1, 100);
	$ivresse = $bar->ivresse ? $bar->ivresse->get_effet() : 0;
	if( $de <= 5 )
	{ // indice
		/// @todo faire indices
		$bar->conversation();
	}
	else if( $de <= 10 - $ivresse )
	{ // quête
		$quetes = quete::get_quetes_dispos($this->perso, $R, 'bar');
		$n = count($quetes);
		$ok = false;
	 	shuffle($quetes);
	 	for($i=0; $i<$n; $i++)
	 	{
	 		if( !$perso->prend_quete($quetes[$i]->get_id()) )
	 		{
	 			$bar->quete( $quetes[$i] );
	 			$ok = true;
	 			break;
			}
		}
		if( !$ok )
			$bar->conversation();
	}
	else if( $de <= 20 )
		$bar->conversation();
	else
		$bar->rumeur($de);
	$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case, $bar) );
	exit;
case 'mise':
	$G_url->add('jeu', $_GET['jeu']);
	$jeux = $G_interf->creer_taverne_jeux($_GET['jeu'], true);
	$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case, $jeux) );
	exit;
case 'jouer':
	if( array_key_exists('mise', $_GET) )
	{
		$_SESSION['mise'] = $_GET['mise'];
		$perso->add_star( -$_GET['mise'] );
		$_SESSION['score'] = 0;
		$_SESSION['score_adv'] = 0;
		$_SESSION['passe'] = 0;
		$_SESSION['avance'] = 0;
	}
	// calcul de deux valeurs aléatoires suivant une loi gaussienne (centré en 0 et d'écart-type 1)
	$u = rand(0, 1000) / 1000;
	$v = rand(0, 1000) / 1000;
	$x = sqrt(-log($u)) * cos(2*pi()*$v);
	$y = sqrt(-log($u)) * sin(2*pi()*$v);
	$_SESSION['passe']++;
	switch($_GET['jeu'])
	{
	case 'distance':
		$score = score_flechettes( 200 * $x + $perso->get_distance() );
		$score_adv = score_flechettes( 300 * $y + 500 );
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
		if( $_SESSION['score_adv'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise']*2 );
			else if( $_SESSION['score'] == $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise'] );
		}
		$perso->add_pa(-1);
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('distance', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_distance($augmentation[0]);
    $perso->sauver();
		break;
	case 'melee':
		$perso->add_pa(-1);
		$x = sqrt(200 * $x + $perso->get_melee()) / 30;
		$y = sqrt(300 * $y + 500) / 30;
		$_SESSION['score'] += $x - $y;
		if( $_SESSION['score'] > 1 )
			$perso->add_star( $_SESSION['mise']*2 );
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('melee', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_melee($augmentation[0]);
    $perso->sauver();
		break;
	case 'esquive':
		$perso->add_pa(-1);
		$score = (200 * $x + $perso->get_distance()) > 500 ? 1 : 0;
		$score_adv = (300 * $y + 500) > 500 ? 1 : 0;
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('esquive', $perso, 4);
    if ($augmentation[1] == 1)
      $perso->set_esquive($augmentation[0]);
    if( $_SESSION['score'] > $_SESSION['score_adv'] )
			$perso->add_star( $_SESSION['mise']*2 );
    $perso->sauver();
		break;
	case 'dressage':
		$score = note( 200 * $x + $perso->get_distance() );
		$score_adv = note( 300 * $y + 500 );
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
		if( $_SESSION['score_adv'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise']*2 );
			else if( $_SESSION['score'] == $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise'] );
		}
		$perso->add_pa(-1);
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('dressage', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_dressage($augmentation[0]);
    $perso->sauver();
    break;
	case 'incantation':
		$perso->add_pa(-1);
		$x = sqrt(200 * $x + $perso->get_incantation()) / 30;
		$y = sqrt(300 * $y + 500) / 30;
		$_SESSION['score'] += $x - $y;
		if( $_SESSION['score'] > 1 )
			$perso->add_star( $_SESSION['mise']*2 );
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('incantation', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_incantation($augmentation[0]);
    $perso->sauver();
		break;
	case 'sort_element':
		$score = note( 200 * $x + $perso->get_sort_element() );
		$score_adv = note( 300 * $y + 400 );
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
		if( $_SESSION['score_adv'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise']*2 );
			else if( $_SESSION['score'] == $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise'] );
		}
		$perso->add_pa(-1);
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('sort_element', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_sort_element($augmentation[0]);
    $perso->sauver();
    break;
	case 'sort_vie':
		$score = note( 200 * $x + $perso->get_sort_vie() );
		$score_adv = note( 300 * $y + 400 );
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
		if( $_SESSION['score_adv'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise']*2 );
			else if( $_SESSION['score'] == $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise'] );
		}
		$perso->add_pa(-1);
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('sort_vie', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_sort_vie($augmentation[0]);
    $perso->sauver();
    break;
	case 'sort_mort':
		$score = note( 200 * $x + $perso->get_sort_mort() );
		$score_adv = note( 300 * $y + 400 );
		$_SESSION['score'] += $score;
		$_SESSION['score_adv'] += $score_adv;
		if( $_SESSION['score_adv'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise']*2 );
			else if( $_SESSION['score'] == $_SESSION['score_adv'] )
				$perso->add_star( $_SESSION['mise'] );
		}
		$perso->add_pa(-1);
    //Augmentation de l'aptitude
		$augmentation = augmentation_competence('sort_mort', $perso, 7);
    if ($augmentation[1] == 1)
      $perso->set_sort_mort($augmentation[0]);
    $perso->sauver();
    break;
	}
	$perso->sauver();
	$interf_princ->maj_perso();
	$G_url->add('jeu', $_GET['jeu']);
	$jeux = $G_interf->creer_taverne_jeux($_GET['jeu'], false, $score, $score_adv);
	$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case, $jeux) );
	exit;
case 'jeux':
	$jeux = $G_interf->creer_taverne_jeux();
	$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case, $jeux) );
	exit;
}
$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case) );

function score_flechettes($tir)
{
	if( $tir >= 800 )
		return 50;
	else if( $tir >= 550 )
		return 30;
	else if( $tir >= 300 )
		return 25;
	else if( $tir >= 100 )
		return 15;
	else if( $tir >= 10 )
		return 10;
	else
		return 0;
}

function note($jet)
{
	return max(min(ceil($jet / 100), 10), 1);
}


		/*Bien le bonjour ami voyageur !<br />
		//Affichage des quêtes
		if($R->get_nom() != 'Neutre') $return = affiche_quetes('taverne', $perso);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
		}
		*/
	
?>