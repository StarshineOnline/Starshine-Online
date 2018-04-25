<?php
/**
 * @file interf_classements.class.php
 * Affichage des classements
 */
 
/**
 * classe gérant l'affichage des classements
 */
class interf_classements extends interf_onglets
{
	function __construct($categorie, $type)
	{
		global $G_url;
		parent::__construct('ongl_class', 'classements');
		$url = $G_url->copie('ajax', 2);
		$roy = $this->add_onglet('Royaumes', $url->get('categorie', 'royaumes'), 'ongl_royaumes', 'invent', $categorie=='royaumes');
		$grp = $this->add_onglet('Groupes', $url->get('categorie', 'groupes'), 'ongl_groupes', 'invent', $categorie=='groupes');
		$perso_roy = $this->add_onglet('Personnages − royaume', $url->get('categorie', 'perso_race'), 'ongl_perso_race', 'invent', $categorie=='perso_race');
		$perso_tous = $this->add_onglet('Personnages − global', $url->get('categorie', 'perso_tous'), 'ongl_perso_tous', 'invent', $categorie=='perso_tous');
		
		$G_url->add('categorie', $categorie);
		switch($categorie)
		{
		case 'royaumes':
			$roy->add( new interf_classement_royaumes($type) );
			break;
		case 'groupes':
			$grp->add( new interf_classement_groupes($type) );
			break;
		case 'perso_race':
			$perso_roy->add( new interf_classement_perso_race($type) );
			break;
		case 'perso_tous':
			$perso_tous->add( new interf_classement_perso_tous($type) );
			break;
		}
	}
}

class interf_classement_royaumes extends interf_pills
{
	protected $pops=array();
	protected $type;
	function __construct($type)
	{	
		global $G_url, $db;
		parent::__construct('class_royaumes', 'classement');
		$this->type = $type ? $type : 'victoire';
		// Liste des classements royaume
		$this->add_elt('Victoires', $G_url->get('type', 'victoire'), $this->type=='victoire');
		$this->add_elt('Cases', $G_url->get('type', 'cases'), $this->type=='cases');
		$this->add_elt('Niveaux', $G_url->get('type', 'level'), $this->type=='level');
		$this->add_elt('Honneur', $G_url->get('type', 'honneur'), $this->type=='honneur');
		$this->add_elt('PvP', $G_url->get('type', 'frag'), $this->type=='frag');
		$this->add_elt('Suicide', $G_url->get('type', 'mort'), $this->type=='mort');
		$this->add_elt('Crime', $G_url->get('type', 'crime'), $this->type=='crime');
		
		// Population des royaumes
		/// @todo passer à l'objet
	  $requete = "SELECT COUNT(*) as tot FROM perso WHERE statut = 'actif' AND level > 0 GROUP BY race";
	  $req = $db->query($requete);
	  while($row = $db->read_assoc($req))
	  {
	  	$this->pops[$row['race']] = $row['tot']>0 ? $row['tot'] : 1;
	  }
  
		switch($this->type)
		{
	  case 'victoire':
	    $requete = "SELECT race, point_victoire_total as tot FROM `royaume` WHERE ID <> 0 ORDER BY tot DESC";
	    break;
	  case 'cases':
	    $requete = "SELECT COUNT(*) as tot, race FROM `map` LEFT JOIN royaume ON royaume.id = map.royaume WHERE royaume <> 0 AND x <= 190 AND y <= 190 GROUP BY royaume ORDER BY tot DESC";
	    break;
	  default:
	    $requete = "SELECT SUM($this->type) as tot, race FROM perso WHERE statut = 'actif' and level > 0 GROUP BY race ORDER BY tot DESC";
	    break;
	  }
		$req = $db->query($requete);
		$this->aff_tableau($req);
	}
	function aff_tableau($req)
	{
		global $Gtrad, $db;
		
		// Race du personnage
		$race_perso = joueur::get_perso()->get_race();
		
		// Tableau et en-tête
		$tbl = $this->get_contenu()->add( new interf_data_tbl('tbl_royaumes', '', false, false, false, 0) );
		$tbl->nouv_cell('#');
		$tbl->nouv_cell('Nom');
		$tbl->nouv_cell('Valeur');
		$tbl->nouv_cell('Moyenne');
		
		// Corps du tableau
		$i = 1;
		while( $row = $db->read_array($req) )
		{
			$tbl->nouv_ligne(false, $row['race']==$race_perso ? 'info' : false);
			$tbl->nouv_cell( $i );
			$tbl->nouv_cell( $Gtrad[$row['race']] );
			$tbl->nouv_cell( number_format($row['tot'], 0, ',', ' ') );
			$tbl->nouv_cell( $tbl->pop[$row['race']] ? number_format($row['tot'] / $tbl->pop[$row['race']], 0, ',', ' ') : '0' );
			$i++;
		}
	}
}

