<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
?>
<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="<?php echo $root; ?>css/index2.css" />

<?php
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
?>
<div id="site">
	<div id="haut">
		<a href="<?php echo $root; ?>index.php"><img src="<?php echo $root; ?>image/logossot.png" /></a>
		<div class="ident">
<?php
			$joueur = recupperso($_SESSION['ID']);		
		$joueur = check_perso($joueur);
		echo $_SESSION['nom'].' - Niv '.$joueur['level'].'<br />
		PA  = '.$joueur['pa'].' / '.$G_PA_max.'<br />
		HP '.genere_image_hp($joueur).'<br />
		MP '.genere_image_mp($joueur).'<br />';
	
	echo'	</div>';
	
	?>
	
					<div class="grade">
					<table>
						<tr>
							<td width="40%">
								Niveau
							</td>
							<td>
								: <?php echo $joueur['level']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Grade
							</td>
							<td>
								: <?php echo $joueur['grade']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Stars
							</td>
							<td>
								: <?php echo $joueur['star']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Joueurs tués
							</td>
							<td>
								: <?php echo $joueur['frag']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Nombre de mort
							</td>
							<td>
								: <?php echo $joueur['mort']; ?>
							</td>
						</tr>
					</table>
				</div>
	<?php
	
	if(array_key_exists('ID', $_SESSION))
	{
		//Affichage de nouveaux messages
		//Détermine le nombre de message du joueur
		$requete = "SELECT * FROM message WHERE id_dest = '".$_SESSION['ID']."' AND FIND_IN_SET('lu', type) = 0";
		$req = $db->query($requete);
		?>
		<div id="news">
			<div class="titre">
			<?php
			if($db->num_rows == 0) echo 'Pas de nouveaux messages';
			else
			{
				echo '<a href="jeu2.php?page_info=messagerie.php">Vous avez '.$db->num_rows.' nouveaux messages</a>';
			}
			?>
			</div>
			<br />
			<?php
			while($row = $db->read_assoc($req))
			{
				$date = strftime("%d/%m/%Y à %H:%M", $row['date']);
				echo 'Le '.$date.' par '.$row['nom_envoi'].' - <strong>Titre : '.$row['titre'].'</strong><br />';
			}
			?>
			<br />
			<br />
		<?php
		//Affichage du journal des nouvelles actions du joueur
		if($journal != '')
		{
			?>
				<div class="titre">
					Journal des dernières actions
				</div>
					<ul class="ville">
					<?php
					echo $journal;
					?>
					</ul>
			<?php
		}
		//Affichage du MOTK si besoin
		$requete = "SELECT * FROM motk WHERE race = '".$joueur['race']."'";
		if($row['date'] >= $joueur['dernier_connexion'])
		{
			?>
			<div class="titre">
				Les dernières infos du roi
			</div>
			<?php
			echo nl2br($row['message']);
		}
		?>
		</div>
		<?php
	}
	?>
		<div id="presentation">
			<div class="titre">
				Présentation
			</div>
			Bienvenue dans le monde de Starshine-Online.<br />
			Pour l'instant au stade de la béta (c'est à dire en phase d'équilibrage et d'amélioration du monde), starshine-online sera un jeu de rôle massivement mutijoueur en tour par tour.<br />
			Il vous permettra d'entrer dans la peau d'un grand héros de l'univers Starshine peuplé de nombreuses créatures et d'autres héros ennemis près a tout pour détruire votre peuple.<br />
			<br />
			<strong>N'oubliez pas de reporter les bugs et problèmes, et de suggérer de nouvelles choses sur le forum.</strong>
		</div>
		<div id="news">
			<div class="titre">
				News
			</div>
	<?php
	
	$requete = "SELECT * FROM punbbtopics WHERE (forum_id = 2) ORDER BY posted DESC";
	$req = $db->query($requete);
	
	$i = 0;
	while($row = $db->read_array($req))
	{
		echo '<div class="titre_news"><img src="image/logo_news.png" alt="" style="float : left;" /> <strong><a class="news" href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'.$row['subject'].'</a></strong><br />
		<span class="heure">Par '.$row['poster'].', le '.date("l d F Y à H:i", $row['posted']).'</span><!-- <span style="font-size : 10px;"> ('.($row['num_replies']).' commentaires)</span> --></div>'; 
		if ($i < 5)
		{
			$requete_post = "SELECT * FROM punbbposts WHERE (topic_id = ".$row['id'].") ORDER BY id ASC";
			$req_post = $db->query($requete_post);
			$row_post = $db->read_array($req_post);
			$message = nl2br($row_post['message']);
			$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
			$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
			$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
			echo '<div class="news">'.$message.'</div>';
		}
		$i++;
	}
	
	?>
		</div>
	<?php
}

	//Connecté
	if (isset($_SESSION['nom']))
	{
		echo '
		<div id="jouer">
			<p style="text-align : center;"><a href="jeu2.php">Cliquez ici pour accéder au jeu.</a></p>
		</div>';
	}
	?>
	</div>
	<?php
include('bas.php');

?>