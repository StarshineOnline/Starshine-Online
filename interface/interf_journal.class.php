<?php
/**
 * @file interf_journal.class.php
 * Interfaces pour les élections et candidatures
 */

class interf_journal extends interf_cont
{
	static $liste_options = array('soin'=>'Soins', 'gsoin'=>'Soins de groupe', 'buff'=>'Buffs', 'gbuff'=>'Buffs de groupe', 'rez'=>'Résurrections', 'degat'=>'Dégâts', 'kill'=>'Kills', 'quete'=>'Quêtes', 'loot'=>'Loots', 'siege'=>'Dégâts à l\'arme de siège', 'destruction'=>'Destruction de bâtiments'/*, 'nbrLignesJournal'=>'Nombre de lignes'*/);
	function __construct(&$perso, $options, $mois='actuel', $page=1)
	{
		global $G_url;
		include(root.'connect_log.php');
		$div_haut = $this->add( new interf_bal_cont('div', 'forms_journal') );
		// Choix du mois
		$form_mois = $div_haut->add( new interf_form($G_url->get('action', 'mois'), 'choix_mois', 'get', 'input-group') );
		$span_opt = /*$div_haut*/$form_mois->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn_opt = $span_opt->add( new interf_bal_smpl('button', '', false, 'btn btn-default icone icone-debug2') );
		$btn_opt->set_attribut('onclick', '$(\'#options\').slideToggle(); return false;');
		$btn_opt->set_tooltip('Affiche / masque les options d\'affichage');
		/*$div_haut*/$form_mois->add( new interf_bal_smpl('span', 'Mois', false, 'input-group-addon') );
		$sel_mois = /*$div_haut*/$form_mois->add( new interf_select_form('mois', false, false, 'form-control') );
		$sel_mois->add_option('Actuel', 'actuel');
		/// TODO: passer à l'objet
		$requete = 'SELECT table_name FROM information_schema.tables WHERE table_name like \'journal%\' AND table_schema = \''.$cfg_log['sql']['db'].'\' ORDER BY table_name DESC';
		$req = $db_log->query($requete);
		while($row = $db_log->read_assoc($req))
		{
			$date = str_replace('journal-', '', $row['table_name']);
			$sel_mois->add_option($date, $date, $date==$mois);
		}
		$span_voir = /*$div_haut*/$form_mois->add( new interf_bal_cont('span', false, 'input-group-btn') );
		$btn_voir = $span_voir->add( new interf_chp_form('submit', false, false, 'Voir', false, 'btn btn-default') );
		$btn_voir->set_attribut('onclick', 'return charger_formulaire(\'choix_mois\');');
		// Options
		$form_opt = $div_haut->add( new interf_form($G_url->get('action', 'options'), 'options', 'post', 'form-horizontal') );
		$form_opt->set_attribut('style', 'display: none');
		$form_opt->add( new interf_bal_smpl('span', 'Éléments que vous ne voulez pas voir apparaitre dans votre journal des actions&nbsp;:') );
		foreach(self::$liste_options as $cle=>$texte)
		{
			$div = $form_opt->add( new interf_bal_cont('div', false, 'checkbox') );
			$chbx = $div->add( new interf_chp_form('checkbox', $cle, $texte, 1) );
			if( $options[$cle] )
				$chbx->set_attribut('checked', 'checked');
		}
		$div = $form_opt->add( new interf_bal_cont('div') );
		$sel = $div->add( new interf_select_form('nbrLignesJournal', 'Nombre de lignes', 'nbr_lignes', 'form-control', 'control-label') );
		$sel->add_option('15', 15, $options['nbrLignesJournal']==15);
		$sel->add_option('30', 30, $options['nbrLignesJournal']==30);
		$sel->add_option('45', 45, $options['nbrLignesJournal']==45);
		$sel->add_option('60', 60, $options['nbrLignesJournal']==60);
		$valider = $form_opt->add( new interf_chp_form('submit', false, false, 'Valider', false, 'btn btn-default') );
		$valider->set_attribut('onclick', '$(\'#options\').slideToggle(); return charger_formulaire(\'options\');');
		
		/// Journal
		$div = $this->add( new interf_bal_cont('div', 'journal') );
		$div->add( new interf_journal_page($perso, $options, $mois, $page) );
	}
}

