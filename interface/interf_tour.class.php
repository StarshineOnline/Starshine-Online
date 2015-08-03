<?php
/**
 * @file interf_infos_case.class.php
 * Permet l'affichage des informations d'une tour.
 */
 
         

class interf_tour extends interf_batiment
{
	protected $distance;
	protected $perso;
	protected $tour;
	
	function __construct(&$tour)
	{
		parent::__construct($tour, 'tour.php');
		$this->tour = &$tour;
		$this->perso = &joueur::get_perso();
		$batiment = $tour->get_def();
		$this->distance = $batiment->get_bonus('batiment_vue');
		
		// Icone et titre
		//$this->set_img_centre($tour->get_image(), 'tour.php?id_construction='.$tour->get_id(), $batiment->get_nom());
		//$this->barre_haut->add( new interf_txt($tour->get_nom()) );

		// Jauges
		//$this->set_jauge_ext($tour->get_hp(), $batiment->get_hp(), 'hp', 'HP : ');
		$this->set_jauge_int($this->distance, 12, 'avance', 'Distance de vue : ');
		
		$this->centre->add( new interf_bal_smpl('p', 'Position - X : '.$tour->get_x().' - Y : '.$tour->get_y()) );
		$this->aff_persos();
		$this->aff_batiments();
	}
	
	/// Affiche les personnages (PJs)
	function aff_persos()
	{
		global $Gtrad, $Trace, $Tclasse, $db;
		$div = $this->centre->add( new interf_bal_cont('div', 'liste_gauche', 'liste_case') );
		$div->add( new interf_bal_smpl('span', 'Personnages', false, 'xsmall') );
		$lst = $div->add( new interf_bal_cont('ul') );
		
		$royaume = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
		
		$distance = $this->distance;
		if($buff=$this->tour->get_buff_actif('buff_vision'))
			$distance += $buff->get_effet();
		if($buff=$this->tour->get_buff_actif('debuff_vision'))
			$distance -= $buff->get_effet();
		
    /// @todo à améliorer
    $requete = 'SELECT *, GREATEST(ABS('.$this->tour->get_x().' - CAST(x AS SIGNED)), ABS('.$this->tour->get_y().' - CAST(y AS SIGNED))) as distance FROM perso AS p INNER JOIN diplomatie AS d ON p.race = d.race WHERE x BETWEEN '.($this->tour->get_x() - $distance).' AND '.($this->tour->get_x() + $distance).' AND y BETWEEN '.($this->tour->get_y() - $distance).' AND '.($this->tour->get_y() + $distance).' AND statut="actif" ORDER BY distance ASC, d.'.$this->perso->get_race().' DESC, level DESC';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
		{
      $pj = new perso($row);
			if( $pj->get_id() == $this->perso->get_id() || $pj->is_buff('potion_discretion') )
				continue;
			$li = $lst->add( new interf_bal_cont('li', false, 'info_case pj') );
			$lien = $li->add( new interf_lien_cont('infoperso.php?id='.$pj->get_id(), false, 'info_elt') );
    	/// @todo à améliorer
      // Cache sa classe ?
      if( $pj->get_cache_classe() == 2 )
        $classe = 'combattant';
      else if($pj->get_cache_classe() == 1 && $pj->get_race() != $this->perso->get_race())
        $classe = 'combattant';
      else
        $classe = $pj->get_classe();
      // Camouflage
      $pj->check_specials();
      $race = $pj->get_race_a();
      $img = 'image/personnage/'.$race.'/'.$race.'_'.$Tclasse[$classe]['type'].'.png';
			$lien->add( new interf_img(/*$pj->get_image()*/$img) );
			$diplo = 'diplo'.$royaume->get_diplo( $pj->get_race() );
			$diplo_txt = $Gtrad[$diplo];
			if($diplo == 127)
			{
				$amende = recup_amende( $pj->get_id() );
				if($amende)
				{
					switch($amende['statut'])
					{
					case 'bandit' :
						$diplo = 5;
						$diplo_txt = 'Bandit';
						break;
					case 'criminel' :
						$diplo = 10;
						$diplo_txt = 'Criminel';
						break;
					}
				}
			}
			$facteur_honneur = max($diplo * 0.2 - 0.8, 0);
			if( $pj->est_mort() )
			{
				$diplo .= ' mort';
				$div_mort = $lien->add( new interf_bal_cont('div', false, 'jauge_case') );
				$div_mort->add( new interf_bal_cont('span', false, 'icone icone-mort') );
				$div_mort->set_tooltip('Ce personnage est mort', 'bottom');
			}
			$nom = $lien->add( new interf_bal_smpl('span', $pj->get_nom(), false, $diplo) );
			$nom->set_tooltip($Gtrad[$pj->get_race()].($pj->get_level()?'':' (PNJ)').' : '.$diplo_txt.' - honneur/réputation : '.($facteur_honneur * 100).'%', 'bottom');
			$lien->add( new interf_bal_smpl('div', $pj->get_x().'/'.$pj->get_y().' - dist. : '.$row['distance'], false, 'xsmall') );
		}
	}
	
