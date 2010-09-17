<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

echo 'Création du dossier '.$date.'<br />';
if(mkdir('image/stat/'.$date, 0777))
  echo 'Répertoire '.$date.' créé<br />';
else
  echo 'Le répertoire '.$date.' existe déjà<br />';

echo 'Déplacement des anciennes images dans le nouveau dossier<br />';
copy('image/carte.png', 'image/stat/'.$date.'/carte.png');
copy('image/carte_royaume.png', 'image/stat/'.$date.'/carte_royaume.png');
copy('image/carte_densite_mob.png', 'image/stat/'.$date.'/carte_densite_mob.png');
copy('image/stat_lvl.png', 'image/stat/'.$date.'/stat_lvl.png');
copy('image/stat_race.png', 'image/stat/'.$date.'/stat_race.png');
copy('image/stat_classe1.png', 'image/stat/'.$date.'/stat_classe1.png');
copy('image/stat_classe2.png', 'image/stat/'.$date.'/stat_classe2.png');
copy('image/stat_classe3.png', 'image/stat/'.$date.'/stat_classe3.png');
copy('image/stat_classe4.png', 'image/stat/'.$date.'/stat_classe4.png');
copy('image/stat_star1.png', 'image/stat/'.$date.'/stat_star1.png');
copy('image/stat_star2.png', 'image/stat/'.$date.'/stat_star2.png');
copy('image/stat_star3.png', 'image/stat/'.$date.'/stat_star3.png');
copy('image/stat_joueur.png', 'image/stat/'.$date.'/stat_joueur.png');
copy('image/stat_monstre.png', 'image/stat/'.$date.'/stat_monstre.png');
copy('image/stat_niveau_moyen.png', 'image/stat/'.$date.'/stat_niveau_moyen.png');

?>