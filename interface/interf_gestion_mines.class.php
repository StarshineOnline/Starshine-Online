<?php
/**
 * @file interf_gestion_mines.class.php
 * Gestion des bourgs & mines
 */

/// Classe pour la liste d'échange
class interf_gestion_mines extends interf_cont
{
	function __construct(&$royaume, $actions=false, $carte=null)
	{
		global $G_url;
		interf_alerte::aff_enregistres($this);
		$princ = $this->add( new interf_bal_cont('div', 'bourgs_mines') );
		$acordeon = $princ->add( new interf_accordeon('liste_bourgs') );
		$bourgs = bourg::create(false, false, 'id', false, 'type = "bourg" AND x <= 190 AND y <= 190 AND royaume = '.$royaume->get_id());
		foreach($bourgs as $bourg)
		{
			$bourg->get_mines();
			$bourg->get_placements();
			$panneau = $acordeon->nouv_panneau($bourg->get_nom().' : '.$bourg->get_x().' / '.$bourg->get_y().' ('.$bourg->get_mine_total().' / '.$bourg->get_mine_max().')', 'bourg_'.$bourg->get_id(), $carte==$bourg->get_id());
			$bat = $bourg->get_batiment();
			$G_url->add('bourg', $bourg->get_id());
			$url = $G_url->copie('id', $bourg->get_id());
			$div = $panneau->add( new interf_bal_cont('div', false, 'info_case') );
			$suppr = $div->add( new interf_lien('', $url->get('action', 'suppr_bourg'), false, 'icone icone-poubelle') );
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir supprimer ce bâtiment ?\');');
			$suppr->set_tooltip('Supprimer');
			$nom = $div->add( new interf_lien('', $url->get('action', 'renommer'), false, 'icone icone-modifier') );
			$nom->set_tooltip('Renommer');
			$voir = $div->add( new interf_lien('', $url->get('action', 'voir'), false, 'icone icone-oeil') );
			$voir->set_tooltip('Voir la carte (et ajouter des mines)');
			if($bat->get_suivant() && !joueur::get_perso()->is_buff('debuff_rvr'))
			{
					$batiment_suivant = new batiment($bat->get_suivant());
					if( $batiment_suivant->get_cond1() < (time() - $bourg->get_date_construction()) )
					{
						$amel = $div->add( new interf_lien_cont($url->get('action', 'ameliorer'), false, 'icone') );
						$amel->add( new interf_bal_smpl('span', '', false, 'icone icone-plus') );
						$amel->add( new interf_bal_smpl('span', $batiment_suivant->get_cout(), false, 'xsmall') );
						$amel->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir améliorer ce bâtiment ?\');');
						$amel->set_tooltip('Améliorer ('.$batiment_suivant->get_cout().' stars)');
					}
					else
					{
						$tps_total = $batiment_suivant->get_cond1();// + $bourg->get_date_construction();
						$tps_ecoule = time() - $bourg->get_date_construction();
						$jauge = $div->add( new interf_jauge_bulle(false, $tps_ecoule, $tps_total, false, 'avance', false, 'jauge_case') );
						$jauge->set_tooltip('Amélioration possible dans '.transform_sec_temp($tps_total-$tps_ecoule));
					}
			}
			$div->add( new interf_bal_smpl('span', $bat->get_nom()) );
			$div->add( new interf_img('../image/batiment/'.$bat->get_image().'_04.png') );
			if(count($bourg->mines) > 0)
			{
				$liste = $panneau->add( new interf_bal_cont('ul') );
				foreach($bourg->mines as $mine)
				{
					$url->add('id', $mine->get_id());
					$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
					$suppr = $li->add( new interf_lien('', $url->get('action', 'suppr_mine'), false, 'icone icone-poubelle') );
					$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir supprimer ce bâtiment ?\');');
					$suppr->set_tooltip('Supprimer');
					$nom = $li->add( new interf_lien('', $url->get('action', 'renommer'), false, 'icone icone-modifier') );
					$nom->set_tooltip('Renommer');
					$batiment = $mine->get_batiment();
					/// @todo afficher la jauge en cas de trève
					if($batiment->get_suivant() && !joueur::get_perso()->is_buff('debuff_rvr'))
					{
						$batiment_suivant = new batiment($batiment->get_suivant());
						if( $batiment_suivant->get_cond1() < (time() - $mine->get_date_construction()) )
						{
							$amel = $li->add( new interf_lien_cont($url->get('action', 'ameliorer'), false, 'icone') );
							$amel->add( new interf_bal_smpl('span', '', false, 'icone icone-plus') );
							$amel->add( new interf_bal_smpl('span', $batiment_suivant->get_cout(), false, 'xsmall') );
							$amel->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir améliorer ce bâtiment ?\');');
							$amel->set_tooltip('Améliorer ('.$batiment_suivant->get_cout().' stars)');
						}
						else
						{
							$tps_total = $batiment_suivant->get_cond1();// + $mine->get_date_construction();
							$tps_ecoule = time() - $mine->get_date_construction();
							$jauge = $li->add( new interf_jauge_bulle(false, $tps_ecoule, $tps_total, false, 'avance', false, 'jauge_case') );
							$jauge->set_tooltip('Amélioration possible dans '.transform_sec_temp($tps_total-$tps_ecoule));
						}
					}
					$nom = $li->add( new interf_bal_smpl('span', $mine->get_nom()) );
					$li->add( new interf_bal_smpl('span', $batiment->get_nom().' - X='.$mine->get_x().' - Y='.$mine->get_y(), false, 'xsmall') );
					/// @todo à faire marcher
					/*$descr = '';
					if( !empty($mine->ressources['Pierre']) )
						$descr .= 'Pierre : '.$mine->ressources['Pierre'].'<br />';
					if( !empty($mine->ressources['Bois']) )
						$descr.= 'Bois : '.$mine->ressources['Bois'].'<br />';
					if( !empty($mine->ressources['Eau']) )
						$descr .= 'Eau : '.$mine->ressources['Eau'].'<br />';
					if( !empty($mine->ressources['Sable']) )
						$descr .= 'Sable : '.$mine->ressources['Sable'].'<br />';
					if( !empty($mine->ressources['Nourriture']) )
						$descr .= 'Nourriture : '.$mine->ressources['Nourriture'].'<br />';
					if( !empty($mine->ressources['Charbon']) )
						$descr .= 'Charbon : '.$mine->ressources['Charbon'].'<br />';
					if( !empty($mine->ressources['Essence Magique']) )
						$descr .= 'Essence Magique : '.$mine->ressources['Essence Magique'].'<br />';
					if( !empty($mine->ressources['Star']) )
						$descr .= 'Star : '.$mine->ressources['Star'].'<br />';
					$nom->set_tooltip($descr, 'bottom');
					$nom->set_attribut('data-html', 'true');*/
				}
			}
			if(count($bourg->placements) > 0)
			{
				$liste = $panneau->add( new interf_bal_smpl('h5', 'Construction(s) :') );
				$liste = $panneau->add( new interf_bal_cont('ul') );
				foreach($bourg->placements as $placement)
				{
					$url->add('id', $mine->get_id());
					$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
					$suppr = $li->add( new interf_lien('', $url->get('action', 'suppr_constr'), false, 'icone icone-poubelle') );
					$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir supprimer cette construction ?\');');
					$suppr->set_tooltip('Supprimer');
					$tps_total = $placement->get_fin_placement() - $placement->get_debut_placement();
					$tps_ecoule = time() - $placement->get_debut_placement();
					$jauge = $li->add( new interf_jauge_bulle(false, $tps_ecoule, $tps_total, false, 'avance', false, 'jauge_case') );
					$jauge->set_tooltip('Fin dans '.transform_sec_temp($tps_total-$tps_ecoule));
					$li->add( new interf_bal_smpl('span', $placement->get_nom()) ); 
					$li->add( new interf_bal_smpl('span', 'X='.$placement->get_x().' - Y='.$placement->get_y(), false, 'xsmall') );
				}
			}
		}
		
		// carte
		$div_carte = $princ->add( new interf_bal_cont('div', 'carte_bourg') );
		if($carte)
		{
			$G_url->add('bourg', $carte);
			$bourg = new bourg($carte);
			$div_carte->add( new interf_carte($bourg->get_x(), $bourg->get_y(), interf_carte::aff_gest_bourgs, 5, 'carte') );
		}
	}
}

