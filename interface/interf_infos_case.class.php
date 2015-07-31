<?php
/**
 * @file interf_infos_case.class.php
 * Permet l'affichage des informations d'une case en fonction du joueur.
 */
 
include_once(root.'inc/diplo.inc.php');

class interf_infos_case extends interf_cont
{
	protected $case;
	protected $distance;
	protected $perso;
	protected $reduc_pa;
	protected $royaume;
	
	function __construct(&$case, $distance, $id_case, $reponse)
	{
		$this->perso = &joueur::get_perso();
		$this->case = &$case;
		$this->distance = $distance;
		$this->royaume = new royaume($case->get_royaume());
		
		// réduction du coût en PA des attaques
		$this->reduc_pa = $this->perso->is_buff('buff_rapidite') ? $this->perso->get_buff('buff_rapidite', 'effet') : 0;
		if( $this->perso->is_buff('debuff_ralentissement') )
			$this->reduc_pa -= $this->perso->get_buff('debuff_ralentissement', 'effet');
		
		$this->aff_descr();
		if( $distance <= 1 )
			$this->aff_texte($id_case, $reponse);
		$this->aff_batiments();
		$this->aff_persos();
		$this->aff_monstres();
	}
	
	/// Affiche la description de la case (type de terrain, appartenance, …)
	function aff_descr()
	{
		global $Trace, $Gtrad;
		$type_terrain = type_terrain($this->case->get_info());
		$coutpa = cout_pa($type_terrain[0], $this->perso->get_race());
		$coutpa_base = $coutpa;
		$coutpa_diagonale = cout_pa2($coutpa, $this->perso, $this->case, true);
		$coutpa = cout_pa2($coutpa, $this->perso, $this->case, false);
			
		$infos_cases = $this->add( new interf_bal_cont('div', 'descr_case', 'info_case') );
		if( $this->distance == 1 && $coutpa_base <= 49 )
		{
			$lien = $infos_cases->add( new interf_lien_cont('', 'deplacer') );
			$lien->set_tooltip('Aller sur cette case', 'bottom', '#information');
			$lien->add( new interf_bal_smpl('p', null, false, 'icone icone-aller-a') );
			if( $this->case->get_x() == $this->perso->get_x() || $this->case->get_y() == $this->perso->get_y() )
				$lien->add( new interf_bal_smpl('p', 'PA : '.$coutpa, false, 'xsmall') );
			else
				$lien->add( new interf_bal_smpl('p', 'PA : '.$coutpa_diagonale, false, 'xsmall') );
		}
		$div = $infos_cases->add( new interf_bal_cont('div') );
		$titre_case = $this->case->get_royaume() ? $this->royaume->get_nom() : 'Neutre';
		//Si c'est la capitale
		/*if($this->case->get_x() == $Trace[$R->get_race()]['spawn_x'] && $this->case->get_y() == $Trace[$R->get_race()]['spawn_y'])
			echo $titre_case .= ' − Capitale '.$R->get_capitale();
		else*/ if( $this->case->get_type() == 3 ) // ou un point exceptionnel
			echo $titre_case .= ' − Point exceptionnel';
		$div->add( new interf_bal_smpl('h4', $titre_case) );
		if( $this->case->get_royaume() )
		{
			$par1 = $div->add( new interf_bal_cont('p') );
			$par1->add( new interf_bal_smpl('span', $Gtrad[$this->royaume->get_race()], 'royaume') );
			$par1->add( new interf_txt(' − ') );
			$par1->add( new interf_bal_smpl('span', $Gtrad['diplo'.$this->royaume->get_diplo($this->perso->get_race())], 'diplomatie') );
			$par1->add( new interf_txt(' − Taxe : ') );
			$par1->add( new interf_bal_smpl('span', $this->royaume->get_taxe_diplo($this->perso->get_race()).'%', 'taxe') );
		}
		$par2 = $div->add( new interf_bal_cont('p') );
		$par2->add( new interf_bal_smpl('span', $type_terrain[1], 'terrain') );
		$par2->add( new interf_txt(' − ') );
		if( $coutpa_base > 49 ) 
			$par2->add( new interf_bal_smpl('span', 'Infranchissable', 'coup_pa', 'infranch') );
		else
		{
			$par2->add( new interf_bal_smpl('span', $coutpa.' PA de déplacement', 'coup_pa') );
			$par2->add( new interf_bal_smpl('span', '(en diagonale = '.$coutpa_diagonale.' PA)', 'coup_pa_diag', 'xsmall') );
		}
	}
	
