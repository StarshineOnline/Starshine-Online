<?php
/**
 * @file interf_dressage.class.php
 * Classes pour le dressage
 */

/// Classe pour la gestion du dressage
class interf_dressage extends interf_cont
{
	protected $perso;
	protected $actions;
	function __construct(&$perso, $actions)
	{
		global $G_url, $db;
		$this->perso = $perso;
		$this->actions = $actions;
		// Tri des créatures
		$creatures = $perso->get_pets(true);
		$princ = null;
		$second = array();
		foreach($creatures as $creat)
		{
			if( $creat->get_principale() == 1 )
				$princ = &$creat;
			else
				$second[] = &$creat;
		}
		
		// Créature principale
		if( $princ )
		{
			$this->add( new interf_bal_smpl('h4', 'Créature principale') );
			$this->aff_creature($princ);
		}
		// Autres créatures
		if( count($second) )
		{
			$this->add( new interf_bal_smpl('h4', count($second) > 1 ? 'Autres créatures' : 'Autre créature') );
			foreach($second as $creat)
			{
				$this->aff_creature($creat);
			}
		}
		
		// Dresssages
		if( $perso->is_buff('dressage') )
		{
			$this->add( new interf_bal_smpl('h4', 'Vous dressez une créature') );
			$monstre = new map_monstre( $perso->get_buff('dressage', 'effet2') );
			$div_btns = $this->add( new interf_bal_cont('div', false, 'btn-group') );
			$annuler = $div_btns->add( new interf_bal_smpl('a', 'Annuler', false, 'btn btn-default') );
			$annuler->set_attribut('href', $G_url->get('action', 'annuler'));
			$annuler->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez vous arrêtez le dressage ?\');');
	    $continuer = $div_btns->add( new interf_lien('Continuer', 'dressage.php?id='.$monstre->get_id(), false, 'btn btn-primary') );
	    $continuer->set_tooltip('Continuez le dressage de '.$monstre->get_nom());
		}
		else
		{
			$this->add( new interf_bal_smpl('h4', 'Créatures que vous pouvez dresser sur la case ') );
			$this->add( new interf_bal_smpl('p', 'Niveau maximal : '.$perso->max_dresse(), false, 'small') );
			/// @todo passer à l'objet
			$requete = 'SELECT mm.id, m.nom, mm.hp, m.level, m.lib, m.hp AS hp_max FROM map_monstre mm INNER JOIN monstre m ON mm.type = m.id WHERE mm.x = '.$perso->get_x().' AND mm.y = '.$perso->get_y().' AND m.affiche != \'h\' AND m.level <= '.$perso->max_dresse().' ORDER BY level ASC, nom ASC, id ASC';
			$res = $db->query($requete);
			if( $db->num_rows > 0 )
			{
				$ul = $this->add( new interf_bal_cont('ul', 'creat_dresse') );
				while($row = $db->read_object($res))
				{
					$li = $ul->add( new interf_bal_cont('li', false, 'info_case') );
					$dresse = $li->add( new interf_lien('', 'dressage.php?id='.$row->id, false, 'icone icone-lapin') );
					$dresse->set_tooltip('Dresser.');
					$lien = $li->add( new interf_lien_cont('info_monstre.php?id='.$row->id) );
					/// @todo à améliorer
					$niveau = $row->level > 0 ? $row->level : 1;
					$nbr_barre_total = min(max(ceil($this->perso->get_survie() / $niveau), 0), 100);
					$nbr_barre = round($row->hp / $row->hp_max * $nbr_barre_total);
					$longueur = max(round(100 * ($nbr_barre / $nbr_barre_total), 2), 0);
					$fiabilite = round((100 / $nbr_barre_total) / 2, 2);
					$jauge = $lien->add( new interf_jauge_bulle(false, $nbr_barre, $nbr_barre_total, false, 'hp', false, 'jauge_case') );
					$jauge->set_tooltip('HP : '.$longueur.'% ± '.$fiabilite.'%', 'bottom', '#contenu');
					/// @todo à améliorer
					$image = 'image/monstre/'.$row->lib;
					$image .= file_exists($image.'_low.png') ? '_low.png' : '.png';
					$lien->add( new interf_img($image) );
					$nom = $lien->add( new interf_bal_smpl('span', $row->nom) );
				}
			}
		}
	}
	protected function aff_creature(&$creature)
	{
		global $G_url, $Gtrad;
		$monstre = $creature->get_def();
		$div = $this->add( new interf_bal_cont('div', 'creat_'.$creature->get_id(), 'info_case') );
		$G_url->add('id', $creature->get_id());
		if( $this->actions )
		{
			$suppr = $div->add( new interf_lien('', $G_url->get('action', 'supprimer'), false, 'icone icone-poubelle') );
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment supprimer cette créature ?\');');
			$suppr->set_tooltip('Supprimer');
			if( $creature->get_hp() < $monstre->get_hp() || $creature->get_mp() < $creature->get_mp_max() )
			{
				$soin = $div->add( new interf_lien_cont($G_url->get('action', 'soin'), false, 'icone') );
				$soin->add( new interf_bal_smpl('span', '', false, 'icone icone-soin') );
				$cout_hp = round($this->perso->get_hp_max() / 10);
				$soin->add( new interf_bal_smpl('span', '-'.$cout_hp.' HP', false, 'xsmall') );
				$soin->set_tooltip('Soigner, puissance : '.$this->perso->soin_pet().', vous coûte '.$cout_hp.' HP et 1 PA.');
			}
			if( $creature->get_principale() != 1 )
			{
				$princ = $div->add( new interf_lien('', $G_url->get('action', 'principale'), false, 'icone icone-favoris') );
				$princ->set_tooltip('Définir comme créature principale.');
			}
			$modif = $div->add( new interf_bal_smpl('a', '', false, 'icone icone-modifier') );
			$modif->set_tooltip('Modifier le nom');
			$modif->set_attribut('onclick', 'return modif_nom_creature('.$creature->get_id().');');
		}
		$infos = $div->add( new interf_lien('', $G_url->get('action', 'infos'), false, 'icone icone-info') );
		$infos->set_tooltip('Afficher les informations sur la créature.');
		$div_g = $div->add( new interf_bal_cont('div') );
		$div_g->add( new interf_jauge_bulle('MP', $creature->get_mp(), $creature->get_mp_max(), '%', 'mp', false, 'jauge_case') );
		$div_g->add( new interf_jauge_bulle('HP', $creature->get_hp(), $monstre->get_hp(), '%', 'hp', false, 'jauge_case') );
		$img = $div_g->add( new interf_img('image/monstre/'.$monstre->get_lib().'.png', $monstre->get_nom()) );
		$img->set_tooltip($monstre->get_nom());
		$div_g->add( new interf_bal_smpl('span', $creature->get_nom(), false, 'nom_creature') );
		$script = $div_g->add( new interf_bal_cont('div') );
		$script->add( new interf_txt('Script : ') );
		$script_attaque = recupaction_all($creature->get_action_a(), true);
		if( $script_attaque )
		{
			if( $this->actions )
				$script->add( new interf_lien($script_attaque['nom'], 'actions.php?creature='.$creature->get_id()) );
			else
				$script->add( new interf_txt($script_attaque['nom']) );
		}
		else
			$script->add( new interf_bal_smpl('span', 'défaut', false, 'sans_script') );
		if( $this->actions && $monstre->get_sort_dressage() )
		{
			$div_buffs = $div->add( new interf_bal_cont('div', false, 'creat_buffs') );
			$buffs = explode(';', $monstre->get_sort_dressage());
			foreach($buffs as $b)
			{
				$id_infos = 'info_'.$creature->get_id().'_'.$b;
				if( $b[0] == 's' || $b[0] == 'S' )
					$sortcomp = new sort_jeu( substr($b, 1) );
				else if( $b[0] == 'c' )
					$sortcomp = new comp_jeu( substr($b, 1) );
				$cout_mp = $sortcomp->get_mp();
				$cout_pa = $sortcomp->get_pa();
				if($this->perso->is_buff('buff_concentration', true))
					$cout_mp = ceil($cout_mp * (1 - ($this->perso->get_buff('buff_concentration','effet') / 100)));
				$cout_grp = round($cout_mp * 1.5);
				$div_b = $div_buffs->add( new interf_bal_cont('div', false, 'info_case') );
				$inf = $div_b->add( new interf_bal_smpl('a', '', false, 'icone icone-info') );
				$inf->set_tooltip('Afficher/masquer les informations');
				$inf->set_attribut('onclick', '$(\'#'.$id_infos.'\').slideToggle();');
				if( $b[0] == 's' )
				{
					$groupe = $div_b->add( new interf_lien_cont($G_url->get(array('action'=>'lancer_groupe', 'buff'=>$b)), false, 'icone') );
					$groupe->add( new interf_bal_smpl('span', '', false, 'icone icone-groupe') );
					$groupe->add( new interf_bal_smpl('span', $cout_grp.' MP', false, 'xsmall') );
					$groupe->set_tooltip('Lancer sur votre groupe ('.$cout_grp.' MP et '.$cout_pa.' PA)');
				}
				$lancer = $div_b->add( new interf_lien_cont($G_url->get(array('action'=>'lancer', 'buff'=>$b))) );
				$lancer->set_tooltip('Lancer');
				$lancer->add( new interf_img($sortcomp->get_image()) );
				$lancer->add( new interf_bal_smpl('span', $sortcomp->get_nom(), false, 'livre_nom') );
				$lancer->add( new interf_bal_smpl('span', $cout_pa.' PA − '.$cout_mp.' MP', false, 'xsmall') );
				$infos = $div_b->add( new interf_bal_cont('div', $id_infos, 'creat_buffs') );
				$infos->add( new interf_bal_smpl('p', $sortcomp->get_description(true)) );
				if( $b[0] == 'c' && $sortcomp->get_arme_requis() )
				{
					$armes = explode(';', $sortcomp->get_arme_requis());
					$armes = array_map(function ($a) { global $Gtrad; return $Gtrad[$a]; }, $armes);
					$infos->add( new interf_bal_smpl('p', 'Arme'.(count($armes)>1?'s':'').' : '.implode(', ', $armes)) );
				}
				$infos->add( new interf_bal_smpl('p', 'Durée : '.transform_min_temp($sortcomp->get_duree())) );
				$infos->set_attribut('style', 'display: none');
			}
		}
	}
}

?>