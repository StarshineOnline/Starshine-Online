<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
$tab_sort_jeu = explode(';', $joueur['sort_jeu']);
$W_case = $_GET['poscase'];
$W_distance = detection_distance($W_case, $_SESSION["position"]);
?>
<h2>Livre de Sorts</h2>
<?php
if (isset($_GET['ID']))
{
	$requete = "SELECT * FROM sort_jeu WHERE id = ".sSQL($_GET['ID']);
	$req = $db->query($requete);
	$row = $db->read_array($req);
	
	if($W_distance > $row['portee'])
	{
		echo 'Vous êtes trop loin pour lancer ce sort !';
	}
	else
	{
		$sortpa_base = $row['pa'];
		$sortmp_base = $row['mp'];
		$sortpa = round($row['pa'] * $joueur['facteur_magie']);
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur['buff'])) $sortmp = ceil($sortmp * (1 - ($joueur['buff']['buff_concentration']['effet'] / 100)));
		if($joueur['pa'] < $sortpa)
		{
			echo 'Pas assez de PA';
		}
		elseif($joueur['mp'] < $sortmp)
		{
			echo 'Pas assez de mana';
		}
		elseif($joueur['hp'] <= 0)
		{
			echo 'Vous êtes mort';
		}
		else
		{
			switch($row['type'])
			{
				case 'debuff_aveuglement' : case 'debuff_desespoir' : case 'debuff_enracinement' : case 'debuff_desespoir' : case 'debuff_ralentissement' :
					$cible = recupmonstre($_GET['id_monstre']);
					//Test d'esquive du sort
					$attaque = rand(0, ($joueur['volonte'] * ($joueur[$row['comp_assoc']] + $joueur['incantation'])));
					$defense = rand(0, ($cible['volonte'] * $cible['PM'] / 3));
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					if ($attaque > $defense)
					{
						//Mis en place du debuff
						if(lance_buff($row['type'], $_GET['id_monstre'], $row['effet'], $row['effet2'], ($row['duree'] * 4), $row['nom'], description($row['description'], $row), 'monstre', 1, 0, 0))
						{
							echo 'Le sort '.$row['nom'].' a été lancé avec succès<br />';
						}
						else
						{
							echo 'Il bénéficit d\'un débuff plus puissant<br />';
						}
					}
					else
					{
						echo 'Le '.$cible['nom'].' resiste a votre sort !<br />';
				 	}
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur['incantation'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur['incantation'].' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant a '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."', incantation = '".$joueur['incantation']."', ".$row['comp_assoc']." = '".$joueur[$row['comp_assoc']]."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
				break;
			}
		}
	}
	echo '<br /><a href="javascript:envoiInfo(\'sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$_GET['id_monstre'].'\', \'information\');">Revenir au livre de sort</a>';
}
else
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris VALUES('', ".sSQL($_GET['id']).", ".$joueur['ID'].")";
				$db->query($requete);
			break;
			case 'delfavoris' :
				$requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur['ID'];
				$db->query($requete);
			break;
		}
	}
	$i = 0;
	$type = '';
	$magies = array('favoris');
	$magie = '';
	$requete = "SELECT * FROM sort_jeu GROUP BY comp_assoc";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$magies[] = $row['comp_assoc'];
		}
	}
	foreach($magies as $magie)
	{
		echo '<a href="javascript:envoiInfo(\'sort_monstre.php?poscase='.$W_case.'&amp;tri='.$magie.'&amp;id_monstre='.$_GET['id_monstre'].'\', \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
	}
	if(array_key_exists('tri', $_GET)) $where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\''; else $_GET['tri'] = 'favoris';
	if($_GET['tri'] == 'favoris')
	{
		$requete = "SELECT * FROM sort_jeu WHERE id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = ".$joueur['ID'].") AND cible = 4";
	}
	else
	{
		$requete = "SELECT * FROM sort_jeu ".$where." AND cible = 4 ORDER BY comp_assoc ASC, type ASC";
	}
	$req = $db->query($requete);
	$magie = '';
	while($row = $db->read_array($req))
	{
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		$sortpa = round($row['pa'] * $joueur['facteur_magie']);
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur['buff'])) $sortmp = ceil($sortmp * (1 - ($joueur['buff']['buff_concentration']['effet'] / 100)));
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			echo '<h2>'.$Gtrad[$magie].'</h2>';
		}
		if(in_array($row['id'], $tab_sort_jeu))
		{
			if($Gtrad[$row['type']] != $Gtrad[$type])
			{
				$type = $row['type'];
				echo '<h3>'.$Gtrad[$row['type']].'</h3>';
			}
			$image = image_sort($row['type']);
			?>
			<div style="z-index: 3;">
			<tr>
			<?php
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			if($row['cible'] == 4)
			{
				$href = 'javascript:envoiInfo(\'sort_monstre.php?poscase='.$W_case.'&amp;ID='.$row['id'].'&amp;id_monstre='.$_GET['id_monstre'].'\', \'information\')';
				$color = 'blue';
				$cursor = 'cursor : pointer;';
			}
			else
			{
				$href = '';
				$cursor = '';
				$color = 'black';
			}
			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>" onmousemove="afficheInfo('info_<?php echo $i; ?>', 'block', event, 'centre');" onmouseout="afficheInfo('info_<?php echo $i; ?>', 'none', event );"><?php echo $row['nom']; ?></span>
				<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
				<?php
				echo description($row['description'], $row).'<br /><span class="xmall">Incantation : '.$incanta.'</span>';
				?>
				</div>
			</td>
			<?php
			echo '
			<td>
				<span class="xsmall"> '.$sortpa.' PA 
			</td>
			<td>
				'.$sortmp.' MP
			</td> 
			<td>';
			?>
			</div>
			<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
			<?php
			echo description($row['description'], $row);
			if($_GET['tri'] == 'favoris') echo ' <td><a href="javascript:envoiInfo(\'sort_monstre.php?action=delfavoris&amp;id='.$row['id'].'\', \'information\')"><img src="image/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="javascript:envoiInfo(\'sort_monstre.php?action=favoris&amp;id='.$row['id'].'\', \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}
	}
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />