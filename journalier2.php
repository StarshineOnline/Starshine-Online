<?php
if (file_exists('root.php')) {
  include_once('root.php');
}

include_once('journalier2-head.php');

include_once('journalier2-perso.php');

include_once('journalier2-map.php');

// Sauvegarde stats
include_once('journalier2-stats.php');

// PV, entretien
include_once('journalier2-royaumes.php');

// Géneration stats
echo "Création des images statistiques\n";
require_once('stats/stat_lvl.php');
require_once('stats/stat_race.php');
require_once('stats/stat_classe.php');

// Carte du monde
include_once('journalier2-map-monde.php');

// Cartes des royaumes
include_once('journalier2-map-royaumes.php');

// Carte pose des drapeaux
include_once('journalier2-map-pose-drapeaux.php');

// Carte de la densité mobs
include_once('journalier2-map-mobs.php');

// Mises en archives
include_once('journalier2-archives.php');

// Calcul des recettes
include_once('journalier2-recettes.php');

// Stars des nouveaux joueurs
include_once('journalier2-misc.php');

require_once('stats/stat_star.php');
require_once('stats/stat_autres.php');

// Elections et revolutions
include_once('journalier2-elections.php');

// Enchères
include_once('journalier2-encheres.php');

// Grades
include_once('journalier2-grades.php');

$mail_send = getenv('SSO_MAIL');
if ($mail_send == null || $mail_send == '') $mail_send = 'starshineonline@gmail.com';
mail($mail_send, 'Starshine - Script journalier du '.$date, $mail);

echo "Done\n";

?>