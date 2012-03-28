<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
session_start();
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

if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'donne' :
			$_SESSION['ps']['total']++;
		break;
		case 'prend' :
			if(in_array($_GET['id'], $_SESSION['ps']['bonus']))
			{
				echo 'Vous possédez déjà ce bonus !<br />';
			}
			else
			{
				//Récupération des infos interessantes du bonus
				$requete = "SELECT * FROM bonus WHERE id_bonus = ".$_GET['id'];
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				//Vérification si il a assez de points
				if($_SESSION['ps']['total'] >= $row['point'])
				{
					//Vérifie si il a assez en compétence requise
					if(true)
					{
						$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".$_GET['id'];
						$req_bn = $db->query($requete);
						$bn_num_rows = $db->num_rows;
						$check = true;
						while(($row_bn = $db->read_assoc($req_bn)) AND $check)
						{
							if(!in_array($row_bn['id_bonus'], $_SESSION['ps']['bonus'])) $check = false;
						}
						if($check)
						{
							$_SESSION['ps']['bonus'][$_GET['id']] = $_GET['id'];
							$_SESSION['ps']['total'] -= $row['point'];
						}
						else
						{
							echo 'Il vous manque un bonus pour apprendre celui-ci<br />';
						}
					}
					else
					{
					echo 'Il vous faut '.$row['valeur_requis'].' en '.$Gtrad[$row['competence_requis']].'<br />';
					}
				}
				else
				{
					echo 'Vous n\'avez pas assez de points Starshine<br />';
				}
			}
		break;
		case 'raz' :
			$_SESSION['ps']['total'] = 0;
		break;
		case 'raz_bonus' :
			$_SESSION['ps']['bonus'] = array();
		break;
	}
}
if($_SESSION['ps']['bonus'] == '') $_SESSION['ps']['bonus'] = array();
if(!array_key_exists('categorie', $_GET)) $categorie = 1; else $categorie = $_GET['categorie'];

?>
<head>
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/interface.css" />
	<script language="Javascript" type="text/javascript" src="javascript/fonction.js"></script>
</head>
<h1>Vous avez <?php echo $_SESSION['ps']['total']; ?> point(s) Starshine</h1>
<table style="font-size : 0.9em;">
<tr>
	<td style="text-align : center;">
		<a href="test_bonus.php?categorie=1">1</a>
	</td>
	<td style="text-align : center;">
		<a href="test_bonus.php?categorie=2">2</a>
	</td>
	<td style="text-align : center;">
		<a href="test_bonus.php?categorie=3">3</a>
	</td>
</tr>
<?php
$requete = "SELECT COUNT(*) as tot, ligne FROM bonus WHERE id_categorie = ".$categorie." GROUP BY ligne";
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
	$requete = "SELECT * FROM bonus WHERE id_categorie = ".$categorie." AND ligne = ".$row_l['ligne']." ORDER BY id_bonus ASC";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".$row['id_bonus'];
		$req_bn = $db->query($requete);
		$bn_num_rows = $db->num_rows;
		$check = true;
		while(($row_bn = $db->read_assoc($req_bn)) AND $check)
		{
			if(!in_array($row_bn['id_bonus'], $_SESSION['ps']['bonus'])) $check = false;
		}
		if($check)
		{
			$possede = false;
			if(in_array($row['id_bonus'], $_SESSION['ps']['bonus'])) $possede = true;
			if($possede) $color = '#aaaa00';
			else $color = '#000';
			$texte = '<strong style="color : '.$color.'">'.$row['nom'].'</strong><br />';
			if(!$possede) $texte.= '<span class="xsmall">'.$row['point'].' point(s)</span><br />';
			$texte .= '
					<a href="javascript:if(confirm(\'Voulez-vous vraiment prendre le bonus ~'.$row['nom'].'~ pour '.$row['point'].' points ? \')) document.location.href=\'test_bonus.php?action=prend&amp;id='.$row['id_bonus'].'&amp;categorie='.$categorie.'\';"><img src="image/cadre.png" onmousemove="afficheInfo(\'cadre_'.$row['id_bonus'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'cadre_'.$row['id_bonus'].'\', \'none\', event, \'centre\');"></a>
					<div style="color : #fff; text-align : left; display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color : #555; border: 1px solid #000000; font-size:12px; width: 250px; padding: 5px;" id="cadre_'.$row['id_bonus'].'">
						<strong>'.$row['nom'].'</strong><br />
						'.$row['description'].'<br />
						Requis : '.$Gtrad[$row['competence_requis']].' '.$row['valeur_requis'].'
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
				if($row_bp[0] > 1 AND in_array($row['id_bonus'], $_SESSION['ps']['bonus']))
				{
					//print_r($row_bp);
					$case1 = '<img src="image/coin_hg.png">';
					$case3 = '<img src="image/coin_hd.png">';
				}
				if($bn_num_rows > 1)
				{
					//print_r($row_bp);
					$case1 = '<img src="image/coin_bg.png">';
					$case3 = '<img src="image/coin_bd.png">';
				}
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
<a href="test_bonus.php?action=donne&amp;categorie=<?php echo $categorie; ?>">Se donner un point Starshine</a><br />
<a href="test_bonus.php?action=raz&amp;categorie=<?php echo $categorie; ?>">RAZ des points Starshine</a><br />
<a href="test_bonus.php?action=raz_bonus&amp;categorie=<?php echo $categorie; ?>">RAZ des bonus Starshine</a><br />
<?php
//echo '<pre>';
//print_r($_SESSION['ps']);
?>
