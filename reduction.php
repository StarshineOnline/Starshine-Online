<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

for($i = 10; $i <= 500; $i = $i + 10)
{
	$reduction = (round(10000 * (sqrt($i / 10) / 40))) / 100;
	echo $i.' | '.$reduction.' %<br />';
}

?>