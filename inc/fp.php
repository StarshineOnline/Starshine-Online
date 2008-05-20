<?php
header('Content-Type: text/html; charset=ISO-8859-1');
if(!isset($root)) $root = '';
// Fichier principal ou l'on pourra les informations de la sessions et ou l'on mettra tout les fichiers a inclure
session_start();
ob_start();

//Inclusion des fonctions permettant de g�rer le temps
include($root.'class/db.class.php');
include($root.'fonction/time.inc.php');
include($root.'fonction/action.inc.php');

//R�cup�re le timestamp en milliseconde de d�but de cr�ation de la page
$debut = getmicrotime();

//R�cup�ration des variables de connexion � la base et connexion � cette base
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

//Inclusion du fichier contenant les fonctions permettant de g�rer les qu�tes
include($root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de g�rer l'�quipement
include($root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include($root.'class/inventaire.class.php');

//Inclusion du fichier contenant les fonctions de gestion des r�ponses
include_once($root.'fonction/reponses.inc.php');

//Inclusion du fichier contenant les fonctions de securit�
include_once($root.'fonction/security.inc.php');

// Inclusion du fichier permettant d'imprier l'en-t�te
include_once($root.'fonction/print.inc.php');

?>