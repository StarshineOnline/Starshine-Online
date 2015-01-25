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
			$div->add( new interf_lien('', $url->get('action', 'debut'), false, 'icone icone-plus') )->set_tooltip('Débuter');
			$div->add( new interf_lien('', $url->get('action', 'modifier'), false, 'icone icone-modifier') )->set_tooltip('Modifier');
			break;
		case 1:
			$div->add( new interf_lien('', $url->get('action', 'fermer'), false, 'icone icone-moins') )->set_tooltip('Fermer');
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
	protected $div_princ;
	protected $div_gauche;
	protected $div_droite;
	function __construct()
	{
		interf_alerte::aff_enregistres($this);
		$this->div_princ = $this->add( new interf_bal_cont('div', 'gestion_batailles') );
		$this->div_gauche = $this->add( new interf_bal_cont('div', 'gest_bat_infos') );
		$this->div_droite = $this->add( new interf_bal_cont('div', 'gest_bat_carte') );
	}
}

class interf_modif_bataille extends interf_gest_bat_base
{
	function __construct($bataille=null)
	{
		parent::__construct();
	}
}

?>