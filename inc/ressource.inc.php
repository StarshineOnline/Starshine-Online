<?php
if (file_exists('../root.php'))
	include_once('../root.php');

$Gress = array();

$Gress['Plaine']['Pierre'] = 4;
$Gress['Plaine']['Bois'] = 5;
$Gress['Plaine']['Eau'] = 6;
$Gress['Plaine']['Sable'] = 2;
$Gress['Plaine']['Nourriture'] = 6;
$Gress['Plaine']['Star'] = 0;
$Gress['Plaine']['Charbon'] = 1;
$Gress['Plaine']['Essence Magique'] = 0;

$Gress['Forêt']['Pierre'] = 2;
$Gress['Forêt']['Bois'] = 8;
$Gress['Forêt']['Eau'] = 5;
$Gress['Forêt']['Sable'] = 0;
$Gress['Forêt']['Nourriture'] = 4;
$Gress['Forêt']['Star'] = 0;
$Gress['Forêt']['Charbon'] = 2;
$Gress['Forêt']['Essence Magique'] = 3;

$Gress['Désert']['Pierre'] = 7;
$Gress['Désert']['Bois'] = 0;
$Gress['Désert']['Eau'] = 0;
$Gress['Désert']['Sable'] = 10;
$Gress['Désert']['Nourriture'] = 2;
$Gress['Désert']['Star'] = 0;
$Gress['Désert']['Charbon'] = 2;
$Gress['Désert']['Essence Magique'] = 5;

$Gress['Montagne']['Pierre'] = 9;
$Gress['Montagne']['Bois'] = 4;
$Gress['Montagne']['Eau'] = 3;
$Gress['Montagne']['Sable'] = 6;
$Gress['Montagne']['Nourriture'] = 2;
$Gress['Montagne']['Star'] = 0;
$Gress['Montagne']['Charbon'] = 1;
$Gress['Montagne']['Essence Magique'] = 1;

$Gress['Marais']['Pierre'] = 1;
$Gress['Marais']['Bois'] = 2;
$Gress['Marais']['Eau'] = 1;
$Gress['Marais']['Sable'] = 3;
$Gress['Marais']['Nourriture'] = 3;
$Gress['Marais']['Star'] = 0;
$Gress['Marais']['Charbon'] = 4;
$Gress['Marais']['Essence Magique'] = 8;

$Gress['Terre Maudite']['Pierre'] = 3;
$Gress['Terre Maudite']['Bois'] = 3;
$Gress['Terre Maudite']['Eau'] = 0;
$Gress['Terre Maudite']['Sable'] = 2;
$Gress['Terre Maudite']['Nourriture'] = 2;
$Gress['Terre Maudite']['Star'] = 0;
$Gress['Terre Maudite']['Charbon'] = 8;
$Gress['Terre Maudite']['Essence Magique'] = 5;

$Gress['Glace']['Pierre'] = 1;
$Gress['Glace']['Bois'] = 1;
$Gress['Glace']['Eau'] = 10;
$Gress['Glace']['Sable'] = 0;
$Gress['Glace']['Nourriture'] = 3;
$Gress['Glace']['Star'] = 0;
$Gress['Glace']['Charbon'] = 2;
$Gress['Glace']['Essence Magique'] = 6;

$Gress['Route']['Pierre'] = 0;
$Gress['Route']['Bois'] = 0;
$Gress['Route']['Eau'] = 0;
$Gress['Route']['Sable'] = 0;
$Gress['Route']['Nourriture'] = 0;
$Gress['Route']['Star'] = 40;
$Gress['Route']['Charbon'] = 0;
$Gress['Route']['Essence Magique'] = 0;