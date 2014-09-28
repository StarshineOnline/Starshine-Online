<?php
/**
 * @file interf_monstre.class.php
 * Permet l'affichage des informations d'un monstre
 */
         

class interf_monstre extends interf_cont
{
	protected $perso;
	protected $entite;
	protected $incarn;
	protected $def;
	function __construct(&$entite, $actions)
	{
		global $G_url;
		$this->perso = &joueur::get_perso();
		$this->entite = &entite::factory('monstre', $entite, $this->perso);
		$monstre = &$entite->get_def();
		/// @todo à améliorer
		$this->incarn = &$entite;
		$this->def = &$monstre;
		$distance = $this->perso->calcule_distance($entite);
		
		$infos_monstre = $this->add( new interf_bal_cont('div', 'infos_monstre', 'info_case') );
		
		// Actions
		switch( get_class($entite) )
		{
		case 'map_monstre';
			$case = convert_in_pos($entite->get_x(), $entite->get_y());
			$retour = $infos_monstre->add( new interf_lien_cont('informationcase.php?case='.$case, false, 'icone icone-retour') );
			$retour->set_tooltip('Retour aux informations de la case', 'bottom', '#information');
			$vue = $infos_monstre->add( new interf_lien_cont('', false, 'icone icone-oeil') );
			$vue->set_tooltip('Afficher uniquement les monstres de ce type sur la carte', 'bottom', '#information');
			if( $distance == 0 && $this->perso->can_dresse($monstre) && $this->perso->nb_pet() < $this->perso->get_comp('max_pet') )
			{
				$dresse = $this->perso->is_buff('dressage');
				if( !$dresse || $this->perso->get_buff('dressage', 'effet2') == $this->entite->get_id() )
				{//http://www.starshine-online.com/dressage.php?id=687498
					$dressage = $infos_monstre->add( new interf_lien('', 'dressage.php?id='.$this->entite->get_id(), false, 'icone icone-lapin') );
					$dressage->set_tooltip(($dresse?'Continuer à d':'D').'resser (10 PA)', 'bottom', '#information');
				}
			}
			if( $this->perso->get_sort_jeu() )
			{
				$sort = $infos_monstre->add( new interf_lien('', 'livre.php?type_cible=monstre&amp;cible='.$this->entite->get_id(), false, 'icone icone-sorts') );
				$sort->set_tooltip('Lancer un sort', 'bottom', '#information');
			}
			if( $this->perso->peut_attaquer() )
			{
				$pa_attaque = $this->perso->get_cout_attaque($this->perso, $this->entite);
				if( $this->perso->nb_pet() > 0 && $this->perso->get_pet() && $distance <= $this->perso->get_distance_tir() )
				{
					$att_pet = $infos_monstre->add( new interf_lien('', 'attaque.php?type=monstre&amp;id_monstre='.$this->entite->get_id().'&amp;pet', false, 'icone icone-chien') );
					$att_pet->set_tooltip('Attaquer avec votre créature ('.$pa_attaque.' PA)', 'bottom', '#information');
				}
				if( $distance <= $this->perso->get_distance_tir() )
				{
					$att = $infos_monstre->add( new interf_lien('', 'attaque.php?type=monstre&amp;id_monstre='.$this->entite->get_id(), false, 'icone icone-attaque') );
					$att->set_tooltip('Attaquer ('.$pa_attaque.' PA)', 'bottom', '#information');
				}
			}
			$div = $infos_monstre->add( new interf_bal_cont('div') );
			// HP
			/// @todo à améliorer
			$niveau = $this->entite->get_level() > 0 ? $this->entite->get_level() : 1;
			$nbr_barre_total = min(max(ceil($this->perso->get_survie() / $niveau), 0), 100);
			$nbr_barre = round($this->entite->get_hp() / $this->entite->get_hp_max() * $nbr_barre_total);
			$longueur = max(round(100 * ($nbr_barre / $nbr_barre_total), 2), 0);
			$fiabilite = round((100 / $nbr_barre_total) / 2, 2);
			$jauge = $div->add( new interf_jauge_bulle(false, $nbr_barre, $nbr_barre_total, false, 'hp', false, 'jauge_case') );
			$jauge->set_tooltip('HP : '.$longueur.'% ± '.$fiabilite.'%', 'bottom', '#contenu');
			$jauge->add( new interf_bal_smpl('div', round($longueur).'%', false, 'bulle_valeur') );
			break;
		case 'pet':
			$G_url->add('id', $entite->get_id());
			$retour = $infos_monstre->add( new interf_lien_cont($G_url->get(), false, 'icone icone-retour') );
			$retour->set_tooltip('Retour à la gestion de vos créatures', 'bottom', '#information');
			$vue = $infos_monstre->add( new interf_lien_cont('', false, 'icone icone-oeil') );
			$vue->set_tooltip('Afficher uniquement les monstres de ce type sur la carte', 'bottom', '#information');
			if( $entite->get_hp() < $monstre->get_hp() || $entite->get_mp() < $entite->get_mp_max() )
			{
				$soin = $infos_monstre->add( new interf_lien_cont($G_url->get('action', 'soin'), false, 'icone') );
				$soin->add( new interf_bal_smpl('span', '', false, 'icone icone-soin') );
				$cout_hp = round($this->perso->get_hp_max() / 10);
				$soin->add( new interf_bal_smpl('span', '-'.$cout_hp.' HP', false, 'xsmall') );
				$soin->set_tooltip('Soigner, puissance : '.$this->perso->soin_pet().', vous coûte '.$cout_hp.' HP et 1 PA.');
			}
			$div = $infos_monstre->add( new interf_bal_cont('div') );
			$div->add( new interf_jauge_bulle('MP', $entite->get_mp(), $entite->get_mp_max(), '%', 'mp', false, 'jauge_case') );
			$div->add( new interf_jauge_bulle('HP', $entite->get_hp(), $monstre->get_hp(), '%', 'hp', false, 'jauge_case') );
			$niveau = $this->entite->get_level();
		}
		
		$div->add( new interf_img('image/monstre/'.$monstre->get_lib().'.png', $monstre->get_nom()) );
		// niveau
		$diff_niv = $niveau - $this->perso->get_level();
		if( $monstre->get_affiche() == 'h' )
		{
			$diff_niv = 0;
			$niveau = 'xxx';
		}
		$classe = 'niv'.min(max($diff_niv,-5),5);
		switch($diff_niv)
		{
		case -1:
			$texte = 'Ce monstre a 1 niveau de moins que vous';
			break;
		case 0:
			$texte = 'Ce monstre a le même niveau que vous';
			break;
		case 1:
			$texte = 'Ce monstre a 1 niveau de moins que vous';
			break;
		default:
			if( $diff_niv < -1 )
				$texte = 'Ce monstre a '.(-$diff_niv).' niveaux de moins que vous';
			else
				$texte = 'Ce monstre a '.$diff_niv.' niveaux de plus que vous';
		}
		$niv = $div->add( new interf_bal_smpl('span', 'Niveau : '.$niveau, 'niveau_monstre', $classe) );
		$niv->set_tooltip($texte, 'bottom');
		$div->add( new interf_bal_smpl('br') );
		$div->add( new interf_bal_smpl('span', 'Type : '.$this->entite->get_type_def(), 'type_monstre') );
		
		if( $niveau != 'xxx' )
			$this->aff_infos();
	}
	
