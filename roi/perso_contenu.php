<?php
//Inclusion du haut du document html
$connexion = true;
$root = '../';
//Inclusion du haut du document html
include_once($root.'inc/fp.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Véifie si le perso est mort
verif_mort($joueur, 1);

$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);

$W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND statut = 'actif'";
$W_req = $db->query($W_requete);
$W_row = $db->read_row($W_req);
$h = $W_row[0];
$semaine = time() - (3600 * 24 * 7);

$W_requete = "select sum(level)/count(id) moy from perso WHERE statut = 'actif'";
$W_req = $db->query($W_requete);
$W_row = $db->read_row($W_req);
$ref_ta = min(3, floor($W_row[0]));

$W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND level > $ref_ta AND dernier_connexion > ".$semaine." AND statut = 'actif'";
$W_req = $db->query($W_requete);
$W_row = $db->read_row($W_req);
$hta = $W_row[0];
$food_necessaire = floor($food_necessaire * $h);
?>
<div style='width:300px;float:left;'>

	<strong>Stars du royaume : </strong><?php echo $R['star']; ?><br />
	<strong>Taux de taxe</strong> : <?php echo $R['taxe_base']; ?>% <br />
	<strong>Habitants</strong> : <?php echo $h; ?> <br />
</div>
<div style='width:300px;float:left;'>
	<strong>Habitants très actifs</strong> : <?php echo $hta; ?><br />
	<strong>Nourriture</strong> : <?php echo $R['food']; ?><br />
	<strong>Nourriture nécessaire</strong> : <?php echo $food_necessaire; ?>
</div>

<div style='width:270px;float:left;'>
	<span class='bois' title='Bois'><?php echo $R['bois']; ?></span>
	<span class='eau' title='Eau'><?php echo $R['eau']; ?></span>
	<span class='essence' title='Essence Magique'><?php echo $R['essence']; ?></span>
	<span class='pierre' title='Pierre'><?php echo $R['pierre']; ?></span>
	<span class='sable' title='Sable'><?php echo $R['sable']; ?></span>
	<span class='charbon' title='Charbon'><?php echo $R['charbon']; ?></span>
</div>
