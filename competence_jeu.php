<?php
include ('livre.php');
$tab_sort_jeu = explode(';', $joueur['comp_jeu']);
?>
<hr>
<?php
if($joueur['groupe'] != 0) $groupe_joueur = recupgroupe($joueur['groupe'], ''); else $groupe_joueur = false;
if (isset($_GET['ID']))
{
	$requete = "SELECT * FROM comp_jeu WHERE id = ".sSQL($_GET['ID']);
	//echo $requete;
	$req = $db->query($requete);

	$row = $db->read_array($req);
	if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes') $groupe = true; else $groupe = false;
	$sortpa = round($row['pa']);
	$sortmp = round($row['mp']);
	$action = false;
	$cibles = array($joueur['ID']);
	if($joueur['pa'] < $sortpa)
	{
		echo '<h5>Pas assez de PA</h5>';
	}
	elseif($joueur['mp'] < $sortmp)
	{
		echo '<h5>Pas assez de mana</h5>';
	}
	else
	{
		switch($row['type'])
		{
			case 'buff_forteresse' : case 'buff_position' : case 'rapide_vent' : case 'renouveau_energetique' : case 'longue_portee' : case 'fleche_tranchante' : case 'oeil_chasseur' : case 'renouveau_energique' :
				foreach($cibles as $cible)
				{
					$cible_s = recupperso($cible);
					//Mis en place du buff
					if(lance_buff($row['type'], $cible_s['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible_s['buff']), $cible_s['rang_grade']))
					{
						$action = true;
						echo $cible_s['nom'].' a bien reçu le buff<br />';
					}
					else
					{
						if($G_erreur == 'puissant') echo $cibles_s.' bénéficie d\'un buff plus puissant<br />';
						else echo $cible_s['nom'].' a trop de buff<br />';
					}
				}
				if($action)
				{
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
					//Insertion du buff dans le journal du lanceur
					$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'buff', '".$joueur['nom']."', '".$cible_s['nom']."', NOW(), '".$row['nom']."', 0, 0, 0)";
					$db->query($requete);
				}
			break;
			case 'buff_cri_bataille' : case 'buff_cri_victoire' : case 'buff_cri_rage' : case 'buff_cri_detresse' : case 'buff_cri_protecteur' : case 'preparation_camp' : case 'fouille_gibier' : case 'recherche_precieux' :
				if($groupe_joueur)
				{
					$cibles = array();
					foreach($groupe_joueur['membre'] as $membre)
					{
						//On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
						if($membre['distance'] <= 7) $cibles[] = $membre['id_joueur'];
					}
				}
				else
				{
					$cibles = array($joueur['ID']);
				}
				foreach($cibles as $cible)
				{
					$cible_s = recupperso($cible);
					if($row['type'] == 'preparation_camp') $row['effet2'] = time();
					//Mis en place du buff
					if(lance_buff($row['type'], $cible_s['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible_s['buff']), $cible_s['rang_grade']))
					{
						$action = true;
						echo $cible_s['nom'].' a bien reçu le buff<br />';
						//Insertion du buff dans le journal du receveur
						$requete = "INSERT INTO journal VALUES('', ".$cible_s['ID'].", 'rgbuff', '".$cible_s['nom']."', '".$joueur['nom']."', NOW(), '".$row['nom']."', 0, 0, 0)";
						$db->query($requete);
					}
					else
					{
						if($G_erreur == 'puissant') echo $cibles_s.' bénéficie d\'un buff plus puissant<br />';
						else echo $cible_s['nom'].' a trop de buffs.<br />';
					}
				}
				if($action)
				{
					$joueur['pa'] = $joueur['pa'] - $sortpa;
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
					//Insertion du buff dans le journal du lanceur
					$requete = "INSERT INTO journal VALUES('', ".$joueur['ID'].", 'gbuff', '".$joueur['nom']."', '".$cible_s['nom']."', NOW(), '".$row['nom']."', 0, 0, 0)";
					$db->query($requete);
				}
			break;
			case 'repos_interieur' :
				$joueur['pa'] += 2;
				$joueur['mp'] = $joueur['mp'] - $sortmp;
				//Mis à jour du joueur
				$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."' WHERE ID = '".$_SESSION['ID']."'";
				$req = $db->query($requete);
				echo '<a href="javascript:envoiInfo(\'competence_jeu.php?ID='.$_GET['ID'].'\', \'information\')">Utilisez a nouveau cette compétence</a>';
			break;
		}
	}
	echo '<br /><a href="javascript:envoiInfo(\'competence_jeu.php\', \'information\');">Revenir au livre des compétences</a>';
}
else
{
	$i = 0;
	$type = '';
	$magies = array();
	$magie = '';
	$requete = "SELECT * FROM comp_jeu GROUP BY comp_assoc";
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
		echo '<a href="javascript:envoiInfo(\'competence_jeu.php?tri='.$magie.'\', \'information\');"><img src="image/'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'"/></a> ';
	}
	if ('champion' == $joueur['classe'] AND !array_key_exists('tri', $_GET))
	{
		$where = "WHERE comp_assoc = 'melee'";
	}
	else
	{
		$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
	}
	$requete = "SELECT * FROM comp_jeu ".$where." ORDER BY comp_assoc ASC, type ASC";
	$req = $db->query($requete);


	$magie = '';
	echo '<table width="97%" class="information_case">';

	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
		}
		if(in_array($row['id'], $tab_sort_jeu))
		{
			echo '<div style="z-index: 3;">';
			$href = 'javascript:envoiInfo(\'competence_jeu.php?ID='.$row['id'].'\', \'information\')';
			$cursor = 'cursor : pointer;';
			$color = '#444';
			echo '
			<tr>
				<td>
					<span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href.'" onmousemove="afficheInfo(\'info_'.$i.'\', \'block\', event, \'xmlhttprequest\');" onmouseout="afficheInfo(\'info_'.$i.'\', \'none\', event );"> <strong>'.$row['nom'].'</strong></span>';
					?>
					<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
					<?php
					echo description($row['description'], $row);
					?>
					</div>
				</td>
				<td>
					<span class="xsmall">(<?php echo $row['mp']; ?> MP - <?php echo $row['pa']; ?> PA)</span>
				</td>
			</tr>
			</div>
			<?php
			$i++;
		}
	}
	echo '</table>';
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
