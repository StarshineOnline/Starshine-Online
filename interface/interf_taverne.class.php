<?php
/**
 * @file interf_taverne.class.php
 * Classes pour l'interface de la taverne
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de la taverne
class interf_taverne extends interf_ville_onglets
{
	function __construct(&$royaume, &$case, &$interf=null)
	{
		global $db, $G_interf;
		parent::__construct($royaume, $case);
		$perso = joueur::get_perso();
		
		// Icone & jauges
		$this->icone = $this->set_icone_centre('biere');
		$this->icone->set_tooltip('Taverne');
		// Meilleure régénération possible
		$services = taverne::create(null, null, 'id ASC', false, '1');
		$gains_hp = 0;
		$gains_mp = 0;
		foreach($services as $elt)
		{
			$requis = $elt->get_requis();
			if($requis)
			{ // Vérifier les conditions
				$cond = explode(';', $requis);
				foreach($cond as $tcond)
				{
					$ctype = substr($tcond, 0, 1);
					$cval = substr($tcond, 1);
					$cok = true;
					switch ($ctype)
					{
					case 'q': // quete
						$q = explode(';', $this->perso->get_quete_fini());
						$cok = in_array($cval, $q);
						break;
					default:
						$cok = false;
						break;
					}
					if (!$cok)
						break; // un requis pas matché : on s'arrête
				}
				if (!$cok) // un requis pas matché : on ignore la ligne
					continue;
			}
			$prix = $elt->get_star() + ceil($elt->get_star() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
			$pa = $elt->get_pa();
			$honneur = $elt->get_honneur() + ceil($this->perso->get_honneur() * $elt->get_honneur_pc() / 100);
			$hp = $elt->get_hp() + ceil($this->perso->get_hp_maximum() * $elt->get_hp_pc() / 100);
			$mp = $elt->get_mp() + ceil($this->perso->get_mp_maximum() * $elt->get_mp_pc() / 100);
			$max_pa = $pa ? ($perso->get_pa() / $pa) : 0;
			$max_prix = $prix ? floor($perso->get_star() / $prix) : 0;
			$max_honneur = $honneur ? floor($perso->get_honneur() / $honneur) : 0;
			$n = min($max_pa, $max_prix, $max_honneur);
			$gains_hp = max($gains_hp, $n*$hp);
			$gains_mp = max($gains_mp, $n*$mp);
		}
		$prct_hp = min($gains_hp / ($perso->get_hp_max() - $perso->get_hp()), 1) * 100;
		$prct_mp = min($gains_mp / ($perso->get_mp_max() - $perso->get_mp()), 1) * 100;
		// Ivresse
		$ivresse = $perso->get_buff('ivresse');
		$ivresse = $ivresse ? $ivresse->get_effet() : 0; 
		// Jauges
		$this->set_jauge_ext(round(min($prct_hp, $prct_mp)), '%', 'mp', 'Meilleure régénération possible : ');
		$this->set_jauge_int($ivresse, '%', 'pa', 'Ivresse : ');	
		
		// Onglets
		if( !$interf )
			$interf = $G_interf->creer_taverne_repos($royaume);
		if( get_class($interf) == 'interf_tbl_quetes' )
			$onglet = 'tab_quetes';
		else
			$onglet = substr(get_class($interf), 15);
		$this->onglets->add_onglet('Repos', 'taverne.php?ajax=2&type=repos', 'tab_repos', 'ecole_mag', $onglet=='repos');
		$this->onglets->add_onglet('Bar', 'taverne.php?ajax=2&type=bar', 'tab_bar', 'ecole_mag', $onglet=='bar');
		$this->onglets->add_onglet('Jeux', 'taverne.php?ajax=2&type=jeux', 'tab_jeux', 'ecole_mag', $onglet=='jeux');
		if( quete::get_nombre_quetes($perso, $royaume, 'taverne') )
			$this->onglets->add_onglet('Quêtes', 'taverne.php?type=quetes&ajax=2', 'tab_quetes', 'ecole_mag', $onglet=='quetes');
		
		interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$onglet) );
		$this->onglets->get_onglet('tab_'.$onglet)->add( $interf );
	}
}

/// Classe  pour les listes d'achats de services de repos
class interf_taverne_repos extends interf_tableau //interf_liste_achat
{	
	//const url = 'taverne.php';
	protected $royaume;
	protected $perso;
	function __construct(&$royaume/*, $nbr_alertes=0*/)
	{
		parent::__construct(false, 'table table-striped');
		$this->perso = &joueur::get_perso();
		$this->royaume = &$royaume;
		$this->aff_titres_col();
		$services = taverne::create(null, null, 'id ASC', false, '1');
		foreach($services as $elt)
		{
			$requis = $elt->get_requis();
			if($requis)
			{ // Vérifier les conditions
				$cond = explode(';', $requis);
				foreach($cond as $tcond)
				{
					$ctype = substr($tcond, 0, 1);
					$cval = substr($tcond, 1);
					$cok = true;
					switch ($ctype)
					{
					case 'q': // quete
						$q = explode(';', $this->perso->get_quete_fini());
						$cok = in_array($cval, $q);
						break;
					default:
						$cok = false;
						break;
					}
					if (!$cok)
						break; // un requis pas matché : on s'arrête
				}
				if (!$cok) // un requis pas matché : on ignore la ligne
					continue;
			}
			$this->aff_cont_col($elt);
		}
		//parent::__construct($royaume, 'tbl_repos', $services, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->nouv_cell('Nom');
		$this->nouv_cell('Stars');
		$this->nouv_cell('Cout en PA');
		$this->nouv_cell('Cout en honneur');
		$this->nouv_cell('HP gagnés');
		$this->nouv_cell('MP gagnés');
		$this->nouv_cell('Achat');
	}
	
	function aff_cont_col(&$elt)
	{
		$prix = $elt->get_star() + ceil($elt->get_star() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
		$stars_ok = $this->perso->get_star() >= $prix;
		$pa_ok = $this->perso->get_pa() >= $elt->get_pa();
		$achat = $stars_ok && $pa_ok;
		$this->nouv_ligne(false, $achat ? '' : 'non-achetable');
		if( $this->perso->get_bonus_shine(12) !== false &&  $this->perso->get_bonus_shine(12)->get_valeur() == 2)
			$nom = $elt->get_nom_f();
		else
			$nom = $elt->get_nom();
		$lien = new interf_bal_smpl('a', $nom, 'elt'.$elt->get_id());
		$this->nouv_cell( $lien );
		$url = 'taverne.php?action=infos&type=achat&id='.$elt->get_id();
		$lien->set_attribut('onclick', 'chargerPopover(\'elt'.$elt->get_id().'\', \'info_elt'.$elt->get_id().'\', \'right\', \''.$url.'\', \''.addslashes($nom).'\');');
		$this->nouv_cell( new interf_bal_smpl('span', $prix, false, $stars_ok ? '' : 'text-danger') );
		$this->nouv_cell( new interf_bal_smpl('span', $elt->get_pa(), false, $pa_ok ? '' : 'text-danger') );
		$this->nouv_cell( $elt->get_honneur() + ceil($this->perso->get_honneur() * $elt->get_honneur_pc() / 100) );
		$this->nouv_cell( $elt->get_hp() + ceil($this->perso->get_hp_maximum() * $elt->get_hp_pc() / 100) );
		$this->nouv_cell( $elt->get_mp() + ceil($this->perso->get_mp_maximum() * $elt->get_mp_pc() / 100) );
		if( $achat )
				$this->nouv_cell( new interf_lien('Achat', 'taverne.php?action=achat&type=achat&id='.$elt->get_id()) );
			else
				$this->nouv_cell('&nbsp;');
	}
}

