<?php
include('haut.php');
include('haut_site.php');
include('menu.php');

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
<div id="contenu">
	<div id="centre3">
	<div class="titre">
		Diplomatie
	</div>
	<br />
<table style="border : 1px solid #ddd;" cellspacing="0">
<tr style="font-weight : bold; font-size : 1em; background-color : #cccccc; text-align : center;">
';

foreach($fields as $field)
{
	echo '
	<td style="border : 1px solid #89959b; padding : 1px; font-size : 0.9em;">
		'.$Gtrad[$field].'
	</td>';
}

echo '
	<td style="border : 1px solid #89959b; padding : 1px; font-size : 0.9em;">
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
			elseif (($row[$field] > 6) AND ($row[$field] != 127)) $color = 'ff9999';
			elseif ($row[$field] == 127) $color = 'cccccc';
			if($row[$field] != 127) $total += $row[$field];
			else $color = 'ffffff';
			echo '
		<td style="background-color : #'.$color.'; border : 1px solid #89959b; font-size : 0.9em;">
		'.$diplomatie[$row[$field]][0];
		}
		else echo 
	'<td style="font-weight : bold; background-color : #cccccc; text-align : center; border : 1px solid #89959b;">
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
	<td style="background-color : #'.$color.'; border : 1px solid #89959b;">
		'.$global.'
	</td>
</tr>';
}

?>
</table>
<br />
	<div class="titre">
		Légende
	</div>
<table style="border : 0px; background-color : #E4EAF2; width : 100%;">
<tr class="diplo">
	<td>
		Relation
	</td>
	<td>
		% xp gagné
	</td>
	<td>
		% honneur gagné
	</td>
	<td>
		Taxe
	</td>
	<td>
		Frais de poste
	</td>
	<td>
		Téléportation
	</td>
	<td>
		Accès aux Villes
	</td>
	<td>
		Pose de drapeaux
	</td>
</tr>
<?php
$i = 0;
foreach($diplomatie as $diplo)
{
	if ($diplo[0] != 'X')
	{
	echo '
<tr class="diplo">
	<td>
		'.$diplo[0].'
	</td>
	<td>
		'.($i * 20).'%
	</td>
	<td>
		';
	$pourcent = (($i * 20) - 80);
	if ($pourcent < 0) $pourcent = 0;
	echo $pourcent.'%
	</td>
	<td>
		'.$diplo[1].'
	</td>
	<td>
		'.$diplo[5].'
	</td>
	<td>
		'.$diplo[2].'
	</td>
	<td>
		'.$diplo[3].'
	</td>
	<td>
		'.$diplo[4].'
	</td>
</tr>
	';
	$i++;
	}
}

?>
</table>
<br />
	<div class="titre">
		Informations sur les peuples :
	</div>
<br />
<?php
$races = array_keys($Trace);
$count_race = count($races);
$iii = 0;
while($iii < $count_race)
{
	?>
	<div style="text-align : left;">
	<table style="width : 100%; margin-bottom : 15px; border : 0px; background-color : #E4EAF2; border-spacing : 0px;">
	<tr>
		<td colspan="2">
			<div class="titre">
				<strong><?php echo $Gtrad[$races[$iii]]; ?></strong>
			</div>
		</td>
	</tr>
	<tr style="width : 100%;">
		<td style="width : 50%; vertical-align : top; padding-left : 5px;">
			<h3>
				Joueurs Gradés
			</h3>
	<?php
	$requete = "SELECT *, grade.nom as gnom, perso.nom as name FROM perso LEFT JOIN grade ON perso.rang_royaume = grade.id WHERE race = '".$races[$iii]."' AND grade.id <> 7 ORDER BY grade.facteur ASC";
	$req = $db->query( $requete);
	while($row = $db->read_assoc($req))
	{
		echo $row['gnom'].' '.$row['name'].'<br />';
	}
		?>
		</td>
		<td style="width : 50%; vertical-align : top;">
			<h3>
				Construction de la ville
			</h3>
			<?php
			$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = '".$Trace[$races[$iii]]['numrace']."'";
			$req = $db->query( $requete);
			while($row = $db->read_assoc($req))
			{
				echo $row['nom'].'<br />';
			}
			?>
		</td>
	</tr>
	</table>
		<?php
	$iii++;
}
?>
	</div>
</div>