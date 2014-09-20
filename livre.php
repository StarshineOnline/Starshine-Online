<?php // -*- mode: php; tab-width:2 -*-
/**
* @file livre.php
* Permet l'affichage des sorts, compétences et recettes d'alchimie et de forge.
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$perso = joueur::get_perso();

if(  array_key_exists('type', $_GET) )
	$type = $_GET['type'];
else if( $perso->get_facteur_magie() == 1 )
	$type = 'sort_jeu';
else
	$type = $perso->get_comp_jeu() ? 'comp_jeu' : 'comp_combat';

if(  array_key_exists('categorie', $_GET) )
	$categorie = $_GET['categorie'];
else
{
	switch($type)
	{
	case 'sort_combat':
		// On prend la magie avec le plus grand score
		$magies = array(	'sort_element'=>$perso->get_sort_element(),
											'sort_mort'=>$perso->get_sort_mort(),
											'sort_vie'=>$perso->get_sort_vie() );
		arsort($magies);
		$magies = array_keys($magies);
		$categorie = $magies[0];
		break;
	case 'comp_combat':
		// On prend l'aptitude avec le plus grand score
		$apt = array(	'melee'=>$perso->get_melee(),
									'distance'=>$perso->get_distance(),
									'esquive'=>$perso->get_esquive(),
									'blocage'=>$perso->get_blocage(),
									'dressage'=>$perso->get_dressage() );
		arsort($apt);
		$apt = array_keys($apt);
		$categorie = $apt[0];
		break;
	default:
		$categorie = 'favoris';
	}
}
$id_cible = array_key_exists('cible', $_GET) ? $_GET['cible'] : $perso->get_id();
$type_cible = array_key_exists('type_cible', $_GET) ? $_GET['type_cible'] : 'perso';
///TODO: à améliorer
$auto_cible = false;
if($type_cible == 'perso')
{
	if( $id_cible == $perso->get_id() )
	{
		$auto_cible = true;
		$cible = &$perso;
	}
	else
		$cible = new perso($id_cible);
}
else if($type_cible == 'monstre')
	$cible = entite::factory('monstre', new map_monstre($id_cible));
else
{
	///TODO : à refaire ?
	security_block(URL_MANIPULATION, 'Type de cible inconnu');
}
	
if( array_key_exists('action', $_GET) )
{
	$groupe = false;
	switch( $_GET['action'] )
	{
	case 'onglet':
		$cadre = new interf_princ_ob();
		$cadre->add( $G_interf->creer_livre_sortcomp($type, $perso, $categorie, !$perso->est_mort()) );
		$cadre->code_js('maj_tooltips();');
		exit;
	/// TODO: à améliorer
	case 'favori':
		switch( $type )
		{
		case 'sort_jeu':
	    $requete = "INSERT INTO sort_favoris (id_sort, id_perso) VALUES(".sSQL($_GET['id']).", ".$perso->get_id().")";
	    $db->query($requete);
	    break;
		case 'comp_jeu':
	    $requete = "INSERT INTO comp_favoris (id_comp, id_perso) VALUES(".sSQL($_GET['id']).", ".$perso->get_id().")";
	    $db->query($requete);
	    break;
	  default:
	  	/// TODO: loguer triche
		}
    break;
	case 'suppr_favori':
		switch( $type )
		{
		case 'sort_jeu':
	    $requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$perso->get_id();
	    $db->query($requete);
	    break;
		case 'comp_jeu':
	    $requete = "DELETE FROM comp_favoris WHERE id_comp =  ".sSQL($_GET['id'])." AND id_perso = ".$perso->get_id();
	    $db->query($requete);
	    break;
	  default:
	  	/// TODO: loguer triche
		}
    break;
	case 'lancer_groupe':
		$groupe = true;
	case 'lancer':
		$comp_sort = comp_sort::factory_gen($_GET['type'], $_GET['id']);
		// On vérifie que c'est bien un sort ou une compétence hors combat
		if( $type == 'comp_combat' || $type == 'sort_combat' )
	  {
    	///TODO : à refaire ?
    	security_block(URL_MANIPULATION, 'Lancement hors combat non autorisé');
	  }
		// On vérifie que c'est connu et qu'on a les prérequis (on ne sait jamais)
		///TODO: loguer ça ?
		if( !$comp_sort->est_connu($perso, 'lancer') || ! $comp_sort->verif_prerequis($perso, 'lancer') )
    	break;
		// On vérifie que l'on peut lancer un sort / une compétence
		if($perso->is_buff('petrifie'))
	  {
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes pétrifié, vous ne pouvez pas rien faire.');
			break;
	  }
		if( $type == 'sort_jeu' && $perso->is_buff('bloque_sort'))
	  {
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes sous vunérabilité, vous ne pouvez plus lancer de sorts hors combat.');
			break;
	  }
	  // On vérifie le type de cible
		///TODO: loguer ça ?
		$cible_ok = true;
	  switch( $comp_sort->get_cible() )
	  {
	  case comp_sort::cible_perso:
	  case comp_sort::cible_case:
	  case comp_sort::cible_batiment:
	  case comp_sort::cible_9cases:
	  	$cible_ok = $id_cible == $perso->get_id();
	  	break;
	  case comp_sort::cible_autre:
	  case comp_sort::cible_autregrp:
	  	$cible_ok = $type_cible == 'monstre' || $id_cible != $perso->get_id();
	  	break;
	  case comp_sort::cible_groupe:
	  	$groupe = false;
		}
		if( !$cible_ok )
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cible non valide.');
			break;
		}
		/// On vérifie la portée
		if( $perso->calcule_distance($cible) > $comp_sort->get_portee() )
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes trop loin !');
			break;
		}
		// Vérification que le joueur a le droit aux sorts de groupe	
    if( $groupe && !($perso->is_competence('sort_groupe') || $perso->is_competence('sort_groupe_'.$comp_sort->get_comp_assoc())) )
    {
    	///TODO : à refaire ?
    	security_block(URL_MANIPULATION, 'Sort de groupe non autorisé');
		}
		// Coût en PA & MP
    $cout_pa_base = $cout_pa = $comp_sort->get_pa();
    $cout_mp_base = $cout_mp = $comp_sort->get_mp_final($perso);
    if($groupe)
			$cout_mp = ceil($cout_mp * 1.5);
    if( $perso->is_buff('buff_contagion') )
		{
      if( mb_ereg('^maladie_', $comp_sort->get_type()) )
			{
        $contagion = $perso->get_buff('buff_contagion');
        $dbg = 'réduction de coût par la contagion (depuis '.$cout_pa.'/'.$cout_mp.') -> ';
        $cout_pa -= $contagion->get_effet();
        $cout_mp -= $contagion->get_effet2();
        if($cout_pa < 1) $cout_pa = 1;
        if($cout_mp < 0) $cout_mp = 0;
        interf_debug::enregistre($dbg.$cout_pa.'/'.$cout_mp);
      }
    }
    if($perso->get_pa() < $sortpa)
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Pas assez de PA !');
			break;
		}
    if($perso->get_mp() < $sortmp)
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Pas assez de mana !');
			break;
		}
		/// On vérifie que le personnage n'est pas mort
    if($perso->get_hp() <= 0)
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes mort !');
			break;
		}
		// Interface
		$interf_princ = $G_interf->creer_jeu();
		$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Lancement de '.$comp_sort->get_nom()) );
		interf_alerte::aff_enregistres($cadre);
		interf_debug::aff_enregistres($cadre);
		// On lance
		$lancement = $comp_sort->lance($perso, $cible, $groupe, /*$lanceur_url*/'', $type_cible);
    // On fait le final si le lancement est réussi
    if($lancement)
    {
      $perso->set_pa($perso->get_pa() - $cout_pa);
      $perso->set_mp($perso->get_mp() - $cout_mp);
      // Amélioration des aptitudes si c'est un sort
      if( $type == 'sort_jeu' )
      {
        //Augmentation des compétences
        $difficulte = diff_sort($comp_sort->get_difficulte() * 1.1, $perso, 'incantation', $cout_pa_base, $cout_mp_base);
        $augmentation = augmentation_competence('incantation', $perso, $difficulte);
        if ($augmentation[1] == 1)
          $perso->set_incantation($augmentation[0]);
        $difficulte = diff_sort($comp_sort->get_difficulte() * 1.1, $perso, $comp_sort->get_comp_assoc(), $cout_pa_base, $cout_mp_base);
        $augmentation = augmentation_competence($comp_sort->get_comp_assoc(), $perso, $difficulte);
        if ($augmentation[1] == 1)
          $perso->set_comp($comp_sort->get_comp_assoc(), $augmentation[0]);
      }
      $perso->sauver();
    	$interf_princ->maj_perso();

      // Augmentation du compteur de l'achievement
      /// TODO: à remettre au bon endroit
      /*if($buff)
      {
        $achiev = $perso->get_compteur('buff');
        $achiev->set_compteur($achiev->get_compteur() + 1);
        $achiev->sauver();
      }
      elseif($debuff)
      {
        $achiev = $perso->get_compteur('debuff');
        $achiev->set_compteur($achiev->get_compteur() + 1);
        $achiev->sauver();
      }*/
    }
    $liens = $cadre->add( new interf_bal_cont('div', false, 'btn-group') );
    if( $comp_sort::propose_relance )
    {
    	$url = 'livre.php?type='.$type.'&categorie='.$categorie.'&action=lancer'.($groupe?'_groupe':'').'&cible='.$id_cible.'&type_cible='.$type_cible.'&id='.$comp_sort->get_id();
    	$lien = $liens->add( new interf_lien_cont($url, false, 'btn btn-default') );
    	$lien->add( new interf_bal_smpl('span', '', false, 'icone icone-sorts') );
    	$lien->add( new interf_bal_smpl('span', 'Relancer '.($type == 'sort_jeu' ? 'le sort' : 'la compétence')) );
		}
    $lien = $liens->add( new interf_lien_cont('livre.php?type='.$type.'&categorie='.$categorie.'&cible='.$id_cible.'&type_cible='.$type_cible, false, 'btn btn-default') );
  	$lien->add( new interf_bal_smpl('span', '', false, 'icone icone-retour') );
  	$lien->add( new interf_bal_smpl('span', 'Revenir au livre de sort') );
    if( interf_debug::doit_aff_bouton() )
    {
    	$btn = $liens->add( new interf_bal_cont('button', false, 'btn btn-default') );
    	$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-debug') );
    	$btn->add( new interf_bal_smpl('span', 'Débogage') );
    	$btn->set_attribut('onclick', '$(\'.debug\').toggle();');
		}
		exit;
	}
} // fin action

