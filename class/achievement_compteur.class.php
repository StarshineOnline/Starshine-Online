<?php // -*- php -*-
if (file_exists('../root.php'))
  include_once('../root.php');

class fake_compteur
{
	function sauver() {}
	function supprimer() {}
	function get_compteur() {	return 0;	}
	function set_compteur($compteur) {}
	function get_variable() {	return 0;	}
	function set_variable($variable) {}
	function get_id_perso() {	return 0;	}
	function set_id_perso($id_perso) {}
	function get_achievement_type() {	return 0;	}
}

Class achievement_compteur
{
	private $id;
	private $id_perso;
	private $variable;
	private $compteur;
	
	/**	
		*	Constructeur permettant la création d'un achievement.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-achievement_compteur() qui construit un etat "vide".
		*		-achievement_compteur($id) qui va chercher l'etat dont l'id est $id
		*		-achievement_compteur($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_perso = 0, $variable = '', $compteur = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_perso, variable, compteur FROM achievement_compteur WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_perso, $this->variable, $this->compteur) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_perso = $id['id_perso'];
			$this->variable = $id['variable'];
			$this->compteur = $id['compteur'];
		}
		// Recherche du compteur d'un achievement d'un joueur s'il existe
		elseif($id == 0 AND is_numeric($id_perso) AND $variable != NULL)
		{
			$requeteSQL = $db->query('SELECT id, compteur FROM achievement_compteur WHERE id_perso = '.$id_perso.' AND variable = '.$variable.'');
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id, $this->compteur) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_perso = $id_perso;
			$this->variable = $variable;
		}
		else
		{
			$this->id = $id;
			$this->id_perso = $id_perso;
			$this->variable = $variable;
			$this->compteur = $compteur;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE achievement_compteur SET ';
			$requete .= 'id_perso = "'.$this->id_perso.'", variable = "'.$this->variable.'", compteur = '.$this->compteur.'';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO achievement_compteur (id_perso, variable, compteur) VALUES(';
			$requete .= '"'.$this->id_perso.'", "'.$this->variable.'", "'.$this->compteur.'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM achievement_compteur WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_compteur()
	{
		return $this->compteur;
	}
	
	function set_compteur($compteur)
	{
		global $db;
		$this->compteur = $compteur;
		
		//On regarde si l'achievement est debloqué
		if(!isset($this->achievement_type)) $this->get_achievement_type();
		foreach($this->achievement_type AS $achiev)
		{
			if ($this->compteur >= $achiev->get_value())
			{
				$achievement = achievement::create(array('id_perso','id_achiev'), array($this->id_perso, $achiev->get_id()));
				// Achievement pas deja debloqué ?
				if(count($achievement) == 0)
				{
					$joueur = new perso($this->id_perso);
					$achievement = new achievement();
					$achievement->set_id_perso($this->id_perso);
					$achievement->set_id_achiev($achiev->get_id());
					$achievement->sauver();
					interf_alerte::enregistre(interf_alerte::msg_info, $joueur->get_nom().' debloque l\'achievement "'.$achiev->get_nom().'" !');
				}
			}
		}
	}
	
	function get_variable()
	{
		return $this->variable;
	}
	
	function set_variable($variable)
	{
		$this->variable = $variable;
	}
	
	function get_id_perso()
	{
		return $this->id_perso;
	}
	
	function set_id_perso($id_perso)
	{
		$this->id_perso = $id_perso;
	}
	
	function get_achievement_type()
	{
		global $db;
		$this->achievement_type = array();

		$requete = "SELECT id, nom, description, value, variable, secret FROM achievement_type WHERE variable = '".$this->variable."'";
		$req = $db->query($requete);
		while ($row = $db->read_assoc($req))
			$this->achievement_type[] = new achievement_type($row);
	}
	
}
?>