class interf_bourg_case extends interf_dialogBS
{
	function __construct(&$bourg, $x, $y, &$royaume)
	{
		global $G_url, $db;
		parent::__construct('Poser une mine', true);
		$bourg->get_mines();
		$bourg->get_placements();
		if($bourg->get_mine_max() <= $bourg->get_mine_total())
		{
			$this->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Construction impossible, ce bourg ne peut plus avoir de mine associée') );
			return;
		}
		//On vérifie que la case appartient bien au royaume
		/// @todo passer à l'objet
		$requete = "SELECT ID, type FROM map WHERE x = ".sSQL($x)." AND y = ".sSQL($y)." AND royaume = ".$royaume->get_id();
		$db->query($requete);
		if($db->num_rows == 0)
		{
			$this->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Construction impossible, ce terrain ne vous appartient pas') );
			return;
		}
		$row = $db->read_assoc($req);
		//On vérifie qu'il y a pas déjà une construction sur cette case
		/// @todo passer à l'objet
		$requete = "SELECT id FROM construction WHERE x = ".$x." AND y = ".$y;
		$db->query($requete);
		if($db->num_rows > 0)
		{
			$this->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Construction impossible, il y a déjà un batiment') );
			return;
		}
		//On vérifie qu'il y a pas déjà une construction sur cette case
		/// @todo passer à l'objet
		$requete = "SELECT id FROM placement WHERE x = ".$x." AND y = ".$y;
		$db->query($requete);
		if($db->num_rows > 0)
		{
			$this->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Construction impossible, il y a déjà un batiment en construction') );
			return;
		}
		//On peut construire une mine
		/// @todo passer à l'objet
		$requete = "SELECT b.id, b.nom, b.cout, bp.valeur production, bs.valeur specialite 
			FROM batiment b 
			LEFT JOIN batiment_bonus bp ON bp.id_batiment = b.id and bp.bonus = 'production' 
			LEFT JOIN batiment_bonus bs ON bs.id_batiment = b.id and bs.bonus = 'specialite' 
			where b.type = 'mine' and b.cond1 = 0 and b.cout < 1000000";
		$req = $db->query($requete);		
		$form = $this->add( new interf_form($G_url->get('action', 'construire'), 'constr_mine') );
		$nbr = $form->add_champ_bs('hidden', 'bourg', null, $bourg->get_id());
		$nbr = $form->add_champ_bs('hidden', 'x', null, $x);
		$nbr = $form->add_champ_bs('hidden', 'y', null, $y);
		$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div->add( new interf_bal_smpl('span', 'Mine à construire', false, 'input-group-addon') );
		$sel = $div->add( new interf_select_form('mine', false, false, 'form-control') );
		while($row = $db->read_assoc($req))
		{
			$description = '';
			if($row['specialite'] != 0)
			{
				switch($row['specialite'])
				{
					case 1 :
						$description = 'Pierre x'.$row['production'];
					break;
					case 2 :
						$description = 'Bois x'.$row['production'];
					break;
					case 3 :
						$description = 'Eau x'.$row['production'];
					break;
					case 4 :
						$description = 'Sable x'.$row['production'];
					break;
					case 5 :
						$description = 'Nourriture x'.$row['production'];
					break;
					case 6 :
						$description = 'Star x'.$row['production'];
					break;
					case 7 :
						$description = 'Charbon x'.$row['production'];
					break;
					case 8 :
						$description = 'Essence Magique x'.$row['production'];
					break;
				}
			}
			else
				$description = 'Toute ressources x'.$row['production'];
			$sel->add_option($row['nom'].' - '.$row['cout'].' stars ('.$description.')', $row['id']);
		}
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Construire', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'constr_mine\');', 'primary');
	}
}

?>