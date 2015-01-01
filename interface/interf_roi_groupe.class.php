<?php
/**
 * @file interf_roi_groupe.class.php
 * 
 */ 

class interf_roi_groupe extends interf_onglets
{
	function __construct(&$royaume, $onglet)
	{
		global $G_url;
		parent::__construct('ongl_bourse', 'bourse');
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Groupes du royaume', $url->get('onglet', 'royaume'), 'ongl_royaume', 'ongl_gest', $onglet=='royaume');
		$this->add_onglet('Personnages dans un groupe étranger', $url->get('onglet', 'etrangers'), 'ongl_etrangers', 'ongl_gest', $onglet=='etrangers');
		$this->add_onglet('Personnages sans groupe', $url->get('onglet', 'sans'), 'ongl_sans', 'ongl_gest', $onglet=='sans');
		
		$div = $this->get_onglet('ongl_'.$onglet);
		switch($onglet)
		{
		case 'royaume':
			$div->add( new interf_roi_groupe_roy($royaume) );
			break;
		case 'etrangers':
			$div->add( new interf_roi_groupe_ext($royaume) );
			break;
		case 'sans':
			$div->add( new interf_roi_groupe_sans($royaume) );
			break;
		}
	}
}

class interf_roi_groupe_roy extends interf_data_tbl
{
	function __construct(&$royaume)
	{
  	global $db, $G_url, $G_nb_joueur_groupe;
		parent::__construct('groupes_royaume', '', false, false);
		
		$this->nouv_cell('Nom');
		$this->nouv_cell('Chef');
		$this->nouv_cell('Nombre');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Bataille');
		$this->nouv_cell('Message');
	
		$requete = "SELECT groupe.id as groupeid, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$royaume->get_race()."'";
		$req = $db->query($requete);
		//$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
		while($row = $db->read_assoc($req))
		{
			$groupe = new groupe($row['groupeid']);
			$leader = new perso($groupe->get_id_leader());
			$bataille_groupe = new bataille_groupe(0,0,$row['groupeid']);
			if($bataille_groupe->is_bataille()) 
			{
				$bataille = new bataille($bataille_groupe->get_id_bataille());
				$nom = $bataille->get_nom();
			}
			else
				$nom = "Aucune";
			/*foreach($partages as $part)
			{
				if($groupe->get_partage() == $part[0])
					$partage = $part[1];
			}*/
			$this->nouv_ligne();
			$this->nouv_cell( new interf_lien($groupe->get_nom(), $G_url->get(array('action'=>'infos', 'id'=>$groupe->get_id()))) );
			$this->nouv_cell( $leader->get_nom() );
			$this->nouv_cell( ($G_nb_joueur_groupe + 1 - $groupe->get_place_libre()).' / '.($G_nb_joueur_groupe+1) );
			//$this->nouv_cell( $partage );
			$this->nouv_cell( $groupe->get_level() );
			$this->nouv_cell( $nom );
			$this->nouv_cell( $lien = new interf_bal_smpl('a', '', false, 'icone icone-message') );
			$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=roi&id='.$groupe->get_id());
		}
	}
}

class interf_roi_groupe_ext extends interf_data_tbl
{
	function __construct(&$royaume)
	{
  	global $db, $G_url, $Tclasse;
		parent::__construct('groupes_etrangers', '', false, false);
		
		$this->nouv_cell('&nbsp');
		$this->nouv_cell('Nom');
		$this->nouv_cell('Classe');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Grade');
		$this->nouv_cell('Groupe');
		$this->nouv_cell('Royaume');
		$this->nouv_cell('Niv. grp');
		$this->nouv_cell('Message');
	
		$requete = 'SELECT p.id, p.groupe, l.race FROM perso AS p INNER JOIN groupe_joueur AS gj ON gj.id_groupe = p.groupe INNER JOIN perso AS l ON gj.id_joueur = l.id WHERE p.race = "'.$royaume->get_race().'" AND l.race != "'.$royaume->get_race().'" AND gj.leader = "y"';
		$req = $db->query($requete);
		//$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
		while($row = $db->read_assoc($req))
		{
			$perso = new perso($row['id']);
			$groupe = new groupe($row['groupe']);
			$leader = new perso($groupe->get_id_leader());
			$classe = $perso->get_cache_classe() >= 2 ? 'combattant' : $Tclasse[$perso->get_classe()]["type"];
			$this->nouv_ligne();
			$this->nouv_cell( new interf_img('../image/personnage/'.$perso->get_race().'/'.$perso->get_race().'_'.$classe.'.png') );
			$this->nouv_cell( $perso->get_nom() );
			$this->nouv_cell( $perso->get_cache_classe() >= 2 ? '' : $perso->get_classe() );
			$this->nouv_cell( $perso->get_cache_niveau() >= 2 ? '' : $perso->get_level() );
			$this->nouv_cell( $perso->get_grade()->get_nom() );
			$this->nouv_cell( $groupe->get_nom() );
			$this->nouv_cell( $Gtrad[$row['race']] );
			$this->nouv_cell( $groupe->get_level() );
			$this->nouv_cell( $lien = new interf_bal_smpl('a', '', false, 'icone icone-message') );
			$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$perso->get_id());
		}
	}
}

