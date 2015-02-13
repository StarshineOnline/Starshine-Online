<?php
/**
 * @file interf_gest_batailles.class.php
 * Interfaces de la gestion des batailles
 */ 

class interf_gest_batailles extends interf_cont
{
	protected $liste;
	function __construct($royaume)
	{
		global $G_url;
		$this->liste = $this->add( new interf_bal_cont('ul', 'liste_batailles') );
		$bataille_royaume = new bataille_royaume($royaume->get_id());
		$bataille_royaume->get_batailles();
		
		foreach($bataille_royaume->batailles as $bataille)
		{
			$this->affiche_bataille($bataille);
		}
		
		$this->add( new interf_lien('Nouvelle bataille', $G_url->get('action', 'nouveau'), 'nouv_bataille', 'btn btn-default') );
	}
	protected function affiche_bataille(&$bataille)
	{
		global $G_url;
		$url = $G_url->copie('id', $bataille->get_id());
		$li = $this->liste->add( new interf_bal_cont('li', false, 'info_case') );
		$div = $li->add( new interf_bal_cont('div') );
		$suppr = $div->add( new interf_lien('', $url->get('action', 'suppr'), false, 'icone icone-poubelle') );
		$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir supprimer cette bataille ?\');');
		$suppr->set_tooltip('Supprimer');
		switch($bataille->get_etat())
		{
		case 0:
			$div->add( new interf_lien('', $url->get('action', 'debut'), false, 'icone icone-plus') )->set_tooltip('Débuter');
			$div->add( new interf_lien('', $url->get('action', 'modifier'), false, 'icone icone-modifier') )->set_tooltip('Modifier');
			break;
		case 1:
			$div->add( new interf_lien('', $url->get('action', 'fermer'), false, 'icone icone-moins') )->set_tooltip('Fermer');
			$div->add( new interf_lien('', $url->get('action', 'modifier'), false, 'icone icone-modifier') )->set_tooltip('Modifier');
			break;
		}
		$div->add( new interf_lien('', $url->get('action', 'gerer'), false, 'icone icone-options') )->set_tooltip('Gérer');
		//$li->add( new interf_lien('', $G_url->get('action', 'gerer'), false, 'icone icone-infos') )->set_tooltip('Afficher la description');
		$div->add( new interf_bal_smpl('span', $bataille->get_nom()) );
		$div->add( new interf_bal_smpl('span', 'État : '.ucwords($bataille->etat_texte()), false, 'xsmall') );
		$texte = new texte($bataille->get_description(), texte::batailles);
		$div_descr = $li->add( new interf_bal_smpl('div', $texte->parse()) );
	}
}

class interf_gest_bat_base extends interf_cont
{
	//protected $div_princ;
	protected $div_gauche;
	protected $div_droite;
	function __construct()
	{
		interf_alerte::aff_enregistres($this);
		$div_princ = $this->add( new interf_bal_cont('div', 'gestion_batailles') );
		$this->div_gauche = $div_princ->add( new interf_bal_cont('div', 'gest_bat_infos') );
		$this->div_droite = $div_princ->add( new interf_bal_cont('div', 'gest_bat_carte') );
	}
}

class interf_modif_bataille extends interf_gest_bat_base
{
	function __construct(&$bataille)
	{
		global $G_url, $G_max_x, $G_max_y, $db;
		parent::__construct();
		if( $bataille->get_id() )
			$G_url->add('id', $bataille->get_id());
		// Informations
		$form = $this->div_gauche->add( new interf_form($G_url->get('action', 'creer'), 'info_bataille', 'post') );
		$form->add_champ_bs('text', 'nom', null, $bataille->get_nom(), 'Nom');
		$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div->add( new interf_bal_smpl('span', 'X', false, 'input-group-addon') );
		//$x = $form->add_champ_bs('number', 'x', null, $bataille->get_nom(), 'X');
		$x = $div->add( new interf_chp_form('number', 'x', false, $bataille->get_x(), 'bataille_x', 'form-control') );
		$x->set_attribut('min', 0);
		$x->set_attribut('max', $G_max_x);
		$x->set_attribut('step', 1);
		//$y = $form->add_champ_bs('number', 'y', null, $bataille->get_nom(), 'Y');
		$div->add( new interf_bal_smpl('span', 'Y', false, 'input-group-addon') );
		$y = $div->add( new interf_chp_form('number', 'y', false, $bataille->get_y(), 'bataille_y', 'form-control') );
		$y->set_attribut('min', 0);
		$y->set_attribut('max', $G_max_y);
		$y->set_attribut('step', 1);
		$span = $div->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$span->add( new interf_bal_smpl('button', 'Voir', array('type'=>'button', 'class'=>'btn btn-default', 'onclick'=>'batailles_maj_carte();')) );
		$editeur = $form->add( new interf_editeur('descr_bataille', false, false, 'editeur') );
		$editeur->set_texte( $bataille->get_description() );
		// Groupes
		/// @todo passer à l'objet
		$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".joueur::get_perso()->get_race()."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$bat_groupe = new bataille_groupe(0,0,$row['groupeid']);
			if( $bat_groupe->is_bataille() && $bat_groupe->get_id_bataille() != $bataille->get_id() )
				continue;
			$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
			$span = $div->add( new interf_bal_cont('span', false, 'input-group-addon') );
			$checkbox = $span->add( new interf_chp_form('checkbox', 'groupes[]', false, $row['groupeid']) );
			if( $bat_groupe->is_bataille() )
				$checkbox->set_attribut('checked', 'checked');
			$div->add( new interf_bal_smpl('span', 'Impliquer le groupe '.($row['groupenom']?$row['groupenom']:'groupe #'.$row['groupeid']), false, 'input-group-addon') );
		}
		// Bouton
		$btn = $form->add( new interf_chp_form('submit', false, false, $bataille->get_id() ? 'Modifier' : 'creer', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire_texte(\'info_bataille\',\'descr_bataille\');');
		// carte
		$this->div_droite->add( new interf_carte($bataille->get_x(), $bataille->get_y(), interf_carte::aff_gest_batailles, 8, 'carte') );
	}
}

class interf_gerer_bataille extends interf_gest_bat_base
{
	function __construct(&$bataille)
	{
		//global $G_url, $G_max_x, $G_max_y, $db;
		parent::__construct();
		$this->div_gauche->add( new interf_bal_smpl('h4', $bataille->get_nom()) );
		$this->div_gauche->add( new interf_bal_smpl('p', $bataille->get_description()) );
		// carte
		$this->div_droite->add( new interf_carte($bataille->get_x(), $bataille->get_y(), interf_carte::aff_gest_batailles, 8, 'carte') );
	}
}

?>