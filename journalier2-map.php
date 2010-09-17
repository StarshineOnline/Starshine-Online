<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Vérification si batiments fini de construire
$case = new map_case();
$case->check_case('all');

//On vire le monstres trop vieux
$requete = "DELETE FROM map_monstre WHERE mort_naturelle < ".time();
$db->query($requete);

?>