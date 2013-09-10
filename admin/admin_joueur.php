<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

function is_pnj($row) {
  // TODO
  return true;
}

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	echo '
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Liste des Joueurs
	</div>
	';
	if(array_key_exists('direction', $_GET))
	{
		switch($_GET['direction'])
		{
			case 'info_joueur' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom, statut, password, classe, level, race, rang_royaume, fin_ban, x, y FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$requete = "SELECT COUNT(*) as tot FROM log_connexion WHERE message = 'Ok' AND id_joueur = ".$row['ID'];
				$req_connex = $db->query($requete);
				$row_connex = $db->read_row($req_connex);
        log_admin::log('admin', 'Consultation de la fiche de '.$joueur->get_nom());
				?>

			<table class="admin">
			<tr>
				<td style="width : 25%;">
					<strong>Nom</strong>
				</td>
				<td style="width : 25%;">
					<a href="admin_joueur.php?direction=nom&amp;id=<?php echo $row['ID']; ?>"><?php echo $row['nom']; ?></a>
				</td>
				<td style="width : 25%;">
					<strong>Statut</strong>
				</td>
				<td style="width : 25%;">
					<?php
						echo $row['statut'];
						if($row['statut'] == 'ban') echo ' pour '.transform_sec_temp($row['fin_ban'] - time());
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Race</strong>
				</td>
				<td>
					<?php echo $row['race']; ?>
				</td>
				<td>
					<strong>Niveau</strong>
				</td>
				<td>
					<?php echo $row['level']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Classe</strong>
				</td>
				<td>
					<?php echo $row['classe']; ?>
				</td>
				<td>
					<strong>Rang</strong>
				</td>
				<td>
					<?php
					//Récupération du grade
					$requete = "SELECT nom FROM grade WHERE id = ".$row['rang_royaume'];
					$req = $db->query($requete);
					$row_grade = $db->read_assoc($req);
					echo $row_grade['nom'];
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Nombre de connexions</strong>
				</td>
				<td>
					<?php echo $row_connex[0]; ?>
				</td>
				<td>
					<strong>Position</strong>
				</td>
				<td>
				 	<?php
				 	echo "x : ".$row['x']." / y : ".$row['y'];
				 	?>
				</td>
			</tr>
			<tr>
				<td>
					<strong>IPs</strong>
				</td>
				<td>
					<div style="height : 300px; overflow : auto; padding : 20px; width : 100%;">
					<ul>
				<?php
					$requete = "SELECT *, COUNT(*) as tot FROM log_connexion WHERE message = 'Ok' AND id_joueur = ".$id." GROUP BY ip";
					$req = $db->query($requete);
					while($row_log = $db->read_assoc($req))
					{
						echo '<li>'.$row_log['ip'].' / '.$row_log['tot'].' connexion(s)</li>';
					}
				?>
					</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Même mot de passe avec</strong>
				</td>
				<td>
					<ul>
				<?php
					$requete = "SELECT ID, nom FROM perso WHERE password = '".$row['password']."' AND ID <> ".$id;
					$req = $db->query($requete);
					while($row_pass = $db->read_assoc($req))
					{
						echo '<li><a href="admin_joueur.php?direction=info_joueur&amp;id='. $row_pass['ID'].'">'.$row_pass['nom'].'</a></li>';
					}
				?>
					</ul>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Actions</strong>
				</td>
				<td colspan="3">
					<ul>
						<?php if($row['statut'] != 'ban')
						{
							?><li><a href="admin_joueur.php?direction=ban&amp;id=<?php echo $_GET['id']; ?>">Banir</a></li>
							<?php
						}
						else
						{
						?>
							<li><a href="admin_joueur.php?direction=deban&amp;id=<?php echo $_GET['id']; ?>">Débanir</a></li>
						<?php
						}
						if ($_SESSION['admin_nom'] == 'admin' ||
								$_SESSION['admin_db_auth'] == 'admin' ||
                is_pnj($row))
						{
						?>
						<li><a href="admin_joueur.php?direction=objet&amp;id=<?php echo $_GET['id']; ?>">Donner un objet</a> | <a href="admin_joueur.php?direction=donnestars&amp;id=<?php echo $_GET['id']; ?>">Donner stars</a> | <a href="admin_joueur.php?direction=recette&amp;id=<?php echo $_GET['id']; ?>">Donner une recette</a> | <a href="admin_joueur.php?direction=arme&amp;id=<?php echo $_GET['id']; ?>">Donner une arme</a></li> | <a href="admin_joueur.php?direction=armure&amp;id=<?php echo $_GET['id']; ?>">Donner une armure</a> | <a href="admin_joueur.php?direction=accessoire&amp;id=<?php echo $_GET['id']; ?>">Donner un accessoire</a> | <a href="admin_joueur.php?direction=titre&amp;id=<?php echo $_GET['id']; ?>">Donner un titre</a></li>
						<li><a href="admin_joueur.php?direction=quete&amp;id=<?php echo $_GET['id']; ?>">Quêtes</a> - <a href="admin_joueur.php?direction=inventaire&amp;id=<?php echo $_GET['id']; ?>">Inventaire</a> - <a href="admin_joueur.php?direction=journal&amp;id=<?php echo $_GET['id']; ?>">Voir le journal des actions</a> | <a href="admin_joueur.php?direction=messagerie&amp;id=<?php echo $_GET['id']; ?>">Voir la messagerie</a> | <a href="admin_joueur.php?direction=donnepa&amp;id=<?php echo $_GET['id']; ?>">Donner full PA</a></li>
						<?php
						}
						?>
					</ul>
				</td>
			</tr>
			<tr>
<form name="tp_form" method="get" action="admin_joueur.php">
<td>Téléporter</td>
<td><label>x: <input type="text" name="tp_x" size="4"/></label></td>
<td><label>y: <input type="text" name="tp_y" size="4"/></label></td>
<td>
<input type="hidden" name="direction" value="tp"/>
<input type="hidden" name="id" value="<?php echo $id ?>"/>
<input type="submit"/></td>
</form>
			</tr>
			</table>
				<?php
			break;
			case 'ban' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				?>
				Vous allez bannir <?php echo $row['nom']; ?>, pour quelle durée ?
				<form method="post" action="admin_joueur.php?id=<?php echo $id; ?>&amp;direction=ban2">
					<select name="duree" id="duree">
						<option value="3600">1 heure</option>
						<option value="86400">1 jour</option>
						<option value="604800">1 semaine</option>
						<option value="2592000">1 mois</option>
						<option value="946080000">1 an</option>
						<option value="94608000000">100 ans</option>
					</select>
					<input type="submit" value="Bannir !" />
				</form>
				<?php
			break;

		case 'tp':
			$x = intval($_GET['tp_x']);
			$y = intval($_GET['tp_y']);
			$joueur = new perso($_GET['id']);
			if ($x > 0 && $y > 0)
			{
				$joueur->set_x($x);
				$joueur->set_y($y);
				$joueur->sauver();
				
        log_admin::log('admin', 'Téléportation de '.$joueur->get_nom().' en ['.$x.','.$y.']');
			}
			else
				echo "ERREUR TP: [$x][$y]<br/>";
?>
<a href="admin_joueur.php?direction=info_joueur&id=<?php echo $_GET['id'] ?>">Retour</a>
	 <script type="text/javascript">window.location = 'admin_joueur.php?direction=info_joueur&id=<?php echo $_GET['id'] ?>';</script>
<?php
			break;

      case 'titredel':
      case 'titreplus':
      case 'titre2':
        if (isset($_POST['val']))
          $db->query("insert into titre_honorifique values (null, $_GET[id], '$_POST[val]', 1)");
        if (isset($_GET['idtd']))
          $db->query("delete from titre_honorifique where id = $_GET[idtd]");
        if (isset($_GET['idtp']))
          $db->query("update titre_honorifique set niveau = niveau + 1 where id = $_GET[idtp]");
				log_admin::log('admin', 'Don du titre "'.$_POST['val'].'" à '.$joueur->get_nom());
        // pas de break: on va afficher
      case 'titre' :
        echo 'Titres déjà donnés:<ul>';
				$id = $_GET['id'];
				$requete = "SELECT titre,niveau,id FROM titre_honorifique WHERE id_perso = ".$id;
				$req = $db->query($requete);
				while ($row = $db->read_assoc($req)) {
          echo "<li>$row[titre] niveau $row[niveau] <a href=\"?direction=titreplus&amp;id=$id&amp;idtp=$row[id]\">(monter)</a> <a href=\"?direction=titredel&amp;id=$id&amp;idtd=$row[id]\">(supprimer)</a></li>\n";
        }
        echo '</ul><form method="post" action="admin_joueur.php?direction=titre2&amp;id='.$id.'"><input type="text" name="val" /><input type="submit" value="nouveau" /></form>';
        break;
			case 'ban2' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$requete = "UPDATE perso SET statut = 'ban', fin_ban = ".(time() + $_POST['duree'])." WHERE ID = ".$id;
				if($db->query($requete))
				{
					echo $row['nom'];
				?>
				a bien été banni !<br />
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $row['ID']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Ban de '.$joueur->get_nom());
				}
			break;
			case 'deban' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$requete = "UPDATE perso SET statut = 'actif', fin_ban = 0 WHERE ID = ".$id;
				if($db->query($requete))
				{
					
				?>
				Vous avez débanni <?php echo $row['nom']; ?> !<br />
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $row['ID']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Déban de '.$joueur->get_nom());
				}
			break;
			case 'objet' :
				echo 'Objet : <select id="id_objet">';
				$requete = "SELECT * FROM objet";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="o'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				$requete = "SELECT * FROM objet_royaume";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="r'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				$requete = "SELECT * FROM gemme";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="g'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				$requete = "SELECT * FROM grimoire";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="l'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				echo '
				</select><br />
				Nombre <input type="text" id="nombre" /><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=objet2&amp;id_objet=\' + document.getElementById(\'id_objet\').value + \'&amp;nombre=\' + document.getElementById(\'nombre\').value" />';
			break;
			case 'objet2' :
				$joueur = new perso($_GET['id']);
				$i = 0;
				while($i < $_GET['nombre'])
				{
					$joueur->prend_objet($_GET['id_objet']);

					$i++;
				}
				log_admin::log('admin', 'Don de '.$_GET['nombre'].' objets ('.$_GET['id_objet'].') à '.$joueur->get_nom());
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
			break;
			case 'donnestars' :
				echo 'Montant : <input type="text" id="nombre" /><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=donnestars2&amp;nombre=\' + document.getElementById(\'nombre\').value" />';
			break;
			case 'donnestars2' :
				$joueur = new perso($_GET['id']);
				$joueur->add_star($_GET['nombre']);
				$joueur->sauver();

				log_admin::log('admin', 'Don de '.$_GET['nombre'].' stars à '.$joueur->get_nom());
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
			break;
			case 'nom' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				if($db->query($requete))
				{
					echo '<form action="admin_joueur.php?id='.$id.'&amp;direction=nom2" method="post">
					Nom : <input type="text" name="nom" value="'.$row['nom'].'"/><br />
					<input type="submit" value="Valider" />
					</form>';
				}
			break;
			case 'nom2' :
				$id = $_GET['id'];
				$requete = "SELECT ID, nom FROM perso WHERE ID = ".$id;
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$nom = $_POST['nom'];
				$requete = 'UPDATE perso SET nom = "'.$nom.'" WHERE ID = '.$id;
				$db->query($requete);
			break;
			case 'journal' :
				$joueur = new perso($_GET['id']);
				$requete = "SELECT COUNT(*) FROM journal WHERE id_perso = ".$joueur->get_id();
				$req = $db->query($requete);
				$row = $db->read_row($req);
				$req = $db->query($requete);
				$requete = "SELECT * FROM journal WHERE id_perso = ".$joueur->get_id()." ORDER by time DESC, id DESC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					//Convertit la date en un format plus court
					echo affiche_ligne_journal($row);
				}
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id'];?>">Revenir à sa feuille de personnage</a>
				<?php
			break;
			case 'messagerie';
				$joueur = new perso($_GET['id']);			
				$requete = "SELECT * FROM message WHERE id_dest = ".$joueur->get_id()." OR id_envoi = ".$joueur->get_id()." ORDER BY date DESC";
				$req = $db->query($requete);	
				?>
				<table>
					<tr>
						<td>
							Titre
						</td>
						<td>
							Par
						</td>
						<td>
							Pour
						</td>
						<td>
							Date
						</td>
					</tr>
				<?php
				if($req = $db->query($requete))
				{
				$i = 0;
				while($row = $db->read_array($req))
				{
				$date = strftime("%d/%m/%Y %H:%M", $row['date']);
				?>
					<tr style="background-color:#AAA;">
						<td>
							<?php echo htmlspecialchars(stripslashes($row['titre'])); ?>
						</td>
						<td>
							<?php
							echo $row['nom_envoi'];
							?>
						</td>
						<td>
							<?php
							echo $row['nom_dest'];
							?>
						</td>
						<td>
							<?php echo $date; ?>
						</td>
					</tr>
					<tr>
						<td colspan=4>
							<?php echo $row['message']; ?>
						</td>
					</tr>
					<?php
					$i++;
					}
				}
				?>
				</table>
			<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $joueur->get_id(); ?>">Revenir à sa feuille de personnage</a>
			<?				
			break;		
			case 'arme' :
				echo 'Arme : <select id="id_arme">';
				$requete = "SELECT * FROM arme";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="a'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				echo '
				</select><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=arme2&amp;id_arme=\' + document.getElementById(\'id_arme\').value" />';
			break;
			case 'arme2' :
				$joueur = new perso($_GET['id']);
				$joueur->equip_objet($_GET['id_arme']);
				echo $G_erreur;
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Don de l\'arme '.$_GET['id_arme'].' à '.$joueur->get_nom());
			break;
			case 'armure' :
				echo 'Armure : <select id="id_armure">';
				$requete = "SELECT * FROM armure";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="p'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				echo '
				</select><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=armure2&amp;id_armure=\' + document.getElementById(\'id_armure\').value" />';
			break;
			case 'armure2' :
				$joueur = new perso($_GET['id']);
				$joueur->equip_objet($_GET['id_armure']);
				echo $G_erreur;
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Don de l\'armure '.$_GET['id_armure'].' à '.$joueur->get_nom());
			break;
			case 'accessoire' :
				echo 'Accessoire : <select id="id_accessoire">';
				$requete = "SELECT * FROM accessoire";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="m'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				echo '
				</select><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=accessoire2&amp;id_accessoire=\' + document.getElementById(\'id_accessoire\').value" />';
			break;
			case 'accessoire2' :
				$joueur = new perso($_GET['id']);
				$joueur->prend_objet($_GET['id_accessoire']);
				echo $G_erreur;
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Don de l\'accessoire '.$_GET['id_accessoire'].' à '.$joueur->get_nom());
			break;
			case 'recette' :
				echo 'Recette : <select id="id_recette">';
				$requete = "SELECT * FROM recette";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="r'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}
				echo '
				</select><br />
				Nombre <input type="text" id="nombre" /><br />
				<input type="button" value="valider" onclick="document.location = \'admin_joueur.php?id='.$_GET['id'].'&amp;direction=recette2&amp;id_recette=\' + document.getElementById(\'id_recette\').value + \'&amp;nombre=\' + document.getElementById(\'nombre\').value" />';
			break;
			case 'recette2' :
				$joueur = new perso($_GET['id']);
				$i = 0;
				while($i < $_GET['nombre'])
				{
					$joueur->prend_recette($_GET['id_recette']);
					$i++;
				}
				?>
				<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $_GET['id']; ?>">Revenir à sa feuille de personnage</a>
				<?php
				log_admin::log('admin', 'Don de la recette '.$_GET['id_recette'].' à '.$joueur->get_nom());
			break;
			case 'quete' :
				$joueur = new perso($_GET['id']);
				if($joueur->get_quete() != '')
				{
					$i = 0;
					$quete_id = array();
					$qlist = $joueur->get_quete();
					if (is_string($qlist))
						$qlist = unserialize($qlist);
					foreach($qlist as $quete)
					{
						$quete_id[] = $quete['id_quete'];
						$quest[$quete['id_quete']] = $i;
						$i++;
					}
					$i = 0;
					$ids = implode(',', $quete_id);
					$requete = 'SELECT * FROM quete WHERE id IN ('.$ids.') ORDER BY lvl_joueur ASC';
					if(count($quete_id) > 0)
					{
						$req = $db->query($requete);
						while($row = $db->read_array($req))
						{
							$objectif = unserialize($row['objectif']);
							echo '
							<li onmousemove="afficheInfo(\'info_'.$i.'\', \'block\', event, \'xmlhttprequest\');" onmouseout="afficheInfo(\'info_'.$i.'\', \'none\', event );">
								<h3 style="margin : 0px; padding : 0px; display : inline;">'.$row['nom'].'</h3> '.$qlist[$quest[$row['id']]]['objectif'][0]->nombre.' / '.$objectif[0]->nombre.'
								<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_'.$i.'">
									<h3 style="margin : 0px; padding : 0px;margin-bottom : 3px;"">'.$row['nom'].'</h3>
									<span style="font-style : italic;">Niveau conseillé '.$row['lvl_joueur'].'</span><br />
									<br />
									'.nl2br($row['description']).'
									<h3 style="margin : 0px; padding : 0px; margin-top : 5px;">Récompense</h3>
									<ul>
										<li>Stars : '.$row['star'].'</li>
										<li>Expérience : '.$row['exp'].'</li>
										<li>Honneur : '.$row['honneur'].'</li>
										<li><strong>Objets</strong> :</li>';
										$rewards = explode(';', $row['reward']);
                    while (count($rewards) > 0 && $rewards[0] == '')
                      array_shift($rewards);
										$r = 0;
										while($r < count($rewards))
										{
											$reward_exp = explode('-', $rewards[$r]);
											$reward_id = $reward_exp[0];
											$reward_id_objet = mb_substr($reward_id, 1);
											$reward_nb = $reward_exp[1];
											switch($reward_id[0])
											{
												case 'r' :
													$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
													$req_r = $db->query($requete);
													$row_r = $db->read_assoc($req_r);
													echo '<li>Recette de '.$row_r['nom'].' X '.$reward_nb.'</li>';
												break;
											}
											$r++;
										}
										echo '
									</ul>
								</div>
							</li>';
							?>
							<?php
							$i++;
						}
					}
				}
			break;
			case 'inventaire' :
				$joueur = new perso($_GET['id']);
