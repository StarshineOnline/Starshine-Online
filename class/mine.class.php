<?php
class mine
{
	public $id_mine;
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
	function __construct($id_mine = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $type = 0, $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_mine) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE id = '.$id_mine);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->type, $this->rez, $this->rechargement, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_mine = $id_mine;
		}
		elseif( (func_num_args() == 1) && is_array($id_mine) )
		{
			$this->id_mine = $id_mine['id'];
			$this->id_royaume = $id_mine['royaume'];
			$this->id_batiment = $id_mine['id_batiment'];
			$this->x = $id_mine['x'];
			$this->y = $id_mine['y'];
			$this->hp = $id_mine['hp'];
			$this->nom = $id_mine['nom'];
			$this->type = $id_mine['type'];
			$this->rez = $id_mine['rez'];
			$this->rechargement = $id_mine['rechargement'];
			$this->image = $id_mine['image'];
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
			$this->id_mine = $id_mine;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id_mine > 0 )
		{
			$requete = 'UPDATE construction SET ';
			$requete .= 'royaume = '.$this->id_royaume;
			$requete .= ' WHERE id = '.$this->id_mine;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO construction (royaume) VALUES(';
			$requete .= $this->id_royaume.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_mine) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_mine > 0 )
		{
			$requete = 'DELETE FROM construction WHERE id_boug = '.$this->id_mine;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}	
}
?>