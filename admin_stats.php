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
	echo '
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Statistique
	</div>				
	';
	?>
	<ul>
		<li><a href="admin_stats.php?id=1">Joueurs ayant fait le plus de dégats en moyenne lorsqu'il attaque</a></li>
		<li><a href="admin_stats.php?id=2">Joueurs ayant fait le moins de dégats en moyenne lorsqu'il attaque</a></li>
		<li><a href="admin_stats.php?id=3">Joueurs ayant pris le plus de dégats en moyenne lorsqu'il attaque</a></li>
		<li><a href="admin_stats.php?id=4">Joueurs ayant pris le moins de dégats en moyenne lorsqu'il attaque</a></li>
		<li><a href="admin_stats.php?id=5">Joueurs ayant fait le plus de dégats en moyenne lorsqu'il défend</a></li>
		<li><a href="admin_stats.php?id=6">Joueurs ayant fait le moins de dégats en moyenne lorsqu'il défend</a></li>
		<li><a href="admin_stats.php?id=7">Joueurs ayant pris le plus de dégats en moyenne lorsqu'il défend</a></li>
		<li><a href="admin_stats.php?id=8">Joueurs ayant pris le moins de dégats en moyenne lorsqu'il défend</a></li>
		<li><a href="admin_stats.php?id=9">Joueurs ayant fait les plus gros dégats en attaque</a></li>
		<li><a href="admin_stats.php?id=10">Joueurs ayant fait les plus faible dégats en attaque</a></li>
		<li><a href="admin_stats.php?id=17">Joueurs ayant fait les plus gros dégats en défense</a></li>
		<li><a href="admin_stats.php?id=18">Joueurs ayant fait les plus faible dégats en défense</a></li>
		<li><a href="admin_stats.php?id=19">Joueurs ayant fait le plus d'attaque</a></li>
		<li><a href="admin_stats.php?id=20">Joueurs ayant subit le plus d'attaque</a></li>
		<li><a href="admin_stats.php?id=21">Ratio Kill / Death</a></li>
		<li><a href="admin_stats.php?id=11">Joueurs ayant fait le plus de soins en moyenne</a></li>
		<li><a href="admin_stats.php?id=12">Joueurs ayant fait le plus de soins au total</a></li>
		<li><a href="admin_stats.php?id=13">Joueurs ayant reçu le plus de loot</a></li>
		<li><a href="admin_stats.php?id=14">Joueurs ayant vendu le plus de d'objet a l'HV</a></li>
		<li><a href="admin_stats.php?id=15">Joueurs ayant vendu le plus a l'HV</a></li>
		<li><a href="admin_stats.php?id=16">Joueurs ayant fini le plus de quètes</a></li>
	</ul>
	<?php
	if(array_key_exists('id', $_GET))
	{
		switch($_GET['id'])
		{
			case 1 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 2 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 3 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 4 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 5 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 6 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 7 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 8 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 9 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 10 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 11 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'soin' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 12 : 
				$requete = "SELECT COUNT(*) as tot, SUM(valeur * 1) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'soin' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 13 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'loot' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 14 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'vend' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 15 : 
				$requete = "SELECT COUNT(*) as tot, SUM(valeur2 * 1) as moyenne, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'vend' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 16 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'f_quete' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 17 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur2 * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 0;
			break;
			case 18 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur2 * 1) as moyenne, passif FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 0;
			break;
			case 19 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 20 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `journal` WHERE EXTRACT(MONTH FROM time) > 09 AND  action = 'defense' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 21 : 
				$requete = "SELECT COUNT(*) as tot, (perso.frag / COUNT(*)) as moyenne, journal.*, perso.frag FROM `journal` LEFT JOIN perso ON journal.actif = perso.nom WHERE EXTRACT(MONTH FROM time) > 09 AND  perso.frag > 10 AND action = 'mort' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 10;
			break;
		}
		?>
	<table style="">
		<tr>
			<td>
				#
			</td>
			<td>
				Nom
			</td>
			<td>
				Moyenne
			</td>
			<td>
				Nombre d'attaque
			</td>
		</tr>
		<?php
		$req = $db->query($requete);
		$i = 1;
		while($row = $db->read_assoc($req) AND $i < 51)
		{
			if($row['tot'] > $min)
			{
				if($joueur == 'actif') $ligne_joueur = '<a href="admin_joueur.php?direction=info_joueur&id='.$row['id_perso'].'">'.$row[$joueur].'</a>';
				else $ligne_joueur = $row[$joueur];
				echo '
			<tr>
				<td>
					'.$i.'
				</td>
				<td>
					'.$ligne_joueur.'
				</td>
				<td>
					'.$row['moyenne'].'
				</td>
				<td>
					'.$row['tot'].'
				</td>
			</tr>
			';	
				$i++;
			}
		}
		?>
		</table>
		<?php
	}
	include('bas.php');
}
?>