<?php
/**
 * @file interf_bestiaire.class.php
 * Affichage du bestiaire
 */
 
/**
 * classe gérant l'affichage du bestiaire
 */
class interf_bestiaire extends interf_onglets
{
	function __construct($terrain)
	{
		global $G_url;
		parent::__construct('ongl_bestiaire', 'bestiaire');
		$G_url->add('ajax', 2);
		$this->add_onglet('Plaine', $G_url->get('terrain', 'plaine'), 'ongl_plaine', 'invent', $terrain=='plaine');
		$this->add_onglet('Forêt', $G_url->get('terrain', 'foret'), 'ongl_foret', false, $terrain=='foret');
		$this->add_onglet('Désert', $G_url->get('terrain', 'desert'), 'ongl_desert', false, $terrain=='desert');
		$this->add_onglet('Glace', $G_url->get('terrain', 'glace'), 'ongl_glace', false, $terrain=='glace');
		$this->add_onglet('Eau', $G_url->get('terrain', 'eau'), 'ongl_eau', false, $terrain=='eau');
		$this->add_onglet('Montagne', $G_url->get('terrain', 'montagne'), 'ongl_montagne', false, $terrain=='montagne');
		$this->add_onglet('Marais', $G_url->get('terrain', 'marais'), 'ongl_marais', false, $terrain=='marais');
		$this->add_onglet('Route', $G_url->get('terrain', 'route'), 'ongl_route', false, $terrain=='route');
		$this->add_onglet('Terre maudite', $G_url->get('terrain', 'terre_maudite'), 'ongl_terre_maudite', false, $terrain=='terre_maudite');
		
		/// @bug la datatable se s'initialise pas ici (mais ça marche ensuite)
		$this->get_onglet('ongl_'.$terrain)->add( new interf_liste_monstres($terrain) );
	}
}

class interf_liste_monstres extends interf_data_tbl
{
	function __construct($terrain)
	{
		global $db;
		
		parent::__construct('bestiaire_'.$terrain, 'invent', false, false);
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Nom');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Terrains');
		
		$terrains['plaine'] = 1;
		$terrains['foret'] = 2;
		$terrains['desert'] = 3;
		$terrains['glace'] = 4;
		$terrains['eau'] = 5;
		$terrains['montagne'] = 6;
		$terrains['marais'] = 7; 
		$terrains['route'] = 8; 
		$terrains['terre_maudite'] = 11;
		$terrain = $terrains[$terrain];
		$tab = array('', 'Plaine', 'Forêt', 'Désert', 'Glace', 'Eau', 'Montagne', 'Marais', 'Route', '', '', 'Terre maudite'); 
		/// @todo passer à l'objet
		$requete = "SELECT lib, terrain, nom, level FROM monstre WHERE affiche = 'y' AND (terrain = '".$terrain."' OR terrain LIKE '".$terrain.";%' OR terrain LIKE '%;".$terrain."' OR terrain LIKE '%;".$terrain.";%') ORDER BY level ASC, xp ASC";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$this->nouv_ligne();
			
			$image = 'image/monstre/'.$row['lib'];
			$terrain = explode(';', $row['terrain']);
			$type_terrain = array();
			foreach($terrain as $t)
			{
				$type_terrain[] = $tab[$t];
			}
			$type_terrain = implode(', ', $type_terrain);
			if (file_exists($image.'.png')) $image .= '.png';
			else $image .= '.gif';
			
			$this->nouv_cell( new interf_img($image, $row['lib']) );
			$this->nouv_cell( $row['nom'] );
			$this->nouv_cell( $row['level'] );
			$this->nouv_cell($type_terrain);
		}
	}
}

?>