$interf_princ = $G_interf->creer_jeu();
/// TODO: à améliorer
if( (!array_key_exists('ajax', $_GET) || $action != 'afficher') && $auto_cible )
{
	// Cadre de la partie droite
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Livres') );
	// Onglets
	$tabs = $cadre->add( new interf_onglets('tab_livres', 'cont_livre') );
	// Si le perso a des sort hors combat affichage de l'onglet des sorts hors combat
	if( $perso->get_sort_jeu() )
	{
		$img = new interf_img('image/interface/livres/iconesorthorscombat.png', 'Sorts hors combat');
		$tabs->add_onglet($img, 'livre.php?type=sort_jeu&action=onglet', 'tab_sort_jeu', 'invent', $type == 'sort_jeu');
	}
	// Si le perso a des sort de combat affichage de l'onglet des sorts de combat
	if( $perso->get_sort_combat() )
	{
		$img = new interf_img('image/interface/livres/iconesortcombat.png', 'Sorts de combat');
		$tabs->add_onglet($img, 'livre.php?type=sort_combat&action=onglet', 'tab_sort_combat', 'invent', $type == 'sort_combat');
	}
	// Si le perso a des compétences hors combat affichage de l'onglet des compétences hors combat
	if( $perso->get_comp_jeu() )
	{
		$img = new interf_img('image/interface/livres/iconecompentecehorscombat.png', 'Compétences hors combat');
		$tabs->add_onglet($img, 'livre.php?type=comp_jeu&action=onglet', 'tab_comp_jeu', 'invent', $type == 'comp_jeu');
	}
	// Si le perso a des compétences de combat affichage de l'onglet des compétences de combat
	if( $perso->get_comp_combat() )
	{
		$img = new interf_img('image/interface/livres/iconecompentececombat.png', 'Compétences de combat');
		$tabs->add_onglet($img, 'livre.php?type=comp_combat&action=onglet', 'tab_comp_combat', 'invent', $type == 'comp_combat');
	}
	// Si le perso a des recettes d'alchimie, affichage de l'onglet correspondant
	/// TODO: à améliorer
	$requete = 'SELECT * FROM perso_recette WHERE id_perso = '.$perso->get_id().' LIMIT 0, 1';
	$req = $db->query($requete);
	if( $db->num_rows($req) )
	{
		$img = new interf_img('image/interface/livres/iconealchimie.png', 'Alchimie');
		$tabs->add_onglet($img, 'livre.php?type=alchimie&action=onglet', 'tab_alchimie', 'invent', $type == 'alchimie');
	}
	//Si le perso a des recettes de forge, affichage de l'onglet correspondant
	if( true )
	{
		$img = new interf_img('image/interface/livres/iconeforge.png', 'Forge');
		$tabs->add_onglet($img, 'livre.php?type=forge&action=onglet', 'tab_forge', 'invent', $type == 'forge');
	}
	$onglet = $tabs->get_onglet('tab_'.$type);
	interf_alerte::aff_enregistres($onglet);
	$onglet->add( $G_interf->creer_livre_sortcomp($type, $cible, $categorie, !$perso->est_mort()) );
}
else if( $auto_cible )
{
	$cont = $interf_princ->add( new interf_bal_cont('section', 'tab_'.$type) );
	interf_alerte::aff_enregistres($cont);
	$cont->add( $G_interf->creer_livre_sortcomp($type, $cible, $categorie, !$perso->est_mort()) );
}
else if( $type == 'sort_jeu' )
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Livre de sort') );
	interf_alerte::aff_enregistres($cadre);
	$cadre->add( $G_interf->creer_livre_sortcomp($type, $cible, $categorie, !$perso->est_mort()) );
}

$interf_princ->code_js('maj_tooltips();');

