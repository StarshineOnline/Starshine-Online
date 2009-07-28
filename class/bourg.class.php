<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bourg extends construction
{
	public $mine_max;
	
	/**	
	* Constructeur permettant la création d'un bourg.
	* @access public
	* @param int $id, int $id_royaume, int $id_batiment, int $x, int $y, int $hp, string $nom, int $type, int $rez, int $rechargement, string $image
	* @return null
	*/
	function __construct($id = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $type = 0, $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->type, $this->rez, $this->rechargement, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['royaume'];
			$this->id_batiment = $id['id_batiment'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->hp = $id['hp'];
			$this->nom = $id['nom'];
			$this->type = $id['type'];
			$this->rez = $id['rez'];
			$this->rechargement = $id['rechargement'];
			$this->image = $id['image'];
		}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->id_batiment = $id_batiment;
			$this->x = $x;
			$this->y = $y;
			$this->hp = $hp;
			$this->nom = $nom;
			$this->type = $type;
			$this->rez = $rez;
			$this->rechargement = $rechargement;
			$this->image = $image;
			$this->id = $id;
		}
		
		//Mines max
		switch($this->nom)
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
		$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE type = 'mine' AND rechargement = ".$this->id;
		$req_m = $db->query($requete);
		$i = 0;
		while($row_m = $db->read_assoc($req_m))
		{
			$this->mines[$i] = new mine($row_m);
			if($ressource) $this->mines[$i]->get_ressources();
			$i++;
		}
	}
	function get_placements()
	{
		global $db;
		$this->placements = array();
		$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, rez, type, debut_placement, fin_placement FROM placement WHERE type = 'mine' AND rez = ".$this->id;
		$req_m = $db->query($requete);
		while($row_m = $db->read_assoc($req_m))
		{
			$this->placements[] = new placement($row_m);
		}
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