<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$perso->check_perso();
$bonus = recup_bonus($perso->get_id());

$interf_princ = $G_interf->creer_jeu();

$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 1;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;


if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
	case 'prend' :
		if(array_key_exists($_GET['id'], $bonus))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous possédez déjà ce bonus !');
			break;
		}
		//Récupération des infos interessantes du bonus
		$requete = "SELECT * FROM bonus WHERE id_bonus = ".sSQL($_GET['id']);
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		//Vérification si il a assez de points
		if($perso->get_point_sso() >= $row['point'])
		{
			//Vérifie si il a assez en compétence requise
			if($perso->get_comp($row['competence_requis']) >= $row['valeur_requis'])
			{
				$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".sSQL($_GET['id']);
				$req_bn = $db->query($requete);
				$bn_num_rows = $db->num_rows;
				$check = true;
				while(($row_bn = $db->read_assoc($req_bn)) AND $check)
				{
					if(!array_key_exists($row_bn['id_bonus'], $bonus)) $check = false;
				}
				if($check)
				{
					if( in_array($_GET['id'], array(bonus_perso::CACHE_GRADE_ID, bonus_perso::CACHE_CLASSE_ID, bonus_perso::CACHE_STATS_ID, bonus_perso::CACHE_NIVEAU_ID)) )
						$perso->ajout_bonus_shine($_GET['id'], '', 1);
					else
						$perso->ajout_bonus_shine($_GET['id']);
					$perso->set_point_sso($perso->get_point_sso() - $row['point']);
					$perso->sauver();
					$bonus = recup_bonus($perso->get_id());
				}
				else
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous manque un bonus pour apprendre celui-ci');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$row['valeur_requis'].' en '.$Gtrad[$row['competence_requis']]);
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de points Shine');
	break;
	}
}

if( $ajax == 2 )
{
	interf_alerte::aff_enregistres($interf_princ);
	$interf_princ->add( $G_interf->creer_bonus_shine($categorie) );
}
else
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Points shine') );
	interf_alerte::aff_enregistres($cadre);
	$cadre->add( $G_interf->creer_points_shine($categorie) );
}







exit;


if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'prend' :
			if(array_key_exists($_GET['id'], $bonus))
			{
				echo '<h5>Vous possédez déjà ce bonus !</h5>';
			}
			else
			{
				//Récupération des infos interessantes du bonus
				$requete = "SELECT * FROM bonus WHERE id_bonus = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				//Vérification si il a assez de points
				if($joueur->get_point_sso() >= $row['point'])
				{
					//Vérifie si il a assez en compétence requise
					if($joueur->get_comp($row['competence_requis']) >= $row['valeur_requis'])
					{
						$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".sSQL($_GET['id']);
						$req_bn = $db->query($requete);
						$bn_num_rows = $db->num_rows;
						$check = true;
						while(($row_bn = $db->read_assoc($req_bn)) AND $check)
						{
							if(!array_key_exists($row_bn['id_bonus'], $bonus)) $check = false;
						}
						if($check)
						{
							if( in_array($_GET['id'], array(bonus_perso::CACHE_GRADE_ID, bonus_perso::CACHE_CLASSE_ID, bonus_perso::CACHE_STATS_ID, bonus_perso::CACHE_NIVEAU_ID)) )
								$joueur->ajout_bonus_shine($_GET['id'], '', 1);
							else
								$joueur->ajout_bonus_shine($_GET['id']);
							$joueur->set_point_sso($joueur->get_point_sso() - $row['point']);
							$joueur->sauver();
							$bonus = recup_bonus($joueur->get_id());
						}
						else
						{
							echo '<h5>Il vous manque un bonus pour apprendre celui-ci</h5>';
						}
					}
					else
					{
					echo '<h5>Il vous faut '.$row['valeur_requis'].' en '.$Gtrad[$row['competence_requis']].'</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de points Shine</h5>';
				}
			}
		break;
	}
}
if(!array_key_exists('categorie', $_GET)) $categorie = 1; else $categorie = $_GET['categorie'];

