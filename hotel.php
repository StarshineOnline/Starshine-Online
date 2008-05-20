<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$mois = 60 * 60 * 24 * 31;

if($W_distance == 0)
{
	?>
	<h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'hotel.php?poscase='.$W_case.'\', \'carte\')">';?> Hotel des ventes </a></h2>
	<?php include('ville_bas.php');?>

	<div class="ville_test">
	<?php
	if(isset($_GET['action']))
	{
		$message = '';
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				switch ($_GET['type'])
				{
					case 'arme' :
						$requete = "SELECT * FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$cout = $row['prix'];
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet($row['objet'], $joueur))
							{
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de l'argent au vendeur
								$requete = 'UPDATE perso SET star = star + '.$cout.' WHERE ID = '.sSQL($_GET['id_vendeur']);
								$db->query($requete);
								$requete = 'DELETE FROM hotel WHERE id = '.sSQL($_GET['id_vente']);
								$db->query($requete);
								echo '<h5>Arme achetée !</h5>';
								$message = nom_objet($row['objet']);
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'armure' :
						$requete = "SELECT * FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$cout = $row['prix'];
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet($row['objet'], $joueur))
							{
								$joueur = recupperso($joueur['ID']);
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de l'argent au vendeur
								$requete = 'UPDATE perso SET star = star + '.$cout.' WHERE ID = '.sSQL($_GET['id_vendeur']);
								$db->query($requete);
								//Effacement de l'objet
								$requete = 'DELETE FROM hotel WHERE id = '.sSQL($_GET['id_vente']);
								$db->query($requete);
								echo '<h5>Armure achetée !</h5>';
								$message = nom_objet($row['objet']);
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'accessoire' :
						$requete = "SELECT * FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$cout = $row['prix'];
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet($row['objet'], $joueur))
							{
								$joueur = recupperso($joueur['ID']);
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de l'argent au vendeur
								$requete = 'UPDATE perso SET star = star + '.$cout.' WHERE ID = '.sSQL($_GET['id_vendeur']);
								$db->query($requete);
								//Effacement de l'objet
								$requete = 'DELETE FROM hotel WHERE id = '.sSQL($_GET['id_vente']);
								$db->query($requete);
								echo '<h5>Accessoire acheté !</h5>';
								$message = nom_objet($row['objet']);
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'objet' :
						$requete = "SELECT * FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$cout = $row['prix'];
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet($row['objet'], $joueur))
							{
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de l'argent au vendeur
								$requete = 'UPDATE perso SET star = star + '.$cout.' WHERE ID = '.sSQL($_GET['id_vendeur']);
								$db->query($requete);
								//Effacement de l'objet
								$requete = 'DELETE FROM hotel WHERE id = '.sSQL($_GET['id_vente']);
								$db->query($requete);
								echo '<h5>Objet achetée !<h5>';
								$message = nom_objet($row['objet']);
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'gemme' :
						$requete = "SELECT * FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$cout = $row['prix'];
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet($row['objet'], $joueur))
							{
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de l'argent au vendeur
								$requete = 'UPDATE perso SET star = star + '.$cout.' WHERE ID = '.sSQL($_GET['id_vendeur']);
								$db->query($requete);
								//Effacement de l'objet
								$requete = 'DELETE FROM hotel WHERE id = '.sSQL($_GET['id_vente']);
								$db->query($requete);
								echo '<h5>Gemme achetée !</h5>>';
								$message = nom_objet($row['objet']);
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
				}
			break;
			//Suppression d'un objet
			case 'suppr' :
				//Vérification qu'il sagit bien de son objet
				$requete = "SELECT id_vendeur, objet FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				if($row['id_vendeur'] == $joueur['ID'])
				{
					//Suppression de l'objet de l'hotel des vente
					if(prend_objet($row['objet'], $joueur))
					{
						$requete = "DELETE FROM hotel WHERE id = ".sSQL($_GET['id_vente']);
						$db->query($requete);
						echo '<h6>votre objet a bien été supprimé de l\'hotel des ventes.</h6>';
					}
				}
				else
				{
					echo '<h5>Ce n\'est pas votre objet !</h5>';
				}
			break;			
		}
		if($message != '')
		{
			//Insertion de la vente dans le journal du vendeur
			$requete = "INSERT INTO journal VALUES('', ".$_GET['id_vendeur'].", 'vend', '', '', NOW(), '".$message."', '".$cout."', 0, 0)";
			$db->query($requete);
		}
	}
	if(array_key_exists('type', $_GET)) $type = $_GET['type']; else $type = 'arme';
	switch($type)
	{
		case 'arme' :
			$abbr = 'a';
		break;
		case 'armure' :
			$abbr = 'p';
		break;
		case 'objet' :
			$abbr = 'o';
		break;
		case 'gemme' :
			$abbr = 'g';
		break;
		case 'accessoire' :
			$abbr = 'm';
		break;
	}
	$url = "<a href=\"javascript:envoiInfo('hotel.php?poscase=$W_case&amp;type=";
	$urlfin = "', 'carte');\">";
	echo '
	<div class="ville_haut">
		<ul class="onglet-hotel">
			<li>
				'.$url.'arme'.$urlfin.'Armes</a>
			</li>
			<li>
				'.$url.'armure'.$urlfin.'Armures</a>
			</li>
			<li>
				'.$url.'accessoire'.$urlfin.'Accessoires</a>
			</li>
			<li>
				'.$url.'objet'.$urlfin.'Objets</a>
			</li>
			<li>
				'.$url.'gemme'.$urlfin.'Gemmes</a>
			</li>
			<li>
				'.$url.'moi'.$urlfin.'Mes objets</a>
			</li>
		</ul>
	</div>';
	//Récupère tout les royaumes qui peuvent avoir des items en commun
	$requete = "SELECT * FROM diplomatie WHERE race = '".$R['race']."'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$races = array();
	$keys = array_keys($row);
	$i = 0;
	$count = count($row);
	while($i < $count)
	{
		if((($row[$keys[$i]] <= 5) OR ($row[$keys[$i]] == 127)) && ($keys[$i] != 'race')) $races[] = "'".$keys[$i]."'";
		$i++;
	}
	$races = implode(',', $races);
	
	if(array_key_exists('tri_champ', $_GET))
	{
		if(array_key_exists('tri_champ', $_SESSION) AND array_key_exists($_GET['tri_champ'], $_SESSION['tri_champ']))
		{
			if($_SESSION['tri_champ'][$_GET['tri_champ']]['sens'] == 'DESC') $sens = 'ASC';
			else $sens = 'DESC';
		}
		else $sens = 'ASC';
		$_SESSION['tri_champ'][$_GET['tri_champ']] = array('tri_champ' => $_GET['tri_champ'], 'sens' => $sens);
	}
	//On recompose le tri
	if(array_key_exists('tri_champ', $_SESSION))
	{
		$tri_champ = '';
		$check = false;
		foreach($_SESSION['tri_champ'] as $ordre)
		{
			if($check) $tri_champ .= ', ';
			$tri_champ .= $ordre['tri_champ'].' '.$ordre['sens'];
			$check = true;
		}
	}
	else $tri_champ = "objet ASC, prix ASC";
	//Recherche tous les objets correspondants à ces races
	if($type == 'moi')
	{
		$requete = "SELECT * FROM hotel WHERE id_vendeur = ".$joueur['ID']." ORDER BY ".$tri_champ;
	}
	else
	{
		$requete = "SELECT * FROM hotel WHERE race IN (".$races.") AND SUBSTRING(objet FROM 1 FOR 1) = '".$abbr."' AND time > ".(time() - $mois)." ORDER BY ".$tri_champ;
	}
	//echo $requete;
	$req = $db->query($requete);
	$objets = array();
	$ids = array();
	$ids2 = array();
	$objets2 = array();
	$tri = array();
	while($row = $db->read_assoc($req))
	{
		$objet_d = decompose_objet($row['objet']);
		$categorie = $objet_d['categorie'];
		$id_o = $objet_d['id_objet'];
		//Recherche des infos des objets a afficher
		$requete = "SELECT * FROM ".$objet_d['table_categorie']." WHERE id = ".$id_o;
		$req_o = $db->query($requete);
		$row_o = $db->read_assoc($req_o);
		if(!in_array($row_o['type'], $tri)) $tri[] = $row_o['type'];
		//echo $_GET['tri'].' '.$row_o['type'].'<br />';
		if($_GET['tri'] == '') $all = true; else $all = false;
		if((array_key_exists('tri', $_GET) AND $_GET['tri'] == $row_o['type']) OR $all)
		{
			$objets2[$row_o['id']] = $row_o;
			$objets[$row['id']] = $row;
			$ids[] = $id_o;
			$ids2[] = $row['id'];
		}
	}
	//echo '<pre>';
	//print_r($joueur['inventaire']);
	$rendu_final = '
	<table class="marchand" cellspacing="0px">
	<tr class="header trcolor2">
		<td>
			<a href="javascript:envoiInfo(\'hotel.php?poscase='.$W_case.'&amp;type='.$type.'&amp;tri_champ=objet\', \'carte\');">Nom</a>
		</td>
		<td>
			<a href="javascript:envoiInfo(\'hotel.php?poscase='.$W_case.'&amp;type='.$type.'&amp;tri_champ=prix\', \'carte\');">Prix</a>
		</td>
		<td>
			<a href="javascript:envoiInfo(\'hotel.php?poscase='.$W_case.'&amp;type='.$type.'&amp;tri_champ=time\', \'carte\');">Temps restant</a>
		</td>
		<td>
			Achat
		</td>
	</tr>';
	$i = 0;
	$color = 1;
	foreach($ids2 as $plop)
	{
		$row = $objets[$plop];
		$objet_d = decompose_objet($row['objet']);
		$id_objet = $objet_d['id_objet'];
		$objet = $objets2[$id_objet];
		/*echo '<pre>';
		print_r($objet_d);
		print_r($objet);
		print_r($row);*/
		$rendu_final .= '
	<tr class="element trcolor'.$color.'" onclick="javascript:envoiInfo(\'description_objet.php?id_objet='.$row['objet'].'\', \'info_objet\');">
		<td onmousemove="afficheInfo(\'info_'.$row['id'].'\', \'block\', event);" onmouseout="afficheInfo(\'info_'.$row['id'].'\', \'none\', event );">
			'.$objet['nom'];
		if($objet_d['stack'] > 1) $rendu_final .= ' X '.$objet_d['stack'];
		if($objet_d['slot'] > 0) $rendu_final .= '<br /><span class="xsmall">Slot niveau '.$objet_d['slot'].'</span>';
		if($objet_d['slot'] == '0') $rendu_final .= '<br /><span class="xsmall">Slot impossible</span>';
		if($objet_d['enchantement'] > '0')
		{
			$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['enchantement'];
			$req = $db->query($requete);
			$row_e = $db->read_assoc($req);
			$rendu_final .= '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
		}
		$rendu_final .= '
		</td>
		<td>
			'.$row['prix'].'
		</td>
		<td>
			'.transform_sec_temp(($row['time'] + $mois) - time()).'
		</td>
		<td>
			';
			if($type == 'moi')
			{
				$rendu_final .= '<a href="javascript:envoiInfo(\'hotel.php?action=suppr&amp;id_vente='.$row['id'].'&amp;poscase='.$_GET['poscase'].'\', \'carte\')">Supprimer de l\'hotel des ventes</a>';
			}
			else
			{
				$rendu_final .= '<a href="javascript:envoiInfo(\'hotel.php?action=achat&amp;type='.$type.'&partie='.$objet['type'].'&amp;id='.$id_objet.'&amp;id_vente='.$row['id'].'&amp;id_vendeur='.$row['id_vendeur'].'&amp;poscase='.$_GET['poscase'].'\', \'carte\')"><span class="achat">Achat</span></a>';
			}
			$rendu_final .= '
		</td>
	</tr>
		<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 300px; padding: 5px;" id="info_'.$row['id'].'">';
			switch($type)
			{
				case 'arme' :
					$rendu_final .= 'Arme équipée : '.$joueur['arme_nom'].' - '.$joueur['arme_type'].' - Dégats '.$joueur['arme_degat'];
				break;
				case 'armure' :
					//echo $joueur['inventaire']->$objet['type'];
					if($joueur['inventaire']->$objet['type'] != '' AND $joueur['inventaire']->$objet['type'] !== 0)
					{
						$obj = decompose_objet($joueur['inventaire']->$objet['type']);
						$requete = "SELECT * FROM armure WHERE id = ".$obj['id_objet'];
						$req_armure = $db->query($requete);
						$row_armure = $db->read_assoc($req_armure);
						$rendu_final .= 'Armure équipée : '.$row_armure['nom'].' - '.$row_armure['type'].' - PP = '.$row_armure['PP'];
					}
					else $rendu_final .= 'Armure équipée : Aucune';
				break;
				case 'objet' :
					$rendu_final .= description($objets2[$id_objet]['description'], $objets2[$id_objet]);
				break;
			}
		$rendu_final .= '
		</div>';
			if($color == 1) $color = 2; else $color = 1;
		$i++;
	}
	$rendu_final .= '
	</table>';
	echo '
	<select id="tri" onchange="javascript:envoiInfo(\'hotel.php?poscase='.$W_case.'&amp;type='.$type.'&amp;tri=\' + document.getElementById(\'tri\').value, \'carte\');">
		<option value=""></option>';
	foreach($tri as $t)
	{
		echo '<option value="'.$t.'">'.$t.'</option>';
	}
	?>
	</select>
	<div id="info_objet" style="border : 1px solid black;  width : 150px; margin : auto;">
	</div>
	<div>
	<?php
	echo $rendu_final;
	?>
	</div>

	</div>

<?php
}
?>