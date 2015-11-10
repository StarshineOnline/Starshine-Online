<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

require_once(root.'inc/fp.php');

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
$update = '';
$requete = "SELECT id_thread FROM messagerie_thread;";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$requete = "SELECT titre FROM messagerie_message WHERE id_thread = ".$row['id_thread']." ORDER BY date ASC LIMIT 0, 1";
	$req_m = $db->query($requete);
	$row_m = $db->read_assoc($req_m);
	$update .= "UPDATE messagerie_thread SET titre = '".sSQL($row_m['titre'])."' WHERE id_thread = ".$row['id_thread'].";
	<br />";
}
echo $update;