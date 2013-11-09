<?php //  -*- tab-width:2; mode: php  -*-                                                                                                                                                                          
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

include_once(root.'inc/fp.php');

add_css_to_head('../css/datatables.css');

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

if ($G_maintenance)
{
  echo 'Starshine-online est actuellement en cours de mis à jour.<br /> 
  le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
  exit (0);
}

include_once(root.'admin/menu_admin.php');

$req = $db->param_query('select nom,variable from achievement_type', array());
while ($req && $row = $db->stmt_read_assoc($req)) {
  $rows[] = $row;
}

print_dataTables(array('Nom', 'Code'), $rows);

include_once(root.'bas.php');
