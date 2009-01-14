<?php
$root = '../';
//Inclusion du haut du document html
include($root.'haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">