<?php
$mail = '';

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

$joueurs = array();
$races = array();
$i = 0;
$keys = array_keys($Trace);
foreach($Trace as $race)
{
	$j = 0;
	$keys2 = array_keys($Trace);
	foreach($Trace as $race2)
	{
		$races[$keys[$i]][$keys2[$j]] = 0;
		$j++;
	}
	$i++;
}
$requete = "SELECT * FROM journal WHERE action = 'attaque'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if(!array_key_exists($row['actif'], $joueurs))
	{
		$requete = "SELECT race FROM perso WHERE nom = '".$row['actif']."'";
		$req2 = $db->query($requete);
		$row2 = $db->read_row($req2);
		$joueurs[$row['actif']] = $row2[0];
	}
	if(!array_key_exists($row['passif'], $joueurs))
	{
		$requete = "SELECT race FROM perso WHERE nom = '".$row['passif']."'";
		$req2 = $db->query($requete);
		$row2 = $db->read_row($req2);
		$joueurs[$row['passif']] = $row2[0];
	}
	if($joueurs[$row['actif']] != '' AND $joueurs[$row['passif']] != '')
	{
		$races[$joueurs[$row['actif']]][$joueurs[$row['passif']]] += $row['valeur'];
		$races[$joueurs[$row['passif']]][$joueurs[$row['actif']]] += $row['valeur2'];
	}
}
//print_r($races);

$i = 0;
$keys = array_keys($races);
echo '<table border="1">
<tr>
	<td>
	</td>';
foreach($Trace as $race2)
{
	echo '
	<td>
		'.$keys[$i].'
	</td>';
	$i++;
}
echo '</tr>';
$i = 0;
foreach($races as $race)
{
	$j = 0;
	$keys2 = array_keys($race);
	echo '
	<tr>
		<td>
			'.$keys[$i].'
		</td>';
	foreach($race as $rac)
	{
		echo '<td>'.$rac.'</td>';
		$somme[$keys[$i]] += $rac;
		$recu[$keys2[$j]] += $rac;
		$difference[$keys[$i]] += $rac;
		$difference[$keys2[$j]] -= $rac;
		$j++;
	}
	echo '
	</tr>';
	$i++;
}

?>
</table>
<?
echo '<pre>
Dégat infligés';
array_multisort($somme, SORT_DESC);
print_r($somme);
echo 'Dégat recus';
array_multisort($recu, SORT_DESC);
print_r($recu);
echo 'Différence';
array_multisort($difference, SORT_DESC);
print_r($difference);
?>