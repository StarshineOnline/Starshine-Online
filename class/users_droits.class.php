<?php
/*
  Objet users
*/

class users_droits extends baseClass
{
  // variables de table
  public $id_user_droit;
  public $id_user;
  public $id_page;
  public $read;
  public $write;

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
      $db->query("SELECT * FROM `".__CLASS__."` WHERE `id_user_droit`=".(int)$res);

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
      $this->id_user_droit 	= 0;
      $this->id_user				= 0;
      $this->id_page				= 0;
      $this->read						= 0;
      $this->write					= 0;
    }
  }

  function add()
  {
    global $db;

    if( empty($this->id_user) || empty($this->id_page) )
    {
      return false;
    }

    if( $db->add($this) )
    {
    	$this->id_user_droit = $db->last_insert_id();
    	return true;
    }

    return false;
  }

  function update()
  {
    global $db;

    if( empty($this->id_user) || empty($this->id_page) )
   	{
      return false;
    }

    return $db->update($this);
  }

  function delete()
  {
  	global $db;

  	$db->delete($this);
  }
} // fin object
?>
