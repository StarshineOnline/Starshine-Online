<?php

include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

$W_case = $_GET['case'];

$W_coord = convert_in_coord($W_case);

check_case($W_coord);
$W_distance = detection_distance($W_case, $_SESSION["position"]);
if($W_distance < 4)
{
?>

<h2>Informations Case - X : <?php echo $W_coord['x']; ?> | Y : <?php echo $W_coord['y']; ?><a href="javascript:envoiInfo('carte_perso_affiche.php', 'information')"> <img src="image/icone/oujesuis.png" alt="Où je suis ?" title="Où je suis ?" style="vertical-align : middle;" /></a> </h2>

<?php

$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);

$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$type_terrain = type_terrain($W_row['info']);
$coutpa = cout_pa($type_terrain[0], $joueur['race']);
$coutpa_base = $coutpa;
$coutpa_diagonale = cout_pa2($coutpa, $joueur, $W_row, true);
$coutpa = cout_pa2($coutpa, $joueur, $W_row, false);
if ($coutpa_base > 49) $coutpa = 'Infranchissable';

//Si c'est la capitale
if($W_coord['x'] == $Trace[$R['race']]['spawn_x'] AND $W_coord['y'] == $Trace[$R['race']]['spawn_y'])
{
	echo '<h3>Capitale '.$R['capitale'].'</h3>';
}
?>
<div class="information_case">
<h4><?php echo $R['nom']; ?></h4>
<strong><?php echo $Gtrad[$R['race']]; ?></strong> - <?php echo $Gtrad['diplo'.$R['diplo']]; ?> - Taxe : <?php echo $R['taxe']; ?>%<br />
<strong><?php echo $type_terrain[1]; ?></strong> - <?php echo $coutpa; ?> PA de déplacement <span class="xsmall">(en diagonale = <?php echo $coutpa_diagonale; ?> PA)</span>
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
	$facteur_xp = $row_diplo[0] * 0.2;
	$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
	if ($facteur_honneur < 0) $facteur_honneur = 0;
	if(array_key_exists(6, $bonus) AND !check_affiche_bonus($bonus[6], $joueur, $W_row)) $chaine_nom = $W_nom;
	else $chaine_nom = $W_row['gnom'].' '.$W_nom;
	$echo = $Gtrad['diplo'.$diplo].' => XP : '.($facteur_xp * 100).'% - Honneur : '.($facteur_honneur * 100).'%';
	echo '<a href="javascript:envoiInfo(\'infojoueur.php?ID='.$W_ID.'&poscase='.$W_case.'\', \'information\');" onclick="return nd();" onmouseover="return '.make_overlib($echo).'" onmouseout="return nd();"><strong>'.$chaine_nom.'</strong></td><td width="30%">'.$Gtrad[$W_race].'</a></td>';
//	echo '<a href="javascript:envoiInfo(\'infojoueur.php?ID='.$W_ID.'&poscase='.$W_case.'\', \'information\')" onmousemove="afficheInfo(\'info_'.$W_ID.'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$W_ID.'\', \'none\', event, \'centre\');"><strong>'.$chaine_nom.'</strong></td><td width="30%">'.$Gtrad[$W_race].'</a></td>';
//	$affiche_div .= '<div class="jsinformation_case" id="info_'.$W_ID.'">
//	'.$Gtrad['diplo'.$diplo].' => XP : '.($facteur_xp * 100).'% - Honneur : '.($facteur_honneur * 100).'%
//	</div>';
	if ($W_ID != $_SESSION['ID'])
	{
		echo ' <td width="10%"><a href="envoimessage.php?id_type=p'.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/message.png" title="Envoyer un message" /></a></td>';
		if($joueur['sort_jeu'] != '') echo '<td width="10%"> <a href="javascript:envoiInfo(\'sort_joueur.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'\', \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></a></td>';
		if($row_diplo[0] <= 5 OR array_key_exists(5, $mybonus)) echo '<td width="10%"> <a href="javascript:envoiInfo(\'echange.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'\', \'information\')"><img src="image/icone/echanger.png" alt="Echanger" title="Echanger" /></a></td>';
	}
	else
	{
		if($joueur['sort_jeu'] != '') echo '<td width="10%"></td><td width="10%"><a href="javascript:envoiInfo(\'sort.php\', \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></a></td>';
	}
	if($statut_joueur != 'normal') echo ' ('.$statut_joueur.')';
	echo '</tr>';
}
if(array_key_exists('buff_rapidite', $joueur['buff'])) $reduction_pa = $joueur['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
if(array_key_exists('debuff_ralentissement', $joueur['debuff'])) $reduction_pa -= $joueur['debuff']['debuff_ralentissement']['effet'];
echo '</table>'.$affiche_div.'
</div>';
//Affichage des PNJ
$W_requete = 'SELECT * FROM pnj WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].')';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
if($num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>PNJ</h4>';
	while($W_row = $db->read_array($W_query))
	{
		echo  '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
		if($W_row['x'] == $joueur["x"] AND $W_row['y'] == $joueur["y"]) echo ' <a href="javascript:envoiInfo(\'pnj.php?id='.$W_row['id'].'&amp;poscase='.$W_case.'\', \'information\')">Parler...</a>';
		echo '<br /></div>';
	}
}

