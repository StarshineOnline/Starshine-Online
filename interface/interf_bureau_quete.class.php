<?php
/**
 * @file interf_bureau_quete.class.php
 * Classes pour l'interface du bureau des quetes
 */
 
include_once(root.'interface/interf_ville.class.php');

/// Classe gérant l'interface du bureau des quêtes
class interf_bureau_quete extends interf_ville_onglets
{
	function __construct(&$royaume, $case, $type='autre')
	{
		global $G_url;
		parent::__construct($royaume, $case);
		
		// Icone & jauges
		$this->icone = $this->set_icone_centre('quetes');
		$this->icone->set_tooltip('Bureau des quêtes');
		self::aff_jauges($this, $royaume);
		
		// Onglets
		$url = $G_url->copie('ajax', 2);
		$this->onglets->add_onglet('Plaine', $url->get('type', 'plaine'), 'tab_plaine', 'ecole_mag', $type=='plaine');
		$this->onglets->add_onglet('Forêt', $url->get('type', 'foret'), 'tab_foret', 'ecole_mag', $type=='foret');
		$this->onglets->add_onglet('Désert', $url->get('type', 'desert'), 'tab_desert', 'ecole_mag', $type=='desert');
		$this->onglets->add_onglet('Neige', $url->get('type', 'neige'), 'tab_neige', 'ecole_mag', $type=='neige');
		$this->onglets->add_onglet('Montagne', $url->get('type', 'montagne'), 'tab_montagne', 'ecole_mag', $type=='montagne');
		$this->onglets->add_onglet('Marais / TM', $url->get('type', 'marais'), 'tab_marais', 'ecole_mag', $type=='marais');
		$this->onglets->add_onglet('Autres', $url->get('type', 'autre'), 'tab_autre', 'ecole_mag', $type=='autre');
		
		$this->onglets->get_onglet('tab_'.$type)->add( new interf_tbl_quetes($royaume, $type) );
	}
	
	static function aff_jauges(&$interf, &$royaume)
	{
		global $db;
		/// @todo passer à l'objet
		// Jauge extérieure : nombre de quêtes achetées par le royaume
		$requete = 'SELECT COUNT(*) FROM quete AS q WHERE star_royaume > 0';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$nbr_achetable = $row[0];
		$requete = 'SELECT COUNT(*) FROM quete_royaume WHERE id_royaume = '.$royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$nbr_achete = $row[0];
		$interf->set_jauge_ext($nbr_achete, $nbr_achetable, 'avance', 'Nombre de quêtes disponibles : ');
		// Jauge intétieure : nombre de quêtes prise par le personnage
		$requete = 'SELECT COUNT(*) FROM quete_perso as qp INNER JOIN quete_royaume AS qr ON qr.id_quete = qp.id_quete WHERE qr.id_royaume = '.$royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$nbr_prise = $row[0];
		$interf->set_jauge_int($nbr_prise, $nbr_achete, 'pa', 'Nombre de quêtes prises : ');
	}
}

/// Classe affichant les quêtes à prendre au bureau des quêtes
class interf_tbl_quetes extends interf_data_tbl
{
	protected $perso;
	function __construct(&$royaume, $type, $fournisseur='bureau_quete')
	{
		global $db;
		parent::__construct('tbl_'.$type, '', false, false, $fournisseur=='bureau_quete'?358:383 );
		$this->perso = &joueur::get_perso();
		
		$this->nouv_cell('Nom de la quete');
		$this->nouv_cell('Type de quete');
		$this->nouv_cell('Repetable');
		
		$quetes = quete::get_quetes_dispos($this->perso, $royaume, $fournisseur, $type);
		foreach($quetes as $quete)
		{				
			$this->nouv_ligne();
			$this->nouv_cell(new interf_lien($quete->get_nom(), 'bureau_quete.php?action=description&id='.$quete->get_id()));
			$this->nouv_cell($quete->get_type());
			$this->nouv_cell($quete->get_repetable());
		}
		// Lien pour prendre toutes les quêtes
		if( $fournisseur == 'bureau_quete' )
			$this->add( new interf_lien('Prendre toutes les quêtes', 'bureau_quete.php?action=prendre_tout', false, 'offre_achat') );
	}
		
	// on formate la description de la quete pour l'affichage
	function get_description(&$quete)
	{
		global $db;
		
		$this->centre->add( new interf_bal_smpl('h3', 'Bureau des quêtes') );
	}
}

/// Classe gérant l'interface du bureau des quêtes
class interf_bureau_quete_descr extends interf_ville
{
	function __construct($quete, $royaume)
	{
		global $G_url;
		parent::__construct($royaume);
		$etape = quete_etape::create(array('id_quete', 'etape', 'variante'), array($quete->get_id(), 1, 0))[0];
		
		// Icone & jauges
		$this->icone = $this->set_icone_centre('quetes', 'bureau_quete.php');
		$this->icone->set_tooltip('Bureau des quêtes');
		interf_bureau_quete::aff_jauges($this, $royaume);
		
		// Nom de la quête
		$this->centre->add( new interf_bal_smpl('p', $quete->get_nom(), 'nom_quete') );
		// Description & information
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ', 'reduit') );
		include_once(root.'interface/interf_quetes.class.php');
		$div->add( new interf_descr_quete($quete, $etape) );
		// Lien pour prendre la quête
		$this->centre->add( new interf_lien('Prendre', $G_url->get(array('action'=>'prendre', 'id'=>$quete->get_id())), 'ville_bas') );
	}
}
					
					
