<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion du haut du document html
$connexion = true;

//Inclusion du haut du document html
include_once(root.$root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);

$joueur->check_perso();

//Véifie si le perso est mort
verif_mort($joueur, 1);

$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);

$food_necessaire = floor($food_necessaire * $royaume->get_habitants() * 0.95) + floor($royaume->get_food() * 0.05);
?>
<div style='width:300px;float:left;'>

	<strong>Stars du royaume : </strong><?php echo $royaume->get_star(); ?><br />
	<strong>Taux de taxe</strong> : <?php echo $royaume->get_taxe(); ?>% <br />
	<strong>Habitants</strong> : <?php echo $royaume->get_habitants(); ?> <br />
</div>
<div style='width:300px;float:left;'>
	<strong>Habitants très actifs</strong> : <?php echo $royaume->get_habitants_actif(); ?><br />
	<strong>Nourriture</strong> : <?php echo $royaume->get_food(); ?><br />
	<strong>Nourriture nécessaire</strong> : <?php echo $food_necessaire; ?>
</div>

<div style='width:270px;float:left;'>
	<span class='bois' title='Bois' style='padding-left:40px;'><?php echo $royaume->get_bois(); ?></span>
	<span class='eau' title='Eau' style='padding-left:40px;'><?php echo $royaume->get_eau(); ?></span>
	<span class='essence' title='Essence Magique' style='padding-left:40px;'><?php echo $royaume->get_essence(); ?></span>
	<span class='pierre' title='Pierre' style='padding-left:40px;'><?php echo $royaume->get_pierre(); ?></span>
	<span class='sable' title='Sable' style='padding-left:40px;'><?php echo $royaume->get_sable(); ?></span>
	<span class='charbon' title='Charbon' style='padding-left:40px;'><?php echo $royaume->get_charbon(); ?></span>
</div>
