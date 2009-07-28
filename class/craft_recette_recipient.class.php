<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class craft_recette_recipient
{
	public $id;
	public $id_recette;
	public $id_objet;
	public $resultat;
	public $prefixe;
	
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_recipient() qui construit un etat "vide".
		*		-craft_recipient($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recipient($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_recette = 0, $id_objet = 0, $resultat = '', $prefixe = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_recette, id_objet, resultat, prefixe FROM craft_recette_recipient WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_recette, $this->id_objet, $this->resultat, $this->prefixe) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_recette = $id['id_recette'];
			$this->id_objet = $id['id_objet'];
			$this->resultat = $id['resultat'];
			$this->prefixe = $id['prefixe'];
					}
		else
		{
			$this->id_recette = $id_recette;
			$this->id_objet = $id_objet;
			$this->resultat = $resultat;
			$this->prefixe = $prefixe;
						$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recette_recipient SET ';
			$requete .= 'id_recette = '.$this->id_recette.', id_objet = '.$this->id_objet.', resultat = "'.$this->resultat.'", prefixe = "'.$this->prefixe.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recette_recipient (id_recette, id_objet, resultat, prefixe) VALUES(';
			$requete .= $this->id_recette.', '.$this->id_objet.', "'.$this->resultat.'", "'.$this->prefixe.'")';
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
			$requete = 'DELETE FROM craft_recette_recipient WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_recette = '.$this->id_recette.', id_objet = '.$this->id_objet.', resultat = '.$this->resultat.', prefixe = '.$this->prefixe;
	}
}
?>