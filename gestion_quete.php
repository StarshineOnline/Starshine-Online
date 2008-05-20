<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//V�rifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

if($_GET['action'] == 'achat')
{
	//R�cup�re les informations sur la qu�te
	$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//V�rifie que le royaume a assez de stars pour l'acheter
	if($R['star'] >= $row['star_royaume'])
	{
		//Ajout de la qu�te dans la liste des qu�tes du royaume
		$requete = "INSERT INTO quete_royaume VALUES('', ".$R['ID'].", ".$row['id'].")";
		$req = $db->query($requete);
		//Mis a jour des stars du royaume
		$requete = "UPDATE royaume SET star = star - ".$row['star_royaume']." WHERE ID = ".$R['ID'];
		$req = $db->query($requete);
		echo 'Votre royaume a bien acheter la qu�te "'.$row['nom'].'"';
	}
	else
	{
		echo 'Votre royaume n\'a pas assez de stars pour acheter cette qu�te.';
	}
}
else
{
	$requete = "SELECT * FROM quete WHERE id NOT IN (SELECT id_quete FROM quete_royaume WHERE id_royaume = ".$R['ID'].") ORDER BY star_royaume";
	$req = $db->query($requete);

?>
	<div id="carte">
		<h2 style="width : 330px;">Gestion des qu�tes</h2>
		<table>
		<tr>
			<td>
				Nom
			</td>
			<td>
				Cout en star
			</td>
			<td>
				Achat
			</td>
		</tr>
<?php
	while($row = $db->read_array($req))
	{
		echo '
		<tr>
			<td>
				'.$row['nom'].'
			</td>
			<td>
				'.$row['star_royaume'].'
			</td>
			<td>
				<a href="javascript:envoiInfo(\'gestion_quete.php?poscase='.$W_case.'&amp;action=achat&amp;id='.$row['id'].'\', \'carte\')">Acheter cette qu�te</a>
			</td>
		</tr>';
	}
?>
		</table>
<?php
}
?>
	</div>