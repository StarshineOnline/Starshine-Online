<?php
/**
 * @file interf_ville.class.php
 * Classe de base pour l'interface des bâtiments en ville 
 */

/// Classe de base pour l'interface des bâtiments en ville 
class interf_ville extends interf_gauche
{
	protected $royaume;
	protected $icone = null;
	protected $perso;
	
	function __construct(&$royaume, &$case=null)
	{
		$this->royaume = &$royaume;
		$this->perso = joueur::get_perso();
		parent::__construct('carte');
		if( $case && !$case->is_ville() )
		{
			$construction = construction::create(array('x', 'y'), array($this->perso->get_x(),$this->perso->get_y()));
			$batiment = $construction[0]->get_def();
		}
		else
			$batiment = null;

		// Menu ville
		$menu = $this->barre_haut->add( new interf_menu(false, 'menu_ville', false) );
		if( $batiment )
		{
			$quetes = $menu->add( new interf_elt_menu('', 'bureau_quete.php', 'return charger(this.href);') );
			$quetes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-quetes') );
			$quetes->get_lien()->add( new interf_txt('Quêtes') );
			if($batiment->has_bonus('taverne'))
			{
				$tavene = $menu->add( new interf_elt_menu('', 'taverne.php', 'return charger(this.href);') );
				$tavene->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-biere') );
				$tavene->get_lien()->add( new interf_txt('Taverne') );
			}
			if($batiment->has_bonus('ecurie'))
			{
				$tavene = $menu->add( new interf_elt_menu('', 'ecurie.php', 'return charger(this.href);') );
				$tavene->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-cheval') );
				$tavene->get_lien()->add( new interf_txt('Ecurie') );
			}
			if($batiment->has_bonus('teleport'))
			{
				$tavene = $menu->add( new interf_elt_menu('', 'teleport.php', 'return charger(this.href);') );
				$tavene->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-magie') );
				$tavene->get_lien()->add( new interf_txt('Teleportation') );
			}
			if($batiment->has_bonus('alchimiste'))
			{
				$tavene = $menu->add( new interf_elt_menu('', 'alchimiste.php', 'return charger(this.href);') );
				$tavene->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-alchimie') );
				$tavene->get_lien()->add( new interf_txt('Alchimiste') );
			}
		}
		else
		{
			$ville = $menu->add( new interf_elt_menu('', 'ville.php', 'return charger(this.href);') );
			$ville->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-ville') );
			$ville->get_lien()->add( new interf_txt('Ville') );
			$forgeron = $menu->add( new interf_elt_menu('', 'boutique.php?type=arme', 'return charger(this.href);') );
			$forgeron->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-forge') );
			$forgeron->get_lien()->add( new interf_txt('Forgeron') );
			$armurerie = $menu->add( new interf_elt_menu('', 'boutique.php?type=armure', 'return charger(this.href);') );
			$armurerie->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-casque') );
			$armurerie->get_lien()->add( new interf_txt('Armurerie') );
			$hotel_ventes = $menu->add( new interf_elt_menu('', 'hotel.php', 'return charger(this.href);') );
			$hotel_ventes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-encheres') );
			$hotel_ventes->get_lien()->add( new interf_txt('Ventes') );
			$ecole_magie = $menu->add( new interf_elt_menu('', 'ecole.php?type=sort', 'return charger(this.href);') );
			$ecole_magie->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-sorts') );
			$ecole_magie->get_lien()->add( new interf_txt('Magie') );
			$ecole_combat = $menu->add( new interf_elt_menu('', 'ecole.php?type=comp', 'return charger(this.href);') );
			$ecole_combat->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-ecole-combat') );
			$ecole_combat->get_lien()->add( new interf_txt('Combat') );
			$quetes = $menu->add( new interf_elt_menu('', 'bureau_quete.php', 'return charger(this.href);') );
			$quetes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-quetes') );
			$quetes->get_lien()->add( new interf_txt('Quêtes') );
			$tavene = $menu->add( new interf_elt_menu('', 'taverne.php', 'return charger(this.href);') );
			$tavene->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-biere') );
			$tavene->get_lien()->add( new interf_txt('Taverne') );
		}
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
			$txt_niv = 'Niveau : ';
		}
		else
			$txt_niv = $row['nom'].' − niveau : ';
		// Jauges
		$this->set_jauge_ext($row['hp'], $row['hp_max'], 'hp', 'HP : ');
		if( $niv )
			$this->set_jauge_int($niveau, $niv_max, 'avance', $txt_niv);
		// Si le batiment est inactif, on le met au niveau 1
		return $row['statut'] == 'inactif' ? 1 : $niveau;
	}
}

/// Classe de base pour l'interface des bâtiments en ville ayant des onglets
class interf_ville_onglets extends interf_ville
{
	protected $onglets;
	function __construct(&$royaume, &$case=null)
	{
		parent::__construct($royaume, $case);
		$this->onglets = $this->centre->add( new interf_onglets('tab_ville') );
	}
}

?>