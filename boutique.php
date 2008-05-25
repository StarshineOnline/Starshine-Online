<?php
//Connexion obligatoire
$connexion = true;
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

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
		?>
		<h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\',\'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'boutique.php?poscase='.$W_case.'\',\'carte\')">';?> Marchand d'<?php echo $_GET['type']; ?>s </a></h2>
		<?php
		if($_GET['type'] == 'armure')
		{
			$url = 'boutique.php?type=arme&amp;poscase='.$W_case.'&amp;order=';
			$batiment = 'armurerie';

		}
		else
		{
			$url = 'boutique.php?type=armure&amp;poscase='.$W_case.'&amp;order=';
			$batiment = 'forgeron';
		}
		?>
<?php

$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	//On recherche le niveau de la construction
	$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Si le batiment est inactif, on met le batiment au niveau 1, sinon c'est bon
	?>
	<?php include('ville_bas.php');?>
	<div class="ville_test">
	<?php
	if($row['statut'] == 'inactif') $level_batiment = 1; else $level_batiment = $row['level'];
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :

				switch ($_GET['type'])
				{
					case 'arme' :
						$requete = "SELECT id, prix FROM arme WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R['taxe'] / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet('a'.$row['id'], $joueur))
							{
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$_SESSION['ID'];
								$req = $db->query($requete);
								//Récupération de la taxe
								if($taxe > 0)
								{
									$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
									$db->query($requete);
									$requete = "UPDATE argent_royaume SET forgeron = forgeron + ".$taxe." WHERE race = '".$R['race']."'";
									$db->query($requete);
								}
								echo '<h6>Arme achetée !</h6>
								<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
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
						$requete = "SELECT id, prix, type FROM armure WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R['taxe'] / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur['star'] >= $cout)
						{
							if(prend_objet('p'.$row['id'], $joueur))
							{
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$_SESSION['ID'];
								$req = $db->query($requete);
								//Récupération de la taxe
								if($taxe > 0)
								{
									$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
									$db->query($requete);
									$requete = "UPDATE argent_royaume SET armurerie = armurerie + ".$taxe." WHERE race = '".$R['race']."'";
									$db->query($requete);
								}
								echo '<h6>Armure achetée !</h6>
								<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
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
	$url = 'boutique.php?type='.$_GET['type'].'&amp;poscase='.$W_case.'&amp;order=';
	?>
	
	Trier par :	<a href="javascript:envoiInfo('<?php echo $url; ?>prix', 'carte')">Prix</a> :: <a href="javascript:envoiInfo('<?php echo $url; ?>type', 'carte')">Type</a> :: <a href="javascript:envoiInfo('<?php echo $url; if($_GET['type'] == 'arme') echo 'degat'; else echo 'pp'; ?>', 'carte')">Effets</a> :: <a href="javascript:envoiInfo('<?php echo $url; ?>forcex', 'carte')">Force</a><br />
	<br />
	
	<?php
	//Affichage du magasin des armes
	if($_GET['type'] == 'arme')
	{
		if(!array_key_exists('part', $_GET)) $_GET['part'] = 'arc';
		$types = array();
		$types['arc'][0][] = 'Portée';
		$types['arc'][0][] = 'distance_tir';
		$types['baton'][0][] = 'Bonus Cast';
		$types['baton'][0][] = 'var1';
		$types['autre'] = array();
		if($_GET['part'] == 'arc') $type = 'arc';
		elseif($_GET['part'] == 'baton') $type = 'baton';
		else $type = 'autre';
		$url2 = 'boutique.php?type=arme&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
	?>

		<p class="ville_haut"><a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=arc', 'carte')">Arc</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=dague', 'carte')">Dague</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=epee', 'carte')">Epée</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=hache', 'carte')">Hache</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=bouclier', 'carte')">Bouclier</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=baton', 'carte')">Baton</a></p>

		
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
			</td>
			<td>
				Type
			</td>
			<td>
				Mains
			</td>
			<td>
				Dégats
			</td>
			<td>
				<span onClick="return nd();" onmouseover="return <?php echo make_overlib('Coéf Arc = '.$joueur['coef_distance'].'<br />Coéf Mélée = '.$joueur['coef_melee'].'<br />Coéf Incantation = '.$joueur['coef_incantation']); ?>" onmouseout="return nd();">Coéf.</span>
			</td>
	<?php
	foreach($types[$type] as $typ)
	{
		echo '
			<td>
				'.$typ[0].'
			</td>';
	}
	?>
			<td>
				Stars
			</td>
			<td>
				Achat
			</td>
		</tr>
		
		<?php
		
		$color = 1;
		$where = 'lvl_batiment <= '.$level_batiment;
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM arme WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			$mains = explode(';', $row['mains']);
			$main = $mains[0];
			if($row['type'] == 'arc') $skill = 'distance';
			elseif($row['type'] == 'baton') $skill = 'incantation';
			elseif($row['type'] == 'bouclier') $skill = 'blocage';
			else $skill = 'melee';
			if($row['type'] == 'baton' OR $row['type'] == 'bouclier') $coef = $row['forcex'] * $row['melee'];
			else $coef = $row['forcex'] * $row[$skill];
			$coef_joueur = $joueur['coef_'.$skill];
			if($coef_joueur <= $coef OR $cout > $joueur['star']) $couleur = 3;

			$arme = decompose_objet($joueur['inventaire']->$main);
			if($arme['id_objet'] != '')
			{
				$requete = "SELECT * FROM arme WHERE id = ".$arme['id_objet'];
				$req_arme = $db->query($requete);
				$row_arme = $db->read_array($req_arme);
				$echo = 'Arme équipée : '.$row_arme['nom'].' - '.$row_arme['type'].' - Dégats '.$row_arme['degat'];
			}
			else
				$echo = 'Rien n\'est équipé';
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td onmouseover="return <?php echo make_overlib(addslashes($echo)); ?>" onClick="return nd();" onmouseout="nd();">
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['type']; ?>
			</td>
			<td>
				<?php
				echo count(explode(';', $row['mains']));
				?>
			</td>
			<td>
				<?php echo $row['degat']; ?>
			</td>
			<td>
				<span class="<?php echo over_price($coef, $coef_joueur); ?>"><?php echo ($coef); ?>
			</td>
	<?php
	$check = true;
	foreach($types[$type] as $typ)
	{
		echo '
			<td>
				<span class="'.over_price($row[$typ[1]], $joueur[$typ[1]]).'">'.$row[$typ[1]].'</span>
			</td>';
		if (over_price($row[$typ[1]], $joueur[$typ[1]]) == 'achat_over') $check = false;
	}
	?>
			<td>
				<span class="<?php echo over_price($cout, $joueur['star']); ?>"><?php echo $cout; ?></span>
			</td>
			<td>
			
			<?php 

			if (over_price($cout, $joueur['star']) == 'achat_normal' AND over_price($coef, $coef_joueur) == 'achat_normal' AND $check AND over_price($cout, $joueur['star'])== 'achat_normal')
			{
			?>
				<a href="javascript:envoiInfo('boutique.php?action=achat&amp;type=arme&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><span class="achat">Achat</span></a>
			<?php
			}
			?>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		</div>

<?php
	}
	else
	{
		$url2 = 'boutique.php?type=armure&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
	?>

		

		<p class="ville_haut"><a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=ceinture', 'carte')">Ceinture</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=chaussure', 'carte')">Chaussure</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=jambe', 'carte')">Jambe</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=main', 'carte')">Main</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=tete', 'carte')">Tête</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=torse', 'carte')">Torse</a><br />
		<a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=cou', 'carte')">Cou</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=dos', 'carte')">Dos</a> - <a href="javascript:envoiInfo('<?php echo $url2; ?>&amp;part=doigt', 'carte')">Doigt</a></p><br />
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
			</td>
			<td>
				Type
			</td>
			<td>
				PP
			</td>
			<td>
				PM
			</td>
			<td>
				Force
			</td>
			<td>
				Stars
			</td>
			<td>
				Achat
			</td>
		</tr>
		<?php
		
		$color = 1;
		$where = 'lvl_batiment <= '.$level_batiment;
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM armure WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($row['forcex'] > $joueur['force'] OR $cout > $joueur['star']) $couleur = 3;

			if($joueur['inventaire']->$row['type'] != '' AND $joueur['inventaire']->$row['type'] !== 0)
			{
				$armure = decompose_objet($joueur['inventaire']->$row['type']);
				$requete = "SELECT * FROM armure WHERE id = ".$armure['id_objet'];
				$req_armure = $db->query($requete);
				$row_armure = $db->read_array($req_armure);
				$echo = 'Armure équipée : '.$row_armure['nom'].' - PP = '.$row_armure['PP'].' / PM = '.$row_armure['PM'];
			}
			else $echo = 'Armure équipée : Aucune';
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td onmouseover="return <?php echo make_overlib($echo); ?>" onClick="return nd();" onmouseout="return nd();">
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['type']; ?>
			</td>
			<td>
				<?php echo $row['PP']; ?>
			</td>
			<td>
				<?php echo $row['PM']; ?>
			</td>
			<td>
				<span class="<?php echo over_price($row['forcex'], $joueur['force']); ?>"><?php echo $row['forcex']; ?></span>
			</td>
			<td>
				<span class="<?php echo over_price($cout, $joueur['star']); ?>"><?php echo $cout; ?></span>
			</td>
			<td>
			<?php
				if (over_price($cout, $joueur['star']) == 'achat_normal' AND over_price($row['forcex'], $joueur['force']) == 'achat_normal')
				{
				?>	
				<a href="javascript:envoiInfo('boutique.php?action=achat&amp;type=armure&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')"><span class="achat">Achat</span></a>
				<?php 
				}
				?>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		

<?php
	}
}
?>