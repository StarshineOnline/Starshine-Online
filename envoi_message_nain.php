<?php
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

//Nombre de joueurs total
$requete = "SELECT ID, nom FROM `perso` WHERE `level` >=4 AND `race` = 'nain' AND `statut` = 'actif'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$message = 'Maître Jarode souhaite vous voir le plus vite possible, vous le trouverez a l\'extérieur de la ville...[br]mais avant cela il veux que vous vous rendiez à la bibliothèque de l\'université afin de consulter certains documents qu\'il a mis a votre disposition';
	$requete = "INSERT INTO message VALUES('', ".$row['ID'].", 0, 'Assistant de Jarod', '".$row['nom']."', 'Rencontre primordiale', '".mysql_escape_string($message)."', '', ".time().", 0)";
	$db->query($requete);
}