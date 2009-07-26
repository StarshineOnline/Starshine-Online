<?php
if ($maintenance)
{
}
else
{
	include('inc/verif_log_admin.inc.php');
	
  $i = 0; // Avec un index, on pourra réordonner tranquille
	$menu = array();

	$menu[$i]['nom'] = 'Wiki Admin';
	$menu[$i]['url'] = 'http://wiki.starshinebox.info';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Carte globale';
	$menu[$i]['url'] = 'testmap.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Information du monde';
	$menu[$i]['url'] = 'admin_mess.php';
	$menu[$i++]['acces'] = 'admin';

  $menu[$i++]['nom'] = '--'; // Séparateur

	$menu[$i]['nom'] = 'Editeur de carte';
	$menu[$i]['url'] = 'edit_map3.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Editeur de donjon';
	$menu[$i]['url'] = 'edit_donjon.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Création de quête';
	$menu[$i]['url'] = 'create_quete.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Création d\'un monstre';
	$menu[$i]['url'] = 'create_monstre.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Création d\'un grimoire';
	$menu[$i]['url'] = 'create_grimoire.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Edition d\'un monstre';
	$menu[$i]['url'] = 'edit_monstre.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Bourgs';
	$menu[$i]['url'] = 'admin_bourg.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Stats royaumes';
	$menu[$i]['url'] = 'admin_stats_royaume.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Statistiques';
	$menu[$i]['url'] = 'admin_stats.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Liste des persos';
	$menu[$i]['url'] = 'admin_joueur.php';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Multi-Compte';
	$menu[$i]['url'] = 'admin_2.php';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Comparateur de connexion';
	$menu[$i]['url'] = 'compare_connexion.php';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Webalizer';
	$menu[$i]['url'] = 'http://webalizer.starshinebox.info';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Performances';
	$menu[$i]['url'] = 'http://munin.starshine-online';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Etude HV';
	$menu[$i]['url'] = 'etude_hotel.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Visualiseur de donjon';
	$menu[$i]['url'] = 'view_donjon.php';
	$menu[$i++]['acces'] = 'admin';

	$menu[$i]['nom'] = 'Jabber / Admin';
	$menu[$i]['url'] = 'admin_jabber.php';
	$menu[$i++]['acces'] = 'admin';

	}
	?>
	
	<div id="menuindex">

		<div class="sousmenu">
			<div class="hautsousmenu">
				<a href="admin_index.php">Administration</a>
			</div>
			<div class="milieusousmenu">
				<ul class="listemenu">
				<?php
				foreach($menu as $item)
				{
          if($item['nom'] == '--') {
            echo '<li><hr /></li>';
            continue;
          }
					if($item['acces'] == '' OR ($item['acces'] == 'admin' AND 
                                      ($_SESSION['admin_nom'] == 'admin' OR 
                                       $_SESSION['admin_db_auth'] == 'admin')))
					{
				?>
						<li><a href="<?php echo $item['url']; ?>"><?php echo $item['nom']; ?></a></li>
				<?php
					}
				}
				?>
				</ul>
			</div>
		</div>
	</div>