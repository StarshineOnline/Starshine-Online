<?php
/**
 * @file interf_qg.class.php
 * Classes pour le quartier général
 */

/// Classe pour le quartier général
class interf_qg extends interf_ville
{
	function __construct(&$royaume)
	{
		global $Gtrad, $db;
		parent::__construct($royaume);
		$perso = joueur::get_perso();
		// Icone jauges
		$this->icone = $this->set_icone_centre('chateau');
		$this->recherche_batiment('mur');
		
		$this->centre->add( new interf_bal_smpl('h3', 'Dépôt militaire') );
		$form = $this->centre->add( new interf_form('qg.php?action=prendre', 'depot_mil') );
		$div = $form->add( new interf_bal_cont('div', 'ville_princ', 'reduit') );
		interf_alerte::aff_enregistres($div);
		$btn = $form->add( new interf_chp_form('submit', 'prendre', false, 'Prendre', 'ville_bas') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'depot_mil\');');
		
		// Objets dans le dépôt
    $type = '';
		// @todo passer par les objets
    $requete = 'SELECT o.nom, o.type, o.encombrement, d.id_objet, d.id AS id_depot, COUNT(*) AS nbr_objet FROM depot_royaume as d, objet_royaume as o, grade as g WHERE d.id_objet = o.id AND g.id = '.$perso->get_rang_royaume().' AND o.grade <= g.rang AND id_royaume = '.$royaume->get_id().' GROUP BY d.id_objet ORDER BY o.type, o.nom ASC';
    $req = $db->query($requete);
    while($row = $db->read_assoc($req))
    {
    	if($type != $row['type'])
    	{
    		$type = $row['type'];
    		$div_type = $div->add( new  interf_bal_cont('div') );
    		$div_type->add( new  interf_bal_smpl('h4', $Gtrad[$type]) );
    		//$ul = $div_type->add( new  interf_bal_cont('ul', false, 'list-group') );
			}
			//$li = $ul->add( new interf_bal_cont('li', false, 'list-group-item') );
			$div_li = $div_type->add( new interf_bal_cont('div', false, 'input-group') );
			$span = $div_li->add( new interf_bal_cont('span', false, 'input-group-addon') );
			$span->add( new interf_txt($row['nom']) );
			$span->add( new interf_bal_smpl('span', '(encombrement : '.$row['encombrement'].')', false, 'xsmall') );
			$chp = $div_li->add( new interf_chp_form('number', 'nbr'.$row['id_objet'], false, 0, false, 'form-control') );
			$chp->set_attribut('min', 0);
			$chp->set_attribut('max', $row['nbr_objet']);
			$chp->set_attribut('step', 1);
			$div_li->add( new interf_bal_smpl('span', '/ '.$row['nbr_objet'], false, 'input-group-addon') );
		}
	}
}

?>