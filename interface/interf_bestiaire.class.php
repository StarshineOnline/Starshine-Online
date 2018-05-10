<?php
/**
 * @file interf_bestiaire.class.php
 * Affichage du bestiaire
 */
 
/**
 * Classe gérant l'affichage du bestiaire
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
		$this->add_onglet('Montagne', $G_url->get('terrain', 'montagne'), 'ongl_montagne', false, $terrain=='montagne');
		$this->add_onglet('Marais', $G_url->get('terrain', 'marais'), 'ongl_marais', false, $terrain=='marais');
		$this->add_onglet('Terre maudite', $G_url->get('terrain', 'terre_maudite'), 'ongl_terre_maudite', false, $terrain=='terre_maudite');
		$this->add_onglet('Route', $G_url->get('terrain', 'route'), 'ongl_route', false, $terrain=='route');
		$this->add_onglet('Eau', $G_url->get('terrain', 'eau'), 'ongl_eau', false, $terrain=='eau');
		
		/// @bug la datatable ne s'initialise pas ici (mais ça marche ensuite)
		$this->get_onglet('ongl_'.$terrain)->add( new interf_liste_monstres($terrain) );
	}
}

class interf_liste_monstres extends interf_data_tbl
{
	function __construct($terrainNomInterne)
	{
		global $db;
		
		parent::__construct('bestiaire_'.$terrainNomInterne, 'invent', false, false);
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Nom');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Terrains');
		
		$typeTerrainsSearched = array();
		$typeTerrains = type_terrain_find_all();
		foreach($typeTerrains as $terrainId => $typeTerrain){
			if($typeTerrain[0] == $terrainNomInterne)
				$typeTerrainsSearched[$terrainId] = $typeTerrain;
		}
		
		if( !empty($typeTerrainsSearched) )
		{
			$where = "affiche = 'y'";
			$where .= " AND (";
			$first = true;
			foreach($typeTerrainsSearched as $id => $typeTerrain){
				if($first) $first = false;
				else $where .= " OR ";
				$where .= "terrain = '".$id."' OR terrain LIKE '".$id.";%' OR terrain LIKE '%;".$id."' OR terrain LIKE '%;".$id.";%'";
			}
			$where .= ")";
			
			$requete = "SELECT lib, terrain, nom, level"."\n";
			$requete .= "FROM monstre"."\n";
			$requete .= "WHERE ".$where."\n";
			$requete .= "ORDER BY level ASC, xp ASC";
			
			/// @todo passer à l'objet
			$req = $db->query($requete);
			while($monstre = $db->read_array($req))
			{
				$this->nouv_ligne();
				
				$image = 'image/monstre/'.$monstre['lib'];
				if( file_exists(root.$image.'.png') )
					$image .= '.png';
				else
					$image .= '.gif';
				
				$typeTerrainIds = explode(';', $monstre['terrain']);
				$typeTerrainAffichages = array();
				foreach($typeTerrainIds as $tId)
				{
					$typeTerrainAffichages[] = $typeTerrains[$tId][1];
				}
				$typeTerrainAffichages = array_unique($typeTerrainAffichages);
				$typeTerrainAffichage = implode(', ', $typeTerrainAffichages);
				
				$this->nouv_cell( new interf_img($image, $monstre['lib']) );
				$this->nouv_cell( $monstre['nom'] );
				$this->nouv_cell( $monstre['level'] );
				$this->nouv_cell($typeTerrainAffichage);
			}
		}
	}
}
