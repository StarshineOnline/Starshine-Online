<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mise à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				<h4>Vérification des bourgs</h4>
			</div>
		<table>
		<tr>
			<td>
				Id
			</td>
			<td>
				Nom
			</td>
			<td>
				En activité (bdd)
			</td>
			<td>
				Maximum
			</td>
			<td>
				Total
			</td>
			<td>
				Construit
			</td>
			<td>
				Inventaire
			</td>
			<td>
				Inventaire Joueurs
			</td>
		</tr>
		<?php
		$requete = "SELECT id, race, bourg FROM royaume WHERE id <> 0 AND bourg <> 0 ORDER BY id";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$royaumes[$row['race']] = $row;
			//Requète qui compte le nombre de bourg construit pour ce royaume
			$requete = "SELECT COUNT(*) as tot FROM construction WHERE type = 'bourg' AND royaume = ".$row['id'];
			//echo $requete;
			$req_bourg = $db->query($requete);
			$row_bourg = $db->read_assoc($req_bourg);
			$royaumes[$row['race']]['bourg_construit'] = $row_bourg['tot'];
			$requete = "SELECT id, nom, inventaire_slot FROM perso WHERE race = '".$row['race']."' AND inventaire_slot LIKE '%\"r10\"%'";
			//echo $requete;
			$req_perso = $db->query($requete);
			while($row_perso = $db->read_assoc($req_perso))
			{
				$nbr = 0;
				$inventaire = unserialize($row_perso['inventaire_slot']);
				foreach($inventaire as $slot)
				{
					if($slot == 'r10') $nbr++;
				}
				$royaumes[$row['race']]['bourg_joueur'] .= $row_perso['nom'].' : '.$nbr.' - ';
				$royaumes[$row['race']]['bourg_joueur_total'] += $nbr;
			}
			$royaumes[$row['race']]['bourg_total'] = $royaumes[$row['race']]['bourg_joueur_total'] + $royaumes[$row['race']]['bourg_construit'];
			$requete = "SELECT COUNT(*) as tot FROM map WHERE x <= 190 AND y <= 190 AND royaume = ".$row['id'];
			$req_max = $db->query($requete);
			$row_max = $db->read_assoc($req_max);
      $royaumes[$row['race']]['bourg_max'] = 1 + floor($row_max['tot']/250);
			?>
		<tr>
			<td>
				<?php echo $royaumes[$row['race']]['ID']; ?>
			</td>
			<td>
				<?php echo $row['race']; ?>
			</td>
			<td <?php if($royaumes[$row['race']]['bourg'] != $royaumes[$row['race']]['bourg_total']) echo 'style="background-color : red; font-weight : bold;"'; ?>>
				<?php echo $royaumes[$row['race']]['bourg']; ?>
			</td>
			<td <?php if($royaumes[$row['race']]['bourg_total'] > $royaumes[$row['race']]['bourg_max']) echo 'style="background-color : red; font-weight : bold;"'; ?>>
				<?php echo $royaumes[$row['race']]['bourg_max']; ?>
			</td>
			<td>
				<?php echo $royaumes[$row['race']]['bourg_total']; ?>
			</td>
			<td>
				<?php echo $royaumes[$row['race']]['bourg_construit']; ?>
			</td>
			<td>
				<?php echo $royaumes[$row['race']]['bourg_joueur_total']; ?>
			</td>
			<td>
				<?php echo $royaumes[$row['race']]['bourg_joueur']; ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		</div>
    <div>
    <h4>Zones interdites à la poses</h4>
    <form method="get" action ="admin_bourg.php">
      <label>Race :<label>
      <select name='race'>
        <?php
          foreach($Trace as $r=>$t)
          {
            echo '<option value="'.$t['numrace'].'">'.$r.'</option>';
          }
        ?>
      </select>
      <input type="submit" value="Afficher"/>
    </form>
		<?php
    if( array_key_exists('race', $_GET) )
      echo '<br/><img src="carte_dist_bat.php?type=bourg&race='.$_GET['race'].'"/>';
		echo '</div>';
	include_once(root.'admin/admin_bas.php');
}
?>