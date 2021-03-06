<?php
/// @addtogroup Interface
/**
 * @file interf_inventaire.class.php
 * Gestion de l'affichage de l'inventaire
 */
 
/**
 * Classe gérant l'affichage des livres de sorts/compétences
 */
class interf_livre_sortcomp extends interf_bal_cont
{
	protected $perso;
	protected $type;
	protected $cible;
	protected $actions;
	
	function __construct($type, &$cible, $categorie, $actions)
	{
		global $Gtrad, $db;
		parent::__construct('div', false, 'livre');
		$this->perso = &joueur::get_perso();
		$this->type = $type;
		$this->cible = &$cible;
		$this->actions = &$actions;
		
		// Affichage des catégories
		$this->aff_categories($categorie);
		
		// Haut du livre
		$haut = $this->add( new interf_bal_cont('div', false, 'livre_haut') );
		if( $type == 'sort_jeu' )
			$haut->add( new interf_bal_smpl('h3', 'Cible : '.$cible->get_nom()) );
			
		// Corps du live
		$corps = $this->add( new interf_bal_cont('div', false, 'livre_corps') );
		switch( $type )
		{
		case 'sort_jeu':
			$requete = 'SELECT id_sort FROM sort_favoris WHERE id_perso = '.$this->perso->get_id();
			$req = $db->query($requete);
			$favoris = array();
			while( $row = $db->read_array($req) )
			{
				$favoris[] = $row[0];
			}
			if($categorie == 'favoris')
  			$cond = 'id IN ('.implode(',', $favoris).')';
  		else
				$cond = 'comp_assoc = "'.$categorie.'" AND id IN ('.strtr($this->perso->get_sort_jeu(), ';', ',').')';
			$elts = $favoris || $categorie != 'favoris' ? sort_jeu::create('', '', 'comp_assoc ASC, type ASC', false, $cond) : array();
			$lance = true; 
			break;
		case 'sort_combat':
			$cond = 'comp_assoc = "'.$categorie.'" AND id IN ('.strtr($this->perso->get_sort_combat(), ';', ',').')';
			$elts = sort_combat::create('', '', 'comp_assoc ASC, type ASC', false, $cond);
			$lance = false; 
			break;
		case 'comp_jeu':
			$requete = 'SELECT id_comp FROM comp_favoris WHERE id_perso = '.$this->perso->get_id();
			$req = $db->query($requete);
			$favoris = array();
			while( $row = $db->read_array($req) )
			{
				$favoris[] = $row[0];
			}
			if($categorie == 'favoris' && $favoris)
  			$cond = 'id IN ('.implode(',', $favoris).')';
  		else
				$cond = 'comp_assoc = "'.$categorie.'" AND id IN ('.strtr($this->perso->get_comp_jeu(), ';', ',').')';
			$elts = $favoris || $categorie != 'favoris' ? comp_jeu::create('', '', 'comp_assoc ASC, type ASC', false, $cond) : array();
			$lance = true; 
			break;
		case 'comp_combat':
			$cond = 'comp_assoc = "'.$categorie.'" AND id IN ('.strtr($this->perso->get_comp_combat(), ';', ',').')';
			$elts = comp_combat::create('', '', 'comp_assoc ASC, type ASC', false, $cond);
			$lance = false; 
			break;
		}
		$liste = false;
		$comp_assoc = '';
		foreach($elts as $elt)
		{
			if( $elt->get_comp_assoc() != $comp_assoc )
			{
				$comp_assoc = $elt->get_comp_assoc();
				$corps->add( new interf_bal_smpl('h6', $Gtrad[$comp_assoc]) );
				$liste = false;
			}
			if( !$liste )
				$liste = $corps->add( new interf_bal_cont('ul') );
			$li = $liste->add( new interf_bal_cont('li') );
			$e = $li->add( new interf_bal_cont('div', false, 'livre_elt') );
			if( $lance )
			{
		    $cout_pa = $elt->get_pa();
		    $cout_mp = $elt->get_mp_final($this->perso);
				if( $categorie == 'favoris' || in_array($elt->get_id(), $favoris) )
				{
	        $lien = 'livre.php?type='.$type.'&categorie='.$categorie.'&action=suppr_favori&cible='.$cible->get_id().'&type_cible='.$cible->get_type().'&id='.$elt->get_id();
					$fav = $e->add( new interf_lien('', $lien, false, 'icone icone-suppr-favori') );
					$fav->set_tooltip('Supprimer des favoris', 'left', '#livres');
				}
				else
				{
	        $lien = 'livre.php?type='.$type.'&categorie='.$categorie.'&action=favori&cible='.$cible->get_id().'&type_cible='.$cible->get_type().'&id='.$elt->get_id();
					$fav = $e->add( new interf_lien('', $lien, false, 'icone icone-favoris') );
					$fav->set_tooltip('Mettre dans les favoris', 'left', '#livres');
				}
				
				$inf = $e->add( new interf_bal_smpl('a', '', false, 'icone icone-info') );
				$inf->set_tooltip('Afficher/masquer les informations');
				$inf->set_attribut('onclick', '$(\'#info_'.$type.'_'.$elt->get_id().'\').slideToggle();');
        if( $type == 'sort_jeu' )
        {
					if( $this->perso->is_competence('sort_groupe') )
						$lance_groupe = $this->perso->get_comp_perso('sort_groupe')->get_valeur();
					else if( $this->perso->is_competence('sort_groupe_'.$elt->get_comp_assoc()) )
						$lance_groupe = $this->perso->get_comp_perso( 'sort_groupe_'.$elt->get_comp_assoc() )->get_valeur();
					else
						$lance_groupe = false;
        	$sort_groupe = false;
        	if( $cible->get_type() == 'perso' )
        	{
		        if($cible->get_id() == $this->perso->get_id())
		        {
		        	$sort_groupe = $elt->get_cible() == comp_sort::cible_unique && $elt->get_type() != 'rez';
		        	$cond = $sort_groupe || $elt->get_cible() == comp_sort::cible_perso || $elt->get_cible() == comp_sort::cible_groupe || $elt->get_cible() == comp_sort::cible_case || $elt->get_cible() == comp_sort::cible_batiment || $elt->get_cible() == comp_sort::cible_9cases;
		        }
		        else
		        	$cond = ($elt->get_cible() == comp_sort::cible_unique || $elt->get_cible() == comp_sort::cible_autre || $elt->get_cible() == comp_sort::cible_autregrp || $elt->get_cible() == comp_sort::cible_9cases);
					}
					else
						$cond = (/*$elt->get_cible() == comp_sort::cible_unique ||*/ $elt->get_cible() == comp_sort::cible_autre || $elt->get_cible() == comp_sort::cible_autregrp ) && $elt->get_type() != 'rez';
	        if( $sort_groupe && $lance_groupe && $this->perso->get_id_groupe() )
	        {
	        	$lien = 'livre.php?type='.$type.'&categorie='.$categorie.'&action=lancer_groupe&cible='.$cible->get_id().'&type_cible='.$cible->get_type().'&id='.$elt->get_id();
	        	$grp = $e->add( new interf_lien_cont($lien, false, 'icone') );
	        	$grp->add( new interf_bal_smpl('div', '', false, 'icone-groupe'));
	        	$grp->set_tooltip('Lancer sur '.$Gtrad['cible_ex'.comp_sort::cible_groupe]);
	        	$grp->add( new interf_bal_smpl('span', ceil($cout_mp * (2.5 - .5*$lance_groupe) ).' MP', false, 'xsmall'));
					}
				}
				if( $cond )
				{
					$lien = 'livre.php?type='.$type.'&categorie='.$categorie.'&action=lancer&cible='.$cible->get_id().'&type_cible='.$cible->get_type().'&id='.$elt->get_id();
					$e = $e->add( new interf_lien_cont($lien) );
					switch( $elt->get_cible() )
					{
					case comp_sort::cible_unique:
					case comp_sort::cible_autre:
					case comp_sort::cible_autregrp:
						$e->set_tooltip('Lancer sur '.$Gtrad['cible_ex'.$elt->get_cible()].$cible->get_nom());
						break;
					case comp_sort::cible_perso:
					case comp_sort::cible_batiment:
					case comp_sort::cible_9cases:
						$e->set_tooltip('Lancer sur '.$Gtrad['cible_ex'.$elt->get_cible()]);
						break;
					}
				}
				$img = $elt->get_image();
				if( $img )
					$e->add( new interf_img($img) );
			}
			else
			{
				$inf = $e->add( new interf_bal_smpl('a', '', false, 'icone icone-info') );
				$inf->set_tooltip('Afficher/masquer les informations');
				$inf->set_attribut('onclick', '$(\'#info_'.$type.'_'.$elt->get_id().'\').slideToggle();');
			}
			$e->add( new interf_bal_smpl('span', $elt->get_nom(), false, 'livre_nom') );
			if( $lance )
			{
				$e->add( new interf_bal_smpl('span', $cout_pa.' PA − '.$cout_mp.' MP', false, 'xsmall') );
			}
			else
			{
				$texte = $elt->get_mp_final($this->perso).' RM';
				$carac = $this->perso->get_comp( $elt->get_carac_assoc() );
				switch( $elt->get_type() )
				{
				case 'drain_vie':
				case 'vortex_vie':
				case 'vortex_mana':
					$carac -= 2;
				case 'degat_feu':
				case 'degat_nature':
				case 'degat_froid':
				case 'degat_vent':
				case 'degat_terre':
				case 'pacte_sang':
				case 'brisement_os':
				case 'lapidation':
				case 'globe_foudre':
				case 'vortex_mana':
				case 'putrefaction':
				case 'embrasement':
				case 'sphere_glace':
					$de_degat_sort = comp_sort::calcule_des($carac, $elt->get_effet());
					$ide = 0;
					$des = '';
					while($ide < count($de_degat_sort))
					{
						if ($ide > 0) $des .= ' + ';
						$des .= '1D'.$de_degat_sort[$ide];
						$ide++;
					}
					$texte .= ' − '.$des.' dégâts';
					break;
				}
				$e->add( new interf_bal_smpl('span', $texte, false, 'xsmall') );
			}
			// Informations
			$infos = $li->add( new interf_bal_cont('div', 'info_'.$type.'_'.$elt->get_id(), 'livre_infos') );
			$infos->add( new interf_bal_smpl('p', $elt->get_description(true)) );
			if( $type == 'sort_jeu' || $type == 'sort_combat' )
			{
				$infos->add( new interf_bal_smpl('p', 'Incantation : '.$elt->get_incantation()) );
				$infos->add( new interf_bal_smpl('p', $Gtrad[$elt->get_comp_assoc()].' : '.round($elt->get_comp_requis()*$affinite)) );
			}
			else
			{
				$infos->add( new interf_bal_smpl('p', 'Coefficient : '.($elt->get_carac_requis()*$elt->get_comp_requis())) );
				if( $elt->get_arme_requis() )
				{
					$armes = explode(';', $elt->get_arme_requis());
					$armes = array_map(function ($a) { global $Gtrad; return $Gtrad[$a]; }, $armes);
					$infos->add( new interf_bal_smpl('p', 'Arme'.(count($armes)>1?'s':'').' : '.implode(', ', $armes)) );
				}
			}
			$infos->add( new interf_bal_smpl('p', 'Cible : '.$Gtrad['cible'.$elt->get_cible()]) );
			if( $elt->get_duree() )
			{
				if( $lance )
					$infos->add( new interf_bal_smpl('p', 'Durée : '.transform_min_temp($elt->get_duree())) );
				else
					$infos->add( new interf_bal_smpl('p', 'Durée : '.$elt->get_duree().' round'.($elt->get_duree()>1?'s':'')) );
			}
		}
		
		// Bas du livre
		$this->add( new interf_bal_cont('div', false, 'livre_bas') );
	}
	
