<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Verification des joueurs inactifs
$requete = "UPDATE perso SET statut = 'inactif' WHERE dernier_connexion <= ".(time() - (86400 * 21))." AND statut = 'actif'";
$db->query($requete);

//Réduction de l'honneur pour tous les joueurs
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.02) WHERE honneur <= reputation";
$db->query($requete);
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.03) WHERE honneur > reputation";
$db->query($requete);

//Point de crime -1
$requete = "UPDATE perso SET crime = IF(crime - 1 < 0, 0, crime -1) WHERE crime > 0";
$db->query($requete);

?>