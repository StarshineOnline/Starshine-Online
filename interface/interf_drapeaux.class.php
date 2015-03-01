<?php
/**
 * @file interf_drapeaux.class.php
 * Interface de pose des drapeaux
 */ 

class interf_drapeaux extends interf_cont
{
	function __construct(&$royaume, $mag_factor=1, $mleft=0, $mtop=0)
	{
		global $db, $G_max_x;
		$roy_id = $royaume->get_id();
		
		/*$ts = array();
		$ts['start'] = microtime();*/
		/// @todo passer à l'objet
		$req = "SELECT count(1) from depot_royaume d, objet_royaume o, batiment b where o.id = d.id_objet and o.id_batiment = b.id and o.type = 'drapeau' and b.hp = 1 and d.id_royaume = $roy_id";
		$r_nbd = $db->query($req);
		$nbd = $db->read_array($r_nbd);
		$nb_drapeaux_dispo = $nbd[0];
		
		$req = "SELECT count(1) from placement where type = 'drapeau' and hp = 1 and royaume = $roy_id";
		$r_nbp = $db->query($req);
		$nbp = $db->read_array($r_nbp);
		$nb_drapeaux_poses = $nbp[0];
		
		//$ts['make_tmp_adj_tables'] = microtime();
		make_tmp_adj_tables($roy_id);
		//$ts['select'] = microtime();
		$req = "select * from tmp_adj_lib";
		$r_c = $db->query($req);
		$nb_cases_ok = $db->num_rows($r_c);
		
		//$ts['fini'] = microtime();
		
		$rand = rand();
		$_SESSION['map_drap_key'] = $rand;
		$map_size = $G_max_x * 4 * $mag_factor;
		$echelle = 4; // La carte est 3x plus grande
		
		// Carte
		$div_carte = $this->add( new interf_bal_cont('div', 'carte_drapeaux') );
		$carte = $div_carte->add( new interf_img('drapeaux_map.php?img='.$rand, 'Carte des poses de drapeaux', 'mapim') );
		$carte->set_attribut('style', 'left: '.$mleft.'px; top: '.$mtop.'px');
		$carte->set_attribut('width', $map_size);
		$carte->set_attribut('height', $map_size);
		$carte->set_attribut('usemap', '#mapinmap');
		$map = $div_carte->add( new interf_bal_cont('map', array('name'=>'mapinmap')) );
		while ($r = $db->read_object($r_c))
		{
			$r->x1 = ($r->x - 1) * $echelle * $mag_factor;
			$r->y1 = ($r->y - 1) * $echelle * $mag_factor;
			$r->x2 = $r->x1 + ($echelle * $mag_factor);
			$r->y2 = $r->y1 + ($echelle * $mag_factor);
			$area = $map->add( new interf_bal_smpl('area') );
			$area->set_attribut('shape', 'rect');
			$area->set_attribut('coords', $r->x1.', '.$r->y1.', '.$r->x2.', '.$r->y2);
			//$area->set_attribut('alt', $r->x.', '.$r->y);
			$area->set_tooltip($r->x.', '.$r->y);
			$area->set_attribut('href', $r->x.', '.$r->y);
			$area->set_attribut('onclick', 'return pose_drapeau('.$r->x.', '.$r->y.');');
		}
    // Javascript
    $script = $this->add( new interf_bal_smpl('script', '') );
    $script->set_attribut('type', 'text/javascript');
    $script->set_attribut('src', '../javascript/drapeaux.js');
		self::code_js('var mtop = '.$mtop.';');
		self::code_js('var mleft = '.$mleft.';');
		self::code_js('var mag = '.$mag_factor.';');
		
		// Infos
		$div_infos = $this->add( new interf_bal_cont('div', 'infos_drapeaux') );
		$div_depl = $div_infos->add( new interf_bal_cont('div', 'depl_drapeaux') );
		$div1 = $div_depl->add( new interf_bal_cont('div') );
		$div1->add( new interf_bal_smpl('a', '', array('onclick'=>'return zoom_m();', 'class'=>'icone icone-zoom')) );
		$div1->add( new interf_bal_smpl('a', '', array('onclick'=>'return move_u();', 'class'=>'icone icone-haut')) );
		$div1->add( new interf_bal_smpl('a', '', array('onclick'=>'return zoom_l();', 'class'=>'icone icone-dezoom')) );
		$div2 = $div_depl->add( new interf_bal_cont('div') );
		$div2->add( new interf_bal_smpl('a', '', array('onclick'=>'return move_l();', 'class'=>'icone icone-gauche')) );
		$div2->add( new interf_bal_smpl('a', '', array('onclick'=>'return move_b();', 'class'=>'icone icone-bas')) );
		$div2->add( new interf_bal_smpl('a', '', array('onclick'=>'return move_r();', 'class'=>'icone icone-droite')) );
		
		interf_alerte::aff_enregistres($div_infos);
		
		$div_nbr = $div_infos->add( new interf_bal_cont('div', 'drapeaux_nbr') );
		$liste_nbr = $div_nbr->add( new interf_bal_cont('ul') );
		$liste_nbr->add( new interf_bal_smpl('li', 'Drapeaux disponibles : '.$nb_drapeaux_dispo) );
		$liste_nbr->add( new interf_bal_smpl('li', 'Drapeaux posés : '.$nb_drapeaux_poses) );
		$liste_nbr->add( new interf_bal_smpl('li', 'Cases de pose autorisées : '.$nb_cases_ok) );
		
		$div_leg = $div_infos->add( new interf_bal_cont('div', 'drapeaux_legende') );
		$liste_leg = $div_leg->add( new interf_bal_cont('ul') );
		$liste_leg->add( new interf_bal_smpl('li', 'Rose : cases colonisables') );
		$liste_leg->add( new interf_bal_smpl('li', 'Cyan : cases déjà colonisées') );
		
		$btn = $div_infos->add( new interf_bal_smpl('button', 'Poser un maximum', false, 'btn btn-default') );
		$btn->set_attribut('type', 'button');
		$btn->set_attribut('onclick', 'tout_poser()');
	}
}

?>