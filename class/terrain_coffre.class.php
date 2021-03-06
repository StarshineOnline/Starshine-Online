<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class terrain_coffre
{
	public $id;
	public $id_coffre;
	public $objet;
	public $nombre;
	public $encombrement;
	
	/**	
		*	Constructeur permettant la cr�ation d'un terrain_batiment.
		*	Les valeurs nombrer d�faut sont celles de la base de donn�e.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_coffre() qui construit un etat "vide".
		*		-terrain_coffre($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_coffre($array) qui associe les champs de $array � l'objet.
	**/
	function __construct($id = 0, $id_coffre = 0, $objet = '', $nombre = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_coffre, objet, nombre FROM terrain_coffre WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr�e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_coffre, $this->objet, $this->nombre) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_coffre = $id['id_coffre'];
			$this->objet = $id['objet'];
			$this->nombre = $id['nombre'];
			$this->encombrement = $id['encombrement'];
		}
		else
		{
			$this->id_coffre = $id_coffre;
			$this->objet = $objet;
			$this->id = $id;
			$this->nombre = $nombre;
			$this->encombrement = $encombrement;
		}
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain_coffre SET ';
			$requete .= 'id_coffre = '.$this->id_coffre.', objet = "'.$this->objet.'", nombre = '.$this->nombre.', encombrement = '.$this->encombrement;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_coffre (id_coffre, objet, nombre, encombrement) VALUES(';
			$requete .= $this->id_coffre.', "'.$this->objet.'", '.$this->nombre.', '.$this->encombrement.')';
			$db->query($requete);
			//R�cuperation du dernier ID ins�r�.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM terrain_coffre WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_coffre = '.$this->id_coffre.', objet = '.$this->objet.', nombre = '.$this->nombre;
	}

	function moins()
	{
		if($this->nombre <= 1) $this->supprimer();
		else
		{
			$this->nombre--;
			$objet = objet_invent::factory($this->objet);
			$this->encombrement -= $objet->get_encombrement();
			$this->sauver();
		}
	}
}
?>