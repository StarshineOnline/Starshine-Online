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
		$requete = "SELECT info FROM map WHERE ID = ".convert_in_pos($this->x, $this->y)."";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$terrain = type_terrain($row['info']);
		$this->ressource_terrain = ressource_terrain($terrain[1]);
		$this->ressources = array();
		$requete = "SELECT bonus1, bonus2 FROM batiment WHERE id = ".$this->get_id_batiment();
		$req = $db->query($requete);
		$batiment = $db->read_assoc($req);
		if($batiment['bonus2'] != 0)
		{
			switch($batiment['bonus2'])
			{
				case 1 :
					$this->ressources = array('Pierre' => $batiment['bonus1'] * $this->ressource_terrain['Pierre']);
				break;
				case 2 :
					$this->ressources = array('Bois' => $batiment['bonus1'] * $this->ressource_terrain['Bois']);
				break;
				case 3 :
					$this->ressources = array('Eau' => $batiment['bonus1'] * $this->ressource_terrain['Eau']);
				break;
				case 4 :
					$this->ressources = array('Sable' => $batiment['bonus1'] * $this->ressource_terrain['Sable']);
				break;
				case 5 :
					$this->ressources = array('Nourriture' => $batiment['bonus1'] * $this->ressource_terrain['Nourriture']);
				break;
				case 6 :
					$this->ressources = array('Star' => $batiment['bonus1'] * $this->ressource_terrain['Star']);
				break;
				case 7 :
					$this->ressources = array('Charbon' => $batiment['bonus1'] * $this->ressource_terrain['Charbon']);
				break;
				case 8 :
					$this->ressources = array('Essence Magique' => $batiment['bonus1'] * $this->ressource_terrain['Essence Magique']);
				break;
			}
		}
		else
		{
			foreach($this->ressources as $key => $value)
			{
				$this->ressources[$key] = $batiment['bonus1'] * $value;
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