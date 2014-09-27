<?php
// ??
if (file_exists('root.php'))
  include_once('root.php');

$site = true;
include_once(root.'haut_ajax.php');

define('MUC', '@conference.jabber.starshine-online.com');

$stmt = $db->param_query('select race, rang_royaume from perso where nom = ?', array($_GET['user']), 's');
$ret = $db->stmt_read_object($stmt);
$chats = array('sso' . MUC);
if ($ret) {
	$chats[] = $ret->race . MUC;
	if ($ret->rang_royaume == 6)
		$chats[] = 'roi' . MUC;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://forum.starshine-online.com');
echo json_encode(implode(',', $chats));
