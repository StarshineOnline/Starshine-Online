<?php
/**
 * @file interf_vie_royaume.class.php
 * Interfaces pour les élections et candidatures
 */

class interf_vie_royaume_base extends interf_gauche
{
	protected $perso;
	protected $royaume;
	protected $jauge_int = false;
	function __construct($titre)
	{
		parent::__construct('carte');
		$this->perso = joueur::get_perso();
		$this->royaume = royaume::create('race', $this->perso->get_race())[0];
		// Icone,titre & jauge extérieure
		$icone = $this->set_icone_centre('vote', 'vie_royaume.php');
		//$icone->set_tooltip( $this->batiment->get_nom() );
		$this->barre_haut->add( new interf_txt($titre) );
		$jour = date('j') + date('H')/24;
		if( $jour < 15 )
			$val = ($jour - 1) / 14 * 15;
		else
			$val  = 16 + ($jour - 15) / (date('t') - 15) * 15;
		$this->set_jauge_ext($val, 30, 'temps'/*, 'Avancement du mois : '*/);
	}
	function aff_jauge_candidats(&$election)
	{
		global $db;
		if( $this->jauge_int )
			return;
		/// @todo passer à l'objet
		$requete = 'SELECT COUNT(*) FROM candidat WHERE id_election = '.$election->get_id();
		$req = $db->query($requete);
		$val = $db->read_array($req)[0];
		$this->set_jauge_int($val, $this->royaume->get_habitants(), 'avance', 'Nombre de candidats : ');
		$this->jauge_int = true;
	}
	function aff_jauge_votants(&$election)
	{
		global $db;
		if( $this->jauge_int )
			return;
		/// @todo passer à l'objet
		$requete = 'SELECT COUNT(*) FROM vote WHERE id_election = '.$election->get_id();
		$req = $db->query($requete);
		$val = $db->read_array($req)[0];
		$this->set_jauge_int($val, $this->royaume->get_habitants(), 'avance', 'Nombre de votant : ');
		$this->jauge_int = true;
	}
	function aff_jauge_revolution(&$revolution)
	{
		global $db;
		if( $this->jauge_int )
			return;
		/// @todo passer à l'objet
		$requete = 'SELECT COUNT(*) FROM vote_revolution WHERE id_revolution = '.$revolution->get_id();
		$req = $db->query($requete);
		$val = $db->read_array($req)[0];
		$this->set_jauge_int($val, $this->royaume->get_habitants(), 'avance', 'Nombre de votant : ');
		$this->jauge_int = true;
	}
}

