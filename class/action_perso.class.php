<?php
/**
 * @file action_perso.class.php
 * 
 */  

class action_perso extends script
{
	protected $id_joueur;
	
	function get_id_perso()
	{
		return $this->id_joueur;
	}
	function set_id_perso($val)
	{
		$this->id_joueur = $val;
		$this->champs_modif[] = 'id_joueur';
	}
	
	function __construct($nom='', $mode='', $id_perso=0, $action='')
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
			$this->action = $action;
		}
	}
	protected function init_tab($vals)
	{
		parent::init_tab($vals);
		$this->id_joueur = $vals['id_joueur'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = parent::get_champs();
    $tbl['id_joueur']='i';
		return $tbl;
	}
}
?>