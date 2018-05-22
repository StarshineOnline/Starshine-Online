<?php
/**
 * @file interf_groupe.class.php
 * Permet l'affichage des informations du groupe & des batailles
 */

class interf_groupe extends interf_form
{
	function __construct($id, &$groupe)
	{
		global $G_url, $Gtrad;
		parent::__construct($G_url->get( array('action'=>'modifier_infos') ), $id, 'get', 'invent');
		$modif = $groupe->get_id_leader() == joueur::get_perso()->get_id();
		$nom = $this->add_champ_bs('text', 'nom', null, $groupe->get_nom(), 'Nom');
		if( !$modif )
			$nom->set_attribut('disabled', 'disabled');
		$niveau = $this->add_champ_bs('text', false, null, $groupe->get_level(), 'Niveau du groupe');
		$niveau->set_attribut('disabled', 'disabled');
		// Partage
		$div_partage = $this->add( new interf_bal_cont('div', false, 'input-group') );
		$div_partage->add( new interf_bal_smpl('span', 'Répartition des objets', false, 'input-group-addon') );
		$partage = $div_partage->add( new interf_select_form('partage', false, false, 'form-control') );
		$partage->add_option('Aléatoire', 'r',  $groupe->get_partage()=='r');
		$partage->add_option('Par tour', 't',  $groupe->get_partage()=='t');
		$partage->add_option('Chef', 'l',  $groupe->get_partage()=='l');
		$partage->add_option('Trouve = Garde', 'k',  $groupe->get_partage()=='k');
		if( !$modif )
			$partage->set_attribut('disabled', 'disabled');
		// Chef
		$div_chef = $this->add( new interf_bal_cont('div', false, 'input-group') );
		$div_chef->add( new interf_bal_smpl('span', 'Chef', false, 'input-group-addon') );
		$chef = $div_chef->add( new interf_select_form('chef', false, false, 'form-control') );
		$membres = $groupe->get_membre();
		foreach($membres as $m)
		{
			$p = new perso( $m->get_id_joueur() );
			$chef->add_option($p->get_nom(), $p->get_id(), $groupe->get_id_leader()==$p->get_id());
		}
		if( !$modif )
			$chef->set_attribut('disabled', 'disabled');
			
		$btns = $this->add( new interf_bal_cont('div', false, 'btn-group') );
		$btn = $btns->add( new interf_lien_cont($G_url->get('action', 'quitter'), false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment quitter ce groupe ?\');');
		$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-exclure') );
		$btn->add( new interf_bal_smpl('span', 'Quitter le groupe') );
		if( $modif )
		{
			$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-primary') );
			$btn->set_attribut('onclick', 'return charger_formulaire(\''.$id.'\');');
		}
		
		// Invitations
		$invitations = invitation::create('groupe', $groupe->get_id());
		if( count($invitations) )
		{
			$this->add( new interf_bal_smpl('h3', 'Invitations envoyées') );
			$liste = $this->add( new interf_bal_cont('ul', 'invitations') );
			$G_url->add('action', 'suppr_invit');
			foreach($invitations as $invit)
			{
				$perso = new perso($invit->get_receveur());
				$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
				$suppr = $li->add( new interf_lien('', $G_url->get('id', $invit->get_id()), false, 'icone icone-croix') );
				$suppr->set_tooltip('Supprimer');
				$li->add( new interf_bal_smpl('span', $perso->get_nom()) );
				$li->add( new interf_txt(' − ') );
				$li->add( new interf_bal_smpl('span', ucwords($perso->get_classe())) );
				$li->add( new interf_txt(' ') );
				$li->add( new interf_bal_smpl('span', $Gtrad[$perso->get_race()]) );
				$li->add( new interf_txt(' − Niv.') );
				$li->add( new interf_bal_smpl('span', $perso->get_level()) );
			}
		}
	}
}

class interf_batailles extends interf_bal_cont//interf_accordeon
{
	function __construct($id, &$groupe)
	{
		global $Trace, $G_url;
		parent::__construct('div', $id);
		$chef = new perso($groupe->get_id_leader());
		$bataille_royaume = new bataille_royaume($Trace[$chef->get_race()]['numrace']);
		$ouvert = true;
		foreach($bataille_royaume->get_batailles() as $bataille)
		{
			//il faut que ça soit des batailles "en cours"
			if( $bataille->get_etat() == 1 && $bataille->is_groupe_in($groupe->get_id()) )
			{
				$G_url->add('bataille', $bataille->get_id());
				$titre = new interf_bal_cont('div');
				$carte = $titre->add( new interf_lien('', $G_url->get('action', 'carte'), false, 'icone icone-carte2') );
				$carte->set_tooltip('Afficher la carte');
				$titre->add( new interf_bal_smpl('span',$bataille->get_nom()) );
				//$panneau = $this->nouv_panneau($bataille->get_nom(), 'bataille_'.$bataille->get_id(), $ouvert);
				$panneau = $this->add( new interf_panneau($titre, 'bataille_'.$bataille->get_id()) );
				$descr = new texte($bataille->get_description(), texte::batailles);
				$panneau->add( new interf_bal_smpl('p', $descr->parse()) );
				$ouvert = false;
				
				foreach($bataille->get_reperes() as $repere)
				{
					if($repere_groupe = $repere->get_groupe($groupe->get_id()))
					{
						$type = $repere->get_repere_type();
						$div = $panneau->add( new interf_bal_cont('div', false, 'info_case') );
						$G_url->add('id', $repere_groupe->get_id());
						if($repere_groupe->accepter == 0 && joueur::get_perso()->get_id() == $groupe->get_id_leader())						{
							$lien = $div->add( new interf_lien('', $G_url->get('action', 'accepter'), false, 'icone icone-ok') );
							$lien->set_tooltip('Accepter');
						}
						$span = $div->add( new interf_bal_cont('span') );
						$span->add( new interf_bal_smpl('b', $type->get_nom()) );
						$span->add( new interf_txt(' en '.$repere->get_x().' / '.$repere->get_y()) );
					}
				}
			}
		}
	}
}

class interf_carte_bataille extends interf_dialogBS
{
	function __construct(&$bataille)
	{
		parent::__construct($bataille->get_nom(), true);
		$this->add( new interf_carte($bataille->get_x(), $bataille->get_y(), interf_carte::aff_batailles, 8, 'carte_bataille', 0, 0, null, $bataille->get_reperes('tri_type')) );
	}
}

?>