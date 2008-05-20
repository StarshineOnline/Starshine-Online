<?php
include('haut.php');

?>
<table border="1">
<tr>
	<td>
		Race
	</td>
	<td>
		Vie
	</td>
	<td>
		Force
	</td>
	<td>
		Dextérité
	</td>
	<td>
		Puissance
	</td>
	<td>
		Volonté
	</td>
	<td>
		Energie
	</td>
	<td>
		Nécro
	</td>
	<td>
		Element
	</td>
	<td>
		Magie Vie
	</td>
	<td>
		Bonus
	</td>
</tr>
<?php

$keys = array_keys($Trace);
$count = count($Trace);
$i = 0;
while($i < $count)
{
	$race = $Trace[$keys[$i]];
	?>
<tr>
	<td>
		<?php echo $keys[$i]; ?>
	</td>
	<td>
		<?php echo $race['vie']; ?>
	</td>
	<td>
		<?php echo $race['force']; ?>
	</td>
	<td>
		<?php echo $race['dexterite']; ?>
	</td>
	<td>
		<?php echo $race['puissance']; ?>
	</td>
	<td>
		<?php echo $race['volonte']; ?>
	</td>
	<td>
		<?php echo $race['energie']; ?>
	</td>
	<td>
		<?php echo $race['affinite_sort_mort']; ?>
	</td>
	<td>
		<?php echo $race['affinite_sort_element']; ?>
	</td>
	<td>
		<?php echo $race['affinite_sort_vie']; ?>
	</td>
	<td>
		<?php echo $race['passif']; ?>
	</td>
</tr>	<?php
	$i++;
}
?>
</table>