class interf_vie_royaume extends interf_vie_royaume_base
{
	function __construct()
	{
		global $db;
		parent::__construct('Vie du royaume');
		$jour = date('j');
		
		$elections = elections::get_prochain_election($this->royaume->get_id(), true);
		$this->centre->add( new interf_bal_smpl('p', 'Type d\'élection : '.$elections[0]->get_type()) );
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		$div_btns = $div->add( new interf_bal_cont('div', false, 'boutons') );
		$btns = $div_btns->add( new interf_bal_cont('div', false, 'btn-group') );
		interf_alerte::aff_enregistres($div);
		// Infos
		$infos = $div->add( new interf_bal_cont('div', 'infos_royaume') );
		/// @todo passer à l'objet
		$div_roi = $infos->add( new interf_bal_cont('div', 'roi') );
		$div_roi->add( new interf_bal_smpl('strong', 'Roi actuel : ') );
		$req = $db->query('SELECT nom FROM perso WHERE rang_royaume = 6 AND race = "'.$this->perso->get_race().'"');
		$row = $db->read_array($req);
		$div_roi->add( new interf_bal_smpl('span', $row[0]) );
		$div_eco = $infos->add( new interf_bal_cont('div', 'ministre_economie') );
		$div_eco->add( new interf_bal_smpl('strong', 'Ministre de l\'économie : ') );
		$req = $db->query('SELECT nom FROM perso WHERE id = '.$this->royaume->get_ministre_economie());
		$row = $db->read_array($req);
		if($row[0])
			$div_eco->add( new interf_bal_smpl('span', $row[0]) );
		else
			$div_eco->add( new interf_bal_smpl('em', 'aucun') );
		$div_mil = $infos->add( new interf_bal_cont('div', 'ministre_militaire') );
		$div_mil->add( new interf_bal_smpl('strong', 'Ministre militaire : ') );
		$req = $db->query('SELECT nom FROM perso WHERE id = '.$this->royaume->get_ministre_militaire());
		$row = $db->read_array($req);
		if($row[0])
			$div_mil->add( new interf_bal_smpl('span', $row[0]) );
		else
			$div_mil->add( new interf_bal_smpl('em', 'aucun') );
		
		// Elections
		$est_election = elections::is_mois_election($this->royaume->get_id());
		if( $est_election )
		{
			$verif_revolution = $elections[0]->get_type() == 'nomination';
			if($jour >= 2 && $jour < 15)
			{
				$btns->add( new interf_lien('Candidature', 'vie_royaume.php?action=candidature', false, 'btn btn-default') );
				$this->aff_jauge_candidats($elections[0]);
			}
			else if($jour >= 15)
			{
				if( $elections[0]->get_type() == 'universel' )
				{
					$btns->add( new interf_lien('Vote', 'vie_royaume.php?action=vote', false, 'btn btn-default') );
					$this->aff_jauge_votants($elections[0]);
				}
				else 	if( $this->perso->get_grade()->get_id() == 6 )
				{
					$btns->add( new interf_lien('Nomination', 'vie_royaume.php?action=vote', false, 'btn btn-default') );
					$this->aff_jauge_candidats($elections[0]);
				}
			}
		}
		else
		{
			$div->add	( new interf_bal_smpl('p', 'Prochaine élection : '.$elections[0]->get_date()) );
			$verif_revolution = true;
		}
		
		// Révolution
		if( $verif_revolution )
		{
			//Pas d'élection prévue prochainement, on peut renverser le pouvoir
			$est_revolution = revolution::is_mois_revolution($this->royaume->get_id(), $this->perso->get_id());
			if( $est_revolution )
			{
				// Il y a une révolution : on l'indique et propose de voter
				//$btns->add( new interf_lien('Voter pour ou contre la révolution', 'vie_royaume.php?action=vote_revolution', false, 'btn btn-primary') );
				$revolution = revolution::get_prochain_revolution($this->royaume->get_id())[0];
				$votes = vote_revolution::create(array('id_perso', 'id_revolution'), array($this->perso->get_id(), $revolution->get_id()));
				if( count($votes) )
				{
					$txt_pour = $txt_contre = 'Re-cliquer modifiera votre vote.';
					$style_pour = $style_contre = 'default';
					if( $votes[0]->get_pour() )
					{
						$style_pour = 'info';
						$txt_pour = 'Vous avez déjà voté pour. '.$txt_pour;
					}
					else
					{
						$style_contre = 'info';
						$txt_pour = 'Vous avez déjà voté contre. '.$txt_pour;
					}
				}
				else
				{
					$style_pour = $style_contre = 'primary';
					$txt_pour = 'Voter pour la révolution qui est en cours.';
					$txt_contre = 'Voter contre la révolution qui est en cours.';
				}
				$pour = $btns->add( new interf_lien('Pour la révolution', 'vie_royaume.php?action=revolution&vote=1', false, 'btn btn-'.$style_pour) );
				$pour->set_tooltip($txt_pour);
				$contre = $btns->add( new interf_lien('Contre la révolution', 'vie_royaume.php?action=revolution&vote=0', false, 'btn btn-'.$style_contre) );
				$contre->set_tooltip($txt_contre);
				$this->aff_jauge_revolution( revolution::get_prochain_revolution($this->royaume->get_id(), true)[0] );
			}
			else if( $jour >= 2 && $jour < 15 )
			{
				// Il n'y a pas de révolution : on propose d'en déclencher une 
				//$btns->add( new interf_lien('Déclencher une révolution', 'vie_royaume.php?action=revolution', false, 'btn btn-default') );
				$lien = $btns->add( new interf_bal_smpl('a', 'Déclencher une révolution', false, 'btn btn-default') );
				$lien->set_attribut('href', 'vie_royaume.php?action=revolution');
				$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Si vous déclenchez une révolution vous allez automatiquement voter pour. Ceci peut avoir de graves conséquences pour votre royaume. Êtes-vous sûr de vouloir le faire ?\');');
			}
		}
		
