<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
$ress = array();
	$ress['Plaine']['Pierre'] = 4;
	$ress['Plaine']['Bois'] = 5;
	$ress['Plaine']['Eau'] = 6;
	$ress['Plaine']['Sable'] = 2;
	$ress['Plaine']['Nourriture'] = 6;
	$ress['Plaine']['Star'] = 0;
	$ress['Plaine']['Charbon'] = 1;
	$ress['Plaine']['Essence Magique'] = 0;
	
	$ress['Forêt']['Pierre'] = 2;
	$ress['Forêt']['Bois'] = 8;
	$ress['Forêt']['Eau'] = 5;
	$ress['Forêt']['Sable'] = 0;
	$ress['Forêt']['Nourriture'] = 4;
	$ress['Forêt']['Star'] = 0;
	$ress['Forêt']['Charbon'] = 2;
	$ress['Forêt']['Essence Magique'] = 3;
	
	$ress['Désert']['Pierre'] = 7;
	$ress['Désert']['Bois'] = 0;
	$ress['Désert']['Eau'] = 0;
	$ress['Désert']['Sable'] = 10;
	$ress['Désert']['Nourriture'] = 2;
	$ress['Désert']['Star'] = 0;
	$ress['Désert']['Charbon'] = 2;
	$ress['Désert']['Essence Magique'] = 5;
	
	$ress['Montagne']['Pierre'] = 9;
	$ress['Montagne']['Bois'] = 4;
	$ress['Montagne']['Eau'] = 3;
	$ress['Montagne']['Sable'] = 6;
	$ress['Montagne']['Nourriture'] = 2;
	$ress['Montagne']['Star'] = 0;
	$ress['Montagne']['Charbon'] = 1;
	$ress['Montagne']['Essence Magique'] = 1;
	
	$ress['Marais']['Pierre'] = 1;
	$ress['Marais']['Bois'] = 2;
	$ress['Marais']['Eau'] = 1;
	$ress['Marais']['Sable'] = 3;
	$ress['Marais']['Nourriture'] = 3;
	$ress['Marais']['Star'] = 0;
	$ress['Marais']['Charbon'] = 4;
	$ress['Marais']['Essence Magique'] = 8;
	
	$ress['Terre Maudite']['Pierre'] = 3;
	$ress['Terre Maudite']['Bois'] = 3;
	$ress['Terre Maudite']['Eau'] = 0;
	$ress['Terre Maudite']['Sable'] = 2;
	$ress['Terre Maudite']['Nourriture'] = 2;
	$ress['Terre Maudite']['Star'] = 0;
	$ress['Terre Maudite']['Charbon'] = 8;
	$ress['Terre Maudite']['Essence Magique'] = 5;
	
	$ress['Glace']['Pierre'] = 1;
	$ress['Glace']['Bois'] = 1;
	$ress['Glace']['Eau'] = 10;
	$ress['Glace']['Sable'] = 0;
	$ress['Glace']['Nourriture'] = 3;
	$ress['Glace']['Star'] = 0;
	$ress['Glace']['Charbon'] = 2;
	$ress['Glace']['Essence Magique'] = 6;
	
	$ress['Route']['Pierre'] = 0;
	$ress['Route']['Bois'] = 0;
	$ress['Route']['Eau'] = 0;
	$ress['Route']['Sable'] = 0;
	$ress['Route']['Nourriture'] = 0;
	$ress['Route']['Star'] = 40;
	$ress['Route']['Charbon'] = 0;
	$ress['Route']['Essence Magique'] = 0;
	
?>