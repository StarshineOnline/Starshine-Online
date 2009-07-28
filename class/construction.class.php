<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class construction
{
	public $id;
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
	
	/**	
	    *  	Constructeur permettant la création d'une construction.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-construction() qui construit un etat "vide".
	    *		-construction($id) qui va chercher l'etat dont l'id est $id
	    *		-construction($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $type = 0, $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
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
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE construction SET ';
			$requete .= 'royaume = '.$this->id_royaume.', id_batiment = '.$this->id_batiment.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', nom = "'.$this->nom.'", rez = '.$this->rez.', rechargement = '.$this->rechargement.', image = "'.$this->image.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO construction (royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image) VALUES(';
			$requete .= $this->id_royaume.', '.$this->id_batiment.', '.$this->x.', '.$this->y.', '.$this->hp.', "'.$this->nom.'", "'.$this->type.'", '.$this->rez.', '.$this->rechargement.', "'.$this->image.'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM construction WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}
}
?>