<?php //	 -*- tab-width:	 2 -*-

//Inclusion du haut du document html
include('haut_ajax.php');

// Inclusion du gestionnaire de compétences
include('fonction/competence.inc.php');

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
?><h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'ecolecombat.php?poscase='.$W_case.'\', \'carte\')">';?> Ecole de combat </a></h2>
<?php include('ville_bas.php');?>
<?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
$cout_app = 500;
if($W_distance == 0)
{
	//On recherche le niveau de la construction
	$batiment = 'ecole_combat';
	$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Si le batiment est inactif, on met le batiment au niveau 1, sinon c'est bon
	if($row['statut'] == 'inactif') $level_batiment = 1; else $level_batiment = $row['level'];
	if(isset($_GET['ecole']))
	{
		$ecole = $_GET['ecole'];
		if($ecole == 'comp_jeu')
		{
			$nom_autre_ecole = 'En combat';
			$autre_ecole = 'comp_combat';
		}
		else
		{
			$nom_autre_ecole = 'Hors combat';
			$autre_ecole = 'comp_jeu';
		}
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				//Achat
				case 'apprendre' :
					apprend_competence($ecole, sSQL($_GET['id']), $joueur, $R, false);
					break;
			}
		}
		
		if(!isset($_GET['order']) OR ($_GET['order'] == '')) $_GET['order'] = 'type,prix';
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
		Passer à une autre école de combat : <a href="javascript:envoiInfo('ecolecombat.php?ecole=<?php echo $autre_ecole; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><?php echo $nom_autre_ecole; ?></a>
		<br /><br />

		<?php
		//Affichage du magasin des armes
		$url2 = 'ecolecombat.php?ecole='.$_GET['ecole'].'&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
		?>
			<p class="ville_haut"><a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=melee', 'carte')">Mélée</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=distance', 'carte')">Distance</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=esquive', 'carte')">Esquive</a> | <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=blocage', 'carte')">Blocage</a></p>
			<table class="marchand" cellspacing="0px">
			<tr class="header trcolor2">
				<td>
					Nom
				</td>
				<td>
					MP
				</td>
				<td>
					Effet
				</td>
				<td>
					Compétence
				</td>
				<td>
					Arme
				</td>
				<td>
					Stars
				</td>
				<td>
					Apprendre
				</td>
			</tr>
			
			<?php
			
			$color = 1;
			$where = 'lvl_batiment <= '.$level_batiment;
			if(array_key_exists('part', $_GET))
			{
				$where .= " AND comp_assoc = '".sSQL($_GET['part'])."'";
			}
			$comps = explode(';', $joueur[$ecole]);
			$count = count($comps);
			$comps = implode(', ', $comps);
			if($comps != '' AND $count > 0) $where .= " AND id NOT IN (".$comps.") ";
			$requete = "SELECT * FROM ".$ecole." WHERE ".$where." ORDER BY".$ordre;
			//echo $requete;
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				if($row['requis'] != '999')
				{
					$taxe = ceil($row['prix'] * $R['taxe'] / 100);
					$cout = $row['prix'] + $taxe;
					$couleur = $color;
					if($row['carac_requis'] > $joueur[$row['carac_assoc']] OR $row['comp_requis'] > $joueur[$row['comp_assoc']] OR $cout > $joueur['star']) $couleur = 3;
					$row['cible2'] = $G_cibles[$row['cible']];
				?>
				<tr class="element trcolor<?php echo $couleur; ?>">
					<td onmouseover="return <?php echo make_overlib(description('[%cible2%] '.$row['description'], $row)); ?>;" onmouseout="return nd();">
						<?php echo $row['nom']; ?>
					</td>
					<td>
						<?php echo $row['mp']; ?>
					</td>
					<td>
						<?php echo $row['effet']; ?>
					</td>
					<td>
						<span class="<?php echo over_price($row['comp_requis'], $joueur[$row['comp_assoc']]); ?>"><?php echo $Gtrad[$row['comp_assoc']].' ('.$row['comp_requis'].')'; ?></span>
					</td>
					<td>
						<?php
						$arme_requis = explode(';', $row['arme_requis']);
						$arme_requis = implode(' - ', $arme_requis);
						echo $arme_requis;
						?>
					</td>
					<td>
						<span class="<?php echo over_price($cout, $joueur['star']); ?>"><?php echo $cout; ?></span>
					</td>
					<td>
					<?php
					if (over_price($cout, $joueur['star']) == 'achat_normal' AND over_price($row['comp_requis'], $joueur[$row['comp_assoc']]) == 'achat_normal')
					{	
					?>
						<a href="javascript:envoiInfo('ecolecombat.php?ecole=<?php echo $_GET['ecole']; ?>&amp;action=apprendre&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><span class="achat">Apprendre</span></a>
					<?php 
					}
					?>
					</td>
				</tr>
				<?php
					if($color == 1) $color = 2; else $color = 1;
				}
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
			$requete = "UPDATE perso SET star = ".$joueur['star'].", ".sSQL($_GET['app'])." = 1 WHERE ID = ".$joueur['ID'];
			if($db->query($requete)) echo 'L\'apprentissage de '.$Gtrad[$_GET['app']].' est un succès !<br />';
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars<br />';
		}
		?>
		<a href="javascript:envoiInfo('ecolecombat.php?poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Retour à l'école de combat</a>
		<?php
	}
	else
	{
			?>

		<?php
		//Affichage des quêtes
		$return = affiche_quetes('ecole_combat', $joueur);
		if($return[1] > 0)
		{
			echo '<div class="ville_test"><span class="texte_normal">';
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
			echo '</span></div><br />';
		}
		?>
		<div class="ville_test">
			<ul class="ville">
			<?php
			$taxe = ceil($cout_app * $R['taxe'] / 100);
			$cout = $cout_app + $taxe;
			?>
				<li>
					<a href="javascript:envoiInfo('ecolecombat.php?ecole=comp_jeu&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Compétences hors combat</a>
				</li>
				<li>
					<a href="javascript:envoiInfo('ecolecombat.php?ecole=comp_combat&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Compétences de combat</a>
				</li>
			</ul>
			</div>

			
			<?php
		}
}
?>