$tab_loc = array();
$tab_loc[0]['loc'] = 'main_droite';
$tab_loc[0]['type'] = 'arme';
$tab_loc[1]['loc'] = 'main_gauche';
$tab_loc[1]['type'] = 'arme';
$tab_loc[2]['loc'] = 'tete';
$tab_loc[2]['type'] = 'armure';
$tab_loc[3]['loc'] = 'torse';
$tab_loc[3]['type'] = 'armure';
$tab_loc[4]['loc'] = 'main';
$tab_loc[4]['type'] = 'armure';
$tab_loc[5]['loc'] = 'ceinture';
$tab_loc[5]['type'] = 'armure';
$tab_loc[6]['loc'] = 'jambe';
$tab_loc[6]['type'] = 'armure';
$tab_loc[7]['loc'] = 'chaussure';
$tab_loc[7]['type'] = 'armure';
$tab_loc[8]['loc'] = 'dos';
$tab_loc[8]['type'] = 'armure';
$tab_loc[9]['loc'] = 'doigt';
$tab_loc[9]['type'] = 'armure';
$tab_loc[10]['loc'] = 'cou';
$tab_loc[10]['type'] = 'armure';
?>
<div id="inventaire">
<h2 style="width : 330px;">Inventaire</h2>
<table class="inventaire" cellspacing="0">
<tr class="header trcolor2">
	<td>
		Localisation
	</td>
	<td>
		Objet
	</td>
	<td>
		Effets
	</td>
	<td>
	</td>
