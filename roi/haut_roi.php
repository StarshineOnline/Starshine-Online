<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

$joueur->check_perso();
//$case = new map_case();
//$case->check_case('all');
//Véifie si le perso est mort
verif_mort($joueur, 1);

$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
$check = false;
if(verif_ville($joueur->get_x(), $joueur->get_y()))
{
	$check = true;
}
elseif($batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $royaume->get_id()))
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg') $check = true; // à remplacer par une vérification des bonus
}

if($check)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
}
else
{
	echo 'INTERDIT';
	exit();
}