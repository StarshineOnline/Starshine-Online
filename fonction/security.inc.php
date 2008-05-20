<?php

include_once('reponses.inc.php');

function mydie($p)
{
  global $unit_debug;
  if (!isset($unit_debug) || $unit_debug == false) die($p);
  print "$p \n"; exit (0);
}

// Fonction générique de protection des entrées SQL
function sSQL($data)
{
  // On vire les magic quote si elles sont là
  if (get_magic_quotes_gpc()) {
    if (ini_get('magic_quotes_sybase')) {
      $data = str_replace("''", "'", $data);
    } else {
      $data = stripslashes($data);
    }
  } else {
    $data = $data;
  }
  
  // On protège
  $res = mysql_real_escape_string($data);
  if ($res === FALSE)
    return mysql_error();
  return $res;
}

// Si on veut faire des tests génériques
function validate_sql_value($sql)
{
  if (false) security_block(SQL_INJECTION, 'Bad SQL entry');
}

function validate_integer_value($int)
{
  validate_sql_value($int);
  if (!is_numeric($int)) security_block(SQL_INJECTION, 'Bad SQL entry');
  else { global $unit_debug; if (isset($unit_debug) && $unit_debug) print " OK\n"; }
}

function sub_validate_predicate($query, $expected_result, $msg)
{
  $result = mysql_query($query);
  if (!$result) {
    mydie('Requête invalide : ' . mysql_error());
  }
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if ($row[0] != $expected_result) {
    $smsg = 'Bad Entry: '.$row[0].' != '.$expected_result;
    if (isset($msg) && $msg != '')
      $smsg .= ': '.$msg;
    security_block(BAD_ENTRY, $smsg);
  }
  else { global $unit_debug; if (isset($unit_debug) && $unit_debug) print " OK\n"; }
}

function validate_against_predicate($data, $predicate, $expected_result,
				    $msg=null)
{
  validate_sql_value($data);
  sub_validate_predicate($predicate . sSQL($data), $expected_result, $msg);
}

function validate_against_printf_predicate($data, $predicate, $expected_result,
					   $msg=null)
{
  validate_sql_value($data);
  $query = sprintf($predicate, sSQL($data));
  sub_validate_predicate($query, $expected_result, $msg);
}

global $unit_link;
global $unit_debug;
$unit_debug = false;
function subSSQL($d) { print "[$d] -> [".sSQL($d)."]\n"; }
function subSSQLQ($q) {
  global $unit_link;
  print "query: $q\n";
  $result = mysql_query($q, $unit_link);
  if (!$result) {
    mydie('Requête invalide : ' . mysql_error() . "\n");
  }
  $row = mysql_fetch_assoc($result);
  var_dump($row);
}

function unit_sSQL()
{
	error_log("Entering in unit test, may not appear in production use");
  global $unit_link;
  global $unit_debug;
  $unit_debug = true;
  $unit_link = mysql_connect('localhost', 'sso_test')
    OR mydie(mysql_error());
  mysql_select_db("sso_test", $unit_link) OR mydie(mysql_error($unit_link));
  
  subSSQL('a');
  subSSQL('42');
  subSSQL('\'b\'');
  subSSQL('machin chose -- avec un petit comment');
  subSSQL(sSQL('test'));

  $i = sSQL('1');
  subSSQLQ("select id from vote where id_candidat >= '$i' AND 1=0");
  $i = sSQL('1 -- ');
  subSSQLQ("select id from vote where id_candidat >= '$i' AND 1=0");
  $i = sSQL('1 AND 1=1');
  subSSQLQ("select id from vote where id_candidat >= '$i'");
  
  $i = "3";
  print "validate_integer_value($i)";
  validate_integer_value($i);
  print "validate_against_printf_predicate($i, ...)";
  validate_against_printf_predicate($i, "select count(`id`) from candidat where `id_perso` = '%d'", 1);
  $i = 42;
  print "validate_against_printf_predicate($i, ...)";
  validate_against_printf_predicate($i, "select count(`id`) from candidat where `id_perso` = '%d'", 1);

}

function check_undead_players()
{
  global $joueur;
  if (!isset($joueur) || !$joueur) {
    $joueur = recupperso($_SESSION['ID']);
  }
  global $G_undead_checked;
  if ($G_undead_checked) return;
  $G_undead_checked = true;
  if (substr_compare($_SERVER["SCRIPT_NAME"], 'mort.php', -8, 8) == 0) return;
  if ($joueur['hp'] < 1) {
    ob_end_clean();
    if (substr_compare($_SERVER["SCRIPT_NAME"], 'jeu2.php', -8, 8) == 0)
      print_head();
    verif_mort($joueur, 1);
    exit(0);
  }
}

?>