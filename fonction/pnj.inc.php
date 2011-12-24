<?php // -*- mode: php; tab-width:2 -*-

/*
 * Fonctions destinées à être utilisées dans les dialogues PNJ
 * utilise le tag [run:nom_fnct] pour lancer la fonction pnj_run_nom_fnct
 * renvoie un texte qui remplacera le tag à l'affichage
 */

function pnj_run_cache_cache()
{
  global $joueur;
  global $db;
  // incrémente d'un le compteur de quête si elle est active
  $quetes = $joueur->get_liste_quete();
  if ($quetes) {
    $req = $db->query("select id from quete where nom = 'Cache-cache'");
    if ($req && $row = $db->read_object($req))
      $ID_QUETE = $row->id;
    foreach ($quetes as $k => &$q) {
      if ($q['id_quete'] == $ID_QUETE) {
        $q['objectif'][0]->nombre++;
        $joueur->set_quete(serialize($quetes));
        if (verif_quete($q['id_quete'], $k, $joueur))
          fin_quete($joueur, $k, $q['id_quete']);
        $joueur->sauver();
      }
    }
  }
  // fait se cacher le gosse ailleurs
  $pos = array(array('x' => 37, 'y' => 335), array('x' => 31, 'y' => 334),
               array('x' => 45, 'y' => 331), array('x' => 34, 'y' => 337),
               array('x' => 40, 'y' => 333), array('x' => 34, 'y' => 329));
  $i = 0;
  do
  {
    $i = rand(0, count($pos) - 1);
    $x = $pos[$i]['x'];
    $y = $pos[$i]['y'];
  } while ($x == $joueur->get_x() && $y == $joueur->get_y());
  $db->query("update pnj set x = $x, y = $y where nom = 'Riky Tendre-flocon'");
  return "Bravo ! Tu m'as trouvé ! Mais y arriveras-tu de nouveau ?<br />".
    "<em>Riky s'enfuit en courant pour se cacher ailleurs</em>";
}

function pnj_run_tp_devant_cesar($x, $y)
{
  global $joueur;
  global $db;
	$joueur->set_x($x);
	$joueur->set_y($y);
}

function pnj_if_test_true(&$joueur)
{
  return true;
}

function pnj_if_test_false(&$joueur)
{
  return false;
}
