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
		<a href="view_map.php">G�n�rer Carte du monde</a>
	</li>
	<li>
		<a href="view_map.php?show=royaume">G�n�rer Carte du monde par royaume</a>
	</li>

<h1>Qu�tes</h1>
<a href="create_quete.php">Cr�er une qu�te</a>

<h1>G�n�rer des monstres</h1>
<?php
$requete = "SELECT * FROM monstre";
$sqlQuery = mysqli_query($db, $requete);
while($row = mysqli_fetch_array($sqlQuery))
{
	echo '<a href="admin.php?generation_id='.$row['id'].'">G�n�rer des '.$row['nom'].'</a><br />';
}
?>