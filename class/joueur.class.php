<?php
class joueur extends table
{
	protected $id;
	protected $login;
	protected $mdp;
	protected $pseudo;
	protected $droits;
	protected $email;
	//! Constructeur
	/**
		Le constructeur va initialiser les attributs
		
		@param int(11) $id  L'id du joueur
		@param String $login  L'identifiant du joueur
		@param String $mdp  Le mot de passe
		@param String $pseudo Le nom du joueur (vide par defaut)
		@param int(11) $droits Niveau d'acces du joueur (0 par défaut)
		@param String $email Adresse internet du joueur(null par défaut)
	*/
	function __construct($id=0, $login = '', $mdp = '', $pseudo = '', $droits = 0, $email = '')
	{
		global $db;
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requete = $db->query("SELECT id, login, mdp, pseudo, droits, email FROM joueur WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requete) > 0 )
				list($this->id, $this->login, $this->mdp, $this->pseudo, $this->droits, $this->email) = $db->read_array($requete);
			else 
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->login = $id['login'];
			$this->mdp = $id['mdp'];
			$this->pseudo = $id['pseudo'];
			$this->droits = $id['droits'];
			$this->email = $id['email'];
		}
		else
		{
			$this->id = $id;
			$this->login = $login;
			$this->mdp = $mdp;
			$this->pseudo = $pseudo;
			$this->droits = $droits;
			$this->email = $email;
		}
	
	}
	
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'login = "'.mysql_escape_string($this->login).'", mdp = "'.mysql_escape_string($this->mdp).'", pseudo = "'.mysql_escape_string($this->pseudo).'", droits = "'.mysql_escape_string($this->droits).'", email = "'.mysql_escape_string($this->email).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE joueur SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO joueur (login, mdp, pseudo, droits, email) VALUES(';
			$requete .= '"'.mysql_escape_string($this->login).'", "'.mysql_escape_string($this->mdp).'", "'.mysql_escape_string($this->pseudo).'", '.mysql_escape_string($this->droits).', "'.mysql_escape_string($this->email).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_pseudo()
	{
		return $this->pseudo;
	}
	
	function set_pseudo($pseudo)
	{
		$this->pseudo = $pseudo;
		$this->champs_modif[] = 'pseudo';
	}
	
	function get_login()
	{
		return $this->login;
	}
	
	function set_login($login)
	{
		$this->login = $login;
		$this->champs_modif[] = 'login';
	}
	
	function get_mdp()
	{
		return $this->mdp;
	}
	
	function set_mdp($mdp)
	{
		$this->mdp = $mdp;
		$this->champs_modif[] = 'mdp';
	}
	
	function get_email()
	{
		return $this->email;
	}
	
	function set_email($email)
	{
		$this->email = $email;
		$this->champs_modif[] = 'email';
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'login, mdp, pseudo, droits, email';
  }
	/// Renvoie la liste des valeurs des champs pour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.$this->login.'", "'.$this->mdp.'", "'.$this->pseudo.'", "'.$this->droits.'", "'.$this->email.'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'login = "'.$this->login.'", mdp = "'.$this->mdp.'", pseudo = "'.$this->pseudo.'", droits = "'.$this->droits.'", email = "'.$this->email.'"';
	}
	
}

?>