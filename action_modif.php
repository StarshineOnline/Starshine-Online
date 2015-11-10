<?php
///@deprecated
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

?>
	<div id="carte">
		<h2><a href="actions.php" onclick="return envoiInfo(this.href, 'information');">Script de combat</a> - Modification d'un script de combat</h2>
		Sélectionnez le script à modifier :<br />
		<form action="action.php" method="post">
			<select name="id_action" id="id_action">
				<?php
				$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id();
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
