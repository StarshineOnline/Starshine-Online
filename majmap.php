<?php
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
$requete = "SELECT id, info, royaume FROM map2;";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$update = "UPDATE map SET info = '".$row['info']."', royaume = '".$row['royaume']."' WHERE id = ".$row['id'];
	$db->query($update);
}