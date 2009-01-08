<?php
include ('livre.php');
$tab_sort_jeu = explode(';', $joueur['comp_jeu']);
?>
<hr>
<?php
if($joueur['groupe'] != 0) $groupe_joueur = recupgroupe($joueur['groupe'], $joueur['x'].'-'.$joueur['y']); else $groupe_joueur = false;
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
			case 'buff_forteresse' : 
			case 'buff_position' : 
			case 'rapide_vent' : 
			case 'renouveau_energetique' : 
			case 'longue_portee' : 
			case 'fleche_tranchante' : 
			case 'oeil_chasseur' : 
			case 'renouveau_energique' :
			case 'bulle_dephasante' :
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
			case 'buff_cri_bataille' : 
			case 'buff_cri_victoire' : 
			case 'buff_cri_rage' : 
			case 'buff_cri_detresse' : 
			case 'buff_cri_protecteur' : 
			case 'preparation_camp' : 
			case 'fouille_gibier' : 
			case 'recherche_precieux' :
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
				if(array_key_exists('repos_interieur', $joueur['debuff']) AND $joueur['debuff']['repos_interieur']['effet'] >= 10)
				{
					echo 'Vous avez trop utilisé repos intérieur pour le moment !';
				}
				else
				{
					if(array_key_exists('repos_interieur', $joueur['debuff'])) $effet = $joueur['debuff']['repos_interieur']['effet'] + 1;
					else $effet = 1;
					$joueur['pa'] += 2;
					$joueur['mp'] = $joueur['mp'] - $sortmp;
					if(lance_buff('repos_interieur', $joueur['ID'], $effet, 0, (60 * 60 * 24), $row['nom'], description($row['description'].'<br /> Utilisation '.$effet.' / 10', $row), 'perso', 1, 0, 0))
					{
						//Mis à jour du joueur
						$requete = "UPDATE perso SET mp = '".$joueur['mp']."', pa = '".$joueur['pa']."' WHERE ID = '".$_SESSION['ID']."'";
						$req = $db->query($requete);
						echo '<a href="competence_jeu.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau cette compétence</a>';
					}
				}
			break;
			case "esprit_libre" :
					//-- Suppression d'un debuff au hasard
					$debuff_tab = array();
					foreach($joueur["debuff"] as $debuff)
					{
						if($debuff["type"] != "debuff_rez" AND $debuff["type"] != "repos_sage" AND $debuff["type"] != "repos_interieur") { $debuff_tab[count($debuff_tab)] = $debuff["id"]; };
					}
					if(count($debuff_tab) > 0)
					{
						$joueur["pa"] = $joueur["pa"] - $sortpa;
						$joueur["mp"] = $joueur["mp"] - $sortmp;
					
						$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
					}
					else { echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucune debuff.<br/>"; };
						
					//-- Mis à jour du joueur
					$requete = "UPDATE perso SET mp='".$joueur["mp"]."', pa='".$joueur["pa"]."' WHERE ID='".$_SESSION["ID"]."'";
					$req = $db->query($requete);
					echo '<a href="competence_jeu.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau cette compétence</a>';
					
			break;
		}
	}
	echo '<br /><a href="competence_jeu.php" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre des compétences</a>';
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
		echo '<a href="competence_jeu.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'"/></a> ';
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
			$href = 'return envoiInfo(\'competence_jeu.php?ID='.$row['id'].'\', \'information\')';
			$cursor = 'cursor : pointer;';
			$color = '#444';
			$echo = addslashes(description($row['description'], $row));
			echo '
			<tr>
				<td>
					<span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href.'; return nd();" onmouseover="return '.make_overlib($echo).'" onmouseout="return nd();"> <strong>'.$row['nom'].'</strong></span>';
					?>
				
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
