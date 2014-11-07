<?php
/**
 * @file interf_points_shine.class.php
 * Classes pour les points shine
 */

/// Classe pour l'affichage des points shine
class interf_points_shine extends interf_onglets
{
	function __construct($categorie)
	{
		global $G_url;
		parent::__construct('cat_shine', 'points_shine');
		$url = clone $G_url;
		$url->add('ajax', 2);
		$this->add_onglet('Échange', $url->get('categorie',1), 'onglet_1', 'invent', $categorie==1);
		$this->add_onglet('Mimétisme', $url->get('categorie', 2), 'onglet_2', 'invent', $categorie==2);
		$this->add_onglet('Personnalisation', $url->get('categorie', 3), 'onglet_3', 'invent', $categorie==3);
		
		$this->get_onglet('onglet_'.$categorie)->add( new interf_bonus_shine($categorie) );
		$this->get_onglet('onglet_'.$categorie)->add( new interf_bal_smpl('p', 'Cliquez sur un bonus que vous avez déjà pour le configurer', false, 'xsmall') );
	}
}

class interf_bonus_shine extends interf_tableau
{
	function __construct($categorie)
	{
		global $db, $Gtrad, $G_url;
		parent::__construct(false, false, false, false, false);
		$G_url->add('categorie', $categorie);
		
		$bonus = recup_bonus(joueur::get_perso()->get_id());
		$requete = "SELECT COUNT(*) as tot, ligne FROM bonus WHERE id_categorie = ".$categorie." GROUP BY ligne";
		$req_l = $db->query($requete);
		$ligne = false;
		while( $row_l = $db->read_assoc($req_l) )
		{
			if( $ligne )
				$this->nouv_ligne();
			else
				$ligne = true;
			unset($case1, $case2, $case3);
			$i = 0;
			$requete = "SELECT * FROM bonus WHERE id_categorie = ".$categorie." AND ligne = ".$row_l['ligne']." ORDER BY id_bonus ASC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".$row['id_bonus'];
				$req_bn = $db->query($requete);
				$bn_num_rows = $db->num_rows;
				$check = true;
				while( ($row_bn = $db->read_assoc($req_bn)) && $check)
				{
					if( !array_key_exists($row_bn['id_bonus'], $bonus) )
						$check = false;
				}
				if($check)
				{
					$possede = array_key_exists($row['id_bonus'], $bonus);
					$image = $row['id_bonus'].($possede ? '' : '_l'); 
					$li = new interf_bal_cont('li');
					$li->add( new interf_bal_smpl('strong', $row['nom'], false, $possede ? 'possede' : false) );
					$li->add( new interf_bal_smpl('br') );
					if(!$possede)
					{
						$li->add( new interf_bal_smpl('span', $row['point'].' point(s)', false, 'xsmall') );
						$li->add( new interf_bal_smpl('br') );
					}
					$G_url->add('id', $row['id_bonus']);
					unset($img);
					$img = new interf_img('image/niveau/'.$image.'.png', $row['nom']);
					if( $possede )
					{
						if( $row['etat_modifiable'] || $row['valeur_modifiable'] )
						{
							$lien = $li->add( new interf_lien_cont($G_url->get('action', 'configure')) );
							$lien->add($img);
						}
						else
							$li->add($img);
					}
					else
					{
						$lien = $li->add( new interf_lien_cont($G_url->get('action', 'prend')) );
						$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment prendre le bonus '.$row['nom'].' pour '.$row['point'].' points ?\');');
						$lien->add($img);
					}
					$img->set_tooltip('<strong>'.$row['nom'].'</strong><br />'.addcslashes($row['description'], "'").'<br />Requis : '.$Gtrad[$row['competence_requis']].' '.$row['valeur_requis']);
					$img->set_attribut('data-html','true');
					if($row_l['tot'] > 1)
					{
						if($i > 0)
						{
							$case1 = $li;
						}
						else
						{
							$case3 = $li;
						}
					}
					else
					{
						$case2 = $li;
						$requete = "SELECT COUNT(*) FROM bonus_permet WHERE id_bonus = ".$row['id_bonus'];
						$req_bp = $db->query($requete);
						$row_bp = $db->read_row($req_bp);
						if($row_bp[0] > 1 && array_key_exists($row['id_bonus'], $bonus))
						{
							$case1 = new interf_bal_cont('li');
							$case1->add( new interf_img('image/coin_hg.png') );
							$case3 = new interf_bal_cont('li');
							$case3->add( new interf_img('image/coin_hd.png') );
						}
						if($bn_num_rows > 1)
						{
							$case1 = new interf_bal_cont('li');
							$case1->add( new interf_img('image/coin_bg.png') );
							$case3 = new interf_bal_cont('li');
							$case3->add( new interf_img('image/coin_bd.png') );
						}
					}
				}
				$i++;
			}
			$this->nouv_cell($case1);
			$this->nouv_cell($case2);
			$this->nouv_cell($case3);
		}
	}
}

