<?php
if (file_exists('root.php'))
  include_once('root.php');
$interface_v2 =true;
include_once(root.'haut.php');
//include_once(root.'inc/verif_log_admin.inc.php');
?>
<a href="admin_2.php">Revenir à l'administration</a>
<?php
$map = new map(95, 95, 95);

$map->get_pnj();
$map->get_joueur('neutre');
$map->get_drapeau();
$map->get_batiment();
$map->onclick_status = false;
//$map->get_monstre($level);

//if(isset($_GET['cache_monstre'])) $map->change_cache_monstre();

$map->affiche();
?>