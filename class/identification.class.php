<?php
class identification
{
	function __construct()
	{
	}
	
	function connexion($nom, $password, $autologin = false)
	{
		global $db, $erreur_login;
		$requete = 'SELECT ID, nom, password, dernier_connexion, statut, fin_ban, race FROM perso WHERE nom = \''.$nom.'\'';
		$req = $db->query($requete);
		if ($db->num_rows($req) > 0)
		{
			$row = $db->read_assoc($req);
			$password_base = $row['password'];
			$ID_base = $row['ID'];
			//Tout est Ok, on connecte le joueur.
			if ($password === $password_base)
			{
				//V?rification si joueur banni
				if($row['statut'] != 'ban' OR ($row['statut'] == 'ban' AND $row['fin_ban'] <= time()))
				{
					if(($row['statut'] == 'hibern' AND $row['fin_ban'] >= time()))
					{
						$erreur_login = 'Vous êtes en hibernation pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
					}
					else
					{
						//Si il n'y a pas de session
						if(!array_key_exists('nom', $_SESSION))
						{
							//Insertion dans les logs
							$requete = "INSERT INTO log_connexion VALUES(NULL, ".$ID_base.", ".time().", '".$_SERVER['REMOTE_ADDR']."', 'Ok')";
							$db->query($requete);
						}
						$_SESSION['nom'] = $row['nom'];
						$_SESSION['ID'] = $ID_base;
						if($autologin)
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
					$erreur_login = 'Vous avez été banni pour une dur?e de '.transform_sec_temp($row['fin_ban'] - time());
					return false;
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
				$erreur_login = 'Erreur de mot de passe.';
				return false;
			}
		}
		else
		{
			$erreur_login = 'Pseudo inconnu.';
			return false;
		}
	}
	
	function deconnexion()
	{
		session_unregister('nom');
		session_unregister('ID');
		unset($_SESSION['nom']);
		unset($_SESSION['ID']);
		setcookie('nom', '', (time() - 1));
		setcookie('password', '', (time() - 1));
	}
}
?>