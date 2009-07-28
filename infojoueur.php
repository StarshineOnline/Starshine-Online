<?php
if (file_exists('root.php'))
  include_once('root.php');


include_once(root.'inc/fp.php');
//L'id du joueur dont on veut l'info
$W_ID = $_GET['ID'];
//Prise des infos du perso
$joueur = new perso($_SESSION['ID']);
$perso = new perso($W_ID);
$W_case = convert_in_pos($perso->get_x(), $perso->get_y());
$bonus = recup_bonus($W_ID);
$bonus_total = recup_bonus_total($perso->get_id());
$vue = 4;
if ($joueur->get_x() + $vue < $perso->get_x() || $perso->get_x() < $joueur->get_x() - $vue ||
    $joueur->get_y() + $vue < $perso->get_y() || $perso->get_y() < $joueur->get_y() - $vue) {
  if ($joueur->get_groupe() != $perso->get_groupe() || $perso->get_groupe() == '0')
    security_block(URL_MANIPULATION);
}
?>
<fieldset>
	<legend>Information Joueur</legend>
<?php
//affichage des informations du joueur dont on veut l'info
if(array_key_exists(6, $bonus) AND !check_affiche_bonus($bonus[6], $joueur, $perso)) $chaine_nom = $perso->get_nom();
else $chaine_nom = $perso->get_grade()->get_nom().' '.$perso->get_nom();
if(array_key_exists(7, $bonus) AND !check_affiche_bonus($bonus[7], $joueur, $perso)) $classe = 'xxxxx';
else $classe = $perso->get_classe();
if(array_key_exists(11, $bonus) AND !check_affiche_bonus($bonus[11], $joueur, $perso)) $niveau = 'xx';
else $niveau = $perso->get_level();
?>
<div id="info_case">
<h4><span class="titre_info"><?php echo $chaine_nom; ?></span></h4>

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


/*$perso['lignee'] = recupperso_lignee($perso->get_id());
if($perso['lignee'] != 0)
{
	$lignee = recup_lignee($perso['lignee']);
	echo ' - '.$lignee['nom'];
}*/
?>
<?php
echo $Gtrad[$perso->get_race()];
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
Distance du joueur : <?php echo calcul_distance(convert_in_pos($joueur->get_x(), $joueur->get_y()), $W_case); ?> / Methode pythagorienne : <?php echo calcul_distance_pytagore(convert_in_pos($joueur->get_x(), $joueur->get_y()), $W_case); ?>
<h4><span class="titre_info">Actions</span></h4>
<table>
<?php
$W_distance = detection_distance($W_case, $_SESSION["position"]);
if (($perso->get_id() != $_SESSION['ID']))
{
	$pa_attaque = $G_PA_attaque_joueur;
	if($joueur->get_race() == $perso->get_race()) $pa_attaque += 3;
	if($joueur->is_debuff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $joueur->get_debuff('cout_attaque', 'effet'));
	if($joueur->is_debuff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $joueur->get_debuff('plus_cout_attaque', 'effet');
	if($joueur->is_buff('buff_rapidite')) $reduction_pa = $joueur->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($joueur->is_debuff('debuff_ralentissement')) $reduction_pa -= $joueur->get_debuff('debuff_ralentissement', 'effet');
	echo '<tr><td><img src="image/message.png" title="Envoyer un message" /></td><td><a href="envoimessage.php?id_type=p'.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')">Envoyer un message</a></td></tr>';
	if($perso->get_hp() > 0 AND !$joueur->is_debuff('repos_sage') OR !$joueur->is_debuff('bloque_attaque')) echo '<tr><td><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /></td><td><a href="attaque.php?ID='.$W_ID.'&amp;poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"> Attaquer</a><span class="xsmall"> ('.($pa_attaque - $reduction_pa).' PA)</span></td></tr>';
}
if($joueur->get_sort_jeu() != '')
{
	if($perso->get_id() != $_SESSION['ID'])
	{
		echo '<tr><td><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></td><td><a href="sort_joueur.php?id_joueur='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')">Lancer un sort</a></td></tr>';
	}
	else
	{
		echo '<tr><td><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></td><td><a href="sort.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')">Lancer un sort</a></td></tr>';
	}
}