		/// @todo s'il n'y a toujours pas de jauge intérieure, afficher une avec l'évolution du mandat (temps écoulé)
	}
}

class interf_candidature extends interf_vie_royaume_base
{
	function __construct()
	{
		global $db;
		parent::__construct('Candidature');
		$elections = elections::get_prochain_election($this->royaume->get_id(), true);
		$this->aff_jauge_candidats($elections[0]);
		$candidat = candidat::create(array('id_election', 'id_perso'), array($elections[0]->get_id(), $this->perso->get_id()));
		if( count($candidat) )
		{
			$val_duree = $candidat[0]->get_duree();
			$val_type = $candidat[0]->get_type();
			/// @todo passer à l'objet
			$req = $db->query('SELECT nom FROM perso WHERE id='.$candidat[0]->get_id_ministre_economie());
			$val_min_eco = $db->read_array($req)[0];
			$req = $db->query('SELECT nom FROM perso WHERE id='.$candidat[0]->get_id_ministre_militaire());
			$val_min_mil = $db->read_array($req)[0];
			$val_prog = $candidat[0]->get_programme();
		}
		else
		{
			$val_duree = 1;
			$val_type = 'universel';
			$val_min_eco = $val_min_mil = $val_prog = '';
		}
		
		$form = $this->centre->add( new interf_form('vie_royaume.php?action=candidature2', 'candidature', 'POST') );
		// duree
		$div_duree = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div_duree->add( new interf_bal_smpl('span', 'Durée du mandat', false, 'input-group-addon') );
		$duree = $div_duree->add( new interf_select_form('duree', false, false, 'form-control') );
		$duree->add_option('1 mois', 1, $val_duree==1);
		$duree->add_option('2 mois', 2, $val_duree==2);
		$duree->add_option('3 mois', 3, $val_duree==3);
		$duree->add_option('6 mois', 6, $val_duree==6);
		$duree->add_option('1 an', 12, $val_duree==12);
		// Type d'élection
		$div_type = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div_type->add( new interf_bal_smpl('span', 'Type d\'élection', false, 'input-group-addon') );
		$type = $div_type->add( new interf_select_form('duree', false, false, 'form-control') );
		$type->add_option('universelle', 'universel', $val_type=='universel');
		$type->add_option('nomination', 'nomination', $val_type=='nomination');
		// Ministres
		$min_eco = $form->add_champ_bs('text', 'ministre_economie', null, $val_min_eco, 'Ministre de l\'économie', false, 'ministre_economie');
		$min_eco->set_attribut('onkeyup', 'javascript:suggestion(this.value, \'suggestion_mil\', this.id)');
		$min_mil = $form->add_champ_bs('text', 'ministre_militaire', null, $val_min_mil, 'Ministre militaire', false, 'ministre_militaire');
		$min_mil->set_attribut('onkeyup', 'javascript:suggestion(this.value, \'suggestion_eco\', this.id)');
		// Programme
		$div_prog = $form->add( new interf_bal_cont('div') );
		$div_prog->add( new interf_bal_smpl('label', 'Programme électoral') );
		$prog = $div_prog->add( new interf_bal_smpl('textarea', $val_prog, false, 'form-control') );
		$prog->set_attribut('name', 'programme');
		$div_btn = $form->add( new interf_bal_cont('div', false, 'boutons') );
		$grp_btn = $div_btn->add( new interf_bal_cont('div', false, 'btn-group') );
		if( count($candidat) )
		{
			//$grp_btn->add( new interf_lien('Retirer', 'vie_royaume?action=retirer_candidature', false, 'btn btn-default') );
			$lien = $grp_btn->add( new interf_bal_smpl('a', 'Retirer', false, 'btn btn-default') );
			$lien->set_attribut('href', 'vie_royaume.php?action=retirer_candidature');
			$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Votre candidature sera totalement supprimée. Êtes-vous sûr de vouloir le faire ?\');');
	    $btn = $grp_btn->add( new interf_chp_form('submit', false, false, 'Modifier', null, 'btn btn-primary') );
	    $btn->set_attribut('onclick', 'return charger_formulaire(\'candidature\');');
		}
		else
		{
	    $btn = $grp_btn->add( new interf_chp_form('submit', false, false, 'Se présenter', null, 'btn btn-primary') );
	    $btn->set_attribut('onclick', 'return charger_formulaire(\'vote\');');
		}
	}
}

