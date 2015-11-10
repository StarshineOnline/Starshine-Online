<?php
class forge_recette_ingredient extends table
{
	protected $id_recette;
	protected $id_ingredient;
	protected $nombre;
	
	function get_id_recette()
	{
		return $this->id_recette;
	}
	
	function get_id_ingredient()
	{
		return $this->id_ingredient;
	}
	
	function get_nombre()
	{
		return $this->nombre;
	}
	
	function __construct($id_recette=0, $id_ingredient=0, $nombre=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id_recette);
		}
		else
		{
			$this->id_recette = $id_recette;
			$this->id_ingredient = $id_ingredient;
			$this->nombre = $nombre;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_recette = $vals['id_recette'];
		$this->id_ingredient = $vals['id_ingredient'];
		$this->nombre = $vals['nombre'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_recette'=>'i', 'id_ingredient'=>'i', 'nombre'=>'i');
	}
}
?>