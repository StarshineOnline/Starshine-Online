<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//JOURNALIER TRAITEMENT DES LOGS //
include_once(root.'class/db.class.php');
//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');
include_once(root.'connect_log.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//le mois dernier
$time = time() - (31 * 24 * 60 * 60);

// On pourrait le faire de manière globale ce serait mieux
// genre insert into DST select * from SRC where FILTRE_DE_MOIS
$requete = "SELECT * FROM log_connexion WHERE time <= ".$time." ORDER BY time ASC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$moisannee = date("Y-n", $row['time']);
	$table = 'log_connexion-'.$moisannee;
	if(!$db_log->table_exists($table))
	{
		$db_log->query("CREATE TABLE `".$table."` (`id` int(10) unsigned NOT NULL, `id_joueur` int(10) unsigned NOT NULL default '0', `time` int(10) unsigned NOT NULL default '0', `ip` varchar(50) NOT NULL default '', `message` text NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
    echo 'Création de la table : '.$table."\n";
	}
	$requete = 'INSERT INTO `'.$table.'` VALUES('.$row['id'].', '.($row['id_joueur']?$row['id_joueur']:'NULL').', '.($row['id_perso']?$row['id_perso']:'NULL').', '.$row['time'].', "'.$row['ip'].'", "'.$row['message'].'", '.($row['cahce_info']?$row['cahce_info']:'NULL').', "'.($row['wsid']?$row['wsid']:'NULL').'");';
	$db_log->query($requete);
	$db->query("DELETE FROM log_connexion WHERE id = ".$row['id']);
	//echo $requete;
}

$annee = date("Y");
$mois = date("n");
$requete = "SELECT *, EXTRACT(YEAR FROM time) as year, EXTRACT(MONTH FROM time) as month FROM journal WHERE EXTRACT(YEAR FROM time) < ".$annee." OR EXTRACT(MONTH FROM time) < ".$mois." ORDER BY time ASC";
//echo $requete;
$req = $db->query($requete);

$delete = $db->prepare('DELETE FROM journal WHERE id = ?');
$insert = null;
$oldtable = null;

// On pourrait le faire de manière globale ce serait mieux
// genre insert into DST select * from SRC where FILTRE_DE_MOIS
while($row = $db->read_assoc($req))
{
	$moisannee = $row['year'].'-'.$row['month'];
	$table = 'journal-'.$moisannee;
	if(!$db_log->table_exists($table))
	{
    echo 'Création de la table : '.$table."\n";
		$db_log->query("CREATE TABLE `".$table."` (`id` int(10) unsigned NOT NULL, `id_perso` int(10) unsigned NOT NULL default '0', `action` varchar(50) NOT NULL default '', `actif` varchar(50) NOT NULL default '', `passif` varchar(50) NOT NULL default '', `time` datetime NOT NULL default '0000-00-00 00:00:00', `valeur` text NOT NULL, `valeur2` int(11) NOT NULL default '0', `x` mediumint(9) NOT NULL, `y` mediumint(9) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;");
	}
  if ($oldtable != $table)
    $insert = $db_log->prepare('INSERT INTO `' . $table . 
                           '` VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
  $insert->bind_param('iissssssii', $row['id'], $row['id_perso'], 
                      $row['action'], $row['actif'], $row['passif'], 
                      $row['time'], $row['valeur'], $row['valeur2'], 
                      $row['x'], $row['y']);
  if (!$insert->execute()) {
    $db->stmt_error($insert);
  }
  $delete->bind_param('i', $row['id']);
  if (!$delete->execute()) {
    $db->stmt_error($delete);
  }
}
?>