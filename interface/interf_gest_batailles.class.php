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
			$div->add( new interf_lien('', $url->get('action', 'debut'), false, 'icone icone-demarer') )->set_tooltip('Débuter');
			$div->add( new interf_lien('', $url->get('action', 'modifier'), false, 'icone icone-modifier') )->set_tooltip('Modifier');
			break;
		case 1:
			$div->add( new interf_lien('', $url->get('action', 'fermer'), false, 'icone icone-arreter') )->set_tooltip('Fermer');
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
		$G_url->add('action', 'case');
		$this->div_droite->add( new interf_carte($bataille->get_x(), $bataille->get_y(), interf_carte::aff_gest_batailles, 8, 'carte') );
	}
}

class interf_gerer_bataille extends interf_gest_bat_base
{
	protected $acordeon;
	protected $reperes_bataille;
	function __construct(&$bataille)
	{
		global $G_url;
		parent::__construct();
		$G_url->add('bataille', $bataille->get_id());
		$this->div_gauche->add( new interf_bal_smpl('h4', $bataille->get_nom()) );
		$this->div_gauche->add( new interf_bal_smpl('p', $bataille->get_description()) );
		// Groupes
		$groupes = $bataille->get_groupes();
		$this->reperes_bataille = $bataille->get_reperes('tri_type');
		$this->acordeon = $this->div_gauche->add( new interf_accordeon('batailles_groupes') );
		foreach($groupes as $groupe)
		{
			$this->aff_groupe($groupe);
		}
		// carte
		$G_url->add('action', 'case_repere');
		$this->div_droite->add( new interf_carte($bataille->get_x(), $bataille->get_y(), interf_carte::aff_gest_batailles, 8, 'carte', 0, 0, null, $this->reperes_bataille) );
	}
	function aff_groupe(&$groupe)
	{
		global $G_url;
		$url = $G_url->copie('groupe', $groupe->get_id());
		$panneau = $this->acordeon->nouv_panneau($groupe->get_nom(), 'groupe_'.$groupe->get_id()/*, $carte==$bourg->get_id()*/);
		// Nouvelle missions
		$form = $panneau->add( new interf_form($url->get('action', 'mission'), 'mission_grp_'.$groupe->get_id(), 'get', 'input-group') );
		$form->add( new interf_bal_smpl('span', 'Nouvelle mission', false, 'input-group-addon') );
		$sel = $form->add( new interf_select_form('mission', false, false, 'form-control') );
		$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn = $btns->add( new interf_chp_form('submit', false, false, 'Ok', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'mission_grp_'.$groupe->get_id().'\');');
		// Missions actuelles
		$reperes_groupe = $groupe->get_reperes();
		$id_mission = array();
		if(count($reperes_groupe) > 0)
		{
			$liste = $panneau->add( new interf_bal_cont('ul') );
			foreach($reperes_groupe as $repere_groupe)
			{
				$repere_groupe->get_repere()->get_type();
				$url->add('id', $repere_groupe->get_id_repere());
				$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
				$suppr = $li->add( new interf_lien('', $G_url->get('action', 'suppr_mission'), false, 'icone icone-poubelle') );
				$suppr->set_tooltip('Supprimer');
				$li->add( new interf_bal_smpl('span', '<b>Mission :</b> '.$repere_groupe->get_repere()->get_repere_type()->get_nom()) );
				$li->add( new interf_bal_smpl('span', 'X='.$repere_groupe->get_repere()->get_x().' - Y='.$repere_groupe->get_repere()->get_y().($repere_groupe->accepter ? 'Acceptée' : 'En attente d\'être acceptée'), false, 'xsmall') );
				$id_mission[$repere_groupe->get_id_repere()] = 1;
			}
		}
		// On remplis la liste des missions que l'on peut ajouter
		if( count($this->reperes_bataille['action']) == 0 || count($this->reperes_bataille['action']) == count($id_mission) )
			// Aucune mission n'existe, ou elles sont toutes deja assignées au groupe
			$sel->add_option('Aucune mission disponible.')->set_attribut('disabled', 'disabled');
		else
		{
			foreach($this->reperes_bataille['action'] as $mission)
			{
				$mission->get_type();
				// Si elle n'est pas deja assignée au groupe
				if ($id_mission[$mission->get_id()] != 1)
				{
					$sel->add_option($mission->get_repere_type()->get_nom().' ('.$mission->get_x().' / '.$mission->get_y().')', $mission->get_id());
				}
			}
		}
	}
}

class interf_reperes extends interf_dialogBS
{
	function __construct(&$bataille, $x, $y, &$royaume)
	{
		global $G_url, $db;
		parent::__construct('Case X='.$x.' - Y='.$y, true, 'reperes_case');
		$reperes = $bataille->get_repere_by_coord($x, $y);
		$G_url->add( array('bataille'=>$bataille->get_id(), 'x'=>$x, 'y'=>$y) );
		
		// bâtiment du royaumes
		$batiment = false;
		$construction = construction::create(array('royaume', 'x', 'y'), array($royaume->get_id(), $x, $y));
		if($construction)
		{
			$batiment = $construction[0]->get_def();
			$div = $this->add( new interf_bal_cont('div', false, 'info_case') );
			$div->add( new interf_jauge_bulle('HP', $construction[0]->get_hp(), $batiment->get_hp(), false, 'hp', false, 'jauge_case')  );
			$div->add( new interf_img('../'.$construction[0]->get_image()) );
			$div->add( new interf_bal_smpl('b', $construction[0]->get_nom()) );
			$div->add( new interf_bal_smpl('span', '('.$batiment->get_nom().')', false, 'xsmall') );
		}
		// repères
		$type_reperes = array();
		foreach($reperes as $repere)
		{
			$type_reperes[] = $repere->get_type();
			$type = $repere->get_repere_type();
			$div = $this->add( new interf_bal_cont('div', 'repere_'.$repere->get_id(), 'info_case') );
			$suppr = $div->add( new interf_lien('', $G_url->get(), false, 'icone icone-poubelle') );
			$suppr->set_tooltip('Supprimer');
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment supprimer ce repère ?\');');
			switch($repere->get_type())
			{
			case 'action' :
				$div->add( new interf_img('../image/batiment/'.$type->get_image().'_04.png') );
				$texte = 'Mission - '.count($repere->get_groupes()).' groupe(s)';
				break;
			case 'batiment' :
				$div->add( new interf_img('../image/batiment/'.$type->get_image().'_04.png') );
				$texte = 'Bâtiment ennemi';
				break;
			}
			$div->add( new interf_bal_smpl('b', $type->get_nom()) );
			$div->add( new interf_bal_smpl('span', $texte, false, 'xsmall') );
		}
		//Si ya moins de 2 repères
		if(count($reperes) < 10)
		{//($action = null, $id=null, $method = 'get', $classe=null)
			$form = $this->add( new interf_form($G_url->get('action', 'nouv_repere'), 'ajout_repere', 'get', 'input-group') );
			$form->add( new interf_bal_smpl('span', 'Nouveau repère', false, 'input-group-addon') );
			$sel = $form->add( new interf_select_form('repere', false, false, 'form-control') );
			$bataille_royaume = new bataille_royaume($royaume->get_id());
			$types = $bataille_royaume->get_all_repere_type();
			foreach($types as $type)
			{
				$sel->add_option($type->get_nom().' ('.$type->get_description().')', 'a'.$type->get_id());
			}
			if(!$batiment && !in_array('batiment', $type_reperes))
			{
				/// @todo passer à l'objet
				$requete = "SELECT id, nom FROM batiment";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$sel->add_option($row['nom'], 'b'.$row['id']);
				}
			}
			$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$btn = $btns->add( new interf_chp_form('submit', false, false, 'Ajouter', false, 'btn btn-default') );
			$btn->set_attribut('onclick', 'return charger_formulaire(\'ajout_repere\');');
		}
	}
}

?>