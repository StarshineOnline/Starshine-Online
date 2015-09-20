<?php

class annonce extends table
{
	protected $date;
	protected $message;
	protected $auteur;
	
	function get_date()
	{
		return $this->date;
	}
	function set_date($val)
	{
		$this->date = $val;
		$this->champs_modif[] = 'date';
	}
	
	function get_message()
	{
		return $this->message;
	}
	function set_message($val)
	{
		$this->message = $val;
		$this->champs_modif[] = 'message';
	}
	
	function get_auteur()
	{
		return $this->auteur;
	}
	function set_auteur($val)
	{
		$this->auteur = $val;
		$this->champs_modif[] = 'auteur';
	}
	
	function __construct($message='', $date='', $auteur=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($message);
		}
		else
		{
			$this->message = $message;
			$this->date = $date;
			$this->auteur = $auteur;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->message = $vals['message'];
		$this->date = $vals['date'];
		$this->auteur = $vals['auteur'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('date'=>'s', 'message'=>'s', 'auteur'=>'i');
	}
	
	static function envoyer($message, $forum)
	{
		global $G_sujet_infos, $G_prefixe_forum;
		$auteur = array_key_exists('id_joueur', $_SESSION) ? $_SESSION['id_joueur'] : 0;
		$date = date('Y-m-d G:i:s');
		$info = new annonce($message, $date, $auteur);
		$info->sauver();
		if( $forum && isset($G_sujet_infos) && $G_sujet_infos )
		{
			if( file_exists(root.'connect_forum.php') )
			{
				include_once(root.'connect_forum.php');
				if( isset($db_forum) && $db_forum )
				{
					if( $auteur )
					{
						$requete = 'SELECT id FROM '.$G_prefixe_forum.'users WHERE username = "'.mysql_real_escape_string(joueur::get()->get_pseudo()).'"';
						$req = $db_forum->query($requete);
						if( $row = $db_forum->read_array($req) )
							$auteur = $row[0];
					}
					creer_message_forum($message, $G_sujet_infos, $auteur);
				}
			}
		}
	}
	
	static function nouvelles($date)
	{
		return self::create(false, false, 'date DESC', false, 'date > "'.date('Y-m-d G:i:s', $date).'"');
	}
	
	static function derniere($nbr=1)
	{
		return self::create(false, false, 'date DESC LIMIT '.$nbr, false, '1');
	}
}

?>