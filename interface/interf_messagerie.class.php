<?php
/**
 * @file interf_messagerie.class.php
 * Classes pour la messagerie
 */

/// Classe pour la messagerie
class interf_messagerie extends interf_onglets
{
	function __construct(&$perso, $type=null, $id_sujet=false, $page=null)
	{
		global $G_url;
		parent::__construct('liste_messageries', 'messagerie');
		$url = clone $G_url;
		$url->add('ajax', 2);
		$messagerie = new messagerie($perso);
		$non_lu = $messagerie->get_non_lu();
		switch( $type )
		{
		case null:
			$onglet = $type = $perso->get_groupe() ? 'groupe' : 'perso';
			break;
		case 'perso':
		case 'groupe':
		case 'diplomatie':
			$onglet = $type;
			break;
		default:
			$onglet = 'royaume';
		}
		
		if( $perso->get_groupe() )
			$this->add_onglet('Groupe'.($non_lu['groupe']?' <span class="badge">'.$non_lu['groupe'].'<span>':''), $url->get('type', 'groupe'), 'onglet_groupe', 'invent', $onglet=='groupe');
		$this->add_onglet('Perso'.($non_lu['perso']?' <span class="badge">'.$non_lu['perso'].'<span>':''), $url->get('type', 'perso'), 'onglet_perso', 'invent', $onglet=='perso');
		/*if( $perso->get_groupe() || $perso->get_rang() == 6 || $perso->get_rang() == 1 )
			$this->add_onglet('Royaume'.($non_lu['royaume']?' <span class="badge">'.$non_lu['groupe'].'<span>':''), $url->get('type', 'royaume'), 'onglet_royaume', 'invent', $onglet=='royaume');
		if( $perso->get_rang() == 6 || $perso->get_rang() == 1 )
			$this->add_onglet('Diplomatie'.($non_lu['diplomatie']?' <span class="badge">'.$non_lu['groupe'].'<span>':''), $url->get('type', 'diplomatie'), 'onglet_diplomatie', 'invent', $onglet=='diplomatie');*/
		
		if($id_sujet)
			$this->get_onglet('onglet_'.$onglet)->add( new interf_messages($perso, $type, $id_sujet, $page, $messagerie) );
		else
			$this->get_onglet('onglet_'.$onglet)->add( new interf_liste_messages($perso, $type, $messagerie) );
	}
}

class interf_liste_messages extends interf_cont
{
	function __construct(&$perso, $type=null, &$messagerie=null)
	{
		global $G_url;
		$G_url->add('type', $type);
		//parent::__construct('liste_msg_'.$type, '', false, false, false, 350, -4);
		$tbl = $this->add( new interf_data_tbl('liste_msg_'.$type, '', false, false, false, 350, -4) );
		$tbl->nouv_cell('Titre');
		$tbl->nouv_cell('# msg');
		$tbl->nouv_cell('Interloc.');
		$tbl->nouv_cell('Date');
		$lien = new interf_bal_smpl('a', '', false, 'icone icone-moins');
		$lien->set_attribut('href', $G_url->get('action', 'tous_lu'));
		$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes vous sûr de vouloir marquer tous les messages comme lus ?\');');
		$lien->set_tooltip('Marquer tous les messages comme lus.');
		$tbl->nouv_cell($lien);
		
