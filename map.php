<script type="text/JavaScript">
function doShow(title)
{
	a = eval('document.getElementById("' + title + '")');
  with(a)
  {
    if (a.style.display == 'none')
    {
      a.style.display = 'block';
    }
    else
    {
      a.style.display = 'none'; 
    };
  }
}
</script>
<?php

$requete = 'SELECT x, y FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = mysqli_query($db, $requete);
$row = mysqli_fetch_array($req);
$coord['x'] = $row['x'];
$coord['y'] = $row['y'];

echo 'x : '.$coord['x'].' y : '.$coord['y'];
$xmin = $coord['x'] - 3;
if ($xmin < 1) $xmin = 1;
$xmax = $coord['x'] + 3;
if ($xmax > 100) $xmax = 100;
$ymin = $coord['y'] - 3;
if ($ymin < 1) $ymin = 1;
$ymax = $coord['y'] + 3;
if ($ymax > 100) $ymax = 100;

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$colonne.') * 100)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$colonne.') * 100)) <= '.$xmax.'))) ORDER BY ID';
$req = mysqli_query($db, $requete);
$requete_joueurs = 'SELECT * FROM perso WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_joueurs = mysqli_query($db, $requete_joueurs);
?>
<div id="carte">
	<table cellpadding="0" cellspacing="0">
	<tr style="background-color : #000000; color : #ffffff;">
		<td>
		</td>

<?php

for ($i = $xmin; $i <= $xmax; $i++)
{
	echo '<td>'.$i.'</td>';
}

$x = 0;
$y = 0;

$j = 0;
while($row_joueurs = mysqli_fetch_array($req_joueurs))
{
	$i = 0;
	$arraykeys = array_keys($row_joueurs);
	foreach($row_joueurs as $value)
	{			
		if (fmod($i, 2) != 0)
		{
			$nomchamp = $arraykeys[$i];
			$row_j[$j][$nomchamp] = $row_joueurs[$nomchamp];
		}
		$i++;
	}
	$j++;
}

$index = 0;
$x_joueurs = $row_j[$index]['x'];
$y_joueurs = $row_j[$index]['y'];

//Affichage de la map
while($row = mysqli_fetch_array($req))
{
	$coord = convert_in_coord($row['ID']);
	$rowid = $row['ID'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		$z = 0;
		while(($x_joueurs == $coord['x']) AND ($y_joueurs == $coord['y']))
		{
			$info[$rowid][$z]['ID'] = $row_j[$index]['ID'];
			$info[$rowid][$z]['nom'] = $row_j[$index]['nom'];
			$info[$rowid][$z]['race'] = $row_j[$index]['race'];
			$index++;
			if (isset($row_j[$index]['x']))
			{
				$x_joueurs = $row_j[$index]['x'];
				$y_joueurs = $row_j[$index]['y'];
			}
			else
			{
				$x_joueurs = 0;
				$y_joueurs = 0;
			}
			$z++;
		}		
		
		if (isset($info[$rowid])) $row['info'] = $info[$rowid][0]['race'];
		if ($row['info'] == '') $row['info'] = '&nbsp;';
		if ($coord['y'] != $y)
		{
			echo '</tr>
			<tr>
				<td style="background-color : #000000; color : #ffffff;">
					'.$coord['y'].'
				</td>
				<td background="image/tex_herbe.png" width="60" height="60" onclick="doShow(\''.$rowid.'\')">
					'.$row['info'].'
				</td>
			';
			$y = $coord['y'];
		}
		else
		{
			echo '<td background="image/tex_herbe.png" width="60" height="60" onclick="doShow(\''.$rowid.'\')">'.$row['info'].'</td>';
		}
	}
}

?>
	</table>
</div>
<div id="infocarte">
	Informations :
<?php

$numcase = array_keys($info);

$i = 0;
foreach($info as $case)
{
	echo '
	<div id="'.$numcase[$i].'" style="display : none;">Case numéro : '.$numcase[$i].'<br />';
	foreach($case as $joueur)
	{
		echo $joueur['race'].' '.$joueur['nom'].'<br />';
	}
	echo '
	</div>';
	$i++;
}

?>
</div>

<a href="jeu.php?page=deplacement&amp;deplacement=haut">Déplacement vers le haut</a><br />
<a href="jeu.php?page=deplacement&amp;deplacement=gauche">Déplacement à gauche</a> <a href="jeu.php?page=deplacement&amp;deplacement=droite">Déplacement à droite</a><br />
<a href="jeu.php?page=deplacement&amp;deplacement=bas">Déplacement vers le bas</a>