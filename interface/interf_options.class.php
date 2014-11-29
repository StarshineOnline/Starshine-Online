<?php
/**
 * @file interf_options.class.php
 * Affichage des options
 */
 
/**
 * classe gérant l'affichage des options
 */
class interf_options extends interf_onglets
{
	function __construct($categorie)
	{
		global $G_url;
		parent::__construct('ongl_options', 'options');
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Personnage', $url->get('categorie', 'perso'), 'ongl_perso', 'invent', $categorie=='perso');
		$this->add_onglet('Compte joueur', $url->get('categorie', 'joueur'), 'ongl_joueur', 'invent', $categorie=='joueur');
		$this->add_onglet('Affichage', $url->get('categorie', 'affichage'), 'ongl_affichage', 'invent', $categorie=='affichage');
		
		$G_url->add('categorie', $categorie);
		switch($categorie)
		{
		case 'perso':
			$this->get_onglet('ongl_'.$categorie)->add( new interf_options_perso() );
			break;
		case 'joueur':
			$this->get_onglet('ongl_'.$categorie)->add( new interf_options_joueur() );
			break;
		case 'affichage':
			$this->get_onglet('ongl_'.$categorie)->add( new interf_options_affichage() );
			break;
		}
	}
}

class interf_options_perso extends interf_cont
{
	function __construct()
	{
		global $db, $G_url;
		interf_alerte::aff_enregistres($this);
		
		// Titres
		$requete = "SELECT * FROM achievement WHERE id_perso = ".$_SESSION['ID'];
		$req = $db->query($requete);
		if( $db->num_rows($req) )
		{
			$div_t = $this->add( new interf_bal_cont('div') );
			$div_t->add( new interf_bal_smpl('h4', 'Titre') );
			$titre_perso = new titre($_SESSION['ID']);
			$id = $titre_perso->get_id_titre();
			$form = $div_t->add( new interf_form($G_url->get('action', 'titre'), 'options_titre', 'get', 'input-group') );
			$form->add( new interf_bal_smpl('span', 'Titre :', false, 'input-group-addon') );
			$sel = $form->add( new interf_select_form('titre', false, false, 'form-control') );
			$sel->add_option('Aucun titre', 0, $id==0);
			while($row = $db->read_array($req))
			{
				$requete2 = "SELECT * FROM achievement_type WHERE id = ".$row['id_achiev'];
				$req2 = $db->query($requete2);
				$row2 = $db->read_array($req2);
				$titre = explode('-', $row2['titre']);
				if ($titre[1] != null )
					$sel->add_option($titre[1], $row['id_achiev'], $id==$row['id_achiev']);
			}
			$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
			$btn->set_attribut('onclick', 'return charger_formulaire(\'options_titre\');');
		}
		
		// Hibernation et suppression
		$div_hs = $this->add( new interf_bal_cont('div') );
		$div_hs->add( new interf_bal_smpl('h4', 'Hibernation et suppression') );
		$div_hs->add( new interf_bal_smpl('p', 'L\'hibernation permet de mettre le personnage en pause (elle dure au <b>minimum</b> 2 semaines).') );
		$div_hs->add( new interf_bal_smpl('p', 'La supression est définitive, elle permet de créer un nouveau personnage (avec votre compte joueur).') );
		$btns = $div_hs->add( new interf_bal_cont('span', false, 'btn-group') );
		$hibern = $btns->add( new interf_lien('Hiberner', $G_url->get('action', 'hibern'), false, 'btn btn-default') );
		$hibern->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes vous sur de vouloir hiberner ?\')');
		$suppr = $btns->add( new interf_lien('Supprimer', $G_url->get('action', 'suppr'), false, 'btn btn-default') );
		$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes vous sur de vouloir effacer votre personnage ?\')');
	}
}

class interf_options_joueur extends interf_cont
{
	function __construct()
	{
		global $db, $G_url;
		interf_alerte::aff_enregistres($this);
		$joueur = joueur::factory();
		// e-mail
		$div_m = $this->add( new interf_bal_cont('div') );
		$div_m->add( new interf_bal_smpl('h4', 'Adresse e-mail') );
		$form = $div_m->add( new interf_form($G_url->get('action', 'email'), 'options_email', 'post', 'input-group') );
		$form->add( new interf_bal_smpl('span', 'E-mail :', false, 'input-group-addon') );
		$email = $form->add( new interf_chp_form('email', 'email', false, $joueur->get_email(), false, 'form-control') );
		$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'options_email\');');
		
		// Mot de passe
		$div_p = $this->add( new interf_bal_cont('div') );
		$div_p->add( new interf_bal_smpl('h4', 'Mot de passe') );
		$div_p->add( new interf_bal_smpl('p', '<b>Attention :</b> le changement affectera tout le site (jeu, forum, wiki & jabber).') );
		$form = $div_p->add( new interf_form($G_url->get('action', 'mdp'), 'options_mdp', 'post') );
		$anc = $form->add_champ_bs('password', 'anc_mdp', null, null, 'Ancien mot de passe :');
		$anc->set_attribut('required', 'required');
		$nouv1 = $form->add_champ_bs('password', 'nouv_mdp_1', null, null, 'Nouveau mot de passe :');
		$nouv1->set_attribut('required', 'required');
		$nouv2 = $form->add_champ_bs('password', 'nouv_mdp_2', null, null, 'Confirmer nouveau mot de passe :');
		$nouv2->set_attribut('required', 'required');
		$btn = $form->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'options_mdp\');');
	}
}

class interf_options_affichage extends interf_cont
{
	function __construct()
	{
		global $db, $G_url;
		$div_i = $this->add( new interf_bal_cont('div') );
		$div_i->add( new interf_bal_smpl('h4', 'Interface') );
		$form = $div_i->add( new interf_form($G_url->get('action', 'interface'), 'options_interf', 'get', 'input-group') );
		$form->add( new interf_bal_smpl('span', 'Globale :', false, 'input-group-addon') );
		$sel = $form->add( new interf_select_form('valeur', false, false, 'form-control') );
  	/// @todo passer à l'objet
		$requete = 'SELECT valeur FROM options WHERE nom = "interface" AND id_perso = '.$_SESSION['ID'];
		$req = $db->query($requete);
		$index = $db->read_array($req)[0];
		foreach(interf_factory::get_noms() as $c=>$nom)
		{
			$sel->add_option($nom, $c, $c==$index);
		}
		$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire(\'options_interf\');');
	}
}

?>