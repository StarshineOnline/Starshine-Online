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
		/// @todo passer à l'objet (et au polymorphisme)
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
		$url = clone $G_url;
		while( $row = $db->read_assoc($req) )
		{
			$G_url->add('script', $row['id']);
			$li = $this->add( new interf_bal_cont('li', false, 'info_case') ); 
			$suppr = $li->add( new interf_lien('', $G_url->get('action', 'supprimer'), false, 'icone icone-poubelle') );
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment supprimer ce script ?\');');
			$suppr->set_tooltip('Supprimer ce script.');
			$copier = $li->add( new interf_lien('', $G_url->get('action', 'copier'), false, 'icone icone-copier') );
			$copier->set_tooltip('Créer un nouveau script identique à celui-ci.');
			$def = $li->add( new interf_lien('', $url->get(array('action'=>'defense', 'id_script'=>$row['id'])), false, 'icone icone-bouclier') );
			$def->set_tooltip('Définir comme script de défense.');
			$att = $li->add( new interf_lien('', $url->get(array('action'=>'attaque', 'id_script'=>$row['id'])), false, 'icone icone-attaque') );
			$att->set_tooltip('Définir comme script d\'attaque.');
			
			$classe = $row['nom']==$script_attaque['nom'] ? ' icone-attaque' : '';
			$modif = $li->add( new interf_lien_cont($G_url->get('action', 'voir')) );
			$a = $modif->add( new interf_bal_smpl('span', '', false, 'icone'.$classe) );
			if( $classe  )
				$a->set_tooltip('Script d\'attaque');
			$classe = $row['nom']==$script_defense['nom'] ? ' icone-bouclier' : '';
			$d = $modif->add( new interf_bal_smpl('span', '', false, 'icone'.$classe) );
			if( $classe )
				$d->set_tooltip('Script de défense');
			$nom = $modif->add( new interf_bal_smpl('span', $row['nom']) );
			$nom->set_tooltip('Modifier le script');
		}
		// Nouveau script
		$G_url->add('action', 'nouveau');
		$nouveau = $this->add( new interf_bal_cont('div', false, 'nouveau_script') );
		$btns = $nouveau->add( new interf_bal_cont('div', false, 'btn-group') );
		$btns->add( new interf_lien('Nouveau script simple', $G_url->get('type', 's'), false, 'btn btn-default') );
		$btns->add( new interf_lien('Nouveau script avancé', $G_url->get('type', 'a'), false, 'btn btn-default') );
	}
}

