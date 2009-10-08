<?php
if (file_exists('root.php'))
  include_once('root.php');

include ('livre.php');
$tab_sort_jeu = explode(';', $joueur->get_comp_jeu());
?>
<hr>
<?php
if($joueur->get_groupe() != 0) $groupe_joueur = new groupe($joueur->get_groupe()); else $groupe_joueur = false;
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
	$cibles = array($joueur->get_id());
	if($joueur->get_pa() < $sortpa)
	{
		echo '<h5>Pas assez de PA</h5>';
	}
	elseif($joueur->get_mp() < $sortmp)
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
					$cible_s = new perso($cible);
					//Mis en place du buff
					if(lance_buff($row['type'], $cible_s->get_id(), $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible_s->get_buff()), $cible_s->get_grade()->get_rang()))
					{
						$action = true;
						echo $cible_s->get_nom().' a bien reçu le buff<br />';
					}
					else
					{
						if($G_erreur == 'puissant') echo $cibles_s.' bénéficie d\'un buff plus puissant<br />';
						else echo $cible_s->get_nom().' a trop de buff<br />';
					}
				}
				if($action)
				{
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					$joueur->sauver();
					//Insertion du buff dans le journal du lanceur
					$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'buff', '".$joueur->get_nom()."', '".$cible_s->get_nom()."', NOW(), '".$row['nom']."', 0, 0, 0)";
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
					foreach($groupe_joueur->get_membre() as $membre)
					{
						//On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
						if($membre->get_distance_pytagore($joueur) <= 7) $cibles[] = $membre->get_id_joueur();
					}
				}
				else
				{
					$cibles = array($joueur->get_id());
				}
				foreach($cibles as $cible)
				{
					$cible_s = new perso($cible);
					if($row['type'] == 'preparation_camp') $row['effet2'] = time();
					//Mis en place du buff
					if(lance_buff($row['type'], $cible_s->get_id(), $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, count($cible_s->get_buff()), $cible_s->get_grade()->get_rang()))
					{
						$action = true;
						echo $cible_s->get_nom().' a bien reçu le buff<br />';
						//Insertion du buff dans le journal du receveur
						$requete = "INSERT INTO journal VALUES('', ".$cible_s->get_id().", 'rgbuff', '".$cible_s->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$row['nom']."', 0, 0, 0)";
						$db->query($requete);
					}
					else
					{
						if($G_erreur == 'puissant') echo $cibles_s.' bénéficie d\'un buff plus puissant<br />';
						else echo $cible_s->get_nom().' a trop de buffs.<br />';
					}
				}
				if($action)
				{
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					$joueur->sauver();
					//Mis à jour du joueur
					$requete = "UPDATE perso SET mp = '".$joueur->get_mp()."', pa = '".$joueur->get_pa()."' WHERE ID = '".$_SESSION['ID']."'";
					$req = $db->query($requete);
					//Insertion du buff dans le journal du lanceur
					$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'gbuff', '".$joueur->get_nom()."', '".$cible_s->get_nom()."', NOW(), '".$row['nom']."', 0, 0, 0)";
					$db->query($requete);
				}
			break;
			case 'repos_interieur' :
				if($joueur->is_buff('repos_interieur') AND $joueur->get_buff('repos_interieur', 'effet') >= 10)
				{
					echo 'Vous avez trop utilisé repos intérieur pour le moment !';
				}
				else
				{
					//echo '$joueur->get_buff(\'repos_interieur\', \'effet\') => '.$joueur->get_buff('repos_interieur', 'effet').'<br />';
					if($joueur->is_buff('repos_interieur')) $effet = $joueur->get_buff('repos_interieur', 'effet') + 1;
					else $effet = 1;
					//echo '$effet => '.$effet.'<br />';
					if(lance_buff('repos_interieur', $joueur->get_id(), $effet, 0, (60 * 60 * 24), $row['nom'], description($row['description'].'<br /> Utilisation '.$effet.' / 10', $row), 'perso', 1, 0, 0, 0))
					{
						echo 'Le buff a été envoyé<br />';
						$joueur->set_pa($joueur->get_pa() + 2);
						$joueur->set_mp($joueur->get_mp() - $sortmp);
						$joueur->sauver();
						echo '<a href="competence_jeu.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau cette compétence</a>';
					}
				}
			break;
			case "esprit_libre" :
					//-- Suppression d'un debuff au hasard
					$debuff_tab = array();
					foreach($joueur->get_buff() as $debuff)
					{
						if($debuff->get_debuff() == 1) 
						{ 
							$debuff_tab[] = $debuff->get_id(); 
						};
					}
					if(count($debuff_tab) > 0)
					{
						$joueur->set_pa($joueur->get_pa() - $sortpa);
						$joueur->set_mp($joueur->get_mp() - $sortmp);
					
						$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
					}
					else { echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucune debuff.<br/>"; };
						
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
	if ('champion' == $joueur->get_classe() AND !array_key_exists('tri', $_GET))
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
