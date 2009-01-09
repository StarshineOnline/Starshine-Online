<?php

if (isset($_GET['javascript'])) include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

include('levelup.php');

echo '
<table cellspacing="5">
<tr>
	<td>
	<img src="image/logossot_small.png" alt="sso logo" />
	</td>
	<td class="trstat">
		<a href="personnage.php" onclick="return envoiInfo(this.href, \'information\')" style="text-decoration : none; color : #000;" title="Maximum '.($joueur['rang_grade'] + 2).' buffs"><strong>'.ucwords($joueur['grade']).' '.$joueur['nom'].'</strong>
		<br />
		'.$Gtrad[$joueur['race']].' '.$joueur['classe'].'</a><br />
		';
		//Listing des buffs
		foreach($joueur['buff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" ondblclick="if(confirm(\'Voulez vous supprimer '.$buff['nom'].' ?\')) envoiInfo(\'suppbuff.php?id='.$buff['id'].'\', \'perso\');" alt="'.$buff['type'].'" onmousemove="" onclick="afficheInfo(\'info_'.$buff['type'].'\', \'block\', event); document.getElementById(\'info_'.$buff['type'].'\').style.zIndex = 2; document.getElementById(\'carte\').style.zIndex = 0;" onmouseout="" onclick="afficheInfo(\'info_'.$buff['type'].'\', \'none\', event );" />
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
				<strong><a href="point_sso.php" onclick="return envoiInfo(this.href, \'information\');">Point Shine</a></strong>
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
				<strong>Lignée</strong>
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


?>
	<td valign="top">
		<a href="index.php"><img src="image/icone_index.png" alt="Retour à l'index" title="Retour à l'index" style="vertical-align : middle;" /></a>
		<a href="javascript:if(confirm('Voulez vous déconnecter ?')) document.location.href = 'index.php?deco=ok';"><img src="image/deconnexion_icone.png" alt="Déconnexion" title="Déconnexion" style="vertical-align : middle;" /></a><br />
		<a href="http://forum.starshine-online.com/"><img src="image/forum_icone.png" alt="Forum" title="Forum" style="vertical-align : middle;" /></a>
		<a href="option.php"><img src="image/icone_option.png" alt="Options de jeu" title="Options de jeu" style="vertical-align : middle;" /></a>
	</td>
</tr>
</table>