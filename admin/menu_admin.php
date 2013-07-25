<?php //  -*- tab-width:2; mode: php  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

if( !array_key_exists('droits', $_SESSION) or !($_SESSION['droits'] & joueur::droit_interf_admin) )
{
  header("HTTP/1.1 401 Unauthorized" );
  exit();
}
else
{
	//include_once(root.'inc/verif_log_admin.inc.php');
	
	$i = 0; // Avec un index, on pourra réordonner tranquille
	$menu = array();
	$acces = array();

	$menu[$i]['nom'] = 'Carte globale';
	$menu[$i]['url'] = 'testmap.php';
	$menu[$i++]['acces'] = joueur::droit_modo | joueur::droit_anim;

	$menu[$i]['nom'] = 'Information du monde';
	$menu[$i]['url'] = 'admin_mess.php';
	$menu[$i++]['acces'] = joueur::droit_anim;

	$menu[$i]['nom'] = '--'; // Séparateur
	$menu[$i]['url'] = '';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Editeur de carte';
	$menu[$i]['url'] = 'edit_map_full.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept | joueur::droit_graph;

	$menu[$i]['nom'] = 'Editeur de donjon';
	$menu[$i]['url'] = 'edit_donjon.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Editeur de calque supérieur';
	$menu[$i]['url'] = 'edit_calque_sup.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_graph;

	$menu[$i]['nom'] = 'Editeur de zone';
	$menu[$i]['url'] = 'zone_mapping.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_graph;

	$menu[$i]['nom'] = 'Editeur de zone sonore';
	$menu[$i]['url'] = 'zone_sound_mapping.php';
	$menu[$i++]['acces'] = joueur::droit_concept;

	$menu[$i]['nom'] = 'Marees';
	$menu[$i]['url'] = 'marees.php';
	$menu[$i++]['acces'] = joueur::droit_concept;

	$menu[$i]['nom'] = 'Calendrier';
	$menu[$i]['url'] = 'calendrier.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = '--'; // Séparateur
	$menu[$i]['url'] = '';
	$menu[$i++]['acces'] = '';

	$menu[$i]['nom'] = 'Editeur de PNJ';
	$menu[$i]['url'] = 'edit_pnj.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Editeur de constructions';
	$menu[$i]['url'] = 'edit_constr.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Création de quête';
	$menu[$i]['url'] = 'create_quete.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Édition de quête';
	$menu[$i]['url'] = 'edit_quete.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Création d\'un monstre';
	$menu[$i]['url'] = 'create_monstre.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Création d\'un grimoire';
	$menu[$i]['url'] = 'create_grimoire.php';
	$menu[$i++]['acces'] = joueur::droit_concept;

	$menu[$i]['nom'] = 'Edition d\'un monstre';
	$menu[$i]['url'] = 'edit_monstre.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_concept;

	$menu[$i]['nom'] = 'Dernières paroles de monstre';
  $menu[$i]['url'] = 'dernieres_paroles.php';
  $menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_graph;

	$menu[$i]['nom'] = 'Bourgs';
	$menu[$i]['url'] = 'admin_bourg.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_modo;

	$menu[$i]['nom'] = 'Stats royaumes';
	$menu[$i]['url'] = 'admin_stats_royaume.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_modo;

	$menu[$i]['nom'] = 'Statistiques';
	$menu[$i]['url'] = 'admin_stats.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_modo;
	
	$menu[$i]['nom'] = 'Liste des persos';
	$menu[$i]['url'] = 'admin_joueur.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_modo;

	$menu[$i]['nom'] = 'Contrôle des persos/pnj';
	$menu[$i]['url'] = 'controle_joueur.php';
	$menu[$i++]['acces'] = joueur::droit_anim | joueur::droit_modo | joueur::droit_admin;
	
	$menu[$i]['nom'] = 'Log admin';
	$menu[$i]['url'] = 'log_admin.php';
	$menu[$i++]['acces'] = joueur::droit_concept | joueur::droit_modo | joueur::droit_prog;
	
	$menu[$i]['nom'] = 'Multi-Compte';
	$menu[$i]['url'] = 'admin_2.php';
	$menu[$i++]['acces'] = joueur::droit_modo;

	$menu[$i]['nom'] = 'Comparateur de connexion';
	$menu[$i]['url'] = 'compare_connexion.php';
	$menu[$i++]['acces'] = joueur::droit_modo;
	
	$menu[$i]['nom'] = 'Performances';
	$menu[$i]['url'] = 'http://munin.starshine-online.com';
	$menu[$i++]['acces'] = joueur::droit_interf_admin;

	$menu[$i]['nom'] = 'Stats Web';
	$menu[$i]['url'] = 'http://www.starshine-online.com/piwik';
	$menu[$i++]['acces'] = joueur::droit_interf_admin;
	
	/*$menu[$i]['nom'] = 'Etude HV';
	$menu[$i]['url'] = 'etude_hotel.php';
	$menu[$i++]['acces'] = joueur::droit_admin;*/
	
	$menu[$i]['nom'] = 'Visualiseur de donjon';
	$menu[$i]['url'] = 'view_donjon.php';
	$menu[$i++]['acces'] = joueur::droit_concept;

	$menu[$i]['nom'] = 'Arènes';
	$menu[$i]['url'] = 'arenes.php';
	$menu[$i++]['acces'] = joueur::droit_anim;
	
	
	$menu[$i]['nom'] = 'Gestion des titres honorifiques';
	$menu[$i]['url'] = 'titre_honorifique.php';
	$menu[$i++]['acces'] = joueur::droit_anim;

	$menu[$i]['nom'] = 'Event';
	$menu[$i]['url'] = 'event.php';
	$menu[$i++]['acces'] = joueur::droit_anim;

	$menu[$i]['nom'] = 'Jabber / Admin';
	$menu[$i]['url'] = 'admin_jabber.php';
	$menu[$i++]['acces'] = joueur::droit_admin;

	$menu[$i]['nom'] = 'Debuff Cacophonie';
	$menu[$i]['url'] = 'debuff_race.php';
	$menu[$i++]['acces'] = joueur::droit_modo | joueur::droit_anim;
	
	$menu[$i]['nom'] = 'Creation de gemmes';
	$menu[$i]['url'] = 'create_gemme.php';
	$menu[$i++]['acces'] = joueur::droit_concept;
	
	/*$menu[$i]['nom'] = 'Debug D\'inventaire';
	$menu[$i]['url'] = 'debug_inventaire.php';
	$menu[$i++]['acces'] = joueur::droit_prog;*/
	
	$menu[$i]['nom'] = 'Gestion des classes';
	$menu[$i]['url'] = 'edit_classe.php';
	$menu[$i++]['acces'] = joueur::droit_concept;

	}
	?>
	
	<div id="menuindex">

		<div class="sousmenu">
			<div class="hautsousmenu">
				<a id="admintitre" href="index.php">Administration</a>
			</div>
			<div class="milieusousmenu">
				<ul class="listemenu">
				<?php
				foreach($menu as $item)
				{
					if($item['nom'] == '--')
					{
						echo '<li class="separator"><hr /></li>';
						continue;
					}
					if($_SESSION['droits'] & $item['acces'])
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
