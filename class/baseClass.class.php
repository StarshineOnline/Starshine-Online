<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
/**
* The purpose of this class is to implement some basic method for all the other class
*/

class baseClass
{
  // assign the cells with the name of the class property
  function get_variables($row="", $fnt_modifier="strtolower")
  {
    $aVars = get_object_vars($this);

    if( !is_array($row) || !is_array($aVars) || (count($aVars) <= 0) )
      return false;

    foreach($aVars as $key => $value)
    {
      if( array_key_exists($fnt_modifier($key), $row) )
      {
        $this->$key = $this->type_it($row[$fnt_modifier($key)]);
      }
    }

    return true;
  }

  // check if the array $obj contains cells with the name of the class property
  function check_object($obj="", $fnt_modifier="strtolower")
  {
    $aVars = get_object_vars($this);

    if( !is_array($obj) || !is_array($aVars) || (count($aVars) <= 0) )
      return false;

    foreach($aVars as $key => $value)
    {
      if( !array_key_exists($fnt_modifier($key), $obj) )
        return false;
    }

    return true;
  }

	/**
	* Transtypage automatique des propriétés de l'objet lors de l'instanciation
	*/
  function type_it($var)
  {
  	// Test si la variable est numérique et, si oui, la convertis avec le bon type numérique
    if( is_numeric($var) )
    {
      $var = floatval($var);

      // test si un nombre à virgule à réellement une valeur après la virgule
      if( ($var - floor($var)) > 0)
      {
        return (float)$var;
       }
      else
      {
      	return intval($var);
      }
    }

		// test si la variable est null, et retourne null si oui
    else if( is_null($var) )
    {
      return null;
    }

    // test si la variable est un tableau linéarisé
    else if( is_string($var) && @is_array(unserialize($var)) )
    {
    	return unserialize($var);
    }

		// test si la variable est une chaîne de caractère
    else if( is_string($var) )
    {
    	// $var est un datetime au format YYYY-MM-DD HH:MM:SS
    	if( preg_match("/([0-9]+){1,4}-([0-9]+){1,2}-([0-9]+){1,2} ([0-9]+){1,2}:([0-9]+){1,2}:([0-9]+){1,2}/", $var, $date) > 0 )
    	{
    		return (int)datetime_to_time($var);
    	}
    	// $var est une date au format YYYY-MM-DD
    	if( preg_match("/([0-9]+){1,4}-([0-9]+){1,2}-([0-9]+){1,2}/", $var, $date) > 0 )
    	{
    		return (int)date_to_time($var);
    	}
    	else
    	{
      	return (string)$var;
      }
    }

		// dans tous les autres cas, on retourne la variable telle qu'elle
    return $var;
  }

  /**
  * Assignation automatique des valeurs d'un tableau aux propriétés d'un objet
  * Les noms des clés sont les noms des propriétés de l'objet
  */
  function assign($data)
 	{
 		//echo count($data)." ".count(get_object_vars($this));

 		//if( !is_array($data) || (count($data) < count(get_object_vars($this))) )
 		//{
 		//	return false;
 		//}

		//if( is_array($data) && (count($data) >= count(get_object_vars($this))) )
		if( is_array($data) )
		{
 			foreach($data as $key => $value)
 			{
 				$this->$key = $this->type_it($value);
 			}
 		}

 		//else if( is_object($data) && (get_class($data) == get_class($this)) )
 		//{
 		//	$this = $data;
 		//}
 	}

  /**
 	* Récupère les propriétés textes de l'objet dans le bon language
 	*/
 	function get_languages($lng="")
 	{
 		if( empty($lng) )
 		{
 			return false;
 		}

 		global $db;
 		$pk_name = $db->get_primary_key( get_class($this) );

 		if( !is_numeric($this->$pk_name) )
 		{
 			return false;
 		}

 		$db->query("SELECT * FROM `languages_txt` LEFT JOIN `languages` USING(`id_language`) WHERE `languages`.`nom_language_court`='".$lng."' AND `languages_txt`.`table_name`='".get_class($this)."' AND `languages_txt`.`pk_name`='".$pk_name."' AND `languages_txt`.`pk_value`=".$this->$pk_name);

 		while( $row = $db->read_array() )
 		{
 			$this->$row["field_name"] = trim( stripslashes($row["txt"]) );
 		}

 		return true;
 	}

 	/**
 	* Enregistre les propriétés textes de l'objet dans le bon language
 	*/
 	function set_languages($lng="",$prefix="txt_")
 	{
 		if( empty($lng) )
 			return false;

 		global $db;

 		$aIdDB = array();

 		$query = $db->query("SHOW COLUMNS FROM `".get_class($this)."` LIKE '".$prefix."%'");

 		while( $row = $db->read_array($query) )
 		{
 			// s'il n'y a pas de textes à ajouter, on pass
 			if( !isset($this->$row["Field"]) || empty($this->$row["Field"]) )
 				continue;

 			$pk_name = $db->get_primary_key( get_class($this) );

 			$db->query("SELECT * FROM `languages_txt` LEFT JOIN `languages` USING(`id_language`) WHERE `languages`.`nom_language_court`='".$lng."' AND `languages_txt`.`table_name`='".get_class($this)."' AND `languages_txt`.`field_name`='".$row["Field"]."' AND `languages_txt`.`pk_name`='".$pk_name."' AND `languages_txt`.`pk_value`=".$this->$pk_name);

 			if($db->num_rows == 1)
 			{
 				$languages_txt = new languages_txt( $db->read_array() );
 				$languages_txt->txt = $this->$row["Field"];

 				$languages_txt->update();
 			}
 			else
 			{
 				$languages_txt = new languages_txt();
 				$languages_txt->set_master_language($lng);
 				$languages_txt->table_name 	= get_class($this);
 				$languages_txt->field_name 	= $row["Field"];
 				$languages_txt->pk_name			= $pk_name;
 				$languages_txt->pk_value		= $this->$pk_name;
 				$languages_txt->txt 				= $this->$row["Field"];

 				$languages_txt->add();
 			}

 			$aIdDB[] = $languages_txt->id_language_txt;
 		}

 		if( !is_array($aIdDB) )
			return false;

		// vérification, et suppression le cas échéant, des prix en trop
		if( defined("TEST") && TEST)
		{
			echo "DELETE FROM `languages_txt` WHERE `table_name`='".get_class($this)."' AND `pk_name`='".$pk_name."' AND `pk_value`=".$this->$pk_name.( (is_array($aIdDB) && (count($aIdDB) > 0)) ? " AND `id_language_txt` NOT IN(".implode(",",$aIdDB).")" : "" ).";<br />";
		}
		else
		{
			$db->query("DELETE FROM `languages_txt` WHERE `table_name`='".get_class($this)."' AND `pk_name`='".$pk_name."' AND `pk_value`=".$this->$pk_name.( (is_array($aIdDB) && (count($aIdDB) > 0)) ? " AND `id_language_txt` NOT IN(".implode(",",$aIdDB).")" : "" ) );
		}

 		return true;
 	}

 	function delete_languages($lng="",$prefix="txt_")
 	{
 		;
 	}
}
/**
* Fin objet base
*/
?>