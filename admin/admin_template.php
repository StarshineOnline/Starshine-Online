<?php // -*- mode: php; tab-width: 2 -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

/* add_*_to_head(); */

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');
echo '<div style="margin-left: 200px">';

/* site */

echo '</div>';
include_once(root.'admin/admin_bas.php');

?>