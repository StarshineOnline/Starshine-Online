<?php
/// @addtogroup Interface
/**
 * @file interf_livre_forge.class.php
 * Gestion des recettes de forge
 */
 
/**
 * Classe gérant l'affichage du livre de forge
 */
class interf_livre_forge extends interf_accordeon
{
	function __construct($actions)
	{
		global $db, $G_url;
		parent::__construct('livre_forge');
		$perso = joueur::get_perso();
		$G_url->add('action', 'forge');
		
		/// @todo passer à l'objet
		$requete = "SELECT * FROM perso_forge WHERE id_perso = ".$perso->get_id();
		$req = $db->query($requete);
		while( $row = $db->read_assoc($req) )
		{
			$recette = new forge_recette($row['id_recette']);
			$forge = $perso->get_forge();
			$chance_reussite = pourcent_reussite($forge, $recette->get_difficulte());
			//$panneau = $this->nouv_panneau($recette->get_nom(), 'recette_'.$row['id_recette']);
			//$liste_descr = $panneau->add( new interf_bal_cont('ul') );
			$liste_descr = new interf_bal_cont('ul');
			$diff = $liste_descr->add( new interf_bal_cont('li') );
			$diff->add( new interf_bal_smpl('strong', 'Difficulté : ') );
			$diff->add( new interf_bal_smpl('span', $recette->get_difficulte()) );
			$diff->add( new interf_bal_smpl('span', ' ('.$chance_reussite.'% de chances de réussite)', false, 'small') );
			$bonus = $liste_descr->add( new interf_bal_cont('li') );
			$bonus->add( new interf_bal_smpl('strong', 'Bonus :') );
			$bonus->add( new interf_bal_smpl('span', $recette->get_descr_bonus()) );
			$malus = $liste_descr->add( new interf_bal_cont('li') );
			$malus->add( new interf_bal_smpl('strong', 'Malus :') );
			$malus->add( new interf_bal_smpl('span', $recette->get_descr_malus()) );
			// Ingrédients
			//$div_ingred = $panneau->add( new interf_bal_cont('div', false, 'liste') );
			$div_ingred = new interf_bal_cont('div', false, 'liste');
			$div_ingred->add( new interf_bal_smpl('h6', 'Ingrédients') );
			$lst_ingred = $div_ingred->add( new interf_bal_cont('ul') );
			$complet = true;
			foreach($recette->get_ingredients() as $ingredient)
			{
				$perso_ingredient = $perso->recherche_objet('o'.$ingredient->get_id_ingredient());
				if($perso_ingredient[0] < $ingredient->get_nombre())
				{
					$classe = false;
					$complet = false;
				}
				else
					$classe = 'text-success';
				//Recherche de l'objet
				$requete = "SELECT nom FROM objet WHERE id = ".$ingredient->get_id_ingredient();
				$req_i = $db->query($requete);
				$row_i = $db->read_row($req_i);
				$nbr = $perso_ingredient[0] ? $perso_ingredient[0] : '0';
				$lst_ingred->add( new interf_bal_smpl('li', $row_i[0].' X '.$ingredient->get_nombre().' ('.$nbr.')', false, $classe) );
			}
			// Objets
			//$div_obj = $panneau->add( new interf_bal_cont('div', false, 'liste') );
			$div_obj = new interf_bal_cont('div', false, 'liste');
			$div_obj->add( new interf_bal_smpl('h6', 'Arme / armure') );
			$lst_obj = $div_obj->add( new interf_bal_cont('ul') );
			$perso_obj = $perso->liste_objet($recette->get_objet());
			$li = $lst_obj->add( new interf_bal_cont('li') );
			/*$obj = objet_invent::factory($recette->get_objet());
			if( $complet && $perso_obj[0] )
				$li->add( new interf_lien($obj->get_nom().' (10 PA)', $G_url->get('id', $recette->get_id())) );
			else if( $perso_obj[0] )
				$li->add( new interf_txt($obj->get_nom()) );*/
			$objet = false;
			if( $perso_obj )
			{
				foreach($perso_obj as $o=>$n)
				{
					if( objet_invent::test_forge($o) )
						continue;
					$obj = objet_invent::factory($recette->get_objet());
					$lien = $li->add( new interf_lien_cont($G_url->get( array('id'=>$recette->get_id(),'objet'=>$o) ), false, 'text-success') );
					$lien->add( new inter_txt($obj->get_nom()) );
					$ench = $obj->get_info_enchant();
					if( $ench )
						$lien->add( new interf_bal_smpl('span', $ench, false, 'xsmall') );
					$lien->add( new inter_txt(' ('.$n.')') );
					$lien->set_tooltip('Modifier cet objet (10 PA)', 'left');
				}
			}
			if( !$objet )
			{
				$obj = objet_invent::factory($recette->get_objet());
				$li->add( new interf_txt($obj->get_nom()) );
			}
			$style = $complet ? 'success' : 'default';
			$panneau = $this->nouv_panneau($recette->get_nom(), 'recette_'.$row['id_recette'], false, $style);
			$panneau->add($liste_descr);
			$panneau->add($div_ingred);
			$panneau->add($div_obj);
			unset($panneau);
			unset($liste_descr);
			unset($div_ingred);
			unset($div_obj);
			
		}
	}
}

?>