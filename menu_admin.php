<?php
if ($maintenance)
{
}
else
{
	include('inc/verif_log_admin.inc.php');
	}
	?>
	
	<div id="menuindex">

		<div class="sousmenu">
			<div class="hautsousmenu">
				Administration
			</div>
			<div class="milieusousmenu">
				<ul class="listemenu">
				<?php
				if ($_SESSION['admin_nom'] == 'admin')
				{
				
				?>
				
						<li><a href="http://wiki.starshinebox.info">Wiki Admin</a></li>				
						<li><a href="testmap.php">Carte globale</a></li>
						<li><a href="admin_mess.php">Information du monde</a></li>
						<li><a href="edit_map3.php">Editeur de carte</a></li>
						<li><a href="edit_donjon.php">Editeur de donjon</a></li>
						<li><a href="create_quete.php">Création de quête</a></li>
						<li><a href="create_monstre.php">Création d'un monstre</a></li>
						<li><a href="create_grimoire.php">Création d'un grimoire</a></li>
						<li><a href="edit_monstre.php">Edition d'un monstre</a></li>
						<li><a href="admin_stats_royaume.php">Stats royaumes</a></li>
						<li><a href="admin_stats.php">Statistiques</a></li>
				<?php
				}

				
				?>
						<li><a href="admin_joueur.php">Liste des persos</a></li>
						<li><a href="admin_2.php">Multi-Compte</a></li>
						<li><a href="compare_connexion.php">Comparateur de connexion</a></li>
						<li><a href="http://stats.starshinebox.info">Webalizer</a></li>
				</ul>
			</div>
		</div>
	</div>