		if( !$messagerie )
			$messagerie = new messagerie($perso);
		$messagerie->get_threads($type, 'ASC', false, 1);
		$nbr_msg_tot = 0;
		foreach($messagerie->threads as $cle => $sujet)
		{
			$url = clone $G_url;
			$url->add('sujet', $sujet->get_id());
			$nbr_msg = $sujet->get_message_total($perso->get_id(), $messagerie->get_condition());
			$nbr_msg_tot += $nbr_msg;
			$groupe = new groupe($perso->get_groupe(), '');
			// Si y'a au moins un message dans le thread, et que ce thread n'est pas masqué par le joueur
			if($nbr_msg > 0 && $nbr_msg != $messagerie->get_thread_masque($sujet->get_id()))
			{
				$date = date("d-m-y H:i", strtotime($sujet->get_dernier_message()));
				//Recherche du destinataire
				if($sujet->get_id_dest() != 0)
				{
					$id_interlocuteur =  $sujet->get_id_dest() != $perso->get_id() ? $sujet->get_id_dest() : $sujet->get_id_auteur();
					/// @todo passer à l'objet
					$interlocuteur = recupperso_essentiel($id_interlocuteur);
					$nom_interlocuteur = $interlocuteur['nom'];
				}
				else /// TODO : ramplacer par dernier interlocuteur
					$nom_interlocuteur = 'groupe';
				$non_lu = $messagerie->get_thread_non_lu($sujet->get_id(), $sujet->get_categorie());
				$classe = $non_lu>0 ? 'non_lu ' : '';
				if($sujet->get_important() == 1)
					$classe .= 'important';
				$lgn = $tbl->nouv_ligne('sujet_'.$sujet->get_id(), $classe);
				//$lgn->set_attribut('onclick', 'return charger($(this).find(\'a\').attr(\'href\'));');
				/// @todo à améliorer
				$titre = $sujet->get_titre();
				$titre = htmlspecialchars(stripslashes($titre));
				$tbl->nouv_cell( new interf_lien($titre, $url->get('action', 'lire')) );
				$tbl->nouv_cell( $nbr_msg.($non_lu>0 ? ' <span class="badge">'.$non_lu.'</span>' : '') );
				$tbl->nouv_cell( $nom_interlocuteur );
				$tbl->nouv_cell( $date );
				$cell = $tbl->nouv_cell();
				/*if( $groupe->get_leader() && $type == 'groupe' )
				{
					if($thread->important) $important_etat = 0;
					else $important_etat = 1;
					//$options = '<a href="thread_modif?id_thread='.$thread->id_thread.'&important='.$important_etat.'" onclick="return envoiInfo(this.href, \'\');">(i)</a>';
				}*/
				if( $groupe->get_leader() == $perso->get_id() && $type == 'groupe' || $sujet->get_id_auteur() == $perso->get_id() || $sujet->get_id_dest() == $perso->get_id() )
				{
					$lien = $cell->add( new interf_bal_smpl('a', '', false, 'icone icone-moins') );
					$lien->set_attribut('href', $url->get('action', 'masquer_sujet'));
					$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes vous sûr de vouloir masquer ce sujet ?\');');
					$lien->set_tooltip('Masquer ce sujet.');
				}
				if( $groupe->get_leader() == $perso->get_id() && $type == 'groupe' || $sujet->get_id_auteur() == $perso->get_id() && $nbr_msg <= 1 )
				{
					$lien = $cell->add( new interf_bal_smpl('a', '', false, 'icone icone-poubelle') );
					$lien->set_attribut('href', $url->get('action', 'suppr_sujet'));
					$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Si vous supprimez ce sujet, tous les messages à l\\\'intérieur seront supprimés !\');');
					$lien->set_tooltip('Supprimer ce sujet.');
				}
			}
		}
		// Nouveaux messages
		$this->add( new interf_lien('Nouveau sujet', $G_url->get('action', 'nouveau'), false, 'nouv_sujet') );
		// Achievements
		if($actions && $type == 'perso' && $nbr_msg_tot >= 500)
		{
			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('messages');
			$achiev->set_compteur($nbr_msg_tot);
			$achiev->sauver();
				
		}
	}
}

class interf_messages extends interf_cont
{
	protected $perso;
	protected $div_msg;
	function __construct(&$perso, $type, $id_sujet, $page=null, &$messagerie=null)
	{
		global $G_url;
		$this->perso = &$perso;
		$G_url->add('type', $type);
		if( !$messagerie )
			$messagerie = new messagerie($perso);
		$messagerie->get_thread($id_sujet, 'all', 'ASC', $page!==null?$page:'last', 10);
		$messagerie->thread->get_titre();
		$div_titre = $this->add( new interf_bal_cont('div', 'titre') );
		$lien = $div_titre->add( new interf_lien('', $G_url->get(), false, 'icone icone-retour') );
		$div_titre->add( new interf_bal_smpl('h4', htmlspecialchars(stripslashes( $messagerie->thread->get_titre() ))) );
		$nbr_msg = $messagerie->thread->get_message_total($perso->get_id(), $messagerie->get_condition());
		$page_max = ceil($nbr_msg / 10);
		if($page === null)
			$page = $messagerie->thread->page;
		//messages
		$G_url->add('sujet', $id_sujet);
		$this->div_msg = $this->add( new interf_bal_cont('div', 'messages') );
		foreach($messagerie->thread->messages as $message)
		{
			$this->aff_message($message);
		}
		// pagination
		$pagination = $this->add( new interf_bal_cont('div', 'pagination') );
		$G_url->add('action', 'lire');
		$pagination->add( new interf_pagination($page, $page_max, $G_url->copie()) );	
		// réponse
		$this->add( new interf_editeur('nouv_'.$type, $G_url->get('action', 'ecrire'), false, 'editeur') );
	}
	function aff_message(&$message)
	{
		global $G_url;
		$url = clone $G_url;
		$url->add('msg', $message->get_id());
		$date =	date("d-m-y H:i", strtotime($message->get_date()));
		$div = $this->div_msg->add( new interf_bal_cont('div', 'msg_'.$message->get_id(), 'message') );
		// entete
		$classe = 'entete'.($this->perso->get_id() == $message->get_id_auteur() ? ' soi' : '');
		$entete = $div->add( new interf_bal_cont('div', false, $classe) );
		if( $this->perso->get_id() == $message->get_id_auteur() )
		{
			$lien = $entete->add( new interf_bal_smpl('a', '', false, 'icone icone-poubelle') );
			$lien->set_attribut('href', $url->get('action', 'suppr_msg'));
			$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez vous supprimer ce message ?\');');
			$lien->set_tooltip('Supprimer ce message.');
		}
		$entete->add( new interf_bal_smpl('span', $message->get_nom_auteur(), false, 'auteur') );
		$entete->add( new interf_bal_smpl('span', $date, false, 'xsmall') );
		// corps du message
		$texte =  new texte($message->get_message(), texte::messagerie);
		$corps = $div->add( new interf_bal_smpl('div', $texte->parse(), false, 'texte') );
	}
}

