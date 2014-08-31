<?php
/**
 * @file action_pet.class.php
 * 
 */  

class action_pet extends script
{
	protected $id_joueur;
	protected $type_monstre;
	
	function get_id_perso()
	{
		return $this->id_joueur;
	}
	function set_id_perso($val)
	{
		$this->id_joueur = $val;
		$this->champs_modif[] = 'id_joueur';
	}
	
	function get_type_monstre()
	{
		return $this->type_monstre;
	}
	function set_type_monstre($val)
	{
		$this->type_monstre = $val;
		$this->champs_modif[] = 'type_monstre';
	}
	
	function __construct($nom='', $mode='', $id_perso=0, $type_monstre=0, $action='')
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->mode = $mode;
			$this->id_joueur = $id_perso;
			$this->type_monstre = $type_monstre;
			$this->action = $action;
		}
	}
	protected function init_tab($vals)
	{
		parent::init_tab($vals);
		$this->id_joueur = $vals['id_joueur'];
		$this->type_monstre = $vals['type_monstre'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['id_joueur']='i';
    $tbl['type_monstre']='i';
		return $tbl;
	}
}

?>