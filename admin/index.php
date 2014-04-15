<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

?>
<div id="site"><?php
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Load
			</div>
			<img src="http://munin.starshine-online.com/localdomain/localhost.localdomain/load-day.png" /> <img src="http://munin.starshine-online.com/localdomain/localhost.localdomain/if_eth0-day.png" />
		<div style="float:right; width:400px;">
			<div class="titre">Scripts</div>
				<br />
				<?php
					$joueur = joueur::factory();
					$admin = $joueur->get_droits() & joueur::droit_admin;
					$fich = fopen($G_logs.'journalier.txt', 'r');
					if( $fich )
					{
						echo '<b>Journaliers</b><br/>';
						echo 'Date : <i>'.fgets($fich).'<i><br/>';
						$infos = array();
						while( $lgn = fgets($fich) )
						{
							$lgn = explode('=', $lgn);
							$infos[ $lgn[0] ] = $lgn[1];
						}
						if( $admin )
							echo 'Dossier : <i>'.$infos['dossier'].'<i><br/>';
						echo ($admin?'<a href="logs_scripts.php?script=journalier">Journalier 1</a> : ':'Journalier 1 : ').($infos['journalier1']==0?'ok':'erreur').'<br/>';
						echo ($admin?'<a href="logs_scripts.php?script=journalier2">Journalier 2</a> : ':'Journalier 2 : ').($infos['journalier2']==0?'ok':'erreur').'<br/>';
						echo ($admin?'<a href="logs_scripts.php?script=journalier3">Journalier 3</a> : ':'Journalier 3 : ').($infos['journalier3']==0?'ok':'erreur').'<br/>';
						echo ($admin?'<a href="logs_scripts.php?script=journalier4">Journalier 4</a> : ':'Journalier 4 : ').($infos['journalier4']==0?'ok':'erreur').'<br/>';
					}
					fclose($fich);
					$fich = fopen($G_logs.'horaire.txt', 'r');
					if( $fich )
					{
						echo '<b>Horaire</b><br/>';
						echo 'Date : <i>'.fgets($fich).'<i><br/>';
						$infos = array();
						while( $lgn = fgets($fich) )
						{
							$lgn = explode('=', $lgn);
							$infos[ $lgn[0] ] = $lgn[1];
						}
						if( $admin )
							echo 'Dossier : <i>'.$infos['dossier'].'<i><br/>';
						echo ($admin?'<a href="logs_scripts.php?script=horaire">Horraire</a> : ':'Horraire : ').($infos['horaire']==0?'ok':'erreur').'<br/>';
					}
					fclose($fich);
					$fich = fopen($G_logs.'calendrier.txt', 'r');
					if( $fich )
					{
						echo '<b>Horaire</b><br/>';
						echo 'Date : <i>'.fgets($fich).'<i><br/>';
						$infos = array();
						while( $lgn = fgets($fich) )
						{
							$lgn = explode('=', $lgn);
							$infos[ $lgn[0] ] = $lgn[1];
						}
						echo ($admin?'<a href="logs_scripts.php?script=calendrier">Calendrier</a> : ':'Calendrier : ').($infos['calendrier']==0?'ok':'erreur').'<br/>';
					}
					fclose($fich);
					if( $admin )
					{
						$fich = fopen($G_logs.'sauvegarde.txt', 'r');
						if( $fich )
						{
							echo '<b>Sauvegarde</b><br/>';
							echo 'Date : <i>'.fgets($fich).'<i><br/>';
							$infos = array();
							$infos_jour = array();
							$infos_mois = array();
							$jour = array();
							$mois = array();
							if( trim($lgn=fgets($fich)) == 'local:' )
							{
								if( trim($lgn=fgets($fich)) == 'journalier:' )
								{
									while( strpos($lgn = fgets($fich), ':') === false )
									{
										if( $lgn[ strlen($lgn)-2 ] != '/' )
											$jour[] = $lgn;
									}
								}
								if( trim($lgn) == 'mensuel:' )
								{
									while( strpos($lgn = fgets($fich), ':') === false )
									{
										if( $lgn[ strlen($lgn)-1 ] != '/' )
											$mois[] = $lgn;
									}
								}
							}
							if( trim($lgn) == 'Distant:' )
							{
								$type = false;
								while( $lgn = fgets($fich) )
								{
									switch( trim($lgn) )
									{
									case 'Journalier:':
										$type = 'jour';
										break;
									case 'Mensuel:':
										$type = 'mois';
										break;
									default:
										$lgn = explode('=', $lgn);
										switch($type)
										{
										case 'jour':
											$infos_jour[ $lgn[0] ] = $lgn[1];
											break;
										case 'jour':
											$infos_mois[ $lgn[0] ] = $lgn[1];
											break;
										default:
											$infos[ $lgn[0] ] = $lgn[1];
										}
									}
								}
							}
							else
								echo 'erreur distant : '.$lgn.'<br/>';
							echo 'Compte : <i>'.$infos['email'].'<i><br/>';
							if($jour)
							{
								echo 'Journalier:<br />';
								echo 'Fichier : '.$infos_jour['Titre'].'<br/>';
								foreach( $jour as $fich )
									echo $fich.'<br />';
								echo 'Taille : '.(round($infos_jour['Taille']/1000)/1000).' MO<br/>';
								echo 'Transfert : '.$infos_jour['md5'].'<br/>';
								$suppr = explode(',', $infos_jour['Suppression']);
								echo 'Ancien supprimé ('.$suppr[0].') : '.$suppr[1].'<br/>';
							}
							if($mois)
							{
								echo 'Mensuel:<br />';
								echo 'Fichier : '.$infos_mois['Titre'].'<br/>';
								foreach( $mois as $fich )
									echo $fich.'<br />';
								echo 'Taille : '.(round($infos_mois['Taille']/1000)/1000).' MO<br/>';
								echo 'Transfert : '.$infos_mois['md5'].'<br/>';
								$suppr = explode(',', $infos_mois['Suppression']);
								echo 'Ancien supprimé ('.$suppr[0].') : '.$suppr[1].'<br/>';
							}
						}
						fclose($fich);
					}
				?>
		</div>
			<div class="titre">
				Derniers loots
			</div>
			<table>
			<tr>
				<td>
					Joueur
				</td>
				<td>
					Loot
				</td>
				<td>
					Date
				</td>
			</tr>
			<?php
			//Derniers Loots
			$requete = "SELECT * FROM journal WHERE action = 'loot' ORDER BY time DESC LIMIT 0, 10";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$joueur = new perso($row['id_perso']);
				?>
			<tr>
				<td>
					<a href="admin_joueur.php?direction=info_joueur&id=<?php echo $row['id_perso']; ?>"><?php echo $joueur->get_nom(); ?></a>
				</td>
				<td>
					<?php echo $row['valeur']; ?>
				</td>
				<td>
					<?php echo $row['time']; ?>
				</td>
			</tr>
				<?php
			}
			?>
			<table>
		</div>
	</div>
	<?php
	include_once(root.'bas.php');
}
?>
