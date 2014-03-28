<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bourg extends construction
{
	public $mine_max;
	
	function get_mine_max()
	{
		//Mines max
		switch($this->get_nom())
		{
			case 'Bourgade' :
				$this->mine_max = 1;
			break;
			case 'Petit bourg' :
				$this->mine_max = 2;
			break;
			case 'Bourg' :
				$this->mine_max = 4;
			break;
		}
	}

	function get_mines($ressource = false)
	{
		global $db;
		$this->mines = array();
		$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement FROM construction WHERE type = 'mine' AND rechargement = ".$this->get_id();
		$req_m = $db->query($requete);
		$i = 0;
		while($row_m = $db->read_assoc($req_m))
		{
			$this->mines[$i] = new mine($row_m['id']);
			if($ressource) $this->mines[$i]->get_ressources();
			$i++;
		}
	}
	function get_placements()
	{
		global $db;
		$this->placements = placement::create(array('type', 'rez'), array('mine', $this->get_id()));
	}
	
	function get_mine_total()
	{
		$this->mine_total = 0;
		if(!isset($this->mines)) $this->get_mines();
		$this->mine_total += count($this->mines);
		if(!isset($this->placements)) $this->get_placements();
		$this->mine_total += count($this->placements);
		return $this->mine_total;
	}
}
?>