class interf_journal_page extends interf_cont
{
	protected $perso;
	protected $page;
	function __construct(&$perso, $options, $mois='actuel', $page=1)
	{
		global $G_url, $db;
		include(root.'connect_log.php');
		$this->perso = &$perso;
		// page du journal
		$this->page = $this->add( new interf_bal_cont('ul', 'page') );
		/// TODO: passer à l'objet
		$and = '';
		$nombre_action_journal = 15;
		foreach($options as $cle=>$valeur)
		{
			if($cle == "nbrLignesJournal")
			{
				$nombre_action_journal = $valeur;
			}
			else if($valeur == 1)
			{
				switch($cle)
				{
					case 'soin' :
						$and .= " AND action <> 'soin' AND action <> 'rsoin'";
					break;
					case 'gsoin' :
						$and .= " AND action <> 'gsoin' AND action <> 'rgsoin'";
					break;
					case 'buff' :
						$and .= " AND action <> 'buff' AND action <> 'rbuff'";
					break;
					case 'gbuff' :
						$and .= " AND action <> 'gbuff' AND action <> 'rgbuff'";
					break;
					case 'rez' :
						$and .= " AND action <> 'rez' AND action <> 'rrez'";
					break;
					case 'degat' :
						$and .= " AND action <> 'attaque' AND action <> 'defense'";
					break;
					case 'kill' :
						$and .= " AND action <> 'mort' AND action <> 'tue'";
					break;
					case 'quete' :
						$and .= " AND action <> 'f_quete'";
					break;
					case 'loot' :
						$and .= " AND action <> 'loot'";
					break;
					case 'pet' :
						$and .= " AND action <> 'pet_leave'";
					break;
					case 'rp' :
						$and .= " AND action <> 'rp'";
					break;
					case 'siege' :
						$and .= " AND action <> 'siege'";
					break;
					case 'destruction' :
						$and .= " AND action <> 'destruction'";
					break;
				}
			} 
		}
		if( $mois == 'actuel' )
		{
			$table = 'journal';
			$dbj = &$db;
		}
		else
		{
			$table = 'journal-'.$mois;
			$dbj = &$db_log;
		}
		$requete = 'SELECT COUNT(*) FROM `'.$table.'` WHERE id_perso = '.$this->perso->get_id();
		$req = $dbj->query($requete);
		$row = $dbj->read_row($req);
		$page_max = ceil($row[0] / $nombre_action_journal);
		$limit1 = ($page - 1) * $nombre_action_journal;
		$limit2 = $nombre_action_journal;
		$requete = 'SELECT * FROM `'.$table.'` WHERE id_perso = '.$this->perso->get_id().$and.' ORDER by time DESC, id DESC LIMIT '.$limit1.', '.$limit2;
		$req = $dbj->query($requete);
		while($row = $dbj->read_assoc($req))
		{
			$this->aff_entree($row);
		}
		// Pagination
		$pagination = $this->add( new interf_bal_cont('div', 'pagination') );
		$G_url->add('action', 'page');
		$G_url->add('mois', $mois);
		$pagination->add( new interf_pagination($page, $page_max, $G_url) );
	}
	function aff_entree($row)
	{
		global $G_url;
		$date = strtotime($row['time']);
		$date = date("j/m H:i", $date);
		$lien =  false;
		switch($row['action'])
		{
		case 'attaque' :
			$classe = 'jdegat';
			$lien = $G_url->get( array('action'=>'combat', 'id'=>$row['id']) );
			if ($row['actif'] != $this->perso->get_nom()) // Equivaut à : l'attaquant est le pet
				$texte = 'Vous attaquez '.$row['passif'].' avec '.$row['actif'].' et lui faites '.$row['valeur'].' dégâts, il lui en fait '.$row['valeur2'];
			else
				$texte = 'Vous attaquez '.$row['passif'].' et lui faites '.$row['valeur'].' dégâts, il vous en fait '.$row['valeur2'];
			break;
		case 'defense' :
			$classe = 'jrdegat';
			$lien = $G_url->get( array('action'=>'combat', 'id'=>$row['id']) );
			if ($row['actif'] != $this->perso->get_nom()) // Equivaut à : le defenseur est le pet
				$texte = $row['passif'].' a attaqué '.$row['actif'].' et fait '.$row['valeur'].' dégâts et '.$row['actif'].' fait '.$row['valeur2'];
			else
				$texte = $row['passif'].' vous a attaqué et fait '.$row['valeur'].' dégâts et vous lui faites '.$row['valeur2'];
			break;
		case 'tue' :
			$classe = 'jkill';
			$texte = 'Vous tuez '.$row['passif'];
		break;
		case 'mort' :
			$classe = 'jmort';
			$texte = $row['passif'].' vous a tué';
			break;
		case 'siege' :
			$classe = 'jdegat';
			$texte = 'Vous attaquez '.$row['passif'].' à l\'arme de siège et lui faites '.$row['valeur'].' dégâts';
		case 'destruction' :
			$classe = 'jkill';
			$texte = 'Vous détruisez '.$row['passif'];
			break;
		case 'pet_leave' :
			$classe = 'jmort';
			$texte = 'Votre '.$row['valeur'].' a échappé à votre contrôle et vous a quitté';
			break;
		case 'soin' :
			$classe = 'jsoin';
			$texte = 'Vous soignez '.$row['passif'].' de '.$row['valeur'].' HP';
			break;
		case 'rsoin' :
			$classe = 'jsoin';
			$texte = $row['passif'].' vous soigne de '.$row['valeur'].' HP';
			break;
		case 'gsoin' :
			$classe = 'jgsoin';
			$texte = 'Vous soignez votre groupe pour un total de '.$row['valeur'].' HP';
			break;
		case 'rgsoin' :
			$classe = 'jgsoin';
			$texte = $row['passif'].' vous soigne (en groupe) de '.$row['valeur'].' HP';
			break;
		case 'vend' :
			$classe = false;
			$texte = 'Vous avez vendu '.$row['valeur'].' pour '.$row['valeur2'].' stars';
			break;
		case 'loot' :
			$classe = 'jloot';
			$texte = 'Vous avez obtenu '.$row['valeur'].' en tuant un monstre';
			break;
		case 'f_quete' :
			$classe = 'jquete';
			$texte = 'Vous finissez la quête "'.$row['valeur'];
			break;
		case 'r_amende' :
			$classe = false;
			$texte = $row['passif'].' paye sa dette envers la société, et vous recevez '.$row['valeur'].' stars';
			break;
		case 'buff' :
			$classe = 'jbuff';
			$texte = 'Vous lancez le buff '.$row['valeur'].' sur '.$row['passif'];
			break;
		case 'rbuff' :
			$classe = 'jbuff';
			$texte = $row['passif'].' vous buff avec '.$row['valeur'];
			break;
		case 'gbuff' :
			$classe = 'jgbuff';
			$texte = 'Vous lancez le buff '.$row['valeur'].' sur votre groupe';
			break;
		case 'rgbuff' :
			$classe = 'jgbuff';
			$texte = $row['passif'].' vous buff (en groupe) avec '.$row['valeur'];
			break;
		case 'debuff' :
			$classe = 'jdebuff';
			$texte = 'Vous lancez le debuff '.$row['valeur'].' sur '.$row['passif'];
			break;
		case 'rdebuff' :
			$classe = 'jdebuff';
			$texte = $row['passif'].' vous debuff avec '.$row['valeur'];
			break;
		case 'rez' :
			$classe = 'jbuff';
			$texte = 'Vous avez ressuscité '.$row['passif'].' ('.$row['valeur'].'%)';
			break;
		case 'rrez' :
			$classe = 'jbuff';
			$texte = $row['passif'].' vous a ressuscité ('.$row['valeur'].'%)';
			break;
		case 'teleport' :
			$classe = 'jgbuff';
			if ($row['valeur'] == 'jeu')
				$texte = $row['actif'].' vous téléporte dans le jeu';
			else
				$texte = $row['actif'].' vous téléporte dans l\'arène '.$row['valeur'];
			break;
		case 'rp' :
			$classe = 'jrp';
			$texte = $row['valeur'];
			break;
		}
		$li = $this->page->add( new interf_bal_cont('li', false, $classe) );
		$li->add( new interf_bal_smpl('span', '['.$date.']', false, 'small') );
		if( $lien )
			$li->add( new interf_lien(ucfirst($texte).'.', $lien) );
		else
			$li->add( new interf_bal_smpl('span', ucfirst($texte).'.') );
	}
}
?>