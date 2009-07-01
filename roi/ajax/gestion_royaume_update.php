<?php
$root = './../../';
//Inclusion du haut du document html
include($root.'haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Véifie si le perso est mort
verif_mort($joueur, 1);

$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);


if (isset($_POST['action']))
{
	switch($_POST['action'])
	{
		case "update_objet_royaume":
			$requete = "SELECT * FROM objet_royaume WHERE id = ".sSQL($_POST['id']);
			$nombre = $_POST['nbr'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$check = true;
			//Si c'est pour une bourgade on vérifie combien il y en a déjà
			if($row['type'] == 'bourg')
			{
				$nb_bourg = nb_bourg($R['ID']);
				$nb_case = nb_case($R['ID']);
				if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250)) $check = false;
			}
			//On vérifie les stars
			if($R['star'] >= ($row['prix'] * $nombre) && $check)
			{
				//On vérifie les ressources
				if(($R['pierre'] >= $row['pierre'] * $nombre) && ($R['bois'] >= $row['bois'] * $nombre) && ($R['eau'] >= $row['eau'] * $nombre) && ($R['charbon'] >= $row['charbon'] * $nombre) && ($R['sable'] >= $row['sable'] * $nombre) && ($R['essence'] >= $row['essence'] * $nombre))
				{
					$i = 0;
					while($i < $nombre)
					{
						//Achat
						$requete = "INSERT INTO depot_royaume VALUES ('', ".$row['id'].", ".$R['ID'].")";
						$db->query($requete);
						//On rajoute un bourg au compteur
						if($row['type'] == 'bourg')
						{
							$requete = "UPDATE royaume SET bourg = bourg + 1 WHERE ID = ".$R['ID'];
							$db->query($requete);
						}
						//On enlève les stars au royaume
						$requete = "UPDATE royaume SET star = star - ".$row['prix'].", bois = bois - ".$row['bois'].", pierre = pierre - ".$row['pierre'].", eau = eau - ".$row['eau'].", charbon = charbon - ".$row['charbon'].", sable = sable - ".$row['sable'].", essence = essence - ".$row['essence']." WHERE ID = ".$R['ID'];
						if($db->query($requete))
						{
							echo '<h6>'.$row['nom'].' bien acheté.</h6><br />';
						}
						$i++;
					}
				}
				else echo '<h5>Il vous manque des ressources !</h5>';
			}
			elseif(!$check)
			{
				echo '<h5>Il y a déjà trop de bourg sur votre royaume.</h5><br />
				Actuellement : '.$nb_bourg.'<br />
				Maximum : '.ceil($nb_case / 250);
			}
			else
			{
				echo '<h5>Le royaume n\'a pas assez de stars</h5>';
			}
		break;
		case 'update_propagande':
			$message = addslashes($_POST['message']);
			if ($message != '')
			{
				$requete = "UPDATE motk SET propagande = '".$message."' WHERE id_royaume = ".$R['ID'];
				if($req = $db->query($requete)) 
				{
					echo '<h6>Propagande bien modifiée !</h6>';
				}
				else echo('<h5>Erreur lors de l\'envoi du message</h5>');
			}
			else
			{
				echo '<h5>Vous n\'avez pas saisi de message</h5>';
			}
		
		break;
		case 'update_motk':		
			$message = addslashes($_POST['message']);
			if ($message != '')
			{
				$requete = "UPDATE motk SET message = '".$message."', date = ".time()." WHERE id_royaume = ".$R['ID'];
				if($req = $db->query($requete)) 
				{
					echo '<h6>Message du roi bien modifié !</h6>';
				}
				else echo('<h5>Erreur lors de l\'envoi du message</h5>');
			}
			else
			{
				echo '<h5>Vous n\'avez pas saisi de message</h5>';
			}
		break;

		
	}
}
?>