class interf_classement_groupes extends interf_pills
{
	function __construct($type)
	{	
		global $G_url, $db;
		parent::__construct('class_groupes', 'classement');
		if(!$type)
			$type = 'honneur';
		$this->add_elt('Honneur', $G_url->get('type', 'honneur'), $type=='honneur');
		$this->add_elt('Expérience', $G_url->get('type', 'exp'), $type=='exp');
		$this->add_elt('Stars', $G_url->get('type', 'star'), $type=='star');
		$this->add_elt('PvP', $G_url->get('type', 'frag'), $type=='frag');
		$this->add_elt('Crime', $G_url->get('type', 'crime'), $type=='crime');
		$this->add_elt('Survie', $G_url->get('type', 'survie'), $type=='survie');
		$this->add_elt('Artisanat', $G_url->get('type', 'artisanat'), $type=='artisanat');
		$this->add_elt('HP', $G_url->get('type', 'hp'), $type=='hp');
		$this->add_elt('MP', $G_url->get('type', 'mp'), $type=='mp');
		
		switch($type)
		{
	  case 'artisanat':
	    $requete = 'SELECT SUM(ROUND(SQRT(architecture + alchimie + forge + identification)*10)) as val, COUNT(*) AS nbr, groupe.nom, groupe.id FROM perso INNER JOIN groupe ON groupe.id = perso.groupe WHERE statut = "actif" and level > 0 GROUP BY groupe.id ORDER BY val DESC';
	    break;
	  default:
	    $requete = 'SELECT SUM('.$type.') as val, COUNT(*) AS nbr, groupe.nom, groupe.id FROM perso INNER JOIN groupe ON groupe.id = perso.groupe WHERE statut = "actif" and level > 0 GROUP BY perso.groupe ORDER BY val DESC';
	    break;
	  }
		$req = $db->query($requete);
		
		// Tableau et en-tête
		/// @todo rétablir la recherche
		$tbl = $this->get_contenu()->add( new interf_data_tbl('tbl_groupes', '', true, false) );
		$tbl->nouv_cell('#');
		$tbl->nouv_cell('Nom');
		$tbl->nouv_cell('Valeur');
		$tbl->nouv_cell('Moyenne');
		
		// Corps du tableau
		$groupe = joueur::get_perso()->get_id_groupe();
		$i = 1;
		while( $row = $db->read_array($req) )
		{
			$tbl->nouv_ligne(false, $row['id']==$groupe ? 'info' : false);
			$tbl->nouv_cell( $i );
			$tbl->nouv_cell( $row['nom'] );
			$tbl->nouv_cell( number_format($row['val'], 0, ',', ' ') );
			$tbl->nouv_cell( number_format($row['val']/$row['nbr'], 0, ',', ' ') );
			$i++;
		}
	}
}

