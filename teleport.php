<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

	if(array_key_exists('id', $_GET))
	{
		if($W_row['type'] == 1)
		{
			$requete = 'SELECT * FROM teleport WHERE ID = '.sSQL($_GET['id']);
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$P_distance = calcul_distance(convert_in_pos($row['posx'], $row['posy']), $joueur->get_pos());
			if($row['cout'] > 0)
			{
				$cout = $row['cout'];
				$taxe = 0;
			}
			else
			{
				$cout = ($P_distance * 10);
				$taxe = ceil($cout * $R->get_taxe_diplo($joueur->get_race()) / 100);
				$cout = $cout + $taxe;
			}
			if(($joueur->get_star() >= $cout) AND ($joueur->get_pa() >= 5))
			{
				$joueur->set_x($row['posx']);
				$joueur->set_y($row['posy']);
				$joueur->set_star($joueur->get_star() - $cout);
				$joueur->set_pa($joueur->get_pa() - 5);
				$joueur->sauver();
				//Récupération de la taxe
				if($taxe > 0)
				{
					$R->set_star($R->get_star() + $taxe);
					$R->sauver();
					$requete = "UPDATE argent_royaume SET teleport = teleport + ".$taxe." WHERE race = '".$R->get_race()."'";
					$db->query($requete);
				}
				header("Location: deplacement.php");
			}
			else echo 'Vous n\'avez pas assez de stars ou de PA !<br />';
		}
	}
	if(array_key_exists('id_bourg', $_GET))
	{
		$W_distance = detection_distance($W_case, $_SESSION['position']);
		if($W_distance == 0)
		{
			$requete = "SELECT id, x, y FROM construction WHERE id = ".sSQL($_GET['id_bourg']);
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$P_distance = calcul_distance(convert_in_pos($row['x'], $row['y']), $joueur->get_pos());
			$cout = ($P_distance * 7);
			$taxe = ceil($cout * $R->get_taxe_diplo($joueur->get_race()) / 100);
			$cout = $cout + $taxe;
			if(($joueur->get_star() >= $cout) AND ($joueur->get_pa() >= 5))
			{
				$joueur->set_x($row['posx']);
				$joueur->set_y($row['posy']);
				$joueur->set_star($joueur->get_star() - $cout);
				$joueur->set_pa($joueur->get_pa() - 5);
				$joueur->sauver();
				//Récupération de la taxe
				if($taxe > 0)
				{
					$R->set_star($R->ger_star() + $taxe);
					$R->sauver();
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
   	<h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville( '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> - ', $joueur->get_pos()); ?> <?php echo '<a href="teleport.php" onclick="return envoiInfo(this.href, \'carte\')">';?> Pierre de téléportation </a></h2>
		<?php include_once(root.'ville_bas.php');?>

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
				$requete_diplo = "SELECT ".$row_race['race']." FROM diplomatie WHERE race = '".$joueur->get_race()."'";
				$req_diplo = $db->query($requete_diplo);
				$row_diplo = $db->read_row($req_diplo);
				$distance = calcul_distance(convert_in_pos($row['posx'], $row['posy']), $joueur->get_pos());
				$cout =  $distance * 10;
				$cout = ceil(($cout * $R->get_taxe_diplo($joueur->get_race()) / 100) + $cout);
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
	?>
	</ul>
	Liste des bourgs possible pour téléportation :<br />
	<ul>
	<?php
	if($R->get_diplo($joueur->get_race()) == 127)
	{
	    //Séléction de tous les téléport disponibles
	    $requete = "SELECT id, x, y FROM construction WHERE type = 'bourg' AND royaume = ".$R->get_id();
	    $req = $db->query($requete);
	    while($row = $db->read_array($req))
	    {
		    $distance = calcul_distance(convert_in_pos($row['x'], $row['y']), $joueur->get_pos());
		    $cout =  $distance * 7;
		    $cout = ceil(($cout * $R->get_taxe_diplo($joueur->get_race()) / 100) + $cout);
		    echo '<li><a href="teleport.php?poscase='.$W_case.'&amp;id_bourg='.$row['id'].'" onclick="if(confirm(\'Voulez vous vous téléporter sur ce bourg - '.$cout.' Stars et 5 PA)\')) return envoiInfo(this.href, \'centre\'); else return false;">Téléportation sur le bourg (X : '.$row['x'].' / Y : '.$row['y'].')</a> ('.$cout.' Stars et 5 PA)</li>';
	    }
	}
?>
</ul>
</div>