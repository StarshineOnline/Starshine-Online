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
  const aff_defaut = 0x2b7e;
  const masque_ordre = 0x30;
  const masque_diplo = 0xf00;

  protected $x_min;
  protected $x_max;
  protected $y_min;
  protected $y_max;
  protected $cases;
  protected $grd_img;

	function __construct($x, $y, $options=0x2b7e, $champ_vision=3, $id='carte', $niv_min=0, $niv_max=255)
	{
    global $Tclasse, $Gcouleurs, $db, $Trace;
		parent::__construct($id, null, 'carte_bord_haut');

    $this->grd_img = true.
    /// @todo réduction de la vue en donjon

		$this->x_min = $x - $champ_vision;
		$this->x_max = $x + $champ_vision;
		$this->y_min = $y - $champ_vision;
		$this->y_max = $y + $champ_vision;

    /// @todo Bordure de carte

		// Bord haut
		$this->nouv_cell('&nbsp;', 'carte_bord_haut_gauche');
		for($j=$this->x_min; $j<=$this->x_max; $j++)
		{
			$this->nouv_cell($j, $j==$x ? 'carte_bord_haut_x' : null);
		}
		
		// On récupère les infos sur les cases
		$infos_cases = map::get_valeurs('decor,royaume,info,type', 'x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max, array('x','y'));
    // Calques
    /// @todo à améliorer
    $req = $db->query("select * from map_type_calque");
    while($row = $db->read_object($req))
      $calques[$row->type] = $row;
		// Carte
		$this->cases = array();
		for($i=$this->y_min; $i<=$this->y_max; $i++)
		{
			$this->nouv_ligne();
			$this->entete = true;
			$this->nouv_cell($i, $i==$y ? 'carte_bord_haut_y' : null);
			$this->entete = false;
		  $this->cases[$i] = array();
			for($j=$this->x_min; $j<=$this->x_max; $j++)
			{
				$this->cases[$i][$j] = &$this->nouv_cell(null, null, 'decor tex'.$infos_cases[$j.'|'.$i]['decor']);
        $type = $infos_cases[$j.'|'.$i]['type'];
        if( array_key_exists($type, $calques) )
        {
          $calque = $this->cases[$i][$j]->add( new interf_bal_cont('div') );
      		$dx = (-$j + $map_type_calque->decalage_x) * 60;
      		$dy = (-$i + $map_type_calque->decalage_y) * 60;
          $style = 'background-image: url(image/texture/'.$calques[$type]->calque.');';
          $style .= ' background-attachment: scroll; background-position: '.$dx.'px '.$dy.'px;';
          $style .= ' margin: -2px; height: 62px; width: 60px; background-repeat: repeat;';
          $calque->set_attribut('style', $style);
        }
			}
		}

    // Perso du joueur
    $perso = joueur::get_perso();
    $div = $this->cases[$perso->get_y()][$perso->get_x()]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
    $img = 'image/personnage'.($this->grd_img?'':'_low').'/'.$perso->get_race().'/'.$perso->get_race().'_'.$Tclasse[$perso->get_classe()]['type'].'.png';
    $div->set_attribut('style', 'background-image: url(\''.$img.'\');');

    /// @todo repères
    /// @todo couche donjons
    /// @todo calque atmosphere
    // calques terrain

		// conditions
		$diplo = ($options & self::masque_diplo) >> 8;
		if( $diplo >= 11 )
		{
			if( $options & self::aff_diplo_sup )
			{
				$cond_bat =  'royaume = '.$Trace[$perso->get_race()]['numrace'];
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
		
    // Éléments à afficher
    if( $options & self::aff_pnj )
    	$this->afficher_pnj();
    switch( $options & self::masque_ordre )
    {
    case self::aff_pcb:
	    $this->afficher_pj($perso, $cond_pj);
	    $this->afficher_placements($cond_bat);
	    $this->afficher_batiments($cond_bat);
    	break;
    case self::aff_cbp:
	    $this->afficher_placements($cond_bat);
	    $this->afficher_batiments($cond_bat);
	    $this->afficher_pj($perso, $cond_pj);
    	break;
    case self::aff_cpb:
	    $this->afficher_placements($cond_bat);
	    $this->afficher_pj($perso, $cond_pj);
	    $this->afficher_batiments($cond_bat);
    	break;
		}
    if( $options & self::aff_monstres  )
      $this->afficher_monstres($niv_min, $niv_max);
      
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
        $cont->set_attribut('id', 'pos_'.$pos);
        $cont->set_attribut('href', 'informationcase.php?case='.$pos);
        $cont->set_attribut('onclick', 'return charger(this.href);');
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
	}

  protected function afficher_batiments($cond_bat)
  {
    $bats = construction::get_images_zone($this->x_min, $this->x_max, $this->y_min, $this->y_max, $this->grd_img, $cond_bat);
    foreach($bats as $b)
    {
      $div = $this->cases[$b->y][$b->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\''.$b->image.'\');');
    }
  }

  protected function afficher_placements($cond_bat)
  {
    $bats = placement::get_images_zone($this->x_min, $this->x_max, $this->y_min, $this->y_max, $cond_bat);
    foreach($bats as $b)
    {
      $div = $this->cases[$b->y][$b->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\''.$b->image.'\');');
    }
  }

  protected function afficher_pj(&$perso=null, $cond_pj)
  {
    global $Tclasse, $db;
    /// @todo à améliorer
    if( $perso )
      $requete = 'SELECT * FROM perso AS p INNER JOIN diplomatie AS d ON p.race = d.race WHERE x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max.' AND x != '.$perso->get_x().' AND y != '.$perso->get_y().' AND statut="actif" AND '.$cond_pj.' GROUP BY x, y ORDER BY d.'.$perso->get_race().' DESC, level DESC';
    else
      $requete = 'SELECT * FROM perso WHERE x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max.' AND statut="actif" AND '.$cond_pj.' GROUP BY x, y ORDER BY level DESC';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
    {
      $p = new perso($row);
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
      $img = 'image/personnage'.($this->grd_img?'':'_low').'/'.$race.'/'.$race.'_'.$Tclasse[$classe]['type'].'.png';
      $div->set_attribut('style', 'background-image: url(\''.$img.'\');');
    }
  }

  protected function afficher_pnj()
  {
    /// @todo à améliorer
		$pnj = pnj::get_valeurs('x, y, image', 'x >= '.$this->x_min.' AND x <= '.$this->x_max.' AND y >= '.$this->y_min.' AND y <= '.$this->y_max);
    foreach($pnj as $p)
    {
      // S'il y a déjà un contenu on passe au suivant.
      $fils = $this->cases[ $p['y'] ][ $p['x'] ]->get_fils(0);
      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
        continue;
      $div = $this->cases[ $p['y'] ][ $p['x'] ]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\'image/pnj/'.$p['image'].'.png\');');
    }
  }

  protected function afficher_monstres($niv_min=0, $niv_max=255)
  {
    global $db;
    $perso = joueur::get_perso();
    /// @todo à améliorer
    $requete = 'SELECT x, y, lib FROM map_monstre AS mm INNER JOIN monstre AS m ON mm.type = m.id WHERE (x BETWEEN '.$this->x_min.' AND '.$this->x_max.') AND (y BETWEEN '.$this->y_min.' AND '.$this->y_max.') AND x != '.$perso->get_x().' AND y != '.$perso->get_y().' AND (level BETWEEN '.$niv_min.' AND '.$niv_max.') GROUP BY x, y ORDER BY ABS(CAST(level AS SIGNED) - '.$perso->get_level().') ASC, level DESC';
    $req = $db->query($requete);
    while($row = $db->read_object($req))
    {
      // S'il y a déjà un contenu on passe au suivant.
      $fils = $this->cases[$row->y][$row->x]->get_fils(0);
      if( $fils && $fils->get_attribut('class') == 'carte_contenu' )
        continue;
      $div = $this->cases[$row->y][$row->x]->insert( new interf_bal_cont('div', null, 'carte_contenu') );
      $div->set_attribut('style', 'background-image: url(\'image/monstre/'.$row->lib.'.png\');');
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
				if( $row['valeur'] == 0)
					$options ^=  self::act_sons;
				break;
			}
		}
		return $options;
	}
}
?>