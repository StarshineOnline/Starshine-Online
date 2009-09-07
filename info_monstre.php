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
$map_monstre = new map_monstre($W_ID);
$monstre = new monstre($map_monstre->get_type());

$survie = $joueur->get_survie();
if($monstre->get_type() == 'bete' AND $joueur->is_competence('survie_bete')) $survie += $joueur->get_competence('survie_bete');
if($monstre->get_type() == 'humanoide' AND $joueur->is_competence('survie_humanoide')) $survie += $joueur->get_competence('survie_humanoide');
if($monstre->get_type() == 'magique' AND $joueur->is_competence('survie_magique')) $survie += $joueur->get_competence('survie_magique');
$pa_attaque = $G_PA_attaque_monstre;
if($joueur->is_buff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $joueur->get_buff('cout_attaque', 'effet'));
if($joueur->is_buff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $joueur->get_buff('plus_cout_attaque', 'effet');
if($joueur->is_buff('buff_rapidite')) $reduction_pa = $joueur->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
if($joueur->is_buff('debuff_ralentissement')) $reduction_pa -= $joueur->get_buff('debuff_ralentissement', 'effet');
$coeff = floor($survie / $monstre->get_level());
?>
<fieldset>
	<legend>Information sur <?php echo $monstre->get_nom() ?></legend>
	<p>Niveau : <?php echo $monstre->get_level(); ?><?php if($coeff >= 7) echo ' - Type : '.$monstre->get_type(); ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php
	if(!$joueur->is_buff('repos_sage') OR !$joueur->is_buff('bloque_attaque')) echo '<a href="attaque.php?type=monstre&id_monstre='.$map_monstre->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer ('.($pa_attaque - $reduction_pa).' PA)" style="vertical-align : middle;" /> </a>';
	if($joueur->get_sort_jeu() != '') echo ' <a href="sort.php?id_monstre='.$monstre->get_id().'&amp;type=monstre" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" style="vertical-align : middle;" /></a>';
	echo '<a href="informationcase.php?case='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/retour.png" alt="Retour" title="Retour à l\'information case" style="vertical-align : middle;" /></a>';
	echo '<br />';
	//Listing des debuffs
	if($coeff >= 10)
	{
		foreach($map_monstre->get_buff() as $buff)
		{
			echo '<img src="image/buff/'.$buff->get_type().'_p.png" alt="'.$buff->get_type().'" onmouseover="'.make_overlib('<strong>'.$buff->get_nom().'</strong><br />'.$buff->get_description().'<br />Durée '.transform_sec_temp($buff->get_fin() - time())).'" onmouseout="return nd();" />';
		}
	}
	?>
	</p>
	<?php
	if($monstre->get_level() > 0) $level = $monstre->get_level(); else $level = 1;
	$nbr_barre_total = ceil($survie / $level);
	if($nbr_barre_total < 1) $nbr_barre_total = 1;
	if($nbr_barre_total > 100) $nbr_barre_total = 100;
	$nbr_barre = round(($map_monstre->get_hp() / $monstre->get_hp()) * $nbr_barre_total);
	$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
	if($longueur < 0) $longueur = 0;
	$fiabilite = round((100 / $nbr_barre_total), 2);
	?>
				<img src="genere_barre_vie.php?longueur=<?php echo $longueur; ?>" title="Estimation des HP : <?php echo $longueur; ?>% / + ou - : <?php echo $fiabilite; ?>%" /><br />
	<?php
	//Description
	if($coeff >= 5) echo '
	<p class="xsmall">'.$monstre->get_description().'</p>';
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
		<td onmouseover="<?php echo make_overlib('Réduction des dégâts de '.(round(1 - calcul_pp($monstre->get_pp()), 4) * 100).' %'); ?>" onmouseout="nd();">
			<?php echo $monstre->get_pp(); ?>
		</td>
		<?php
		if($coeff >= 12)
		{
		?>
		<td>
			PM
		</td>
		<td onmouseover="<?php echo make_overlib('Réduction des dégâts de '.(round(1 - calcul_pp($monstre->get_pm() * $monstre->get_puissance() / 12), 2) * 100).' %'); ?>" onmouseout="nd();">
			<?php echo $monstre->get_pm(); ?>
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
			<?php echo $monstre->get_reserve(); ?>
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
			15
		</td>
		<td rowspan="4" colspan="7">
			<img src="image/monstre/<?php echo $monstre->get_lib(); ?>.png">
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
			<?php echo $monstre->get_forcex(); ?>
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
			<?php echo $monstre->get_dexterite(); ?>
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
			<?php echo $monstre->get_puissance(); ?>
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
			<?php echo $monstre->get_volonte(); ?>
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
			<?php echo $monstre->get_melee(); ?>
		</td>
	</tr>
	<tr class="trcolor1">
		<td>
			Incantation
		</td>
		<td>
			<?php echo $monstre->get_incantation(); ?>
		</td>
	</tr>
	<?php
	}
	?>
	</table>

</fieldset>
