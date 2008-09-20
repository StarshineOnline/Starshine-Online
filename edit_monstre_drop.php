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
	if(array_key_exists('id_monstre', $_GET)) $id_monstre = $_GET['id_monstre'];
	else $id_monstre = $_POST['id_monstre'];
	$monstre = recupmonstre($id_monstre, false);
	$requete = "SELECT id, nom, drops FROM monstre WHERE id = ".$id_monstre;
	$req = $db->query($requete);
	$monstre = $db->read_assoc($req);
	$drops = explode(';', $monstre['drops']);
	print_r($_POST);
	if(array_key_exists('chance_drop', $_POST))
	{
		if($_POST['objet'] != '') $o_drop = $_POST['objet'];
		elseif($_POST['arme'] != '') $o_drop = $_POST['arme'];
		elseif($_POST['armure'] != '') $o_drop = $_POST['armure'];
		elseif($_POST['accessoire'] != '') $o_drop = $_POST['accessoire'];
		elseif($_POST['grimoire'] != '') $o_drop = $_POST['grimoire'];
		echo $o_drop;
		$drops[] = $o_drop.'-'.$_POST['chance_drop'];
		$drops_i = implode(';', $drops);
		$requete = "UPDATE monstre SET drops = '".$drops_i."' WHERE id = ".$id_monstre;
		$db->query($requete);
	}
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Edition des drops de <?php echo $monstre['nom']; ?> - <?php echo $monstre['reserve']; ?> RM - Arme : <?php echo $monstre['arme_type']; ?>
			</div>
			Drops actuels :
			<ul>
			<?php
				foreach($drops as $drop)
				{
					if($drop != '')
					{
						$explode = explode('-', $drop);
						if($explode[0][0] == 'r')
						{
							$nom_objet = 'recette';
							$description_objet = '';
						}
						else
						{ 
							$nom_objet = nom_objet($explode[0]);
							$description_objet = nom_objet($explode[0]);
						}
						echo '<li>'.$nom_objet.' - 1 chance sur '.$explode[1].' <span class="xsmall">'.$description_objet.'</span></li>';
					}
				}
			?>
			</ul>
			<form action="edit_monstre_drop.php" method="post">
			Objet :
			<select name="objet">
					<option></option>
			<?php
				$requete = "SELECT * FROM objet ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="o'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				?>
			</select>
			<br />
			Arme :
			<select name="arme">
					<option></option>
				<?php
				$requete = "SELECT * FROM arme ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="a'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				?>
			</select>
			<br />
			Armure :
			<select name="armure">
					<option></option>
				<?php
				$requete = "SELECT * FROM armure ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="p'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				?>
			</select>
			<br />
			Accessoire :
			<select name="accessoire">
					<option></option>
				<?php
				$requete = "SELECT * FROM accessoire ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="m'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				?>
			</select>
			<br />
			Grimoire :
			<select name="grimoire">
					<option></option>
				<?php
				$requete = "SELECT * FROM grimoire ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="l'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
			?>
			</select>
			Chance de drop, 1 sur <input type="text" name="chance_drop" />
			<input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
			<input type="submit" value="Valider" />
			</form>
			<?php
}
			?>
		</div>
	</div>