	/// Affiche les informations sur le monstre en fonction de la survie
	function aff_infos()
	{
		global $Gtrad;
		// Calcule du coefficient pour savoir ce que l'on va afficher
		$survie = $this->perso->get_survie();
		if($this->entite->get_type_def() == 'bete' AND $this->perso->is_competence('survie_bete'))
			$survie += $this->perso->get_competence('survie_bete');
		if($this->entite->get_type_def() == 'humanoide' AND $this->perso->is_competence('survie_humanoide'))
			$survie += $this->perso->get_competence('survie_humanoide');
		if($this->entite->get_type_def() == 'magique' AND $this->perso->is_competence('survie_magique'))
			$survie += $this->perso->get_competence('survie_magique');
		$coeff = floor($survie / $this->entite->get_level());
		
		
		//Description
		if($coeff >= 1)
			$this->add( new interf_bal_smpl('p', $this->def->get_description(), 'descr_monstre', 'info_case') );
		
		// debuffs
		if( get_class($this->incarn) == 'map_monstre' )
		{
			$debuffs = $this->add( new interf_bal_cont('div', 'debuffs_monstre') );
			if($coeff >= 10)
				$debuffs->add( new interf_liste_buff($this->entite, false) );
			if($coeff >= 15)
				$debuffs->add( new interf_liste_buff($this->entite, true) );
		}
		
		// gauche
		$gauche = $this->add( new interf_bal_cont('div', 'liste_gauche', 'liste_case') );
		$lg = $gauche->add( new interf_descr_tbl() );
		if($coeff >= 30)
			$lg->nouv_elt('HP', $this->entite->get_hp_max());
		if($coeff >= 11)
			$lg->nouv_elt('Constitution', 15);
		if($coeff >= 16)
			$lg->nouv_elt('Force', $this->entite->get_force());
		if($coeff >= 18)
			$lg->nouv_elt('Dextérité', $this->entite->get_dexterite());
		if($coeff >= 17)
			$lg->nouv_elt('Puissance', $this->entite->get_puissance());
		if($coeff >= 19)
			$lg->nouv_elt('Volonté', $this->entite->get_volonte());
		if($coeff >= 12)
		{
			$pp = $lg->nouv_elt('PP', $this->entite->get_pp());
			$pp->set_tooltip('Réduction des dégâts physiques de '.round(calcul_pp($this->entite->get_pp()),1).' %', 'bottom');
		}
		if($coeff >= 13)
		{
			$pp = $lg->nouv_elt('PM', $this->entite->get_pm());
			///@todo à améliorer
			$pp->set_tooltip('Réduction des dégâts magiques de '.round(calcul_pp($pm * $this->entite->get_puissance() / 12),1).' %', 'bottom');
		}
		if($coeff >= 14)
			$lg->nouv_elt('RM', $this->entite->get_reserve());
		
		// droite
		$droite = $this->add( new interf_bal_cont('div', 'liste_droite', 'liste_case') );
		$ld = $droite->add( new interf_descr_tbl() );
		if($coeff >= 5)
			$ld->nouv_elt('Arme', $Gtrad[$this->entite->get_arme_type()]);
		if($coeff >= 21)
			$ld->nouv_elt('Mêlée / Tir', $this->entite->get_melee());
		if($coeff >= 23)
			$ld->nouv_elt('Esquive', $this->entite->get_esquive());
		if($coeff >= 24)
			$ld->nouv_elt('Blocage', $this->entite->get_blocage());
		if($coeff >= 22)
			$ld->nouv_elt('Incantation', $this->entite->get_incantation());
		if($coeff >= 25)
			$ld->nouv_elt('Magie élémetaire', $this->entite->get_sort_element());
		if($coeff >= 26)
			$ld->nouv_elt('Nécromancie', $this->entite->get_sort_mort());
		if($coeff >= 27)
			$ld->nouv_elt('Magie de la vie', $this->entite->get_sort_vie());
		if($coeff >= 20)
			$ld->nouv_elt('Bouclier', $this->entite->get_bouclier_degat());
	}
}

?>