<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');

$diplomatie = array();
$diplomatie[0][0] = 'Alliance fraternelle';
$diplomatie[1][0] = 'Alliance';
$diplomatie[2][0] = 'Paix durable';
$diplomatie[3][0] = 'Paix';
$diplomatie[4][0] = 'En bons termes';
$diplomatie[5][0] = 'Neutre';
$diplomatie[6][0] = 'Mauvais termes';
$diplomatie[7][0] = 'Guerre';
$diplomatie[8][0] = 'Guerre durable';
$diplomatie[9][0] = 'Ennemis';
$diplomatie[10][0] = 'Ennemis eternels';
$diplomatie[127][0] = 'X';
$diplomatie[0][1] = 'Très Faible';
$diplomatie[1][1] = 'Faible';
$diplomatie[2][1] = 'Normale';
$diplomatie[3][1] = 'Forte';
$diplomatie[4][1] = 'Très Forte';
$diplomatie[5][1] = 'Excessive';
$diplomatie[6][1] = 'Très Excessive';
$diplomatie[7][1] = 'Aucune';
$diplomatie[8][1] = 'Aucune';
$diplomatie[9][1] = 'Aucune';
$diplomatie[10][1] = 'Aucune';
$diplomatie[127][1] = 'Minime';
$diplomatie[0][2] = 'Oui';
$diplomatie[1][2] = 'Oui';
$diplomatie[2][2] = 'Oui';
$diplomatie[3][2] = 'Oui';
$diplomatie[4][2] = 'Non';
$diplomatie[5][2] = 'Non';
$diplomatie[6][2] = 'Non';
$diplomatie[7][2] = 'Non';
$diplomatie[8][2] = 'Non';
$diplomatie[9][2] = 'Non';
$diplomatie[10][2] = 'Non';
$diplomatie[127][2] = 'Oui';
$diplomatie[0][3] = 'Oui';
$diplomatie[1][3] = 'Oui';
$diplomatie[2][3] = 'Oui';
$diplomatie[3][3] = 'Oui';
$diplomatie[4][3] = 'Oui';
$diplomatie[5][3] = 'Oui';
$diplomatie[6][3] = 'Oui';
$diplomatie[7][3] = 'Non';
$diplomatie[8][3] = 'Non';
$diplomatie[9][3] = 'Non';
$diplomatie[10][3] = 'Non';
$diplomatie[127][3] = 'Oui';
$diplomatie[0][4] = 'Non';
$diplomatie[1][4] = 'Non';
$diplomatie[2][4] = 'Non';
$diplomatie[3][4] = 'Non';
$diplomatie[4][4] = 'Non';
$diplomatie[5][4] = 'Non';
$diplomatie[6][4] = 'Non';
$diplomatie[7][4] = 'Oui';
$diplomatie[8][4] = 'Oui';
$diplomatie[9][4] = 'Oui';
$diplomatie[10][4] = 'Oui';
$diplomatie[127][4] = 'Non';
$i = 0;
while($i < 12)
{
	if($i == 11) $j = 127; else $j = $i;
	$diplomatie[$j][5] = ceil(pow(1.6, ($i + 1)));
	$i++;
}

$requete = "SELECT * FROM diplomatie";
$req = $db->query($requete);

$fields = array();
while($row = $db->read_field($req))
{
	$fields[] = $row->name;
}

echo '
<table style="text-align:center;color:#222;" cellspacing="1">
<tr style="font-weight : bold; font-size : 1em; background-color : #cccccc; text-align : center;">
';

foreach($fields as $field)
{
	echo '
	<td style="font-size : 0.9em;">
		'.$Gtrad[$field].'
	</td>';
}

echo '
	<td style="padding : 1px; font-size : 0.9em;">
		Global
	</td>
</tr>';
while($row = $db->read_array($req))
{
	$total = 0;
	echo '
<tr class="diplo" style="padding : 1px;">';
	foreach($fields as $field)
	{
		echo '';
		$color = '';
		if (is_numeric($row[$field]))
		{
			if ($row[$field] < 4) $color = '99ff99';
			elseif (($row[$field] <= 6) AND ($row[$field] >= 4)) $color = 'BBBBBB';
			elseif (($row[$field] > 6) AND ($row[$field] != 127)) $color = 'ff9999';
			elseif ($row[$field] == 127) $color = 'cccccc';
			if($row[$field] != 127) $total += $row[$field];
			else $color = 'ffffff';
			echo '
		<td style="background-color : #'.$color.'; font-size : 0.9em;">
		'.$diplomatie[$row[$field]][0];
		}
		else echo 
	'<td style="font-weight : bold; background-color : #cccccc; text-align : center; ">
		'.$Gtrad[$row[$field]];
		echo '
	</td>';
	}
	if($total > 55)
	{
		$global = 'Belliqueux';
		$color = 'ff9999';
	}
	elseif($total < 45)
	{
		$global = 'Pacifiques';
		$color = '99ff99';
	}
	else
	{
		$global = 'Normal';
		$color = 'ffffff';
	}
	echo '
	<td style="background-color : #'.$color.';">
		'.$global.'
	</td>
</tr>';
}

?>
</table>