class interf_vote extends interf_vie_royaume_base
{
	function __construct()
	{
		global $db;
		parent::__construct('Vote');
		$elections = elections::get_prochain_election($this->royaume->get_id(), true);
		if( $elections[0]->get_type() == 'universel' )
			$this->aff_jauge_votants($elections[0]);
		else
			$this->aff_jauge_candidats($elections[0]);
		$candidats = candidat::create('id_election',$elections[0]->get_id());
		/// @todo passer à l'objet
		$requete = 'SELECT * FROM vote WHERE id_election = '.$elections[0]->get_id().' AND id_perso = '.$this->perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if( $row )
		{
			$id = $row['id_candidat'];
			$texte = $elections[0]->get_type() == 'universel' ? 'Modifier son vote' : 'Modifier sa nommination';
		}
		else
		{
			$id = 0;
			$texte = $elections[0]->get_type() == 'universel' ? 'Voter' : 'Nommer';
		}
		
		$this->centre->add( new interf_bal_smpl('p', 'Nombre de candidats : '.count($candidats)) );
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		if( count($candidats) )
		{
			$form = $div->add( new interf_form('vie_royaume.php?action=vote2', 'vote') );
			$div_sel = $form->add( new interf_bal_cont('div', false, 'input-group') );
			$div_sel->add( new interf_bal_smpl('span', 'Candidat', false, 'input-group-addon') );
			$sel = $div_sel->add( new interf_select_form('candidat', false, false, 'form-control') );
			foreach($candidats as $c)
			{
				$sel->add_option($c->get_nom(), $c->get_id(), $id == $c->get_id());
			}
			$infos = $form->add( new interf_bal_cont('div', 'infos_candidature') );
			$div_btn = $form->add( new interf_bal_cont('div', false, 'boutons') );
	    $btn = $div_btn->add( new interf_chp_form('submit', false, false, $texte, null, 'btn btn-primary') );
	    $btn->set_attribut('onclick', 'return charger_formulaire(\'vote\');');
		}
		else
			$div->add( new interf_bal_smpl('p', 'Il n\'y a pas de candidat. Le roi actuel et ses ministres seront reconduis pour un mois.') );
	}
}

class interf_infos_candidat extends interf_cont
{
	function __construct(&$candidat)
	{
		global $db;
		// Durée
		$div = $this->add( new interf_bal_cont('div', 'duree') );
		$this->add( new interf_bal_smpl('strong', 'Durée du mandat :') );
		if( $candidat->get_duree() == 12 )
			$this->add( new interf_bal_smpl('span', '1 an') );
		else
			$this->add( new interf_bal_smpl('span', $candidat->get_duree().' mois') );
		// Type d'élection
		$div = $this->add( new interf_bal_cont('div', 'type') );
		$this->add( new interf_bal_smpl('strong', 'Prochaine élection : ') );
		$this->add( new interf_bal_smpl('span', $candidat->get_type()) );
		/// @todo passer à l'objet
		/// Ministre de l'économie
		$req = $db->query('SELECT nom FROM perso WHERE id='.$candidat->get_id_ministre_economie());
		$div = $this->add( new interf_bal_cont('div', 'ministre_economie') );
		$this->add( new interf_bal_smpl('strong', 'Ministre de l\'économie : ') );
		$this->add( new interf_bal_smpl('span', $db->read_array($req)[0]) );
		/// Ministre militaire
		$req = $db->query('SELECT nom FROM perso WHERE id='.$candidat->get_id_ministre_militaire());
		$div = $this->add( new interf_bal_cont('div', 'ministre_militaire') );
		$this->add( new interf_bal_smpl('strong', 'Ministre militaire : ') );
		$this->add( new interf_bal_smpl('span', $db->read_array($req)[0]) );
		// Programme
		$div = $this->add( new interf_bal_cont('div', 'programme') );
		$this->add( new interf_bal_smpl('strong', 'Programme : ') );
		$this->add( new interf_bal_smpl('span', $candidat->get_programme()) );
	}
}

?>