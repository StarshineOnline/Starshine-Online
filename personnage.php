<?php

include('inc/fp.php');
//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$adresse = 'personnage.php?id_perso='.$_GET['id_perso'].'&amp;';
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(23, $bonus) AND check_affiche_bonus($bonus[23], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$adresse = 'personnage.php?';
	$joueur_id = $_SESSION['ID'];
}
$joueur = recupperso($joueur_id);
check_perso($joueur);
$joueur = recupperso($joueur_id);
$de_degat = de_degat($joueur['force'], 0);
$de_degat_arme = de_degat($joueur['force'], $joueur['arme_degat']);

echo '
<fieldset>
	<legend>Nom : '.$joueur['nom'].'</legend>
	<p class="brillant"><a href="'.$adresse.'direction=carac" onclick="return envoiInfo(this.href, \'information\')">Carac</a> | <a href="'.$adresse.'direction=comp" onclick="return envoiInfo(this.href, \'information\')">Compétences</a> | <a href="'.$adresse.'direction=magie" onclick="return envoiInfo(this.href, \'information\')">Magie</a> | <a href="'.$adresse.'direction=stat" onclick="return envoiInfo(this.href, \'information\')">Stats</a></p>
	<p><strong>'.$joueur['nom'].'</strong> - '.$Gtrad[$joueur['race']].' - '.$joueur['classe'].'</p>
	';
	if(!array_key_exists('direction', $_GET)) $_GET['direction'] = 'carac';
	switch($_GET['direction'])
	{
		case 'carac' :
		echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor1">
		<td>
			Constitution
		</td>
		<td>
			'.$joueur['vie'].'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Force
		</td>
		<td>
			'.$joueur['force'].'
		</td>
	</tr>';
	$prochainpa = (($joueur['dernieraction'] + $G_temps_PA) - time());
	if($prochainpa < 0) $prochainpa = 0;
	$prochainpa_m = floor($prochainpa / 60);
	$prochainpa_s = $prochainpa - ($prochainpa_m * 60);

	$prochainregen = (($joueur['regen_hp'] + $G_temps_regen_hp) - time());
	$prochainregen_h = floor($prochainregen / 3600);
	$prochainregen_m = floor(($prochainregen - ($prochainregen_h * 3600)) / 60);
	$prochainregen_s = $prochainregen - ($prochainregen_h * 3600) - ($prochainregen_m * 60);

	//echo strftime("%d/%m/%Y %H:%M", $joueur['maj_hp']);
	$prochainmaj = (($joueur['maj_hp'] + $G_temps_maj_hp) - time());
	$prochainmaj_j = floor($prochainmaj / (3600 * 24));
	$prochainmaj = $prochainmaj - ($prochainmaj_j * 3600 * 24);
	$prochainmaj_h = floor($prochainmaj / 3600);
	$prochainmaj_m = floor(($prochainmaj - ($prochainmaj_h * 3600)) / 60);
	$prochainmaj_s = $prochainmaj - ($prochainmaj_h * 3600) - ($prochainmaj_m * 60);

	$prochainmajm = (($joueur['maj_mp'] + $G_temps_maj_mp) - time());
	$prochainmajm_j = floor($prochainmajm / (3600 * 24));
	$prochainmajm = $prochainmajm - ($prochainmajm_j * 3600 * 24);
	$prochainmajm_h = floor($prochainmajm / 3600);
	$prochainmajm_m = floor(($prochainmajm - ($prochainmajm_h * 3600)) / 60);
	$prochainmajm_s = $prochainmajm - ($prochainmajm_h * 3600) - ($prochainmajm_m * 60);
	echo '
	<tr class="trcolor1">
		<td>
			Dextérité
		</td>
		<td>
			'.$joueur['dexterite'].'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Puissance
		</td>
		<td>
			'.$joueur['puissance'].'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Volonté
		</td>
		<td>
			'.$joueur['volonte'].'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Energie
		</td>
		<td>
			'.$joueur['energie'].'<br />
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Prochain PA
		</td>
		<td>
			'.$prochainpa_m.'m '.$prochainpa_s.'s<br />
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Prochaine Régén. HP / MP
		</td>
		<td>
			'.$prochainregen_h.'h '.$prochainregen_m.'m '.$prochainregen_s.'s<br />
		</td>
	</tr>
	<tr class="trcolor1">
		<td style="padding-right : 10px;">
			Prochaine Augmentation HP
		</td>
		<td>
			'.$prochainmaj_j.'j '.$prochainmaj_h.'h '.$prochainmaj_m.'m '.$prochainmaj_s.'s<br />
		</td>
	</tr>
	<tr class="trcolor2">
		<td style="padding-right : 10px;">
			Prochaine Augmentation MP
		</td>
		<td>
			'.$prochainmajm_j.'j '.$prochainmajm_h.'h '.$prochainmajm_m.'m '.$prochainmajm_s.'s<br />
		</td>
	</tr>
	</table>
	';
		if($joueur['teleport_roi'] != 'true' AND !$visu)
		{
			echo '<a href="personnage.php?direction=stat&action=teleport" onclick="if(confirm(\'Voulez vous vraiment vous téléportez sur votre capitale ?\')) return envoiInfo(this.href, \'information\'); else return false;">Se téléporter dans votre capitale</a>';
		}
		break;

		case 'comp' :
			$maximum['melee'] = recup_max_comp('melee', $joueur['classe_id']);
			$maximum['distance'] = recup_max_comp('distance', $joueur['classe_id']);
			$maximum['esquive'] = recup_max_comp('esquive', $joueur['classe_id']);
			$maximum['blocage'] = recup_max_comp('blocage', $joueur['classe_id']);
			$maximum['artisanat'] = 123;
			$maximum['architecture'] = recup_max_comp('architecture', $joueur['classe_id']);
			$maximum['alchimie'] = recup_max_comp('alchimie', $joueur['classe_id']);
			$maximum['forge'] = recup_max_comp('forge', $joueur['classe_id']);
			$maximum['identification'] = recup_max_comp('identification', $joueur['classe_id']);
			$maximum['survie'] = recup_max_comp('survie', $joueur['classe_id']);
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor1">
		<td>
			Mêlée
		</td>
		<td>
			'.genere_image_comp($joueur, 'melee', $maximum['melee']).' <span class="xsmall">('.$joueur['melee'].' / '.$maximum['melee'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Tir à distance
		</td>
		<td>
			'.genere_image_comp($joueur, 'distance', $maximum['distance']).' <span class="xsmall">('.$joueur['distance'].' / '.$maximum['distance'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Esquive
		</td>
		<td>
			'.genere_image_comp($joueur, 'esquive', $maximum['esquive']).' <span class="xsmall">('.$joueur['esquive'].' / '.$maximum['esquive'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Blocage
		</td>
		<td>
			'.genere_image_comp($joueur, 'blocage', $maximum['blocage']).' <span class="xsmall">('.$joueur['blocage'].' / '.$maximum['blocage'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Artisanat
		</td>
		<td>
			'.genere_image_comp($joueur, 'artisanat', $maximum['artisanat']).' <span class="xsmall">('.$joueur['artisanat'].' / '.$maximum['artisanat'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Architecture
		</td>
		<td>
			'.genere_image_comp($joueur, 'architecture', $maximum['architecture']).' <span class="xsmall">('.$joueur['architecture'].' / '.$maximum['architecture'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Alchimie
		</td>
		<td>
			'.genere_image_comp($joueur, 'alchimie', $maximum['alchimie']).' <span class="xsmall">('.$joueur['alchimie'].' / '.$maximum['alchimie'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Forge
		</td>
		<td>
			'.genere_image_comp($joueur, 'forge', $maximum['forge']).' <span class="xsmall">('.$joueur['forge'].' / '.$maximum['forge'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Identification d\'objets
		</td>
		<td>
			'.genere_image_comp($joueur, 'identification', $maximum['identification']).' <span class="xsmall">('.$joueur['identification'].' / '.$maximum['identification'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Survie
		</td>
		<td>
			'.genere_image_comp($joueur, 'survie', $maximum['survie']).' <span class="xsmall">('.$joueur['survie'].' / '.$maximum['survie'].')</span>
		</td>
	</tr>
	';
	$keys = array_keys($joueur['competences']);
	$i = 0;
	while($i < count($joueur['competences']))
	{
		$numero = (($i % 2) + 1);
		$maximum = recup_max_comp($keys[$i], $joueur['classe_id']);
		echo '
	<tr class="trcolor'.$numero.'">
		<td>
			'.$Gtrad[$keys[$i]].'
		</td>
		<td>
			'.genere_image_comp2($joueur, $keys[$i], $maximum).' <span class="xsmall">('.$joueur['competences'][$keys[$i]].' / '.$maximum.')</span>
		</td>
	</tr>
		';
		$i++;
	}
	echo '
	</table>
	';
		break;

		case 'magie' :
			$maximum['incantation'] = recup_max_comp('incantation', $joueur['classe_id']);
			$maximum['sort_vie'] = recup_max_comp('sort_vie', $joueur['classe_id']);
			$maximum['sort_mort'] = recup_max_comp('sort_mort', $joueur['classe_id']);
			$maximum['sort_element'] = recup_max_comp('sort_element', $joueur['classe_id']);
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor2">
		<td>
			Protection Magique
		</td>
		<td>
			<span onmouseover="return '.make_overlib('PM de base : '.$joueur['PM_base']).'" onmouseout="return nd();">'.$joueur['PM'].'</span> - Réduction des dégâts de '.(round(1 - calcul_pp($joueur['PM'] * $joueur['puissance'] / 12), 2) * 100).' %
		</td>
	</tr>
	<tr class="trcolor1">
		<td style="padding-right : 10px;">
			Réserve de mana
		</td>
		<td>
			<span onmouseover="return '.make_overlib('Réserve de base : '.$joueur['reserve_base']).'" onmouseout="return nd();">'.$joueur['reserve'].'</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td style="padding-right : 10px;">
			Coéf. Incantation
		</td>
		<td>
			'.($joueur['puissance'] * $joueur['incantation']).'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Incantation
		</td>
		<td>
			'.genere_image_comp($joueur, 'incantation', $maximum['incantation']).' <span class="xsmall">('.$joueur['incantation'].' / '.$maximum['incantation'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			'.$Gtrad['sort_vie'].'
		</td>
		<td>
			'.genere_image_comp($joueur, 'sort_vie', $maximum['sort_vie']).' <span class="xsmall">('.$joueur['sort_vie'].' / '.$maximum['sort_vie'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			'.$Gtrad['sort_element'].'
		</td>
		<td>
			'.genere_image_comp($joueur, 'sort_element', $maximum['sort_element']).' <span class="xsmall">('.$joueur['sort_element'].' / '.$maximum['sort_element'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			'.$Gtrad['sort_mort'].'
		</td>
		<td>
			'.genere_image_comp($joueur, 'sort_mort', $maximum['sort_mort']).' <span class="xsmall">('.$joueur['sort_mort'].' / '.$maximum['sort_mort'].')</span>
		</td>
	</tr>
	</table>
	<br />
	<strong>Affinités magiques :</strong><br />
	Magie de la Vie : '.$Gtrad['affinite'.$Trace[$joueur['race']]['affinite_sort_vie']].'<br />
	Magie de la Mort : '.$Gtrad['affinite'.$Trace[$joueur['race']]['affinite_sort_mort']].'<br />
	Magie Elémentaire : '.$Gtrad['affinite'.$Trace[$joueur['race']]['affinite_sort_element']].'<br />
			';
		break;

		case 'stat' :
			if(array_key_exists('action', $_GET) AND $_GET['action'] == 'teleport' AND $joueur['teleport_roi'] == false)
			{
				$requete = "UPDATE perso SET x = ".$Trace[$joueur['race']]['spawn_x'].", y = ".$Trace[$joueur['race']]['spawn_y'].", teleport_roi = 'true' WHERE ID = ".$joueur['ID'];
				$db->query($requete);
			}
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor1">
		<td>
			PP
		</td>
		<td>
			<span onmouseover="return '.make_overlib('PP de base : '.$joueur['PP_base']).'" onmouseout="return nd();">'.$joueur['PP'].'</span> - Réduction des dégâts de '.(round(1 - calcul_pp($joueur['PP']), 4) * 100).' %
		</td>
	</tr>
	<tr class="trcolor2">
		<td style="padding-right : 10px;">
			Dégâts sans arme
		</td>
		<td>
			';
	$i = 0;
	while($i < count($de_degat))
	{
		if ($i > 0) echo ' + ';
		echo '1D'.$de_degat[$i];
		$i++;
	}
	echo '
		</td>
	</tr>
	<tr class="trcolor1">
		<td style="padding-right : 10px;">
			Dégâts avec arme
		</td>
		<td>
			';
	$i = 0;
	while($i < count($de_degat_arme))
	{
		if ($i > 0) echo ' + ';
		echo '1D'.$de_degat_arme[$i];
		$i++;
	}
	echo '
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Coéf. Mélée
		</td>
		<td>
			'.$joueur['coef_melee'].'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Coéf. blocage
		</td>
		<td>
			'.$joueur['coef_blocage'].'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Coéf. distance
		</td>
		<td>
			'.$joueur['coef_distance'].'
		</td>
	</tr>
	</table>
	';
		break;
	}
?>
</fieldset>