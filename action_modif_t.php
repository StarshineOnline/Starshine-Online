<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
$joueur->check_perso();

if($_GET['type'] == 'attaque')
{
	$nom_type = "l'attaque";
	$type = 'attaque';
}
else
{
	$nom_type = "la défense";
	$type = 'defense';
}

?>
	<div id="carte">
		<h2>Sélection d'un script de combat pour <?php echo $nom_type; ?></h2>
		Sélectionnez le script a utiliser lors de <?php echo $nom_type; ?> :<br />
		<form action="action_modif_t.php" method="post">
			<select name="id_action" id="id_action">
				<?php
				$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID'];
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option>
					<?php
				}
				?>
			</select>
			<input type="button" name="valid" value="Valider" onclick="envoiInfo('actions.php?action=select&amp;type=<?php echo $type; ?>&amp;id_action=' + document.getElementById('id_action').value, 'information');" />
		</form>
	</div>
