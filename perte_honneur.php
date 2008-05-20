<?php
$i = 0;
$somme = 10000;
while($i < 100)
{
	$somme = ($somme -  ($somme * 3 / 100));
	$i++;
	echo $i.' - '.$somme.'<br />';
}
?>