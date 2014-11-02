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
	}
}

class interf_bonus_shine extends interf_tableau
{
	function __construct($categorie)
	{
		global $db, $Gtrad, $G_url;
		parent::__construct(false, false, false, false, false);
		$G_url->add('categorie', $categorie);
		
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
					if( $possede )
					{
						$lien = $li->add( new interf_lien_cont($G_url->get('action', 'configure')) );
						$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment prendre le bonus '.$row['nom'].' pour '.$row['point'].' points ?\');');
					}
					else
					{
						$lien = $li->add( new interf_lien_cont($G_url->get('action', 'prend')) );
					}
					$lien->set_tooltip('<strong>'.$row['nom'].'</strong><br />'.addcslashes($row['description'], "'").'<br />Requis : '.$Gtrad[$row['competence_requis']].' '.$row['valeur_requis']);
					$lien->set_attribut('data-html','true');
					$lien->add( new interf_img('image/niveau/'.$image.'.png', $row['nom']) );
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
							$case1->add( new interf_img('image/coin_hd.png') );
						}
						if($bn_num_rows > 1)
						{
							$case1 = new interf_bal_cont('li');
							$case1->add( new interf_img('image/coin_bg.png') );
							$case3 = new interf_bal_cont('li');
							$case1->add( new interf_img('image/coin_bd.png') );
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



?>