?>
<h3>Vous avez <?php echo $joueur->get_point_sso(); ?> point(s) Shine <a href="configure_point_sso.php" onclick="affichePopUp(this.href); return false;">(configurer)</a></h3>
<table style="width:100%;">
<tr>
	<td style="text-align : center; width:33%;">
		<a href="point_sso.php?categorie=1" onclick="return envoiInfo(this.href, 'information')">Echange</a>
	</td>
	<td style="text-align : center; width:33%;">
		<a href="point_sso.php?categorie=2" onclick="return envoiInfo(this.href, 'information')">Mimétisme</a>
	</td>
	<td style="text-align : center; width:33%;">
		<a href="point_sso.php?categorie=3" onclick="return envoiInfo(this.href, 'information')">Personnalisation</a>
	</td>
</tr>
</table>
<div class="information_case">
<table style="font-size : 0.9em; width:97%;">
<?php
$requete = "SELECT COUNT(*) as tot, ligne FROM bonus WHERE id_categorie = ".$categorie." GROUP BY ligne";
$req_l = $db->query($requete);
while($row_l = $db->read_assoc($req_l))
{
	//print_r($row_l);
	$case1 = '';
	$case2 = '';
	$case3 = '';
	echo '
	<tr>';
	$i = 0;
	$requete = "SELECT * FROM bonus WHERE id_categorie = ".$categorie." AND ligne = ".$row_l['ligne']." ORDER BY id_bonus ASC";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".$row['id_bonus'];
		$req_bn = $db->query($requete);
		$bn_num_rows = $db->num_rows;
		$check = true;
		while(($row_bn = $db->read_assoc($req_bn)) AND $check)
		{
			if(!array_key_exists($row_bn['id_bonus'], $bonus)) $check = false;
		}
		if($check)
		{
			$possede = false;
			if(array_key_exists($row['id_bonus'], $bonus)) $possede = true;
			if($possede)
			{
				$color = '#aaaa00';
				$image = $row['id_bonus'];
			}
			else
			{
				$color = '#000';
				$image = $row['id_bonus'].'_l';
			}
			$li = new interf_bal_cont('li');
			$texte = '<strong style="color : '.$color.'">'.$row['nom'].'</strong><br />';
			if(!$possede) $texte.= '<span class="xsmall">'.$row['point'].' point(s)</span><br />';
			$overlib = '<strong>'.$row['nom'].'</strong><br />'.addcslashes($row['description'], "'").'<br />Requis : '.$Gtrad[$row['competence_requis']].' '.$row['valeur_requis'];
			$texte .= '<a href="point_sso.php?action=prend&amp;id='.$row['id_bonus'].'&amp;categorie='.$categorie.'" onclick="if(confirm(\'Voulez-vous vraiment prendre le bonus ~'.$row['nom'].'~ pour '.$row['point'].' points ? \')) return envoiInfo(this.href, \'information\'); else return false;"><img src="image/niveau/'.$image.'.png" onmouseover="return overlib(\''.$overlib.'\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();" alt="cadre"></a>';
			//print_r($row);
			if($row_l['tot'] > 1)
			{
				if($i > 0)
				{
					$case1 = $texte;
				}
				else
				{
					$case3 = $texte;
				}
			}
			else
			{
				$case2 = $texte;
				$requete = "SELECT COUNT(*) FROM bonus_permet WHERE id_bonus = ".$row['id_bonus'];
				$req_bp = $db->query($requete);
				$row_bp = $db->read_row($req_bp);
				if($row_bp[0] > 1 AND array_key_exists($row['id_bonus'], $bonus))
				{
					//print_r($row_bp);
					$case1 = '<img src="image/coin_hg.png">';
					$case3 = '<img src="image/coin_hd.png">';
				}
				if($bn_num_rows > 1)
				{
					//print_r($row_bp);
					$case1 = '<img src="image/coin_bg.png">';
					$case3 = '<img src="image/coin_bd.png">';
				}
			}
		}
		$i++;
	}
	echo '
		<td style="text-align : center; width:33%;">
			'.$case1.'
		</td>
		<td style="text-align : center; width:33%;">
			'.$case2.'
		</td>
		<td style="text-align : center; width:33%;">
			'.$case3.'
		</td>
	</tr>';
}
?>
</table>
</div>
</fieldset>