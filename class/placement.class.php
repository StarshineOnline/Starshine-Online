<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class placement
{
	public $id_placement;
	public $id_royaume;
	public $id_batiment;
	public $x;
	public $y;
	public $type;
	public $hp;
	public $debut_placement;
	public $fin_placement;
	public $rez;
	public $nom;
	public $image;
	
	/**	
	    *  	Constructeur permettant la création d'un etat de message.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un etat "vide".
	    *		-Objets($id) qui va chercher l'etat dont l'id est $id_bourse_royaume dans la base.
	**/
	function __construct($id_placement = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $rez = 0, $type = '', $debut_placement = 0, $fin_placement = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_placement) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, rez, type, debut_placement, fin_placement FROM placement WHERE id = '.$id_placement);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->rez, $this->type, $this->debut_placement, $this->fin_placement) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_placement = $id_placement;
		}
		elseif( (func_num_args() == 1) && is_array($id_placement) )
		{
			$this->id_placement = $id_placement['id'];
			$this->id_royaume = $id_placement['royaume'];
			$this->id_batiment = $id_placement['id_batiment'];
			$this->x = $id_placement['x'];
			$this->y = $id_placement['y'];
			$this->hp = $id_placement['hp'];
			$this->nom = $id_placement['nom'];
			$this->rez = $id_placement['rez'];
			$this->type = $id_placement['type'];
			$this->debut_placement = $id_placement['debut_placement'];
			$this->fin_placement = $id_placement['fin_placement'];
			$this->image = $id_placement['type'];
		}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->id_batiment = $id_batiment;
			$this->x = $x;
			$this->y = $y;
			$this->hp = $hp;
			$this->nom = $nom;
			$this->rez = $rez;
			$this->type = $type;
			$this->debut_placement = $debut_placement;
			if($this->debut_placement == 0) $this->debut_placement = time();
			$this->fin_placement = $fin_placement;
			$this->id_placement = $id_placement;
			$this->image = $type;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id_placement > 0 )
		{
			$requete = 'UPDATE placement SET ';
			$requete .= 'royaume = '.$this->id_royaume.', id_batiment = '.$this->id_batiment.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', nom = "'.$this->nom.'", rez = '.$this->rez.', type = "'.$this->type.'", debut_placement = '.$this->debut_placement.', fin_placement = '.$this->fin_placement;
			$requete .= ' WHERE id = '.$this->id_placement;
			//echo $requete;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO placement (royaume, id_batiment, x, y, hp, nom, rez, type, debut_placement, fin_placement) VALUES(';
			$requete .= $this->id_royaume.', '.$this->id_batiment.', '.$this->x.', '.$this->y.', '.$this->hp.', "'.$this->nom.'", '.$this->rez.', "'.$this->type.'", '.$this->debut_placement.', '.$this->fin_placement.')';
			//echo $requete;
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_placement) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_placement > 0 )
		{
			$requete = 'DELETE FROM cplacement WHERE id_placement = '.$this->id_placement;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}	
}
?>