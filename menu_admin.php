<?php
if ($maintenance)
{
}
else
{
	include('inc/verif_log_admin.inc.php');
	
	$menu = array();
	$menu[0]['nom'] = 'Wiki Admin';
	$menu[0]['url'] = 'http://wiki.starshinebox.info';
	$menu[0]['acces'] = 'admin';
	$menu[1]['nom'] = 'Carte globale';
	$menu[1]['url'] = 'testmap.php';
	$menu[1]['acces'] = 'admin';
	$menu[2]['nom'] = 'Information du monde';
	$menu[2]['url'] = 'admin_mess.php';
	$menu[2]['acces'] = 'admin';
	$menu[3]['nom'] = 'Editeur de carte';
	$menu[3]['url'] = 'edit_map3.php';
	$menu[3]['acces'] = 'admin';
	$menu[4]['nom'] = 'Editeur de donjon';
	$menu[4]['url'] = 'edit_donjon.php';
	$menu[4]['acces'] = 'admin';
	$menu[5]['nom'] = 'Création de quête';
	$menu[5]['url'] = 'create_quete.php';
	$menu[5]['acces'] = 'admin';
	$menu[6]['nom'] = 'Création d\'un monstre';
	$menu[6]['url'] = 'create_monstre.php';
	$menu[6]['acces'] = 'admin';
	$menu[7]['nom'] = 'Création d\'un grimoire';
	$menu[7]['url'] = 'create_grimoire.php';
	$menu[7]['acces'] = 'admin';
	$menu[8]['nom'] = 'Edition d\'un monstre';
	$menu[8]['url'] = 'edit_monstre.php';
	$menu[8]['acces'] = 'admin';
	$menu[9]['nom'] = 'Bourgs';
	$menu[9]['url'] = 'admin_bourg.php';
	$menu[9]['acces'] = 'admin';
	$menu[10]['nom'] = 'Stats royaumes';
	$menu[10]['url'] = 'admin_stats_royaume.php';
	$menu[10]['acces'] = 'admin';
	$menu[11]['nom'] = 'Statistiques';
	$menu[11]['url'] = 'admin_stats.php';
	$menu[11]['acces'] = 'admin';
	$menu[12]['nom'] = 'Liste des persos';
	$menu[12]['url'] = 'admin_joueur.php';
	$menu[12]['acces'] = '';
	$menu[13]['nom'] = 'Multi-Compte';
	$menu[13]['url'] = 'admin_2.php';
	$menu[13]['acces'] = '';
	$menu[14]['nom'] = 'Comparateur de connexion';
	$menu[14]['url'] = 'compare_connexion.php';
	$menu[14]['acces'] = '';
	$menu[15]['nom'] = 'Webalizer';
	$menu[15]['url'] = 'http://webalizer.starshinebox.info';
	$menu[15]['acces'] = '';
	$menu[16]['nom'] = 'Performances';
	$menu[16]['url'] = 'http://munin.starshine-online';
	$menu[16]['acces'] = '';
	$menu[17]['nom'] = 'Etude HV';
	$menu[17]['url'] = 'etude_hotel.php';
	$menu[17]['acces'] = 'admin';
	$menu[18]['nom'] = 'Visualiseur de donjon';
	$menu[18]['url'] = 'view_donjon.php';
	$menu[18]['acces'] = 'admin';

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
					if($item['acces'] == '' OR ($item['acces'] == 'admin' AND $_SESSION['admin_nom'] == 'admin'))
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