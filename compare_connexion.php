<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	if(!array_key_exists('id1', $_GET))
	{
		?>
		<form method="get" action="compare_connexion.php">
			Identifiant du joueur 1 : <input type="text" name="id1" /><br />
			Identifiant du joueur 2 : <input type="text" name="id2" /><br />
			Intervalle de temps entre 2 connexions (en secondes) : <input type="text" name="intervalle" value="60" /><br />
			<input type="submit" value="Valider" />
		</form>
		<?php
	}
	else
	{
		?>
		Les lignes en gras sont les connexions sur la même IP<br />
		<br />
		<?php
		$id1 = $_GET['id1'];
		$id2 = $_GET['id2'];
		//On récupère le listing connexion des 2 joueurs
		$requete = "SELECT * FROM log_connexion WHERE message = 'Ok' AND id_joueur = ".$id1." ORDER BY time ASC";
		$req = $db->query($requete);
		while($row_log = $db->read_assoc($req))
		{
			$tab1[] = $row_log;
		}
		$requete = "SELECT * FROM log_connexion WHERE message = 'Ok' AND id_joueur = ".$id2." ORDER BY time ASC";
		$req = $db->query($requete);
		while($row_log = $db->read_assoc($req))
		{
			$tab2[] = $row_log;
		}
		
		$i = 0;
		$j = 0;
		$count1 = count($tab1);
		$count2 = count($tab2);
		$intervalle = $_GET['intervalle'];
		
		while($i < $count1)
		{
			if(($tab1[$i]['time'] + $intervalle) > $tab2[$j]['time'] AND ($tab1[$i]['time'] - $intervalle) < $tab2[$j]['time'])
			{
				//echo $tab1[$i]['time'].' '.($tab2[$j]['time'] + $intervalle).' '.($tab2[$j]['time'] - $intervalle).'<br />';
				if($tab1[$i]['ip'] == $tab2[$j]['ip']) $gras = true;
				else $gras = false;
				echo '<li>';
				if($gras) echo '<strong>';
				echo date("d-m-Y H:i:s", $tab1[$i]['time']).' - IP : '.$tab1[$i]['ip'].' &&&&& '.date("d-m-Y H:i:s", $tab2[$j]['time']).' - IP : '.$tab2[$j]['ip'];
				if($gras) echo '</strong>';
				echo '</li>';
			}
			while($tab2[$j]['time'] < $tab1[$i]['time'] AND $j < $count2)
			{
				$j++;
			}
			$i++;
		}
	}
}