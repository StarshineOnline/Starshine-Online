<?php

for($i = 10; $i <= 500; $i = $i + 10)
{
	$reduction = (round(10000 * (sqrt($i / 10) / 40))) / 100;
	echo $i.' | '.$reduction.' %<br />';
}

?>