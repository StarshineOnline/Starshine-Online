<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
$options = recup_option($_SESSION['ID']);
?>
<h2>Journal</h2>
<?php
$nombre_action_journal = 15;
if(array_key_exists('page', $_GET))
{
	$page = $_GET['page'];
}
else $page = 1;
echo '
<h3 style="text-align : center;">Page '.$page.'</h3>
<div class="information_case">
<ul class="ville">';
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
$requete = "SELECT COUNT(*) FROM journal WHERE id_perso = ".$joueur['ID'];
$req = $db->query($requete);
$row = $db->read_row($req);
$page_max = ceil($row[0] / $nombre_action_journal);
$req = $db->query($requete);
$requete = "SELECT * FROM journal WHERE id_perso = ".$joueur['ID'].$and." ORDER by time DESC, id DESC LIMIT ".$limit1.", ".$limit2;
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	//Convertit la date en un format plus court
	echo affiche_ligne_journal($row);
}
?>
</ul>
<?php
if($page > 1)
{
	$a1 = '<a href="javascript:envoiInfo(\'journal.php?page='.($page - 1).'\', \'information\');">';
	$a2 = '</a>';
}
else
{
	$a1 = '';
	$a2 = '';
}
if($page != $page_max)
{
	$a3 = '<a href="javascript:envoiInfo(\'journal.php?page='.($page + 1).'\', \'information\');">';
	$a4 = '</a>';
}
else
{
	$a3 = '';
	$a4 = '';
}
echo '</div>';
echo $a1.'<-- Page précédente'.$a2.' / '.$a3.'Page suivante -->'.$a4;
?>