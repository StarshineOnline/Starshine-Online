<?php
/**
 * @file interf_gestion_royaume.class.php
 * Interface de gestion 
 */ 

class interf_gestion_royaume extends interf_cont
{
	protected $royaume;
	protected $tbl;
	function __construct(&$royaume, $roi=true)
	{
		$this->royaume = &$royaume;
		interf_alerte::aff_enregistres($this);
		$this->aff_equipe($roi);
		$this->aff_journal();
	}
	protected function aff_equipe($roi)
	{
		global $G_url;
		$perso = joueur::get_perso();
		$liste = $this->add( new interf_bal_cont('ul', 'equipe_dirigeante') );
		$roi = perso::create(array('race', 'rang_royaume'), array($this->royaume->get_race(), 6))[0];
		if( $roi )
		{
			$li_roi = $liste->add( new interf_bal_cont('li', false, 'info_case') );
			$li_roi->add( new interf_bal_smpl('strong', 'Roi : ') );
			$li_roi->add( new interf_bal_smpl('span', $roi->get_nom()) );
			if( $roi->get_id() != $perso->get_id() )
			{
				$li_roi->add( $lien_roi = new interf_bal_smpl('a', $roi->get_nom(), 'icone icone-message') );
				$lien_roi->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$roi->get_id());
				$lien_roi->set_tooltip('Envoyer un message');
			}
		}
		if( $this->royaume->get_ministre_economie() )
		{
			$eco = new perso( $this->royaume->get_ministre_economie() );
			$li_eco = $liste->add( new interf_bal_cont('li', false, 'info_case') );
			$li_eco->add( new interf_bal_smpl('strong', 'Ministre de l\'économie : ') );
			$li_eco->add( new interf_bal_smpl('span', $eco->get_nom()) );
			if( $roi )
			{
				$suppr_eco = $li_eco->add( $lien_eco = new interf_lien('', $G_url->get('action', 'suppr_eco'), false, 'icone icone-poubelle') );
				$suppr_eco->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir virer ce ministre ?\');');
				$suppr_eco->set_tooltip('Virer le ministre de l\'économie');
			}
			if( $eco->get_id() != $perso->get_id() )
			{
				$li_eco->add( $lien_eco = new interf_bal_smpl('a', $eco->get_nom(), false, 'icone icone-message') );
				$lien_eco->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$eco->get_id());
				$lien_eco->set_tooltip('Envoyer un message');
			}
		}
		if( $this->royaume->get_ministre_militaire() )
		{
			$mil = new perso( $this->royaume->get_ministre_economie() );
			$li_mil = $liste->add( new interf_bal_cont('li', false, 'info_case') );
			$li_mil->add( new interf_bal_smpl('strong', 'Ministre militaire : ') );
			$li_mil->add( new interf_bal_smpl('span', $mil->get_nom()) );
			if( $roi )
			{
				$suppr_mil = $li_mil->add( $lien_eco = new interf_lien('', $G_url->get('action', 'suppr_mil'), false, 'icone icone-poubelle') );
				$suppr_mil->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir virer ce ministre ?\');');
				$suppr_mil->set_tooltip('Virer le ministre militaire');
			}
			if( $mil->get_id() != $perso->get_id() )
			{
				$li_mil->add( $lien_mil = new interf_bal_smpl('a', $mil->get_nom(), 'icone icone-message') );
				$lien_mil->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$mil->get_id());
				$lien_mil->set_tooltip('Envoyer un message');
			}
		}
	}
	protected function aff_journal()
	{
		$this->tbl = $this->add( new interf_data_tbl('journal_royaume', '') );
		$this->tbl->nouv_cell('Date');
		$this->tbl->nouv_cell('Acteur');
		$this->tbl->nouv_cell('Action');
		$this->tbl->nouv_cell('X');
		$this->tbl->nouv_cell('Y');
		
		$entrees = journal_royaume::create('id_royaume', $this->royaume->get_id(), 'id DESC');
		foreach($entree as $e)
		{
			$this->tbl->nouv_ligne();
			$this->tbl->nouv_cell( $e->get_time() );
			$this->tbl->nouv_cell( $e->get_actif() );
			$this->tbl->nouv_cell( $this->get_texte_journal($e) );
			$this->tbl->nouv_cell( $e->get_x() );
			$this->tbl->nouv_cell( $e->get_y() );
		}
	}
	protected function get_texte_journal(&$entree)
	{
		switch( $entree->get_action() )
		{
		case 'vente_bourse':
			return 'a mis en vente '.$entree->get_valeur2().' unités de '.$entree->get_valeur();
		case 'offre_achat':
			return 'a déposé une offre d\'achat pour '.$entree->get_valeur2().' unités de '.$entree->get_valeur();
		case 'echange':
			return 'a porposé un échange à'.$entree->get_passif();
		case 'reactive_ville':
			return 'a réactivé '.$entree->get_passif().' pour '.$entree->get_valeur2().' stars';
		case 'ameliore_ville':
			return 'a amélioré '.$entree->get_passif().' pour '.$entree->get_valeur2().' stars';
		case 'reduit_ville':
			return 'a réduit '.$entree->get_passif();
		case 'repare_ville':
			return 'a réparé '.$entree->get_passif().' pour '.$entree->get_valeur2().' stars';
		case 'taxe':
			return 'a changé les taxes à '.$entree->get_valeur2().'%';
		case 'suppr_batiment':
			return 'a supprimé le bâtiment externe '.$entree->get_valeur2().' ('.$entree->get_passif().')';
		case 'achat':
			return 'a acheté '.$entree->get_valeur2().' éléments(s) de '.$entree->get_passif();
		case 'monte_diplo':
			return 'a envoyé une demande de montée de diplomatie à '.$entree->get_valeur().', avec '.$entree->get_valeur2().'star(s), à '.$entree->get_passif();
		case 'baisse_diplo':
			return 'a baissé la diplomatie à '.$entree->get_valeur().' avec '.$entree->get_passif();
		case 'baisse_diplo_autre':
			return 'a baissé la diplomatie avec vous à '.$entree->get_valeur();
		case 'refus_diplo':
			return 'a refusé la montée de diplomatie à '.$entree->get_valeur().' (accompagnée de '.$entree->get_valeur2().'star(s)) de '.$entree->get_passif();
		case 'refus_diplo_autre':
			return 'a refusé votre montée de diplomatie à '.$entree->get_valeur().' (accompagnée de '.$entree->get_valeur2().'star(s))';
		case 'accepte_diplo':
			return 'a acceptée la montée de diplomatie à '.$entree->get_valeur().' (accompagnée de '.$entree->get_valeur2().'star(s)) de '.$entree->get_passif();
		case 'accepte_diplo_autre':
			return 'a acceptée votre montée de diplomatie à '.$entree->get_valeur().' (accompagnée de '.$entree->get_valeur2().'star(s))';
		case 'msg_roi':
			return 'a changé le message du roi';
		case 'propagande':
			return 'a changé la propagande';
		case 'prend_depot':
			return 'a pris '.$entree->get_valeur2().' éléments(s) de '.$entree->get_valeur().' au dépôt';
		case 'pose_depot':
			return 'a déposé 1 '.$entree->get_valeur().' au dépôt';
		default:
			return 'Action inconnue : '.$entree->get_action();
		}
	}
}

?>