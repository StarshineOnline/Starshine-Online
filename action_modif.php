<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);

?>
	<div id="carte">
		<h2><a href="javascript:envoiInfo('actions.php', 'information');">Script de combat</a> - Modification d'un script de combat</h2>
		Sélectionnez le script à modifier :<br />
		<form action="action.php" method="post">
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
			<input type="button" name="valid" value="Valider" onclick="envoiInfo('action.php?from=modif&amp;id_action=' + document.getElementById('id_action').value, 'information');" />
		</form>
	</div>