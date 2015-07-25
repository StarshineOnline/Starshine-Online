<?php
/**
 * @file interf_quetes.class.php
 * Classes pour l'interface des quetes
 */

class interf_quetes extends interf_onglets
{
	function __construct(&$perso, $type='autre')
	{
		global $G_url;
		parent::__construct('onglets_quetes', 'invent');
		
		// Onglets
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Plaine', $url->get('type', 'plaine'), 'tab_quetes_plaine', 'invent', $type=='plaine');
		$this->add_onglet('Forêt', $url->get('type', 'foret'), 'tab_quetes_foret', 'invent', $type=='foret');
		$this->add_onglet('Désert', $url->get('type', 'desert'), 'tab_quetes_desert', 'invent', $type=='desert');
		$this->add_onglet('Neige', $url->get('type', 'neige'), 'tab_quetes_neige', 'invent', $type=='neige');
		$this->add_onglet('Montagne', $url->get('type', 'montagne'), 'tab_quetes_montagne', 'invent', $type=='montagne');
		$this->add_onglet('Marais / TM', $url->get('type', 'marais'), 'tab_quetes_marais', 'invent', $type=='marais');
		$this->add_onglet('Autres', $url->get('type', 'autre'), 'tab_quetes_autre', 'invent', $type=='autre');
		
		$this->get_onglet('tab_quetes_'.$type)->add( new interf_quetes_terrain($perso, $type) );
	}
}

class interf_quetes_terrain extends interf_accordeon
{
	function __construct(&$perso, $type='autre')
	{
		global $G_url, $db;
		parent::__construct('quetes_'.$type);
		$quetes = quete_perso::create('id_perso', $perso->get_id());
		foreach($quetes as $qp)
		{
			$quete = $qp->get_quete();
			if( $quete->get_terrain() != $type )
				continue;
			$etape = $qp->get_etape();
			$G_url->add('id', $qp->get_id());
			
			$titre = $quete->get_nom().' (niv. '.$etape->get_niveau().' - ';
			$titre .= strtoupper($quete->get_type()[0]).' '.strtoupper($etape->get_collaboration()[0]);
			if( $quete->get_repetable() == 'oui' )
				$titre .= ' R';
			$descr = new interf_descr_quete($quete, $etape, $qp);
			$titre .= ' - '.$descr->nbr_obj_reussi.' / '.$descr->nbr_obj_total.')';
			
			/// @todo passer à l'objet
			$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_min_monstres"');
			$row = $db->read_array($req);
			$niv_min = $row ? $row[0] : '0';
			$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_max_monstres"');
			$row = $db->read_array($req);
			$niv_max = $row ? $row[0] : 255;
			$niv_aff = $niv_min == $niv_max ? $niv_max : false;
			if( $niv_aff == $etape->get_niveau() )
			{
				$filtre = new interf_lien_cont('deplacement.php?action=tous_monstres', false, 'icone icone-oeil-bare');
				$filtre->set_tooltip('Afficher tous les monstres', 'bottom', '#information');
			}
			else
			{
				$filtre = new interf_lien_cont('deplacement.php?action=niveau&niveau='.$etape->get_niveau(), false, 'icone icone-oeil');
				$filtre->set_tooltip('Afficher uniquement les monstres de ce niveau sur la carte', 'bottom', '#information');
			}
			$panneau = $this->nouv_panneau($titre, 'quete_'.$quete->get_id(), false, 'default', $filtre);
			if( $quete->get_nombre_etape() > 1 )
				$panneau->add( new interf_bal_smpl('h4', $etape->get_etape().' / '.$quete->get_nombre_etape().' : '.$etape->get_nom()) );
			$panneau->add( $descr );
			$abandon = $panneau->add( new interf_lien('Abandonner', $G_url->get('action', 'abandonner'), false, 'btn btn-default') );
			$abandon->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir abandonné cette quête ?\');');
			unset($descr);
		}
	}
}

