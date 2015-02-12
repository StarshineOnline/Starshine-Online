<?php
/**
 * @file interf_alchimiste.class.php
 * Classes pour l'interface de l'alchimiste
 */
 
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'alchimiste
class interf_alchimiste extends interf_ville_onglets
{
	function __construct(&$royaume, &$case, $onglet)
	{
		global $db;
		parent::__construct($royaume, $case);
		
		// Icone
		$this->icone = $this->set_icone_centre('alchimie');
		$this->icone->set_tooltip('Alchimiste');	
		
		// Nombre de recettes débloquées
		$requete = 'SELECT COUNT(*) AS nbr FROM craft_recette WHERE royaume_alchimie < '.$royaume->get_alchimie();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$nbr_rec = $row['nbr'];
		// Nombre de recettes débloquables
		$requete = 'SELECT COUNT(*) AS nbr FROM craft_recette WHERE royaume_alchimie < 99999999';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$nbr_rec_tot = $row['nbr'];
		// Nombre de recettes connues par le personnages parmi celles disponibles
		/// @todo passer à l'objet
		$requete = "SELECT COUNT(*) AS nbr FROM perso_recette AS pr INNER JOIN craft_recette AS cr ON pr.id_recette = cr.id WHERE id_perso = ".joueur::get_perso()->get_id().' AND cr.royaume_alchimie < '.$royaume->get_alchimie();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$nbr_rec_connues = $row['nbr'];
		// Jauges
		$this->set_jauge_ext($nbr_rec, $nbr_rec_tot, 'avance', 'Recettes débloquées : ');
		$this->set_jauge_int($nbr_rec_connues, $nbr_rec, 'avance', 'Recettes débloquées : ');	
		
		// Onglets
		$this->onglets->add_onglet('Recherches', '', 'tab_recherche', 'ecole_mag', $onglet=='recherche');
		$this->onglets->add_onglet('Consommables', 'alchimiste.php?categorie=objet&ajax=2', 'tab_objet', 'ecole_mag', $onglet=='objet');
		$this->onglets->add_onglet('Recettes', 'alchimiste.php?categorie=recette&ajax=2', 'tab_recette', 'ecole_mag', $onglet=='recette');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$onglet) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$onglet.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->aff_recherche($royaume);
		switch($tab)
		{
		case 'objet':
			$this->onglets->get_onglet('tab_'.$onglet)->add( new interf_achat_alchimie($royaume, $n) );
			break;
		case 'recette':
			$this->onglets->get_onglet('tab_'.$onglet)->add( new interf_achat_recette($royaume, $n) );
			break;
		}
	}
	
	function aff_recherche(&$royaume)
	{
		global $db;
		$requete = "SELECT royaume_alchimie FROM craft_recette WHERE royaume_alchimie < ".$royaume->get_alchimie()." ORDER BY royaume_alchimie DESC LIMIT 0, 1";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$min = $row['royaume_alchimie'];
		$requete = "SELECT royaume_alchimie FROM craft_recette WHERE royaume_alchimie < 99999999 AND royaume_alchimie > ".$royaume->get_alchimie()." ORDER BY royaume_alchimie ASC LIMIT 0, 1";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$max = $row['royaume_alchimie'];
		$onglet = $this->onglets->get_onglet('tab_recherche');
		if( $max )
		{
			$total = $max - $min;
			$actuel = $royaume->get_alchimie() - $min;
			$pourcent = round($actuel / $total * 100, 2);
			$onglet->add( new interf_bal_smpl('p', $pourcent.'% du déblocage de la prochaine recette !') );
		}
		else
		{
			$onglet->add( new interf_bal_smpl('p', 'Il n\'y a plus de recette à débloquer. Mais vous pouvez tout de même continuer à effectuer des recherches pour vous entrainer.') );
		}
		$onglet->add( new interf_lien('Faire des recherches en alchimie (10 PA).', 'alchimiste.php?action=recherche', false, 'btn btn-default') );
	} 
}

/// Classe de base pour les listes d'objet d'alchimie
class interf_achat_alchimie extends interf_liste_achat
{
	const url='alchimiste.php';
	function __construct(&$royaume, $nbr_alertes=0)
	{
		global $db;
		$this->categorie = 'objet';
		$objets = objet::create(null, null, 'prix ASC', false, 'achetable = "y"');
		parent::__construct($royaume, 'tbl_objet', $objets, $nbr_alertes);
	}
	function aff_titres_col()
	{
	}
	
	function aff_cont_col(&$elt)
	{
	}
}

/// Classe de base pour les listes de recettes d'alchimie
class interf_achat_recette extends interf_liste_achat
{
	const url='alchimiste.php';
	function __construct(&$royaume, $nbr_alertes=0)
	{
		global $db;
		$this->categorie = 'recette';
		$recettes = alchimie_recette::create(null, null, 'royaume_alchimie ASC', false, 'royaume_alchimie < '.$royaume->get_alchimie());
		parent::__construct($royaume, 'tbl_recette', $recettes, $nbr_alertes);
	}
	function aff_titres_col()
	{
	}
	function aff_cont_col(&$elt)
	{
	}
	protected function peut_acheter(&$elt)
	{
		if( perso_recette::recov($this->perso->get_id(), $elt->get_id()) )
			return null;
		else
			return parent::peut_acheter($elt);
	}
}

?>