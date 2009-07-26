<?php
$textures = false;
include('../haut.php');
setlocale(LC_ALL, 'fr_FR');
include('../haut_site.php');
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
	<form method="post">
	<select name="id">
		<option value="1">Joueurs ayant fait le plus de dégats en moyenne lorsqu'il attaque</option>
		<option value="2">Joueurs ayant fait le moins de dégats en moyenne lorsqu'il attaque</option>
		<option value="3">Joueurs ayant pris le plus de dégats en moyenne lorsqu'il attaque</option>
		<option value="4">Joueurs ayant pris le moins de dégats en moyenne lorsqu'il attaque</option>
		<option value="5">Joueurs ayant fait le plus de dégats en moyenne lorsqu'il défend</option>
		<option value="6">Joueurs ayant fait le moins de dégats en moyenne lorsqu'il défend</option>
		<option value="7">Joueurs ayant pris le plus de dégats en moyenne lorsqu'il défend</option>
		<option value="8">Joueurs ayant pris le moins de dégats en moyenne lorsqu'il défend</option>
		<option value="9">Joueurs ayant fait les plus gros dégats en attaque</option>
		<option value="10">Joueurs ayant fait les plus faible dégats en attaque</option>
		<option value="17">Joueurs ayant fait les plus gros dégats en défense</option>
		<option value="18">Joueurs ayant fait les plus faible dégats en défense</option>
		<option value="19">Joueurs ayant fait le plus d'attaque</option>
		<option value="20">Joueurs ayant subit le plus d'attaque</option>
		<option value="21">Ratio Kill / Death</option>
		<option value="11">Joueurs ayant fait le plus de soins en moyenne</option>
		<option value="12">Joueurs ayant fait le plus de soins au total</option>
		<option value="13">Joueurs ayant reçu le plus de loot</option>
		<option value="14">Joueurs ayant vendu le plus de d'objet a l'HV</option>
		<option value="15">Joueurs ayant vendu le plus a l'HV</option>
		<option value="16">Joueurs ayant fini le plus de quètes</option>
	</select>
	<select name="table">
	<?php
	require_once('connect_log.php');
	$requete = "SHOW TABLES WHERE Tables_in_starshine_log like 'journal%'";
	$req = $db_log->query($requete);
	while($row = $db_log->read_assoc($req))
	{
		?>
		<option value="<?php echo $row['Tables_in_starshine_log']; ?>"><?php echo $row['Tables_in_starshine_log']; ?></option>
		<?php
	}
	?>
	</select>
	<input type="submit" value="Valider">
	</form>
	<?php
	if(array_key_exists('id', $_POST))
	{
		$table = $_POST['table'];
		switch($_POST['id'])
		{
			case 1 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 2 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 3 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 4 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 5 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 6 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur2 * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 7 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 8 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 50;
			break;
			case 9 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 10 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne ASC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 11 : 
				$requete = "SELECT COUNT(*) as tot, AVG(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'soin' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 50;
			break;
			case 12 : 
				$requete = "SELECT COUNT(*) as tot, SUM(valeur * 1) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'soin' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 13 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, id_perso FROM `".$table."` WHERE action = 'loot' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 14 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, id_perso FROM `".$table."` WHERE action = 'vend' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 15 : 
				$requete = "SELECT COUNT(*) as tot, SUM(valeur2 * 1) as moyenne, id_perso FROM `".$table."` WHERE action = 'vend' GROUP BY id_perso ORDER BY moyenne DESC";
				$joueur = 'id_perso';
				$min = 0;
			break;
			case 16 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'f_quete' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 17 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur2 * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne DESC";
				$joueur = 'passif';
				$min = 0;
			break;
			case 18 : 
				$requete = "SELECT COUNT(*) as tot, MAX(valeur2 * 1) as moyenne, passif FROM `".$table."` WHERE action = 'attaque' GROUP BY passif ORDER BY moyenne ASC";
				$joueur = 'passif';
				$min = 0;
			break;
			case 19 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'attaque' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 20 : 
				$requete = "SELECT COUNT(*) as tot, COUNT(*) as moyenne, actif, id_perso FROM `".$table."` WHERE action = 'defense' GROUP BY actif ORDER BY moyenne DESC";
				$joueur = 'actif';
				$min = 0;
			break;
			case 21 : 
				$requete = "SELECT COUNT(*) as tot, (perso.frag / COUNT(*)) as moyenne, journal.*, perso.frag FROM `".$table."` LEFT JOIN perso ON journal.actif = perso.nom WHERE perso.frag > 10 AND action = 'mort' GROUP BY actif ORDER BY moyenne DESC";
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
		$req = $db_log->query($requete);
		$i = 1;
		while($row = $db_log->read_assoc($req) AND $i < 51)
		{
			if($row['tot'] > $min)
			{
				$ligne_joueur = $row[$joueur];
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
	include('../bas.php');
}
?>