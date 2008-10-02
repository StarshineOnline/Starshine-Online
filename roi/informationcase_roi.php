<?php
$root = '../';

include($root.'inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

$W_case = $_GET['case'];

$W_coord = convert_in_coord($W_case);

check_case($W_coord);
?>

<h2>Informations Case - X : <?php echo $W_coord['x']; ?> | Y : <?php echo $W_coord['y']; ?></h2>

<?php

$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);

$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$type_terrain = type_terrain($W_row['info']);

//Si c'est la capitale
if($W_coord['x'] == $Trace[$R['race']]['spawn_x'] AND $W_coord['y'] == $Trace[$R['race']]['spawn_y'])
{
	echo '<h3>Capitale '.$R['capitale'].'</h3>';
}
?>
<div class="information_case">
	<h4><?php echo $R['nom']; ?></h4>
	<strong><?php echo $Gtrad[$R['race']]; ?></strong> - <?php echo $Gtrad['diplo'.$R['diplo']]; ?> - Taxe : <?php echo $R['taxe']; ?>%<br />
	<strong><?php echo $type_terrain[1]; ?></strong>
</div>
<?php
$W_requete = 'SELECT perso.ID, perso.nom, race, hp, rang_royaume, grade.nom as gnom FROM perso LEFT JOIN grade ON perso.rang_royaume = grade.id WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].') AND statut = \'actif\'';
$W_query = $db->query($W_requete);

//Affichage des infos des joueurs
if($db->num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>Joueurs</h4>
	<table width="90%">';
}
	
$mybonus = recup_bonus($_SESSION['ID']);
$affiche_div = '';
while($W_row = $db->read_array($W_query))
{
	echo '<tr><td width="50%">
	';
	$W_nom = $W_row['nom'];
	$W_race = $W_row['race'];
	$W_ID = $W_row['ID'];
	$W_hp = $W_row['hp'];
	$bonus = recup_bonus($W_ID);
	// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
	if ($W_hp <= 0)
	{
		echo '<img src="image/interface/mort.png" alt="Mort" title="Le personnage est mort" style="vertical-align : top;" /> ';
	}
	else
	{
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	$requete = "SELECT ".$W_race." FROM diplomatie WHERE race = '".$joueur['race']."'";
	$req_diplo = $db->query($requete);
	$row_diplo = $db->read_array($req_diplo);
	
	$statut_joueur = 'normal';
	$diplo = $row_diplo[0];
	if($row_diplo[0] == 127)
	{
		$amende = recup_amende($W_ID);
		$row_diplo[0] = 0;
		if($amende)
		{
			switch($amende['statut'])
			{
				case 'normal' :
				break;
				case 'bandit' :
					$row_diplo[0] = 5;
					$statut_joueur = 'Bandit';
				break;
				case 'criminel' :
					$row_diplo[0] = 10;
					$statut_joueur = 'Criminel';
				break;
			}
		}
	}
	echo '<strong>'.$W_nom.'</strong></td><td width="30%">'.$Gtrad[$W_race].'</a></td>';
	if($statut_joueur != 'normal') echo ' ('.$statut_joueur.')';
	echo '</tr>';
}
echo '</table>'.$affiche_div.'
</div>';

//Affichage des placements
$W_requete = 'SELECT * FROM placement WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].')';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
if($num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>En construction</h4>';
	while($W_row = $db->read_array($W_query))
	{
		//Recherche du batiment
		$requete = "SELECT * FROM batiment WHERE id = ".$W_row['id_batiment'];
		$req = $db->query($requete);
		$row_b = $db->read_assoc($req);
		$Royaume = get_royaume_info($joueur['race'], $W_row['royaume']);
		echo '<span onmousemove="afficheInfo(\'infob_'.$W_row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'infob_'.$W_row['id'].'\', \'none\', event, \'centre\');">'.$row_b['nom'].' '.$Gtrad[$Royaume['race']].'</span> - HP : '.$W_row['hp'];
		if($joueur['race'] != $Royaume['race'] AND !array_key_exists('repos_sage', $joueur['debuff'])) echo  ' <a href="javascript:envoiInfo(\'attaque_monstre.php?ID='.$W_row['id'].'&amp;type=batiment&amp;table=placement&amp;poscase='.$W_case.'\', \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($G_PA_attaque_batiment - $reduction_pa).' PA)</a>';
		echo '<br />
		<div class="jsinformation_case" id="infob_'.$W_row['id'].'">
			'.transform_sec_temp($W_row['fin_placement'] - time()).' avant fin de construction 
		</div></div>';
	}
}

//Affichage des batiments
$W_requete = 'SELECT * FROM construction WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].')';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
if($num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>Batiments</h4>';
	while($W_row = $db->read_array($W_query))
	{
		//Recherche du batiment
		$requete = "SELECT * FROM batiment WHERE id = ".$W_row['id_batiment'];
		$req = $db->query($requete);
		$row_b = $db->read_assoc($req);
		$Royaume = get_royaume_info($joueur['race'], $W_row['royaume']);
		echo '<span onmousemove="afficheInfo(\'infob_'.$W_row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'infob_'.$W_row['id'].'\', \'none\', event, \'centre\');"><image src="image/mini_'.$row_b['type'].'.png" style="vertical-align : top;" title="'.$row_b['nom'].'" alt="'.$row_b['nom'].'" />';
		$nom = $row_b['nom'];
		if($row_b['type'] == 'fort' AND $joueur['race'] == $Royaume['race']) $nom = '<a href="javascript:envoiInfo(\'fort.php?poscase='.$W_case.'&amp;id_batiment='.$row_b['id'].'\', \'centre\');">'.$row_b['nom'].'</a>';
		if($row_b['type'] == 'bourg' AND $joueur['race'] == $Royaume['race']) $nom = '<a href="javascript:envoiInfo(\'bourg.php?poscase='.$W_case.'&amp;id_batiment='.$row_b['id'].'\', \'centre\');">'.$row_b['nom'].'</a>';
		if($row_b['type'] == 'arme_de_siege' AND $joueur['race'] == $Royaume['race']) $nom = '<a href="javascript:envoiInfo(\'arme_de_siege.php?poscase='.$W_case.'&amp;id_construction='.$W_row['id'].'\', \'centre\');">'.$row_b['nom'].'</a>';
		echo ' '.$nom;
		echo ' '.$Gtrad[$Royaume['race']].' - HP : '.$W_row['hp'];
		echo '</span>';
		if($joueur['race'] != $Royaume['race']) echo  ' <a href="javascript:envoiInfo(\'attaque_monstre.php?ID='.$W_row['id'].'&amp;type=batiment&amp;table=construction&poscase='.$W_case.'\', \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($G_PA_attaque_batiment - $reduction_pa).' PA)</a>';
		echo '<br />
		<div class="jsinformation_case"  id="infob_'.$W_row['id'].'">
			'.$row_b['description'].'
		</div></div>';
	}
}
?>
</div>