	/// @todo remplacer les entrées/sorties de donjons par les messages de cases
	
	/// Afficher le texte d'une case
	function aff_texte($id_case, $reponse)
	{
		global $db;
		// Informations case spéciale
		$requete = 'SELECT * from map_event WHERE x = '.$this->case->get_x(). ' AND y = '.$this->case->get_y();
		$req = $db->query($requete);
		if ($db->num_rows > 0)
		{
			$div = $this->add( new interf_bal_cont('div', false, 'info_case') );
			$row = $db->read_array($req);
			$div->add( new interf_bal_smpl('h4', $row['titre']) );
			$texte = new texte($row['description'], texte::cases);
      $texte->set_liens('informationcase.php?case='.$id_case, $id_case, true);
      $texte->set_id_objet('C'.$id_case);
			$div->add( new interf_bal_smpl('div', $texte->parse($reponse)) );
			if($row['action'] != '' && $this->distance == 0)
			{
				$div->add( new interf_lien($row['action'], 'map_event.php?poscase='.$id_case) );
			}
		}
	}
	
	/// Affiche les bâtiments
	function aff_batiments()
	{
		global $Gtrad, $G_PA_attaque_batiment;
		// Bâtiments en construction
		$placements = placement::create(array('x', 'y'), array($this->case->get_x(), $this->case->get_y()));
		foreach($placements as $plac)
		{
			$bat = $plac->get_def();
    	if( $bat->get_quete() && !count(quete_perso::create(array('id_etape', 'id_perso'), array($bat->get_quete(), $perso->get_id()))) )
    			continue;
			$royaume = new royaume(  $plac->get_royaume() );
			
			$div = $this->add( new interf_bal_cont('div', false, 'info_case placement') );
			if( $this->perso->get_race() != $royaume->get_race() )
			{
				if( $this->perso->peut_attaquer() )
				{
					if( $this->perso->nb_pet() > 0 && $this->perso->get_pet() && $this->distance <= $this->perso->get_distance_tir() )
					{
						$att_pet = $div->add( new interf_lien('', 'attaque.php?id_batiment='.$plac->get_id().'&amp;type=batiment&amp;table=placement&amp;pet', false, 'icone icone-chien') );
						$att_pet->set_tooltip('Attaquer avec votre créature ('.($G_PA_attaque_batiment - $reduction_pa).' PA)', 'bottom', '#information');
					}
					if( $this->distance <= $this->perso->get_distance_tir() )
					{
						$att = $div->add( new interf_lien('', 'attaque.php?id_batiment='.$plac->get_id().'&amp;type=batiment&amp;table=placement', false, 'icone icone-attaque') );
						$att->set_tooltip('Attaquer ('.($G_PA_attaque_batiment - $reduction_pa).' PA)', 'bottom', '#information');
					}
				}
			}
			else if( $plac->get_fin_placement() - $plac->get_debut_placement() > $bat->get_temps_construction_min() && $this->distance == 0 )
			{
				$acc = $div->add( new interf_lien('', 'architecture.php?action=construit&type=placement&id='.$plac->get_id(), false, 'icone icone-architecture') );
				$acc->set_tooltip('Accélérer (30 PA)', 'bottom', '#information');
			}
			$diplo = 'diplo'.$royaume->get_diplo( $this->perso->get_race() );
			$div2 = $div->add( new interf_bal_cont('div') );
			$div2->add( new interf_img($plac->get_image()) );
			//$div2->add( new interf_bal_smpl('span', $plac->get_nom().' ('.$bat->get_nom().' '.$Gtrad[$this->royaume->get_race()].')', false, '') );
			$nom = $div2->add( new interf_bal_cont('span') );
			$nom->add( new interf_bal_smpl('span', $plac->get_nom()) );
			$nom->add( new interf_txt(' − ') );
			$nom->add( new interf_bal_smpl('span', $Gtrad[$royaume->get_race()], false, $diplo) );
			$nom->set_tooltip($bat->get_nom().' − '.$Gtrad[$diplo], 'bottom');
			$avanc = $div2->add( new interf_jauge_bulle(false, time() - $plac->get_debut_placement(), $plac->get_fin_placement() - $plac->get_debut_placement(), false, 'avance', false, 'jauge_case') );
			$avanc->add( new interf_bal_smpl('div', round((time() - $plac->get_debut_placement()) / ($plac->get_fin_placement() - $plac->get_debut_placement()) * 100).'%', false, 'bulle_valeur') );
			$avanc->set_tooltip(transform_sec_temp($plac->get_fin_placement() - time()).' avant fin de construction', 'bottom', '#contenu');
			$hp = $div2->add( new interf_jauge_bulle('HP', $plac->get_hp(), $bat->get_hp(), false, 'hp', false, 'jauge_case') );
			$hp->add( new interf_bal_smpl('div', round($plac->get_hp() / $bat->get_hp() * 100).'%', false, 'bulle_valeur') );
		}
		
		// Bâtiments construits
		$constructions = construction::create(array('x', 'y'), array($this->case->get_x(), $this->case->get_y()));
		foreach($constructions as $constr)
		{
			$bat = $constr->get_def();
			$royaume = new royaume(  $constr->get_royaume() );
			
			$div = $this->add( new interf_bal_cont('div', false, 'info_case construction') );
			$lien = false;
			if( $this->perso->get_race() != $royaume->get_race() )
			{
				/// @todo à améliorer
				if( $this->perso->peut_attaquer() && $constr->get_type() != 'bourg' )
				{
					if( $this->perso->nb_pet() > 0 && $this->perso->get_pet() && $this->distance <= $this->perso->get_distance_tir() )
					{
						$att_pet = $div->add( new interf_lien('', 'attaque.php?id_batiment='.$constr->get_id().'&amp;type=batiment&amp;table=construction&amp;pet', false, 'icone icone-chien') );
						$att_pet->set_tooltip('Attaquer avec votre créature ('.($G_PA_attaque_batiment - $reduction_pa).' PA)', 'bottom', '#information');
					}
					if( $this->distance <= $this->perso->get_distance_tir() )
					{
						$att = $div->add( new interf_lien('', 'attaque.php?id_batiment='.$constr->get_id().'&amp;type=batiment&amp;table=construction', false, 'icone icone-attaque') );
						$att->set_tooltip('Attaquer ('.($G_PA_attaque_batiment - $reduction_pa).' PA)', 'bottom', '#information');
					}
				}
			}
			else
			{
				if( $constr->get_hp() < $bat->get_hp() && $this->distance == 0 )
				{
					$repar = $div->add( new interf_lien('', 'architecture.php?action=repare&type=construction&id='.$constr->get_id(), false, 'icone icone-architecture') );
					$repar->set_tooltip('Réparer (30 PA)', 'bottom', '#information');
				}
				/// TODO : à améliorer ?
				switch( $bat->get_type() )
				{
				case 'fort':
				case 'bourg':
					if( $this->distance == 0 )
						$lien = 'bourg_fort.php?id_construction='.$constr->get_id();
					break;
				case 'tour':
					if( $this->distance == 0 )
						$lien = 'tour.php?id_construction='.$constr->get_id();
					break;
				case 'arme_de_siege':
					$lien = 'arme_de_siege.php?id_construction='.$constr->get_id();
					break;
				}
			}
			$diplo = 'diplo'.$royaume->get_diplo( $this->perso->get_race() );
			if( $lien )
				$div2 = $div->add( new interf_lien_cont($lien, false, 'info_elt') );
			else
				$div2 = $div->add( new interf_bal_cont('div') );
			$div2->add( new interf_img($constr->get_image()) );
			$hp = $div2->add( new interf_jauge_bulle('HP', $constr->get_hp(), $bat->get_hp(), false, 'hp', false, 'jauge_case') );
			$hp->add( new interf_bal_smpl('div', round($constr->get_hp() / $bat->get_hp() * 100).'%', false, 'bulle_valeur') );
			$nom = $div2->add( new interf_bal_cont('span') );
			$nom->add( new interf_bal_smpl('span', $constr->get_nom()) );
			$nom->add( new interf_txt(' − ') );
			$nom->add( new interf_bal_smpl('span', $Gtrad[$royaume->get_race()], false, $diplo) );
			$nom->set_tooltip($bat->get_nom().' − '.$Gtrad[$diplo], 'bottom');
			$div->add( new interf_liste_buff($constr, false) );
			$div->add( new interf_liste_buff($constr, true) );
		}
	}
	
