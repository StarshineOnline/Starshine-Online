<?php
/**
 * @file interf_ecurie.class.php
 * Classes pour le quartier général
 */
 

/// Classe pour le quartier général
class interf_ecurie extends interf_ville
{
	protected $perso;
	protected $places_ville;
	protected $places_terrain;
	protected $places_perso;
	function __construct(&$royaume, &$case)
	{
		global $db, $Gtrad;
		/// @todo centraliser
		$max_ecurie = 10;
		parent::__construct($royaume, $case);
		$this->perso = joueur::get_perso();
		$this->perso->get_pets(true);
		$this->perso->get_ecurie(true);
		// On regarde si on a une écurie personnelle
		/// @todo passer par les objets
		$requete = 'SELECT b.effet FROM terrain AS t INNER JOIN terrain_construction AS c ON c.id_terrain = t.id INNER JOIN terrain_batiment AS b ON c.id_batiment = b.id WHERE b.type = "ecurie" AND t.id_joueur = '.$this->perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		// places
		$this->places_ville = $this->perso->nb_pet_ecurie() < $max_ecurie;
		$this->places_terrain = $row && $this->perso->nb_pet_ecurie_self() < $row['effet'];
		$this->places_perso = $this->perso->nb_pet() < $this->perso->get_comp('max_pet');
		// Icone & jauges
		$icone = $this->set_icone_centre('cheval');
		$icone->set_tooltip('Ecuries');
		//$this->recherche_batiment('', false);
		$utilisation = $this->perso->nb_pet_ecurie() + $this->perso->nb_pet_ecurie_self();
		$max = $max_ecurie + ($row ? $row['effet'] : 0);
		$this->set_jauge_int($utilisation, $max, 'pa', 'Utilisation : ');
		
		// On ne met rien, mais il faut tout de même occuper l'espace (on pourrait trouver une information à mettre)
		$this->centre->add( new interf_bal_smpl('p', '&nbsp;') );
		//
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		interf_alerte::aff_enregistres($div);
		// Créatures en ville
		$div->add( new interf_bal_smpl('h4', 'Créatures en ville ('.$this->perso->nb_pet_ecurie().' / '.$max_ecurie.')') );
		$liste = $div->add( new interf_bal_cont('ul', 'ecurie_ville') );
		/// @todo à améliorer
		foreach($this->perso->ecurie as $pet)
		{
			$this->aff_creature($liste, $pet, true);
		}
		// Ecurie personnelle
		if( $row )
		{
			$div->add( new interf_bal_smpl('h4', 'Créatures dans votre écurie ('.$this->perso->nb_pet_ecurie_self().' / '.$row['effet'].')') );
			$liste = $div->add( new interf_bal_cont('ul', 'ecurie_terrain') );
			/// @todo différencier les deux écuries
			foreach($this->perso->ecurie as $pet)
			{
				$this->aff_creature($liste, $pet, true);
			}
		}
		// Créatures sur soi
		$div->add( new interf_bal_smpl('h4', 'Créatures sur vous ('.$this->perso->nb_pet().' / '.$this->perso->get_comp('max_pet').')') );
		$liste = $div->add( new interf_bal_cont('ul', 'creatures_perso') );
		/// @todo à améliorer
		foreach($this->perso->pets as $pet)
		{
			$this->aff_creature($liste, $pet, false);
		}
	}
	function aff_creature(&$liste, &$pet, $ecurie)
	{
		$pet->get_monstre();
		$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
		if( $ecurie )
		{
			if( $this->places_perso )
			{
				$reprendre = $li->add( new interf_lien('', 'ecurie.php?action=reprendre&id='.$pet->get_id(), false, 'icone icone-bas') );
				$reprendre->set_tooltip('Reprendre');
			}
		}
		else
		{
			if( $this->places_ville )
			{	
				$taxe_depot = ceil($pet->get_cout_depot() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
				$reprendre = $li->add( new interf_lien_cont('ecurie.php?action=deposer_ville&id='.$pet->get_id()) );
				$reprendre->add( new interf_bal_smpl('span', '', false, 'icone icone-haut') );
				$reprendre->add( new interf_bal_smpl('span', $pet->get_cout_depot() + $taxe_depot, false, 'xsmall') );		
				$reprendre->set_tooltip('Déposer ('.($pet->get_cout_depot() + $taxe_depot).' stars)');
			}
			if( $this->places_terrain )
			{
				$reprendre = $li->add( new interf_lien('', 'ecurie.php?action=deposer_terrain&id='.$pet->get_id(), false, 'icone icone-haut') );	
				$reprendre->set_tooltip('Déposer');
			}
		}
		if( $pet->get_hp() <= 0 )
		{
			$taxe_rez = ceil($pet->get_cout_rez() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
			$rez = $li->add( new interf_lien_cont('ecurie.php?action=soin&id='.$pet->get_id()) );
			$rez->add( new interf_bal_smpl('span', '', false, 'icone icone-rez') );
			$rez->add( new interf_bal_smpl('span', ($pet->get_cout_rez() + $taxe_rez), false, 'xsmall') );
			$rez->set_tooltip('Ressusciter ('.($pet->get_cout_rez() + $taxe_rez).' stars)');
		}
		else
		{
			$taxe_soin = ceil($pet->get_cout_soin() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
			$rez = $li->add( new interf_lien_cont('ecurie.php?action=rez&id='.$pet->get_id()) );
			$rez->add( new interf_bal_smpl('span', '', false, 'icone icone-soin') );
			$rez->add( new interf_bal_smpl('span', ($pet->get_cout_soin() + $taxe_soin), false, 'xsmall') );
			$rez->set_tooltip('Soigner ('.($pet->get_cout_soin() + $taxe_soin).' stars)');
		}
		$hp = $li->add( new interf_jauge_bulle('MP', $pet->get_mp(), $pet->get_mp_max(), false, 'mp', false, 'jauge_case') );
		$li->add( new interf_jauge_bulle('HP', $pet->get_hp(), $pet->monstre->get_hp(), false, 'hp', false, 'jauge_case') );
		$li->add( new interf_img('image/monstre/'.$pet->monstre->get_lib().'.png', $pet->monstre->get_nom()) );
		$li->add( new interf_bal_smpl('span', $pet->get_nom()) );
		$li->add( new interf_bal_smpl('span', $pet->monstre->get_nom().' - niveau '.$pet->monstre->get_level(), false, 'xsmall') );
	}
}

?>