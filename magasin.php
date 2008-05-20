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
if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
?>
    	<h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> - ', $W_case); ?> <?php echo '<a href="javascript:envoiInfo(\'taverne.php?poscase='.$W_case.'\',\'carte\')">';?> Magasin </a></h2>
				<?php include('ville_bas.php');?>
		<?php
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
				$requete = "SELECT id, prix FROM objet WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$taxe = ceil($row['prix'] * $R['taxe'] / 100);
				$cout = $row['prix'] + $taxe;
				if ($joueur['star'] >= $cout)
				{
					if(prend_objet('o'.$row['id'], $joueur))
					{
						$joueur['star'] = $joueur['star'] - $cout;
						$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$_SESSION['ID'];
						$req = $db->query($requete);
						//Récupération de la taxe
						if($taxe > 0)
						{
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET magasin = magasin + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h5>Objet acheté !</h5>';
					}
					else
					{
						echo $G_erreur;
					}
				}
				else
				{
					echo 'h5>Vous n\'avez pas assez de Stars</h5>';
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
	$url = 'magasin.php?poscase='.$W_case.$fort.'&amp;order=';

		$types = array();
	?>
		<?php
		//Affichage des quêtes
		$return = affiche_quetes('magasin', $joueur);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo '<div class="ville_test"><span class="texte_normal">';
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
			echo '</span></div><br />';
		}
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
				Achat
			</td>
		</tr>
		
		<?php
		
		$color = 1;
		$where = " achetable = 'y'";
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM objet WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($row['forcex'] > $joueur['force'] OR $row['melee'] > $joueur['melee'] OR $cout > $joueur['star'] OR $row['distance'] > $joueur['distance']) $couleur = 3;
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td>
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<a href="javascript:envoiInfo('magasin.php?action=achat&amp;id=<?php echo $row['id']; ?>&amp;type=<?php echo $row['type']; ?>&amp;poscase=<?php echo $_GET['poscase'].$fort; ?>', 'carte')"><span class="achat">Achat</span></a>
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
?>