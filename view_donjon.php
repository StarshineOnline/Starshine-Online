<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');

echo '<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="/css/index.css" />';

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
?>

<a href="?donjon=myriandre">Myriandre</a><br/>
<a href="?donjon=gobelin">Gobelin</a><br/>
<div>
<?php

   if (isset($_GET['donjon'])) {
     if ($_GET['donjon'] == 'myriandre') {
       $xmin = 1;
       $xmax = 50;
       $ymin = 200;
       $ymax = 221;
       $print = true;
     }
     if ($_GET['donjon'] == 'gobelin') {
       $xmin = 1;
       $xmax = 46;
       $ymin = 250;
       $ymax = 300;
       $print = true;
     }
   }

  if ($print) {
    // print map
    require('class/map.class.php');
    {//-- Initialisation
      $MAP = Array();
    }

    $map = new map($xmin, $ymin, ($xmax - $xmin) / 2 + 2);

    $map->xmin = $xmin;
    $map->xmax = $xmax;
    $map->ymin = $ymin;
    $map->ymax = $ymax;
    
    $map->get_pnj();
    $map->get_joueur();
    $map->get_drapeau();
    $map->get_batiment();
    $map->get_monstre(1);
    
    $map->affiche();

  }

}
?></div>