<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

$mail = '';
$root = '';

include_once(root.'inc/variable.inc.php');
global $G_max_x;
global $G_max_y;

define('MAP_WIDTH', $G_max_x);
define('MAP_HEIGHT', $G_max_y);
define('CARTE_WIDTH', MAP_WIDTH * 3);
define('CARTE_HEIGHT', MAP_HEIGHT * 3);
define('CARTE3D_WIDTH', MAP_WIDTH * 4); // *4 car la carte 3d est plus grande
define('CARTE3D_HEIGHT', MAP_HEIGHT * 4); // *4 car la carte 3d est plus grande

function __autoload($class_name)
{
	global $root;
	require_once($root.'class/'.$class_name .'.class.php');
}

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

include_once(root."pChart/pData.class");
include_once(root."pChart/pChart.class");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

?>