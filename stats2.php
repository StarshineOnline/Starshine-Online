<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte complète, l\'expérience acquérie grâce à l\'alpha m\'a permis de voir les gros problèmes qui pourraient se poser.<br />
	Je vais donc travailler sur la béta.<br />';
}
else
{
	include('menu.php');
	
	//Tableau regroupant les statistiques par type
	$stats = array();
	$stats['race']['race']['titre'] = '# Les races #';
	$stats['race']['race']['race']['image'] = 'stat_race.png';
	$stats['race']['race']['race']['titre'] = 'Les races';
	$stats['race']['star']['titre'] = '# Les stars #';
	$stats['race']['star']['star1']['image'] = 'stat_star1.png';
	$stats['race']['star']['star1']['titre'] = 'Stars 1';
	$stats['race']['star']['star2']['image'] = 'stat_star2.png';
	$stats['race']['star']['star2']['titre'] = 'Stars 2';
	$stats['race']['star']['star3']['image'] = 'stat_star3.png';
	$stats['race']['star']['star3']['titre'] = 'Stars 3';
	$stats['race']['carte']['titre'] = '# Carte des Royaumes #';
	$stats['race']['carte']['carte']['image'] = 'carte_royaume.png';
	$stats['race']['carte']['carte']['titre'] = 'Carte Royaume';
	$stats['global']['classe']['titre'] = '# Les classes #';
	$stats['global']['classe']['classe1']['image'] = 'stat_classe1.png';
	$stats['global']['classe']['classe1']['titre'] = 'Rang 1';
	$stats['global']['classe']['classe2']['image'] = 'stat_classe2.png';
	$stats['global']['classe']['classe2']['titre'] = 'Rang 2';
	$stats['global']['classe']['classe3']['image'] = 'stat_classe3.png';
	$stats['global']['classe']['classe3']['titre'] = 'Rang 3';
	$stats['global']['niveau']['titre'] = '# Les niveaux #';
	$stats['global']['niveau']['niveau']['image'] = 'stat_lvl.png';
	$stats['global']['niveau']['niveau']['titre'] = 'Niveaux';
	$stats['global']['joueur']['titre'] = '# Les joueurs #';
	$stats['global']['joueur']['total']['image'] = 'stat_joueur.png';
	$stats['global']['joueur']['total']['titre'] = 'Nombre de joueurs';
	$stats['global']['joueur']['niveau_moyen']['image'] = 'stat_niveau_moyen.png';
	$stats['global']['joueur']['niveau_moyen']['titre'] = 'Niveau moyen';
	$stats['global']['monstre']['titre'] = '# Nombre de monstres #';
	$stats['global']['monstre']['total']['image'] = 'stat_monstre.png';
	$stats['global']['monstre']['total']['titre'] = 'Nombre de monstres';

	$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
	$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"), date("Y")));
	
	if(!array_key_exists('categorie', $_GET)) $categorie = 'global';
	else $categorie = $_GET['categorie'];
	?>
	<div id="bloc">
		<div id="presentation">
			<div class="titre">
				Statistiques
			</div>
			<div>
				<ul>
					<li><a href="stats2.php?categorie=global">Générales</a></li>
					<li><a href="stats2.php?categorie=race">Royaumes</a></li>
				</ul>
			</div>
		</div>
		<?php
		//Historique d'un graph
		if(array_key_exists('historique', $_GET))
		{
			?>
			<div class="bloc">
				<div class="titre">
					Historique
				</div>
				<div style="text-align : center;">
					<?php
						$histo_hier_Y = date("Y", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] - 1, $_GET['annee']));
						$histo_hier_m = date("m", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] - 1, $_GET['annee']));
						$histo_hier_d = date("d", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] - 1, $_GET['annee']));
						$histo_demain_Y = date("Y", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] + 1, $_GET['annee']));
						$histo_demain_m = date("m", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] + 1, $_GET['annee']));
						$histo_demain_d = date("d", mktime(0, 0, 0, $_GET['mois'] , $_GET['jour'] + 1, $_GET['annee']));
					?>
					<a href="stats2.php?historique=y&amp;annee=<?php echo $histo_hier_Y; ?>&amp;mois=<?php echo $histo_hier_m; ?>&amp;jour=<?php echo $histo_hier_d; ?>&amp;image=<?php echo $_GET['image']; ?>">Jour précédent</a> - <a href="stats2.php?historique=y&amp;annee=<?php echo $histo_demain_Y; ?>&amp;mois=<?php echo $histo_demain_m; ?>&amp;jour=<?php echo $histo_demain_d; ?>&amp;image=<?php echo $_GET['image']; ?>">Jour suivant</a><br />
					<img src="image/stat/<?php echo $_GET['annee'].'-'.$_GET['mois'].'-'.$_GET['jour']; ?>/<?php echo $_GET['image']; ?>" alt="">
				</div>
			</div>
			<?php
		}
		//Page générale
		else
		{
			foreach($stats[$categorie] as $stat)
			{
			?>
			<div class="bloc">
				<div class="titre">
					<?php echo $stat['titre']; ?>
				</div>
				<div style="text-align : center;">
					<?php
					foreach($stat as $image)
					{
						if($image[0] != '#')
						{
							?><a href="stats2.php?historique=y&amp;annee=<?php echo date('Y'); ?>&amp;mois=<?php echo date('m'); ?>&amp;jour=<?php echo (date('d') - 1); ?>&amp;image=<?php echo $image['image']; ?>"><img src="image/<?php echo $image['image']; ?>" alt="<?php echo $image['titre']; ?>" /></a><?php
						}
					}
					?>
				</div>
			</div>
			<?php
			}
		}
		?>
	</div>
	<?php
}

	?>
</div>
	<?php
include('bas.php');

?>