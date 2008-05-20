<?php
include('class/db.class.php');
include('fonction/time.inc');
include('fonction/action.inc.php');

//R�cup�re le timestamp en milliseconde de d�but de cr�ation de la page
$debut = getmicrotime();

//R�cup�ration des variables de connexion � la base et connexion � cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc');

//Inclusion du fichier contenant les fonctions permettant de g�rer les qu�tes
include('fonction/quete.inc');

//Inclusion du fichier contenant les fonctions permettant de g�rer l'�quipement
include('fonction/equipement.inc');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

$requete = "SELECT ID FROM royaume WHERE ID <> 0 ORDER BY ID ASC";
$req = $db->query($requete);
while($row = $db->read_row($req))
{
	for($i = 0; $i < 100; $i++)
	{
		$requete = "INSERT INTO depot_royaume VALUES('', 1, ".$row[0].")";
		$db->query($requete);
	}
}
?>