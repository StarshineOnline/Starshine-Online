<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class craft_recette_ingredient
{
	public $id;
	public $id_recette;
	public $id_ingredient;
	public $nombre;
	public $secret;
	public $effet;

	/**	
		*	Constructeur permettant la cr?ation d'un ingredient de recette.
		*	Les valeurs nombrer d?faut sont celles de la base de donn?e.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_recette_ingredient() qui construit un etat "vide".
		*		-craft_recette_ingredient($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recette_ingredient($array) qui associe les chasecrets de $array ? l'objet.
	**/
	function __construct($id = 0, $id_recette = 0, $id_ingredient = 0, $nombre = 0, $secret = 0, $effet = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_recette, id_ingredient, nombre, secret, effet FROM craft_recette_ingredient WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_recette, $this->id_ingredient, $this->nombre, $this->secret, $this->effet) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_recette = $id['id_recette'];
			$this->id_ingredient = $id['id_ingredient'];
			$this->nombre = $id['nombre'];
			$this->secret = $id['secret'];
			$this->effet = $id['effet'];
		}
		else
		{
			$this->id_recette = $id_recette;
			$this->id_ingredient = $id_ingredient;
			$this->nombre = $nombre;
			$this->secret = $secret;
			$this->effet = $effet;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recette_ingredient SET ';
			$requete .= 'id_recette = '.$this->id_recette.', id_ingredient = '.$this->id_ingredient.', nombre = '.$this->nombre.', secret = '.$this->secret.', effet = "'.$this->effet.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recette_ingredient (id_recette, id_ingredient, nombre, secret, effet) VALUES(';
			$requete .= $this->id_recette.', '.$this->id_ingredient.', '.$this->nombre.', '.$this->secret.', "'.$this->effet.'")';
			$db->query($requete);
			//R?cuperation du dernier ID ins?r?.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM craft_recette_ingredient WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}
}
?>