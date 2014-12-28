<?php
/**
 * @file interf_quete.class.php
 * Interface de la bourse des royaume 
 */ 
include_once(root.'inc/ressource.inc.php');
include_once(root.'class/quete.class.php');

//Créer l'interface de la quete selon l'id.

class interf_quete extends interf_dialogBS
{
	function __construct($idquete)
	{
		$quete = new quete($idquete);
		parent::__construct($quete->get_nom(), true, 'quete');		
	}
	

}
//Créer l'interface de gestion des quetes pour les rois

class interf_quete_royaume extends interf_cont
{
	function __construct()
	{
		$this->aff_tableau();
	}
	
	protected function aff_tableau()
	{
		global $db, $ress;
		$tbl = $this->add( new interf_data_tbl('tbl_quete', '', false, false, false, 4	) );
		$tbl->nouv_cell('Quete');
		$tbl->nouv_cell('Type');
		$tbl->nouv_cell('Fournisseur');
		$tbl->nouv_cell('Repetable');
		$tbl->nouv_cell('Cout');
		
		//on charge toutes les quetes
		$requete = "SELECT id FROM quete";
		$req = $db->query($requete);
		
		//var_dump($liste);
		while( $row = $db->read_assoc($req) )
		{
			$quete = new quete($row['id']);
			$tbl->nouv_ligne();
			$tbl->nouv_cell(new interf_lien($quete->get_nom(), 'quete.php?q='.$quete->get_id()));
			$tbl->nouv_cell($quete->get_type());
			$tbl->nouv_cell($quete->get_fournisseur());
			$tbl->nouv_cell($quete->get_repetable());
			$tbl->nouv_cell($quete->get_star_royaume());
		}
	}
}
?>
