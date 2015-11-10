<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');
//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

//Nombre de joueurs total
$requete = "SELECT ID, nom FROM `perso` WHERE `level` >=4 AND `race` = 'nain' AND `statut` = 'actif'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$message = 'Maître Jarode souhaite vous voir le plus vite possible, vous le trouverez a l\'extérieur de la ville...[br]mais avant cela il veux que vous vous rendiez à la bibliothèque de l\'université afin de consulter certains documents qu\'il a mis a votre disposition';
	$requete = "INSERT INTO message VALUES('', ".$row['ID'].", 0, 'Assistant de Jarod', '".$row['nom']."', 'Rencontre primordiale', '".sSQL($message)."', '', ".time().", 0)";
	$db->query($requete);
}