class interf_bonus_shine_config extends interf_dialogBS
{
	function __construct($id)
	{
		global $db;
		$requete = "SELECT * FROM bonus WHERE id_bonus = ".sSQL($id);
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		parent::__construct($row['nom'], true);
		
		$form = $this->add( new interf_form('point_sso.php?action=modif&id='.$id, 'config_bonus', 'post') );
		$code = 'return charger_formulaire("'.$id.'");';
		if($row['valeur_modifiable'])
		{
			$bonus_total = recup_bonus_total(joueur::get_perso()->get_id());
			$bonus = recup_bonus(joueur::get_perso()->get_id());
			//Différents type de modification
			switch($id)
			{
			case 12:  //Sexe
				$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
				$div->add( new interf_bal_smpl('span', 'Sexe :', false, 'input-group-addon') );
				$partage = $div->add( new interf_select_form('valeur', false, false, 'form-control') );
				$partage->add_option('Masculin', '1', $bonus_total[$id]['valeur']==1);
				$partage->add_option('Feminin', '1', $bonus_total[$id]['valeur']==2);
				break;
			case 16:  //Description
				if( array_key_exists(24, $bonus) )
				{
					$form->add( new interf_editeur('texte_descr') );
					$code = 'return charger_formulaire_texte("'.$id.'", "texte_descr");';
				}
				else
				{
					$form->add( new interf_bal_smpl('textarea', $bonus_total[$id]['valeur'], false, 'form-control') );
					$form->set_attribut('name', 'texte');
				}
				break;
			case 19:  //Avatar
				$form->add( new interf_bal_smpl('p', 'Poids maximum du fichier : 20ko', false, 'help-block') );
				$form->add( new interf_bal_smpl('p', 'Dimensions maximums du fichier : 80px * 80px', false, 'help-block') );
				$div = $form->add( new interf_bal_cont('div', false, 'form-group') );
				$div->add( new interf_chp_form('file', 'fichier', false, false, 'fichier') );
				$code = 'return charger_formulaire_fichier("'.$id.'", "fichier");';
				//<input type="hidden" name="MAX_FILE_SIZE"  VALUE="20240" />
				//<input type="submit" value="Envoyer" onclick="return envoiFichier('formAvatar', 'popup_content');">
				break;
			}
		}
		//Configuration de l'état
		if($row['etat_modifiable'])
		{
			$div_e = $form->add( new interf_bal_cont('div', false, 'input-group') );
			$div_e->add( new interf_bal_smpl('span', 'Afficher', false, 'input-group-addon') );
			$etat = $div_e->add( new interf_select_form('etat', false, false, 'form-control') );
			$etat->add_option('à tout le monde', '1', $bonus[$id]==0);
			$etat->add_option('aux joueurs de votre race', '1', $bonus[$id]==1);
			$etat->add_option('à personne', '1', $bonus[$id]==2);
		}
		$this->ajout_btn('Annuler', 'fermer');
		$this->ajout_btn('Ok', 'fermer');
	}
}



?>