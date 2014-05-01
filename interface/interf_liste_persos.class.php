<?php

class interf_liste_persos extends interf_tableau
{
	function __construct(&$persos, $page)
	{
		parent::__construct();
		$this->nouv_cell('Nom');
		$this->nouv_cell('Race');
		$this->nouv_cell('Classe');
		
		$page .= strpos($page, '?') ? '&' : '?';
			
		foreach($persos as $p)
		{
			$this->nouv_ligne();
			$lien = new interf_bal_smpl('a', $p->get_nom());
			$this->nouv_cell( $lien );
			$lien->set_attribut('href', $page.'nom='.urlencode($p->get_nom()).'&id='.$p->get_id());
			$lien->set_attribut('onClick', 'return  charger(this.href);');
			$this->nouv_cell( $p->get_race() );
			$this->nouv_cell( $p->get_classe() );
		}
	}
}
?>