<?php
session_start();
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

?>
<head>
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/interface.css" />
	<script language="Javascript" type="text/javascript" src="javascript/fonction.js"></script>
</head>
<table style="font-size : 0.9em;">
<?php
$requete = "SELECT COUNT(*) as tot, ligne FROM bonus WHERE id_categorie = 1 GROUP BY ligne";
$req_l = $db->query($requete);
while($row_l = $db->read_assoc($req_l))
{
	//print_r($row_l);
	$case1 = '';
	$case2 = '';
	$case3 = '';
	echo '
	<tr>';
	$i = 0;
	$requete = "SELECT * FROM bonus WHERE id_categorie = 1 AND ligne = ".$row_l['ligne']." ORDER BY id_bonus ASC";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$texte = '<strong>'.$row['nom'].'</strong><br />
				<span class="xsmall">'.$row['point'].' points</span><br />
				<a href="javascript:if(confirm(\'Voulez vous vraiment prendre le bonus ~'.$row['nom'].'~ pour '.$row['point'].' points ? \')) document.location.href=\'\';"><img src="image/cadre.png" onmousemove="afficheInfo(\'cadre_'.$row['id_bonus'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'cadre_'.$row['id_bonus'].'\', \'none\', event, \'centre\');"></a>
				<div style="color : #fff; text-align : left; display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color : #555; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="cadre_'.$row['id_bonus'].'">
					<strong>'.$row['nom'].'</strong><br />
					'.$row['description'].'
				</div>';
		//print_r($row);
		if($row_l['tot'] > 1)
		{
			if($i > 0)
			{
				$case1 = $texte;
			}
			else
			{
				$case3 = $texte;
			}
		}
		else
		{
			$case2 = $texte;
			$requete = "SELECT COUNT(*) FROM bonus_permet WHERE id_bonus = ".$row['id_bonus'];
			$req_bp = $db->query($requete);
			$row_bp = $db->read_row($req_bp);
			if($row_bp[0] > 1)
			{
				//print_r($row_bp);
				$case1 = '<img src="image/coin_hg.png">';
				$case3 = '<img src="image/coin_hd.png">';
			}
			$requete = "SELECT COUNT(*) FROM bonus_permet WHERE id_bonus_permet = ".$row['id_bonus'];
			$req_bp = $db->query($requete);
			$row_bp = $db->read_row($req_bp);
			if($row_bp[0] > 1)
			{
				//print_r($row_bp);
				$case1 = '<img src="image/coin_bg.png">';
				$case3 = '<img src="image/coin_bd.png">';
			}
		}
		$i++;
	}
	echo '
		<td style="text-align : center;">
			'.$case1.'
		</td>
		<td style="text-align : center;">
			'.$case2.'
		</td>
		<td style="text-align : center;">
			'.$case3.'
		</td>
	</tr>';
}
?>
</table>