<?php
/**
 * @file interf_carte_monde.class.php
 * Affichage de la carte du monde
 */
 
/**
 * classe gérant l'affichage  de la carte du monde
 */
class interf_carte_monde extends interf_bal_cont
{
	protected $svg = null;
	const taille_case = 3;
	protected $x_min;
	protected $x_max;
	protected $y_min;
	protected $y_max;
	protected $options = false;
	protected $cases = array();
	function __construct($id=false, $classe=false)
	{
		parent::__construct('div', $id, $classe);
	}
	function aff_svg($lim_vue=false)
	{
		global $G_max_x, $G_max_y, $G_url;
		
		if( $lim_vue )
		{
			$perso = joueur::get_perso();
			$taille = ($lim_vue*2 + 1) * self::taille_case;
			//$lien = 'carte_perso.php?vue='.$lim_vue;
			$lien = 'image/carte.png';
			$this->x_min = $perso->get_x() - $lim_vue;
			$this->y_min = $perso->get_y() - $lim_vue;
			$this->x_max = $perso->get_x() + $lim_vue;
			$this->y_max = $perso->get_y() + $lim_vue;
			if( $this->x_min < 0 )
			{
				$this->x_max -= $this->x_min;
				$this->x_min = 0;
			}
			if( $this->y_min < 1 )
			{
				$this->y_max -= $this->y_min;
				$this->y_min = 1;
			}
			if( $this->x_max > $G_max_x )
			{
				$this->x_min += $this->x_max - $G_max_x;
				$this->x_max = $G_max_x;
			}
			if( $this->y_max > $G_max_y )
			{
				$this->y_min += $this->y_max - $G_max_y;
				$this->y_max = $G_max_y;
			}
		}
		else
		{
			$taille = $G_max_x * self::taille_case;
			$lien = 'image/carte.png';
			$this->x_min = 1;
			$this->y_min = 1;
			$this->x_max = $G_max_x;
			$this->y_max = $G_max_y;
		}
		$taille_img = $G_max_x * self::taille_case;
		if( strpos($G_url->get(), 'roi/') > 0 )
			$lien = '../'.$lien;
		$taille = ($lim_vue ? $lim_vue*2 + 1 : $G_max_x) * self::taille_case;
		$this->svg = $this->add( new interf_bal_cont('svg', $this->options ? 'carte_monde' : false) );
		$this->svg->set_attribut('width', $taille.'px');
		$this->svg->set_attribut('height', $taille.'px');
		$this->svg->set_attribut('viewBox', '0 0 '.$taille.' '.$taille);
		$this->svg->set_attribut('xmlns', 'http://www.w3.org/2000/svg');
		$this->svg->set_attribut('xmlns:xlink', 'http://www.w3.org/1999/xlink');
		
		if( $this->options )
		{
			$this->ajout_filtre('barbares', '-.7 .7 .5 0 0');
			$this->ajout_filtre('corrompus', '5 3 1 0 0', '.4 0');
			$this->ajout_filtre('edb', '-2 2 -1 0 0');
			$this->ajout_filtre('mv', '4 1 5 0 0', '0 .4 0');
			$this->ajout_filtre('trolls', '1 -2 -2 0 0');
			$this->ajout_filtre('humains', '-2 -5 1 0 0');
			$this->ajout_filtre('barbares', '-.7 .7 .5 0 0');
			$this->ajout_filtre('nains', '.2 .5 5 0 0', '0 .4 0');
			$this->ajout_filtre('vampires', '3 -3 .7 0 0', '0 .4 0');
			$this->ajout_filtre('scavs', '.3 .3 -3 0 0');
			$this->ajout_filtre('orcs', '-1.5 1 2.5 0 0', '0 .4 0');
			$this->ajout_filtre('he', '.2 .2 .2 0 0');
			$this->ajout_filtre('monstres', '-.5 -.5 0 0 1', false, 1);
		}
		
		$img = $this->svg->add( new interf_bal_smpl('image', null, 'img_monde') );
		/*$img->set_attribut('width', $taille.'px');
		$img->set_attribut('height', $taille.'px');*/
		$img->set_attribut('x', '-'.($this->x_min*3 - 3).'px');
		$img->set_attribut('y', '-'.($this->y_min*3 - 3).'px');
		$img->set_attribut('width', $taille_img.'px');
		$img->set_attribut('height', $taille_img.'px');
		$img->set_attribut('xlink:href', $lien);
	}
	function aff_groupe($id_groupe, $taille=3)
	{
		if( !$this->svg )
			return;
		$perso = joueur::get_perso();
		if( $id_groupe )
		{
			$groupe = new groupe( $id_groupe );
			$groupe->get_membre_joueur();
			$membres = array();
			foreach($groupe->membre_joueur as $membre)
			{
				$ind = $membre->get_x().'-'.$membre->get_y();
				if( in_array($ind, $this->cases) )
					break;
				$soi = $membre->get_id() == $perso->get_id();
				if( array_key_exists($ind, $membres) )
				{
					$membres[$ind]['persos'][] = $membre->get_nom();
					$membres[$ind]['soi'] |= $soi;
				}
				else
					$membres[$ind] = array('x'=>$membre->get_x(), 'y'=>$membre->get_y(), 'soi'=>$soi, 'persos'=>array( $membre->get_nom() ));
			}
			foreach($membres as $c=>$m)
			{
				$this->cases[] = $c;
				$x = ($m['x'] - $this->x_min) * self::taille_case;
				$y = ($m['y'] - $this->y_min) * self::taille_case;
				$classe = $m['soi'] ? 'carte_perso' : 'carte_groupe';
				$rect = $this->svg->add( new interf_bal_smpl('rect', null, false, $classe) );
				$rect->set_attribut('x', $x.'px');
				$rect->set_attribut('y', $y.'px');
				$rect->set_attribut('width', self::taille_case.'px');
				$rect->set_attribut('height', self::taille_case.'px');
				$rect->set_tooltip( implode(', ', $m['persos']) );
			}
		}
		else
		{
				$x = ($perso->get_x() - $this->x_min) * self::taille_case;
				$y = ($perso->get_y() - $this->y_min) * self::taille_case;
				$rect = $this->svg->add( new interf_bal_smpl('rect', null, false, 'carte_perso') );
				$rect->set_attribut('x', $x.'px');
				$rect->set_attribut('y', $y.'px');
				$rect->set_attribut('width', self::taille_case.'px');
				$rect->set_attribut('height', self::taille_case.'px');
				$rect->set_tooltip('Votre position');
		}
	}
	function aff_pos($x, $y)
	{
		$x2 = ($x - $this->x_min) * self::taille_case;
		$y2 = ($y - $this->y_min) * self::taille_case;
		$rect = $this->svg->add( new interf_bal_smpl('rect', null, false, 'carte_pos') );
		$rect->set_attribut('x', $x2.'px');
		$rect->set_attribut('y', $y2.'px');
		$rect->set_attribut('width', self::taille_case.'px');
		$rect->set_attribut('height', self::taille_case.'px');
		$rect->set_tooltip('X='.$x.' − Y='.$y);
	}
	function aff_options()
	{
		$this->options = $this->add( new interf_bal_cont('div', 'carte_options', 'list-group') );
		$this->ajout_option('Barbares', 'barbares');
		$this->ajout_option('Corrompus', 'corrompus');
		$this->ajout_option('Elfes-des-bois', 'edb');
		$this->ajout_option('Hauts-Elfes', 'he');
		$this->ajout_option('Humains', 'humains');
		$this->ajout_option('Morts-vivants', 'mv');
		$this->ajout_option('Nains', 'nains');
		$this->ajout_option('Orcs', 'orcs');
		$this->ajout_option('Scavengers', 'scavs');
		$this->ajout_option('Trolls', 'trolls');
		$this->ajout_option('Vampires', 'vampires');
		$this->ajout_option('Monstres', 'monstres');
	}
	function aff_habitants(&$royaume, $taille=3)
	{
		if( !$this->svg )
			return;
		$perso = joueur::get_perso();
		$habitants = perso::create(false, false, 'id', false, 'race = "'.$royaume->get_race().'" AND statut = "actif" AND level > 0 AND x < 190 AND y < 190');
		$membres = array();
		foreach($habitants as $hab)	
		{
			$ind = $hab->get_x().'-'.$hab->get_y();
			if( in_array($ind, $this->cases) )
				break;
			$soi = $hab->get_id() == $perso->get_id();
			if( array_key_exists($ind, $membres) )
			{
				$membres[$ind]['persos'][] = $hab->get_nom();
				$membres[$ind]['soi'] |= $soi;
			}
			else
				$membres[$ind] = array('x'=>$hab->get_x(), 'y'=>$hab->get_y(), 'soi'=>$soi, 'persos'=>array( $hab->get_nom() ));
		}
		foreach($membres as $c=>$m)
		{
			$this->cases[] = $c;
			$x = ($m['x'] - $this->x_min) * self::taille_case;
			$y = ($m['y'] - $this->y_min) * self::taille_case;
			$classe = $m['soi'] ? 'carte_perso' : 'carte_groupe';
			$rect = $this->svg->add( new interf_bal_smpl('rect', null, false, $classe) );
			$rect->set_attribut('x', $x.'px');
			$rect->set_attribut('y', $y.'px');
			$rect->set_attribut('width', self::taille_case.'px');
			$rect->set_attribut('height', self::taille_case.'px');
			$rect->set_tooltip( implode(', ', $m['persos']) );
		}
	}
	function aff_batiments(&$royaume, $taille=3)
	{
		if( !$this->svg )
			return;
		$batiments = construction::create(false, false, 'id', false, 'royaume = "'.$royaume->get_id().'" AND type != "arme_de_siege" AND x < 190 AND y < 190');
		foreach($batiments as $b)	
		{
			$ind = $b->get_x().'-'.$b->get_y();
			if( in_array($ind, $this->cases) )
				break;
			$this->cases[] = $c;
			$x = ($b->get_x() - $this->x_min) * self::taille_case;
			$y = ($b->get_y() - $this->y_min) * self::taille_case;
			$classe = 'carte_'.$b->get_type();
			$rect = $this->svg->add( new interf_bal_smpl('rect', null, false, $classe) );
			$rect->set_attribut('x', $x.'px');
			$rect->set_attribut('y', $y.'px');
			$rect->set_attribut('width', self::taille_case.'px');
			$rect->set_attribut('height', self::taille_case.'px');
			$rect->set_tooltip( $b->get_nom() );
		}
	}
	protected function ajout_option($nom, $id)
	{
		$lien = $this->options->add( new interf_bal_smpl('a', $nom, 'opt_'.$id, 'list-group-item') );
		$lien->set_attribut('onclick', 'return carte_royaume(\''.$id.'\');');
	}
	protected function ajout_filtre($id, $matrice, $table='0 .4', $rouge=.5)
	{
		/// @bug reste noir au lieu de rouge pour les corrompus
		$filtre = $this->svg->add( new interf_bal_cont('filter', 'filtre_'.$id) );
		$colormatrix = $filtre->add( new interf_bal_smpl('fecolormatrix') );
		$colormatrix->set_attribut('type', 'matrix');
		$colormatrix->set_attribut('values', '0 0 0 0 '.$rouge.'  0 0 0 0 0  0 0 0 0 0  '.$matrice);
		if( $table )
		{
			$transfer = $filtre->add( new interf_bal_cont('fecomponenttransfer') );
			$funca = $transfer->add( new interf_bal_smpl('fefunca') );
			$funca->set_attribut('type', 'discrete');
			$funca->set_attribut('tableValues', $table);
		}
	}
}
?>