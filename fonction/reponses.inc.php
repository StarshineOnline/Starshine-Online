<?php
if (file_exists('../root.php'))
  include_once('../root.php');


define('URL_MANIPULATION', 1);
define('SQL_INJECTION', 2);
define('BAD_ENTRY', 3);

// Envoie une erreur 403 avec le message personnalisé passé en paramètre
function send_forbidden($message=null)
{
  global $unit_debug;
  if (isset($unit_debug) && $unit_debug == true) {
    echo "send_forbidden($message);\n";
    return;
  }
  @ob_end_clean();
  header('HTTP/1.0 403 Forbidden');
  echo '<html><head>403 - Forbidden</head><body><h1>Forbidden</h1><p>';
  if (isset($message) && $message != '') echo $message;
  else echo 'Vous ne pouvez pas acc&egrave;der &agrave; cette page';
  echo '</body></html>';
  exit (1);
}

// Gère un problème de sécurité suivant son type
function security_block($type, $msg='')
{
  switch ($type)
    {
    case URL_MANIPULATION:
      send_forbidden('Gros malin, va ! '.$msg);
      break;
    case SQL_INJECTION:
      send_forbidden('Gros malin, va ! '.$msg);
      break;
    case BAD_ENTRY:
      send_forbidden('Gros malin, va ! '.$msg);
      break;
    default:
      log_error("security_block: $type non reconnu");
      send_forbidden($msg);
      break;
    }
  global $unit_debug;
  if (isset($unit_debug) && $unit_debug == true) return ;
  exit (1);
}
?>