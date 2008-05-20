<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

if (isset($_GET['generation_id']))
{
}

?>

<h1>Editeur de carte</h1>
<ul>
	<li>
		<a href="edit_map2.php">Editeur</a>
	</li>
	<li>
		<a href="view_map.php">Générer Carte du monde</a>
	</li>
	<li>
		<a href="view_map.php?show=royaume">Générer Carte du monde par royaume</a>
	</li>

<h1>Quètes</h1>
<a href="create_quete.php">Créer une quète</a>

<h1>Générer des monstres</h1>
<?php
$requete = "SELECT * FROM monstre";
$sqlQuery = mysqli_query($db, $requete);
while($row = mysqli_fetch_array($sqlQuery))
{
	echo '<a href="admin.php?generation_id='.$row['id'].'">Générer des '.$row['nom'].'</a><br />';
}
?>