</tr>
<?php
$color = 2;
foreach($tab_loc as $loc)
{
	if($color == 1) $color = 2; else $color = 1;
?>
<tr class="element trcolor<?php echo $color; ?>">
	<td>
		<?php echo $Gtrad[$loc['loc']]; ?>
	</td>
	<td>
		<?php
		if($joueur->get_inventaire()->$loc['loc'] != '')
		{
			$objet = decompose_objet($joueur->get_inventaire()->$loc['loc']);
			switch($loc['type'])
			{
				case 'arme' :
					if($joueur->get_inventaire()->$loc['loc'] != 'lock')
					{
						$requete = "SELECT * FROM `arme` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						echo $row['nom'];
					}
					else
					{
						echo 'Lock';
					}
				break;
				case 'armure' :
					$requete = "SELECT * FROM `armure` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					echo $row['nom'];
				break;
			}
			if($objet['slot'] > 0)
			{
				echo '<br /><span class="xsmall">Slot niveau '.$objet['slot'].'</span>';
			}
			if($objet['slot'] == '0')
			{
				echo '<br /><span class="xsmall">Slot impossible</span>';
			}
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
		}
		?>
	</td>
	<td>
		<?php
		if($joueur->get_inventaire()->$loc['loc'] != '' AND $joueur->get_inventaire()->$loc['loc'] != 'lock')
		{
			switch($loc['type'])
			{
				case 'arme' :
					if($loc['loc'] == 'main_droite')
					{
						echo 'Dégâts : '.$joueur['arme_droite'];
					}
					else
					{
						if($row['type'] == 'dague')	echo 'Dégâts : '.$joueur['arme_gauche'];
						else echo 'Dégâts absorbés : '.$row['degat'];
					}
				break;
				case 'armure' :
					echo 'PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
		}
		?>
	</td>
	<td>
		<?php
		if($joueur->get_inventaire()->$loc['loc'] != '' AND $joueur->get_inventaire()->$loc['loc'] != 'lock')
		{
		?>
			<a href="inventaire.php?action=desequip&amp;partie=<?php echo $loc['loc']; ?>" onclick="return envoiInfo(this.href, 'centre');">Désequiper</a>
		<?php
		}
		?>
	</td>
</tr>
<?php
}
?>
<table>
<tr>
	<td>
		Nom
	</td>
	<td>
		Action
	</td>
</tr>
<?php
$i = 0;
if($joueur->get_inventaire_slot() != '')
{
	foreach($joueur->get_inventaire_slot() as $invent)
	{
		if($invent !== 0 AND $invent != '')
		{
			$objet_d = decompose_objet($invent);
			if($objet_d['identifier'])
			{
				switch ($objet_d['categorie'])
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$mains = explode(';', $row['mains']);
						$partie = $mains[0];
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'accessoire';
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['prix'] = pow(10, $row['niveau']) * 10;
					break;
					case 'r' :
						$requete = "SELECT * FROM objet_royaume WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
					break;
				}
			}
			else
			{
				$row['nom'] = 'Objet non-identifiée';
			}
			echo '
	<tr>
		<td>
			'.$row['nom'];
		$modif_prix = 1;
		if($objet_d['stack'] > 1) echo ' X '.$objet_d['stack'];
		if($objet_d['slot'] > 0)
		{
			echo '<br /><span class="xsmall">Slot niveau '.$objet_d['slot'].'</span>';
			$modif_prix = 1 + ($objet_d['slot'] / 5);
		}
		if($objet_d['slot'] == '0')
		{
			echo '<br /><span class="xsmall">Slot impossible</span>';
			$modif_prix = 0.9;
		}
		if($objet_d['enchantement'] > '0')
		{
			$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['enchantement'];
			$req = $db->query($requete);
			$row_e = $db->read_assoc($req);
			$modif_prix = 1 + ($row_e['niveau'] / 2);
			echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
		}
		//else echo ' X 1';
		echo '
		</td>
		<td>
			<a href="admin_joueur.php?direction=supp_objet&amp;id='.$joueur->get_id().'&amp;id_objet='.$objet_d['id_objet'].'&amp;key_slot='.$i.'">Supprimer</a>
		</td>
	</tr>';
			$i++;
		}
	}
}
?>
</table>
<?php
			break;
			case 'supp_objet' :
				$joueur = new perso($_GET['id']);
				$joueur->supprime_objet($_GET['id_objet'], 1);
				echo 'Objet bien supprimer<br />';
				echo '<a href="admin_joueur.php?direction=inventaire&amp;id='.$joueur->get_id().'">Retour à l\'inventaire</a> | <a href="admin_joueur.php?direction=info_joueur&amp;id='.$joueur->get_id().'">Retour au personnage</a>';
			break;
		case 'donnepa':
        $pa = max($G_PA_max, 180);
		$joueur = new perso($_GET['id']);
        $joueur->set_pa($pa);
        $joueur->sauver();

        log_admin::log('admin', 'Don d\'un full PA à '.$joueur->get_nom());
        break;
		}
	}
	else
	{
		if(array_key_exists('page', $_GET)) $page = $_GET['page']; else $page = 1;
		if(array_key_exists('tri', $_GET)) $tri = $_GET['tri']; else $tri = 'nom';
		if(array_key_exists('sort', $_GET)) $sort = $_GET['sort']; else $sort = 'ASC';
		if(array_key_exists('pseudo', $_GET)) $pseudo = " AND nom LIKE '%".$_GET['pseudo']."%'"; else $pseudo = '';
		if($sort == 'ASC') $sort2 = 'DESC'; else $sort2 = 'ASC';
	?>
	<form method="get" action="admin_joueur.php">
		Rechercher un joueur ayant pour nom : <input type="text" name="pseudo" />
	</form>
		<div id="news">
			<div class="titre">
				Liste de tous les personnages
			</div>
			<table class="adminListing" cellspacing="0">
			<tr>
				<td>
					<a href="admin_joueur.php?tri=nom&amp;sort=<?php echo $sort2; ?>">Nom</a>
				</td>
				<td>
					<a href="admin_joueur.php?tri=dernier_connexion&amp;sort=<?php echo $sort2; ?>">Dernière connexion</a>
				</td>
				<td>
					<a href="admin_joueur.php?tri=tot&amp;sort=<?php echo $sort2; ?>">Nombre de connexions</a>
				</td>
				<td>
					<a href="admin_joueur.php?tri=statut&amp;sort=<?php echo $sort2; ?>">Statut</a>
				</td>
			</tr>
	<?php
		//$requete = "SELECT perso.ID, nom, statut, dernier_connexion, COUNT(*) AS tot FROM perso RIGHT JOIN log_connexion ON log_connexion.id_joueur = perso.ID WHERE log_connexion.message = 'Ok' ".$pseudo." GROUP BY log_connexion.id_joueur ORDER by ".$tri." ".$sort." LIMIT ".(($page - 1) * 25).", 25";
    $requete = "SELECT perso.ID, nom, statut, dernier_connexion FROM perso WHERE 1 ".$pseudo." ORDER by ".$tri." ".$sort." LIMIT ".(($page - 1) * 25).", 25";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			?>
			<tr>
				<td>
					<a href="admin_joueur.php?direction=info_joueur&amp;id=<?php echo $row['ID']; ?>"><?php echo $row['nom']; ?></a>
				</td>
				<td>
					<?php
						$temps = time() - $row['dernier_connexion'];
						//Affiche en rouge les persos qui n'ont pas joué depuis plus de 90 jours.
						echo '<span class="'.over_price($temps, (60 * 60 * 24 * 90)).'">'.transform_sec_temp($temps).'</span>';
					?>
				</td>
				<td>
            <?php /*echo $row['tot'];*/ ?>
				</td>
				<td>
					<?php echo $row['statut']; ?>
				</td>
			</tr>
			<?php
		}
		?>
			</table>
			<?php if($page > 1) echo '<a href="admin_joueur.php?page='.($page - 1).'&amp;tri='.$tri.'&amp;sort='.$sort.'">'; ?><< Page précédente<?php if($page > 1) echo '</a>'; ?> - <a href="admin_joueur.php?page=<?php echo ($page + 1).'&amp;tri='.$tri.'&amp;sort='.$sort; ?>">Page suivante >></a>
		<?php
	}
}
	?>
		</div>
	<?php
	include_once(root.'bas.php');

?>