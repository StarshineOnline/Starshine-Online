<?php
/**
 * @file interf_quete.class.php
 * Interface de la bourse des royaume 
 */ 
include_once(root.'inc/ressource.inc.php');
include_once(root.'class/quete.class.php');
include_once(root.'class/quete_etape.class.php');

//Créer l'interface de la quete selon l'id.

class interf_quete extends interf_dialogBS
{
	function __construct($idquete, &$royaume)
	{
		$quete = new quete($idquete);
		$champ = array('id_quete', 'etape', 'variante');
		$valeur = array($idquete, 1, 0);
		$etape = quete_etape::create($champ, $valeur)[0];
		
		parent::__construct($quete->get_nom(), true, 'quete');	
		$this->add( new interf_bal_smpl('h5', 'Nom de la quête', false, false));	
		$this->add( new interf_bal_smpl('br'));	
		$this->add( new interf_bal_smpl('span', $quete->get_nom(), false, false));
		//$this->add( new interf_bal_smpl('span', $etape->get_nom(), false, false));
		$this->add( new interf_bal_smpl('br'));	
	
		$this->add( new interf_bal_smpl('h5', 'Description', false, false));	
		$this->add( new interf_bal_smpl('br'));	
		$this->add( new interf_bal_smpl('span', var_dump($etape), false, false));	
		$this->add( new interf_bal_smpl('br'));	

		$this->add( new interf_bal_smpl('h5', 'Achat de la quete ?', false, false));	
		$this->add( new interf_bal_smpl('br'));	
		$this->add( new interf_lien('Acheter', $quete->achat($quete, $royaume)));
		
		$this->add( new interf_bal_smpl('br'));	
	}
}
	
		
//Créer l'interface de gestion des quetes pour les rois

class interf_quete_royaume extends interf_cont
{
	function __construct(&$royaume)
	{
		$this->aff_tableau($royaume);
	}
	
	protected function aff_tableau(&$royaume)
	{
		global $db, $ress;
		$tbl = $this->add( new interf_data_tbl('tbl_quete', '', false, false, false, 4	) );
		$tbl->nouv_cell('Quete');
		$tbl->nouv_cell('Type');
		$tbl->nouv_cell('Fournisseur');
		$tbl->nouv_cell('Repetable');
		$tbl->nouv_cell('Cout');
		
		//on charge toutes les quetes
		$requete = "SELECT * FROM quete WHERE star_royaume > 0 AND id NOT IN (SELECT id_quete FROM quete_royaume WHERE id_royaume = ".$royaume->get_id().")";
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
