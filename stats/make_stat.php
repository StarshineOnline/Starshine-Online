<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
$root = '../';
function __autoload($class_name)
{
	global $root;
	require_once($root.'class/'.$class_name .'.class.php');
}

include_once(root.$root.'fonction/time.inc.php');
include_once(root.$root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.$root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.$root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.$root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.$root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.$root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.$root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.$root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.$root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.$root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.$root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.$root.'class/inventaire.class.php');

include_once(root.$root."pChart/pData.class");
include_once(root.$root."pChart/pChart.class");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
include_once(root.'stat_autres.php');
include_once(root.'stat_star.php');
?>