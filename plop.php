<?php
function pourcent_reussite($base, $difficulte)
{
	if($base > $difficulte)
	{
		$chance = ($difficulte * ($difficulte + 1)) / 2;
		$chance += ($base - $difficulte) * ($difficulte + 1);
	}
	else $chance = ($base * ($base + 1)) / 2;
	$total = ($difficulte + 1) * ($base + 1);
	$chance_reussite = 100 * round(($chance / $total), 4);
	return $chance_reussite;
}

echo pourcent_reussite($_GET['att'], $_GET['deff']).' ';

$tab_diff = array(100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200);
$tab_inc = array(50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600);
?>
<table border="1" cellspacing="0">
	<tr>
		<td>
			Incantation / Inc. Necessaire
		</td>
<?php
foreach($tab_diff as $diff)
{
	echo '
	<td>
		<strong>'.($diff / 2).'</strong>
	</td>
	';
}
?>
	</tr>
<?php
foreach($tab_inc as $inc)
{
	$pot_mag = $inc * 2;
	echo '
	<tr>
		<td>
			<strong>'.$inc.'</strong>
		</td>
		';
	foreach($tab_diff as $diff)
	{
		$de_pot = 100;
		$de_diff = 100;
		if($pot_mag > $diff) $de_pot += ($pot_mag - $diff);
		else $de_diff += ($diff - $pot_mag);
		echo '
		<td>
			'.pourcent_reussite($de_pot, $de_diff).'
		</td>
		';
	}
	echo '
	</tr>';
}
?>
</table>
<br />
Actuellement :<br />
<table border="1" cellspacing="0">
	<tr>
		<td>
			Incantation / Inc. Necessaire
		</td>
<?php
foreach($tab_diff as $diff)
{
	echo '
	<td>
		<strong>'.($diff / 2).'</strong>
	</td>
	';
}
?>
	</tr>
<?php
foreach($tab_inc as $inc)
{
	$pot_mag = $inc * 1.3333333;
	echo '
	<tr>
		<td>
			<strong>'.$inc.'</strong>
		</td>
		';
	foreach($tab_diff as $diff)
	{
		echo '
		<td>
			'.pourcent_reussite($pot_mag, $diff).'
		</td>
		';
	}
	echo '
	</tr>';
}
?>
</table>