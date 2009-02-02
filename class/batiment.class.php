<?php
class batiment
{
	public $id;
	public $nom;
	public $image;
	
	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_repere_type() qui construit un etat "vide".
		*		-bataille_repere_type($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_repere_type($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom image FROM batiment WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->image = $id['image'];
		}
		else
		{
			$this->nom = $nom;
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
			$requete = 'UPDATE batiment SET ';
			$requete .= 'nom = "'.$this->nom.'", image = "'.$this->image.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO batiment (nom, image) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->image.'")';
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
			$requete = 'DELETE FROM batiment WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}

	function get_image($root, $resolution = 'high')
	{
		if($this->resolution != 'high') $image = $root."image/batiment_low/";
		else $image = $root."image/batiment/";

		if(file_exists($image.$this->image."_04.png")) 		{ $image .= $this->image."_04.png"; }
		elseif(file_exists($image.$this->image."_04.gif")) 	{ $image .= $this->image."_04.gif"; }
		else 														{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
		return $image;
	}
}
?>