<?php

class quete_perso extends table
{
	protected $id_perso;
	protected $id_quete;
	protected $id_etape;
	protected $avancement;
	/**
	* Constructeur
	*/
	function __construct($id_perso=0, &$id_quete=0, $id_etape=0, $avancement='')
	{
		if( func_num_args() == 1 )
		{
			$this->charger($id_perso);
		}
		else
		{
			$this->id_perso = $id_perso;
			$idq = $this->id_quete = is_object($id_quete) ? $id_quete->get_id() : $id_quete;
			if( $id_etape )
				$etape = new quete_etape($id_etape);
			else
				$etape = quete_etape::create(array('id_quete', 'etape'), array($idq, 1))[0];
			$this->id_etape = $etape ? $etape->get_id() : 0;
			if( is_object($id_quete) )
			{
				$objectifs = explode(';', $etape->get_objectif());
				for($i=0; $i<count($objectifs); $i++)
				{
					$obj = explode(':', $objectifs[$i]);
					$objectifs[$i] = $obj[0].':0';
				}
				$this->avancement = implode(';', $objectifs);
			}
			else
				$this->avancement = $avancement;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_perso = $vals['id_perso'];
		$this->id_quete = $vals['id_quete'];
		$this->id_etape = $vals['id_etape'];
		$this->avancement = $vals['avancement'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_perso'=>'i', 'id_quete'=>'i', 'id_etape'=>'i', 'avancement'=>'s');
	}
	
	/// Renvoie la quete corespondante
	function get_quete()
	{
		return new quete($this->id_quete);
	}
	
	/// Renvoie l'épape corespondante
	function get_etape()
	{
		return new quete_etape($this->id_etape);
	}
	
	/// Renvoie l'avancement
	function get_avancement()
	{
		return $this->avancement;
	}
}

?>