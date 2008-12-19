<?php
class bourg
{
	public $id_bourg;
	public $id_royaume;
	public $id_batiment;
	public $x;
	public $y;
	public $hp;
	public $nom;
	public $type;
	public $rez;
	public $rechargement;
	public $image;
	public $mine_max;
	
	/**	
	    *  	Constructeur permettant la création d'un etat de message.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un etat "vide".
	    *		-Objets($id) qui va chercher l'etat dont l'id est $id_bourse_royaume dans la base.
	**/
	function __construct($id_bourg = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $type = 0, $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_bourg) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE id = '.$id_bourg);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->type, $this->rez, $this->rechargement, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_bourg = $id_bourg;
		}
		elseif( (func_num_args() == 1) && is_array($id_bourg) )
		{
			$this->id_bourg = $id_bourg['id'];
			$this->id_royaume = $id_bourg['royaume'];
			$this->id_batiment = $id_bourg['id_batiment'];
			$this->x = $id_bourg['x'];
			$this->y = $id_bourg['y'];
			$this->hp = $id_bourg['hp'];
			$this->nom = $id_bourg['nom'];
			$this->type = $id_bourg['type'];
			$this->rez = $id_bourg['rez'];
			$this->rechargement = $id_bourg['rechargement'];
			$this->image = $id_bourg['image'];
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
			$this->id_bourg = $id_bourg;
		}
		
		//Mines max
		switch($this->nom)
		{
			case 'Bourgade' :
				$this->mine_max = 1;
			break;
			case 'Petit Bourg' :
				$this->mine_max = 2;
			break;
			case 'Bourg' :
				$this->mine_max = 4;
			break;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id_bourg > 0 )
		{
			$requete = 'UPDATE construction SET ';
			$requete .= 'royaume = '.$this->id_royaume;
			$requete .= ' WHERE id = '.$this->id_bourg;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO construction (royaume) VALUES(';
			$requete .= $this->id_royaume.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_bourg) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_bourg > 0 )
		{
			$requete = 'DELETE FROM construction WHERE id_boug = '.$this->id_bourg;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}
	
	function get_mines($ressource = false)
	{
		global $db;
		$this->mines = array();
		$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE type = 'mine' AND rechargement = ".$this->id_bourg;
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
		$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, rez, type, debut_placement, fin_placement FROM placement WHERE type = 'mine' AND rez = ".$this->id_bourg;
		$req_m = $db->query($requete);
		while($row_m = $db->read_assoc($req_m))
		{
			$this->placements[] = new placement($row_m);
		}
	}
}
?>