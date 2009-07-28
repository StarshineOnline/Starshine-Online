<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class perso_recette
{
	public $id;
	public $id_recette;
	public $id_perso;
	public $nombre;
	
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-perso_recette() qui construit un etat "vide".
		*		-perso_recette($id) qui va chercher l'etat dont l'id est $id
		*		-perso_recette($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_recette = 0, $id_perso = 0, $nombre = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_recette, id_perso, nombre FROM perso_recette WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_recette, $this->id_perso, $this->nombre) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_recette = $id['id_recette'];
			$this->id_perso = $id['id_perso'];
			$this->nombre = $id['nombre'];
		}
		else
		{
			$this->id_recette = $id_recette;
			$this->id_perso = $id_perso;
			$this->nombre = $nombre;
						$this->id = $id;
		}
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE perso_recette SET ';
			$requete .= 'id_recette = '.$this->id_recette.', id_perso = '.$this->id_perso.', nombre = '.$this->nombre;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO perso_recette (id_recette, id_perso, nombre) VALUES(';
			$requete .= $this->id_recette.', '.$this->id_perso.', '.$this->nombre.')';
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
			$requete = 'DELETE FROM perso_recette WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_recette = '.$this->id_recette.', id_perso = '.$this->id_perso.', nombre = '.$this->nombre;
	}

	function recov($id_perso, $id_recette)
	{
		global $db;
		$requete = "SELECT id, id_recette, id_perso, nombre FROM perso_recette WHERE id_perso = ".$id_perso." AND id_recette = ".$id_recette;
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			return new perso_recette($db->read_assoc);
		}
		else return false;
	}
}
?>