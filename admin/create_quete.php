<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance) die('maintenance');
include_once(root.'admin/menu_admin.php');

echo '
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Création d\'une quête
	</div>				
	';


//Insertion de la quête dans la bdd
if(array_key_exists('submit3', $_POST))
{
  $requete = "INSERT INTO quete(nom, description, fournisseur, objectif, exp, honneur, star, repete, mode, niveau_requis, honneur_requis, quete_requis, star_royaume, lvl_joueur, achat) VALUES('".sSQL($_SESSION['quete']['nom'])."', '".sSQL($_SESSION['quete']['description'])."', '".sSQL($_SESSION['quete']['fournisseur'])."', '".serialize($_SESSION['objectif'])."', '".$_SESSION['quete']['exp']."', '".$_SESSION['quete']['honneur']."', '".$_SESSION['quete']['star']."', '".$_SESSION['quete']['repetable']."', '".$_SESSION['quete']['mode']."', '".$_SESSION['quete']['niveau_requis']."', '".$_SESSION['quete']['honneur_requis']."', '".$_SESSION['quete']['quete_requis']."', '".$_SESSION['quete']['star_royaume']."', '".$_SESSION['quete']['niveau']."', 'oui')";
  $db->query($requete);
}
if(array_key_exists('submit', $_POST) OR array_key_exists('submit2', $_POST))
{
  if(array_key_exists('submit2', $_POST))
  {
    $numero = count($_SESSION['objectif']);
    $_SESSION['objectif'][$numero]->cible = $_POST['objectif'].$_POST['cible'];
    $_SESSION['objectif'][$numero]->nombre = $_POST['nombre'];
    $_SESSION['objectif'][$numero]->requis = $_POST['requis'];
  }
  else
  {
    $_SESSION['objectif'] = array();
    $_SESSION['quete']['nom'] = $_POST['nom_quete'];
    $_SESSION['quete']['niveau'] = $_POST['niveau_quete'];
    $_SESSION['quete']['niveau_requis'] = $_POST['niveau_requis'];
    $_SESSION['quete']['honneur_requis'] = $_POST['honneur_requis'];
    $_SESSION['quete']['honneur'] = $_POST['honneur'];
    $_SESSION['quete']['exp'] = $_POST['exp'];
    $_SESSION['quete']['star'] = $_POST['star'];
    $_SESSION['quete']['star_royaume'] = $_POST['star_royaume'];
    $_SESSION['quete']['repetable'] = $_POST['repetable'];
    $_SESSION['quete']['mode'] = $_POST['mode'];
    $_SESSION['quete']['fournisseur'] = $_POST['fournisseur'];
    $_SESSION['quete']['quete_requis'] = $_POST['quete_requis'];
    $_SESSION['quete']['description'] = $_POST['description'];
  }
?>
<form action="create_quete.php" method="post">
<table class="admin">
<tr>
	<td>
		Objectif
	</td>
	<td>
		: <select name="objectif">
			<option value="M">Tuer</option>
			<option value="P">Parler à </option>
		</select>
	</td>
	<td>
		Requis
	</td>
	<td>
		<select name="requis">
			<option value=""></option>
			<?php
				$i = 0;
				foreach($_SESSION['objectif'] as $objectif)
				{
					echo '<option value="'.$i.'">'.$i.' - '.$objectif->cible.'</option>';
					$i++;
				}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>
		Cible
	</td>
	<td>
		: <select name="cible">
			<?php
			$requete = "SELECT * FROM monstre ORDER BY level ASC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				echo '<option value="'.$row['id'].'">'.$row['nom'].' - Niv.'.$row['level'].'</option>';
			}
			?>
			<option value="0">N´importe quel PNJ</option>
			<?php
			$requete = "SELECT * FROM pnj ORDER BY nom ASC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				echo '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
			}
			?>
		</select>
	</td>
	<td>
		Nombre
	</td>
	<td>
		: <input type="text" name="nombre" />
	</td>
</tr>
</table>
<input type="submit" name="submit2" value="Etape Suivante >>" />
<input type="submit" name="submit3" value="Valider totallement la quête..." />
<form>
		<?php
	}
	else
	{
	?>
<form action="create_quete.php" method="post">
<table class="admin">
<tr>
	<td>
		Nom de la quête
	</td>
	<td>
		: <input type="text" name="nom_quete" />
	</td>
	<td>
		Niveau de la quête
	</td>
	<td>
		: <input type="text" name="niveau_quete" />
	</td>
</tr>
<tr>
	<td>
		Niveau minimum requis
	</td>
	<td>
		: <input type="text" name="niveau_requis" />
	</td>
	<td>
		Honneur minimum requis
	</td>
	<td>
		: <input type="text" name="honneur_requis" />
	</td>
</tr>
<tr>
	<td>
		Répétable
	</td>
	<td>
		: <select name="repetable">
			<option value="y">Oui</option>
			<option value="n">Non</option>
		</select>
	</td>
	<td>
		Fournisseur
	</td>
	<td>
		: <select name="fournisseur">
			<option value="">Bureau des quêtes</option>
			<option value="taverne">Taverne</option>
			<option value="ecole_combat">Ecole de combat</option>
			<option value="magasin">Alchimiste</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		Mode
	</td>
	<td>
		: <select name="mode">
			<option value="g">Groupe</option>
			<option value="s">Solo</option>
		</select>
	</td>
	<td>
		Quête requise
	</td>
	<td>
		:
		<select name="quete_requis">
			<option value="">Aucune</option>
		<?php
		$requete = "SELECT id, nom, lvl_joueur FROM quete ORDER BY nom";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			?><option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option><?php
		}
		?>
		</select>
	</td>
</tr>
<tr>
	<td>
		Gain XP
	</td>
	<td>
		: <input type="text" name="exp" />
	</td>
	<td>
		Gain honneur
	</td>
	<td>
		: <input type="text" name="honneur" />
	</td>
</tr>
<tr>
	<td>
		Gain Stars
	</td>
	<td>
		: <input type="text" name="star" />
	</td>
	<td>
		Stars Royaume
	</td>
	<td>
		: <input type="text" name="star_royaume" />
	</td>
</tr>
<tr>
	<td>
		Description
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea name="description" cols="60" rows="15"></textarea>
	</td>
</tr>
</table>
<input type="submit" name="submit" value="Etape Suivante >>" />
<form>
<?php
	}
?>
