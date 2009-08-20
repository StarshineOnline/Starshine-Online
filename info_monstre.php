<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//L'id du monstre dont on veut l'info
$W_ID = $_GET['ID'];
//Case du monstre dont on veut l'info
//if (!isset($_GET['poscase'])) security_block(URL_MANIPULATION);
$W_case = $_GET['poscase'];
//Prise des infos du perso
$joueur = new perso($_SESSION['ID']);
$monstre = recupmonstre($W_ID);

$survie = $joueur->get_survie();
if($monstre['espece'] == 'bete' AND $joueur->is_competence('survie_bete')) $survie += $joueur->get_competence('survie_bete', 'effet');
if($monstre['espece'] == 'humanoide' AND $joueur->is_competence('survie_humanoide')) $survie += $joueur->get_competence('survie_humanoide');
if($monstre['espece'] == 'magique' AND $joueur->is_competence('survie_magique')) $survie += $joueur->get_competence('survie_magique');
$pa_attaque = $G_PA_attaque_monstre;
if($joueur->is_debuff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $joueur->get_debuff('cout_attaque', 'effet'));
if($joueur->is_debuff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $joueur->get_debuff('plus_cout_attaque');
if($joueur->is_buff('buff_rapidite')) $reduction_pa = $joueur->get_buff('buff_rapidite'); else $reduction_pa = 0;
if($joueur->is_debuff('debuff_ralentissement')) $reduction_pa -= $joueur->get_debuff('debuff_ralentissement');
$coeff = floor($survie / $monstre['level']);
?>
<fieldset>
	<legend>Information sur <?php echo $monstre['nom'] ?></legend>
	<p>Niveau : <?php echo $monstre['level']; ?><?php if($coeff >= 7) echo ' - Type : '.$monstre['espece']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php
	if(!$joueur->is_debuff('repos_sage') OR !$joueur->is_debuff('bloque_attaque')) echo '<a href="attaque_monstre.php?ID='.$monstre['id'].'&poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer ('.($pa_attaque - $reduction_pa).' PA)" style="vertical-align : middle;" /> </a>';
	if($joueur->get_sort_jeu() != '') echo ' <a href="sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$monstre['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" style="vertical-align : middle;" /></a>';
	echo '<a href="informationcase.php?case='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/retour.png" alt="Retour" title="Retour à l\'information case" style="vertical-align : middle;" /></a>';
	echo '<br />';
	//Listing des debuffs
	if($coeff >= 10)
	{
		foreach($monstre['debuff'] as $buff)
		{
			echo '<img src="image/buff/'.$buff['type'].'_p.png" alt="'.$buff['type'].'" onmouseover="'.make_overlib('<strong>'.$buff['nom'].'</strong><br />'.$buff['description'].'<br />Durée '.transform_sec_temp($buff['fin'] - time())).'" onmouseout="return nd();" />';
		}
	}
	?>
	</p>
	<?php
	if($monstre['level'] > 0) $level = $monstre['level']; else $level = 1;
	$nbr_barre_total = ceil($survie / $level);
	if($nbr_barre_total < 1) $nbr_barre_total = 1;
	if($nbr_barre_total > 100) $nbr_barre_total = 100;
	$nbr_barre = round(($monstre['hp'] / $monstre['hp_max_1']) * $nbr_barre_total);
	$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
	if($longueur < 0) $longueur = 0;
	$fiabilite = round((100 / $nbr_barre_total), 2);
	?>
				<img src="genere_barre_vie.php?longueur=<?php echo $longueur; ?>" title="Estimation des HP : <?php echo $longueur; ?>% / + ou - : <?php echo $fiabilite; ?>%" /><br />
	<?php
	//Description
	if($coeff >= 5) echo '
	<p class="xsmall">'.$monstre['description'].'</p>';
	?>
	<table style="border : 0px;" cellspacing="0" width="100%">
	<?php
	if($coeff >= 11)
	{
	?>
	<tr class="trcolor1">
		<td>
			PP
		</td>
		<td onmouseover="<?php echo make_overlib('Réduction des dégâts de '.(round(1 - calcul_pp($monstre['PP']), 4) * 100).' %'); ?>" onmouseout="nd();">
			<?php echo $monstre['PP']; ?>
		</td>
		<?php
		if($coeff >= 12)
		{
		?>
		<td>
			PM
		</td>
		<td onmouseover="<?php echo make_overlib('Réduction des dégâts de '.(round(1 - calcul_pp($monstre['PM'] * $monstre['puissance'] / 12), 2) * 100).' %'); ?>" onmouseout="nd();">
			<?php echo $monstre['PM']; ?>
		</td>
		<?php
		}
		if($coeff >= 14)
		{
		?>
		<td>
			RM
		</td>
		<td>
			<?php echo $monstre['reserve']; ?>
		</td>
		<?php
		}
		?>
	</tr>
	<?php
	}
	if($coeff >= 13)
	{
	?>
	<tr class="trcolor1">
		<td>
			Constitution
		</td>
		<td>
			<?php echo $monstre['vie']; ?>
		</td>
		<td rowspan="4" colspan="7">
			<img src="image/monstre/<?php echo $monstre['image']; ?>">
		</td>
	</tr>
	<?php
	}
	if($coeff >= 17)
	{
	?>
	<tr class="trcolor2">
		<td>
			Force
		</td>
		<td>
			<?php echo $monstre['force']; ?>
		</td>
	</tr>
	<?php
	}
	if($coeff >= 19)
	{
	?>
	<tr class="trcolor1">
		<td>
			Dextérité
		</td>
		<td>
			<?php echo $monstre['dexterite']; ?>
		</td>
	</tr>
	<?php
	}
	if($coeff >= 17)
	{
	?>
	<tr class="trcolor2">
		<td>
			Puissance
		</td>
		<td>
			<?php echo $monstre['puissance']; ?>
		</td>
	</tr>
	<?php
	}
	if($coeff >= 19)
	{
	?>
	<tr class="trcolor1">
		<td>
			Volonté
		</td>
		<td>
			<?php echo $monstre['volonte']; ?>
		</td>
	</tr>
	<?php
	}
	if($coeff >= 21)
	{
	?>
	<tr class="trcolor2">
		<td>
			Mêlée
		</td>
		<td>
			<?php echo $monstre['melee']; ?>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Incantation
		</td>
		<td>
			<?php echo $monstre['incantation']; ?>
		</td>
	</tr>
	<?php
	}
	?>
	</table>

</fieldset>