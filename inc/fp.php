<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
/**
* @file fp.php
* Fichier d'inclusion principal.
* 
* Fichier contenant les informations de la session et tous les fichiers à inclure.
*/


if(!isset($root)) $root = '';

// Initialisation de la session et de la bufferisation de la sortie.
session_start();
ob_start();

// Spécifie à la bibliothèque mb qu'on utilise UTF-8
mb_internal_encoding("UTF-8");

// Spécifie le fuseau horraire pour les dates.
date_default_timezone_set('Europe/Paris');

//Inclusion des fonctions permettant de gérer le temps
include_once(root.$root.'fonction/time.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

// Inclusion des fonctions gérant les actions en combat
include_once(root.$root.'fonction/action.inc.php');

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

//Inclusion du fichier contenant toutes les fonctions de l'attaque
include_once(root.$root.'fonction/attaque.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.$root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.$root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.$root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant les fonctions de gestion des réponses
include_once(root.$root.'fonction/reponses.inc.php');

//Inclusion du fichier contenant les fonctions de securité
include_once(root.$root.'fonction/security.inc.php');

// Inclusion du fichier permettant d'imprier l'en-tête
include_once(root.$root.'fonction/print.inc.php');

function __autoload($class_name)
{
	global $root;
	$file = root.'class/'.$class_name.'.class.php';
	require_once($file);
}

?>
