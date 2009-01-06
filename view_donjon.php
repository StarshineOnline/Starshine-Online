<?php
$textures = false;
$interface_v2 = true;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');

echo '<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="/css/index.css" />';
echo '<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="/css/interfacev2.css" />';

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
<table><tr><td><div id="carte">
<?php

   if (isset($_GET['donjon'])) {
     if ($_GET['donjon'] == 'myriandre') {
       $xmin = 1;
       $xmax = 25;
       $ymin = 197;
       $ymax = 221;
       $print = true;
     }
     if ($_GET['donjon'] == 'gobelin') {
       $xmin = 1;
       $xmax = 47;
       $ymin = 250;
       $ymax = 296;
       $print = true;
     }
   }

  if ($print) {
    // print map
    require('class/map.class.php');
    {//-- Initialisation
      $MAP = Array();
    }

    $map = new map($xmin, $ymin, ($xmax - $xmin) / 2);

    $map->xmin = $xmin;
    $map->xmax = $xmax;
    $map->ymin = $ymin;
    $map->ymax = $ymax;
    
    $map->get_pnj();
    $map->get_joueur();
    $map->get_drapeau();
    //$map->get_batiment();
    $map->get_monstre(1);
    
    $map->affiche();

  }

}
?></div></td></tr></table>