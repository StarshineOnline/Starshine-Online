<?php

if (isset($_GET['javascript'])) include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

include('levelup.php');

echo '
<table cellspacing="5">			
<tr>
	<td>';

			require_once('deplacementjeu.php');
echo '
	</td>
	<td class="trstat">
		<a href="javascript:envoiInfo(\'personnage.php\', \'information\')" style="text-decoration : none; color : #000;" title="Maximum '.($joueur['rang_grade'] + 2).' buffs"><strong>'.ucwords($joueur['grade']).' '.$joueur['nom'].'</strong>
		<br />
		'.$Gtrad[$joueur['race']].' '.$joueur['classe'].'</a><br />
		';
		//Listing des buffs
		foreach($joueur['buff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" ondblclick="if(confirm(\'Voulez vous supprimer '.$buff['nom'].' ?\')) envoiInfo(\'suppbuff.php?id='.$buff['id'].'\', \'perso\');" alt="'.$buff['type'].'" onmousemove="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'block\', event); document.getElementById(\'info_'.$buff['type'].'\').style.zIndex = 2; document.getElementById(\'carte\').style.zIndex = 0;" onmouseout="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'none\', event );" />
			<div class="infobox" id="info_'.$buff['type'].'">
				<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Dur�e '.transform_sec_temp($buff['fin'] - time()).'
			</div>';
		}
		if(count($joueur['debuff']) > 0) echo '<br />';
		//Listing des debuffs
		foreach($joueur['debuff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" onmousemove="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'block\', event); document.getElementById(\'info_'.$buff['type'].'\').style.zIndex = 2; document.getElementById(\'carte\').style.zIndex = 0;" onmouseout="javascript:afficheInfo(\'info_'.$buff['type'].'\', \'none\', event );" />
			<div class="infobox" id="info_'.$buff['type'].'">
				<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Dur�e '.transform_sec_temp($buff['fin'] - time()).'
			</div>';
		}
		//xp
		$pourcent_level = progression_level(level_courant($joueur['exp']));
		echo '

	</td>
	<td class="trstat">
		<table style="font-size : 0.9em;">
		<tr class="trcolor2">
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				PA
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.genere_image_pa($joueur).'
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.$joueur['pa'].' / '.$G_PA_max.'
			</td>

		</tr>
		<tr class="trcolor2">
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				HP
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.genere_image_hp($joueur).'
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.$joueur['hp'].' / '.$joueur['hp_max'].'
			</td>
		</tr>
		<tr class="trcolor2">
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				MP
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.genere_image_mp($joueur).'
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.$joueur['mp'].' / '.$joueur['mp_max'].'
			</td>
		</tr>
		<tr class="trcolor2">
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				XP
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.genere_image_exp($joueur['exp'], prochain_level($joueur['level']), $pourcent_level).'
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.$pourcent_level.' % - <strong>Niv. '.$joueur['level'].'</strong>
			</td>
		</tr>
		<tr class="trcolor2">
			<td style="font-size : 10px; margin : 0px; padding : 0px;" colspan="3">
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

	</td>
	<td class="trstat">
		<table>
		<tr class="trcolor2" style="font-size : 10px; margin : 0px; padding : 0px;">
			<td>
				<img src="image/'.moment_jour().'.png" alt="'.moment_jour().'" title="'.moment_jour().'" style="vertical-align : middle;">
			</td>
			<td style="text-align : center;">
				'.moment_jour().'<br />'.date_sso().'
			</td>
		</tr>
		<tr class="trcolor2" style="font-size : 10px; margin : 0px; padding : 0px;">
			<td>
				<strong>Stars</strong>
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px; text-align : center;">
				'.$joueur['star'].'
			</td>
		</tr>
		<tr class="trcolor2" style="font-size : 10px; margin : 0px; padding : 0px;">
			<td>
				<strong>Honneur</strong>
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px; text-align : center;">
				'.$joueur['honneur'].'
			</td>
		</tr>
		<tr class="trcolor2" style="font-size : 10px; margin : 0px; padding : 0px;">
			<td>
				<strong><a href="javascript:envoiInfo(\'point_sso.php\', \'information\');">Point Shine</a></strong>
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px; text-align : center;">
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
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				<strong>Lign�e</strong>
			</td>
			<td style="font-size : 10px; margin : 0px; padding : 0px;">
				'.$lignee['nom'].'
			</td>
		</tr>';
	}*/
	echo '
		</table>
	</td>
	';

//Recherche si le joueur a re�u une invitation pour grouper
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
	<td class="trstat">
	Vous avez re�u une invitation pour grouper de la part de '.$W_row2['nom'].'<br />
	<a href="javascript:envoiInfo(\'reponseinvitation.php?ID='.$ID_invitation.'&groupe='.$ID_groupe.'&reponse=oui\', \'information\')">Accepter</a> / <a href="javascript:envoiInfo(\'reponseinvitation.php?ID='.$ID_invitation.'&reponse=non\', \'information\')">Refuser</a>
	</td>';
}

