<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'menu.php');
	//Si le joueur est connecté on affiche le menu de droite

	?>
	<div id="contenu">
		<div id="centre2">
	<?php
$tableau = array();
$joueurs = array();

$requete = "SELECT COUNT(*) as tot, race as race_joueur FROM perso WHERE statut = 'actif' GROUP BY race ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$joueurs[$row['race_joueur']] = $row['tot'];
}

$tab = array();
$requete = "SELECT (SUM(honneur) * 4) as tot, race as race_joueur FROM perso WHERE statut = 'actif' GROUP BY race ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<style type="text/css">
table.classroy tr td
{
	border : 1px solid #000;
}

td.space
{
	padding-right : 60px;
	border : 0px;
}
</style>
<h2>Honneur</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>
<?php


$tab = array();
$requete = "SELECT (SUM(level) * 4000) as tot, race as race_joueur FROM perso WHERE statut = 'actif' GROUP BY race ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Niveaux</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>
<?php

$tab = array();
$requete = "SELECT royaume.race as race_joueur, (SUM( cout ) * 100) AS tot FROM `construction` LEFT JOIN batiment ON construction.id_batiment = batiment.id LEFT JOIN royaume ON construction.royaume = royaume.id GROUP BY royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Constructions (hors ville)</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>
<?php

$tab = array();
$requete = "SELECT (COUNT(*) * 1000) as tot, royaume.race as race_joueur FROM `map` LEFT JOIN royaume ON royaume.id = map.royaume WHERE royaume <> 0 GROUP BY royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Cases controlées</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>

<?php


$tab = array();
$requete = "SELECT royaume.race as race_joueur, (SUM( cout ) * 200) AS tot FROM `construction_ville` LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id LEFT JOIN royaume ON construction_ville.id_royaume = royaume.id GROUP BY id_royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Constructions (en ville)</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>

<?php


$tab = array();
$requete = "SELECT royaume.race as race_joueur, (SUM( star_royaume ) * 50) AS tot FROM `quete_royaume` LEFT JOIN quete ON quete_royaume.id_quete = quete.id LEFT JOIN royaume ON quete_royaume.id_royaume = royaume.id GROUP BY id_royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Quètes</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>
<?php


$tab = array();
$requete = "SELECT race as race_joueur, (star * 50) as tot FROM `royaume` WHERE ID <> 0 ORDER BY star DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau[$row['race_joueur']] += $row['tot'];
	$tab[$row['race_joueur']] = $row['tot'];
}

?>
<h2>Stars du royaume</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tab);
$i = 1;
foreach($tab as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>

<?php


array_multisort($tableau, SORT_DESC);
?>
<h2>Total</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
array_multisort($tab, SORT_DESC);
$tab2 = array();
$keys = array_keys($tableau);
$i = 1;
$somme = 0;
$somme2 = 0;
foreach($tableau as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	$somme += $row;
	$somme2 += $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
$moyenne = $somme / 11;
$moyenne2 = $somme2 / 11;

?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo number_format($row, 0, ',', ' '); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>

<h2>Ratio</h2>
<table>
<tr>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points
				</td>
			</tr>
<?php
$tab2 = array();
$keys = array_keys($tableau);
$i = 1;
foreach($tableau as $row)
{
	$tab2[$keys[($i - 1)]] = $row / $joueurs[$keys[($i - 1)]];
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo round((($row / $moyenne) * 100), 3); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
	<td class="space">
		<table class="classroy">
			<tr>
				<td>
					#
				</td>
				<td>
					Royaume
				</td>
				<td>
					Points / Joueur
				</td>
			</tr>
<?php
array_multisort($tab2, SORT_DESC);
$keys = array_keys($tab2);
$i = 1;
foreach($tab2 as $row)
{
	?>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $Gtrad[$keys[($i - 1)]]; ?>
				</td>
				<td>
					<?php echo round((($row / $moyenne2) * 100), 3); ?>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>
</div>
<?php
	include_once(root.'menu_d.php');
}
?>
</div>