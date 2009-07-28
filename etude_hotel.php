<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'menu_admin.php');
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Objets vendu a l'HV depuis le début du jeu
			</div>
			<ul>
	<?php
	$requete = "SELECT * FROM journal WHERE action = 'vend'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$objets[$row['valeur']]['total'] += 1;
		$objets[$row['valeur']]['somme'] += $row['valeur2'];
		$objets[$row['valeur']]['nom'] = $row['valeur'];
		$objets[$row['valeur']]['liste'][] = $row['valeur2'];
	}
	$i = 0;
	$count = count($objets);
	$keys = array_keys($objets);
	while($i < $count)
	{
		$moyenne = $objets[$keys[$i]]['somme'] / $objets[$keys[$i]]['total'];
		$j = 0;
		$count_liste = count($objets[$keys[$i]]['liste']);
		while($j < $count_liste AND $count_liste > 1)
		{
			$valeur = intval($objets[$keys[$i]]['liste'][$j]);
			if($valeur < (0.1 * $moyenne) OR $valeur > (3 * $moyenne))
			{
				$objets[$keys[$i]]['somme'] -= $valeur;
				$objets[$keys[$i]]['total'] -= 1;
				//echo $objets[$keys[$i]]['nom'].' - '.$moyenne.' -> '.$valeur.' '.$keys[$i].' '.$j.'<br />';
			}
			$j++;
		}
		$i++;
	}
	?>
	<table>
	<?php
	array_multisort($objets, SORT_DESC);
	foreach($objets as $objet)
	{
		if($objet['total'] > 0)
		{
			$moyen = ($objet['somme'] / $objet['total']);
			$requete = "SELECT prix FROM objet WHERE nom = '".mysql_escape_string($objet['nom'])."'";
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_assoc($req);
				$prix_max = $row['prix'] * 8;
				$prix_moyen = $prix_max / 20;
				$pourcent = ($moyen - $prix_mini) / ($prix_max) * 100;
			}
			else
			{
				$prix_max = 'inconnu';
				$pourcent = '';
				$prix_mini = '';
			}
			echo '
			<tr>
				<td>
					'.$objet['total'].' <strong><a href="cours_objet.php?objet='.$objet['nom'].'">'.$objet['nom'].'</strong>
				</td>
				<td>
					au prix moyen de : '.$moyen.'
				</td>
				<td>
					 -> Max : '.$prix_max.' -> Moyen  '.$prix_moyen.'
				</td>
				<td>
					'.$pourcent.'
				</td>
			</tr>';
		}
	}
	?>
	</table>
	<?php
	$objets = array();
?>
			</ul>
			<div class="titre">
				Objets en vente a l'HV depuis 1 mois
			</div>
			<ul>
	<?php
	$requete = "SELECT * FROM hotel WHERE time > ".(time() - (60 * 60 * 24 * 30));
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$objet_d = decompose_objet($row['objet']);
		$objet_nom = nom_objet($row['objet']);
		$objets[$objet_d['id']]['total'] += 1;
		$objets[$objet_d['id']]['somme'] += $row['prix'];
		$objets[$objet_d['id']]['nom'] = $objet_nom;
	}
	array_multisort($objets, SORT_DESC);
	foreach($objets as $objet)
	{
		echo '<ul>'.$objet['total'].' <strong>'.$objet['nom'].'</strong> au prix moyen de : '.($objet['somme'] / $objet['total']).'</ul>';
	}
}
?>
			</ul>
		</div>
	</div>