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
		parent::__construct($G_url->get('action', 'modifier_infos'), $id, 'get', 'invent');
		$modif = $groupe->get_leader() == joueur::get_perso()->get_id();
		$nom = $this->add_champ_bs('text', 'nom', null, $groupe->get_nom(), 'Nom');
		if( !$modif )
			$nom->set_attribut('disabled', 'disabled');
		$niveau = $this->add_champ_bs('text', false, null, $groupe->get_level(), 'Niveau du groupe');
		$niveau->set_attribut('disabled', 'disabled');
		// Partage
		$div_partage = $this->add( new interf_bal_cont('div', false, 'input-group') );
		$div_partage->add( new interf_bal_smpl('span', 'Durée du mandat', false, 'input-group-addon') );
		$partage = $div_partage->add( new interf_select_form('duree', false, false, 'form-control') );
		$partage->add_option('Aléatoire', 'r',  $groupe->get_partage()=='r');
		$partage->add_option('Par tour', 'r',  $groupe->get_partage()=='t');
		$partage->add_option('Chef', 'r',  $groupe->get_partage()=='l');
		$partage->add_option('Trouve = Garde', 'r',  $groupe->get_partage()=='k');
		if( !$modif )
			$partage->set_attribut('disabled', 'disabled');
		// Chef
		$div_chef = $this->add( new interf_bal_cont('div', false, 'input-group') );
		$div_chef->add( new interf_bal_smpl('span', 'Chef', false, 'input-group-addon') );
		$chef = $div_chef->add( new interf_select_form('duree', false, false, 'form-control') );
		$membres = $groupe->get_membre();
		foreach($membres as $m)
		{
			$p = new perso( $m->get_id_joueur() );
			$chef->add_option($p->get_nom(), $p->get_id(), $groupe->get_leader()==$p->get_id());
		}
		if( !$modif )
			$chef->set_attribut('disabled', 'disabled');
			
		$btns = $this->add( new interf_bal_cont('div', false, 'btn-group') );
		$btn = $btns->add( new interf_lien_cont('degrouper.php', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment quitter ce groupe ?\');');
		$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-exclure') );
		$btn->add( new interf_bal_smpl('span', 'Quitter le groupe') );
		if( $modif )
		{
			$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-primary') );
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

?>