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
	
	function recherche_batiment($batiment, $niv=true)
	{
		global $db;
		if( $niv )
		{
			$requete = 'SELECT MAX(level) as niv_max FROM batiment_ville WHERE type = "'.$batiment.'"';
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$niv_max = $row['niv_max'];
		}
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
		if( $niv )
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

/// Classe gérant l'interface du forgeron
class interf_forgeron extends interf_ecole_mag
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('forge');
		$niveau = $this->recherche_batiment('forgeron');
		
		// Onglets
		$this->onglets->add_onglet('Épées', 'boutique.php?type=arme&ajax=2&categorie=epee', 'tab_epee', 'ecole_mag', $categorie=='epee');
		$this->onglets->add_onglet('Haches', 'boutique.php?type=arme&ajax=2&categorie=hache', 'tab_hache', 'ecole_mag', $categorie=='hache');
		$this->onglets->add_onglet('Dagues', 'boutique.php?type=arme&ajax=2&categorie=dague', 'tab_dague', 'ecole_mag', $categorie=='dague');
		$this->onglets->add_onglet('Arcs', 'boutique.php?type=arme&ajax=2&categorie=arc', 'tab_arc', 'ecole_mag', $categorie=='arc');
		$this->onglets->add_onglet('Boucliers', 'boutique.php?type=arme&ajax=2&categorie=bouclier', 'tab_bouclier', 'ecole_mag', $categorie=='bouclier');
		$this->onglets->add_onglet('Bâtons', 'boutique.php?type=arme&ajax=2&categorie=baton', 'tab_baton', 'ecole_mag', $categorie=='baton');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_arme($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe gérant l'interface de l'armurerie
class interf_armurerie extends interf_ecole_mag
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('casque');
		$niveau = $this->recherche_batiment('armurerie');
		
		// Onglets
		$this->onglets->add_onglet('Torse', 'boutique.php?type=armure&ajax=2&categorie=torse', 'tab_torse', 'ecole_mag', $categorie=='torse');
		$this->onglets->add_onglet('Tête', 'boutique.php?type=armure&ajax=2&categorie=tete', 'tab_tete', 'ecole_mag', $categorie=='tete');
		$this->onglets->add_onglet('Jambe', 'boutique.php?type=armure&ajax=2&categorie=jambe', 'tab_jambe', 'ecole_mag', $categorie=='jambe');
		$this->onglets->add_onglet('Taille', 'boutique.php?type=armure&ajax=2&categorie=ceinture', 'tab_ceinture', 'ecole_mag', $categorie=='ceinture');
		$this->onglets->add_onglet('Main', 'boutique.php?type=armure&ajax=2&categorie=main', 'tab_main', 'ecole_mag', $categorie=='main');
		$this->onglets->add_onglet('Pieds', 'boutique.php?type=armure&ajax=2&categorie=chaussure', 'tab_chaussure', 'ecole_mag', $categorie=='chaussure');
		$this->onglets->add_onglet('Dos', 'boutique.php?type=armure&ajax=2&categorie=dos', 'tab_dos', 'ecole_mag', $categorie=='dos');
		$this->onglets->add_onglet('Cou', 'boutique.php?type=armure&ajax=2&categorie=cou', 'tab_cou', 'ecole_mag', $categorie=='cou');
		$this->onglets->add_onglet('Doigt', 'boutique.php?type=armure&ajax=2&categorie=doigt', 'tab_doigt', 'ecole_mag', $categorie=='doigt');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_armure($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe gérant l'interface de l'enchanteur
class interf_enchanteur extends interf_ecole_mag
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('diament');
		//$niveau = $this->recherche_batiment('enchanteur');
		
		// Onglets
		$this->onglets->add_onglet('Grands accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=grand', 'tab_grand', 'ecole_mag', $categorie=='grand');
		$this->onglets->add_onglet('Moyens accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=moyen', 'tab_moyen', 'ecole_mag', $categorie=='moyen');
		$this->onglets->add_onglet('Petits accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=petit', 'tab_petit', 'ecole_mag', $categorie=='petit');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_accessoire($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe gérant l'interface du dresseur
class interf_dresseur extends interf_ecole_mag
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('faucon');
		//$niveau = $this->recherche_batiment('dresseur');
		
		// Onglets
		$this->onglets->add_onglet('Cou', 'boutique.php?type=dressage&ajax=2&categorie=cou', 'tab_cou', 'ecole_mag', $categorie=='cou');
		$this->onglets->add_onglet('Selle', 'boutique.php?type=dressage&ajax=2&categorie=selle', 'tab_selle', 'ecole_mag', $categorie=='selle');
		$this->onglets->add_onglet('Dos', 'boutique.php?type=dressage&ajax=2&categorie=dos', 'tab_dos', 'ecole_mag', $categorie=='dos');
		$this->onglets->add_onglet('Arme', 'boutique.php?type=dressage&ajax=2&categorie=arme', 'tab_arme', 'ecole_mag', $categorie=='arme');
		$this->onglets->add_onglet('Torse', 'boutique.php?type=dressage&ajax=2&categorie=torse', 'tab_torse', 'ecole_mag', $categorie=='torse');
		$this->onglets->add_onglet('Pattes', 'boutique.php?type=dressage&ajax=2&categorie=pattes', 'tab_pattes', 'ecole_mag', $categorie=='pattes');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_dressage($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe gérant l'interface de l'alchimiste
class interf_alchimiste extends interf_ecole_mag
{
	function __construct(&$royaume, $onglet)
	{
		global $db;
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('alchimie');
		//$this->recherche_batiment('alchimiste');
		
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
		$this->set_jauge_int($nbr_rec, $nbr_rec_tot, 'avance', 'Recettes débloquées : ');
		
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


?>