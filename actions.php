<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

//Récupération des informations du personnage
$perso = joueur::get_perso();
$perso->check_perso();

$interf_princ = $G_interf->creer_jeu();


$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$script = array_key_exists('script', $_GET) ? $_GET['script'] : null;
$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
switch($action)
{
}

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Scripts de combat') );
if( $script )
{
	
}
else
{
	//$cadre->add( new interf_bal_smpl('p', 'Voici l\'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.') );
	$onglets = $cadre->add( new interf_onglets('ongl_scripts', 'scripts') );
	$onglets->add_onglet('Perso', $G_url->get(), 'ongl_perso', 'invent', !$id);
	$pets = $perso->get_pets(true);
	foreach($pets as $pet)
	{
		$onglets->add_onglet($pet->get_nom(), $G_url->get('id', $pet->get_id()), 'ongl_'.$pet->get_id(), 'invent', $id==$pet->get_id());
	}
	if( $id )
	{
		$pet = new pet($id);
		$onglets->get_onglet('ongl_'.$id)->add( $G_interf->creer_liste_scripts($pet) );
	}
	else
		$onglets->get_onglet('ongl_perso')->add( $G_interf->creer_liste_scripts($perso) );
	/*$aide = $cadre->add( new interf_bal_cont('div') );
	$aide->add( new interf_bal_smpl('h4', 'Aide') );
	$aide->add( new interf_bal_smpl('p', 'Une attaque sur un monstre ou un joueur se fait généralement en 10 rounds (11 si l\'un des deux est un Orc, 9 si l\'attaquant a le buff Sacrifice sur lui). Vous pouvez paramétrer les 10 actions que vous allez faire dans le script de combat, afin de les réaliser à chaque attaque. Il est donc conseillé de créer un script d\'attaque, et de créer vos 10 actions en ajoutant les compétences que vous voulez utiliser. Vous pouvez aussi créer un script de défense qui s\'exécutera automatiquement si vous êtes attaqué par d\'autres joueurs. (les compétences que vous pourrez utiliser dans votre script sont limitées par votre réserve de mana)') );
	$aide->add( new interf_lien('Pour avoir plus d\'informations sur le script de combat', 'http://wiki.starshine-online.com/doku.php?id=combat:scripts') );*/
}




exit;






//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
?>
		<?php
		//Suppression du script
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'suppr_action')
		{
			$requete = "DELETE FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			if($db->query($requete))
			{
				echo '<h6>Script effacé avec succès !</h6>';
			}
		}
		if(array_key_exists('valid', $_GET))
		{
			//Si ya pas d'id_action alors création
			if($_GET['id_action'] == '')
			{
				$requete = "INSERT INTO action_perso VALUES('', ".$joueur->get_id().", '".sSQL($_POST['action_nom'])."', '', '".sSQL($_POST['mode'])."')";
				$req = $db->query($requete);
				$id_action = $db->last_insert_id();
			}
			else
			{
				$id_action = $_GET['id_action'];
			}
			$actionexplode = explode(';', recupaction($id_action));
			$i = 1;
			while($i <= $_GET['r'])
			{
				$final = $_GET['final'.$i];
				$action = '#09='.$i.'@';
				$typefinal = $final[0];
				$action_nom = sSQL($_GET['action_nom']);
				$numfinal = mb_substr($final, 1, strlen($final));
				if($final == 'attaque') $a_final = '!';
				elseif($typefinal == 's') $a_final = '~'.$numfinal;
				else $a_final = '_'.$numfinal;
				$action .= $a_final;
				$actionexplode[($i - 1)] = $action;
				$i++;
			}
			$requete = "UPDATE action_perso SET action = '".implode(';', $actionexplode)."', nom = '".$action_nom."' WHERE id = ".$id_action;
			//echo $requete;
			$db->query($requete);
			echo '<h6>Le script est bien été ajouté / modifié</h6>';
		}

		if(array_key_exists('action', $_GET) && $_GET['action'] == 'select' && $_GET['id_action'] != '')
		{
			if($_GET['type'] == 'attaque') $t = 'a';
			else $t = 'd';
			$id_action = sSQL($_GET['id_action'], SSQL_INTEGER);
			$requete = "UPDATE perso SET action_".$t." = '".sSQL($id_action)."' WHERE ID = ".$joueur->get_id();
			if($db->query($requete))
			{
				$set = 'set_action_'.$t;
				$joueur->$set($_GET['id_action']);
				$joueur->sauver();
				echo '<h6>Script '.$_GET['type'].' bien séléctionné.</h6>';
			}
		}
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'dupliq' && $_GET['id_action'] != '')
		{
			$requete = "SELECT action, mode FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$requete = "INSERT INTO action_perso VALUES(null, ".$joueur->get_id().", '".sSQL($_GET['nom_copie'])."', '".$row['action']."', '".$row['mode']."')";
			//echo $requete;
			if($db->query($requete))
			{
				echo '<h6>Script dupliqué.</h6>';
			}
		}
		?>
			Voici l'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.<br />
			<fieldset>
				<legend>Vos scripts de combat</legend>
				<ul id="liste_script">
				<?php
				$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$type = '';
					if($row['nom']==$script_attaque['nom']) $type .= '<a><span class="attaque" title="Script d\'attaque"> </span></a> ';
					else $type .= '<a><span class="space"></span></a> ';
					if($row['nom']==$script_defense['nom']) $type .= '<a><span class="shield" title="Script de défense"> </span></a> ';
					else $type .= '<a><span class="space"></span></a> ';
					?>
					<li><?php echo $type; ?><span class="nom_script" onclick="envoiInfo('action.php?from=modif&amp;id_action=<?php echo $row['id']; ?>', 'information');" title="Modifier le script"><?php echo $row['nom']; ?></span>
						<span class="options">
							<a title="Définir comme script d'attaque" onclick="envoiInfo('actions.php?action=select&amp;type=attaque&amp;id_action=<?php echo $row['id']; ?>', 'information'); return false;"><span class="attaque hover"></span></a>
							<a title="Définir comme script de défense" onclick="envoiInfo('actions.php?action=select&amp;type=defense&amp;id_action=<?php echo $row['id']; ?>', 'information'); return false;"><span class="shield hover"></span></a>
							<a title="Copier" onclick="envoiInfo('actions.php?action=dupliq&amp;id_action=<?php echo $row['id']; ?>&amp;nom_copie=copie', 'information');"><span class="copy hover"></span></a>
							<a title="Supprimer ce script de combat" style="float : left;" onclick="if(confirm('Voulez-vous vraiment supprimer ce script ?')) envoiInfo('actions.php?action=suppr_action&amp;id_action=<?php echo $row['id']; ?>', 'information');"><span class="del hover"></span></a>
						</span>
					</li>
					<?php
				}
				?>
				</ul>
				<br />
				<a href="action.php?mode=s" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat simple</a> - <a href="action.php?mode=a" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat avancé</a>
