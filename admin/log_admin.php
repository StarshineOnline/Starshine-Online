<?php
// TODO: navigation, outils ...

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

echo '<div><h1>Logs admin enregistrés</h1>';
echo log_admin::display_all(false, 100);
echo '</div>';
echo '</div>';

print_foot();
?>
