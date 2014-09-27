<?php
/// @deprecated ?
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