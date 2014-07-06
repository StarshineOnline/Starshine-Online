<?php
/**
 * @file interf_ecole_mag.class.php
 * Classes pour l'interface des écoles et magasins
 */
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe de base pour l'interface des écoles et magasins
class interf_ecole_mag extends interf_ville
{
	protected $onglets;
	protected $icone = null;
	protected $perso;
	function __construct(&$royaume)
	{
		parent::__construct($royaume);
		$this->perso = joueur::get_perso();
		$this->onglets = $this->centre->add( new interf_onglets('tab_ecole_mag') );
	}
	
	function recherche_batiment($batiment)
	{
		global $db;
		$requete = 'SELECT MAX(level) as niv_max FROM batiment_ville WHERE type = "'.$batiment.'"';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$niv_max = $row['niv_max'];
		///TODO: à améliorer
		$requete = 'SELECT level, statut, c.hp, b.hp AS hp_max, nom FROM construction_ville AS c LEFT JOIN batiment_ville AS b ON c.id_batiment = b.id WHERE b.type = "'.$batiment.'" AND c.id_royaume = '.$this->royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$niveau =  $row['level'];
		// Tooltip de l'icone
		if( $this->icone )
		{
			$this->icone->set_tooltip($row['nom']);
			$niv = 'Niveau : ';
		}
		else
			$niv = $row['nom'].' − niveau : ';
		// Jauges
		$this->set_jauge_ext($row['hp'], $row['hp_max'], 'hp', 'HP : ');
		$this->set_jauge_int($niveau, $niv_max, 'avance', $niv);
		// Si le batiment est inactif, on le met au niveau 1
		return $row['statut'] == 'inactif' ? 1 : $niveau;
	}
}

/// Classe gérant l'interface de l'école de magie
class interf_ecole_magie extends interf_ecole_mag
{
	function __construct(&$royaume, $type)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('sorts');
		$niveau = $this->recherche_batiment('ecole_magie');
		
		// Onglets
		$this->onglets->add_onglet('Sorts hors combat', 'ecole.php?type=sort_jeu&ajax=2', 'tab_sort_jeu', 'ecole_mag', $type=='sort_jeu');
		$this->onglets->add_onglet('Sorts de combat', 'ecole.php?type=sort_combat&ajax=2', 'tab_sort_combat', 'ecole_mag', $type=='sort_combat');
		if( true || !$this->perso->get_sort_element() || !$this->perso->get_sort_mort() || !$this->perso->get_sort_vie() )
			$this->onglets->add_onglet('Magies', 'ecole.php?type=sort_combat&ajax=2', 'tab_magie', 'ecole_mag', $type=='magie');
		
		// Filtres
		$haut = $this->onglets->get_haut();
		$li1 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magievie.png'), 'javascript:filtre_table(\'sort_vie\');', false, false, 'filtre') );
		$li2 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magiemort.png'), 'javascript:filtre_table(\'sort_mort\');', false, false, 'filtre') );
		$li3 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magieelementaire.png'), 'javascript:filtre_table(\'sort_element\');', false, false, 'filtre') );
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$type) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$type.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		// Contenu
		switch( $type )
		{
		case 'sort_jeu':
			$this->onglets->get_onglet('tab_sort_jeu')->add( new interf_achat_sort_jeu($royaume, $niveau, $n) );
			break; 
		case 'sort_combat':
			$this->onglets->get_onglet('tab_sort_combat')->add( new interf_achat_sort_combat($royaume, $niveau, $n) );
			break; 
		}
	}
}

/// Classe gérant l'interface de l'école de combat
class interf_ecole_combat extends interf_ecole_mag
{
	function __construct(&$royaume, $type)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('ecole-combat');
		$niveau = $this->recherche_batiment('ecole_combat');
		
		// Onglets
		$this->onglets->add_onglet('Comp. hors combat', 'ecole.php?type=comp_jeu&ajax=2', 'tab_comp_jeu', 'ecole_mag', $type=='comp_jeu');
		$this->onglets->add_onglet('Comp. de combat', 'ecole.php?type=comp_combat&ajax=2', 'tab_comp_combat', 'ecole_mag', $type=='comp_combat');
		
		// Filtres
		$haut = $this->onglets->get_haut();
		if( $type == 'comp_jeu' )
		{
			$li1 = $haut->add( new interf_elt_menu(new interf_img('image/dressage.png'), 'javascript:filtre_table(\'dressage\');', false, false, 'filtre') );
		}
		else
		{
			$li1 = $haut->add( new interf_elt_menu(new interf_img('image/blocage.png'), 'javascript:filtre_table(\'blocage\');', false, false, 'filtre') );
		}
		$li2 = $haut->add( new interf_elt_menu(new interf_img('image/distance.png'), 'javascript:filtre_table(\'distance\');', false, false, 'filtre') );
		$li3 = $haut->add( new interf_elt_menu(new interf_img('image/esquive.png'), 'javascript:filtre_table(\'esquive\');', false, false, 'filtre') );
		$li4 = $haut->add( new interf_elt_menu(new interf_img('image/melee.png'), 'javascript:filtre_table(\'melee\');', false, false, 'filtre') );
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$type) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$type.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		// Contenu
		switch( $type )
		{
		case 'comp_jeu':
			$this->onglets->get_onglet('tab_comp_jeu')->add( new interf_achat_comp_jeu($royaume, $niveau, $n) );
			break; 
		case 'comp_combat':
			$this->onglets->get_onglet('tab_comp_combat')->add( new interf_achat_comp_combat($royaume, $niveau, $n) );
			break; 
		}
	}
}

?>