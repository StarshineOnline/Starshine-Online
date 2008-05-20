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

$grades = array();

$requete = "SELECT * FROM grade";
$db->query($requete);
while($row = $db->read_assoc($req))
{
	$grade[$row['rang']] = $row;
}

?>

<table border="1" cellspacing="0">
	<tr>
		<td>
			<strong>Objet</strong>
		</td>
		<td>
			<strong>Grade</strong>
		</td>
	</tr>
<?php
$requete = "SELECT * FROM objet_royaume ORDER BY grade ASC, nom ASC";
$db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '
	<tr>
		<td>
			'.$row['nom'].'
		</td>
		<td>
			'.$grade[$row['grade']]['nom'].'
		</td>
	</tr>';
}

?>
</table>