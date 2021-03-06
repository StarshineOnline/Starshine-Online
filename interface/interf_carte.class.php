<?php
/**
 * @file interf_carte.class.php
 * Classes pour la partie gauche de l'interface
 */  

/// Classe pour afficher la carte du jeu
class interf_carte extends interf_tableau
{
	const aff_royaumes = 0x1;
	const aff_atmosphere = 0x2;
	const aff_jour = 0x4;
	const aff_monstres = 0x8;
	const aff_pcb = 0x10;
	const aff_cbp = 0x20;
	const aff_cpb = 0x30;
	const aff_pnj = 0x40;
	const aff_ads = 0x80;
	const aff_diplo_af = 0x000;
	const aff_diplo_a = 0x100;
	const aff_diplo_p = 0x200;
	const aff_diplo_pd = 0x300;
	const aff_diplo_bt = 0x400;
	const aff_diplo_n = 0x500;
	const aff_diplo_mt = 0x600;
	const aff_diplo_g = 0x700;
	const aff_diplo_gd = 0x800;
	const aff_diplo_e = 0x900;
	const aff_diplo_ee = 0xa00;
	const aff_diplo_vr = 0xb00;
	const aff_diplo_sup = 0x1000;
	const act_sons = 0x2000;
	const aff_petit = 0x4000;
	const aff_restreint = 0x8000; // seulement batiments du royaume
	const aff_lien_gest = 0x10000;
	const aff_defaut = 0x2b7e;
	/// @bug debuguer l'affichage des royaumes pour les petites textures et le remettre ici
	const aff_gestion = 0xc000;
	const aff_gest_bourgs = 0x18000;
	const aff_gest_batailles = 0x1c000;
	const aff_batailles = 0xc000;
	const masque_ordre = 0x30;
	const masque_diplo = 0xf00;

	protected $x_min;
	protected $x_max;
	protected $y_min;
	protected $y_max;
	protected $cases;
	protected $grd_img;
	protected $infos='';
	protected $diplos=array();
	protected $races=array();
	protected $doss_prefixe = '';

