<?php
$root = '../';
function __autoload($class_name)
{
	global $root;
	require_once($root.'class/'.$class_name .'.class.php');
}

include($root.'fonction/time.inc.php');
include($root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include($root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include($root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include($root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include($root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include($root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include($root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include($root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include($root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include($root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include($root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include($root.'class/inventaire.class.php');

include($root."pChart/pData.class");
include($root."pChart/pChart.class");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
include('stat_autres.php');
include('stat_star.php');
?>