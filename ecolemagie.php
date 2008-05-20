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

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
<h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'ecolemagie.php?poscase='.$W_case.'\', \'carte\')">';?> Ecole de Magie </a></h2>
		<?php include('ville_bas.php');?>
<?php
//if($_GET['ecole'] == 'sort_jeu') echo '<a href="javascript:envoiInfo(\'ecolemagie.php?ecole=sort_combat&amp;poscase='.$W_case.'\', \'carte\')">Ecole de magie en combat</a><br />'; elseif($_GET['ecole'] == 'sort_combat') echo '<a href="javascript:envoiInfo(\'ecolemagie.php?ecole=sort_jeu&amp;poscase='.$W_case.'\', \'carte\')">Ecole de magie hors combat</a><br />';
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
$cout_app = 500;
if($W_distance == 0)
{
	//On recherche le niveau de la construction
	$batiment = 'ecole_magie';
	$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Si le batiment est inactif, on met le batiment au niveau 1, sinon c'est bon
	if($row['statut'] == 'inactif') $level_batiment = 1; else $level_batiment = $row['level'];
	if(isset($_GET['ecole']))
	{
		$ecole = $_GET['ecole'];
		if($ecole == 'sort_combat')
		{
			$nom_autre_ecole = 'Hors combat';
			$autre_ecole = 'sort_jeu';
		}
		else
		{
			$nom_autre_ecole = 'En combat';
			$autre_ecole = 'sort_combat';
		}
		?><?php
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				//Achat
				case 'apprendre' :
					$requete = "SELECT * FROM ".$ecole." WHERE id = ".sSQL($_GET['id']);
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$taxe = ceil($row['prix'] * $R['taxe'] / 100);
					$cout = $row['prix'] + $taxe;
					if ($joueur['star'] >= $cout)
					{
						if($joueur['incantation'] >= ($row['incantation'] * $joueur['facteur_magie']))
						{
							if($joueur[$row['comp_assoc']] >= round($row['comp_requis'] * $joueur['facteur_magie'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10))))
							{
								$sort_jeu = explode(';', $joueur[$ecole]);
								if(!in_array($row['id'], $sort_jeu))
								{
									$joueur_sorts = explode(';', $joueur[$ecole]);
									if($row['requis'] == '' OR in_array($row['requis'], $joueur_sorts))
									{
										if($sort_jeu[0] == '') $sort_jeu = array();
										$sort_jeu[] = $row['id'];
										$joueur[$ecole] = implode(';', $sort_jeu);
										$joueur['star'] = $joueur['star'] - $cout;
										$requete = "UPDATE perso SET star = ".$joueur['star'].", ".$ecole." = '".$joueur[$ecole]."' WHERE ID = ".$_SESSION['ID'];
										$req = $db->query($requete);
										//Récupération de la taxe
										if($taxe > 0)
										{
											$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
											$db->query($requete);
											$requete = "UPDATE argent_royaume SET ecole_magie = ecole_magie + ".$taxe." WHERE race = '".$R['race']."'";
											$db->query($requete);
										}
										echo '<h6>Sort appris !</h6>';
									}
									else
									{
										echo '<h5>Vous devez connaitre un autre sort pour apprendre celui-ci</h5>';
									}
								}
							}
							else
							{
								echo '<h5>Vous n\'avez pas assez en '.$Gtrad[$row['comp_assoc']].'</h5>';
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez en incantation</h5>';
						}
					}
					else
					{
						echo '<h5>Vous n\'avez pas assez de Stars</h5>';
					}
				break;
			}
		}
		
		if(!isset($_GET['order']) OR ($_GET['order'] == '')) $_GET['order'] = 'incantation,type,nom,prix';
		$order = explode(',', $_GET['order']);
		$i = 0;
		$ordre = '';
		while($i < count($order))
		{
			if($i != 0) $ordre .= ',';
			$ordre .= ' '.$order[$i].' ASC';
			$i++;
		}
		//Affichage du menu de séléction et de tri
		$url = 'ecolemagie.php?ecole='.$_GET['ecole'].'&amp;type='.$_GET['type'].'&amp;poscase='.$W_case.'&amp;part='.$_GET['part'].'&amp;order=';
		?>
		<div class="ville_test">
		Passer à une autre école de magie : <a href="javascript:envoiInfo('ecolemagie.php?ecole=<?php echo $autre_ecole; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><?php echo $nom_autre_ecole; ?></a>
		<br /><br />
		<?php
		$url2 = 'ecolemagie.php?ecole='.$_GET['ecole'].'&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
		?>
			<p class="ville_haut"><a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=sort_vie', 'carte')">Vie</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=sort_element', 'carte')">Element</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=sort_mort', 'carte')">Nécromancie</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=all', 'carte')">Toutes</a></p>
			<table class="marchand" cellspacing="0px">
			<tr class="header trcolor2" style="font-size : 0.9em;">
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>nom,prix', 'carte')">Nom</a></strong>
				</td>
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>nom,prix', 'carte')">PA</a></strong>
				</td>
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>mp,nom,prix', 'carte')">MP</a></strong>
				</td>

				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>incantation,prix,nom', 'carte')">Incant.</strong>
				</td>
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>comp_requis,type,prix,nom', 'carte')">Comp.</strong>
				</td>
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url; ?>prix,type,nom', 'carte')">Stars</a></strong>
				</td>
				<td>
					<strong><a href="javascript:envoiInfo('<?php echo $url.$_GET['order']; ?>&amp;hide=yes', 'carte')">X</a></strong>
				</td>
			</tr>
			
			<?php
			
			$color = 1;
			$where = 'lvl_batiment <= '.$level_batiment;
			if(array_key_exists('part', $_GET) AND $_GET['part'] != 'all' AND $_GET['part'] != '')
			{
				$where .= " AND comp_assoc = '".sSQL($_GET['part'])."'";
			}
			else $_GET['part'] = 'all';
			$sortt_j = explode(';', $joueur[$ecole]);
			$sort_j = '('.implode(', ', $sortt_j).')';
			if(array_key_exists('hide', $_GET) AND $_GET['hide'] == 'yes') $where .= " AND id NOT IN ".$sort_j;
			$requete = "SELECT * FROM ".$ecole." WHERE ".$where." ORDER BY".$ordre;
			//echo $requete;
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				$taxe = ceil($row['prix'] * $R['taxe'] / 100);
				$cout = $row['prix'] + $taxe;
				$inc = ($row['incantation'] * $joueur['facteur_magie']);
				$comp = round($row['comp_requis'] * $joueur['facteur_magie'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
				//echo $row['pa'].' '.$joueur['facteur_magie'];
				$sortpa = ($row['pa'] * $joueur['facteur_magie']);
				$couleur = $color;
				if($comp > $joueur[$row['comp_assoc']] OR $cout > $joueur['star'] OR $inc > $joueur['incantation']) $couleur = 3;
				if(in_array($row['id'], $sortt_j)) $couleur = 5;
				$row['cible2'] = $G_cibles[$row['cible']];
				
			?>

			<tr class="element trcolor<?php echo $couleur; ?>">
				<td onmouseover="<?php echo make_overlib(addslashes(description('[%cible2%] '.$row['description'], $row))); ?>" onmouseout="return nd();">
					<?php echo $row['nom']; ?>
				</td>
				<td>
					<?php echo round($sortpa); ?>
				</td>
				<td>
					<?php echo round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10))); ?>
				</td>

				<td>
					<span class="<?php echo over_price($inc, $joueur['incantation']); ?>"><?php echo $inc; ?></span>
				</td>
				<td style="text-align : right;">
					<span class="<?php echo over_price($comp, $joueur[$row['comp_assoc']]); ?>"><strong><?php echo $comp; ?></strong> <img src="image/<?php echo $row['comp_assoc']; ?>.png" alt="<?php echo $Gtrad[$row['comp_assoc']]; ?>" title="<?php echo $Gtrad[$row['comp_assoc']]; ?>" style="vertical-align : middle;" /></span>
				</td>
				<td>
					<span class="<?php echo over_price($cout, $joueur['star']); ?>"><?php echo $cout; ?></span>
				</td>
				<td style="width : 50px;">
				<?php 
				if (over_price($cout, $joueur['star']) == 'achat_normal' AND over_price($comp, $joueur[$row['comp_assoc']]) == 'achat_normal' AND over_price($inc, $joueur['incantation']) == 'achat_normal' AND $couleur != 5)
				{
				?>	
				<a href="javascript:envoiInfo('ecolemagie.php?ecole=<?php echo $_GET['ecole']; ?>&amp;action=apprendre&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><span class="achat">Achat</span></a>
				<?php
				}
				if ($couleur == 5)
				{
				echo 'Connu';	
				}
				?>
				</td>
			</tr>
			<?php


			}
			?>
			</table>
			</div>

	<?php
	}
	elseif(array_key_exists('app', $_GET))
	{
		$taxe = ceil($cout_app * $R['taxe'] / 100);
		$cout = $cout_app + $taxe;
		if($joueur['star'] >= $cout)
		{
			$joueur['star'] -= $cout;
			$requete = "UPDATE perso SET star = ".$joueur['star'].", ".sSQL($_GET['app'])." = 3 WHERE ID = ".$joueur['ID'];
			if($db->query($requete)) echo 'L\'apprentissage de '.$Gtrad[$_GET['app']].' est un succès !<br />';
			if($taxe > 0)
			{
				$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
				$db->query($requete);
			}
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars<br />';
		}
		?>
		<a href="javascript:envoiInfo('ecolemagie.php?poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Retour à l'école de magie</a>
		<?php
	}
	else
	{
		?>


		<div class="ville_test">
			<ul class="ville">
			<?php
			$taxe = ceil($cout_app * $R['taxe'] / 100);
			$cout = $cout_app + $taxe;
			if($joueur['sort_vie'] == 0)
			{
				?>
				<li>
					<a href="javascript:envoiInfo('ecolemagie.php?app=sort_vie&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Apprendre la magie de la vie (coût <?php echo $cout; ?> stars)</a>
				</li>
				<?php
			}
			if($joueur['sort_element'] == 0)
			{
				?>
				<li>
					<a href="javascript:envoiInfo('ecolemagie.php?app=sort_element&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Apprendre la magie élémentaire (coût <?php echo $cout; ?> stars)</a>
				</li>
				<?php
			}
			if($joueur['sort_mort'] == 0)
			{
				?>
				<li>
					<a href="javascript:envoiInfo('ecolemagie.php?app=sort_mort&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Apprendre la magie de la mort (coût <?php echo $cout; ?> stars)</a>
				</li>
				<?php
			}
			?>
				<li>
					<a href="javascript:envoiInfo('ecolemagie.php?ecole=sort_jeu&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Sorts hors combat</a>
				</li>
				<li>
					<a href="javascript:envoiInfo('ecolemagie.php?ecole=sort_combat&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Sorts de combat</a>
				</li>
			</ul>
			</div>

			<?php
		}
}
refresh_perso();
?>