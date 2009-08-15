<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

check_perso($joueur);

//Véifie si le perso est mort
verif_mort($joueur, 1);

$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