class interf_roi_groupe_sans extends interf_data_tbl
{
	function __construct(&$royaume)
	{
  	global $db, $G_url, $Tclasse;
		parent::__construct('groupes_sans', '', false, false);
		
		$this->nouv_cell('&nbsp');
		$this->nouv_cell('Nom');
		$this->nouv_cell('Classe');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Grade');
		$this->nouv_cell('Message');
	
		$persos = perso::create(false, false, 'id', false, 'groupe = 0 AND race = "'.$royaume->get_race().'" AND level > 0 AND statut = "actif"');
		foreach($persos as $perso)
		{
			$classe = $perso->get_cache_classe() >= 2 ? 'combattant' : $Tclasse[$perso->get_classe()]["type"];
			$this->nouv_ligne();
			$this->nouv_cell( new interf_img('../image/personnage/'.$perso->get_race().'/'.$perso->get_race().'_'.$classe.'.png') );
			$this->nouv_cell( $perso->get_nom() );
			$this->nouv_cell( $perso->get_cache_classe() >= 2 ? '' : $perso->get_classe() );
			$this->nouv_cell( $perso->get_cache_niveau() >= 2 ? '' : $perso->get_level() );
			$this->nouv_cell( $perso->get_grade()->get_nom() );
			$this->nouv_cell( $lien = new interf_bal_smpl('a', '', false, 'icone icone-message') );
			$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$perso->get_id());
		}
	}
}

class interf_roi_groupe_info extends interf_dialogBS
{
	protected $tbl;
	function __construct(&$groupe)
	{
		global $Tclasse, $Gtrad;
		parent::__construct('Groupe '.$groupe->get_nom(), true);
		
		$this->tbl = $this->add( new interf_data_tbl('groupe_info', '', false, false) );
		$this->tbl->nouv_cell('&nbsp;');
		$this->tbl->nouv_cell('Nom');
		$this->tbl->nouv_cell('Race');
		$this->tbl->nouv_cell('Grade');
		$this->tbl->nouv_cell('Classe');
		$this->tbl->nouv_cell('Niveau');
		$this->tbl->nouv_cell('Message');
		
		$groupe->get_membre_joueur();
		foreach($groupe->membre_joueur as $membre)
		{
			$classe = $membre->get_cache_classe() >= 2 ? 'combattant' : $Tclasse[$membre->get_classe()]["type"];
			$this->tbl->nouv_ligne();
			$this->tbl->nouv_cell( new interf_img('../image/personnage/'.$membre->get_race().'/'.$membre->get_race().'_'.$classe.'.png') );
			$this->tbl->nouv_cell( $membre->get_nom() );
			$this->tbl->nouv_cell( $Gtrad[$membre->get_race()] );
			$this->tbl->nouv_cell( $membre->get_grade()->get_nom() );
			$this->tbl->nouv_cell( $membre->get_cache_classe() >= 2 ? '' : $membre->get_classe() );
			$this->tbl->nouv_cell( $membre->get_cache_niveau() >= 2 ? '' : $membre->get_level() );
			$this->tbl->nouv_cell( $lien = new interf_bal_smpl('a', '', false, 'icone icone-message') );
			$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$membre->get_id());
		}
	}
}

?>