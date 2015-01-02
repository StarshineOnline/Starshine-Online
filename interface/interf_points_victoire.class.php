<?php
/**
 * @file 
 *  
 * Interface de la bourse des royaume 
 */ 

class interf_points_victoire extends interf_data_tbl
{
	function __construct($peut_agir)
	{
  	global $G_url;
		parent::__construct('pts_vic', '', false, false);
		
		$this->nouv_cell('Nom');
		$this->nouv_cell('Coût');
		$this->nouv_cell('Description');
		$this->nouv_cell('Durée');
		
		$actions = point_victoire_action::create(0, 0);
		foreach($actions as $action)
		{
			$this->nouv_ligne();
			if( $peut_agir )
				$this->nouv_cell( new interf_lien($action->get_nom(), $G_url->get(array('action'=>'utilise', 'id'=>$action->get_id()))) );
			else
				$this->nouv_cell( $action->get_nom() );
			$this->nouv_cell( $action->get_cout() );
			$this->nouv_cell( $action->get_description() );
			$this->nouv_cell( transform_min_temp($action->get_duree()) );
		}
	}
}

?>