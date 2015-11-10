<?php
class interf_jabber extends interf_bal_cont
{
	function __construct($nom, $options)
	{
		global $jabber;
		parent::__construct('div', 'jabber', 'ng-cloak');
		$this->set_attribut('ng:controller', 'ssoJabber');
		$json = 'nom:"'.$nom.'", mdp:"'.joueur::factory()->get_jabber_mdp().'", serveur:"'.$jabber['serveur'].'", ressource:"'.$jabber['ressource'].'", domaine_salons:"'.$jabber['salons'].'"';
		foreach($options as $c=>$o)
		{
			if( $c == 'salons' )
			{
				$json_salons = array();
				foreach($o as $s=>$i)
				{
					$json_salons[] = '{nom:"'.$i['nom'].'", id:"'.$s.'", auto:'.($i['auto']?'true':'false').', option:"'.$i['option'].'"}';
				}
				$json .= ', salons:['.implode(',', $json_salons).']';
			}
			else
			{
				$json .= ', '.$c.':';
				if($o === true)
					$json .= 'true';
				else if($o === false)
					$json .= 'false';
				else if($o === null)
					$json .= 'null';
				else if(is_string($o))
					$json .= '"'.$o.'"';
				else
					$json .= $o;
			}
		}
		if( !in_array('style', $options) )
			$json .= ', style:true';
		if( !in_array('audio', $options) )
			$json .= ', audio:true';
		if( !in_array('salons', $options) )
			$json .= ', salons:[{nom:"Général", id:"sso", auto:false}]';
		self::code_js('var sso_jabber = {'.$json.'};');
		// Haut
		$haut = $this->add( new interf_bal_cont('div', 'jabber_haut') );
		$haut_droite = $haut->add( new interf_bal_cont('ul', 'jabber_haut_droite', 'nav navbar-nav navbar-right') );
		$options = $haut_droite->add( new interf_menu_div('Options') );
		$form = $options->add( new interf_bal_cont('div', 'jabber_options', 'form-horizontal') );
		$div_style = $form->add( new interf_bal_cont('div', false, 'form-group') );
		$ctrl_style = $div_style->add( new interf_chp_form('checkbox', 'jabber_style') );
		$ctrl_style->set_attribut('onclick', 'jabber_option(this);');
		$ctrl_style->set_attribut('ng:model', 'jabber.style');
		$div_style->add( new interf_bal_smpl('label', 'Texte riche') );
		$div_audio = $form->add( new interf_bal_cont('div', false, 'form-group') );
		$ctrl_audio = $div_audio->add( new interf_chp_form('checkbox', 'jabber_audio') );
		$ctrl_audio->set_attribut('ng:model', 'jabber.audio');
		$ctrl_audio->set_attribut('onclick', 'jabber_option(this);');
		$div_audio->add( new interf_bal_smpl('label', 'Audio') );
		$form->add( new interf_bal_smpl('strong', 'Connexions automatique') );
		$div_auto = $form->add( new interf_bal_cont('div', false, 'form-group') );
		$div_auto->set_attribut('ng:repeat', 'salon in jabber.salons');
		$ctrl_auto = $div_auto->add( new interf_chp_form('checkbox', '{{salon.option}}') );
		$ctrl_auto->set_attribut('ng:model', 'salon.auto');
		$ctrl_auto->set_attribut('onclick', 'jabber_option(this);');
		$div_auto->add( new interf_bal_smpl('label', '{{salon.nom}}') );
		$aff_dbg = $haut_droite->add( new interf_elt_menu('', '') );
		$aff_dbg->get_lien()->set_attribut('class', 'icone icone-debug');
		$aff_dbg->get_lien()->set_attribut('ng:click', 'jabber.aff_debug = !jabber.aff_debug;');
		$haut->add( new interf_bal_smpl('h4', 'Discussion') );
		$haut->add( new interf_bal_smpl('span', '<strong>Statut :</strong> {{jabber.statut}}') );
		// erreur
		$erreur = $this->add( new interf_alerte(interf_alerte::msg_erreur, false, 'jabber_erreur', '{{jabber.erreur}}') );
		$erreur->set_attribut('ng:show', 'jabber.erreur');
		// discussion
		$discussions = $this->add( new interf_onglets('jabber_salons', 'jabber_discussions') );
		$discussions->set_attribut('ng:show', 'jabber.connecte');
		$div_disc = $discussions->add_repeat('salon in jabber.salons', '{{salon.nom}}', 'jabber_salon_{{salon.id}}');
		$div_switch = $div_disc->add( new interf_bal_cont('div') );
		$div_switch->set_attribut('ng:switch', 'salon.statut');
		$div_nc = $div_switch->add( new interf_bal_cont('div', false, 'jabber_vide') );
		$div_nc->set_attribut('ng:switch-when', '0');
		$btn = $div_nc->add( new interf_bal_smpl('button', 'Entrer', false, 'btn btn-default') );
		$btn->set_attribut('ng:click', 'entrer_salon($index);');
		$div_cc = $div_switch->add( new interf_bal_cont('div', false, 'jabber_vide') );
		$div_cc->set_attribut('ng:switch-when', '1');
		$div_cc->add( new interf_bal_smpl('span', '', false, 'icone icone-charger') );
		$div_cc->add( new interf_bal_smpl('em', 'Connexion en cours') );
		$div_cc = $div_switch->add( new interf_bal_cont('div', false, 'container-fluid jabber_salon') );
		$div_cc->set_attribut('ng:switch-when', '2');
		$div_g = $div_cc->add( new interf_bal_cont('div', false, 'col-sm-10') );
		$div_d = $div_g->add( new interf_bal_cont('div', false, 'jabber_discussion') );
		$msg = $div_d->add( new interf_bal_cont('p') );
		$msg->set_attribut('ng:repeat', 'msg in salon.messages');
		$msg->set_attribut('ng:class', 'msg.classe');
		$msg->add( new interf_bal_smpl('strong', '[{{msg.date}}] {{msg.auteur}} : ', false, 'jabber_msg_info') );
		$msg->add( new interf_bal_smpl('span', '') )->set_attribut('ng:bind-html', 'msg.texte | html');
		$div_e = $div_g->add( new interf_bal_cont('div', false, 'jabber_salon_envoi') );
		$envoi = $div_e->add( new interf_editeur('jabber_envoi_{{salon.id}}', true, false, 'editeur') );
		$envoi->get_btn_envoi()->set_attribut('ng:click', 'envoi_salon($index);');
		$envoi->set_attribut('sso:jabber-envoi', '{{salon.id}}');
		$div_p = $div_cc->add( new interf_bal_cont('div', false, 'col-sm-2 jabber_participants') );
		$liste = $div_p->add( new interf_bal_cont('ul') );
		$part = $liste->add( new interf_bal_cont('li', '{{part.nom}}') );
		$part->set_attribut('ng:repeat', 'part in salon.participants | orderBy:\'ordre\'');
		$icone = $part->add( new interf_bal_smpl('span', '{{part.ordre}}', false, 'jabber_info_part icone') );
		$icone->set_attribut('ng:show', 'part.icone');
		$icone->set_attribut('ng:class', 'part.icone');
		$quitter = $part->add( new interf_bal_smpl('a', '', false, 'jabber_quitter icone icone-croix') );
		$quitter->set_attribut('ng:hide', 'part.ordre');
		$quitter->set_attribut('ng:click', 'quitter_salon(salon.index);');
		$quitter->set_tooltip('Quitter ce salon');
		$part->add( new interf_bal_smpl('span', '{{part.nom}}', false, 'jabber_nom') );
		// debogage
		$debug = $this->add( new interf_bal_cont('div', 'jabber_debug') );
		$debug->set_attribut('ng:show', 'jabber.aff_debug');
		$debug_haut = $debug->add( new interf_bal_cont('div', 'jabber_dbg_haut', 'form-inline') );
		$form = $debug_haut->add( new interf_bal_cont('div', 'jabber_dbg_niv', 'form-group') );
		//$ctrl_niv = $form->add( new interf_chp_form('number', false, 'Niveau minimal de debug', '0', false, 'form-control') );
		$ctrl_niv = $form->add( new interf_select_form(false, 'Niveau d\'affichage', false, 'form-control') );
		$ctrl_niv->add_option('Debug', '0', true);
		$ctrl_niv->add_option('Infos', 1);
		$ctrl_niv->add_option('Alertes', 2);
		$ctrl_niv->add_option('Erreurs', 3);
		$ctrl_niv->add_option('Erreurs fatales', 4);
		$ctrl_niv->set_attribut('ng:model', 'jabber.dbg_niv_min');
		//$ctrl_niv->set_attribut('convert-to-number', '');
		//$ctrl_niv->set_attribut('min', '0');
		$debug_haut->add( new interf_bal_smpl('h5', 'Débogage') );
		$debug_log = $debug->add( new interf_bal_cont('div', 'jabber_dbg_log') );
		$msg = $debug_log->add( new interf_bal_smpl('p', '{{dbg.message}}') );
		$msg->set_attribut('ng:repeat', 'dbg in jabber.debug | filter:jabber.dbg_niv_min:dbgSupeq()');
		//$msg->set_attribut('ng:repeat', 'dbg in debug');
		$msg->set_attribut('ng:class', 'dbg.classe');
		
		// Audio
		$audio_msg = $this->add( new interf_bal_cont('audio', 'jabber_son_msg') );
		$audio_msg->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/message.ogg', 'type'=>'audio/ogg') ) );
		$audio_msg->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/message.mp3', 'type'=>'audio/mpeg') ) );
		$audio_envoi = $this->add( new interf_bal_cont('audio', 'jabber_son_envoi') );
		$audio_envoi->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/envoi.ogg', 'type'=>'audio/ogg') ) );
		$audio_envoi->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/envoi.mp3', 'type'=>'audio/mpeg') ) );
		$audio_entre = $this->add( new interf_bal_cont('audio', 'jabber_son_entre') );
		$audio_entre->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/entrant.ogg', 'type'=>'audio/ogg') ) );
		$audio_entre->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/entrant.mp3', 'type'=>'audio/mpeg') ) );
		$audio_sort = $this->add( new interf_bal_cont('audio', 'jabber_son_sort') );
		$audio_sort->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/parti.ogg', 'type'=>'audio/ogg') ) );
		$audio_sort->add( new interf_bal_smpl('source', null, array('src'=>root_url.'audio/parti.mp3', 'type'=>'audio/mpeg') ) );
		
		//self::code_js('$(".dropdown input, .dropdown label").click(function(e) { e.stopPropagation();});');
	}
	static function creer_jabber($nom, &$parent, $salons)
	{
		global $G_interf;
		$parent->add( new interf_js() );
		//$parent->add( new interf_js_script('javascript/jsjac/JSJaC.js') );
		$parent->add( new interf_js_script(root_url.'javascript/strophe/strophe.js') );
		$parent->add( new interf_js_script(root_url.'javascript/strophe/strophe.muc.js') );
		$parent->add( new interf_js_script(root_url.'javascript/jabber.js') );
    $parent->add( $G_interf->creer_jabber($nom, $salons) );
	}
	static function get_options_perso(&$perso)
	{
  	global $db;
		$groupe = 'groupe_'.$perso->get_groupe();
		$race = $perso->get_race();
		$options = array('salons'=>array());
		$options['style'] = true;
		$options['audio'] = true;
		$options['salons'][$groupe] = array('nom'=>'Groupe', 'auto'=>true, 'option'=>'jabber_groupe');
		$options['salons'][$race] = array('nom'=>'Royaume', 'auto'=>true, 'option'=>'jabber_race');
		$options['salons']['sso'] = array('nom'=>'Général', 'auto'=>false, 'option'=>'jabber_sso');
		$options['salons']['salledesventes'] = array('nom'=>'Ventes', 'auto'=>false, 'option'=>'jabber_ventes');
  	///@todo passer à l'objet
		$requete = 'select nom, valeur from options where id_perso = '.$perso->get_id().' and nom in ("jabber_style", "jabber_audio", "jabber_groupe", "jabber_race", "jabber_sso", "jabber_ventes")';
		$req = $db->query($requete);
		while( $row = $db->read_assoc($req) )
		{
			switch($row['nom'])
			{
			case 'jabber_style':
				if( $row['valeur'] == 0 )
					$options['style'] = false;
				break;
			case 'jabber_audio':
				if( $row['valeur'] == 0 )
					$options['audio'] = false;
				break;
			case 'jabber_groupe':
				if( $row['valeur'] == 0)
					$options['salons'][$groupe]['auto'] = false;
				break;
			case 'jabber_audio':
				if( $row['valeur'] == 0)
					$options['salons'][$race]['auto'] = false;
				break;
			case 'jabber_sso':
				if( $row['valeur'] == 1)
					$options['salons']['sso']['auto'] = true;
				break;
			case 'jabber_ventes':
				if( $row['valeur'] == 1)
					$options['salons']['salledesventes']['auto'] = true;
				break;
			}
		}
		return $options;
	}
}
?>