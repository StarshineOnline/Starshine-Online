<?php // -*- mode: php -*-
if (file_exists('../root.php'))
  include_once('../root.php');


include_once(root.'fonction/reponses.inc.php');

function mydie($p)
{
  global $unit_debug;
  if (!isset($unit_debug) || $unit_debug == false) die($p);
  print "$p \n"; exit (0);
}

define('SSQL_INTEGER', 0);
define('SSQL_FLOAT', 1);
define('SSQL_STRING', 2);
define('SSQL_NONE', -1);

// Fonction générique de protection des entrées SQL
function sSQL($data, $type = SSQL_NONE)
{
  global $db;

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

  switch ($type)
  {
    case SSQL_INTEGER:
      validate_integer_value($data);
      $data = intval($data);
      break;
    case SSQL_FLOAT:
      validate_numeric_value($data);
      $data = floatval($data);
      break;
    case SSQL_STRING:
      validate_sql_value($data);
      $data = strval($data);
      break;
    case SSQL_NONE:
      break; // No type test
  }
  
  // On protège
  $res = $db->escape($data);
  if ($res === FALSE) {
    echo $db->error();
    exit (1);
  }
  return $res;
}

// Si on veut faire des tests génériques
function validate_sql_value($sql)
{
  if (is_object($sql) || is_array($sql) || false)
    security_block(SQL_INJECTION, 'Bad SQL entry');
}

function validate_integer_value($int)
{
  validate_sql_value($int);
  if (!is_numeric($int)) security_block(SQL_INJECTION, 'Bad SQL entry');
  else { global $unit_debug; if (isset($unit_debug) && $unit_debug) print " OK\n"; }
}

function validate_numeric_value($int)
{
  validate_sql_value($int);
  if (!is_numeric($int)) security_block(SQL_INJECTION, 'Bad SQL entry');
  else { global $unit_debug; if (isset($unit_debug) && $unit_debug) print " OK\n"; }
}

function sub_validate_predicate($query, $expected_result, $msg)
{
  global $db;
  $result = $db->query($query);
  if (!$result) {
    mydie('Requête invalide : ' . $db->error());
  }
  $row = $db->read_array($result, MYSQLI_NUM);
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
  $result = mysqli_query($q, $unit_link);
  if (!$result) {
    mydie('Requête invalide : ' . mysqli_error($unit_link) . "\n");
  }
  $row = mysqli_fetch_assoc($result);
  var_dump($row);
}

function unit_sSQL()
{
  error_log("Entering in unit test, may not appear in production use");
  global $unit_link;
  global $unit_debug;
  $unit_debug = true;
  $unit_link = mysqli_connect('localhost', 'sso_test')
    OR mydie(mysqli_connect_error());
  mysqli_select_db($unit_link, "sso_test") OR mydie(mysqli_error($unit_link));
  
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

function check_undead_players($ajax = false)
{
  global $G_undead_checked;
  if ($G_undead_checked) return;
  $G_undead_checked = true;
  if (strlen($_SERVER["SCRIPT_NAME"]) >= 8) // Si on met ==, autant faire == 'mort.php'
    if (substr_compare($_SERVER["SCRIPT_NAME"], 'mort.php', -8, 8) == 0)
      return;
  if (strlen($_SERVER["SCRIPT_NAME"]) >= 9)
    if (substr_compare($_SERVER["SCRIPT_NAME"], 'index.php', -9, 9) == 0)
      return;
  if (strlen($_SERVER["SCRIPT_NAME"]) >= 10)
    if (substr_compare($_SERVER["SCRIPT_NAME"], 'create.php', -10, 10) == 0)
      return;
  global $joueur;
  if (!isset($joueur) || !$joueur) {
    $joueur = new perso($_SESSION['ID']);
  }
  if ($joueur->get_hp() < 1) {
    ob_end_clean();
		if ($ajax) {
			print_js_onload("location.href = 'interface.php'");
		}
		else {
			if (strlen($_SERVER["SCRIPT_NAME"]) >= 8)
				if (substr_compare($_SERVER["SCRIPT_NAME"], 'interface.php', -8, 8) == 0)
					print_head();
			verif_mort($joueur, 1);
		}
    exit(0);
  }
}

function check_existing_account($new_account, $perso = true, $joueur = false, $login = false, $id_perso_exception = 0) {
  global $db;
  $count = 0;

  $accnt = sSQL($new_account);

  if($perso)
  {
  $requete = "select id from perso where replace(nom, ' ', '_') = replace('$accnt', ' ', '_') AND id <> '$id_perso_exception'";
  $req = $db->query($requete);
  while ($row = $db->read_row($req))
    $count++;
   }

  if($joueur)
  {
  $requete = "select id from joueur where replace(pseudo, ' ', '_') = replace('$accnt', ' ', '_')";
  $req = $db->query($requete);
  while ($row = $db->read_row($req))
    $count++;
   }

   if($login)
   {
  $requete = "select id from joueur where replace(login, ' ', '_') = replace('$accnt', ' ', '_')";
  $req = $db->query($requete);
  while ($row = $db->read_row($req))
    $count++;
   }

  $admin = '';

  $requete = "select nom from jabber_admin where replace(nom, ' ', '_') = replace('$accnt', ' ', '_')";
  $req = $db->query($requete);
  while ($row = $db->read_row($req)) {
    $admin .= "Tentative de spoof de pseudo admin: '$new_account' \n";
    $admin .= print_r($_SERVER, true);
    $count++;
  }

  if ($count > 0 && $admin != '') {
    $mail_send = 'starshineonline@gmail.com';
    //$mail_send = 'bastien@geekwu.org';
    mail($mail_send, 'Starshine - spoof de nom admin', $mail);
  }

  return $count;
}

?>