	/// Affiche les bâtiments
	function aff_batiments()
	{
		global $db, $Gtrad;
		$div = $this->centre->add( new interf_bal_cont('div', 'liste_droite', 'liste_case') );
		$div->add( new interf_bal_smpl('span', 'Bâtiments', false, 'xsmall') );
		$lst = $div->add( new interf_bal_cont('ul') );
		
	
		// Recherche des placements
		/// @todo à améliorer
		$requete = 'SELECT p.nom, p.x, p.y, p.hp, p.debut_placement, p.fin_placement, p.royaume, b.nom as nom_bat, b.image, b.type, b.hp AS hp_max, r.race, d.'.$this->perso->get_race().' AS diplo, GREATEST(ABS('.$this->tour->get_x().' - CAST(p.x AS SIGNED)), ABS('.$this->tour->get_y().' - CAST(p.y AS SIGNED))) as distance, quete FROM	placement p INNER JOIN batiment b ON b.id = p.id_batiment INNER JOIN royaume r ON r.id = p.royaume INNER JOIN diplomatie AS d ON r.race = d.race WHERE x BETWEEN '.($this->tour->get_x() - $this->distance).' AND '.($this->tour->get_x() + $this->distance).' AND y BETWEEN '.($this->tour->get_y() - $this->distance).' AND '.($this->tour->get_y() + $this->distance).' ORDER BY distance ASC, d.'.$this->perso->get_race().' DESC, b.type, b.nom';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
		{
			if( $row['quete'] && !count(quete_perso::create(array('id_etape', 'id_perso'), array($row['quete'], $perso->get_id()))) )
    		continue;
			$li = $lst->add( new interf_bal_cont('li', false, 'info_case placement') );
			$li->add( new interf_img(placement::calc_image($row['image'], $row['type'], $row['debut_placement'], $row['fin_placement'], $row['royaume'])) );
			$avanc = $li->add( new interf_jauge_bulle(false, time() - $row['debut_placement'], $row['fin_placement'] - $row['debut_placement'], false, 'avance', false, 'jauge_case') );
			$avanc->set_tooltip(transform_sec_temp($row['fin_placement'] - time()).' avant fin de construction', 'bottom', '#contenu');
			$li->add( new interf_jauge_bulle('HP', $row['hp'], $row['hp_max'], false, 'hp', false, 'jauge_case') );
			$diplo = 'diplo'.$row['diplo'];
			$nom = $li->add( new interf_bal_smpl('span', $row['nom'], false, $diplo) );
			$nom->set_tooltip($row['nom_bat'].' '.$Gtrad[$row['race']].' − '.$Gtrad[$diplo], 'bottom');
			$li->add( new interf_bal_smpl('div', $row['x'].'/'.$row['y'].' - dist. : '.$row['distance'], false, 'xsmall') );
		}
	
		// Bâtiments construits
		$requete = 'SELECT c.id, c.nom, c.x, c.y, c.hp, b.nom as nom_bat, b.image, b.type, b.hp AS hp_max, r.race, d.'.$this->perso->get_race().' AS diplo, GREATEST(ABS('.$this->tour->get_x().' - c.x), ABS('.$this->tour->get_y().' - c.y)) as distance FROM	construction c INNER JOIN batiment b ON b.id = c.id_batiment INNER JOIN royaume r ON r.id = c.royaume INNER JOIN diplomatie AS d ON r.race = d.race WHERE x BETWEEN '.($this->tour->get_x() - $this->distance).' AND '.($this->tour->get_x() + $this->distance).' AND y BETWEEN '.($this->tour->get_y() - $this->distance).' AND '.($this->tour->get_y() + $this->distance).' ORDER BY distance ASC, d.'.$this->perso->get_race().' DESC, b.type, b.nom';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
		{
			if( $row['id'] == $this->tour->get_id() )
				continue;
			$li = $lst->add( new interf_bal_cont('li', false, 'info_case construction') );
			$li->add( new interf_img(construction::make_url_image($row['image'])) );
			$li->add( new interf_jauge_bulle('HP', $row['hp'], $row['hp_max'], false, 'hp', false, 'jauge_case') );
			$diplo = 'diplo'.$row['diplo'];
			$nom = $li->add( new interf_bal_smpl('span', $row['nom'], false, $diplo) );
			$nom->set_tooltip($row['nom_bat'].' '.$Gtrad[$row['race']].' − '.$Gtrad[$diplo], 'bottom');
			$li->add( new interf_bal_smpl('div', $row['x'].'/'.$row['y'].' - dist. : '.$row['distance'], false, 'xsmall') );
		}
	}
}

?>