	/// Affiche les personnages (PJ & PNJ)
	function aff_persos()
	{
		global $Gtrad, $Trace, $Tclasse, $db;
		$div = $this->add( new interf_bal_cont('div', 'liste_gauche', 'liste_case') );
		$div->add( new interf_bal_smpl('span', 'Personnages', false, 'xsmall') );
		$lst = $div->add( new interf_bal_cont('ul') );
		
		//$pnjs = pnj::create(array('x', 'y'), array($this->case->get_x(), $this->case->get_y()));
		$pnjs = pnj::get_valeurs('id, nom, image', 'x = '.$this->case->get_x().' AND y = '.$this->case->get_y());
		if( $pnjs )
		{
			foreach($pnjs as $pnj)
			{
				$li = $lst->add( new interf_bal_cont('li', false, 'info_case pnj') );
				if( $this->distance )
					$lien = $li->add( new interf_bal_cont('div') );
				else
					$lien = $li->add( new interf_lien_cont('pnj.php?id='.$pnj['id'], false, 'info_elt') );
				$lien->add( new interf_img('image/pnj/'.$pnj['image'].'.png') );
				$nom = $lien->add( new interf_bal_smpl('span', $pnj['nom']) );
				$nom->set_tooltip('PNJ', 'bottom');
			}
		}
		
		$royaume = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
    /// @todo à améliorer
    $requete = 'SELECT * FROM perso AS p INNER JOIN diplomatie AS d ON p.race = d.race WHERE x = '.$this->case->get_x().' AND y = '.$this->case->get_y().' AND statut="actif" ORDER BY d.'.$this->perso->get_race().' DESC, level DESC';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
		{
      $pj = new perso($row);
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
			if( $pj->get_id() == $this->perso->get_id() )
			{
				$nom = $lien->add( new interf_bal_smpl('span', $pj->get_nom()) );
				$nom->set_tooltip('Vous', 'bottom');
			}
			else
			{
				$diplo = $royaume->get_diplo( $pj->get_race() );
				$diplo_classe= 'diplo'.$diplo;
				$diplo_txt = $Gtrad[$diplo_classe];
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
							$diplo_classe = 'diplo5';
							break;
						case 'criminel' :
							$diplo = 10;
							$diplo_txt = 'Criminel';
							$diplo_classe = 'diplo10';
							break;
						}
					}
				}
				$facteur_honneur = $diplo == 127 ? 0 : max($diplo * 0.2 - 0.8, 0);
				if( $pj->est_mort() )
				{
					$diplo .= ' mort';
					$div_mort = $lien->add( new interf_bal_cont('div', false, 'jauge_case') );
					$div_mort->add( new interf_bal_cont('span', false, 'icone icone-mort') );
					$div_mort->set_tooltip('Ce personnage est mort', 'bottom');
				}
				$nom = $lien->add( new interf_bal_smpl('span', $pj->get_nom(), false, $diplo_classe) );
				$nom->set_tooltip($Gtrad[$pj->get_race()].($pj->get_level()?'':' (PNJ)').' : '.$diplo_txt.' − honneur/réputation : '.($facteur_honneur * 100).'%', 'bottom');
			}
		} 
	}
	
	/// Affiche les monstres
	function aff_monstres()
	{
		global $db;
		$div = $this->add( new interf_bal_cont('div', 'liste_droite', 'liste_case') );
		$div->add( new interf_bal_smpl('span', 'Monstres', false, 'xsmall') );
		$lst = $div->add( new interf_bal_cont('ul') );
		
		
		$dresse = $this->perso->is_buff('dressage') ? $this->perso->get_buff('dressage', 'effet2') : false;
			
		/*$monstres = map_monstre::create(array('x', 'y'), array($this->case->get_x(), $this->case->get_y()));
		foreach($monstres as $m)*/
    /// @todo à améliorer
    $requete = 'SELECT mm.id, x, y, lib, nom, level, mm.hp as hp, m.hp as hp_max, affiche, quete FROM map_monstre AS mm INNER JOIN monstre AS m ON mm.type = m.id WHERE x = '.$this->case->get_x().' AND y = '.$this->case->get_y().' ORDER BY ABS(CAST(level AS SIGNED) - '.$this->perso->get_level().') ASC, level DESC';
    $req = $db->query($requete);
    while($row = $db->read_object($req))
		{
			// Monstre spécifiques à une quête
			/// @todo à améliorer
    	if( $row->quete && !count(quete_perso::create(array('id_etape', 'id_perso'), array($row->quete, $perso->get_id()))) )
    			continue;
			//$monstre = $m->get_def();
			$li = $lst->add( new interf_bal_cont('li', false, 'info_case monstre') );
			$lien = $li->add( new interf_lien_cont('info_monstre.php?id='.$row->id, false, 'info_elt') );
			/// @todo à améliorer
			$image = 'image/monstre/'.$row->lib;
			$image .= file_exists($image.'_low.png') ? '_low.png' : '.png';
			$lien->add( new interf_img($image) );
			/// @todo à améliorer
			$niveau = $row->level > 0 ? $row->level : 1;
			$nbr_barre_total = min(max(ceil($this->perso->get_survie() / $niveau), 0), 100);
			$nbr_barre = round($row->hp / $row->hp_max * $nbr_barre_total);
			$longueur = max(round(100 * ($nbr_barre / $nbr_barre_total), 2), 0);
			$fiabilite = round((100 / $nbr_barre_total) / 2, 2);
			$jauge = $lien->add( new interf_jauge_bulle(false, $nbr_barre, $nbr_barre_total, false, 'hp', false, 'jauge_case') );
			$jauge->set_tooltip('HP : '.$longueur.'% ± '.$fiabilite.'%', 'bottom', '#contenu');
			$diff_niv = $row->affiche=='h' ? 0 : $niveau - $this->perso->get_level();
			$classe = 'niv'.min(max($diff_niv,-5),5);
			if( $row->affiche=='h' )
			{
				$texte = 'Ce monstre a un niveau inconnu';
				$classe = '';
			}
			else
			{
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
			}
			if( $dresse == $row->id )
			{
				$texte .= ' − vous dressez ce monstre';
				$classe .= ' dresse';
			}
			$nom = $lien->add( new interf_bal_smpl('span', $row->nom, false, $classe) );
			$nom->set_tooltip($texte, 'bottom');
		}
	}
}
?>