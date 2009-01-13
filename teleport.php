<?php

//Inclusion du haut du document html
include('inc/fp.php');

$joueur = recupperso($_SESSION['ID']);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

	if(array_key_exists('id', $_GET))
	{
	
		$W_distance = detection_distance($W_case, $_SESSION["position"]);
		if($W_distance == 0)
		{
			$requete = 'SELECT * FROM teleport WHERE ID = '.sSQL($_GET['id']);
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$P_distance = calcul_distance(convert_in_pos($row['posx'], $row['posy']), $_SESSION["position"]);
			if($row['cout'] > 0)
			{
				$cout = $row['cout'];
				$taxe = 0;
			}
			else
			{
				$cout = ($P_distance * 10);
				$taxe = ceil($cout * $R['taxe'] / 100);
				$cout = $cout + $taxe;
			}
			if(($joueur['star'] >= $cout) AND ($joueur['pa'] >= 5))
			{
				$joueur['x'] = $row['posx'];
				$joueur['y'] = $row['posy'];
				$joueur['star'] = $joueur['star'] - $cout;
				$joueur['pa'] = $joueur['pa'] - 5;
				$requete = "UPDATE perso SET x = ".$joueur['x'].", y = ".$joueur['y'].", pa = ".$joueur['pa'].", star = ".$joueur['star']." WHERE ID = ".$_SESSION['ID'];
				$db->query($requete);
				//Récupération de la taxe
				if($taxe > 0)
				{
					$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
					$db->query($requete);
					$requete = "UPDATE argent_royaume SET teleport = teleport + ".$taxe." WHERE race = '".$R['race']."'";
					$db->query($requete);
				}
				header("Location: deplacement.php");
			}
			else echo 'Vous n\'avez pas assez de stars ou de PA !<br />';
		}
	}
	$W_coord = convert_in_coord($W_case);
	?>
   	<h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville( '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R['nom'].'</a> - ', $W_case); ?> <?php echo '<a href="teleport.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Pierre de téléportation </a></h2>
		<?php include('ville_bas.php');?>

	<div class="ville_test">
	Liste des villes possible pour téléportation :<br />
	<ul>
	<?php
	//Séléction de tous les téléport disponibles
	$requete = 'SELECT * FROM teleport';
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		// Bastien : Si coût = 0 (pas NULL), on saute l'entrée
		if ($row['cout'] === '0') continue;
		if($row['cout'] > 0)
		{
			$cout = $row['cout'];
			$row_diplo[0] = 127;
			$row_race['capitale'] = 'Ville Neutre';
			$row_race['race'] = 'neutre';
		}
		else
		{
			$coords_roy = convert_in_pos($row['posx'], $row['posy']);
			//Récupération du royaume du téléport
			$requete_roy = 'SELECT * FROM map WHERE ID = '.$coords_roy;
			$req_roy = $db->query($requete_roy);
			$row_roy = $db->read_array($req_roy);
			//Récupération de la race du royaume
			$requete_race = 'SELECT * FROM royaume WHERE ID = '.$row_roy['royaume'];
			$req_race = $db->query($requete_race);
			$row_race = $db->read_array($req_race);
			if($row_race['race'] != '')
			{
				//Sélection de la diplomatie
				$requete_diplo = "SELECT ".$row_race['race']." FROM diplomatie WHERE race = '".$joueur['race']."'";
				$req_diplo = $db->query($requete_diplo);
				$row_diplo = $db->read_row($req_diplo);
				$distance = calcul_distance(convert_in_pos($row['posx'], $row['posy']), $_SESSION["position"]);
				$cout =  $distance * 10;
				$cout = ceil(($cout * $R['taxe'] / 100) + $cout);
			}
			else
			{
				$row_diplo[0] = 8;
			}
		}
		//Si en paix
		if(($row_diplo[0] <= 3) OR $row_diplo[0] == 127 AND $distance > 2)
		{
			if($cout != 0)
			{
				echo '
				<li><a href="teleport.php?poscase='.$W_case.'&amp;id='.$row['ID'].'" onclick="if(confirm(\'Voulez vous vous téléporter à '.addslashes($row_race['capitale']).' ('.$Gtrad[$row_race['race']].' - '.$cout.' Stars et 5 PA)\')) return envoiInfo(this.href, \'centre\'); else return false;">Téléportation à '.$row_race['capitale'].' ('.$Gtrad[$row_race['race']].')</a> ('.$cout.' Stars et 5 PA)</li>';
			}
		}
	}
	echo '</ul>';
	echo'</div>';
?>