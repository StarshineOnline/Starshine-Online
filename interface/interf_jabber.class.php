<?php
class interf_jabber extends interf_bal_cont
{
	function __construct($nom)
	{
		global $jabber;
		parent::__construct('div', 'jabber');
		$this->set_attribut('ng:controller', 'ssoJabber');
		self::code_js('var sso_jabber = {nom:"'.$nom.'", mdp:"'.joueur::factory()->get_mdp().'", serveur:"'.$jabber['serveur'].'", ressource:"'.$jabber['ressource'].'"};');
		$haut = $this->add( new interf_bal_cont('div', 'jabber_haut') );
		$haut->add( new interf_bal_smpl('h4', 'Discussion') );
		$haut->add( new interf_bal_smpl('span', '<strong>Statut :</strong> {{statut}}') );	
		$erreur = $this->add( new interf_alerte(interf_alerte::msg_erreur, false, 'jabber_erreur', '{{erreur}}') );
		$erreur->set_attribut('ng:show', 'erreur');
		$debug = $this->add( new interf_bal_cont('div', 'jabber_debug') );
		$debug_haut = $debug->add( new interf_bal_cont('div', 'jabber_dbg_haut', 'form-inline') );
		$debug_haut->add( new interf_bal_smpl('h6', 'Débug') );
		$form = $debug_haut->add( new interf_bal_cont('div', 'jabber_dbg_niv', 'form-group') );
		$ctrl_niv = $form->add( new interf_chp_form('number', false, 'Niveau minimal de debug', '0', false, 'form-control') );
		$msg = $debug->add( new interf_bal_smpl('p', '[{{dbg.niveau}}] {{dbg.message}}') );
		$msg->set_attribut('ng:repeat', 'dbg in debug');
	}
	static function creer_jabber($nom, &$parent)
	{
		global $G_interf;
		$parent->add( new interf_js() );
		$parent->add( new interf_script('javascript/jsjac/JSJaC.js') );
		$parent->add( new interf_script('javascript/jabber.js') );
    $parent->add( $G_interf->creer_jabber($nom) );
	}
}
?>