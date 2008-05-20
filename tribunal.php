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
?>
   	<h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'tribunal.php?poscase='.$W_case.'\', \'carte\')">';?> Tribunal </a></h2>
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
?>

	<div class="ville_test">
	<?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Vérification si le personnage existe
			case 'prime' :
				$perso = $_GET['id_criminel'];
				?>
				<form method="post" action="javascript:envoiInfoPost('tribunal.php?poscase=<?php echo $W_case; ?>&amp;action=prime2&amp;id_criminel=<?php echo $perso; ?>&amp;prime=' + document.getElementById('prime').value, 'carte');">
					Combien de stars voulez vous mettre sur sa tête ? :<br />
					<input type="text" name="prime" id="prime" size="30" /><br />
					<input type="submit" value="Valider" />
				</form>
				<?php
			break;
			//Envoi du message
			case 'prime2' :
				$criminel = sSQL($_GET['id_criminel']);
				$prime = sSQL($_GET['prime']);
				if($prime <= $joueur['star'])
				{
					if($prime > 0)
					{
						$amende = recup_amende($criminel);
						//On supprime les stars au joueur
						$requete = "UPDATE perso SET star = star - ".$prime." WHERE ID = ".$joueur['ID'];
						$db->query($requete);
						//On ajoute la prime dans la liste des primes
						$requete = "INSERT INTO prime_criminel VALUES('', ".$criminel.", ".$joueur['ID'].", ".$amende['id'].", ".$prime.")";
						$db->query($requete);
						//On totalise la prime avec les autres
						$requete = "UPDATE amende SET prime = prime + ".$prime." WHERE id = ".$amende['id'];
						$db->query($requete);
						echo '<h5>Vous avez bien mis une prime sur la tête du criminel !</h5>';
					}
					else
					{
						echo '<h5>Erreur de saisi des stars</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de stars.</h5>';
				}
			break;
		}
	}
	else
	{
		//Affichage des plus grands criminels
		?>
		Voici la liste des criminels de votre royaume :
		<table>
			<tr>
				<td>
					Nom
				</td>
				<td>
					Points de crime
				</td>
				<td>
					Prime
				</td>
				<td>
				</td>
			</tr>
		<?php
		$requete = "SELECT * FROM perso RIGHT JOIN amende ON amende.id_joueur = perso.ID WHERE perso.amende > 0 AND amende.statut = 'criminel' AND race = '".$R['race']."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$perso = recupperso($row['id_joueur']);
			?>
			<tr>
				<td>
					<?php echo $perso['nom']; ?>
				</td>
				<td>
					<?php echo $row['crime']; ?>
				</td>
				<td>
					<?php echo $row['prime']; ?>
				</td>
				<td>
					<a href="javascript:envoiInfo('tribunal.php?poscase=<?php echo $W_case; ?>&amp;action=prime&amp;id_criminel=<?php echo $perso['ID']; ?>', 'carte')">Mettre une prime sur sa tête</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		</div>
		<?php
	}
}
?>