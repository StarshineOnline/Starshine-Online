<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu.php');
	//Si le joueur est connecté on affiche le menu de droite

	echo '<div id="contenu">';
		echo '<div id="centre2">';

	if(array_key_exists('ID', $_SESSION))
	{
		echo '<div class="titre">
		Les dernières infos du roi
		</div>';
		echo '<div id="news">';
		//Si message du roi
		$requete = "SELECT * FROM motk WHERE race = '".$joueur['race']."'";
		$req_m = $db->query($requete);
		$row_m = $db->read_assoc($req_m);
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
		//Affichage de nouveaux messages
		//Détermine le nombre de message du joueur
		$requete = "SELECT * FROM message WHERE id_dest = '".$_SESSION['ID']."' AND FIND_IN_SET('lu', type) = 0";
		$req = $db->query($requete);
		echo '</div>';
		echo '<div class="titre">
		Message
		</div>
		<div id="news">
		';
		if($db->num_rows == 0) 
		{
			echo '<p>Pas de nouveaux messages</p>';
		}
		else
		{
			echo '<p><a href="jeu2.php?page_info=messagerie.php">Vous avez '.$db->num_rows.' nouveaux messages</a></p>';
		}
		echo '<p>';
		while($row = $db->read_assoc($req))
		{
			$date = strftime("%d/%m/%Y à %H:%M", $row['date']);
			echo 'Le '.$date.' par '.$row['nom_envoi'].' - <strong>Titre : '.$row['titre'].'</strong><br />';
		}
		echo '</p>';
		echo '</div>';
		//Affichage du journal des nouvelles actions du joueur
		if($journal != '')
		{
				echo '<div class="titre">';
				echo '<p>Journal des dernières actions</p>';
				echo '</div>';
				echo '<ul class="ville" id="news">';
				echo '<li>'.$journal.'</li>';
				echo '</ul>';

		}
	}
	else
	{
		echo '
			<div class="titre">
				Présentation
			</div>
			<div id="news">

				<p>Bienvenue dans le monde de Starshine-Online.<br />
				Pour l\'instant au stade de la béta (c\'est à dire en phase d\'équilibrage et d\'amélioration du monde), starshine-online sera un jeu de rôle massivement mutijoueur (mmorpg) en tour par tour.<br />
				Il vous permettra d\'entrer dans la peau d\'un grand héros de l\'univers Starshine peuplé de nombreuses créatures et d\'autres héros ennemis près a tout pour détruire votre peuple.<br />
				<br />
				Il est recommandé d\'avoir un navigateur dernière génération pour jouer à Starshine, nous vous conseillons <a href="http://www.mozilla-europe.org/fr/products/firefox/">Firefox</a>.<br />
				<strong>N\'oubliez pas de reporter les bugs et problèmes, et de suggérer de nouvelles choses sur le forum.</strong>
				</div>';
	}

	echo '
	<div class="titre">
				News
				</div>
				<div id="news">
				';
	require('connect_forum.php');
	$requete = "SELECT * FROM punbbtopics WHERE (forum_id = 5) ORDER BY posted DESC";
	$req = $db_forum->query($requete);

	$i = 0;
	while($row = $db_forum->read_array($req) AND $i < 15)
	{
		echo '<div class="titre_news"><strong><a class="news" href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'.utf8_encode($row['subject']).'</a></strong><br />
		<span style="font-size:10px;">Par '.$row['poster'].', le '.date("l d F Y à H:i", $row['posted']).'</span><!-- <span style="font-size : 10px;"> ('.($row['num_replies']).' commentaires)</span> --></div>';
		if ($i < 5)
		{
			$requete_post = "SELECT * FROM punbbposts WHERE (topic_id = ".$row['id'].") ORDER BY id ASC";
			$req_post = $db_forum->query($requete_post);
			$row_post = $db_forum->read_array($req_post);
			$message = utf8_encode(nl2br($row_post['message']));
			$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
			$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
			$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
			$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
			if(strlen($message) > 600)
			{
				$message = mb_substr($message, 0, 600);
				$message .= '<br /><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">Lire la suite</a>';
			}
			echo '<div class="news">'.$message.'</div>';
		}
		$i++;
	}
	echo '</div>';
	echo '</div>';
	include('menu_d.php');

}

/*	//Connecté
	if (isset($_SESSION['nom']))
	{
		echo '
		<div id="jouer">
			<p style="text-align : center;"><a href="jeu2.php">Cliquez ici pour accéder au jeu.</a></p>
		</div>';
	}
*/
echo '</div>';

include('bas.php');

?>