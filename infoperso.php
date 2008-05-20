<?php

if (isset($_GET['javascript'])) include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

include('levelup.php');
echo '<div class="roseeticone">';
require_once('deplacementjeu.php');
echo '
</div>
<div class="infoperso">
		<a href="javascript:envoiInfo(\'personnage.php\', \'information\')" style="text-decoration : none; color : #000;" title="Maximum '.($joueur['rang_grade'] + 2).' buffs"><strong>'.ucwords($joueur['grade']).' '.$joueur['nom'].'</strong>
		<br />
		'.$Gtrad[$joueur['race']].' '.$joueur['classe'].'</a><br />
		';
		//Listing des buffs
		foreach($joueur['buff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" ondblclick="if(confirm(\'Voulez vous supprimer '.$buff['nom'].' ?\')) envoiInfo(\'suppbuff.php?id='.$buff['id'].'\', \'perso\');" alt="'.$buff['type'].'" onmousemove="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'block\', event); document.getElementById(\'info_'.$buff['type'].'\').style.zIndex = 2; document.getElementById(\'carte\').style.zIndex = 0;" onmouseout="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'none\', event );" />
			<div class="infobox" id="info_'.$buff['type'].'">
				<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Durée '.transform_sec_temp($buff['fin'] - time()).'
			</div>';
		}
		if(count($joueur['debuff']) > 0) echo '<br />';
		//Listing des debuffs
		foreach($joueur['debuff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" onmousemove="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'block\', event); document.getElementById(\'info_'.$buff['type'].'\').style.zIndex = 2; document.getElementById(\'carte\').style.zIndex = 0;" onmouseout="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'none\', event );" />
			<div class="infobox" id="info_'.$buff['type'].'">
				<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Durée '.transform_sec_temp($buff['fin'] - time()).'
			</div>';
		}
		//xp
		$pourcent_level = progression_level(level_courant($joueur['exp']));
		echo '

	</div>
	<div class="infoperso">
		<table>
		<tr>
			<td>
				PA
			</td>
			<td>
				'.genere_image_pa($joueur).'
			</td>
			<td>
				'.$joueur['pa'].' / '.$G_PA_max.'
			</td>

		</tr>
		<tr>
			<td>
				HP
			</td>
			<td>
				'.genere_image_hp($joueur).'
			</td>
			<td>
				'.$joueur['hp'].' / '.$joueur['hp_max'].'
			</td>
		</tr>
		<tr>
			<td>
				MP
			</td>
			<td>
				'.genere_image_mp($joueur).'
			</td>
			<td>
				'.$joueur['mp'].' / '.$joueur['mp_max'].'
			</td>
		</tr>
		<tr>
			<td>
				XP
			</td>
			<td>
				'.genere_image_exp($joueur['exp'], prochain_level($joueur['level']), $pourcent_level).'
			</td>
			<td>
				'.$pourcent_level.' % - <strong>Niv. '.$joueur['level'].'</strong>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				';
				if($joueur['action_a'] == 0)
				{
					echo 'Vous n\'avez pas de script d\'action !';
				}
				else
				{
					$script_attaque = recupaction_all($joueur['action_a']);
					$script_defense = recupaction_all($joueur['action_d']);
					echo 'Script : Att = "'.$script_attaque['nom'].'"';
				}
				echo '
			</td>
		</tr>
		</table>

</div>
<div class="infoperso">
		<table>
		<tr>
			<td>
				<img src="image/'.moment_jour().'.png" alt="'.moment_jour().'" title="'.moment_jour().'" style="vertical-align : middle;">
			</td>
			<td style="text-align : center;">
				'.moment_jour().'<br />'.date_sso().'
			</td>
		</tr>
		<tr>
			<td>
				<strong>Stars</strong>
			</td>
			<td style="text-align : center;">
				'.$joueur['star'].'
			</td>
		</tr>
		<tr>
			<td>
				<strong>Honneur</strong>
			</td>
			<td style="text-align : center;">
				'.$joueur['honneur'].'
			</td>
		</tr>
		<tr>
			<td>
				<strong><a href="javascript:envoiInfo(\'point_sso.php\', \'information\');">Point Shine</a></strong>
			</td>
			<td style="text-align : center;">
				'.$joueur['point_sso'].'
			</td>
		</tr>
		';
/*	$joueur['lignee'] = recupperso_lignee($joueur['ID']);
	if($joueur['lignee'] != 0)
	{
		$lignee = recup_lignee($joueur['lignee']);
		echo '
		<tr class="trcolor1">
			<td>
				<strong>Lignée</strong>
			</td>
			<td>
				'.$lignee['nom'].'
			</td>
		</tr>';
	}*/
	echo '
		</table>
	</div>
	';

//Recherche si le joueur a reçu une invitation pour grouper
$W_requete = 'SELECT * FROM invitation WHERE receveur = '.$_SESSION['ID'];
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$ID_invitation = $W_row['ID'];
$ID_groupe = $W_row['groupe'];
//Si il y a une invitation pour le joueur
if ($db->num_rows > 0)
{
	$W_requete = "SELECT nom FROM perso WHERE ID = ".$W_row['inviteur'];
	$W_req = $db->query($W_requete);
	$W_row2 = $db->read_array($W_req);
	
	echo '
	<div class="infoperso">
	Vous avez reçu une invitation pour grouper de la part de '.$W_row2['nom'].'<br />
	<a href="javascript:envoiInfo(\'reponseinvitation.php?ID='.$ID_invitation.'&groupe='.$ID_groupe.'&reponse=oui\', \'information\')">Accepter</a> / <a href="javascript:envoiInfo(\'reponseinvitation.php?ID='.$ID_invitation.'&reponse=non\', \'information\')">Refuser</a>
	</div>';
}

$div_membres = '';
//Affichage du groupe si le joueur est groupé
if ($joueur['groupe'] > 0)
{
	echo '<div class="infoperso">';
	//Récupération du groupe
	$groupe = recupgroupe($joueur['groupe'], '');
	$i = 0;
	$count = count($groupe['membre']);
	while($i < $count)
	{
		//Recherche infos sur le joueur
		$requete = "SELECT hp, hp_max, mp, mp_max, x, y, nom, classe, statut FROM perso WHERE ID = ".$groupe['membre'][$i]['id_joueur'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if($row['statut'] == 'actif')
		{
			$groupe['membre'][$i] = array_merge($row, $groupe['membre'][$i]);
			$groupe['membre'][$i]['hp_max'] = floor($groupe['membre'][$i]['hp_max']);
			$groupe['membre'][$i]['mp_max'] = floor($groupe['membre'][$i]['mp_max']);
			$groupe['membre'][$i]['poscase'] = convert_in_pos($row['x'], $row['y']);
			$ratio_hp = floor(10 * ($groupe['membre'][$i]['hp'] / $groupe['membre'][$i]['hp_max']));
			if($ratio_hp > 10) $ratio_hp = 10;
			if($ratio_hp < 0) $ratio_hp = 0;
			$groupe['membre'][$i]['image_hp'] = 'image/barre/vie'.$ratio_hp.'.png';
			$ratio_mp = floor(10 * ($groupe['membre'][$i]['mp'] / $groupe['membre'][$i]['mp_max']));
			if($ratio_mp > 10) $ratio_mp = 10;
			if($ratio_mp < 0) $ratio_mp = 0;
			$groupe['membre'][$i]['image_mp'] = 'image/barre/mp'.$ratio_mp.'.png';
		}
		$i++;
	}
	//Si le joueur est le leader mettre la variable leader a true.
	if ($groupe['id_leader'] == $_SESSION['ID']) $leader = true;
	else $leader = false;
	echo '
	<table style="width:250px;">
	<tr>
		<td style="text-align: center;">
		<a href="javascript:envoiInfo(\'infogroupe.php?id='.$groupe['id'].'\', \'information\')"><img src="image/information.png" alt="Informations sur le joueur" /></a> <br /><a href="javascript:envoiInfo(\'envoimessage.php?type=groupe&amp;id_groupe='.$groupe['id'].'\', \'information\');"><img src="image/message.png" alt="Envoie d\'un message au groupe" title="Envoie d\'un message au groupe" /></a>	
	</td>
	<td style="vertical-align : top;">';
	$i = 0;
	$j = 0;
	$count = count($groupe['membre']);
	//Boucle d'affichage des membres
	while ($i < $count)
	{
		if ($groupe['membre'][$i]['id_joueur'] != $joueur['ID'])
		{
			echo '
			<table cellspacing="0" style="width:100%;">
			<tr>
				<td style="width:35%;">';
				if ($groupe['membre'][$i]['hp'] <= 0)
				{
					echo '<img src="image/mort.png" alt="Mort" title="Mort" width="10px"/>';
				}
				echo'
					<a href="javascript:envoiInfo(\'infojoueur.php?ID='.$groupe['membre'][$i]['id_joueur'].'&amp;poscase='.$groupe['membre'][$i]['poscase'].'\', \'information\');" title="X : '.$groupe['membre'][$i]['x'].' / Y : '.$groupe['membre'][$i]['y'].'" />
					'.$groupe['membre'][$i]['nom'].'</a>';
				echo '
				</td>
				<td style="width:61%;">
				<table>
				<tr>
					<td style="margin : 0; padding : 0; height : 6px; font-size : 1px;"><img src="'.$groupe['membre'][$i]['image_hp'].'" title="'.$groupe['membre'][$i]['hp'].' / '.$groupe['membre'][$i]['hp_max'].'" /></td>
				</tr>
				<tr>
					<td style="margin : 0; padding : 0; height : 6px; font-size : 1px;"><img src="'.$groupe['membre'][$i]['image_mp'].'" title="'.$groupe['membre'][$i]['mp'].' / '.$groupe['membre'][$i]['mp_max'].'" /></td>
			</tr>
			</table>
			</td>
			<td style="width:4%">';
			if ($leader) echo ' <a href="javascript:if(confirm(\'Voulez vous expulser ce joueur ?\')) envoiInfo(\'kickjoueur.php?ID='.$groupe['membre'][$i]['id_joueur'].'&groupe='.$groupe['id'].'\', \'information\');"><img src="image/exspuler-joueur_icone.png" alt="Expulser le joueur" title="Expulser le joueur" style="width:11px;"/></a>';
			echo '			
			</tr>
			</table>';

		}
		$i++;
	}
	?>
	</td>
	</tr>
	</table>
	</div>
	<?php
}
?>
	<div class="roseeticone">
		<a href="index.php"><img src="image/icone_index.png" alt="Retour à l'index" title="Retour à l'index" style="vertical-align : middle;" /></a>
		<a href="javascript:if(confirm('Voulez vous déconnecter ?')) document.location.href = 'index.php?deco=ok';"><img src="image/deconnexion_icone.png" alt="Déconnexion" title="Déconnexion" style="vertical-align : middle;" /></a><br />
		<a href="http://forum.starshine-online.com/"><img src="image/forum_icone.png" alt="Forum" title="Forum" style="vertical-align : middle;" /></a>
		<a href="option.php"><img src="image/icone_option.png" alt="Options de jeu" title="Options de jeu" style="vertical-align : middle;" /></a>
	</div>
