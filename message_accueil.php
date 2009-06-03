<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

setcookie('dernier_affichage_popup', time(), time() + (24 * 3600 * 31));

if(array_key_exists('affiche', $_GET)) $affiche = $_GET['affiche']; else $affiche = false;

//Si message du roi
$requete = "SELECT * FROM motk WHERE race = '".$joueur['race']."'";
$req_m = $db->query($requete);
$row_m = $db->read_assoc($req_m);
if ($_COOKIE['dernier_affichage_popup'] <= $row_m['date'] OR $affiche == 'all')
{
	$message = htmlspecialchars(stripslashes($row_m['message']));
	$message = str_replace('[br]', '<br />', $message);
	//$message = $amessage.$message;
	$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
	$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
	$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
	$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
	$message = str_replace("[/color]", "</span>", $message);
	$regCouleur = "\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]";
	$message = eregi_replace($regCouleur, "<span style=\"color: \\1\">", $message);
	foreach ($G_autorisations as $balise => $grades) {
		if (!in_array($joueur['rang_royaume'], $grades)) {
			//$message = eregi_replace("\[$balise\].*?\[/$balise\]", '', $message);
			$message = preg_replace("/\[$balise\].*?\[\\/$balise\]/i", '', $message);
	  }
	  else {
	  	//$message = eregi_replace("\[$balise\](.+?)\[/$balise\]", '<small class="confidentiel">R&eacute;serv&eacute; aux '.$balise.' :\\1 </small>', $message);
	  	$message = preg_replace("/\[$balise\](.+?)\[\\/$balise\]/i", '<small class="confidentiel">R&eacute;serv&eacute; aux '.$balise.' : \\1 </small>', $message);
	  }
	}
	//$message = eregi_replace("\[color=(\#[0-9A-F]{6}|[a-z\-]+)\](.*?)\[/color\]", "<span style=\"color : #\\1 \">\\2</span>", $text);
	$motk = '
	<p>'.$message.'</p>';
	echo $motk;
	echo "<hr>";
}

//On cherche les derniers événements de ce joueur.
$requete_journal = "SELECT * FROM journal WHERE id_perso = ".$joueur['ID']." AND time > '".date("Y-m-d H:i:s", $_COOKIE['dernier_affichage_popup'])."' ORDER BY time ASC, id ASC";
$req_journal = $db->query($requete_journal);
while($row_journal = $db->read_assoc($req_journal))
{
	$journal .= affiche_ligne_journal($row_journal);
}
if($journal != '')
{
		echo '<div class="titre">';
		echo '<p>Journal des dernières actions</p>';
		echo '</div>';
		echo '<ul>';
		echo '<li>'.$journal.'</li>';
		echo '</ul>';
		echo '<hr>';
}

require('connect_forum.php');

$requete_news = "SELECT * FROM punbbtopics WHERE (forum_id = 5) ORDER BY posted DESC";
$req_news = $db_forum->query($requete_news);

$row_news = $db_forum->read_array($req_news);
if ($_COOKIE['dernier_affichage_popup'] <= $row_news['posted'])
{
	echo '<div class="titre_news"><strong><a class="news" href="http://forum.starshine-online.com/viewtopic.php?id='.$row_news['id'].'">'./*utf8_encode*/($row_news['subject']).'</a></strong><br />
	<span style="font-size:10px;">Par '.$row_news['poster'].', le '.date("l d F Y à H:i", $row_news['posted']).'</span><!-- <span style="font-size : 10px;"> ('.($row_news['num_replies']).' commentaires)</span> --></div>';

	$requete_post = "SELECT * FROM punbbposts WHERE (topic_id = ".$row_news['id'].") ORDER BY id ASC";
	$req_post = $db_forum->query($requete_post);
	$row_post = $db_forum->read_array($req_post);
	$message = /*utf8_encode*/(nl2br($row_post['message']));
	$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
	$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
	$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
	$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
	if(strlen($message) > 600)
	{
		$message = mb_substr($message, 0, 600);
		$message .= '<br /><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row_news['id'].'">Lire la suite</a>';
	}
	echo '<div class="news">'.$message.'</div>';
	echo "<hr>";
}


$requete = "SELECT * FROM punbbtopics WHERE (forum_id = ".$Trace[$joueur['race']]['forum_id'].") ORDER BY last_post DESC";
$req = $db_forum->query($requete);

$i = 0;
while($row = $db_forum->read_array($req) AND $i < 5)
{
	echo '<div class="titre_news"><strong><a class="news" href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'./*utf8_encode*/($row['subject']).'</a></strong><br />
	<span style="font-size:10px;">Par '.$row['last_poster'].', le '.date("l d F Y à H:i", $row['last_post']).'</span><!-- <span style="font-size : 10px;"> ('.($row['num_replies']).' commentaires)</span> --></div>';
	$i++;
}
?>