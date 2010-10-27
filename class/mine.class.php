<?php
if (file_exists('../root.php'))
  include_once('../root.php');

if (file_exists(root.'class/contruction.class.php')) {
  include_once(root.'class/contruction.class.php');
} 

class mine extends construction
{
	function get_ressources()
	{
		global $db;
		$requete = "SELECT info FROM map WHERE ID = ".convert_in_pos($this->get_x(), $this->get_y())."";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$terrain = type_terrain($row['info']);
		$this->ressource_terrain = ressource_terrain($terrain[1]);
		$this->ressources = array();
		$requete = "SELECT * FROM batiment_bonus WHERE id_batiment = ".
			$this->get_id_batiment();
		$req = $db->query($requete);
		$speciatite = 0;
		while ($bonus = $db->read_assoc($req)) {
			if($bonus['bonus'] == 'specialite') {
				$speciatite = $bonus['value'];
			}
			if($bonus['bonus'] == 'production') {
				$production = $bonus['value'];
			}
		}
		switch($speciatite) {
		case 1 :
			$this->ressources = array('Pierre' => $production * $this->ressource_terrain['Pierre']);
			break;
		case 2 :
			$this->ressources = array('Bois' => $production * $this->ressource_terrain['Bois']);
			break;
		case 3 :
			$this->ressources = array('Eau' => $production * $this->ressource_terrain['Eau']);
			break;
		case 4 :
			$this->ressources = array('Sable' => $production * $this->ressource_terrain['Sable']);
			break;
		case 5 :
			$this->ressources = array('Nourriture' => $production * $this->ressource_terrain['Nourriture']);
			break;
		case 6 :
			$this->ressources = array('Star' => $production * $this->ressource_terrain['Star']);
			break;
		case 7 :
			$this->ressources = array('Charbon' => $production * $this->ressource_terrain['Charbon']);
			break;
		case 8 :
			$this->ressources = array('Essence Magique' => $production * $this->ressource_terrain['Essence Magique']);
			break;
		default:
			foreach($this->ressources as $key => $value) {
				$this->ressources[$key] = $production * $value;
			}
		}
	}
	
	function get_evolution()
	{
		global $db;
		$requete = "SELECT * FROM batiment WHERE cond1 = ".$this->get_id_batiment();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		
		$this->evolution = $row;
	}
	
	function get_hp_max()
	{
		global $db;
		$requete = "SELECT hp FROM batiment WHERE id = ".$this->get_id_batiment();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		
		$this->hp_max = $row['hp'];
		return $this->hp_max;
	}
}
?>