class interf_taverne_bar extends interf_cont
{
	public $ivresse;
	private $royaume;
	function __construct($royaume)
	{
		global $G_url;
		$this->royaume = &$royaume;
		$perso = &joueur::get_perso();
		$this->ivresse = $perso->get_buff('ivresse');
		if( $this->ivresse && $this->ivresse->get_effet() >= 100 )
			$this->add( new interf_bal_smpl('p', 'Vous avez trop bu, le tavernier refuse de vous servir.', false, 'descr_rp') );
		else
		{
			$div_btn = $this->add( new interf_bal_cont('div', 'boire') );
			$div_btn->add( new interf_lien('Boire (1 PA, 1 star)', $G_url->get('action', 'boire'), false, 'btn btn-default') );
		}
	}
	
	function rumeur($de)
	{
		global $Trace, $db, $Gtrad;
		$ivresse = $bar->ivresse ? $bar->ivresse->get_effet() : 0;
		$vrai = comp_sort::test_de(100, 10 + $ivresse / 2);
		if( $de <= 30 )
		{ // info personnage
			$de = rand(1, 100);
			if( $de <= 10 )
				$this->aff_rumeur('perso-star', perso::get_perso_rumeur('star', $vrai));
			else if( $de <= 20 )
				$this->aff_rumeur('perso-melee', perso::get_perso_rumeur('melee', $vrai));
			else if( $de <= 30 )
				$this->aff_rumeur('perso-esquive', perso::get_perso_rumeur('esquive', $vrai));
			else if( $de <= 35 )
				$this->aff_rumeur('perso-blocage', perso::get_perso_rumeur('blocage', $vrai));
			else if( $de <= 45 )
				$this->aff_rumeur('perso-distance', perso::get_perso_rumeur('distance', $vrai));
			else if( $de <= 55 )
				$this->aff_rumeur('perso-incantation', perso::get_perso_rumeur('incantation', $vrai));
			else if( $de <= 60 )
				$this->aff_rumeur('perso-element', perso::get_perso_rumeur('sort_element', $vrai));
			else if( $de <= 65 )
				$this->aff_rumeur('perso-vie', perso::get_perso_rumeur('sort_vie', $vrai));
			else if( $de <= 70 )
				$this->aff_rumeur('perso-mort', perso::get_perso_rumeur('sort_mort', $vrai));
			else if( $de <= 80 )
				$this->aff_rumeur('perso-survie', perso::get_perso_rumeur('survie', $vrai));
			else if( $de <= 90 )
				$this->aff_rumeur('perso-dressage', perso::get_perso_rumeur('dressage', $vrai));
			else
				$this->aff_rumeur('perso-artisanat', perso::get_perso_rumeur('artisanat', $vrai));
		}
		else if( $de <= 35 )
		{ // info personnage qui cache son classement
			$de = rand(1, 100);
			if( $de <= 5 )
				$this->aff_rumeur('perso-star', perso::get_perso_rumeur('star', $vrai, true));
			else if( $de <= 10 )
				$this->aff_rumeur('perso-melee', perso::get_perso_rumeur('melee', $vrai, true));
			else if( $de <= 15 )
				$this->aff_rumeur('perso-esquive', perso::get_perso_rumeur('esquive', $vrai, true));
			else if( $de <= 20 )
				$this->aff_rumeur('perso-blocage', perso::get_perso_rumeur('blocage', $vrai, true));
			else if( $de <= 25 )
				$this->aff_rumeur('perso-distance', perso::get_perso_rumeur('distance', $vrai, true));
			else if( $de <= 30 )
				$this->aff_rumeur('perso-incantation', perso::get_perso_rumeur('incantation', $vrai, true));
			else if( $de <= 35 )
				$this->aff_rumeur('perso-element', perso::get_perso_rumeur('sort_elemet', $vrai, true));
			else if( $de <= 40 )
				$this->aff_rumeur('perso-vie', perso::get_perso_rumeur('sort_vie', $vrai, true));
			else if( $de <= 45 )
				$this->aff_rumeur('perso-mort', perso::get_perso_rumeur('sort_mort', $vrai, true));
			else if( $de <= 50 )
				$this->aff_rumeur('perso-survie', perso::get_perso_rumeur('survie', $vrai, true));
			else if( $de <= 55 )
				$this->aff_rumeur('perso-dressage', perso::get_perso_rumeur('dressage', $vrai, true));
			else if( $de <= 60 )
				$this->aff_rumeur('perso-artisanat', perso::get_perso_rumeur('artisanat', $vrai, true));
			else if( $de <= 70 )
				$this->aff_rumeur('perso-honneur', perso::get_perso_rumeur('honneur', $vrai, true));
			else if( $de <= 80 )
				$this->aff_rumeur('perso-reputation', perso::get_perso_rumeur('reputation', $vrai, true));
			else if( $de <= 85 )
				$this->aff_rumeur('perso-niveau', perso::get_perso_rumeur('level', $vrai, true));
			else if( $de <= 95 )
				$this->aff_rumeur('perso-pvp', perso::get_perso_rumeur('frag', $vrai, true));
			else
				$this->aff_rumeur('perso-suicide', perso::get_perso_rumeur('mort', $vrai, true));
		}
		else if( $de <= 40 )
		{ // position personnage
			///@ todo faire fausse rumeur
			$diplos = $this->royaume->get_diplos();
			$max_de = 0;
			$royaumes = array();
			foreach($diplos as $r=>$d)
			{
				if( $d == 127 )
					$d = 20;
				else
					$d = 10 - $d;
				$max_de += $d;
				$royaumes[$r] = $max_de;
			}
			$de = rand(1, $max_de);
			foreach($royaumes as $r=>$m)
			{
				if( $m <= $de )
				{	
					$royaume = $r;
					$id = $Trace[$r]['numrace'];
					break;
				}
			}
			/// @todo passer à l'objet
			$requete = 'SELECT c.nom, x, y FROM construction AS c INNER JOIN batiment AS b ON c.id_batiment = b.id WHERE c.nom != b.nom AND royaume = '.$this->royaume->get_id().' AND x <= 190 AND y <= 190 ORDER BY RAND()';
			$req = $db->query($requete);
			$trouve = false;
			while( $row = $db->read_assoc($req) )
			{
				$x_min = $row['x'] - 10;
				$x_max = $row['x'] + 10;
				$y_min = $row['y'] - 10;
				$y_max = $row['y'] + 10;
				$persos = perso::create(null, null, 'RAND() LIMIT 1', false, 'x BETWEEN '.$x_min.' AND '.$x_max.' AND y BETWEEN '.$y_min.' AND '.$y_max);
				if( $persos )
				{
					$this->aff_rumeur('perso-lieu', $persos[0]->get_nom(), $row['nom']);
					$trouve = true;
					break;
				}
			}
			if( !$trouve )
			{
				$x_min = $Trace[$royaume]['spawn_x'] - 10;
				$x_max = $Trace[$royaume]['spawn_x'] + 10;
				$x_min = $Trace[$royaume]['spawn_y'] - 10;
				$x_max = $Trace[$royaume]['spawn_y'] + 10;
				$persos = perso::create(null, null, 'RAND() LIMIT 1', false, 'x BETWWEN '.$x_min.' AND '.$x_max.' AND y BETWEEN '.$y_min.' AND '.$y_max);
				if( $persos )
				{
					$roy = new royaume($id);
					$this->aff_rumeur('perso-lieu', $persos[0]->get_nom(), $roy->get_capitale());
					$trouve = true;
				}
			}
			if( !$trouve )
				$this->conversation();
		}
		else if( $de <= 50 )
		{ // info royaume
			$de = rand(1, 7);
			if( $de >= 4 )
				$class = 0;
			else if( $de <= 6 )
				$class = 1;
			else
				$class = 2;
			$de = rand(1, 100);
			if( $de <= 5 )
			{ // hp bâtiments internes
				/// @todo passer à l'objet
				///@ todo faire fausse rumeur
				$requete = 'SELECT c.hp / b.hp as etat, type, id_royaume FROM construction_ville AS c INNER JOIN batiment_ville AS b ON c.id_batiment ORDER BY RAND() LIMIT 1';
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$royaume = new royaume($row['id_royaume']);
				$this->aff_rumeur('royaume-batint-'.($row['etat']>.5 ? 'bon':'mauvais'), $Gtrad[$row['nom']], $royaume->get_capitale(), $Gtrad[$royaume->get_race()], $royaume->get_nom());
			}
			else if( $de <= 25 )
			{ // resssource
				///@ todo faire fausse rumeur
				$de = rand(0, 6);
				$ressources = array('pierre','bois','eau','sable','charbon','essence','food');
				$ressource = $ressources[$de];
				/// @todo passer à l'objet
				switch( rand(0, 3) )
				{
				case 0:
					$requete = 'SELECT nom, race, capitale FROM royaume WHERE id > 0 ORDER BY '.$ressource.' DESC LIMIT '.$class.', 1';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$this->aff_rumeur('royaume-stock-'.$ressource, $row['nom'], $row['capitale'], $Gtrad[$row['race']]);
					break;
				case 1:
					$requete = 'SELECT nom, race, capitale FROM royaume WHERE id > 0 ORDER BY '.$ressource.' ASC LIMIT '.$class.', 1';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$this->aff_rumeur('royaume-stock-'.$ressource, $row['nom'], $row['capitale'], $Gtrad[$row['race']]);
					break;
				case 2:
					$ind = $de == 6 ? 25 : $de + 18;
					$requete = 'SELECT barbare, elfebois, elfehaut, humain, humainnoir, nain, orc, scavenger, troll, vampire, mortvivant FROM stat_jeu  ORDER BY id DESC LIMIT 1';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$prod = array();
					foreach($row as $r=>$s)
					{
						$stats = unserialize($s);
						$prod[$r] = $stats[$ind];
					}
					arsort($prod);
					$prod = array_keys($prod);
					$royaume = new royaume( $Trace[ $prod[$class] ]['numrace'] );
					$this->aff_rumeur('royaume-prod-'.$ressource, $roy->get_nom(), $roy->get_capitale(), $Gtrad[$roy->get_race()]);
					break;
				case 3:
					$ind = $de == 6 ? 25 : $de + 18;
					$requete = 'SELECT barbare, elfebois, elfehaut, humain, humainnoir, nain, orc, scavenger, troll, vampire, mortvivant FROM stat_jeu  ORDER BY id DESC LIMIT 1';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$prod = array();
					foreach($row as $r=>$s)
					{
						$stats = unserialize($s);
						$prod[$r] = $stats[$ind];
					}
					asort($prod);
					$prod = array_keys($prod);
					$royaume = new royaume( $Trace[ $prod[$class] ]['numrace'] );
					$this->aff_rumeur('royaume-fprod-'.$ressource, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
					break;					
				}
			}
			else if( $de <= 75 )
			{
				$plus = (bool)rand(0,1);
				$suf = $plus ? '' : '-peu';
				if( $de <= 35 )
					$info = 'point_victoire';
				else if( $de <= 40 )
					$info = 'alchimie';
				else if( $de <= 50 )
					$info = 'batint';
				else if( $de <= 60 )
				{
					$bats = array('bourg', 'fort', 'tour', 'mur', 'arme-de-siege', 'mine');
					$info = $bats[rand(0, 6)];
				}
				else if( $de <= 65 )
					$info = 'conso_food';
				else if( $de <= 70 )
					$info = 'entretien';
				else if( $de <= 75 )
					$info = 'taxe';
				$royaume = royaume::get_royaume_rumeur($info, $plus, $class, $vrai);
				$this->aff_rumeur('royaume-'.$info.$suf, $royaume->get_nom(), $this->royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
			}
			else if( $de <= 85 )
			{ // terrains les plus / moins possédés
				$royaumes = array_keys($Trace);
				interf_debug::dump_enreg($royaumes);
				$race = $royaumes[rand(0, count($royaumes)-1)];
				interf_debug::dump_enreg($race);
				$royaume = new royaume($Trace[$race]['numrace']);
				$plus = (bool)rand(0,1);
				$suf = $plus ? '' : '-peu';
				$sens = $plus ? ' DESC' : ' ASC';
				/// @todo passer à l'objet
				$requete = 'SELECT info, COUNT(*) nbr FROM map WHERE royaume = '.$royaume->get_id().' AND x <= 190 AND y <= 190 GROUP BY info ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$this->aff_rumeur('royaume-'.type_terrain($row['info']).$suf, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
			}
			else
			{
				$royaumes = array_keys($Trace);
				$de = rand(0, 19);
				$royaume = $de > 10 ? $this->royaume : new royaume( $Trace[ $royaumes[$de] ]['numrace'] );
				/// @todo passer à l'objet
				if( $de <= 90 )
				{ // bâtiments internes qui rapportent le plus
					$requete = 'SELECT '.$royaume->get_race().' FROM stat_jeu ORDER BY id DESC LIMIT 1';
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$stats = array_slice(unserialize( $row[0] ), 2, 10);
					$bats = array('hotel_vente', 'taverne', 'forgeron', 'armurerie', 'alchimiste', 'enchanteur', 'ecole_magie', 'ecole_combat', 'téléportation', 'chasse');
					$tab = array_combine($bats, $stats);
					arsort($tab);
					$bat = array_keys($tab)[$class];
					$this->aff_rumeur('royaume-gains-'.$bat, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
				}
				else if( $de <= 95 )
				{
					if( $royaume->get_conso_food_th() > $royaume->get_conso_food() )
						$this->aff_rumeur('royaume-conso-monte', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
					else if( $royaume->get_conso_food_th() < $royaume->get_conso_food() )
						$this->aff_rumeur('royaume-conso-baisse', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
					else
						$this->aff_rumeur('royaume-conso-stable', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
				}
				else
				{
					if( $royaume->get_entretien_th() > $royaume->get_entretien() )
						$this->aff_rumeur('royaume-entretien-monte', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
					else if( $royaume->get_entretien_th() < $royaume->get_entretien() )
						$this->aff_rumeur('royaume-entretien-baisse', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
					else
						$this->aff_rumeur('royaume-entretien-stable', $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
				}
			}
		}
		else if( $de <= 60 )
		{ // info classement perso disponibles
			$de = rand(1, 100);
			if( $de <= 10 )
				$this->aff_rumeur('perso-honneur', perso::get_perso_rumeur('honneur', $vrai));
			else if( $de <= 20 )
				$this->aff_rumeur('perso-reputation', perso::get_perso_rumeur('reputation', $vrai));
			else if( $de <= 40 )
				$this->aff_rumeur('perso-niveau', perso::get_perso_rumeur('level', $vrai));
			else if( $de <= 60 )
				$this->aff_rumeur('perso-pvp', perso::get_perso_rumeur('frag', $vrai));
			else if( $de <= 80 )
				$this->aff_rumeur('perso-suicide', perso::get_perso_rumeur('mort', $vrai));
			else
				$this->aff_rumeur('perso-crime', perso::get_perso_rumeur('crime', $vrai));
		}
		else if( $de <= 70 )
		{ // info classement groupe
			$plus = (bool)rand(0,1);
			$suf = $plus ? '' : '-peu';
			$de = rand(1, 100);
			if( $de <= 5 )
				$this->aff_rumeur('groupe-honneur', perso::get_groupe_rumeur('honneur', $plus, $vrai));
			else if( $de <= 10 )
				$this->aff_rumeur('groupe-reputation', perso::get_groupe_rumeur('reputation', $plus, $vrai));
			else if( $de <= 20 )
				$this->aff_rumeur('groupe-niveau', perso::get_groupe_rumeur('level', $plus, $vrai));
			else if( $de <= 30 )
				$this->aff_rumeur('groupe-pvp', perso::get_groupe_rumeur('frag', $plus, $vrai));
			else if( $de <= 40 )
				$this->aff_rumeur('groupe-suicide', perso::get_groupe_rumeur('mort', $plus, $vrai));
			else if( $de <= 50 )
				$this->aff_rumeur('groupe-crime', perso::get_groupe_rumeur('crime', $plus, $vrai));
			else if( $de <= 60 )
				$this->aff_rumeur('groupe-survie', perso::get_groupe_rumeur('survie', $plus, $vrai));
			else if( $de <= 70 )
				$this->aff_rumeur('groupe-star', perso::get_groupe_rumeur('star', $plus, $vrai));
			else if( $de <= 80 )
				$this->aff_rumeur('groupe-artisanat', perso::get_groupe_rumeur('artisanat', $plus, $vrai));
			else if( $de <= 90 )
				$this->aff_rumeur('groupe-vie', perso::get_groupe_rumeur('hp', $plus, $vrai));
			else
				$this->aff_rumeur('groupe-mana', perso::get_groupe_rumeur('mp', $plus, $vrai));
		}
		else if( $de <= 80 )
		{ //  info classement royaume
			$de = rand(1, 7);
			if( $de >= 4 )
				$class = 0;
			else if( $de <= 6 )
				$class = 1;
			else
				$class = 2;
			$plus = (bool)rand(0,1);
			$suf = $plus ? '' : '-peu';
			$sens = $plus ? ' DESC' : ' ASC';
			$de = rand(1, 100);
			if( $de <= 20 )
			{
				$royaume = royaume::get_royaume_rumeur('point_victoire_total', $plus, $class, $vrai);
				$this->aff_rumeur('royaume-pv'.$suf, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
			}
			else if( $de <= 40 )
			{
				$royaume = royaume::get_royaume_rumeur('case', $plus, $class, $vrai);
				$this->aff_rumeur('royaume-case'.$suf, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
			}
			else
			{
				if( $de <= 50 )
					$info = 'honneur';
				else if( $de <= 60 )
					$info = 'reputation';
				else if( $de <= 70 )
					$info = 'level';
				else if( $de <= 80 )
					$info = 'frag';
				else if( $de <= 90 )
					$info = 'mort';
				else
					$info = 'crime';
				$royaume = perso::get_royaume_rumeur($info, $plus, $class, $vrai);
				$this->aff_rumeur('royaume-'.$info.$suf, $royaume->get_nom(), $royaume->get_capitale(), $Gtrad[$royaume->get_race()]);
			}
		}
		else
		{ // stats
			$plus = (bool)rand(0,1);
			$suf = $plus ? '' : '-peu';
			$sens = $plus ? ' DESC' : ' ASC';
			$de = rand(0, 4);
			switch($de)
			{
			case 1:
			case 2:
				$de2 = rand(1, 7);
				if( $de >= 4 )
					$class = 0;
				else if( $de <= 6 )
					$class = 1;
				else
					$class = 2;
				break;
			case 0:
			case 4:
				$de2 = rand(1, 31);
				$val = 16;
				$incr = 8;
				for($class=0; $de2 > $val; $class++)
				{
					$val += $incr;
					$incr /= 2;
				}
			}
			/// @todo passer à l'objet 
			switch($de)
			{
			case 0:
				$requete = 'SELECT c.nom, COUNT(*) AS nbr FROM perso AS p INNER JOIN classe AS c ON c.id = p.classe_id WHERE statut = "actif" AND level > 0 GROUP BY c.id ORDER BY nbr '.$sens.' LIMIT '.$class.', 1';
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$this->aff_rumeur('stats-classe'.$suf, $row['nom']);
				break;
			case 1:
				$requete = 'SELECT r.nom, r.capitale, r.race, COUNT(*) AS nbr FROM perso AS p INNER JOIN royaume AS r ON r.race = p.race WHERE statut = "actif" AND level > 0 GROUP BY r.race ORDER BY nbr '.$sens.' LIMIT '.$class.', 1';
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$this->aff_rumeur('stats-race'.$suf, $row['nom'], $row['capitale'], $Gtrad[$row['race']]);
				break;
			case 2:
				$requete = 'SELECT nom, capitale, race FROM royaume WHERE id > 0 ORDER BY star '.$sens.' LIMIT '.$class.', 1';
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$this->aff_rumeur('stats-race'.$suf, $row['nom'], $row['capitale'], $Gtrad[$row['race']]);
				break;
			case 3:
				$royaumes = royaume::create(null, null, 'RAND() LIMIT 2', false, 'id > 0');
				$diplo = $Gtrad['diplo'.$royaumes[0]->get_diplo( $royaumes[1]->get_race() )];
				if( !$diplo )
					log_admin::log('erreur', 'Erreur rumeur diplomatie : diplo'.$royaumes[0]->get_diplo( $royaumes[1]->get_race() ));
				$this->aff_rumeur('diplomatie', $royaumes[0]->get_nom(), $royaumes[0]->get_capitale(), $Gtrad[$royaumes[0]->get_race()], $royaumes[1]->get_nom(), $royaumes[1]->get_capitale(), $Gtrad[$royaumes[1]->get_race()], $diplo);
				break;
			case 4:
				switch(rand(1, 10))
				{
				case 1:
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE m.terrain LIKE "1;22%" AND mm.x <= 190 AND mm.y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					$type = '-plaine';
					break;
				case 2:
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE (terrain LIKE "2%" OR terrain LIKE "2;%") AND mm.x <= 190 AND mm.y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					$type = '-foret';
					break;
				case 3:
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE (terrain LIKE "4%" OR terrain LIKE "4;%") AND mm.x <= 190 AND mm.y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					$type = '-desert';
					break;
				case 4:
					$type = '-neige';
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE (terrain LIKE "3%" OR terrain LIKE "3;%") AND mm.x <= 190 AND mm. y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					break;
				case 5:
					$type = '-montagne';
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE m.terrain LIKE "6;23%" AND mm.x <= 190 AND mm.y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					break;
				case 6:
					$type = '-marais';
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type INNER JOIN map ON map.x = mm.x AND map.y = mm.y WHERE m.terrain LIKE "7;11%" AND mm.x <= 190 AND mm.y <= 190 AND map.info = 7 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					break;
				case 7:
					$type = '-terre_maudite';
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type INNER JOIN map ON map.x = mm.x AND map.y = mm.y WHERE m.terrain LIKE "7;11%" AND mm.x <= 190 AND mm.y <= 190 AND map.info = 11 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					break;
				case 8:
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type WHERE m.terrain != "rand" AND mm.x <= 190 AND mm.y <= 190 GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					$type = '';
					break;
				case 9:
				case 10:
					$requete = 'SELECT m.nom, COUNT(*) AS nbr FROM monstre As m INNER JOIN map_monstre AS mm ON m.id = mm.type INNER JOIN map ON map.x = mm.x AND map.y = mm.y WHERE m.terrain != "rand" AND mm.x <= 190 AND mm.y <= 190 AND map.royaume = '.$this->royaume->get_id().' GROUP BY m.id ORDER BY nbr'.$sens.' LIMIT '.$class.', 1';
					$type = '-royaume';
					break;
				default:
					/// @todo Tous terrains, proportionnellement au nombre de cases 
					$type = '-prop';
					break;
				}
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$this->aff_rumeur('monstre'.$type.$suf, $row['nom']);
			}
		}
	}
	protected function aff_rumeur($type, $nom='', $lieu='', $race='', $nom2='', $lieu2='', $race2='', $diplo='')
	{
		global $db;
		$div = $this->add( new interf_bal_cont('div', 'rumeurs') );
		$div->add( new interf_bal_smpl('p', 'Tout en buvant, vous écoutez les conversations autour de vous :', false, 'descr_rp') );
		/// @todo passer à l'objet
		$royaume = 1 << $this->royaume->get_id();
		$requete = 'SELECT texte FROM rumeurs WHERE type = "'.$type.'" AND royaumes & '.$royaume.' ORDER BY RAND() LIMIT 1';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		if( !$row )
		{
			log_admin::log('erreur', 'Pas de rumeur trouvé pour '.$type);
			$requete = 'SELECT texte FROM rumeurs WHERE type = "conversation" AND royaumes & '.$royaume.' ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_array($req);
		}
		$texte = new texte( str_replace(array('%nom%', '%lieu%', '%race%', '%nom2%', '%lieu2%', '%race2%', '%diplo%'), array($nom, $lieu, $race, $nom2, $lieu2, $race2, $diplo), $row[0]), texte::rumeurs);
		$div->add( new interf_bal_smpl('span', $texte->parse()) );
	}
	function conversation()
	{
		$this->aff_rumeur( 'conversation' );
	}
	function quete(&$quete)
	{
		$div = $this->add( new interf_bal_cont('div', false, 'rumeurs') );
		$div->add( new interf_bal_smpl('p', 'Une personne vous aborde :', false, 'descr_rp') );
		$etape = quete_etape::create(array('id_quete', 'etape', 'variante'), array($quete->get_id(), 1, 0))[0];
		$texte = new texte($etape->get_description(), texte::rumeurs);
		$div->add( new interf_bal_smpl('p', $texte->parse()) );
		$div->add( new interf_bal_smpl('p', 'Vous obtenez la quête '.$quete->get_titre(), false, 'xsmall') );
	}
	function indice()
	{
		global $db;
		$perso = joueur::get_perso();
		$div = $this->add( new interf_bal_cont('div', 'rumeurs') );
		$div->add( new interf_bal_smpl('p', 'Tout en buvant, vous écoutez les conversations autour de vous :', false, 'descr_rp') );
		/// @todo passer à l'objet
		$royaume = 1 << $this->royaume->get_id();
		$requete = 'SELECT r.texte FROM rumeurs AS r INNER JOIN quete_perso AS q ON q.id_etape = r.etape_quete WHERE type = "indice" AND royaumes & '.$royaume.' AND q.id_perso = '.$perso->get_id().' ORDER BY RAND() LIMIT 1';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		if( !$row )
		{
			$requete = 'SELECT texte FROM rumeurs WHERE type = "indice" AND royaumes & '.$royaume.' AND quete = 0 ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_array($req);
		}
		if( !$row )
		{
			$requete = 'SELECT texte FROM rumeurs WHERE type = "conversation" AND royaumes & '.$royaume.' ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_array($req);
		}
		$texte = new texte($row[0], texte::rumeurs);
		$ivresse = $bar->ivresse ? $bar->ivresse->get_effet() : 0;
		$texte->set_indice_vrai( comp_sort::test_de(100, 10 + $ivresse / 2) );
		$div->add( new interf_bal_smpl('span', $texte->parse()) );
	}
	function gain_ivresse()
	{
		$div = $this->add( new interf_bal_smpl('p', 'Votre ivresse augmente de 1.', false, 'rumeurs') );
	}
}

class interf_taverne_jeux extends interf_cont
{
	function __construct($jeu=null, $mise=false, $score=0, $score_adv=0)
	{
		global $G_url;
		if( $mise )
			$this->mise($jeu);
		else
		{
			switch( $jeu )
			{
			case 'distance':
				$this->distance($score, $score_adv);
				break;
			case 'melee':
				$this->melee();
				break;
			case 'esquive':
				$this->esquive($score, $score_adv);
				break;
			case 'blocage':
				$this->blocage($score, $score_adv);
				break;
			case 'dressage':
				$this->dressage($score, $score_adv);
				break;
			case 'incantation':
				$this->melee();
				break;
			case 'sort_element':
				$this->sort_element($score, $score_adv);
				break;
			case 'sort_vie':
				$this->sort_vie($score);
				break;
			case 'sort_mort':
				$this->sort_mort($score);
				break;
			default:
				$this->accueil();
				break;
			}
		}
	}
	protected function accueil()
	{
		global $G_url;
		$div = $this->add( new interf_bal_cont('div', 'jeux') );
		$div->add( new interf_bal_smpl('p', 'À quel jeu voulez-vous jouer ?') );
		$div_btn = $div->add( new interf_bal_cont('div', false, 'btn-group-vertical') );
		$G_url->add('action', 'mise');
		$div_btn->add( new interf_lien('Bras de fer (mêlée, coût variable)', $G_url->get('jeu', 'melee'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Flêchettes (tir à distance, 5 PA)', $G_url->get('jeu', 'distance'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Évitement (esquive, coût variable)', $G_url->get('jeu', 'esquive'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Attrape-balle (blocage, coût variable)', $G_url->get('jeu', 'blocage'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Adresse animalière (dressage, 5 PA)', $G_url->get('jeu', 'dressage'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Jeu télékinétique (incantation, 5 PA)', $G_url->get('jeu', 'incantation'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Animation du feu (magie élémentaire, 5 PA)', $G_url->get('jeu', 'sort_element'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Développement végétal (magie de la vie, 5 PA)', $G_url->get('jeu', 'sort_vie'), false, 'btn btn-default') );
		$div_btn->add( new interf_lien('Osselets magiques (nécromancie, 5 PA)', $G_url->get('jeu', 'sort_mort'), false, 'btn btn-default') );
	}
	protected function mise($jeu)
	{
		global $G_url;
		$perso = joueur::get_perso();
		switch($jeu)
		{
		case 'distance':
			$ratio = $perso->get_distance() / 800;
			break;
		case 'melee':
			$ratio = $perso->get_melee() / 800;
			break;
		case 'esquive':
			$ratio = $perso->get_esquive() / 800;
			break;
		case 'blocage':
			$ratio = $perso->get_blocage() / 650;
			break;
		case 'dressage':
			$ratio = $perso->get_dressage() / 600;
			break;
		case 'incantation':
			$ratio = $perso->get_incantation() / 800;
			break;
		case 'sort_element':
			$ratio = $perso->get_sort_element() / 550;
			break;
		case 'sort_vie':
			$ratio = $perso->get_sort_vie() / 550;
			break;
		case 'sort_mort':
			$ratio = $perso->get_sort_mort() / 550;
			break;
		}
		$max = round( 2 / sqrt($ratio) );
		$div = $this->add( new interf_bal_cont('div', 'jeux') );
		$div->add( new interf_bal_smpl('p', 'Votre adversaire veut bien miser jusqu\'à '.$max.' stars.') );
		$form = $div->add( new interf_form($G_url->get('action','jouer'), 'mise', 'get', 'input-group') );
		$form->add( new interf_bal_smpl('span', 'Vous misez', false, 'input-group-addon') );
		$mise = $form->add( new interf_chp_form('number', 'mise', false, 0, null, 'form-control') );
		$mise->set_attribut('min', 0);
		$mise->set_attribut('max', $max);
		$mise->set_attribut('step', 1);
		$form->add( new interf_bal_smpl('span', 'stars', false, 'input-group-addon') );
		$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn = $btns->add( new interf_chp_form('submit', false, false, 'Miser', null, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'mise\');');
		$div->add( new interf_lien('Revenir à la liste des jeux', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
	}
	protected function distance($score, $score_adv)
	{
		global $G_url;
		$this->add( new interf_bal_smpl('p', 'Une cible se trouve devans vous, votre adversaire et vous avez chacun cinq flêchettes. Les anneaux concentriques donnent des points croissant vers le centre.', false, 'descr_rp') );
		$div = $this->add( new interf_bal_cont('div', false, 'descr_rp') );
		$div->add( new interf_bal_smpl('p', 'Vous lancez votre flêchette et marquez '.$score.' points') );
		$div->add( new interf_bal_smpl('p', 'Votre adversaire lance sa flêchette et marque '.$score_adv.' points') );
		interf_alerte::aff_enregistres($this);
		$liste = $this->add( new interf_bal_cont('ul') );
		$li1 = $liste->add( new interf_bal_cont('li') );
		$li1->add( new interf_bal_smpl('strong', 'Votre score : ') );
		$li1->add( new interf_bal_smpl('span', $_SESSION['score'], 'score') );
		$li2 = $liste->add( new interf_bal_cont('li') );
		$li2->add( new interf_bal_smpl('strong', 'Score de votre adversaire : ') );
		$li2->add( new interf_bal_smpl('span', $_SESSION['score_adv'], 'score_adv') );
		$li3 = $liste->add( new interf_bal_cont('li') );
		$li3->add( new interf_bal_smpl('strong', 'Lancer : ') );
		$li3->add( new interf_bal_smpl('span', $_SESSION['passe'], 'passe') );
		$li3->add( new interf_txt(' / 5') );
		if( $_SESSION['passe'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous gnagnez la partie et remportez '.($_SESSION['mise']*2).' stars.', false, 'descr_rp') );
			else if( $_SESSION['score'] < $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous perdez la partie, votre adversaire rafle la mise.', false, 'descr_rp') );
			else
				$this->add( new interf_bal_smpl('p', 'Match nul, vous récupérez votre mise.', false, 'descr_rp') );
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Changer de jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Rejouer', $G_url->get('action', 'mise'), false, 'btn btn-default') );
		}
		else
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Arrêter le jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Continuer', $G_url->get('action', 'jouer'), false, 'btn btn-primary') );
		}
	}
	protected function melee()
	{
		$this->opposition();
	}
	protected function opposition()
	{
		global $G_url;
		if( $_SESSION['score'] >= 1 )
			$this->add( new interf_bal_smpl('p', 'Vous avez gagné cette partie ! Vous remportez '.($_SESSION['mise']*2).' stars.', false, 'descr_rp') );
		else if( $_SESSION['score'] >= .5 )
			$this->add( new interf_bal_smpl('p', 'Vous êtes proche de gagner cette partie.', false, 'descr_rp') );
		else if( $_SESSION['score'] > 0 )
			$this->add( new interf_bal_smpl('p', 'Vous avez l\'avantage.', false, 'descr_rp') );
		else if( $_SESSION['score'] > -.5 )
			$this->add( new interf_bal_smpl('p', 'Votre adversaire à l\'avantage.', false, 'descr_rp') );
		else if( $_SESSION['score'] > -1 )
			$this->add( new interf_bal_smpl('p', 'Vous êtes proche de perdre cette partie.', false, 'descr_rp') );
		else
			$this->add( new interf_bal_smpl('p', 'Vous avez perdu cette partie ! Votre adversaire rafle la mise.', false, 'descr_rp') );
		interf_alerte::aff_enregistres($this);
		if( abs($_SESSION['score']) >= 1 )
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Changer de jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Rejouer', $G_url->get('action', 'mise'), false, 'btn btn-default') );
		}
		else
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Arrêter le jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Continuer', $G_url->get('action', 'jouer'), false, 'btn btn-primary') );
		}
	}
	protected function incantation()
	{
		$this->add( new interf_bal_smpl('p', 'Vous faites fasse à votre adversaire. Entre vous, à égales distances, se trouve un gobelet. Le but est de pousser le gobelet jusqu\'à ce qu\'il touche l\'autre joueur, sans le toucher !', false, 'descr_rp') );
		$this->opposition();
	}
	protected function esquive($score, $score_adv)
	{
		$this->add( new interf_bal_smpl('p', 'Vous devez retirer votre main avant que votre adversaire ne la touche. Le gagnant est le premier à réussir alors que l\'autre a raté.', false, 'descr_rp') );
		global $G_url;
		$div = $this->add( new interf_bal_cont('div', false, 'descr_rp') );
		if( $score )
			$div->add( new interf_bal_smpl('p', 'Vous avez esquivé votre adversaire.') );
		else
			$div->add( new interf_bal_smpl('p', 'Vous n\'avez pas esquivé votre adversaire.') );
		if( $score_adv )
			$div->add( new interf_bal_smpl('p', 'Votre adversaire vous a esquivé.') );
		else
			$div->add( new interf_bal_smpl('p', 'Votre adversaire ne vous a pas esquivé.') );
		$this->premier_gagnant();
	}
	protected function premier_gagnant()
	{
		global $G_url;
		interf_alerte::aff_enregistres($this);
		$liste = $this->add( new interf_bal_cont('ul') );
		$li1 = $liste->add( new interf_bal_cont('li') );
		$li1->add( new interf_bal_smpl('strong', 'Votre score : ') );
		$li1->add( new interf_bal_smpl('span', $_SESSION['score'], 'score') );
		$li2 = $liste->add( new interf_bal_cont('li') );
		$li2->add( new interf_bal_smpl('strong', 'Score de votre adversaire : ') );
		$li2->add( new interf_bal_smpl('span', $_SESSION['score_adv'], 'score_adv') );
		$li3 = $liste->add( new interf_bal_cont('li') );
		
		if( $_SESSION['score'] != $_SESSION['score_adv'] )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous gnagnez la partie et remportez '.($_SESSION['mise']*2).' stars.', false, 'descr_rp') );
			else
				$this->add( new interf_bal_smpl('p', 'Vous perdez la partie, votre adversaire rafle la mise.', false, 'descr_rp') );
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Changer de jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Rejouer', $G_url->get('action', 'mise'), false, 'btn btn-default') );
		}
		else
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Arrêter le jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Continuer', $G_url->get('action', 'jouer'), false, 'btn btn-primary') );
		}
	}
	protected function blocage($score, $score_adv)
	{
		$this->add( new interf_bal_smpl('p', 'Chaque joueur lance une petite balle sur l\'autre qui doit l\'attraper avec un gobelet. Le premier à l\'attraper alors que l\'autre rate a gagné.', false, 'descr_rp') );
		global $G_url;
		$div = $this->add( new interf_bal_cont('div', false, 'descr_rp') );
		if( $score )
			$div->add( new interf_bal_smpl('p', 'Vous avez attrapé la balle.') );
		else
			$div->add( new interf_bal_smpl('p', 'Vous n\'avez pas attrapé la balle.') );
		if( $score_adv )
			$div->add( new interf_bal_smpl('p', 'Votre adversaire a attrapé la balle.') );
		else
			$div->add( new interf_bal_smpl('p', 'Votre adversaire n\'a pas attrapé la balle.') );
		$this->premier_gagnant();
	}
	protected function dressage($score, $score_adv)
	{
		$this->add( new interf_bal_smpl('p', 'Le jeu se déroule en cinq étapes. Chacune correspond à un tour que vous devez faire faire à un petit animal que vous avez dressé ou une épreuve que vous devez lui faire passer. Votre adversaire doit en faire de même. Chaque épreuve est noté à l\'applaudimètre.', false, 'descr_rp') );
		$this->note($score, $score_adv);
	}
	protected function note($score, $score_adv)
	{
		global $G_url;
		$div = $this->add( new interf_bal_cont('div', false, 'descr_rp') );
		$div->add( new interf_bal_smpl('p', 'Votre prestation reçoit la note de '.$score) );
		$div->add( new interf_bal_smpl('p', 'La prestation de votre adversaire reçoit la note de '.$score_adv) );
		interf_alerte::aff_enregistres($this);
		$liste = $this->add( new interf_bal_cont('ul') );
		$li1 = $liste->add( new interf_bal_cont('li') );
		$li1->add( new interf_bal_smpl('strong', 'Votre score : ') );
		$li1->add( new interf_bal_smpl('span', $_SESSION['score'], 'score') );
		$li2 = $liste->add( new interf_bal_cont('li') );
		$li2->add( new interf_bal_smpl('strong', 'Score de votre adversaire : ') );
		$li2->add( new interf_bal_smpl('span', $_SESSION['score_adv'], 'score_adv') );
		$li3 = $liste->add( new interf_bal_cont('li') );
		$li3->add( new interf_bal_smpl('strong', 'Passe : ') );
		$li3->add( new interf_bal_smpl('span', $_SESSION['passe'], 'passe') );
		$li3->add( new interf_txt(' / 5') );
		if( $_SESSION['passe'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous gnagnez la partie et remportez '.($_SESSION['mise']*2).' stars.', false, 'descr_rp') );
			else if( $_SESSION['score'] < $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous perdez la partie, votre adversaire rafle la mise.', false, 'descr_rp') );
			else
				$this->add( new interf_bal_smpl('p', 'Match nul, vous récupérez votre mise.', false, 'descr_rp') );
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Changer de jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Rejouer', $G_url->get('action', 'mise'), false, 'btn btn-default') );
		}
		else
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Arrêter le jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Continuer', $G_url->get('action', 'jouer'), false, 'btn btn-primary') );
		}
	}
	protected function sort_element($score, $score_adv)
	{
		$this->add( new interf_bal_smpl('p', 'Le jeu se déroule près de la cheminée de la taverne. À chacune des cinq épreuves vous devez utiliser la magie pour manipuler les flammes afin de créer un spectacle sur un thème donné. Votre adversaire doit en faire de même. Chaque épreuve est noté à l\'applaudimètre.', false, 'descr_rp') );
		$this->note($score);
	}
	protected function sort_vie($score)
	{
		$this->add( new interf_bal_smpl('p', 'Le jeu se déroule en cinq épreuve. À chacune on apporte à votrre adversaire et vous une plante ou un bout de plante dans un état plus ou moins bon. Le but est d\'utiliser votre magie pour faire ce dévelloper cette plante. Celui qui a le meilleur résultat gagne l\'épreuve.', false, 'descr_rp') );
		$this->binaire($score);
	}
	protected function sort_mort($score)
	{
		$this->add( new interf_bal_smpl('p', 'Le jeu se déroule en cinq épreuve. À chacune vous et votre adversaire devez animer des osselets et leur faire réaliser un certaine tâche (en opposition ou en parallèle). Celui qui réussi le mieux gagne l\'épreuve.', false, 'descr_rp') );
		$this->binaire($score);
	}
	protected function binaire($score)
	{
		global $G_url;
		$div = $this->add( new interf_bal_cont('div', false, 'descr_rp') );
		if($score)
			$div->add( new interf_bal_smpl('p', 'Votre prestation est la plus appréciée.') );
		else
			$div->add( new interf_bal_smpl('p', 'La prestation de votre adversaire est la plus appréciée.') );
		interf_alerte::aff_enregistres($this);
		$liste = $this->add( new interf_bal_cont('ul') );
		$li1 = $liste->add( new interf_bal_cont('li') );
		$li1->add( new interf_bal_smpl('strong', 'Votre score : ') );
		$li1->add( new interf_bal_smpl('span', $_SESSION['score'], 'score') );
		$li2 = $liste->add( new interf_bal_cont('li') );
		$li2->add( new interf_bal_smpl('strong', 'Score de votre adversaire : ') );
		$li2->add( new interf_bal_smpl('span', $_SESSION['score_adv'], 'score_adv') );
		$li3 = $liste->add( new interf_bal_cont('li') );
		$li3->add( new interf_bal_smpl('strong', 'Passe : ') );
		$li3->add( new interf_bal_smpl('span', $_SESSION['passe'], 'passe') );
		$li3->add( new interf_txt(' / 5') );
		if( $_SESSION['passe'] == 5 )
		{
			if( $_SESSION['score'] > $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous gnagnez la partie et remportez '.($_SESSION['mise']*2).' stars.', false, 'descr_rp') );
			else if( $_SESSION['score'] < $_SESSION['score_adv'] )
				$this->add( new interf_bal_smpl('p', 'Vous perdez la partie, votre adversaire rafle la mise.', false, 'descr_rp') );
			else
				$this->add( new interf_bal_smpl('p', 'Match nul, vous récupérez votre mise.', false, 'descr_rp') );
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Changer de jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Rejouer', $G_url->get('action', 'mise'), false, 'btn btn-default') );
		}
		else
		{
			$btns = $this->add( new interf_bal_cont('div', 'jeux', 'btn-group') );
			$btns->add( new interf_lien('Arrêter le jeu', $G_url->get('action', 'jeux'), false, 'btn btn-default') );
			$btns->add( new interf_lien('Continuer', $G_url->get('action', 'jouer'), false, 'btn btn-primary') );
		}
	}
}

