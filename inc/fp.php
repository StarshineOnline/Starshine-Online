<?php
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

//Classe gérant les accès à la base de données
include($root.'class/db.class.php');

//Inclusion des fonctions permettant de gérer le temps
include($root.'fonction/time.inc.php');

// Inclusion des fonctions gérant les actions en combat
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

//Inclusion du fichier contenant toutes les fonctions de l'attaque
include($root.'fonction/attaque.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include($root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include($root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include($root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include($root.'class/inventaire.class.php');

//Inclusion du fichier contenant les fonctions de gestion des réponses
include_once($root.'fonction/reponses.inc.php');

//Inclusion du fichier contenant les fonctions de securité
include_once($root.'fonction/security.inc.php');

// Inclusion du fichier permettant d'imprier l'en-tête
include_once($root.'fonction/print.inc.php');

?>