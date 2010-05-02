<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$options = recup_option($_SESSION['ID']);
?>
<fieldset>

<?php
$nombre_action_journal = 15;
if(array_key_exists('page', $_GET))
{
	$page = $_GET['page'];
}
else $page = 1;
if(array_key_exists('archive', $_GET) AND $_GET['archive'] != 'now')
{
	require_once('connect_log.php');
	$table = 'journal-'.$_GET['archive'];
	$date = ' - '.$_GET['archive'];
}
else
{
	$table = 'journal';
	$date = '';
}
echo '<legend>
Journal - Page '.$page.$date.'</legend>';

$and = '';
$i = 0;
$count = count($options);
$keys = array_keys($options);
while($i < $count)
{
	if($options[$keys[$i]] == 1)
	{
		switch($keys[$i])
		{
			case 'soin' :
				$and .= " AND action <> 'soin' AND action <> 'rsoin'";
			break;
			case 'gsoin' :
				$and .= " AND action <> 'gsoin' AND action <> 'rgsoin'";
			break;
			case 'buff' :
				$and .= " AND action <> 'buff' AND action <> 'rbuff'";
			break;
			case 'gbuff' :
				$and .= " AND action <> 'gbuff' AND action <> 'rgbuff'";
			break;
			case 'degat' :
				$and .= " AND action <> 'attaque' AND action <> 'defense'";
			break;
			case 'kill' :
				$and .= " AND action <> 'mort' AND action <> 'tue'";
			break;
			case 'quete' :
				$and .= " AND action <> 'f_quete'";
			break;
			case 'loot' :
				$and .= " AND action <> 'loot'";
			break;
		}
	}
	$i++;
}
$limit1 = ($page - 1) * $nombre_action_journal;
$limit2 = $nombre_action_journal;
$requete = "SELECT COUNT(*) FROM `".$table."` WHERE id_perso = ".$joueur->get_id();
if($table == 'journal') $db_use = $db;
else $db_use = $db_log;
$req = $db_use->query($requete);
$row = $db_use->read_row($req);
$page_max = ceil($row[0] / $nombre_action_journal);
$requete = "SELECT * FROM `".$table."` WHERE id_perso = ".$joueur->get_id().$and." ORDER by time DESC, id DESC LIMIT ".$limit1.", ".$limit2;
$req = $db_use->query($requete);
while($row = $db_use->read_assoc($req))
{
	//Convertit la date en un format plus court
	echo affiche_ligne_journal($row);
}
?>

<?php
if($page > 1)
{
	if(array_key_exists('archive', $_GET)) $archive = '&amp;archive='.$_GET['archive']; else $archive = '';
	$a1 = '<a href="journal.php?page='.($page - 1).$archive.'" onclick="return envoiInfo(this.href, \'information\');">';
	$a2 = '</a>';
}
else
{
	$a1 = '';
	$a2 = '';
}
if($page != $page_max)
{
	if(array_key_exists('archive', $_GET)) $archive = '&amp;archive='.$_GET['archive']; else $archive = '';
	$a3 = '<a href="journal.php?page='.($page + 1).$archive.'" onclick="return envoiInfo(this.href, \'information\');">';
	$a4 = '</a>';
}
else
{
	$a3 = '';
	$a4 = '';
}
echo '</div>';
require_once('connect_log.php');
$requete = 'SELECT table_name FROM information_schema.tables WHERE table_name like \'journal%\' AND table_schema = \''.$cfg_log['sql']['db'].'\' ORDER BY table_name DESC';
$req = $db_log->query($requete);
echo $a1.'<-- Page précédente'.$a2.' / '.$a3.'Page suivante -->'.$a4.'<br />
<select name="archive" id="archive">
	<option value="now">Mois actuel</option>';
while($row = $db_log->read_assoc($req))
{
	$date = str_replace('journal-', '', $row['table_name']);
	?>
	<option value="<?php echo $date; ?>"><?php echo $date; ?></option>
	<?php
}
?>
</select> <input type="button" onclick="return envoiInfo('journal.php?archive=' + document.getElementById('archive').value, 'information');" value="Ok" />
</fieldset>