//Affichage des Donjons
$W_requete = 'SELECT * FROM donjon WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].')';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
if($num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>Donjons</h4>';
	while($W_row = $db->read_array($W_query))
	{
		echo  '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
		//Entrée du donjon
		$quete_fini = explode(';', $joueur['quete_fini']);
		$unlock = false;
		//Myriandre locké si pas fini la quête
		if($W_row['nom'] == 'Myriandre')
		{
			if(in_array('53', $quete_fini)) $unlock = true;
		}
		elseif($W_row['nom'] == 'Teleport')
		{
			$requete_dragon = 'SELECT id FROM map_monstre WHERE type = 125 OR type = 126';
			$req_dragon = $db->query($requete_dragon);
			
			$num_rows = $db->num_rows;
			//Si les 2 dragons sont morts alors on ouvre
			if($num_rows == 0) $unlock = true;
		}
		else $unlock = true;
		if($W_row['x'] == $joueur["x"] AND $W_row['y'] == $joueur["y"] AND $unlock) echo ' <a href="jeu2.php?donjon_id='.$W_row['id'].'">Entrer dans le donjon</a>';
		echo '<br /></div>';
	}
}

//Affichage des sorties de Donjons
$W_requete = 'SELECT * FROM donjon WHERE (x_donjon = '.$W_coord["x"].') AND (y_donjon = '.$W_coord["y"].')';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
if($num_rows > 0)
{
	echo '
	<div class="information_case">
	<h4>Donjons</h4>';
	while($W_row = $db->read_array($W_query))
	{
		echo  '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
		//Sortie du donjon
		if($W_row['x_donjon'] == $joueur["x"] AND $W_row['y_donjon'] == $joueur["y"]) echo ' <a href="jeu2.php?donjon_id='.$W_row['id'].'&amp;type=sortie">Sortir du donjon</a>';
		echo '<br /></div>';
	}
}

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
		if($joueur['race'] != $Royaume['race'])
		{
			if(!array_key_exists('repos_sage', $joueur['debuff']))
			{
				echo  ' <a href="javascript:envoiInfo(\'attaque_monstre.php?ID='.$W_row['id'].'&amp;type=batiment&amp;table=placement&amp;poscase='.$W_case.'\', \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($G_PA_attaque_batiment - $reduction_pa).' PA)</a>';
			}
		}
		else
		{
				echo  ' <a href="javascript:envoiInfo(\'archi_accelere_construction.php?id_construction='.$W_row['id'].'\', \'information\')">Accélérer <span class="xsmall">(30 PA)</a>';
		}
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
		elseif($W_row['hp'] < $row_b['hp'])
		{
			echo  ' <a href="javascript:envoiInfo(\'archi_soigne_construction.php?id_construction='.$W_row['id'].'\', \'information\')">Réparer <span class="xsmall">(30 PA)</a>';
		}

		echo '<br />
		<div class="jsinformation_case"  id="infob_'.$W_row['id'].'">
			'.$row_b['description'].'
		</div></div>';
	}
}

$pa_attaque = $G_PA_attaque_monstre;
if(array_key_exists('cout_attaque', $joueur['debuff'])) $pa_attaque = ceil($pa_attaque / $joueur['debuff']['cout_attaque']['effet']);
if(array_key_exists('plus_cout_attaque', $joueur['debuff'])) $pa_attaque = $pa_attaque * $joueur['debuff']['plus_cout_attaque']['effet'];

$W_requete = 'SELECT id, nom, type, hp, level FROM map_monstre WHERE (x = '.$W_coord["x"].') AND (y = '.$W_coord["y"].') ORDER BY level ASC, nom ASC, id ASC';
$W_query = $db->query($W_requete);

$num_rows = $db->num_rows;
//Affichage des infos des monstres
if($num_rows > 0) echo '
<div class="information_case">
<h4>Monstres</h4>
<table>';
while($W_row = $db->read_array($W_query))
{
	$W_nom = $W_row['nom'];
	$W_type = $W_row['type'];
	$W_ID = $W_row['id'];
	//Recherche des capas du mob
	$W2_requete = 'SELECT * FROM monstre WHERE id = '.$W_type.' ORDER BY level ASC';
	$W2_query = $db->query($W2_requete);
	$W2_row = $db->read_array($W2_query);
	$W_hp = $W_row['hp'];
	$diff_level = ($W2_row['level'] - $joueur['level']);
	if($diff_level > 5) $diff_level = 5;
	elseif($diff_level < -5) $diff_level = -5;
	//echo $diff_level;
	$color = $G_consider[$diff_level];
	if($diff_level > 0) $strong = 'bold'; else $strong = 'normal';
	// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
	echo '
	<tr>
		<td width="30%">
			&nbsp;&nbsp;&nbsp;<span style="color : '.$color.'; font-weight : '.$strong.'">'.$W_nom.'</span>
		</td>
		<td width="30%">';
		if(!array_key_exists('repos_sage', $joueur['debuff']) OR !array_key_exists('bloque_attaque', $joueur['debuff'])) echo '<a href="javascript:envoiInfo(\'attaque_monstre.php?ID='.$W_ID.'&poscase='.$W_case.'\', \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($pa_attaque - $reduction_pa).' PA)</span></a>';
		echo ' <a href="javascript:envoiInfo(\'info_monstre.php?ID='.$W_ID.'&poscase='.$W_case.'\', \'information\')"><img src="image/icone/mobinfo.png" alt="Voir informations sur le monstre" title="Voir informations sur le monstre" style="vertical-align : middle;" /></a>';
		if($joueur['sort_jeu'] != '') echo ' <a href="javascript:envoiInfo(\'sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$W_ID.'\', \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" style="vertical-align : middle;" /></a>';
	echo '
		</td>
	</tr>';
}
if ($num_rows > 0) echo '</table>';
}
?>
</div>
