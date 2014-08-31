<?php
/**
 * @file interf_script.class.php
 * Classes pour la gestions des scripts
 */

/// Classe pour la liste des scripts
class interf_liste_scripts extends interf_bal_cont
{
	function __construct($entite)
	{
		global $G_url, $db;
		parent::__construct('ul');
		/// TODO: passer à l'objet (et au polymorphisme)
		if( get_class($entite) == 'perso' )
		{
			$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$entite->get_id()." ORDER BY nom ASC";
			$script_attaque = recupaction_all($entite->get_action_a());
			$script_defense = recupaction_all($entite->get_action_d());
		}
		else
		{
			$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$entite->get_id_joueur()." AND type_monstre = ".$entite->get_id_monstre()." ORDER BY nom ASC";
			$script_attaque = recupaction_all($entite->get_action_a(), true);
			$script_defense = recupaction_all($entite->get_action_d(), true);
		}
		$req = $db->query($requete);
		while( $row = $db->read_assoc($req) )
		{
			$G_url->add('script', $row['id']);
			$li = $this->add( new interf_bal_cont('li', false, 'info_case') ); 
			$suppr = $li->add( new interf_lien('', $G_url->get('action', 'supprimer'), false, 'icone icone-poubelle') );
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment supprimer ce script ?\');');
			$suppr->set_tooltip('Supprimer ce script.');
			$copier = $li->add( new interf_lien('', $G_url->get('action', 'copier'), false, 'icone icone-copier') );
			$copier->set_tooltip('Créer un nouveau script identique à celui-ci.');
			$def = $li->add( new interf_lien('', $G_url->get('action', 'defense'), false, 'icone icone-bouclier') );
			$def->set_tooltip('Définir comme script de défense.');
			$att = $li->add( new interf_lien('', $G_url->get('action', 'attaque'), false, 'icone icone-attaque') );
			$att->set_tooltip('Définir comme script d\'attaque.');
			
			$classe = $row['nom']==$script_attaque['nom'] ? ' icone-attaque' : '';
			$modif = $li->add( new interf_lien_cont($G_url->get('action', 'voir')) );
			$a = $modif->add( new interf_bal_smpl('span', '', false, 'icone'.$classe) );
			if( $classe  )
				$a->set_tooltip('Script d\'attaque');
			$classe = $row['nom']==$script_defense['nom'] ? ' icone-defense' : '';
			$d = $modif->add( new interf_bal_smpl('span', '', false, 'icone'.$classe) );
			if( $classe )
				$d->set_tooltip('Script de défense');
			$nom = $modif->add( new interf_bal_smpl('span', $row['nom']) );
			$nom->set_tooltip('Modifier le script');
		}
	}
}

/// Classe pour la liste des scripts
class interf_script extends interf_cont
{
	function __construct($script)
	{
		global $G_url;
		$nom = $script->get_nom();
		if( $nom !== null )
		{
			$form_nom = $this->add( new interf_form($G_url->get('action', 'modifier_nom'), 'nom_script', 'get', 'input-group') );
			$form_nom->add( new interf_bal_smpl('span', 'Nom', false, 'input-group-addon') );
			$form_nom->add( new interf_chp_form('text', 'nom', false, $nom, false, 'form-control') );
			$btns_nom = $form_nom->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$val_nom = $btns_nom->add( new interf_chp_form('submit', false, false, 'Changer', false, 'btn btn-default') );
			$val_nom->set_attribut('onclick', 'return charger_formulaire(\'nom_script\');');
		}
		$ul = $this->add( new interf_bal_cont('ul', 'actions') );
	}
}

?>