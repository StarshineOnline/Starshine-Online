<?php
/**
 * @file interf_boutique_mil.class.php
 * Interface de la bouinterf_boutique militaire
 */ 

class interf_boutique_mil extends interf_data_tbl
{
	function __construct(&$royaume, $action=false)
	{
  	global $db, $Gtrad, $G_url;
		parent::__construct('boutique_militaire', '', false, false, false, 1);
		
		//$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Nom');
		$this->nouv_cell('Type');
		$this->nouv_cell( new interf_img('../image/starsv2.png') )->set_tooltip($Gtrad['star']);
		//$this->nouv_cell( new interf_img('../image/icone/ressources_nourriture.png') )->set_tooltip($Gtrad['foor']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_bois.png') )->set_tooltip($Gtrad['bois']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_eau.png') )->set_tooltip($Gtrad['eau']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_pierre.png') )->set_tooltip($Gtrad['pierre']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_sable.png') )->set_tooltip($Gtrad['sable']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_essence.png') )->set_tooltip($Gtrad['essence']);
		$this->nouv_cell( new interf_img('../image/icone/ressources_charbon.png') )->set_tooltip($Gtrad['charbon']);
		$this->nouv_cell('Dépot');
		$this->nouv_cell('Max');
		$this->nouv_cell('Achat');
	
  	/// @todo passer à l'objet
		$requete = 'SELECT * FROM objet_royaume';
		$req = $db->query($requete);
		$i = 0;
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			// nombre dans le dépot
			$requete = 'SELECT COUNT(*) FROM depot_royaume WHERE id_royaume = '.$royaume->get_id().' AND id_objet = '.$row['id'];
			$req_d = $db->query($requete);
			$depot = $db->read_array($req_d)[0];
			// On calcul combien le royaume peut acheter au max
			$max = 0;
			$achetable = $royaume->get_star() > $row['prix'];// &&  $royaume->get_food() > $row['food'];
			$achetable &= $royaume->get_bois() > $row['bois'] &&  $royaume->get_eau() > $row['eau'];
			$achetable &= $royaume->get_pierre() > $row['pierre'] &&  $royaume->get_sable() > $row['sable'];
			$achetable &= $royaume->get_essence() > $row['essence'] &&  $royaume->get_charbon() > $row['charbon'];
			if( $achetable )
			{
				if($row['prix'] != 0)
					$max = floor($royaume->get_star() / $row['prix']);
				/*if($row['food'] != 0)
					$max = min(floor($royaume->get_food() / $row['food']), $max);*/
				if($row['pierre'] != 0)
					$max = min(floor($royaume->get_pierre() / $row['pierre']), $max);
				if($row['bois'] != 0)
					$max = min(floor($royaume->get_bois()/$row['bois']), $max);
				if($row['eau'] != 0 )
					$max = min(floor($royaume->get_eau()/$row['eau']), $max);
				if($row['sable'] != 0)
					$max = min(floor($royaume->get_sable()/$row['sable']), $max);
				if($row['charbon'] != 0)
					$max = min(floor($royaume->get_charbon()/$row['charbon']), $max);
				if($row['essence'] != 0)
					$max = min(floor($royaume->get_essence()/$row['essence']), $max);
			}
			else
				$max = 0;

			$this->nouv_ligne();
			$this->nouv_cell($row['nom']);
			$this->nouv_cell( $Gtrad[$row['type']] );
			$this->aff_cell_rsrc($row['prix'], $royaume->get_star());
			//$this->aff_cell_rsrc($row['food'], $royaume->get_food());
			$this->aff_cell_rsrc($row['bois'], $royaume->get_bois());
			$this->aff_cell_rsrc($row['eau'], $royaume->get_eau());
			$this->aff_cell_rsrc($row['pierre'], $royaume->get_pierre());
			$this->aff_cell_rsrc($row['sable'], $royaume->get_sable());
			$this->aff_cell_rsrc($row['essence'], $royaume->get_essence());
			$this->aff_cell_rsrc($row['charbon'], $royaume->get_charbon());
			$this->nouv_cell($depot);
			$cell_max = $this->nouv_cell( new interf_bal_smpl('a', $max) );
			if( $achetable )
			{
				$cell_max->set_attribut('onclick', '$(\'#nbr_'.$row['id'].'\').attr(\'value\', '.$max.');');
				$cell_max->set_tooltip('Cliquez pour acheter le maximum possible.');
				$form = new interf_form($G_url->get('action', 'achat'), 'achat_'.$row['id'], 'get', 'input-group');
				$this->nouv_cell( $form );
				$nbr = $form->add( new interf_chp_form('number', 'nombre', false, 0, 'nbr_'.$row['id'], 'form-control') );
		    $nbr->set_attribut('min', 0);
		    $nbr->set_attribut('step', 1);
		    $nbr->set_attribut('max', $max);
				$form->add( new interf_chp_form('hidden', 'id', false, $row['id']) );
				$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
				//$btn = $btns->add( new interf_chp_form('submit', false, false, '', false, 'btn btn-default icone icone-argent') );
				$btn = $btns->add( new interf_bal_smpl('a', '', false, 'btn btn-default icone icone-argent') );
				$btn->set_attribut('onclick', 'return charger_formulaire(\'achat_'.$row['id'].'\');');
			}
			else
				$this->nouv_cell('&nbsp;');
		}
	}
	protected function aff_cell_rsrc($val, $caisse)
	{
		$this->nouv_cell($val, false, $caisse<$val ? 'text-danger' : '');
	}
}

?>