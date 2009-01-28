<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Création récipients pour recette
			</div>
			<?php
			print_r($_POST);
			$recette = new craft_recette($_POST['recette']);
			switch($_POST['recipient'])
			{
				case 'potion' :
					$objet = new objets('', $recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->getId().'-1';
					$recette_recipient->id_objet = 5;
					$recette_recipient->prefixe = 'Petite fiole';
					$recette_recipient->sauver();
					$objet = new objets('', 'eclair '.$recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->id.'-1';
					$recette_recipient->id_objet = 6;
					$recette_recipient->prefixe = 'Fiole éclair';
					$recette_recipient->sauver();
					$objet = new objets('', 'bouchon '.$recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->getId().'-1';
					$recette_recipient->id_objet = 30;
					$recette_recipient->prefixe = 'Fiole a bouchon';
					$recette_recipient->sauver();
				break;
				case 'parchemin' :
					$objet = new objets('', $recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->getId().'-1';
					$recette_recipient->id_objet = 14;
					$recette_recipient->prefixe = 'Parchemin';
					$recette_recipient->sauver();
					$objet = new objets('', 'grand '.$recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->id.'-1';
					$recette_recipient->id_objet = 15;
					$recette_recipient->prefixe = 'Grand parchemin';
					$recette_recipient->sauver();
					$objet = new objets('', 'petit '.$recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->getId().'-1';
					$recette_recipient->id_objet = 48;
					$recette_recipient->prefixe = 'Petit parchemin';
					$recette_recipient->sauver();
				break;
				case 'globe' :
					$objet = new objets('', $recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->getId().'-1';
					$recette_recipient->id_objet = 41;
					$recette_recipient->prefixe = 'Globe de sort';
					$recette_recipient->sauver();
					$objet = new objets('', 'Gros '.$recette->nom, '', 0, 'n', 0, 0, 'y', '');
					$objet->sauver();
					$recette_recipient = new craft_recette_recipient();
					$recette_recipient->id_recette = $recette->id;
					$recette_recipient->resultat = 'o'.$objet->id.'-1';
					$recette_recipient->id_objet = 42;
					$recette_recipient->prefixe = 'Gros globe de sort';
					$recette_recipient->sauver();
				break;
			}
			?>
			<form method="post" action="admin_recette.php">
				Recette : 
				<select id="recette" name="recette">
				<?php
				$requete = "SELECT * FROM craft_recette ORDER BY id ASC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option>
					<?php
				}
				?>
				</select><br />
				Récipient : 
				<select id="recipient" name="recipient">
					<option value="potion">Potions</option>
					<option value="parchemin">Parchemins</option>
					<option value="globe">Globes de sort</option>
				</select>
				<input type="submit" value="Envoyer" />
			</form>
		</div>
		<?php
	include('bas.php');
}
?>