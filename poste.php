<?php
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = convert_in_pos($joueur['x'], $joueur['y']);
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
		<h2 class="ville_titre"><?php if(verif_ville($joueur['x'], $joueur['y'])) return_ville( '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="poste.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Poste </a></h2>
		<?php include('ville_bas.php');?>
<?php
//Affichage des quêtes
if($R['nom'] != 'Neutre') $return = affiche_quetes('poste', $joueur);
if($return[1] > 0 AND !array_key_exists('fort', $_GET))
{
	echo '<div class="ville_test"><span class="texte_normal">';
	echo 'Voici quelques petits services que j\'ai à vous proposer :';
	echo $return[0];
	echo '</span></div><br />';
}

$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		?>
	
		<div class="ville_test">
		<?php
		switch ($_GET['action'])
		{
			//Vérification si le personnage existe
			case 'select_perso' :
				$perso = sSQL($_GET['perso_envoi']);
				$requete = "SELECT ID, nom, race FROM perso WHERE nom = '".$perso."'";
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
					$row = $db->read_assoc($req);
					$requete = "SELECT ".$row['race']." FROM diplomatie WHERE race = '".$joueur['race']."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					if($row_diplo[0] == 127) $row_diplo[0] = -1;
					$cout = ceil(pow(1.6, ($row_diplo[0] + 1)));
					$taxe = ceil($cout * $R['taxe'] / 100);
					$cout = $cout + $taxe;
					echo 'Cela vous coutera '.$cout.' stars.<br />';
					?>
					<form method="post" id="formMessage" action="poste.php?action=envoi&amp;cout=<?php echo $cout; ?>&amp;ID=<?php echo $row['ID']; ?>">
						Titre du message :<br />
						<input type="text" name="titre" id="titre" size="30" /><br />
						Message :<br />
						<textarea name="message" id="message" cols="30" rows="6"></textarea><br />
						<input type="button" onclick="envoiFormulaire('formMessage', 'carte');" value="Envoyer !" />
					</form>
					<?php
				}
				else
				{
					echo 'Ce personnage n\'existe pas.';
				}
			break;
			//Envoi du message
			case 'envoi' :
				$W_ID = sSQL($_GET['ID']);
				$cout = sSQL($_GET['cout']);
				$taxe = ceil(sSQL($_GET['cout']) * $R['taxe'] / 100);
				$cout = sSQL($_GET['cout']) + $taxe;
				if($cout <= $joueur['star'])
				{
					$titre = addslashes(sSQL($_POST['titre']));
					if($titre != '')
					{
						$message = addslashes(sSQL($_POST['message']));
						if ($message != '')
						{
							$id_groupe = 0;
							$id_dest = 0;
							$id_thread = 0;
							$id_dest = $W_ID;
							$messagerie = new messagerie($joueur['ID']);
							$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
							echo '<h6>Message transmis avec succès</h6>';

							$joueur['star'] -= $cout;
							$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
							$req = $db->query($requete);
							//Récupération de l'argent
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							echo '<h6>Message bien envoyé !</h6>';
						}
						else
						{
							echo '<h5>Vous n\'avez pas saisi de message</h5>';
						}
					}
					else
					{
						echo '<h5>Vous n\'avez pas saisi de titre</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de stars.</h5>';
				}
			break;
		}
	?>
	</div>
	</td></tr></table>
	<?php
	}
	else
	{
	//Affichage de la poste
	?>

	<div class="ville_test">
	A qui voulez vous envoyer un message ?
	<input type="text" id="perso_envoi" value="" /><br />
	<input type="button" onclick="javascript:envoiInfo('poste.php?poscase=<?php echo $W_case; ?>&amp;action=select_perso&amp;perso_envoi=' + document.getElementById('perso_envoi').value, 'carte')" value="Sélectionner" />
	</div>
	<?php
	}
}
?>