class interf_descr_quete extends interf_cont
{
	public $nbr_obj_total = 0;
	public $nbr_obj_reussi = 0;
	function __construct(&$quete, &$etape, &$quete_perso=null)
	{
		global $db, $Gtrad;
		$liste_descr = $this->add( new interf_bal_cont('ul', false, 'infos_quete') );
		$liste_descr->add( new interf_bal_smpl('li', 'Niveau conseillé : '.$etape->get_niveau()) );
		$liste_descr->add( new interf_bal_smpl('li', 'Type : '.$quete->get_type()) );
		$liste_descr->add( new interf_bal_smpl('li', 'Collaboration : '.$etape->get_collaboration()) );
		$liste_descr->add( new interf_bal_smpl('li', 'Répétable : '.$quete->get_repetable()) );
		$texte = new texte($etape->get_description(), texte::descr_quetes);
		$this->add( new interf_bal_smpl('p', $texte->parse(), false, 'descr_quete') );
		
		// Avancement
		if( $quete_perso )
		{
			$avancement = array();
			$avanc = explode(';', $quete_perso->get_avancement());
			foreach($avanc as $a)
			{
				$a = explode(':', $a);
				$avancement[$a[0]] = $a[1];
				$this->nbr_obj_reussi += $a[1];
			}
		}
		// Objectifs
		$objectifs = explode(';', $etape->get_objectif());
		$liste_obj = new interf_bal_cont('ul');
		foreach($objectifs as $obj)
		{
			$type = mb_substr($obj, 1);
			$valeur = explode(':', $type);
			/// @todo passer à l'objet
			switch($obj[0])
			{
			case 'M':  // tuer des monstres
				$requete = "SELECT nom FROM monstre WHERE id = ". $valeur[0];
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$texte = 'Tuer '.$valeur[1].' '.$row['nom'];
				if($quete_perso)
					$texte .= ' : '.$avancement[$obj[0].$valeur[0]].' / '.$valeur[1];
				$liste_obj->add( new interf_bal_smpl('li', $texte) );
				$this->nbr_obj_total += $valeur[1];
				break;
			case 'P': // parler à un PNJ
				$requete = "SELECT nom FROM pnj WHERE id = ". $valeur[0];
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$liste_obj->add( new interf_bal_smpl('li', 'Parler à '.$row['nom']) );
				break;
			case 'J': // tuer des perso selon la diplomatie
				include_once('../inc/diplo.inc.php');
				$texte = 'Tuer '.$valeur[1].' personnage(s) dont la diplomatie est au moins '.$DIPLO[$valeur[0]];
				if($quete_perso)
					$texte .= ' : '.$avancement[$obj[0].$valeur[0]].' / '.$valeur[1];
				$liste_obj->add( new interf_bal_smpl('li', $texte) );	
				$this->nbr_obj_total += $valeur[1];
				break;
			case 'L': // trouver un objet
				$objet = objet::factory($valeur[0]);
				$texte = 'Trouver '.$valeur[1].' '.$objet->get_nom();
				if($quete_perso)
					$texte .= ' : '.$avancement[$obj[0].$valeur[0]].' / '.$valeur[1];
				$liste_obj->add( new interf_bal_smpl('li', $texte) );
				$this->nbr_obj_total += $valeur[1];
				break;
			case 'O': // rapporter un objet
				$objet = objet::factory($valeur[0]);
				$texte = 'Rapporter '.$valeur[1].' '.$objet->get_nom();
				if($quete_perso)
					$texte .= ' : '.$avancement[$obj[0].$valeur[0]].' / '.$valeur[1];
				$liste_obj->add( new interf_bal_smpl('li', $texte) );
				$this->nbr_obj_total += $valeur[1];
				break;
			case 'X': // texte
				$liste_obj->add( new interf_bal_smpl('li', $type) );
				break;
			case 'C': // case
				$coords = convert_in_coord($type);
				$liste_obj->add( new interf_bal_smpl('li', 'Aller en X='.$coords['x'].' - Y='.$coords['y']) );
				break;
			}
		}
		if( $liste_obj->get_fils() )
		{
			$div_obj = $this->add( new interf_bal_cont('div', false, 'objectifs_quete') );
			$div_obj->add( new interf_bal_smpl('h4', 'Objectif(s)') );
			$div_obj->add( $liste_obj );
		}
		
		// Récompenses
		$recompenses = explode(';', $etape->get_gain_perso());
		$liste_recomp = new interf_bal_cont('ul');
		$perso = joueur::get_perso();
		foreach($recompenses as $recomp)
		{
			$valeurs = mb_substr($recomp, 1);
			$gains = quete_etape::calcul_gain($valeurs, $perso);
			switch($recomp[0])
			{
			case 'o': // objet
				if( is_array($gains) )
				{
					$txt = '';
					foreach($gains as $g)
					{
						$obj = objet::factory($g);
						if( $txt )
							$txt .= ', ';
						$txt .= $obj->get_nom();
					}
					$liste_recomp->add( new interf_bal_smpl('li', 'Un objet parmi les suivants : '.$txt) );
				}
				else
				{
					$obj = objet::factory($gains);
					$liste_recomp->add( new interf_bal_smpl('li', $obj->get_nom()) );
				}
				break;
			case 's': // stars
				$liste_recomp->add( new interf_bal_smpl('li', is_array($gains) ? 'Des stars' : $gains.' stars') );
				break;
			case 'e':  // expérience
				$liste_recomp->add( new interf_bal_smpl('li', is_array($gains) ? 'De l\'expérience' : $gains.' points d\'expérience') );
				break;
			case 'h':  // honneur
				$liste_recomp->add( new interf_bal_smpl('li', is_array($gains) ? 'De l\'honneur' : $gains.' points d\'honneur') );
				break;
			case 'p':  // réputation
				$liste_recomp->add( new interf_bal_smpl('li', is_array($gains) ? 'De la réputation' : $gains.' points de réputation') );
				break;
			case 'a':  // points d'aptitude
				if( is_array($gains) )
					$liste_recomp->add( new interf_bal_smpl('li', 'Des points d\'aptitudes') );
				else
				{
					$apt = explode(':', $gains);
					$liste_recomp->add( new interf_bal_smpl('li', $apt[1].' point(s) en '.$Gtrad[$apt[0]]) );
				}
				break;
			case 'r':  // recette d'alchimie
				if( is_array($gains) )
					$liste_recomp->add( new interf_bal_smpl('li', 'Une recette d\'alchimie') );
				else
				{
					$recette = new alchimie_recette($gains);
					$liste_recomp->add( new interf_bal_smpl('li', 'La recette de '.$recette->get_nom()) );
				}
				break;
			case 'f':  // recette de forge
				if( is_array($gains) )
					$liste_recomp->add( new interf_bal_smpl('li', 'Une recette de forge') );
				else
				{
					$recette = new forge_recette($gains);
					$liste_recomp->add( new interf_bal_smpl('li', 'La recette de '.$recette->get_nom()) );
				}
				break;
			// b : (de)buff -> on cache
			// t : achievement  -> on cache
			}
		}
		// gains de groupe
		if( $etape->get_gain_groupe() )
			$liste_recomp->add( new interf_bal_smpl('li', 'Des gains pour le groupe…') );
		// gains du royaume
		$recompenses = explode(';', $etape->get_gain_royaume());
		foreach($recompenses as $recomp)
		{
			$gains = mb_substr($recomp, 1);
			switch($recomp[0])
			{
			case 's': // stars
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' stars pour le royaume.') );
				break;
			case 'p': // pierre
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' de pierre pour le royaume.') );
				break;
			case 'a': // sable
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' de sable pour le royaume.') );
				break;
			case 'b': // bois
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' de bois pour le royaume.') );
				break;
			case 'e': // eau
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' d\'eau pour le royaume.') );
				break;
			case 'c': // charbon
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' de charbon pour le royaume.') );
				break;
			case 'm': // essence magique
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' d\'essence magique pour le royaume.') );
				break;
			case 'n': // nourriture
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' de nourriture pour le royaume.') );
				break;
			case 'v': // points de victoire
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' points de victoire pour le royaume.') );
				break;
			case 'r': // points de royaume
				$liste_recomp->add( new interf_bal_smpl('li', $gains.' points de royaume pour le royaume.') );
				break;
			case 'o': // objet dans le quartier général
				$gains = explode('*', $gains);
				$nbr = count($gains) > 1 ? $gains[1] : 1;
				$obj = objet_royaume($gains[0]);
				$liste_recomp->add( new interf_bal_smpl('li', $nbr.' '.$obj->nom().' pour le royaume.') );
				break;
			// 'x', 'X' -> on cache
			}
		}
		
		if( $liste_recomp->get_fils() )
		{
			$div_recomp = $this->add( new interf_bal_cont('div', false, 'recompenses_quete') );
			$div_recomp->add( new interf_bal_smpl('h4', 'Récompense(s)') );
			$div_recomp->add( $liste_recomp );
		}
	}
}

?>