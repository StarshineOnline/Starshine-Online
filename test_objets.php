<?php
session_start();
include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include('fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include('fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

?>
<head>
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/interface.css" />
	<script language="Javascript" type="text/javascript" src="javascript/fonction.js"></script>
</head>

<?php
$req = $db->query("SELECT * FROM objet");
while($row = $db->read_assoc($req))
{
	$objets[$row['id']] = $row;
}

$req = $db->query("SELECT * FROM recette WHERE id < 10");
while($row = $db->read_assoc($req))
{
	echo '<h2>'.$row['nom'].'</h2>
	<ul>';
	$ingredients = explode(';', $row['ingredient']);
	foreach($ingredients as $ingredient)
	{
		$explode = explode('-', $ingredient);
		echo '<li>'.$objets[$explode[0]]['nom'].' x '.$explode[1].'</li>';
	}
	echo '</ul>';
}
?>