	protected function aff_categories($categorie)
	{
		global $Gtrad;
		switch( $this->type )
		{
		case 'sort_jeu':
			$gauche = $this->add( new interf_bal_cont('div', 'livre_sorts', 'livre_gauche') );
			$categories = array('sort_element', 'sort_mort', 'sort_vie', 'favoris');
			break;
		case 'sort_combat':
			$gauche = $this->add( new interf_bal_cont('div', 'livre_sorts_combat', 'livre_gauche') );
			$categories = array('sort_element', 'sort_mort', 'sort_vie');
			break;
		case 'comp_jeu':
			$gauche = $this->add( new interf_bal_cont('div', 'livre_competences', 'livre_gauche') );
			$categories = array('distance', 'esquive', 'dressage', 'melee', 'favoris');
			break;
		case 'comp_combat':
			$gauche = $this->add( new interf_bal_cont('div', 'livre_competences_combat', 'livre_gauche') );
			$categories = array('distance', 'esquive', 'blocage', 'melee');
			break;
		}
		
		$menu = $gauche->add( new interf_menu(false, false, false) );
		foreach($categories as $cat)
		{
			$url = 'livre.php?action=afficher&type='.$this->type.'&categorie='.$cat.'&cible='.$this->cible->get_id().'&type_cible='.$this->cible->get_type();
			$elt = $menu->add( new interf_elt_menu('&nbsp;', $url, 'return charger(this.href);', false, 'livre_categorie livre_'.$cat) );
			$elt->set_tooltip($Gtrad[$cat], 'right', '#livre');
		}
	}
}
?>