	function __construct($x, $y, $options=0x2b7e, $champ_vision=3, $id='carte', $niv_min=0, $niv_max=255, $parent_calques=null, &$reperes=null)
	{
		global $Tclasse, $Gcouleurs, $db, $Trace, $G_max_x, $G_max_y, $G_url;
		$this->grd_img = !($options & self::aff_petit);
		parent::__construct($id, $this->grd_img ? 'aide' : 'carte_petit', 'carte_bord_haut');
		
		$perso = joueur::get_perso();
		
		if( strpos($G_url->get(),'roi/') !== false)
			$this->doss_prefixe = '../';	

		// Réduction de la vue en donjon
		if( $y > 190 )
			$champ_vision--;

		$this->x_min = $x - $champ_vision;
		$this->x_max = $x + $champ_vision;
		$this->y_min = $y - $champ_vision;
		$this->y_max = $y + $champ_vision;
		
		// Bordure de carte
		if( $this->x_min <= 0 )
		{
			$this->x_max -= $this->x_min - 1;
			$this->x_min = 1;
		}
		else if( $x <= $G_max_x && $this->x_max > $G_max_x )
		{
			$this->x_min -= $this->x_max - $G_max_x;
			$this->x_max = $G_max_x;
		}
		if( $this->y_min <= 0 )
		{
			$this->y_max -= $this->y_min - 1;
			$this->y_min = 1;
		}
		else if( $y <= $G_max_y && $this->y_max > $G_max_y )
		{
			$this->y_min -= $this->y_max - $G_max_y;
			$this->y_max = $G_max_y;
		}
		
		// Vérification des modifications à effectuer sur les cases avant de les afficher.
		// Notamment, transformation des 'placement' en 'construction' si nécessaire.
		map_case::check_zone($this->x_min, $this->x_max, $this->y_min, $this->y_max);
		
		// Nysin
		// Coordonnées cachées
		$cache = 75 <= $x && $x <= 100 && 288 <= $y && $y <= 305;
		// Bord haut
		$this->nouv_cell('&nbsp;', 'carte_bord_haut_gauche');
		if( $y > 190 )
			$this->nouv_cell('&nbsp;');
		for($j=$this->x_min; $j<=$this->x_max; $j++)
		{
			$c = $cache ? $j - $x : $j;
			$this->nouv_cell($c, $j==$x ? 'carte_bord_haut_x' : null);
		}
		if( $y > 190 )
		{
			$this->nouv_cell('&nbsp;');
			$this->nouv_ligne();
		}
		
		// On récupère les infos sur les cases
		$infos_cases = map::get_valeurs('decor,royaume,info,type', 'x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max, array('x','y'));
		// Calques
		/// @todo à améliorer
		$req = $db->query("select * from map_type_calque");
		while($row = $db->read_object($req))
		  $calques[$row->type] = $row;
		// Carte
		$tex = 'decor tex'.($this->grd_img ? '' : 'l');
		$this->cases = array();
		for($i=$this->y_min; $i<=$this->y_max; $i++)
		{
			$this->nouv_ligne();
			$c = $cache ? $i - $y : $i;
			$this->nouv_cell($c, $i==$y ? 'carte_bord_haut_y' : null, false, true);
			if( $y > 190 )
				$this->nouv_cell('&nbsp;');
			$this->cases[$i] = array();
			// calques terrain
			for($j=$this->x_min; $j<=$this->x_max; $j++)
			{
				$this->infos[$i][$j] = '';
				$case = !($options & self::aff_lien_gest) || $infos_cases[$j.'|'.$i]['royaume'] || $infos_cases[$j.'|'.$i]['info'] == 5 ? $tex.$infos_cases[$j.'|'.$i]['decor'] : '';
				$this->cases[$i][$j] = &$this->nouv_cell(null, null, $case);
				$type = $infos_cases[$j.'|'.$i]['type'];
				$pos = 'rel_'.($j-$x).'_'.($i-$y);
				if( $this->grd_img && array_key_exists($type, $calques) )
				{
					$calque = $this->cases[$i][$j]->add( new interf_bal_cont('div') );
					$dx = (-$j + $map_type_calque->decalage_x) * 60;
					$dy = (-$i + $map_type_calque->decalage_y) * 60;
					$style = 'background-image: url('.$this->doss_prefixe.'image/texture/'.$calques[$type]->calque.');';
					$style .= ' background-attachment: scroll; background-position: '.$dx.'px '.$dy.'px;';
					$style .= ' margin: -2px; height: 62px; width: 60px; background-repeat: repeat;';
					$calque->set_attribut('style', $style);
					$calque->add( new interf_bal_smpl('span', false, 'pos_'.$pos) );
				}
				else
					$this->cases[$i][$j]->add( new interf_bal_smpl('span', false, 'pos_'.$pos) );
			}
			if( $y > 190 )
				$this->nouv_cell('&nbsp;');
		}
		if( $y > 190 )
			$this->nouv_ligne();

		// Perso du joueur
		if( !($options & self::aff_restreint) )
		{
			$div = $this->cases[$perso->get_y()][$perso->get_x()]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
			$img = $this->doss_prefixe.'image/personnage'.($this->grd_img?'':'_low').'/'.$perso->get_race().'/'.$perso->get_race().'_'.$Tclasse[$perso->get_classe()]['type'].'.png';
			$div->set_attribut('style', 'background-image: url(\''.$img.'\');');
		}

		if( $parent_calques )
		{
			if( $y > 190 ) // Calque donjon
			{
				$image = $this->doss_prefixe.'image/interface/calque-atmosphere-noir'.($cache?'plannysin':'').'.png';
				$c_donj = $parent_calques->add( new interf_bal_smpl('div', false, false, 'calque') );
				$c_donj->set_attribut('style', 'background-image: url('.$image.');');
			}
			else
			{
				if( $options & self::aff_jour )
				{
				$image = $this->doss_prefixe.'image/interface/calque-atmosphere-vide-'.strtolower(moment_jour()).'.png';
				$c_jour = $parent_calques->add( new interf_bal_smpl('div', false, false, 'calque') );
				$c_jour->set_attribut('style', 'background-image: url('.$image.');');
				}
				if( $options & self::aff_atmosphere )
				{
					$requete = 'SELECT * FROM map_zone WHERE x1 < '.$this->x_max.' AND x2 > '.$this->x_min.' AND y1 < '.$this->y_max.' AND y2 > '.$this->y_min;
					$req = $db->query($requete);
					while( $row = $db->read_assoc($req) )
					{
					$image = $this->doss_prefixe.'image/interface/calque-atmosphere-'.$row['type'].'.png';
					$c_atm = $parent_calques->add( new interf_bal_smpl('div', false, false, 'calque') );
					$dx = -($this->x_min - $row['x1'])*60;
					$dy = -($this->y_min - $row['y1'])*60;
					$larg = (min($row['x2'], $this->x_max) - $this->x_min + 1) * 60;
					$haut = (min($row['y2'], $this->y_max) - $this->y_min + 1) * 60;
					$c_atm->set_attribut('style', 'background-image: url('.$image.'); background-repeat: repeat;background-position: '.$dx.'px '.$dy.'px; width:'.$larg.'px; height: '.$haut.'px;');
					}
				}
			}
		}

		// Éléments à afficher
		if( $options & self::aff_restreint )
		{
			$cond_bat = 'royaume = '.$Trace[$perso->get_race()]['numrace'];
			if($reperes)
			{
				$this->afficher_reperes($reperes['action']);
				$this->afficher_bat_ennemi($reperes['batiment']);
			}
			$this->afficher_placements($cond_bat);
			$this->afficher_batiments($cond_bat, false);
		}
		else
		{
			// conditions
			$diplo = ($options & self::masque_diplo) >> 8;
			if( $diplo >= 11 )
			{
				if( $options & self::aff_diplo_sup )
				{
					$cond_bat = 'royaume = '.$Trace[$perso->get_race()]['numrace'];
					$cond_pj = 'p.race = '.$perso->get_race();
				}
				else
					$cond_bat = $cond_pj = '1';
			}
			else
			{
				/// @todo passer à l'objet
				$requete = 'SELECT race FROM diplomatie WHERE '.$perso->get_race();
				if( $options & self::aff_diplo_sup )
					$requete .= ' <= '.$diplo.' OR '.$perso->get_race().' = 127';
				else
					$requete .= ' >= '.$diplo.' AND '.$perso->get_race().' != 127';
				$req = $db->query($requete);
				$ids = $races = array();
				while( $row = $db->read_array() )
				{
					$ids[] = $Trace[$row[0]]['numrace'];
					$races[] = '"'.$row[0].'"';
				}
				$cond_bat = 'royaume IN ('.implode(',', $ids).')';
				$cond_pj = 'p.race IN ('.implode(',', $races).')';
			}
			
			// Diplomatie et royaumes
			/// @todo passer à l'objet
			$requete = 'SELECT * FROM diplomatie WHERE race = "'.$perso->get_race().'"';
			$req = $db->query($requete);
			$this->diplos = $db->read_array();
			foreach($Trace as $r=>$t)
				$this->races[$t['numrace']] = $r;
			
			if( $options & self::aff_pnj )
				$this->afficher_pnj();
			switch( $options & self::masque_ordre )
			{
				case self::aff_pcb:
					$this->afficher_pj($perso, $cond_pj);
					$this->afficher_placements($cond_bat);
					$this->afficher_batiments($cond_bat, $options & self::aff_ads);
					break;
				case self::aff_cbp:
					$this->afficher_placements($cond_bat);
					$this->afficher_batiments($cond_bat, $options & self::aff_ads);
					$this->afficher_pj($perso, $cond_pj);
					break;
				case self::aff_cpb:
					$this->afficher_placements($cond_bat);
					$this->afficher_pj($perso, $cond_pj);
					$this->afficher_batiments($cond_bat, $options & self::aff_ads);
					break;
			}
			if( $options & self::aff_monstres  )
				$this->afficher_monstres($niv_min, $niv_max);
		}
		
		if( !($options & self::aff_restreint) || $options & self::aff_lien_gest )
		{
			// Navigation
			for($i=$this->y_min; $i<=$this->y_max; $i++)
			{
				for($j=$this->x_min; $j<=$this->x_max; $j++)
				{
					$cont = $this->cases[$i][$j]->get_fils(0);
					if( !$cont || $cont->get_attribut('class') != 'carte_contenu' )
						$cont = $this->cases[$i][$j]->insert( new interf_bal_cont('a', null, 'carte_contenu') );
					else
						$cont->set_balise('a');
					$pos = 'rel_'.($j-$x).'_'.($i-$y);
					if( $options & self::aff_lien_gest )
						$cont->set_attribut('href', $G_url->get( array('action'=>'case', 'x'=>$j, 'y'=>$i) ));
					else
						$cont->set_attribut('href', 'informationcase.php?case='.$pos);
					$cont->set_attribut('onclick', 'return charger(this.href);');
					if( $this->infos[$i][$j] )
					{
						$cont->set_tooltip('<ul class=\'info_bulle\'>'.$this->infos[$i][$j].'</ul>');
						$cont->set_attribut('data-html', 'true');
					}
				}
			}
		}
		else
		{
			for($i=$this->y_min; $i<=$this->y_max; $i++)
			{
				for($j=$this->x_min; $j<=$this->x_max; $j++)
				{
					if( $this->infos[$i][$j] )
					{
						$cont = $this->cases[$i][$j]->get_fils(0);
						$cont->set_tooltip('<ul class=\'info_bulle\'>'.$this->infos[$i][$j].'</ul>');
						$cont->set_attribut('data-html', 'true');
					}
				}
			}
		}

		// Affichage des royaumes si nécessaire
		if( $options & self::aff_royaumes )
		{
		  for($i=$this->y_min; $i<=$this->y_max; $i++)
		  {
			for($j=$this->x_min; $j<=$this->x_max; $j++)
			{
			  $roy = $infos_cases[$j.'|'.$i]['royaume'];
			  if( $roy )
			  {
				$border = '';
				$commun = ': dashed 1px '.$Gcouleurs[$roy].';';
				$bords = 0;
				if( $j == $this->x_min or $infos_cases[($j-1).'|'.$i]['royaume'] != $roy )
				{
				  $border .= ' border-left'.$commun;
				  $bords++;
				}
				if( $j == $this->x_max or $infos_cases[($j+1).'|'.$i]['royaume'] != $roy )
				{
				  $border .= ' border-right'.$commun;
				  $bords++;
				}
				if( $i == $this->y_min or $infos_cases[$j.'|'.($i-1)]['royaume'] != $roy )
				{
				  $border .= ' border-top'.$commun;
				  $bords++;
				}
				if( $i == $this->y_max or $infos_cases[$j.'|'.($i+1)]['royaume'] != $roy )
				{
				  $border .= ' border-bottom'.$commun;
				  $bords++;
				}
				if( $bords == 4 )
				  $border = ' border'.$commun;
			  }
			  else
				$border = '';
			  $cont = $this->cases[$i][$j]->get_fils(0);
			  /*if( !$cont or $cont->get_attribut('class') != 'carte_contenu' )
				$cont = $this->cases[$i][$j]->insert( new interf_bal_cont('div', null, 'carte_contenu') );*/
			  $cont->set_attribut('style', $cont->get_attribut('style').$border);
			}
		  }
		}
		
		// sons d'ambiance
		if( $parent_calques && $options & self::act_sons )
		{
			$son = $db->query_get_object("select type from map_sound_zone where x1 <= $x and $x <= x2 and y1 <= $y and $y <= y2");
			if( $son )
			{
				$audio = $parent_calques->add( new interf_bal_cont('audio', 'son_ambiance') );
				$audio->set_attribut('autoplay', 'autoplay');
				$audio->set_attribut('loop', 'loop');
				$audio->add( new interf_bal_smpl('source', null, array('src'=>'image/son/'.$son->type.'.ogg', 'type'=>'audio/ogg') ) );
				$audio->add( new interf_bal_smpl('source', null, array('src'=>'image/son/'.$son->type.'.mp3', 'type'=>'audio/mpeg') ) );
			}
		}
	}

  protected function afficher_batiments($cond_bat, $royaumes=false)
  {
  	global $Gtrad;
    $bats = construction::get_images_zone($this->x_min, $this->x_max, $this->y_min, $this->y_max, $this->grd_img, $cond_bat, $royaumes);
    foreach($bats as $b)
    {
    	if( $b->quete && !count(quete_perso::create(array('id_etape', 'id_perso'), array($b->quete, joueur::get_perso()->get_id()))) )
    			continue;
      $div = $this->cases[$b->y][$b->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\''.$this->doss_prefixe.$b->image.'\');');
      $race = $this->races[$b->royaume];
      $this->infos[$b->y][$b->x] .= '<li><span class=\'info_batiment\'>'.$b->nom.'</span> <span class=\'tooltip_diplo'.$this->diplos[$race].'\'>'.$Gtrad[$race].'</span></li>';
    }
  }

  protected function afficher_placements($cond_bat)
  {
  	global $Gtrad;
    $bats = placement::get_images_zone($this->x_min, $this->x_max, $this->y_min, $this->y_max, $cond_bat);
    foreach($bats as $b)
    {
      $div = $this->cases[$b->y][$b->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\''.$this->doss_prefixe.$b->image.'\');');
      $race = $this->races[$b->royaume];
      $this->infos[$b->y][$b->x] .= '<li><span class=\'info_batiment\'>'.$b->nom.'</span> <span class=\'tooltip_diplo'.$this->diplos[$race].'\'>'.$Gtrad[$race].'</span></li>';
    }
  }

  protected function afficher_pj(&$perso=null, $cond_pj)
  {
    global $Tclasse, $db, $Gtrad;
    /// @todo à améliorer
    $pos = array();
    if( $perso )
    	//$requete = 'SELECT * FROM perso AS p INNER JOIN diplomatie AS d ON p.race = d.race WHERE x BETWEEN '.$this->x_min.' AND '.$this->x_max.' AND y BETWEEN '.$this->y_min.' AND '.$this->y_max.' AND NOT ( x = '.$perso->get_x().' AND y = '.$perso->get_y().' ) AND statut="actif" AND '.$cond_pj.' ORDER BY d.'.$perso->get_race().' DESC, level DESC';
      $requete = 'SELECT * FROM perso AS p INNER JOIN diplomatie AS d ON p.race = d.race WHERE x BETWEEN '.$this->x_min.' AND '.$this->x_max.' AND y BETWEEN '.$this->y_min.' AND '.$this->y_max.' AND statut="actif" AND '.$cond_pj.' ORDER BY d.'.$perso->get_race().' DESC, level DESC';
    else
      $requete = 'SELECT * FROM perso WHERE x BETWEEN '.$this->x_min.' AND '.$this->x_max.' AND y BETWEEN '.$this->y_min.' AND '.$this->y_max.' AND statut="actif" AND '.$cond_pj.' ORDER BY level DESC';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
    {
      $p = new perso($row);
      $cle = $row['x'].'_'.$row['y'];
      $niv = $p->get_cache_niveau() ? ' - Niv. '.$p->get_level() : '';
      $this->infos[$p->get_y()][$p->get_x()] .= '<li><span class=\'info_perso\'>'.$p->get_nom().'</span> <span class=\'tooltip_diplo'.$this->diplos[$p->get_race()].'\'>'.$Gtrad[$p->get_race()].'</span>'.$niv.'</li>';
      if( !array_key_exists($cle, $pos) )
      {
      	$pos[$cle] = true;
				// S'il y a déjà un contenu on passe au suivant.
	      $fils = $this->cases[$p->get_y()][$p->get_x()]->get_fils(0);
	      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
	        continue;
	    	/// @todo à améliorer
	      // Cache sa classe ?
	      $div = $this->cases[$p->get_y()][$p->get_x()]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
	      if( $p->get_cache_classe() == 2 )
	        $classe = 'combattant';
	      else if($p->get_cache_classe() == 1 && $p->get_race() != $perso->get_race())
	        $classe = 'combattant';
	      else
	        $classe = $p->get_classe();
	      // Camouflage
	      $p->check_specials();
	      $race = $p->get_race_a();
	      $img = $this->doss_prefixe.'image/personnage'.($this->grd_img?'':'_low').'/'.$race.'/'.$race.'_'.$Tclasse[$classe]['type'].'.png';
	      $div->set_attribut('style', 'background-image: url(\''.$img.'\');');
			}
			
    }
  }

  protected function afficher_pnj()
  {
    /// @todo à améliorer
		$pnj = pnj::get_valeurs('x, y, image, nom', 'x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max);
		if( $pnj )
		{
			foreach($pnj as $p)
	    {
	      $this->infos[$p['y']][$p['x']] .= '<li><span class=\'info_pnj\'>PNJ</span> '.$p['nom'].'</li>';
	      // S'il y a déjà un contenu on passe au suivant.
	      $fils = $this->cases[ $p['y'] ][ $p['x'] ]->get_fils(0);
	      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
	        continue;
	      $div = $this->cases[ $p['y'] ][ $p['x'] ]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
	      $div->set_attribut('style', 'background-image: url(\''.$this->doss_prefixe.'image/pnj/'.$p['image'].'.png\');');
	    }
		}
  }

  protected function afficher_monstres($niv_min=0, $niv_max=255)
  {
    global $db;
    $pos = array();
    $perso = joueur::get_perso();
    /// @todo à améliorer
    //
    $requete = 'SELECT x, y, lib, nom, quete, COUNT(*) AS nbr FROM map_monstre AS mm INNER JOIN monstre AS m ON mm.type = m.id WHERE (x BETWEEN '.$this->x_min.' AND '.$this->x_max.') AND (y BETWEEN '.$this->y_min.' AND '.$this->y_max.') AND NOT (x = '.$perso->get_x().' AND y = '.$perso->get_y().') AND (level BETWEEN '.$niv_min.' AND '.$niv_max.') GROUP BY x, y, lib ORDER BY ABS(CAST(level AS SIGNED) - '.$perso->get_level().') ASC, level DESC';
    $requete = 'SELECT x, y, lib, nom, quete, COUNT(*) AS nbr FROM map_monstre AS mm INNER JOIN monstre AS m ON mm.type = m.id WHERE (x BETWEEN '.$this->x_min.' AND '.$this->x_max.') AND (y BETWEEN '.$this->y_min.' AND '.$this->y_max.') AND (level BETWEEN '.$niv_min.' AND '.$niv_max.') GROUP BY x, y, lib ORDER BY ABS(CAST(level AS SIGNED) - '.$perso->get_level().') ASC, level DESC';
    $req = $db->query($requete);
    while($row = $db->read_object($req))
    {
    	if( $row->quete && !count(quete_perso::create(array('id_etape', 'id_perso'), array($row->quete, $perso->get_id()))) )
    			continue;
    	$cle = $row->x.'_'.$row->y;
      $this->infos[$row->y][$row->x] .= '<li><span class=\'info_monstre\'>Monstre</span> '.$row->nom.' x '.$row->nbr.'</li>';
      if( !array_key_exists($cle, $pos) )
      {
      	$pos[$cle] = true;
	      // S'il y a déjà un contenu on passe au suivant.
	      $fils = $this->cases[$row->y][$row->x]->get_fils(0);
	      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
	        continue;
	      $div = $this->cases[$row->y][$row->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
	      $div->set_attribut('style', 'background-image: url(\''.$this->doss_prefixe.'image/monstre/'.$row->lib.'.png\');');
      }
    }
  }
  
  protected function afficher_reperes($reperes)
  {
    $pos = array();
  	foreach($reperes as $repere)
  	{
    	$cle = $repere->get_x().'_'.$repere->get_y();
			$repere_type = $repere->get_repere_type();
			$this->infos[$repere->get_y()][$repere->get_x()] .= '<li><span class=\'info_repere\'>Mission</span> '.$repere_type->get_nom().'</li>';
    	if( !array_key_exists($cle, $pos) )
    	{
      	$pos[$cle] = true;
	      // S'il y a déjà un contenu on passe au suivant.
	      $fils = $this->cases[$repere->get_y()][$repere->get_x()]->get_fils(0);
	      $img = new interf_img($this->doss_prefixe.'image/repere/'.$repere_type->get_image().'.png', $repere_type->get_nom()[0]); 
	      //if( $fils )
	        $div = $this->cases[$repere->get_y()][$repere->get_x()]->add( $img );
	      /*else
      		$div = $this->cases[$repere->get_y()][$repere->get_x()]->add( new interf_bal_cont('div', null, 'carte_contenu') );*/
	      /*$div = $this->cases[$repere->get_y()][$repere->get_x()]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
				$div->set_attribut('style', 'background-image: url(\''.$this->doss_prefixe.'image/monstre/'.$repere_type->get_image().'.png\');');*/
			}
		}
	}
  
  protected function afficher_bat_ennemi($reperes)
  {
    $pos = array();
  	foreach($reperes as $repere)
  	{
    	$cle = $repere->get_x().'_'.$repere->get_y();
			$repere_type = $repere->get_repere_type();
			$this->infos[$repere->get_y()][$repere->get_x()] .= '<li><span class=\'info_repere\'>Bâtiment ennemi</span> '.$repere_type->get_nom().'</li>';
    	if( !array_key_exists($cle, $pos) )
    	{
      	$pos[$cle] = true;
	      // S'il y a déjà un contenu on passe au suivant.
	      $fils = $this->cases[$repere->get_y()][$repere->get_x()]->get_fils(0);
	      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
	        continue;
      	$div = $this->cases[$repere->get_y()][$repere->get_x()]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
	      $div->set_attribut('style', 'background-image: url(\''.$repere_type->get_image_full($this->doss_prefixe, 'low').'\');');
			}
		}
	}

  protected function afficher_claques_terrain()
  {
    /// @todo à améliorer
    global $db;
    $req = $db->query("select * from map_type_calque");
    while($row = $db->read_object($req))
      $calques[$row->type] = $row;
    for($i=$this->y_min; $i<=$this->y_max; $i++)
    {
      for($j=$this->x_min; $j<=$this->x_max; $j++)
      {
      }
    }
  }
  
  static function calcul_options($id_perso)
  {
  	///@todo passer à l'objet
  	global $db;
		$requete = 'select nom, valeur from options where id_perso = '.$id_perso.' and nom in ("affiche_royaume", "desactive_atm", "desactive_atm_all", "cache_monstre", "affiche_roy_ads", "ordre_aff", "diplo_aff", "diplo_aff_sup", "no_sound", "cache_pnj")';
		$req = $db->query($requete);
		$options = self::aff_defaut;
		while( $row = $db->read_assoc($req) )
		{
			switch($row['nom'])
			{
			case 'affiche_royaume':
				if( $row['valeur'] == 1 )
					$options |=  self::aff_royaumes;
				break;
			case 'desactive_atm':
				if( $row['valeur'] == 1)
					$options ^=  self::aff_atmosphere;
				break;
			case 'desactive_atm_all':
				if( $row['valeur'] == 1)
					$options ^=  self::aff_jour;
				break;
			case 'cache_monstre':
				if( $row['valeur'] == 1)
					$options ^=  self::aff_monstres;
				break;
			case 'affiche_roy_ads':
				if( $row['valeur'] == 1)
					$options |=  self::aff_ads;
				break;
			case 'cache_pnj':
				if( $row['valeur'] == 1)
					$options ^=  self::aff_pnj;
				break;
			case 'diplo_aff_sup':
				if( $row['valeur'] == 1)
					$options |=  self::aff_diplo_sup;
				break;
			case 'ordre_aff':
				$options &=  ~self::masque_ordre;
				$options |= $row['valeur'];
				break;
			case 'diplo_aff':
				$options &=  ~self::masque_diplo;
				$options |=  $row['valeur'];
				break;
			case 'no_sound':
				if( $row['valeur'] == 1)
					$options ^=  self::act_sons;
				break;
			}
		}
		return $options;
	}
}
