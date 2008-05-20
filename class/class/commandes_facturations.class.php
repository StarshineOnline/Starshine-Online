<?php
/**
* Class de gestion des langues
*/

class commandes_facturations extends baseClass
{
	public $id_commande_facturation;
	public $id_commande;
	public $id_client_adresse;


	function __construct($res=0)
		
	
	{
		global $db;
	
	
		if( is_array($res) )
 		{
 			$this->get_variables($res);
 		}

 		else if( is_numeric($res) && ($res > 0) )
 		{
 			
 			$db->query("SELECT * FROM `".__CLASS__."` WHERE `id_commande_facturation`=".(int)$res);

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
 			$this->id_commande_facturation	= 0;
			$this->id_commande		= 0;
			$this->id_client_adresse		= 0;
 		}

	}

	function __destruct()
	{
		;
	}

	/**
 	* Ajoute d'un enregistrement dans la table
 	*/
 	function add()
 	{
 		global $db;

 		if( empty($this->id_commande) || empty($this->id_client_adresse) )
 		{
 			if( defined("DBG_MSG") && DBG_MSG )
 			{
 				echo __METHOD__." in ".basename(__FILE__)." @ l.".__LINE__." : variable obligatoire vide.<br />";
 				
 			}

 			return false;
 		}

 		if( $db->add($this) )
 		{
 			$this->id_commande_livraison = $db->last_insert_id();
 			return true;
 		}

 		return false;
 	}

	/**
 	* Met à jour un enregistrement dans la table
 	*/
 	function update()
 	{
 		global $db;

 		return $db->update($this);
 	}

	/**
 	* Supprime un enregistrement dans la table
 	*/
 	function delete()
 	{
 		global $db;

 		return $db->delete($this);
 	}
}
?>