$div_membres = '';
//Affichage du groupe si le joueur est group�
if ($joueur['groupe'] > 0)
{
	echo '
	<td class="trstat">
		<table style="margin : 0; padding : 0;" cellspacing="0">
		<tr>
	';
	//R�cup�ration du groupe
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
	<td style="vertical-align : top;">
		<strong>Groupe :</strong><br />
		<a href="javascript:envoiInfo(\'infogroupe.php?id='.$groupe['id'].'\', \'information\')"><img src="image/information.png" alt="Informations sur le joueur" /></a> <a href="javascript:envoiInfo(\'envoimessage.php?type=groupe&amp;id_groupe='.$groupe['id'].'\', \'information\');"><img src="image/message.png" alt="Envoie d\'un message au groupe" title="Envoie d\'un message au groupe" /></a>	
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
			<table cellspacing="0">
			<tr>
				<td>';
				if ($groupe['membre'][$i]['hp'] <= 0)
				{
					echo '<img src="image/mort.png" alt="Mort" title="Mort" width="10px"/>';
				}
				echo'
					<a href="javascript:envoiInfo(\'infojoueur.php?ID='.$groupe['membre'][$i]['id_joueur'].'&amp;poscase='.$groupe['membre'][$i]['poscase'].'\', \'information\');" title="X : '.$groupe['membre'][$i]['x'].' / Y : '.$groupe['membre'][$i]['y'].'" />
					'.$groupe['membre'][$i]['nom'].'</a>';
			if ($leader) echo ' <a href="javascript:if(confirm(\'Voulez vous expulser ce joueur ?\')) envoiInfo(\'kickjoueur.php?ID='.$groupe['membre'][$i]['id_joueur'].'&groupe='.$groupe['id'].'\', \'information\');"><img src="image/exspuler-joueur_icone.png" alt="Expulser le joueur" title="Expulser le joueur" /></a>';
			echo '
				</td>
			</tr>
			<tr>
				<td style="margin : 0; padding : 0; height : 6px; font-size : 1px;"><img src="'.$groupe['membre'][$i]['image_hp'].'" title="'.$groupe['membre'][$i]['hp'].' / '.$groupe['membre'][$i]['hp_max'].'" /></td>
			</tr>
			<tr>
				<td style="margin : 0; padding : 0; height : 6px; font-size : 1px;"><img src="'.$groupe['membre'][$i]['image_mp'].'" title="'.$groupe['membre'][$i]['mp'].' / '.$groupe['membre'][$i]['mp_max'].'" /></td>
			</tr>
			</table>';
			if(($j % 2) == 1) echo '</td><td style="vertical-align : top;">';
			$j++;
		}
		$i++;
	}
	?>
		</tr>
		</table>
	</td>
	<?php
}
?>
	<td valign="top">
		<a href="index.php"><img src="image/icone_index.png" alt="Retour � l'index" title="Retour � l'index" style="vertical-align : middle;" /></a>
		<a href="javascript:if(confirm('Voulez vous d�connecter ?')) document.location.href = 'index.php?deco=ok';"><img src="image/deconnexion_icone.png" alt="D�connexion" title="D�connexion" style="vertical-align : middle;" /></a><br />
		<a href="http://forum.starshine-online.com/"><img src="image/forum_icone.png" alt="Forum" title="Forum" style="vertical-align : middle;" /></a>
		<a href="option.php"><img src="image/icone_option.png" alt="Options de jeu" title="Options de jeu" style="vertical-align : middle;" /></a>
	</td>
</tr>
</table>