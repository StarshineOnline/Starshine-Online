<?php
if (file_exists('root.php'))
  include_once('root.php');
  include_once(root.'inc/fp.php');

?><div id="carte">
<?php

$RqMapTxt = "SELECT id,info FROM map 
				 ORDER BY id;";
$RqMap = $db->query($RqMapTxt);
while($objMap = $db->read_object($RqMap))
{
	echo $objMap->id;


}

?>

	
</div>