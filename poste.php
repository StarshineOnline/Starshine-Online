<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($joueur->get_race() != $R->get_race() &&
		$R->get_diplo($joueur->get_race()) > 6)
{
	echo "<h5>Impossible de commercer avec un tel niveau de diplomatie</h5>";
	exit (0);
}

?>
<fieldset>
		<legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> > ', $joueur->get_pos()); ?> <?php echo '<a href="poste.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Poste </a></legend>
		<?php include_once(root.'ville_bas.php');?>
<?php
//Affichage des quêtes
if($R->get_nom() != 'Neutre') $return = affiche_quetes('poste', $joueur);
if($return[1] > 0 AND !array_key_exists('fort', $_GET))
{
	echo '<div class="ville_test"><span class="texte_normal">';
	echo 'Voici quelques petits services que j\'ai à vous proposer :';
	echo $return[0];
	echo '</span></div><br />';
}

if($joueur->get_race() == $R->get_race() OR $R->get_diplo($joueur->get_race()) < 7)
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
					$requete = "SELECT ".$row['race']." FROM diplomatie WHERE race = '".$joueur->get_race()."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					if($row_diplo[0] == 127) $row_diplo[0] = -1;
					$cout = ceil(pow(1.6, ($row_diplo[0] + 1)));
					$taxe = ceil($cout * $R->get_taxe_diplo($joueur->get_race()) / 100);
					echo 'Cela vous coutera '.($cout+$taxe).' stars.<br />';
					?>
					<form method="post" id="formMessage" action="poste.php?action=envoi&amp;cout=<?php echo $cout; ?>&amp;ID=<?php echo $row['ID']; ?>">
						Titre du message :<br />
						<input type="text" name="titre" id="titre" size="30" /><br />
						Message :<br />
						<textarea name="message" id="message" cols="30" rows="6"></textarea><br />
						<input type="button" onclick="envoiInfo('poste.php?action=envoi&cout=<?php echo $cout; ?>&ID=<?php echo $row['ID']; ?>&titre='+encodeURIComponent($('#titre').val())+'&message='+encodeURIComponent($('#message').val()), 'carte');" value="Envoyer !" />
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
				$taxe = ceil(sSQL($_GET['cout'] * $R->get_taxe_diplo($joueur->get_race()) / 100));
				$cout = sSQL($_GET['cout']) + $taxe;
				if($cout <= $joueur->get_star())
				{
					$titre = addslashes(sSQL($_GET['titre']));
					if($titre != '')
					{
						$message = sSQL($_GET['message']);
						if ($message != '')
						{
							$id_groupe = 0;
							$id_dest = 0;
							$id_thread = 0;
							$id_dest = $W_ID;
							$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
							$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
							echo '<h6>Message transmis avec succès</h6>';

							$joueur->set_star($joueur->get_star() - $cout);
							$joueur->sauver();
							//Récupération de l'argent
							$R->set_star($R->get_star() + $taxe);
							$R->sauver();
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
	<input type="text" id="perso_envoi" value="" onkeyup="javascript:suggestion(this.value, 'suggestion');"/>
	<div id="suggestion"></div>
	<br />
	<input type="button" onclick="javascript:envoiInfo('poste.php?action=select_perso&amp;perso_envoi=' + escape(document.getElementById('perso_envoi').value), 'carte')" value="Sélectionner" />
	</div>
	</fieldset>
	<?php
	}
}
?>