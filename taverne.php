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

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
    	<h2 class="ville_titre"><?php if(verif_ville($joueur['x'], $joueur['y'])) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="javascript:envoiInfo(\'taverne.php?poscase='.$W_case.'\',\'carte\')">';?> Taverne </a></h2>
		<?php include('ville_bas.php');?>	
		<div class="ville_test">
		<span class="texte_normal">
		Bien le bonjour ami voyageur !<br />
		<?php
		//Affichage des quêtes
		if($R['nom'] != 'Neutre') $return = affiche_quetes('taverne', $joueur);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
		}
		?></span></div><br /><?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				$requete = "SELECT * FROM taverne WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$taxe = ceil($row['star'] * $R['taxe'] / 100);
				$cout = $row['star'] + $taxe;
				if ($joueur['star'] >= $cout)
				{
					if($joueur['pa'] >= $row['pa'])
					{
						if($joueur['honneur'] >= $row['honneur'])
						{
						$joueur['star'] = $joueur['star'] - $cout;
						$joueur['pa'] = $joueur['pa'] - $row['pa'];
							$joueur['honneur'] = $joueur['honneur'] - $row['honneur'];
						$joueur['hp'] = $joueur['hp'] + $row['hp'];
						if ($joueur['hp'] > $joueur['hp_max']) $joueur['hp'] = floor($joueur['hp_max']);
						$joueur['mp'] = $joueur['mp'] + $row['mp'];
						if ($joueur['mp'] > $joueur['mp_max']) $joueur['mp'] = floor($joueur['mp_max']);
							$requete = "UPDATE perso SET honneur = ".$joueur['honneur'].", star = ".$joueur['star'].", hp = ".$joueur['hp'].", mp = ".$joueur['mp'].", pa = ".$joueur['pa']." WHERE ID = ".$_SESSION['ID'];
						$req = $db->query($requete);
						//Récupération de la taxe
						if($taxe > 0)
						{
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET taverne = taverne + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h5>La taverne vous remercie de votre achat !</h5>';
					}
					else
					{
							echo '<h5>Vous n\'avez pas assez d\'honneur</h5>';
						}
					}
					else
					{
						echo '<h5>Vous n\'avez pas assez de PA</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de Stars</h5>';
				}
			break;
		}
	}
	
	//Affichage de la taverne
	?>


	<div class="ville_test">
	<table class="marchand" cellspacing="0px">
	<tr class="header trcolor2">
		<td>
			Nom
		</td>
		<td>
			Stars
		</td>
		<td>
			Cout en PA
		</td>
		<td>
			Cout en Honneur
		</td>
		<td>
			HP gagné
		</td>
		<td>
			MP gagné
		</td>
		<td>
			Achat
		</td>
	</tr>
		
		<?php
		
		$color = 1;
		$where = "1";
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".$_GET['part']."'";
		}
		$requete = "SELECT * FROM taverne";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['star'] * $R['taxe'] / 100);
			$cout = $row['star'] + $taxe;
			if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
		?>
		<tr class="element trcolor<?php echo $color; ?>">
			<td>
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<?php echo $row['pa']; ?>
			</td>
			<td>
				<?php echo $row['honneur']; ?>
			</td>
			<td>
				<?php echo $row['hp']; ?>
			</td>
			<td>
				<?php echo $row['mp']; ?>
			</td>
			<td>
				<a href="javascript:envoiInfo('taverne.php?action=achat&amp;id=<?php echo $row['ID']; ?>&amp;poscase=<?php echo $_GET['poscase'].$fort; ?>', 'carte')"><span class="achat">Achat</span></a>
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
refresh_perso();
?>