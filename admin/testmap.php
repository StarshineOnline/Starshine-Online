<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin =true;
include_once(root.'admin/admin_haut.php');

if (!array_key_exists('droits', $_SESSION) or
    !($_SESSION['droits'] & joueur::droit_interf_admin)) {
  header("HTTP/1.1 401 Unauthorized" );
  exit();
}

?>
<a href="admin_2.php">Revenir à l'administration</a>
<div class="mapedit">
	<?php
	$map = new map(95, 95, 95,'../');

	$map->get_pnj();
	$map->get_joueur('neutre');
	$map->get_drapeau();
	$map->get_batiment();
	$map->onclick_status = false;
	//$map->get_monstre($level);

	//if(isset($_GET['cache_monstre'])) $map->change_cache_monstre();

	$map->affiche();
	?>
</div>