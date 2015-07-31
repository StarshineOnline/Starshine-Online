<?php
/// @addtogroup Interface
/**
 * @file interf_livre_alchimie.class.php
 * Gestion des recettes d'(alchimie)
 */
 
/**
 * Classe gérant l'affichage des livres de sorts/compétences
 */
class interf_livre_alchimie extends interf_accordeon
{
	function __construct($actions)
	{
		global $db;
		parent::__construct('livre_alchimie');
		$perso = joueur::get_perso();
		/// @todo à améliorer
		$case = new map_case(array('x' => $perso->get_x(), 'y' => $perso->get_y()));
		$R = new royaume($case->get_royaume());
		$recette = new craft_recette();
		$types = $recette->get_info_joueur($perso, $R);
		/// @todo passer à l'objet
		$requete = "SELECT * FROM perso_recette WHERE id_perso = ".$perso->get_id();
		$req = $db->query($requete);
		while( $row = $db->read_assoc($req) )
		{
			$complet = true;
			//$possible = count($types['mortier']) > 0; // ?
			//recherche de la recette
			$recette = new craft_recette($row['id_recette']);
			$recette->get_ingredients();
			$recette->get_recipients();
			$recette->get_instruments();
			$alchimie = $perso->get_alchimie();
			$chance_reussite = pourcent_reussite($alchimie, $recette->difficulte);
			/*$panneau = $this->nouv_panneau($recette->nom, 'recette_'.$row['id_recette']);
			$div_diff = $panneau->add( new interf_bal_cont('div') );*/
			$div_diff = new interf_bal_cont('div');
			$div_diff->add( new interf_bal_smpl('strong', 'Difficulté : '.$recette->difficulte) );
			$div_diff->add( new interf_bal_smpl('span', ' ('.$chance_reussite.'% de chances de réussite)', alse, 'small') );
			// Ingrédients
			//$div_ingred = $panneau->add( new interf_bal_cont('div', false, 'liste') );
			$div_ingred = new interf_bal_cont('div', false, 'liste');
			$div_ingred->add( new interf_bal_smpl('h6', 'Ingrédients') );
			$lst_ingred = $div_ingred->add( new interf_bal_cont('ul') );
			$complet = true;
			foreach($recette->ingredients as $ingredient)
			{
				$perso_ingredient = $perso->recherche_objet('o'.$ingredient->id_ingredient);
				if($perso_ingredient[0] < $ingredient->nombre)
				{
					$classe = false;
					$complet = false;
				}
				else
					$classe = 'text-success';
				//Recherche de l'objet
				$requete = "SELECT nom FROM objet WHERE id = ".$ingredient->id_ingredient;
				$req_i = $db->query($requete);
				$row_i = $db->read_row($req_i);
				$nbr = $perso_ingredient[0] ? $perso_ingredient[0] : '0';
				$lst_ingred->add( new interf_bal_smpl('li', $row_i[0].' X '.$ingredient->nombre.' ('.$nbr.')', false, $classe) );
			}
			// Récipients
			//$div_recip = $panneau->add( new interf_bal_cont('div', false, 'liste') );
			$div_recip = new interf_bal_cont('div', false, 'liste');
			$div_recip->add( new interf_bal_smpl('h6', 'Récipients') );
			$lst_recip = $div_recip->add( new interf_bal_cont('ul') );
			$recip = false;
			$sel = new interf_select_form('recipient', false, false, 'form-control');
			foreach($recette->recipients as $recipient)
			{
				//Recherche de l'objet
				$requete = "SELECT nom FROM objet WHERE id = ".$recipient->id_objet;
				$req_r = $db->query($requete);
				$row_r = $db->read_row($req_r);
				$perso_recipient = $perso->recherche_objet('o'.$recipient->id_objet);
				if($perso_recipient[0] < 1)
					$classe = false;
				else
				{
					$classe = 'text-success';
					$recip = true;
					$sel->add_option($row_r[0], $recipient->id);
				}
				//Recherche du résultat
				$id_resultat = explode('-', $recipient->resultat);
				$id_resultat = decompose_objet($id_resultat[0]);
				$requete = "SELECT description, pa, mp, effet FROM objet WHERE id = ".$id_resultat['id_objet'];
				$req_i = $db->query($requete);
				$row_i = $db->read_assoc($req_i);
				$li = $lst_recip->add( new interf_bal_smpl('li', $row_r[0], false, $classe) );
				$li->set_tooltip(description($row_i['description'], $row_i).' - (coute '.$row_i['pa'].' PA / '.$row_i['mp'].' MP a utiliser)', 'left');
			}
			// Instruments
			//$div_instr = $panneau->add( new interf_bal_cont('div', false, 'liste') );
			$div_instr = new interf_bal_cont('div', false, 'liste');
			$div_instr->add( new interf_bal_smpl('h6', 'Instruments') );
			$lst_instr = $div_instr->add( new interf_bal_cont('ul') );
			$pa_total = 0;
			$mp_total = 0;
			$star_total = 0;
			foreach($recette->instruments as $instrument)
			{
				$pa = $types[$instrument->type]['pa'] ? $types[$instrument->type]['pa'] : 0;
				$mp = $types[$instrument->type]['pa'] ? $types[$instrument->type]['mp'] : 0;
				$stars = $types[$instrument->type]['pa'] ? $types[$instrument->type]['cout'] : 0;
				$txt = $instrument->type.' ('.$pa.' pa, '.$mp.' mp, '.$stars.' stars)';
				$lst_instr->add( new interf_bal_smpl('li', $txt/*, false, $classe*/) );
				$pa_total += $pa;
				$mp_total += $mp;
				$star_total += $stars;
			}
			// Coût
			$style = $complet && $recip ? 'success' : 'default';
			$panneau = $this->nouv_panneau($recette->nom, 'recette_'.$row['id_recette'], false, $style);
			$panneau->add( $div_diff );
			$panneau->add( $div_ingred );
			$panneau->add( $div_recip );
			$panneau->add( $div_instr );
			if( $perso->get_pa() > $pa_total && $complet && $recip )
			{
				$id = 'alch_fabr_'.$row['id_recette'];
				$form = $panneau->add( new interf_form('livre.php?type=alchimie&action=alchimie&id='.$row['id_recette'], $id, 'get', 'input-group') );
				//$div = $panneau->add( new interf_bal_cont('div', false, 'input-group') );
				$span = $form->add( new interf_bal_cont('span', false, 'input-group-addon') );
				$span->add( new interf_bal_smpl('strong', 'Coût : ') );
				$span->add( new interf_txt('PA : '.$pa_total.' − MP : '.$mp_total.' − Stars : '.$star_total) );
				//$div->add( new interf_txt(' − ') );
				$form->add( $sel );
				$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
				//$btns->add( new interf_bal_smpl('Fabriquer', array('class'=>'btn btn-default', 'onclick'=>'')) );
				$btns->add( new interf_chp_form('submit', false, false, 'Fabriquer', array('class'=>'btn btn-default', 'onclick'=>'return charger_formulaire(\''.$id.'\');')) );
			}
			unset($panneau);
			unset($div_diff);
			unset($div_ingred);
			unset($div_recip);
			unset($div_instr);
			unset($sel);
		}
	}
}
?>