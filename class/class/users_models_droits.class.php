<?php
/**
* Objet users
*/

class users_models_droits extends baseClass
{
  // variables de table
  public $id_user_model_droit;
  public $nom_model_droit;
  public $array_model;

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
      $db->query("SELECT * FROM `".__CLASS__."` WHERE `id_user_model_droit`=".(int)$res);

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
      $this->id_user_model_droit 	= 0;
      $this->nom_model_droit			= "";
      $this->array_model					= array();
    }

    $this->droits = array();
  }

  function add()
  {
    if( !check_db_global() ) exit("Unable to load 'db' object");

    global $db;

    if( empty($this->nom_model_droit) || empty($this->array_model) )
      return false;

    if( is_array($this->array_model) )
    {
    	$this->array_model = serialize($this->array_model);
    }

    if( $db->add($this) )
    {
    	$this->id_user_model_droit = $db->last_insert_id();
    	return true;
    }

    return false;
  }

  function update()
  {
    if( !check_db_global() ) exit("Unable to load 'db' object");

    global $db;

    if( empty($this->nom_model_droit) || empty($this->array_model) )
      return false;

    if( is_array($this->array_model) )
    {
    	$this->array_model = serialize($this->array_model);
    }

    return $db->update($this);
  }

  function delete()
  {
  	global $db;

  	$db->delete($this);
  }

  function get_all()
  {
  	global $db;
  	$aTmp = array();

  	$db->query("SELECT * FROM `users_models_droits` ORDER BY `nom_model_droit` ASC");

  	while( $row = $db->read_array() )
  	{
  		$aTmp[] = new users_models_droits($row);
  	}

  	return $aTmp;
  }

  function get_droits()
  {
  	if( is_array($this->array_model) )
  	{
  		?><pre><?php print_r($this->array_model) ?></pre><?php

  		for($k=0; $k < count($this->array_model); $k++)
  		{
  			;
  		}
  	}

  }
} // fin object
?>