if (($W_distance < 2) AND ($W_ID != $_SESSION['ID']) AND ($perso->get_groupe() != $joueur->get_groupe() OR $joueur->get_groupe() == '' OR $joueur->get_groupe() == 0))
{
	echo('<tr><td><img src="image/interface/demande_groupe.png" alt="Inviter ce joueur dans votre groupe" title="Inviter ce joueur dans votre groupe" style="vertical-align : middle;" /></td><td><a href="invitegroupe.php?ID='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"> Inviter ce joueur dans votre groupe</a></td></tr>');
}
//Voir l'inventaire
if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
{
	echo('<tr><td></td><td><a href="inventaire.php?id_perso='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"> Voir l\'inventaire de ce joueur</a></td></tr>');
}
//Voir les caractéristiques
if(array_key_exists(23, $bonus) AND check_affiche_bonus($bonus[23], $joueur, $perso))
{
	echo('<tr><td></td><td><a href="personnage.php?id_perso='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"> Voir les caractéristiques de ce joueur</a></td></tr>');
}
if(!isset($groupe)) { $groupe = new groupe($joueur->get_groupe(), ""); };


if(($perso->get_groupe() == $joueur->get_groupe()) AND ($groupe->get_leader() == $joueur->get_id()))
{
	echo('<tr><td><img src="image/interface/exspuler-joueur_icone.png" alt="Expulser le joueur" title="Expulser le joueur" /></td><td><a style="cursor:pointer;" onclick="javascript:if(confirm(\'Voulez vous expulser ce joueur ?\')) envoiInfo(\'kickjoueur.php?ID='.$perso->get_id().'&groupe='.$groupe['id'].'\', \'information\');">Expulser la personne du groupe</a></td></tr>');
}


echo '</table>';



//Affichage des buffs du joueur
if($joueur->get_groupe() == $perso->get_groupe() && $joueur->get_groupe() !== 0 && $joueur->get_groupe() != '')
{
	if (count($perso->get_buff()) != 0 || count($perso->get_debuff()) != 0)
	{
		echo '<h4><span class="titre_info">Buffs / Debuffs</span></h4>';
		//Listing des buffs
		foreach($perso['buff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" ondblclick="if(confirm(\'Voulez vous supprimer '.$buff['nom'].' ?\')) envoiInfo(\'suppbuff.php?id='.$buff['id'].'\', \'perso\');" onmouseover="'.make_overlib('<strong>'.$buff['nom'].'</strong><br />'.description($buff['description'], $buff).'<br />Durée '.transform_sec_temp($buff['fin'] - time())).'" onmouseout="return nd();" />';
		}
		if(count($perso['debuff']) > 0) echo '<br />';
		//Listing des debuffs
		foreach($perso['debuff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" onmouseover="'.make_overlib('<strong>'.$buff['nom'].'</strong><br />'.description($buff['description'], $buff).'<br />Durée '.transform_sec_temp($buff['fin'] - time())).'" onmouseout="return nd();" />';
		}
	}
}

$titres = recup_titre_honorifique($perso->get_id());
if(!empty($titres) AND array_key_exists(15, $bonus) AND check_affiche_bonus($bonus[15], $joueur, $perso))
{
	?>

		<h4><span class="titre_info">Titres</span></h4>
	<?php
	foreach($titres as $titre)
	{
		echo $titre.'<br />';
	}
}
if(array_key_exists(16, $bonus) AND check_affiche_bonus($bonus[16], $joueur, $perso))
{
	?>

		<h4><span class="titre_info">Description</span></h4>
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
		$bonus_total = recup_bonus_total($perso->get_id());
		echo nl2br($bonus_total[16]['valeur']);
}
?>
</fieldset>