class interf_classement_perso_tous extends interf_pills
{
	const id = 'perso_tous';
	function __construct($type)
	{	
		global $G_url, $db, $Tclasse;
		parent::__construct('class_'.static::id, 'classement');
		if(!$type)
			$type = 'honneur';
		$this->add_elt('Honneur', $G_url->get('type', 'honneur'), $type=='honneur');
		$this->add_elt('Réputation', $G_url->get('type', 'reputation'), $type=='reputation');
		$this->add_elt('Expérience', $G_url->get('type', 'exp'), $type=='exp');
		$this->add_elt('Stars', $G_url->get('type', 'star'), $type=='star');
		$this->add_elt('PvP', $G_url->get('type', 'frag'), $type=='frag');
		$this->add_elt('Suicide', $G_url->get('type', 'mort'), $type=='mort');
		$this->add_elt('Crime', $G_url->get('type', 'crime'), $type=='crime');
		$this->add_elt('Achievements', $G_url->get('type', 'achiev'), $type=='achiev');
		$this->add_elt('Mêlée', $G_url->get('type', 'melee'), $type=='melee', 'Physique');
		$this->add_elt('Esquive', $G_url->get('type', 'esquive'), $type=='esquive', 'Physique');
		$this->add_elt('Blocage', $G_url->get('type', 'blocage'), $type=='blocage', 'Physique');
		$this->add_elt('Distance', $G_url->get('type', 'distance'), $type=='distance', 'Physique');
		$this->add_elt('Incantation', $G_url->get('type', 'incantation'), $type=='incantation', 'Magie');
		$this->add_elt('Magie élémentaire', $G_url->get('type', 'sort_element'), $type=='sort_element', 'Magie');
		$this->add_elt('Nécromancie', $G_url->get('type', 'sort_mort'), $type=='sort_mort', 'Magie');
		$this->add_elt('Magie de la vie', $G_url->get('type', 'sort_vie'), $type=='sort_vie', 'Magie');
		$this->add_elt('Dressage', $G_url->get('type', 'dressage'), $type=='dressage');
		$this->add_elt('Survie', $G_url->get('type', 'survie'), $type=='survie');
		$this->add_elt('Artisanat', $G_url->get('type', 'artisanat'), $type=='artisanat');
		
		switch($type)
		{
	  case 'exp':
	    $requete = 'SELECT exp as val, level, nom, id, race, classe, cache_classe, cache_stat, cache_niveau FROM perso WHERE statut = "actif" and level > 0 ORDER BY val DESC';
	    break;
	  case 'achiev':
	    $requete = 'SELECT COUNT(*) as val, perso.nom, perso.id, race, classe, cache_classe, cache_stat, cache_niveau FROM achievement INNER JOIN perso ON perso.id = achievement.id_perso WHERE statut = "actif" and level > 0 GROUP BY achievement.id_perso ORDER BY val DESC';
	    break;
	  case 'artisanat':
	    $requete = 'SELECT ROUND(SQRT((architecture + alchimie + forge + identification)*10)) as val, nom, id, race, classe, cache_classe, cache_stat, cache_niveau FROM perso WHERE statut = "actif" and level > 0 ORDER BY val DESC';
	    break;
	  default:
	    $requete = 'SELECT '.$type.' as val, nom, id, race, classe, cache_classe, cache_stat, cache_niveau FROM perso WHERE statut = "actif" and level > 0 ORDER BY val DESC';
	    break;
	  }
		$req = $db->query($requete);
		
		// Tableau et en-tête
		/// @todo rétablir la recherche
		$tbl = $this->get_contenu()->add( new interf_data_tbl('tbl_'.static::id, '', true, false) );
		$tbl->nouv_cell('#');
		$tbl->nouv_cell('&nbsp;');
		$tbl->nouv_cell('Nom');
		$tbl->nouv_cell('Valeur');
		if( $type == 'exp' )
		{
			$tbl->nouv_cell('Niveau');
		}
		
		// Corps du tableau
		$id = joueur::get_perso()->get_id();
		$race = joueur::get_perso()->get_race();
		$i = 1;
		while( $row = $db->read_array($req) )
		{
			$tbl->nouv_ligne(false, $row['id']==$id ? 'info' : false);
			$tbl->nouv_cell( $i );
			switch($type)
			{
			case 'exp':
				$cache = max($row['cache_stat'], $row['cache_niveau']);
				break;
			case 'crime':
				$cache = 0;
				break;
			default:
				$cache = $row['cache_stat'];
			}
			if( $cache == 2 || ($cache == 1 && $row['race']!=$race) )
			{
				$tbl->nouv_cell('&nbsp;');
				$tbl->nouv_cell( 'XXX' );
				$tbl->nouv_cell( 'X' );
				if( $type == 'exp' )
					$tbl->nouv_cell( 'X' );
			}
			else
			{
				if( $row['cache_classe']  )
				{
					$tbl->nouv_cell('&nbsp;');
					$tbl->nouv_cell( $row['nom'] );
				}
				else
				{
					$img = 'image/personnage/'.$row['race'].'/'.$row['race'].'_'.$Tclasse[$row['classe']]['type'].'.png';
					//$tbl->nouv_cell( new interf_img($img, $row['race'].' '.$row['classe']) )->add( new interf_txt($row['nom']) );
					$tbl->nouv_cell( new interf_img($img, $row['race'].' '.$row['classe']) );
					$tbl->nouv_cell( $row['nom'] );
				}
				$tbl->nouv_cell( number_format($row['val'], 0, ',', ' ') );
				if( $type == 'exp' )
					$tbl->nouv_cell( number_format($row['level'], 0, ',', ' ') );
			}
			$i++;
		}
	}
}

class interf_classement_perso_race extends interf_classement_perso_tous
{
	const id = 'perso_race';
	/*function __construct($type)
	{	
		global $G_url;
		parent::__construct(false, false, false, 'classement');
	}*/
}

?>