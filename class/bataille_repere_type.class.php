<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bataille_repere_type
{
	public $id;
	public $nom;
	public $description;
	public $ajout_groupe;
	
	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_repere_type() qui construit un etat "vide".
		*		-bataille_repere_type($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_repere_type($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '', $ajout_groupe = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, ajout_groupe, image FROM bataille_repere_type WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->ajout_groupe, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->ajout_groupe = $id['ajout_groupe'];
			$this->image = $id['image'];
		}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->ajout_groupe = $ajout_groupe;
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
			$requete = 'UPDATE bataille_repere_type SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'", ajout_groupe = '.$this->ajout_groupe.', image = "'.$this->image.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille_repere_type (nom, description, ajout_groupe, image) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'", '.$this->ajout_groupe.', "'.$this->image.'")';
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
			$requete = 'DELETE FROM bataille_repere_type WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_nom()
	{
		return $this->nom;
	}
}
?>