class interf_nouveau_message extends interf_form
{
	function __construct($type, $id=null)
	{
		global $G_url, $db, $Gtrad;
		$G_url->add('type', $type);
		parent::__construct($G_url->get('action', 'nouveau_sujet'), 'messagerie', 'post');
		//$this->add( new interf_chp_form('hidden', 'type', false, $type) );
		$div_titre = $this->add( new interf_bal_cont('div', false, 'form-group') );
		$div_titre->add( new interf_chp_form('text', 'titre', 'Titre', false, false, 'form-control') );
		switch($type)
		{
		case 'perso':
			$div_dest = $this->add( new interf_bal_cont('div', false, 'form-group') );
			if( $id )
			{
				$perso = new perso($id);
				$dest = $div_dest->add( new interf_chp_form('text', 'destinataire', 'Destinataire', $perso->get_nom(), 'destinataire', 'form-control') );
				$dest->set_attribut('readonly', 'readonly');
			}
			else
			{
				$dest = $div_dest->add( new interf_chp_form('text', 'destinataire', 'Destinataire', false, 'destinataire', 'form-control') );
				$dest->set_attribut('autocomplete', 'off');
				$dest->set_attribut('onkeyup', 'javascript:suggestion(this.value, \'suggestion\', this.id);');
				$div_dest->add( new interf_bal_cont('div', 'suggestion', 'dropdown') );
			}
			break;
		case 'groupe':
			if( $id )
				$this->add( new interf_chp_form('hidden', 'destinataire', false, $id) );
			break;
		case 'royaume':
			$perso = joueur::get_perso();
			if( $perso->get_rang() == 6 || $perso->get_rang() == 1 )
			{
				$div_grp = $this->add( new interf_bal_cont('div', false, 'form-group') );
				$div_grp->add( new interf_bal_smpl('label', 'Groupe') );
				$sel = $div_grp->add( new interf_select_form('destinataire', false, null, 'form-control') );
				/// @todo passer à l'objet
				$requete = "SELECT groupe.id as groupe_id, groupe.nom AS groupe_nom, perso.nom FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.id WHERE groupe_joueur.leader = 'y' AND perso.race = '".$perso->get_race()."'";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$sel->add_option($row['groupe_nom'].' ('.$row['nom'].')', $row['groupe_id']);
				}
			}
			else
				$this->add( new interf_chp_form('hidden', 'destinataire', false, $perso->get_groupe()) );
			break;
		case 'diplomatie':
			$perso = joueur::get_perso();
			$royaumes = royaume::create(false, false, 'nom ASC', false, 'id != 0 AND race != "'.$perso->get_race().'"');
			$div_roy = $this->add( new interf_bal_cont('div', false, 'form-group') );
			$div_roy->add( new interf_bal_smpl('label', 'Royaume') );
			$sel = $div_roy->add( new interf_select_form('destinataire', false, null, 'form-control') );
			foreach($royaumes as $r)
			{
				$sel->add_option($r->get_nom().' ('.$Gtrad[$r->get_race()].')', $r->get_id());
			}
			break;
		}
		$div_texte = $this->add( new interf_bal_cont('div', false, 'form-group') );
		$div_texte->add( new interf_bal_smpl('label', 'Message') );
		$div_texte->add( new interf_editeur('texte_msg', false, false, 'editeur') );
		$btn = $this->add( new interf_chp_form('submit', false, false, 'Envoyer', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'return charger_formulaire_texte(\'messagerie\', \'texte_msg\');');
	}
}
?>