<?php
require_once(root.'connect_forum.php');

class interf_accueil extends interf_cont
{
	function __construct()
	{
		global $db, $db_forum;
		$perso = joueur::get_perso();
		$nouveaux = array();
		$anciens = array();
		
		// Vote SSO
		$msg_sso = $this->add( new interf_bal_cont('p', null, 'accueil') );
		$msg_sso->add( new interf_txt('N\'oubliez pas de ') );
		$lien_vote = $msg_sso->add( new interf_bal_smpl('a', 'voter pour SSO') );
		$lien_vote->set_attribut('href', 'vote_dons.php');
		$lien_vote->set_attribut('onclick', 'return charger(this.ref);');
		$msg_sso->add( new interf_txt(' !') );
		
		// Élections
		$R = royaume::create('race', $perso->get_race());
		if( elections::is_mois_election($R[0]->get_id()) )
		{
			$elections = elections::get_prochain_election($R[0]->get_id(), true);
			if( date("d") >= 2 && date("d") < 15 )
			{
				if( candidat::create(array('id_perso','id_election'), array($perso->get_id(), $elections[0]->get_id())) )
					$this->add( new interf_bal_smpl('p', 'Les candidatures au poste de roi sont ouvertes.') );
			}
			else if(date("d") >= 15 && ($elections[0]->get_type() == 'universel' || $perso->get_grade()->get_id() == 6) )
			{
				$requete = 'SELECT id FROM vote WHERE id_perso = '.$perso->get_id().' AND id_election = '.$elections[0]->get_id();
				$db->query($requete);
				if(!$db->num_rows)
					$this->add( new interf_bal_smpl('p', 'Vous pouvez '.($elections[0]->get_type() == 'universel' ? 'voter pour' : 'nommer').' le futur.', null, 'accueil') );
			}
		}
		
		// Révolution
		if( revolution::is_mois_revolution($R[0]->get_id(), $perso->get_id()) )
		{
			$revolution = revolution::get_prochain_revolution($R[0]->get_id());
			$requete = 'SELECT id FROM vote_revolution WHERE id_perso = '.$perso->get_id().' AND id_revolution = '.$revolution[0]->get_id();
			$db->query($requete);
			if(!$db->num_rows)
				$this->add( new interf_bal_smpl('p', 'Une révolution a été déclenchée, vous pouvez aller voter pour ou contre.', null, 'accueil') );
		}
		
		$accordeon = $this->add( new interf_accordeon('acceuil_messages') );
		// Message du monde
		$requete = "SELECT * FROM motd WHERE publie = 1 ORDER BY date DESC";
		$req = $db->query($requete);
		$date = 0;
		if( $db->num_rows > 0 )
		{
			$motd = array('id'=>'message_monde', 'titre'=>'Informations du monde', 'contenu'=>array());
			while( $row = $db->read_assoc($req) )
			{
				$div = new interf_bal_cont('div', null, 'message_monde');
				$div->add( new interf_bal_smpl('h4', $row['titre']) );
				$txt = new texte($row['text'], texte::msg_monde);
				$div->add( new interf_bal_smpl('p', $txt->parse()) );
				$motd['contenu'][] = $div;
				if( !$date )
					$date = $row['date'];
			}
		}
		if( $date > $perso->get_dernier_connexion() )
			$nouveaux[] = &$motd;
		else
			$anciens[] = &$motd;
		
		// Message du roi
		$requete = "SELECT * FROM motk WHERE race = '".$perso->get_race()."'";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$txt = new texte($row['message'], texte::msg_roi);
		$motk = array('id'=>'message_roi', 'titre'=>'Message du roi', 'contenu'=>$txt->parse());
		if( $row['date'] > $perso->get_dernier_connexion() )
			$nouveaux[] = &$motk;
		else
			$anciens[] = &$motk;
			
		// Annonce forum
		if( $db_forum )
		{
			$requete = 'SELECT * FROM punbbtopics WHERE forum_id = 5 AND posted > '.$perso->get_dernier_connexion();
			$req = $db_forum->query($requete);
			$nouveau = $db->num_rows > 0;
			if( !$nouveau )
			{
				$requete = 'SELECT * FROM punbbtopics WHERE forum_id = 5 ORDER BY posted DESC LIMIT 0, 1';
				$req = $db_forum->query($requete);
			}
			if( $db->num_rows > 0 )
			{
				$annonce = array('id'=>'annonce', 'titre'=>'Annonce', 'contenu'=>array());
				//$annonce = $accordeon->nouv_panneau('Annonce <span class="label label-primary">nouveau</span>', 'annonce', true, 'info');
				while( $row = $db_forum->read_array($req) )
				{
					//$div = $annonce->add( new interf_bal_cont('div') );
					$div = new interf_bal_cont('div', null, 'message_monde');
					$titre = $div->add( new interf_bal_cont('h4') );
					$lien = $titre->add( new interf_bal_smpl('a', $row['subject']) );
					$lien->add_attribut('href', 'http://forum.starshine-online.com/viewtopic.php?id='.$row['id']);
					$div->add( new interf_bal_smpl('h6', 'Par '.$row['poster'].', le '.date("l d F Y à H:i", $row['posted']).'</span> ('.($row['num_replies']).' commentaires)') );
					$txt = new texte($row['message'], texte::messagerie);
					$div->add( new interf_bal_smpl('p', $txt->parse()) );
				}
			}
			if( $nouveau )
				$nouveaux[] = &$annonce;
			else
				$anciens[] = &$annonce;
			
		}
		
		// Messages nouveaux
		foreach( $nouveaux as $msg )
		{
			$panneau = $accordeon->nouv_panneau($msg['titre'].' <span class="label label-primary">nouveau</span>', $msg['id'], true);
			if( is_array($msg['contenu']) )
			{
				foreach($msg['contenu'] as $cont)
					$panneau->add( $cont );
			}
			else
				$panneau->add( new interf_txt($msg['contenu']) );
		}
		// Messages anciens
		foreach( $anciens as $msg )
		{
			$panneau = $accordeon->nouv_panneau($msg['titre'], $msg['id'], false);
			if( is_array($msg['contenu']) )
			{
				foreach($msg['contenu'] as $cont)
				{
					$panneau->add( $cont );
					unset($cont);
				}
			}
			else
				$panneau->add( new interf_txt($msg['contenu']) );
		}
		
		// Journal
		/*$requete = 'SELECT * FROM journal WHERE id_perso = '.$joueur->get_id().' AND time > "'.date('Y-m-d H:i:s',  $perso->get_dernier_connexion()).'" ORDER BY time ASC, id ASC';
		$req = $db->query($requete);
		if( $db->num_rows > 0 )
		{
			while( $row = $db_forum->read_array($req) )
			{
				$journal .= affiche_ligne_journal($row_journal);
			}
			echo '<div class="titre">';
			echo '<p>Journal des dernières actions</p>';
			echo '</div>';
			echo '<ul>';
			echo '<li>'.$journal.'</li>';
			echo '</ul>';
			echo '<hr>';
		}*/
	}
}

?>