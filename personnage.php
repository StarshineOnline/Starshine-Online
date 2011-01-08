<?php // -*- php -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
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
$joueur = new perso($joueur_id);
$joueur->check_perso();
$de_degat = de_degat($joueur->get_forcex(), 0);
$de_degat_arme = de_degat($joueur->get_forcex(), $joueur->get_arme_degat());

echo '
<fieldset>
	<legend>Nom : '.$joueur->get_nom().'</legend>
	<p class="brillant"><a href="'.$adresse.'direction=carac" onclick="return envoiInfo(this.href, \'information\')">Carac</a> | <a href="'.$adresse.'direction=comp" onclick="return envoiInfo(this.href, \'information\')">Compétences</a> | <a href="'.$adresse.'direction=magie" onclick="return envoiInfo(this.href, \'information\')">Magie</a> | <a href="'.$adresse.'direction=stat" onclick="return envoiInfo(this.href, \'information\')">Stats</a> | <a href="'.$adresse.'direction=achiev" onclick="return envoiInfo(this.href, \'information\')">Achievement</a></p>
	<p><strong>'.$joueur->get_nom().'</strong> - '.$Gtrad[$joueur->get_race()].' - '.$joueur->get_classe().'</p>
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
			'.$joueur->get_vie().'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Force
		</td>
		<td>
			'.$joueur->get_forcex().'
		</td>
	</tr>';
	$prochainpa = (($joueur->get_dernieraction() + $G_temps_PA) - time());
	if($prochainpa < 0) $prochainpa = 0;
	$prochainpa_m = floor($prochainpa / 60);
	$prochainpa_s = $prochainpa - ($prochainpa_m * 60);

	// Gemme du troll
	if ($joueur->is_enchantement('regeneration'))
	{
		$bonus_regen = $joueur->get_enchantement('regeneration', 'effet') * 60;
		if ($G_temps_regen_hp <= $bonus_regen)
		{
			$bonus_regen = $G_temps_regen_hp - 1;
		}
	} else $bonus_regen = 0;

	$prochainregen = (($joueur->get_regen_hp() + ($G_temps_regen_hp - $bonus_regen)) - time());
	$prochainregen_h = floor($prochainregen / 3600);
	$prochainregen_m = floor(($prochainregen - ($prochainregen_h * 3600)) / 60);
	$prochainregen_s = $prochainregen - ($prochainregen_h * 3600) - ($prochainregen_m * 60);

	//echo strftime("%d/%m/%Y %H:%M", $joueur['maj_hp']);
	$prochainmaj = (($joueur->get_maj_hp() + $G_temps_maj_hp) - time());
	$prochainmaj_j = floor($prochainmaj / (3600 * 24));
	$prochainmaj = $prochainmaj - ($prochainmaj_j * 3600 * 24);
	$prochainmaj_h = floor($prochainmaj / 3600);
	$prochainmaj_m = floor(($prochainmaj - ($prochainmaj_h * 3600)) / 60);
	$prochainmaj_s = $prochainmaj - ($prochainmaj_h * 3600) - ($prochainmaj_m * 60);

	$prochainmajm = (($joueur->get_maj_mp() + $G_temps_maj_mp) - time());
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
			'.$joueur->get_dexterite().'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Puissance
		</td>
		<td>
			'.$joueur->get_puissance().'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Volonté
		</td>
		<td>
			'.$joueur->get_volonte().'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Energie
		</td>
		<td>
			'.$joueur->get_energie().'<br />
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
		if($joueur->get_teleport_roi() != 'true' AND !$visu)
		{
			echo '<a href="personnage.php?direction=stat&action=teleport" onclick="if(confirm(\'Voulez vous vraiment vous téléportez sur votre capitale ?\')) return envoiInfo(this.href, \'information\'); else return false;">Se téléporter dans votre capitale</a>';
		}
		break;

		case 'comp' :
			$maximum['melee'] = recup_max_comp('melee', $joueur->get_classe_id());
			$maximum['distance'] = recup_max_comp('distance', $joueur->get_classe_id());
			$maximum['esquive'] = recup_max_comp('esquive', $joueur->get_classe_id());
			$maximum['blocage'] = recup_max_comp('blocage', $joueur->get_classe_id());
			$maximum['dressage'] = recup_max_comp('dressage', $joueur->get_classe_id());
			$maximum['artisanat'] = 123;
			$maximum['architecture'] = recup_max_comp('architecture', $joueur->get_classe_id());
			$maximum['alchimie'] = recup_max_comp('alchimie', $joueur->get_classe_id());
			$maximum['forge'] = recup_max_comp('forge', $joueur->get_classe_id());
			$maximum['identification'] = recup_max_comp('identification', $joueur->get_classe_id());
			$maximum['survie'] = recup_max_comp('survie', $joueur->get_classe_id());
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor1">
		<td>
			Mêlée
		</td>
		<td>
			'.genere_image_comp($joueur->get_melee(), 'melee', $maximum['melee']).' <span class="xsmall">('.$joueur->get_melee().' / '.$maximum['melee'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Tir à distance
		</td>
		<td>
			'.genere_image_comp($joueur->get_distance(), 'distance', $maximum['distance']).' <span class="xsmall">('.$joueur->get_distance().' / '.$maximum['distance'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Esquive
		</td>
		<td>
			'.genere_image_comp($joueur->get_esquive(), 'esquive', $maximum['esquive']).' <span class="xsmall">('.$joueur->get_esquive().' / '.$maximum['esquive'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Blocage
		</td>
		<td>
			'.genere_image_comp($joueur->get_blocage(), 'blocage', $maximum['blocage']).' <span class="xsmall">('.$joueur->get_blocage().' / '.$maximum['blocage'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Dressage
		</td>
		<td>
			'.genere_image_comp($joueur->get_dressage(), 'dressage', $maximum['dressage']).' <span class="xsmall">('.$joueur->get_dressage().' / '.$maximum['dressage'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Artisanat
		</td>
		<td>
			'.genere_image_comp($joueur->get_artisanat(), 'artisanat', $maximum['artisanat']).' <span class="xsmall">('.$joueur->get_artisanat().' / '.$maximum['artisanat'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Architecture
		</td>
		<td>
			'.genere_image_comp($joueur->get_architecture(), 'architecture', $maximum['architecture']).' <span class="xsmall">('.$joueur->get_architecture().' / '.$maximum['architecture'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Alchimie
		</td>
		<td>
			'.genere_image_comp($joueur->get_alchimie(), 'alchimie', $maximum['alchimie']).' <span class="xsmall">('.$joueur->get_alchimie().' / '.$maximum['alchimie'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Forge
		</td>
		<td>
			'.genere_image_comp($joueur->get_forge(), 'forge', $maximum['forge']).' <span class="xsmall">('.$joueur->get_forge().' / '.$maximum['forge'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Identification d\'objets
		</td>
		<td>
			'.genere_image_comp($joueur->get_identification(), 'identification', $maximum['identification']).' <span class="xsmall">('.$joueur->get_identification().' / '.$maximum['identification'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Survie
		</td>
		<td>
			'.genere_image_comp($joueur->get_survie(), 'survie', $maximum['survie']).' <span class="xsmall">('.$joueur->get_survie().' / '.$maximum['survie'].')</span>
		</td>
	</tr>
	';
	$keys = array_keys($joueur->get_competence());
	$i = 0;
	foreach($joueur->get_competence() as $comp)
	{
		$numero = (($i % 2) + 1);
		$maximum = recup_max_comp($comp->get_competence(), $joueur->get_classe_id());
		echo '
	<tr class="trcolor'.$numero.'">
		<td>
			'.$Gtrad[$comp->get_competence()].'
		</td>
		<td>
			'.genere_image_comp2($comp->get_valeur(), $comp->get_competence(), $maximum).' <span class="xsmall">('.$comp->get_valeur().' / '.$maximum.')</span>
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
			$maximum['incantation'] = recup_max_comp('incantation', $joueur->get_classe_id());
			$maximum['sort_vie'] = recup_max_comp('sort_vie', $joueur->get_classe_id());
			$maximum['sort_mort'] = recup_max_comp('sort_mort', $joueur->get_classe_id());
			$maximum['sort_element'] = recup_max_comp('sort_element', $joueur->get_classe_id());
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor2">
		<td>
			Protection Magique
		</td>
		<td>
			<span onmouseover="return '.make_overlib('PM de base : '.$joueur->get_pm(true)).'" onmouseout="return nd();">'.$joueur->get_pm().'</span> - Réduction des dégâts de '.(round(1 - calcul_pp($joueur->get_pm() * $joueur->get_puissance() / 12), 2) * 100).' %
		</td>
	</tr>
	<tr class="trcolor1">
		<td style="padding-right : 10px;">
			Réserve de mana
		</td>
		<td>
			<span onmouseover="return '.make_overlib('Réserve de base : '.$joueur->get_reserve(true)).'" onmouseout="return nd();">'.$joueur->get_reserve_bonus().'</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td style="padding-right : 10px;">
			Coéf. Incantation
		</td>
		<td>
			'.($joueur->get_puissance() * $joueur->get_incantation()).'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Incantation
		</td>
		<td>
			'.genere_image_comp($joueur->get_incantation(), 'incantation', $maximum['incantation']).' <span class="xsmall">('.$joueur->get_incantation().' / '.$maximum['incantation'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			'.$Gtrad['sort_vie'].'
		</td>
		<td>
			'.genere_image_comp($joueur->get_sort_vie(), 'sort_vie', $maximum['sort_vie']).' <span class="xsmall">('.$joueur->get_sort_vie().' / '.$maximum['sort_vie'].')</span>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			'.$Gtrad['sort_element'].'
		</td>
		<td>
			'.genere_image_comp($joueur->get_sort_element(), 'sort_element', $maximum['sort_element']).' <span class="xsmall">('.$joueur->get_sort_element().' / '.$maximum['sort_element'].')</span>
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			'.$Gtrad['sort_mort'].'
		</td>
		<td>
			'.genere_image_comp($joueur->get_sort_mort(), 'sort_mort', $maximum['sort_mort']).' <span class="xsmall">('.$joueur->get_sort_mort().' / '.$maximum['sort_mort'].')</span>
		</td>
	</tr>
	</table>
	<br />
	<strong>Affinités magiques :</strong><br />
	Magie de la Vie : '.$Gtrad['affinite'.$Trace[$joueur->get_race()]['affinite_sort_vie']].'<br />
	Magie de la Mort : '.$Gtrad['affinite'.$Trace[$joueur->get_race()]['affinite_sort_mort']].'<br />
	Magie Elémentaire : '.$Gtrad['affinite'.$Trace[$joueur->get_race()]['affinite_sort_element']].'<br />
			';
		break;

		case 'stat' :
			if(array_key_exists('action', $_GET) AND $_GET['action'] == 'teleport'
				 AND ($joueur->get_teleport_roi() == false OR $joueur->get_teleport_roi() == 'false' OR $joueur->get_teleport_roi() == ''))
			{
				$joueur->set_x($Trace[$joueur->get_race()]['spawn_x']);
				$joueur->set_y($Trace[$joueur->get_race()]['spawn_y']);
				$joueur->set_teleport_roi('true');
				$joueur->sauver();
				print_reload_area('deplacement.php?deplacement=centre', 'centre');
			}
			echo '
	<table style="border : 0px;" cellspacing="0" width="100%">
	<tr class="trcolor1">
		<td>
			PP
		</td>
		<td>
			<span onmouseover="return '.make_overlib('PP de base : '.$joueur->get_pp(true)).'" onmouseout="return nd();">'.$joueur->get_pp().'</span> - Réduction des dégâts de '.(round(1 - calcul_pp($joueur->get_pp()), 4) * 100).' %
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
			'.$joueur->get_coef_melee().'
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Coéf. blocage
		</td>
		<td>
			'.$joueur->get_coef_blocage().'
		</td>
	</tr>
	<tr class="trcolor2">
		<td>
			Coéf. distance
		</td>
		<td>
			'.$joueur->get_coef_distance().'
		</td>
	</tr>
	</table>
	';
		break;
		case 'achiev' :	
			$color = true;
		
			$achievements = $joueur->get_achievement();
			if($achievements != NULL)
			{
				echo '<table style="border : 0px;" cellspacing="0" width="100%">';
				echo '<strong>Debloqués :</strong>';
				foreach($achievements as $achiev)
				{
					if($color) $style = 1;
					else $style = 2;
					$color = !$color;
					$description = description($achiev['description'], $achiev);
					echo '<tr class="trcolor'.$style.'"><td style="';
					if ($achiev['color'] != '') echo 'color: '.$achiev['color'].'; ';
					if ($achiev['strong']) echo 'font-weight: bold; ';
					echo '"><span onmouseover="return '.make_overlib($description).
						'" onmouseout="return nd();">'.$achiev['nom'].'</span></td></tr>';
				}
				echo '</table><hr />';
			}
			
			/*echo '<strong>Non-Debloqués :</strong>';
			echo '<table style="border : 0px;" cellspacing="0" width="100%">';
			$requete = "SELECT * FROM achievement_type ORDER BY nom ASC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				if(!array_key_exists($row['id'], $achievements) AND $row['secret'] != 1)
				{
					if($color) $style = 1;
					else $style = 2;
					$color = !$color;
					$description = description($row['description'], $row);
					echo '<tr class="trcolor'.$style.'"><td><span onmouseover="return '.make_overlib($description).'" onmouseout="return nd();">'.$row['nom'].'</span></td></tr>';
				}
			}
			echo '</table>';*/
		break;
	}
?>
</fieldset>