/// Classe pour la liste des scripts
class interf_script extends interf_cont
{
	private $sorts = null;
	private $comps = null;
	private $script = null;
	private $entite = null;
	function __construct($script, $entite)
	{
		global $G_url;
		$this->script = $script;
		$this->entite = $entite;
		$G_url->add('script', $script->get_id());
		$this->add( new interf_bal_smpl('p', 'Vous avez <strong>'.$entite->get_reserve_bonus().'</strong> réserves de mana au total par combat.') );
		if( $script->get_mode() == 's' )
			$this->aff_simple();
		else
			$this->aff_avance();
	}
	function aff_simple()
	{
		global $G_url;
		$this->aff_nom();
		$i = 0;
		$url = clone $G_url;
		foreach($this->script->get_actions() as $action)
		{
			$G_url->add('ligne', $i);
			$i++;
			$div = $this->add( new interf_bal_cont('div', 'round_'.$i, 'input-group script_round') );
			$div->add( new interf_bal_smpl('span', 'Round '.$i, false, 'input-group-addon') );
			$btn_act = $div->add( new interf_dropdown_select(true) );
			$G_url->add('action', 'modif_action');
			$btn_act->set_tooltip( $action->get_info() );
			$btn_act = $this->remplir_menu_actions($btn_act, $action->get_nom(), $G_url);
		}
		$div = $this->add( new interf_bal_cont('div', false, 'nouveau_script') );
		$btns = $div->add( new interf_bal_cont('div', false, 'btn-group') );
		$btns->add( new interf_lien('Transformer en script avancé', $url->get('action', 'transf_avance'), false, 'btn btn-default') );
	}
	function aff_avance()
	{
		global $G_url;
		$url = clone $G_url;
		// Fichier javascript
		self::code_js('var url_page = "'.$G_url->get().'";');
		$js = $this->add( new interf_bal_smpl('script', '') );
		$js->set_attribut('type', 'text/javascript');
		$js->set_attribut('src', 'javascript/scripts.js');
		$this->aff_nom();
		$ul = $this->add( new interf_bal_cont('ul', 'actions') );
		$i = 0;
		foreach($this->script->get_actions() as $action)
		{
			$G_url->add('ligne', $i);
			$li = $ul->add( new interf_bal_cont('li', 'action_'.$i, 'action') );
			$j=0;
			foreach($action->get_conditions() as $cond)
			{
				$input = $cond->get_type_parametre() == condition::param_valeur;
				$div = $li->add( new interf_bal_cont('div', 'cond_'.$i.'_'.$j, $input ? 'input-group' : 'btn-group') );
				$sel_cond = $div->add( new interf_dropdown_select($input) );
				foreach(condition::get_codes() as $c=>$t)
				{
					$sel_cond->add_option($t, $G_url->get(array('action'=>'change_cond', 'cond'=>$j, 'valeur'=>$c)), $cond->get_code() == $c);
				}
				if( $cond->a_operateur() )
				{
					$sel_op = $div->add( new interf_dropdown_select($input) );
					$sel_op->add_option('&lt;', $G_url->get(array('action'=>'change_op', 'cond'=>$j, 'valeur'=>'p')), $cond->get_operateur() == '<');
					$sel_op->add_option('=', $G_url->get(array('action'=>'change_op', 'cond'=>$j, 'valeur'=>'e')), $cond->get_operateur() == '=');
					$sel_op->add_option('&gt;', $G_url->get(array('action'=>'change_op', 'cond'=>$j, 'valeur'=>'g')), $cond->get_operateur() == '>');
				}
				$param = $cond->get_parametre();
				switch( $cond->get_type_parametre() )
				{
				case condition::param_valeur:
					$param = $div->add( new interf_chp_form('number', false, false, $param, false, 'form-control') );
					$param->set_attribut('onchange', 'change_script_nombre(this, \''.$G_url->get(array('action'=>'change_param', 'cond'=>$j)).'\');');
					break;
				case condition::param_etat:
					$sel_param = $div->add( new interf_dropdown_select($input) );
					foreach(condition::get_etats() as $e=>$t)
					{
						$sel_param->add_option($t, $G_url->get(array('action'=>'change_param', 'cond'=>$j, 'valeur'=>$e)), $param == $e);
					}
					break;
				case condition::param_action:
					$sel_param = $div->add( new interf_dropdown_select($input) );
					foreach(condition_action::get_actions() as $a=>$t)
					{
						$sel_param->add_option($t, $G_url->get(array('action'=>'change_param', 'cond'=>$j, 'valeur'=>$a)), $param == $a);
					}
					break;
				}
				$div_btns = $input ? $div->add( new interf_bal_cont('span', false, 'input-group-btn') ) : $div;
				$div_btns->add( new interf_lien('', $G_url->get(array('action'=>'suppr_cond', 'cond'=>$j)), false, 'btn btn-default icone icone-moins') );
				$j++;
			}
			// Icone et infobulle pour le déplacement
			$depl =  $li->add( new interf_bal_smpl('span', '', false, 'icone icone-deplacer') );
			$depl->set_tooltip('Déplacez ce cadre (par glisser-déposer) pour modifier l\'ordre.');
			// Action
			$div = $li->add( new interf_bal_cont('div', 'act_'.$i, 'btn-group') );
			$div_act = $div->add( new interf_bal_cont('div', false, 'btn-group') );
			$btn_act = $div_act->add( new interf_dropdown_select() );
			$btn_act->set_tooltip( $action->get_info() );
			$this->remplir_menu_actions($btn_act, $action->get_nom(), $G_url->copie('action', 'modif_action'));
			$ajout = $div->add( new interf_lien('', $G_url->get('action', 'ajout_cond'), false, 'btn btn-default icone icone-plus') );
			$ajout->set_tooltip('Ajouter une condition');
			$copier = $div->add( new interf_lien('', $G_url->get('action', 'copie_action'), false, 'btn btn-default icone icone-copier') );
			$copier->set_tooltip('Copier cette action');
			$suppr = $div->add( new interf_lien('', $G_url->get('action', 'suppr_action'), false, 'btn btn-default icone icone-poubelle') );
			$suppr->set_tooltip('Supprimer cette action');
			$i++;
		}
		// Ajout d'une action
		$url->add('action', 'ajout_action');
		$sel_act = $this->add( new interf_dropdown_select() );
		$this->remplir_menu_actions($sel_act, 'Ajouter une action', $url);
	}
	function aff_nom()
	{
		global $G_url;
		$nom = $this->script->get_nom();
		if( $nom !== null )
		{
			/// @todo passer à l'objet (et au polymorphisme)
			if( get_class($entite) == 'perso' )
			{
				$script_attaque = recupaction_all($this->entite->get_action_a());
				$script_defense = recupaction_all($this->entite->get_action_d());
			}
			else
			{
				$script_attaque = recupaction_all($this->entite->get_action_a(), true);
				$script_defense = recupaction_all($this->entite->get_action_d(), true);
			}
			$attaque = $nom == $script_attaque['nom'];
			$defense = $nom == $script_defense['nom'];
			
			$form_nom = $this->add( new interf_form($G_url->get('action', 'modifier_nom'), 'nom_script', 'get', 'input-group') );
			if( !$attaque || !$defense )
			{
				$btns_deb = $form_nom->add( new interf_bal_cont('span', false, 'input-group-btn') );
				if( !$attaque )
				{
					$att = $btns_deb->add( new interf_lien('', $G_url->get('action', 'attaque'), false, 'icone icone-attaque btn btn-default') );
					$att->set_tooltip('Définir comme script d\'attaque.');
				}
				if( !$defense )
				{
					$def = $btns_deb->add( new interf_lien('', $G_url->get('action', 'defense'), false, 'icone icone-bouclier btn btn-default') );
					$def->set_tooltip('Définir comme script de défense.');
				}
			}
			$form_nom->add( new interf_bal_smpl('span', 'Nom', false, 'input-group-addon') );
			$form_nom->add( new interf_chp_form('text', 'nom', false, $nom, false, 'form-control') );
			$btns_fin = $form_nom->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$val_nom = $btns_fin->add( new interf_chp_form('submit', false, false, 'Changer', false, 'btn btn-default') );
			$val_nom->set_attribut('onclick', 'return charger_formulaire(\'nom_script\');');
		}
	}
	function remplir_menu_actions(&$menu, $action, $url)
	{
		if( $this->comp_sorts === null )
		{
			global $db, $Gtrad;
			$this->sorts = array();
			$this->comps = array();
			$apt = null;
			$comp_sort=null;
			/// @todo passer à l'objet
			$sorts = $this->entite->get_sort_combat();
			if( $sorts )
			{
				$requete = 'SELECT id, nom, comp_assoc, type FROM sort_combat WHERE id IN ('.str_replace(';', ',', $sorts).') ORDER BY comp_assoc, nom';
				$req = $db->query($requete);
				while( $row = $db->read_array($req) )
				{
					if( $apt != $row['comp_assoc'] )
					{
						if( count($this->sorts[$apt][$comp_sort]) == 1 )
						{
							$cle = array_keys($this->sorts[$apt][$comp_sort])[0];
							$elt = array_pop($this->sorts[$apt][$comp_sort]);
							unset($this->sorts[$apt][$comp_sort]);
							$this->sorts[$apt][$cle] = $elt;
						}
						$apt = $row['comp_assoc'];
						$this->sorts[$apt] = array();
					}
					if( $comp_sort != $row['type'] )
					{
						if( count($this->sorts[$apt][$comp_sort]) == 1 )
						{
							$cle = array_keys($this->sorts[$apt][$comp_sort])[0];
							$elt = array_pop($this->sorts[$apt][$comp_sort]);
							unset($this->sorts[$apt][$comp_sort]);
							$this->sorts[$apt][$cle] = $elt;
						}
						$comp_sort = $row['type'];
						$this->sorts[$apt][$comp_sort] = array();
					}
					$this->sorts[$apt][$row['type']][$row['id']] = $row['nom'];
				}
				if( count($this->sorts[$apt][$comp_sort]) == 1 )
				{
					$cle = array_keys($this->sorts[$apt][$comp_sort])[0];
					$elt = array_pop($this->sorts[$apt][$comp_sort]);
					unset($this->sorts[$apt][$comp_sort]);
					$this->sorts[$apt][$cle] = $elt;
				}
				foreach($this->sorts as $apt=>$liste)
				{
					if( count($liste) == 1 && is_array( array_values($liste)[0] ) )
					{
						$this->sorts[$apt] = array_values($liste)[0];
					}
				}
			}
			$comps = $this->entite->get_comp_combat();
			if( $comps )
			{
				$apt = null;
				$comp_sort=null;
				$requete = 'SELECT id, nom, comp_assoc, type FROM comp_combat WHERE id IN ('.str_replace(';', ',', $comps).') ORDER BY comp_assoc, type, comp_requis';
				$req = $db->query($requete);
				while( $row = $db->read_array($req) )
				{
					if( $apt != $row['comp_assoc'] )
					{
						if( count($this->comps[$apt][$comp_sort]) == 1 )
						{
							$cle = array_keys($this->comps[$apt][$comp_sort])[0];
							$elt = array_pop($this->comps[$apt][$comp_sort]);
							unset($this->comps[$apt][$comp_sort]);
							$this->comps[$apt][$cle] = $elt;
						}
						$apt = $row['comp_assoc'];
						$this->comps[$apt] = array();
					}
					if( $comp_sort != $row['type'] )
					{
						if( count($this->comps[$apt][$comp_sort]) == 1 )
						{
							$cle = array_keys($this->comps[$apt][$comp_sort])[0];
							$elt = array_pop($this->comps[$apt][$comp_sort]);
							unset($this->comps[$apt][$comp_sort]);
							$this->comps[$apt][$cle] = $elt;
						}
						$comp_sort = $row['type'];
						$this->comps[$apt][$comp_sort] = array();
					}
					$this->comps[$apt][$row['type']][$row['id']] = $row['nom'];
				}
				if( count($this->comps[$apt][$comp_sort]) == 1 )
				{
					$cle = array_keys($this->comps[$apt][$comp_sort])[0];
					$elt = array_pop($this->comps[$apt][$comp_sort]);
					unset($this->comps[$apt][$comp_sort]);
					$this->comps[$apt][$cle] = $elt;
				}
				foreach($this->comps as $apt=>$liste)
				{
					if( count($liste) == 1 && is_array( array_values($liste)[0] ) )
					{
						$this->comps[$apt] = array_values($liste)[0];
					}
				}
			}
		}
		$menu->add_option('Attaque', $url->get('type', 'attaque'));
		if( count($this->comps) )
		{
			$url->add('type', 'comp');
			foreach($this->comps as $apt=>$liste)
			{
				$menu->nouv_groupe( $Gtrad[$apt] );
				foreach($liste as $id=>$nom)
				{
					if( is_numeric($id) )
						$menu->add_option($nom, $url->get('valeur', $id));
					else
					{
						$menu->nouv_groupe(array_values($nom)[0], true);
						foreach($nom as $id=>$nom)
							$menu->add_option($nom, $url->get('valeur', $id));
						$menu->pop_groupe();
					}
				}
			}
		}
		if( count($this->sorts) )
		{
			$url->add('type', 'sort');
			foreach($this->sorts as $apt=>$liste)
			{
				$menu->nouv_groupe( $Gtrad[$apt] );
				foreach($liste as $id=>$nom)
				{
					if( is_numeric($id) )
						$menu->add_option($nom, $url->get('valeur', $id));
					else
					{
						$menu->nouv_groupe(array_values($nom)[0], true);
						foreach($nom as $id=>$nom)
							$menu->add_option($nom, $url->get('valeur', $id));
						$menu->pop_groupe();
					}
				}
			}
		}
		$menu->set_texte($action);
	}
}

?>