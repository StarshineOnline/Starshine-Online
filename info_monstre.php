<?php
include('inc/fp.php');
//L'id du monstre dont on veut l'info
$W_ID = $_GET['ID'];
//Case du monstre dont on veut l'info
//if (!isset($_GET['poscase'])) security_block(URL_MANIPULATION);
$W_case = $_GET['poscase'];
//Prise des infos du perso
$joueur = recupperso($_SESSION['ID']);
$monstre = recupmonstre($W_ID);

$survie = $joueur['survie'];
if($monstre['espece'] == 'bete' AND array_key_exists('survie_bete', $joueur['competences'])) $survie += $joueur['competences']['survie_bete'];
if($monstre['espece'] == 'humanoide' AND array_key_exists('survie_humanoide', $joueur['competences'])) $survie += $joueur['competences']['survie_humanoide'];
if($monstre['espece'] == 'magique' AND array_key_exists('survie_magique', $joueur['competences'])) $survie += $joueur['competences']['survie_magique'];
$pa_attaque = $G_PA_attaque_monstre;
if(array_key_exists('cout_attaque', $joueur['debuff'])) $pa_attaque = ceil($pa_attaque / $joueur['debuff']['cout_attaque']['effet']);
if(array_key_exists('plus_cout_attaque', $joueur['debuff'])) $pa_attaque = $pa_attaque * $joueur['debuff']['plus_cout_attaque']['effet'];
if(array_key_exists('buff_rapidite', $joueur['buff'])) $reduction_pa = $joueur['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
if(array_key_exists('debuff_ralentissement', $joueur['debuff'])) $reduction_pa -= $joueur['debuff']['debuff_ralentissement']['effet'];
$coeff = floor($survie / $monstre['level']);
?>
<fieldset>
	<legend>Information sur <?php echo $monstre['nom'] ?></legend>
<div class="information_case">
	<h4><?php echo $monstre['nom']; ?></h4>
	<p>Niveau : <?php echo $monstre['level']; ?><?php if($coeff >= 7) echo ' - Type : '.$monstre['espece']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php
	if(!array_key_exists('repos_sage', $joueur['debuff']) OR !array_key_exists('bloque_attaque', $joueur['debuff'])) echo '<a href="attaque_monstre.php?ID='.$monstre['id'].'&poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($pa_attaque - $reduction_pa).' PA)</span></a>';
	if($joueur['sort_jeu'] != '') echo ' <a href="sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$monstre['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" style="vertical-align : middle;" /></a>';
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
	$survie = $joueur['survie'];
	if($monstre['espece'] == 'bete' AND array_key_exists('survie_bete', $joueur['competences'])) $survie += $joueur['competences']['survie_bete'];
	if($monstre['espece'] == 'humanoide' AND array_key_exists('survie_humanoide', $joueur['competences'])) $survie += $joueur['competences']['survie_humanoide'];
	if($monstre['espece'] == 'magique' AND array_key_exists('survie_magique', $joueur['competences'])) $survie += $joueur['competences']['survie_magique'];
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
</div>
</fieldset>