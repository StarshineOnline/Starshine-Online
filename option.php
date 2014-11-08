<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
  
$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 'perso';
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

switch($action)
{
case 'titre':
	$titre_perso = new titre($_SESSION['ID']);
	$titre_perso->set_id_titre($_GET['titre']);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre titre a bien été modifié. Pensez à réactualiser !');
	break;
case 'mdp':
	$joueur = joueur::factory();
	if( array_key_exists('anc_mdp', $_POST) || !$joueur->test_mdp( md5($_POST['anc_mdp']) ) )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur, l\'ancien mot de passe n\'est pas le bon.');
		break;
	}
	if( array_key_exists('nouv_mdp_1', $_POST) || array_key_exists('nouv_mdp_2', $_POST) || $_POST['nouv_mdp_1'] != $_POST['nouv_mdp_2'] )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur lors de la saisie du nouveau mot de passe.');
		break;
	}
	$perso = joueur::get_perso();
	$perso->set_password(md5($_POST['nouv_mdp_1']));
	$perso->sauver();
	
	if(array_key_exists('id_joueur', $_SESSION)) 
	{
		$joueur->set_mdp(md5($_POST['nouv_mdp_1']));
		$joueur->set_mdp_jabber(md5($_POST['nouv_mdp_1']));
		$joueur->set_mdp_forum(sha1($_POST['nouv_mdp_1']));
		$joueur->sauver();
	}

	require('connect_forum.php');
	$requete = "UPDATE punbbusers SET password = '".sha1($_POST['nouv_mdp_1'])."' WHERE username = '".sSQL($perso->get_nom())."'";
	$db_forum->query($requete);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre mot de passe a bien été modifié !');
	break;
case 'email' :
	if(array_key_exists('email', $_POST))
	{
		$email = sSQL($_POST['email']);
		$joueur = joueur::factory();
		$joueur->set_email($new_email);
		$joueur->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre email a bien été modifié !');
	}
	break;
case 'suppr':
	$perso = joueur::get_perso();
	$perso->set_statut('suppr');
	$perso->set_fin_ban(time() + 3600 * 24 * 36500);
	$perso->sauver();
	$groupe = $perso->get_groupe();
	if($groupe != 0)
		degroup($perso->get_id(), $groupe);
	require_once('connect_forum.php');
	$requete = "INSERT INTO punbbbans VALUES(NULL, '".sSQL($perso->get_nom())."', NULL, NULL, NULL, NULL, 2)";
	$db_forum->query($requete);
	unset($_COOKIE['nom']);
	unset($_SESSION['nom']);
	unset($_SESSION['ID']);
	$interf_princ->recharger_interface('index.php');
	exit;
case 'hibern':
	$perso->set_statut('hibern');
	$perso->set_fin_ban(time() + 3600 * 24 * 14);
	$perso->sauver();
	unset($_COOKIE['nom']);
	unset($_SESSION['nom']);
	unset($_SESSION['ID']);
	$interf_princ->recharger_interface('index.php');
	exit;
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$G_url->add('categorie', $categorie);
	switch($categorie)
	{
	case 'perso':
		$interf_princ->add( $G_interf->creer_options_perso() );
		break;
	case 'joueur':
		$interf_princ->add( $G_interf->creer_options_joueur() );
		break;
	}
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Options', true, 'dlg_options') );
	$dlg->add( $G_interf->creer_options($categorie) );
}


exit;


$titre_perso = new titre($_SESSION['ID']);
?>
		<p>
			<?php
			if(array_key_exists('action', $_GET))
			{
					case 'atm' :
					{
						$requete = false;
						$val = sSQL($_GET['val']);
						switch ($_GET['effet'])
						{
						case 'sky':
							$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
								$_SESSION['ID'].", 'desactive_atm', $val)";
							break;
						case 'time':
							$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
								$_SESSION['ID'].", 'desactive_atm_all', $val)";
							break;
						default:
							echo "<h5>Erreur de parametre</h5>";
						}
						if ($requete) {
							header("Location: ?");
							$db->query($requete);
							exit(0);
						}
					}
					break;
					case 'sound' :
					{
						$val = sSQL($_GET['val']);
						$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
							$_SESSION['ID'].", 'no_sound', $val)";
						header("Location: ?");
						$db->query($requete);
						exit(0);
					}
					break;
				}
			}
			else
			{
				$perso = new perso($_SESSION['ID']);
				$q = $db->query("select password from perso where id = $_SESSION[ID]");
				if($q)
				{
					$row = $db->read_row($q);
					$clef_api = sha1($row[0]);
				}
				
				$atm_val = 1;
				$atm_all_val = 1;
				$q = $db->query("select nom, valeur from options where ".
												"id_perso = $_SESSION[ID] and nom in ".
												"('desactive_atm', 'desactive_atm_all')");
				if ($q) {
					while ($row = $db->read_row($q)) {
						switch ($row[0]) {
						case 'desactive_atm':
							$atm_val = $row[1] ? 0 : 1;
							break;
						case 'desactive_atm_all':
							$atm_all_val = $row[1] ? 0 : 1;
							break;
						}
					}
				}
				$atm_verb = $atm_val ? 'Désactiver' : 'Activer';
				$atm_all_verb = $atm_all_val ? 'Désactiver <strong>tous</strong>' : 'Activer';

				$no_sound = $db->query_get_object("select valeur from options where ".
												"id_perso = $_SESSION[ID] and nom = 'no_sound'");
				if ($no_sound && $no_sound->valeur)
				{
					$sound_verb = 'Activer';
					$sound_val = 0;
				}
				else
				{
					$sound_verb = 'Désactiver';
					$sound_val = 1;
				}

			?>
			<div class"news">
				<h3>Options graphiques et son</h3>
				  <ul>
<?php if (isset($G_use_atmosphere) && $G_use_atmosphere) { ?>
					  <li><a href="option.php?action=atm&amp;effet=sky&amp;val=<?php echo $atm_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $atm_verb; ?> les effets atmospheriques</a></li>
					  <li><a href="option.php?action=atm&amp;effet=time&amp;val=<?php echo $atm_all_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $atm_all_verb; ?> les effets atmosphériques et liés à l'heure</a></li>
<?php } ?>

					  <li><a href="option.php?action=sound&amp;val=<?php echo $sound_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $sound_verb; ?> les effets sons</a></li>

				  </ul>
			</div>
			</ul>
			<?php
			}
			?>
		</p>
	</div>
