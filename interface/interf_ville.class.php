<?php
/**
 * @file interf_ville.class.php
 * Classe de base pour l'interface des bâtiments en ville 
 */

/// Classe de base pour l'interface des bâtiments en ville 
class interf_ville extends interf_gauche
{
	protected $royaume;
	
	function __construct(&$royaume)
	{
		$this->royaume = &$royaume;
		parent::__construct('carte');

		// Menu ville
		$menu = $this->barre_haut->add( new interf_menu(false, 'menu_ville', false) );
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

?>