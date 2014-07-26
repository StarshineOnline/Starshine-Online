<?php
/**
 * @file interf_hotel_vente.class.php
 * Classes pour l'interface de l'hotel des ventes
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'école de magie
class interf_hotel_vente extends interf_ville_onglets
{
	function __construct(&$royaume, $type, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('encheres');
		//$this->recherche_batiment('ecole_magie');
		
		// Onglets
		$armes = new interf_bal_smpl('span', '', false, 'icone icone-forge');
		$armes->set_tooltip('Armes');
		$this->onglets->add_onglet($armes, 'hotel.php?type='.$type.'&categorie=arme&ajax=2', 'tab_arme', 'ecole_mag', $categorie=='arme');
		$armures = new interf_bal_smpl('span', '', false, 'icone icone-casque');
		$armures->set_tooltip('Armures');
		$this->onglets->add_onglet($armures, 'hotel.php?type='.$type.'&categorie=armure&ajax=2', 'tab_armure', 'ecole_mag', $categorie=='armure');
		$dressage = new interf_bal_smpl('span', '', false, 'icone icone-faucon');
		$dressage->set_tooltip('Dressage');
		$this->onglets->add_onglet($dressage, 'hotel.php?type='.$type.'&categorie=objet_pet&ajax=2', 'tab_objet_pet', 'ecole_mag', $categorie=='objet_pet');
		$accessoires = new interf_bal_smpl('span', '', false, 'icone icone-diament');
		$accessoires->set_tooltip('Accessoires');
		$this->onglets->add_onglet($accessoires, 'hotel.php?type='.$type.'&categorie=accessoire&ajax=2', 'tab_accessoire', 'ecole_mag', $categorie=='accessoire');
		$objets = new interf_bal_smpl('span', '', false, 'icone icone-alchimie');
		$objets->set_tooltip('Objets');
		$this->onglets->add_onglet($objets, 'hotel.php?type='.$type.'&categorie=objet&ajax=2', 'tab_objet', 'ecole_mag', $categorie=='objet');
		$gemmes = new interf_bal_smpl('span', '', false, 'icone icone-diament');
		$gemmes->set_tooltip('Gemmes');
		$this->onglets->add_onglet($gemmes, 'hotel.php?type='.$type.'&categorie=gemme&ajax=2', 'tab_gemme', 'ecole_mag', $categorie=='gemme');
		$grimmoires = new interf_bal_smpl('span', '', false, 'icone icone-livres');
		$grimmoires->set_tooltip('Grimmoires');
		$this->onglets->add_onglet($grimmoires, 'hotel.php?type='.$type.'&categorie=grimmoire&ajax=2', 'tab_grimmoire', 'ecole_mag', $categorie=='grimmoire');
		$objt_perso = new interf_bal_smpl('span', '', false, 'icone icone-inventaire');
		$objt_perso->set_tooltip('Mes objets');
		$this->onglets->add_onglet($objt_perso, 'hotel.php?type='.$type.'&categorie=perso&ajax=2', 'tab_perso', 'ecole_mag', $categorie=='perso');
				
		// Vente / achat
		$haut = $this->onglets->get_haut();
		$li1 = $haut->add( new interf_elt_menu(new interf_bal_smpl('span', '', false, 'icone icone-argent'), false, false, false, 'action'.($type=='achat'?' active':'')) );
		$li1->set_tooltip('Achat');
		$li2 = $haut->add( new interf_elt_menu(new interf_bal_smpl('span', '', false, 'icone icone-argent4'), false, false, false, 'action'.($type=='vente'?' active':'')) );
		$li2->set_tooltip('Vente');
	}
}
?>