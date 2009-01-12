<?php
include('inc/fp.php');
if (isset($_SESSION['nom']))
{
}
elseif($connexion)
{
	header("Location: index.php");
}

/// @var juste pour empêcher Doxygen de bugger

$journal = '';
//Connexion du joueur
if (isset($_POST['log']) OR isset($_COOKIE['nom']))
{
	if(isset($_POST['log']))
	{
		$nom = $_POST['nom'];
		$password = md5($_POST['password']);
	}
	else
	{
		$nom = $_COOKIE['nom'];
		$password = $_COOKIE['password'];
	}
			
	$requete = "SELECT ID, nom, password, dernier_connexion, statut, fin_ban, race FROM perso WHERE nom = '".$nom."'";
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if ($row != '')
	{
		$password_base = $row['password'];
		$ID_base = $row['ID'];
		//Tout est Ok, on connecte le joueur.
		if ($password == $password_base)
		{
			//Vérification si joueur banni
			if($row['statut'] != 'ban' OR ($row['statut'] == 'ban' AND $row['fin_ban'] <= time()))
			{
				if(($row['statut'] == 'hibern' AND $row['fin_ban'] >= time()))
				{
					echo 'Vous êtes en hibernation pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
				}
				else
				{
					//Si il n'y a pas de session
					if(!array_key_exists('nom', $_SESSION))
					{
						//On cherche les derniers événements de ce joueur.
						$requete_journal = "SELECT * FROM journal WHERE id_perso = ".$ID_base." AND time > '".date("Y-m-d H:i:s", $row['dernier_connexion'])."' ORDER BY time ASC, id ASC";
						$req_journal = $db->query($requete_journal);
						while($row_journal = $db->read_assoc($req_journal))
						{
							$journal .= affiche_ligne_journal($row_journal);
						}
						//Insertion dans les logs
						$requete = "INSERT INTO log_connexion VALUES(NULL, ".$ID_base.", ".time().", '".$_SERVER['REMOTE_ADDR']."', 'Ok')";
						$db->query($requete);
					}
					$_SESSION['nom'] = $row['nom'];
					$_SESSION['ID'] = $ID_base;
					if($_POST['auto_login'] == 'Ok')
					{
						setcookie('nom', $nom, (time() + 3600 * 24 * 30));
						setcookie('password', $password, (time() + 3600 * 24 * 30));
					}
					//Mis à jour de la dernière connexion
					$requete = "UPDATE perso SET dernier_connexion = ".time().", statut = 'actif' WHERE ID = ".$_SESSION['ID'];
					$db->query($requete);
				}
			}
			else
			{
				echo 'Vous avez été banni pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
			}
		}
		else
		{
			//Si il n'y a pas de session
			if(!array_key_exists('nom', $_SESSION))
			{
				//Insertion dans les logs
				$requete = "INSERT INTO log_connexion VALUES(NULL, ".$ID_base.", ".time().", '".$_SERVER['REMOTE_ADDR']."', 'Erreur mot de passe')";
				$db->query($requete);
			}
			echo 'Erreur de mot de passe.';
		}
	}
	else
	{
		echo 'Pseudo inconnu.';
	}
}
if (isset($_GET['deco']) AND !isset($_POST['log']))
{
	session_unregister('nom');
	session_unregister('ID');
	unset($_SESSION['nom']);
	unset($_SESSION['ID']);
	setcookie('nom', '', (time() - 1));
	setcookie('password', '', (time() - 1));
	$journal = '';
}

if(array_key_exists('nom', $_SESSION)) $joueur = recupperso($_SESSION['ID']);
if(!isset($root)) $root = '';
//check_undead_players();
if ($site)
{
	  print_head("css:./css/site.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
}
else
{
	if ($interface_v2) {
	  print_head("css:./css/texture.css~./css/texture_low.css~./css/interfacev2.css~./css/prototip.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
	else {
	  print_head("css:./css/texture.css~./css/interface.css~./css/prototip.css~./css/site.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
}
$fin = getmicrotime();

//echo 'TEMPS : '.($fin - $debut);
?>