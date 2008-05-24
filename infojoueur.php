<?php
include('inc/fp.php');
//L'id du joueur dont on veut l'info
$W_ID = $_GET['ID'];
//Case du joueur dont on veut l'info
if (!isset($_GET['poscase'])) security_block(URL_MANIPULATION);
$W_case = $_GET['poscase'];
//Prise des infos du perso
$joueur = recupperso($_SESSION['ID']);
$perso = recupperso($W_ID);
$bonus = recup_bonus($W_ID);
$bonus_total = recup_bonus_total($perso['ID']);
$vue = 4;
if ($joueur['x'] + $vue < $perso['x'] || $perso['x'] < $joueur['x'] - $vue ||
    $joueur['y'] + $vue < $perso['y'] || $perso['y'] < $joueur['y'] - $vue) {
  if ($joueur['groupe'] != $perso['groupe'] || $perso['groupe'] == '0')
    security_block(URL_MANIPULATION);
}
?>
<h2>Information Joueur</h2>
<?php
//affichage des informations du joueur dont on veut l'info
if(array_key_exists('buff_rapidite', $joueur['buff'])) $reduction_pa = $joueur['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
if(array_key_exists(6, $bonus) AND !check_affiche_bonus($bonus[6], $joueur, $perso)) $chaine_nom = $perso['nom'];
else $chaine_nom = $perso['grade'].' '.$perso['nom'];
if(array_key_exists(7, $bonus) AND !check_affiche_bonus($bonus[7], $joueur, $perso)) $classe = 'xxxxx';
else $classe = $perso['classe'];
if(array_key_exists(11, $bonus) AND !check_affiche_bonus($bonus[11], $joueur, $perso)) $niveau = 'xx';
else $niveau = $perso['level'];
?>
<div class="information_case">
<?php
//Avatar
if(array_key_exists(19, $bonus) AND check_affiche_bonus($bonus[19], $joueur, $perso))
{
	$fichier = 'image/avatar/'.$bonus_total[19]['valeur'];
	if(is_file($fichier))
	{
	?>
	<div class="avatar"><img src="<?php echo $fichier; ?>" /></div>
	<?php
	}
}
?>
<h4><?php echo $chaine_nom; ?></h4>
<?php
/*$perso['lignee'] = recupperso_lignee($perso['ID']);
if($perso['lignee'] != 0)
{
	$lignee = recup_lignee($perso['lignee']);
	echo ' - '.$lignee['nom'];
}*/
?>
<?php
echo $Gtrad[$perso['race']];
//Sexe
if(array_key_exists(12, $bonus) AND check_affiche_bonus($bonus[12], $joueur, $perso))
{
	switch($bonus_total[12]['valeur'])
	{
		case '0' :
			$sexe = 'Indéfini';
		break;
		case '1' :
			$sexe = 'Masculin';
		break;
		case '2' :
			$sexe = 'Féminin';
		break;
	}
	echo ' - '.$sexe;
}
?><br />
<?php echo ucfirst($classe); ?> - niveau <?php echo $niveau; ?><br />
Distance du joueur : <?php echo calcul_distance(convert_in_pos($joueur['x'], $joueur['y']), $W_case); ?> / Methode pythagorienne : <?php echo calcul_distance_pytagore(convert_in_pos($joueur['x'], $joueur['y']), $W_case); ?>
</div>
<div class="information_case">
<table>
<?php
$W_distance = detection_distance($W_case, $_SESSION["position"]);
if (($perso['ID'] != $_SESSION['ID']))
{
	echo '<tr><td><img src="image/message.png" title="Envoyer un message" /></td><td><a href="javascript:envoiInfo(\'envoimessage.php?ID='.$W_ID.'\', \'information\')">Envoyer un message</a></td></tr>';
	if($perso['hp'] > 0 AND !array_key_exists('repos_sage', $joueur['debuff'])) echo '<tr><td><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /></td><td><a href="javascript:envoiInfo(\'attaque.php?ID='.$W_ID.'&amp;poscase='.$W_case.'\', \'information\')"> Attaquer</a><span class="xsmall"> ('.($G_PA_attaque_joueur - $reduction_pa).' PA)</span></td></tr>';
}
if($joueur['sort_jeu'] != '')
{
	if($perso['ID'] != $_SESSION['ID'])
	{
		echo '<tr><td><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></td><td><a href="javascript:envoiInfo(\'sort_joueur.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'\', \'information\')">Lancer un sort</a></td></tr>';
	}
	else
	{
		echo '<tr><td><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></td><td><a href="javascript:envoiInfo(\'sort.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'\', \'information\')">Lancer un sort</a></td></tr>';
	}
}

if (($W_distance < 2) AND ($W_ID != $_SESSION['ID']) AND ($perso['groupe'] != $joueur['groupe'] OR $joueur['groupe'] == '' OR $joueur['groupe'] == 0))
{
	echo('<tr><td><img src="image/demande_groupe.png" alt="Inviter ce joueur dans votre groupe" title="Inviter ce joueur dans votre groupe" style="vertical-align : middle;" /></td><td><a href="javascript:envoiInfo(\'invitegroupe.php?ID='.$W_ID.'\', \'information\')"> Inviter ce joueur dans votre groupe</a></td></tr>');
}
//Voir l'inventaire
if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
{
	echo('<tr><td></td><td><a href="javascript:envoiInfo(\'inventaire.php?id_perso='.$W_ID.'\', \'information\')"> Voir l\'inventaire de ce joueur</a></td></tr>');
}
//Voir les caractéristiques
if(array_key_exists(23, $bonus) AND check_affiche_bonus($bonus[23], $joueur, $perso))
{
	echo('<tr><td></td><td><a href="javascript:envoiInfo(\'personnage.php?id_perso='.$W_ID.'\', \'information\')"> Voir les caractéristiques de ce joueur</a></td></tr>');
}
echo '</table>';
//Affichage des buffs du joueur
if($joueur['groupe'] == $perso['groupe'])
{
	if ($perso['buff'] != NULL)
	{
		echo '<br />Buffs / Debuffs<br />';
		//Listing des buffs
		foreach($perso['buff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" ondblclick="if(confirm(\'Voulez vous supprimer '.$buff['nom'].' ?\')) envoiInfo(\'suppbuff.php?id='.$buff['id'].'\', \'perso\');" onmouseover="'.make_overlib('<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Durée '.transform_sec_temp($buff['fin'] - time())).'" onmouseout="return nd();" />';
		}
		if(count($perso['debuff']) > 0) echo '<br />';
		//Listing des debuffs
		foreach($perso['debuff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" onmouseover="'.make_overlib('<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Durée '.transform_sec_temp($buff['fin'] - time())).'" onmouseout="return nd();" />';
		}
	}
}

echo '</div>';
$titres = recup_titre_honorifique($perso['ID']);
if(!empty($titres) AND array_key_exists(15, $bonus) AND check_affiche_bonus($bonus[15], $joueur, $perso))
{
	?>
	<div class="information_case">
		<h4>Titres</h4>
	<?php
	foreach($titres as $titre)
	{
		echo $titre.'<br />';
	}
	?>
	</div>
	<?php
}
if(array_key_exists(16, $bonus) AND check_affiche_bonus($bonus[16], $joueur, $perso))
{
	?>
	<div class="information_case">
		<h4>Description</h4>
		<?php
		//Inclusion du css
		if(array_key_exists(27, $bonus) AND $bonus_total[27]['valeur'] != '0')
		{
			?>
			<style type="text/css" media="screen">
			<?php
			echo $bonus_total[27]['valeur'];
			?>
			</style>
			<?php
		}
		$bonus_total = recup_bonus_total($perso['ID']);
		echo nl2br($bonus_total[16]['valeur']);
		?>
	</div>
	<?php
}
?>
</div>