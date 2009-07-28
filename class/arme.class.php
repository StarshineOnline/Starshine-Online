<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php

class arme
{
	var $id;
	var $nom;
	var $type;
	var $degat;
	var $forcex;
	var $melee;
	var $prix;
	function arme($res=0)
	{
		function __construct($res=0)
		{
			global $db;
			if( is_array($res) )
	 		{
	 			$this->get_variables($res);
	 		}
	 		else if( is_numeric($res) && ($res > 0) )
	 		{
	 			$sqlQuery = mysql_query("SELECT * FROM `perso` WHERE `id` = ".(int)$res);
	 			if(mysql_num_rows($sqlQuery) <= 0)
	 			{
	 				$this->__construct();
	 				return false;
	 			}
	 			$row = $db->read_array();
	 			$this->get_variables($row);
	 		}
	 		else
	 		{
	 		}
		}
	}
}

?>