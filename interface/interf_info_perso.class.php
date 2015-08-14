<?php
/**
 * @file interf_info_perso.class.php
 * Permet l'affichage des informations d'un personnages
 */
         
/**
 * Affichage des informations d'un personnages
 */
class interf_info_perso extends interf_cont
{
	protected $perso;
	protected $pj;
	function __construct(&$pj, $actions)
	{
		global $Tclasse, $Trace, $Gtrad;
		$this->pj = &$pj;
		$this->perso = &joueur::get_perso();
		$distance = $this->perso->calcule_distance($pj);
		
		$infos_pj = $this->add( new interf_bal_cont('div', 'infos_pj', 'info_case') );
		$div = $infos_pj->add( new interf_bal_cont('div') );
		
		// Actions
		$bonus = recup_bonus($pj->get_id());
		$royaume = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
		if( $this->perso->get_id() != $pj->get_id() )
		{
			$bonus_soi = recup_bonus($this->perso->get_id());
			$diplo = $royaume->get_diplo( $pj->get_race() );
		}
		else
		{
			$bonus_soi = $bonus;
			$diplo = 127;
		}
		if( $actions )
		{
			if( $this->perso->get_id() != $pj->get_id() )
			{
				// invitation
				if($distance <= 3 && !$this->perso->is_buff('debuff_groupe', true))
				{
					if($this->perso->get_groupe())
					{
						$groupe = new groupe($this->perso->get_groupe());
						$invit = $groupe->get_id_leader() == $this->perso->get_id() && $groupe->get_place_libre();
					}
					else
						$invit = true;
					if( $invit )
					{
						$sort = $div->add( new interf_lien('', 'invitegroupe.php?id='.$pj->get_id(), false, 'icone icone-inviter') );
						$sort->set_tooltip('Inviter ce joueur dans votre groupe', 'bottom', '#information');
					}
				}
				if( $this->perso->get_groupe() && $this->perso->get_groupe() == $this->pj->get_groupe() )
				{
					$groupe = new groupe($this->perso->get_groupe());
					if( $groupe->get_id_leader() == $this->perso->get_id() )
					{
						$excl = $div->add( new interf_lien('', 'degrouper.php?id='.$pj->get_id(), false, 'icone icone-exclure', 'Voulez vous expulser ce joueur ?') );
						$excl->set_tooltip('Exclure ce joueur du groupe', 'bottom', '#information');
					}
				}
				// échange
				$diplo_ok = $diplo == 127 || $dilo <= 5 || $bonus[5] || $bonus_soi[5];
				$star_ok = (isset($bonus[1]) && isset($bonus_soi[3])) || (isset($bonus[3]) && isset($bonus_soi[1]));
				$obj_ok = (isset($bonus[2]) && isset($bonus_soi[4])) || (isset($bonus[4]) && isset($bonus_soi[2]));
				if( $diplo_ok && ($star_ok || $obj_ok) )
				{
					$ech = $div->add( new interf_lien('', 'echange.php?action=creer&perso='.$pj->get_id(), false, 'icone icone-echange') );
					$ech->set_tooltip('Initier un échange', 'bottom', '#information');
				}
				// message
				$msg = $div->add( new interf_lien('', 'messagerie.php?action=nouveau&type=perso&id='.$pj->get_id(), false, 'icone icone-message') );
				$msg->set_tooltip('Envoyer un message', 'bottom', '#information');
			}
			else
			{
				// feuille de personnage
				$msg = $div->add( new interf_lien('', 'personnage.php', false, 'icone icone-perso') );
				$msg->set_tooltip('Accès à la fiche de votre personnage', 'bottom', '#information');
			}
			// sorts
			if( $this->perso->get_sort_jeu() )
			{
				$sort = $div->add( new interf_lien('', 'livre.php?type_cible=perso&amp;cible='.$this->pj->get_id(), false, 'icone icone-sorts') );
				$sort->set_tooltip('Lancer un sort', 'bottom', '#information');
			}
			if( $this->perso->get_id() != $pj->get_id() )
			{
				// attaques
				if( $this->perso->peut_attaquer() )
				{
					$pa_attaque = $this->perso->get_cout_attaque($this->perso, $this->pj);
					if( $this->perso->nb_pet() > 0 && $this->perso->get_pet() && $distance <= $this->perso->get_distance_tir() )
					{
						$att_pet = $div->add( new interf_lien('', 'attaque.php?type=perso&amp;id_perso='.$this->pj->get_id().'&amp;pet', false, 'icone icone-chien') );
						$att_pet->set_tooltip('Attaquer avec votre créature ('.$pa_attaque.' PA)', 'bottom', '#information');
					}
					if( $distance <= $this->perso->get_distance_tir() )
					{
						$att = $div->add( new interf_lien('', 'attaque.php?type=perso&amp;id_perso='.$this->pj->get_id(), false, 'icone icone-attaque') );
						$att->set_tooltip('Attaquer ('.$pa_attaque.' PA)', 'bottom', '#information');
					}
				}
			}
		}
		
		// bonus
		$bonus_total = recup_bonus_total($pj->get_id());
		// image
		$img = 'image/personnage/'.$this->pj->get_race().'/'.$this->pj->get_race().'_'.$Tclasse[$this->pj->get_classe()]['type'].'.png';
		$div->add( new interf_img($img, $this->pj->get_nom()) );
		// diplomatie
		if( $this->perso->get_id() != $pj->get_id() )
		{
			//$royaume = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
			//$diplo = $royaume->get_diplo( $pj->get_race() );
			$facteur_honneur = max($diplo * 0.2 - 0.8, 0);
			$diplo = 'diplo'.$diplo;
			$diplo_txt = $Gtrad[$diplo];
			if($diplo == 'diplo127')
			{
				$facteur_honneur = 0;
				$amende = recup_amende( $pj->get_id() );
				if($amende)
				{
					switch($amende['statut'])
					{
					case 'bandit' :
						$diplo_txt = 'Bandit';
						$diplo = 'diplo5';
						$facteur_honneur = 0.2;
						break;
					case 'criminel' :
						$diplo_txt = 'Criminel';
						$diplo = 'diplo10';
						$facteur_honneur = 1.2;
						break;
					}
				}
			}
		}
		else
			$diplo = '';
		// grade
		if( !array_key_exists(6, $bonus) || check_affiche_bonus($bonus[6], $this->perso, $this->pj) )
		{
			$grade = $this->pj->get_grade();
			$sp_grade = $div->add( new interf_bal_smpl('span', ucwords($grade->get_nom()), 'grade_pj') );
			$sp_grade->set_tooltip(($diplo?'Ce personnage peut':'Vous pouvez').' avoir jusqu\'à '.$grade->get_nb_buff().' buffs', 'bottom');
		}
		$race = $div->add( new interf_bal_smpl('span', $Gtrad[$pj->get_race()], 'race_pj', $diplo) );
		if($diplo)
			$race->set_tooltip($diplo_txt.' − honneur/réputation : '.($facteur_honneur * 100).'%', 'bottom');
		$div->add( new interf_bal_smpl('br') );
		// classe
		if( !array_key_exists(7, $bonus) || check_affiche_bonus($bonus[7], $this->perso, $this->pj) )
		{
			$div->add( new interf_bal_smpl('span', ucwords($pj->get_classe()), 'classe_pj') );
		}
		// niveau
		if( !array_key_exists(11, $bonus) || check_affiche_bonus($bonus[11], $this->perso, $this->pj) )
		{
			$diff_niv = $this->pj->get_level() - $this->perso->get_level();
			$classe_niv = 'niv'.min(max($diff_niv,-5),5);
			$niv = $div->add( new interf_bal_smpl('span', $pj->get_level()?' - niv. '.$pj->get_level():' - PNJ', 'niv_pj', $classe_niv) );
			if( $this->perso->get_id() != $pj->get_id() )
			{
				switch($diff_niv)
				{
				case -1:
					$texte = 'Ce personnage a 1 niveau de moins que vous';
					break;
				case 0:
					$texte = 'Ce personnage a le même niveau que vous';
					break;
				case 1:
					$texte = 'Ce personnage a 1 niveau de moins que vous';
					break;
				default:
					if( $diff_niv < -1 )
						$texte = 'Ce personnage a '.(-$diff_niv).' niveaux de moins que vous';
					else
						$texte = 'Ce personnage a '.$diff_niv.' niveaux de plus que vous';
				}
				$niv->set_tooltip($texte, 'bottom');
			}
		}
		
		// Etat du PJ
		$etat_pj = $this->add( new interf_bal_cont('div', 'etat_pj', 'info_case') );
		$div2 = $etat_pj->add( new interf_bal_cont('div') );
		// retour à la case
		if( $actions && $distance < 3 )
		{
			$lien = 'informationcase.php?case='.convert_in_pos($this->perso->get_x(), $this->perso->get_y());
			$retour = $div2->add( new interf_lien('', $lien, false, 'icone icone-retour') );
			$retour->set_tooltip('Retour aux informations de la case', 'bottom', '#information');
		}
		// HP & MP
		if( $this->pj->est_mort() )
		{
			$div_mort = $div2->add( new interf_bal_cont('div', false, 'jauge_case') );
			$div_mort->add( new interf_bal_cont('span', false, 'icone icone-mort') );
			$div_mort->set_tooltip('Ce personnage est mort', 'bottom');
		}
		if( $this->pj->get_groupe() && $this->pj->get_groupe() == $this->perso->get_groupe() )
		{
			if( !$this->pj->est_mort() )
			{
				$hp = $div2->add( new interf_jauge_bulle('HP', $this->pj->get_hp(), $this->pj->get_hp_max(), false, 'hp', false, 'jauge_case') );
				$hp->add( new interf_bal_smpl('div', round($this->pj->get_hp() / $this->pj->get_hp_max() * 100).'%', false, 'bulle_valeur') );
			}
			$mp = $div2->add( new interf_jauge_bulle('MP', $this->pj->get_mp(), $this->pj->get_mp_max(), false, 'mp', false, 'jauge_case') );
			$mp->add( new interf_bal_smpl('div', round($this->pj->get_mp() / $this->pj->get_mp_max() * 100).'%', false, 'bulle_valeur') );
		}
		// position
		/// @todo gérer les coordonnées cachées
	  // Nysin ?
		if( 75 <= $pj->get_x() && $pj->get_x() <= 100 && 288 <= $pj->get_y() && $pj->get_y() <= 305 )
    	$txt = 'Position : * / * - distance : '.$distance;
		else
    	$txt = 'Position : '.$pj->get_x().' / '.$pj->get_y().' - distance : '.$distance;
	  $div2->add( new interf_bal_smpl('span', $txt, 'pos_pj') );
		// buffs
	  $buffs = $div2->add( new interf_bal_cont('div',  'buffs_pj') );
		if( $this->pj->get_groupe() && $this->pj->get_groupe() == $this->perso->get_groupe() )
		{
	  	$buffs->add( new interf_liste_buff($this->pj, false) );
		}
	  $buffs->add( new interf_liste_buff($this->pj, true) );
	  
	  // description
	  $portrait = array_key_exists(19, $bonus) && check_affiche_bonus($bonus[19], $this->perso, $this->pj);
	  $sexe = array_key_exists(12, $bonus) && check_affiche_bonus($bonus[12], $this->perso, $this->pj);
	  $invent = array_key_exists(20, $bonus) && check_affiche_bonus($bonus[20], $this->perso, $this->pj) &&  $this->perso->get_id() != $pj->get_id();
	  $carac = array_key_exists(23, $bonus) && check_affiche_bonus($bonus[23], $this->perso, $this->pj) &&  $this->perso->get_id() != $pj->get_id();
	  $titres = array_key_exists(15, $bonus) && check_affiche_bonus($bonus[15], $this->perso, $this->pj);
	  $descr = array_key_exists(16, $bonus) && check_affiche_bonus($bonus[16], $this->perso, $this->pj);
	  if($portrait || $sexe || $invent || $carac || $titres || $descr)
	  {
			$rp_pj = $this->add( new interf_bal_cont('div', 'rp_pj', 'info_case') );
			if( $portrait || $invent || $carac )
			{
				$rp_gauche = $rp_pj->add( new interf_bal_cont('div', 'rp_gauche') );
				if( $portrait && $bonus_total[19]['valeur'] )
				{
					$div_img = $rp_gauche->add( new interf_bal_cont('div') );
					$div_img->add( new interf_img('image/avatar/'.$bonus_total[19]['valeur']) );
				}
				if( $carac )
				{
					$div_carac = $rp_gauche->add( new interf_bal_cont('div', 'rp_fiche') );
					$lien_carac = $div_carac->add( new interf_lien('', '', false, 'icone icone-perso') );
					$lien_carac->set_tooltip('Accès à la fiche de ce personnage', 'right', '#contenu');
				}
				if( $invent )
				{
					$div_invent = $rp_gauche->add( new interf_bal_cont('div', 'rp_invent') );
					$lien_invent = $div_invent->add( new interf_lien('', '', false, 'icone icone-inventaire') );
					$lien_invent->set_tooltip('Accès à l\'inventaire de ce personnage', 'right', '#contenu');
				}
			}
			if( $sexe && $bonus_total[12]['valeur'] )
			{
				$rp_haut = $rp_pj->add( new interf_bal_cont('div', 'rp_haut') );
				switch( $bonus_total[12]['valeur'] )
				{
				case 1:
					$rp_haut->add( new interf_txt('Homme') );
					break;
				case 2:
					$rp_haut->add( new interf_txt('Femme') );
					break;
				}
			}
			$rp_texte = 	$rp_pj->add( new interf_bal_cont('div', 'rp_texte') );
			if( $descr )
			{
				$options = texte::html;
				if( array_key_exists(24, $bonus) && check_affiche_bonus($bonus[24], $this->perso, $this->pj) )
					$options |= texte::bbcode;
				$texte = new texte($bonus_total[16]['valeur']);
				$rp_texte->add( new interf_bal_smpl('div', $texte->parse(), 'rp_descr') );
			}
			if( $titres )
			{
				$titres = recup_titre_honorifique($this->perso->get_id());
				if( !empty($titres) )
				{
					$p_titres = $rp_texte->add( new interf_bal_cont('p', 'rp_titres') );
					$p_titres->add( new interf_bal_smpl('h5', 'Titres') );
					$ul_titres = $p_titres->add( new interf_bal_cont('ul') );
					foreach($titres as $titre)
						$ul_titres->add( new interf_bal_smpl('li', $titre) );
				}
				$achievements = $this->perso->get_achievement();
				if( !empty($achievements) )
				{
					$p_achiev = $rp_texte->add( new interf_bal_cont('div', 'rp_achiev') );
					$p_achiev->add( new interf_bal_smpl('h5', 'Achievements') );
					$ul_achiev = $p_achiev->add( new interf_bal_cont('ul') );
					foreach($achievements as $achiev)
						$ul_achiev->add( new interf_bal_smpl('li', $achiev['nom']) );
				}
			}
		}
	}
}

?>