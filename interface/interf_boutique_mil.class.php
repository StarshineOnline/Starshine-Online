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
		$facteur = $royaume->get_facteur_entretien();
		$objets = objet_royaume::create(false, false, 'id', false, 'rang_royaume < '.$royaume->get_rang());
		foreach($objets as $objet)
		{
			// nombre dans le dépot
			$requete = 'SELECT COUNT(*) FROM depot_royaume WHERE id_royaume = '.$royaume->get_id().' AND id_objet = '.$objet->get_id();
			$req_d = $db->query($requete);
			$depot = $db->read_array($req_d)[0];
			// On calcul combien le royaume peut acheter au max
			$max = 0;
			$achetable = $royaume->get_star() > $objet->get_prix();// &&  $royaume->get_food() > $objet->get_food($facteur);
			$achetable &= $royaume->get_bois() > $objet->get_bois($facteur) &&  $royaume->get_eau() > $objet->get_eau($facteur);
			$achetable &= $royaume->get_pierre() > $objet->get_pierre($facteur) &&  $royaume->get_sable() > $objet->get_sable($facteur);
			$achetable &= $royaume->get_essence() > $objet->get_essence($facteur) &&  $royaume->get_charbon() > $objet->get_charbon($facteur);
			if( $achetable )
			{
				if( $objet->get_prix() )
					$max = floor($royaume->get_star() / $objet->get_prix());
				/*if($row['food'] != 0)
					$max = min(floor($royaume->get_food($facteur) / $objet->get_food($facteur)), $max);*/
				if( $objet->get_bois($facteur) )
					$max = min(floor($royaume->get_bois() / $objet->get_bois($facteur)), $max);
				if( $objet->get_eau($facteur) )
					$max = min(floor($royaume->get_eau() / $objet->get_eau($facteur)), $max);
				if( $objet->get_pierre($facteur) )
					$max = min(floor($royaume->get_pierre() / $objet->get_pierre($facteur)), $max);
				if( $objet->get_sable($facteur) )
					$max = min(floor($royaume->get_sable() / $objet->get_sable($facteur)), $max);
				if( $objet->get_essence($facteur) )
					$max = min(floor($royaume->get_essence() / $objet->get_essence($facteur)), $max);
				if( $objet->get_charbon($facteur) )
					$max = min(floor($royaume->get_charbon() / $objet->get_charbon($facteur)), $max);
			}
			else
				$max = 0;

			$this->nouv_ligne();
			/// @todo ajouter informations sur les bâtiments (popover)
			$nom = $this->nouv_cell($objet->get_nom());
			$this->nouv_cell( $Gtrad[$objet->get_type()] );
			$this->aff_cell_rsrc($objet->get_prix(), $royaume->get_star());
			//$this->aff_cell_rsrc($objet->get_food($facteur), $royaume->get_food($facteur));
			$this->aff_cell_rsrc($objet->get_bois($facteur), $royaume->get_bois($facteur));
			$this->aff_cell_rsrc($objet->get_eau($facteur), $royaume->get_eau($facteur));
			$this->aff_cell_rsrc($objet->get_pierre($facteur), $royaume->get_pierre($facteur));
			$this->aff_cell_rsrc($objet->get_sable($facteur), $royaume->get_sable($facteur));
			$this->aff_cell_rsrc($objet->get_essence($facteur), $royaume->get_essence($facteur));
			$this->aff_cell_rsrc($objet->get_charbon($facteur), $royaume->get_charbon($facteur));
			$this->nouv_cell($depot);
			$cell_max = $this->nouv_cell( new interf_bal_smpl('a', $max) );
			if( $achetable )
			{
				$cell_max->set_attribut('onclick', '$(\'#nbr_'.$objet->get_id().'\').attr(\'value\', '.$max.');');
				$cell_max->set_tooltip('Cliquez pour acheter le maximum possible.');
				$form = new interf_form($G_url->get('action', 'achat'), 'achat_'.$objet->get_id(), 'get', 'input-group');
				$this->nouv_cell( $form );
				$nbr = $form->add( new interf_chp_form('number', 'nombre', false, 0, 'nbr_'.$objet->get_id(), 'form-control') );
		    $nbr->set_attribut('min', 0);
		    $nbr->set_attribut('step', 1);
		    $nbr->set_attribut('max', $max);
				$form->add( new interf_chp_form('hidden', 'id', false, $objet->get_id()) );
				$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
				//$btn = $btns->add( new interf_chp_form('submit', false, false, '', false, 'btn btn-default icone icone-argent') );
				$btn = $btns->add( new interf_bal_smpl('a', '', false, 'btn btn-default icone icone-argent') );
				$btn->set_attribut('onclick', 'return charger_formulaire(\'achat_'.$objet->get_id().'\');');
			}
			else
				$this->nouv_cell('&nbsp;');
			// description
			$obj = $objet->get_objet();
			$nom->set_tooltip($obj->get_description());
			$nom->set_attribut('data-html', 'true');
		}
	}
	protected function aff_cell_rsrc($val, $caisse)
	{
		$this->nouv_cell($val, false, $caisse<$val ? 'text-danger' : '');
	}
}

?>