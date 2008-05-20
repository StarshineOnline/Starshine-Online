<?php
/*
  Objet users
*/

class users extends baseClass
{
  // variables de table
  public $id_user;
  public $fname;
  public $lname;
  public $login;
  public $passwd;
  public $email;
  public $id_user_model_droit;
  public $code_sage_user;
  public $ip;
  public $time_connect;
  public $nb_connect;
  public $active;

	// variables interne à l'objet
  public $droits;

  // Constructeur
  function __construct($res=0)
  {
    if( is_array($res) )
    {
      $this->get_variables($res);
    }

    else if( is_numeric($res) && ($res > 0) )
    {
      if( !check_db_global() ) exit("Unable to load 'db' object");

      global $db;
      $db->query("SELECT * FROM `".__CLASS__."` WHERE `id_user`=".(int)$res);

      if($db->num_rows <= 0)
 			{
 				$this->__construct();
 				return false;
 			}

 			$row = $db->read_array();
 			$this->get_variables($row);
    }
    else if( is_string($res) && (strlen($res) == 40) )
    {
      if( !check_db_global() ) exit("Unable to load 'db' object");

      global $db;
      $db->query("SELECT * FROM `".__CLASS__."` WHERE SHA1(`id_user`)='".$res."'");

      if($db->num_rows <= 0)
 			{
 				$this->__construct();
 				return false;
 			}

 			$row = $db->read_array();
 			$this->get_variables($row);
    }
    else
    {
      $this->id_user    	= 0;
      $this->fname      	= "";
      $this->lname      	= "";
      $this->login      	= "";
      $this->passwd     	= "";
      $this->email      	= "";
      $this->id_user_model_droit = 0;
      $this->code_sage_user	= 0;
      $this->ip         	= $_SERVER["REMOTE_ADDR"];
      $this->time_connect	= time();
      $this->nb_connect  	= 0;
      $this->active     	= 1;
    }
  }

  function add()
  {
    if( !check_db_global() ) exit("Unable to load 'db' object");

    global $db;

    if( empty($this->fname) || empty($this->lname) || empty($this->login) || empty($this->passwd) || empty($this->email) )
    {
      return false;
    }

    if( $db->add($this) )
    {
    	$this->id_user = $db->last_insert_id();
    	return true;
    }

    return false;
  }

  function update()
  {
    if( !check_db_global() ) exit("Unable to load 'db' object");

    global $db;

    if( empty($this->fname) || empty($this->lname) || empty($this->login) || empty($this->passwd) || empty($this->email) )
      return false;

    return $db->update($this);
  }

  function delete()
  {
  	;
  }

	/**
	* Récupère les droits complets d'un utilisateur
	*/
  function get_droits()
  {
  	global $db;
  	$this->droits = array();

  	$db->query("SELECT * FROM `users_droits` WHERE `id_user`=".$this->id_user);

  	while($row = $db->read_array() )
  	{
  		$this->droits[] = new users_droits($row);
  	}
  }

  /**
  * Fixe les droits
  */
  function set_droits()
  {
  	if( !is_array($this->droits) )
  	{
 			return false;
 		}

		global $db;
		$aIdDB = array();

		for($k=0; $k < count($this->droits); $k++)
		{
			$user_droit = $this->droits[$k];

			// récupération id_article_prix
			$db->query("SELECT `id_user_droit` FROM `users_droits` WHERE `id_user`=".$this->id_user." AND `id_page`=".$this->droits[$k]->id_page);

			if( $db->num_rows <= 0)
			{
				$user_droit->add();
			}
			else
			{
				$row = $db->read_row();
				$user_droit->id_user_droit = (int)$row[0];
				$user_droit->update();
			}

			$aIdDB[] = $user_droit->id_user_droit;
		}// fin for

		if( !is_array($aIdDB) )
			return false;

		// vérification, et suppression le cas échéant, des prix en trop
		if( defined("TEST") && TEST)
		{
			echo "DELETE FROM `users_droits` WHERE `id_user`=".$this->id_user.( (is_array($aIdDB) && (count($aIdDB) > 0)) ? " AND `id_user_droit` NOT IN(".implode(",",$aIdDB).")" : "" ).";<br />";
		}
		else
		{
			$db->query("DELETE FROM `users_droits` WHERE `id_user`=".$this->id_user.( (is_array($aIdDB) && (count($aIdDB) > 0)) ? " AND `id_user_droit` NOT IN(".implode(",",$aIdDB).")" : "" ) );
		}

		return true;
  }

  /**
  * Trouve l'index des droits d'une page dans le tableau de droits
  */
  function index_droit($p)
  {
  	if( !is_array($this->droits) )
  	{
  		$this->get_droits();
  	}

  	for($k=0; $k < count($this->droits); $k++)
  	{
  		if( $this->droits[$k]->id_page == $p )
  		{
  			return $k;
  		}
  	}
  }

	/**
	* Vérifie les droits d'un utilisateur pour une page donnée
	*/
  function check_droits($p)
  {
  	global $db;

  	$db->query("SELECT * FROM `users_droits` WHERE `id_user`=".$this->id_user." AND `id_page`=".(int)$p);

		// Par défaut, aucun accès
  	if( $db->num_rows <= 0 )
  	{
  		return array("read" => false, "write" => false);
  	}

  	$row = $db->read_array();
  	return array("read" => (bool)intval($row["read"]), "write" => (bool)intval($row["write"]));
  }

  function get_all()
  {
  	global $db;
  	$aTmp = array();

  	$db->query("SELECT * FROM `users` ORDER BY `lname` ASC, `fname` ASC");

  	while( $row = $db->read_array() )
  	{
  		$aTmp[] = new users($row);
  	}

  	return $aTmp;
  }
} // fin object
?>
