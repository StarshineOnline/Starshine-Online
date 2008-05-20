<?php

include('haut.php');
include('menu.php');
include('fonction/base.inc.php');

//Insertion des différentes pages

if (isset($_GET['page']))
{
	switch($_GET['page'])
	{
		case 'deplacement' :
			include('deplacement.php');
		break;
	}
}

//Affichage de la map.
?>

<div id="map">

<?php
include('map.php');
?>

</div>

<?php

if (isset($_GET['page']))
{
	switch($_GET['page'])
	{
		case 'perso' :
			include('perso.php');
		break;
	}
}

//Affichage du bas de page
include('bas.php');

?>