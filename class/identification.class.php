<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class identification
{
	function __construct()
	{
	}
	
	function connexion($nom, $password, $autologin = false, $api = false)
	{
		global $db, $erreur_login;
		// On utilise le sha1 du md5 pour se logguer en API, histoire de pas
		// pouvoir crafter de cookie d'auto-login
		if ($api) $password_base = sha1($row['password']);

    $requete = 'SELECT * FROM joueur WHERE login = "'.sSQL($nom, SSQL_STRING).'" OR pseudo = "'.sSQL($nom, SSQL_STRING).'"';
	  $req = $db->query($requete);
    if( $db->num_rows($req) > 0 )
    {
      $row = $db->read_assoc($req);
      $password_base = $row['mdp'];
			$id_joueur = $row['id'];
			$droits = $row['droits'];
			$pseudo = $row['pseudo'];
      $requete = 'SELECT ID, nom, race, rang_royaume FROM perso WHERE id_joueur = '.$id_joueur.' AND ( statut NOT IN ("ban", "hibern") OR fin_ban < '.time().' )';
      $req = $db->query($requete);
			$nbr_perso = $db->num_rows($req);
      echo '<p style="color:white;">'.$requete.' -> '.$nbr_perso.' résultats</p>';
			if( $nbr_perso )
			{
        $row = $db->read_assoc($req);
        $id_base = $row['ID'];
        $nom = $row['nom'];
        $race = $row['race'];
        $grade = $row['grade'];
      }
      else
        $id_base = null;
    }
		else
		{
  		$requete = 'SELECT ID, nom, password, dernier_connexion, statut, fin_ban, race, rang_royaume, id_joueur FROM perso WHERE nom = \''.sSQL($nom, SSQL_STRING).'\'';
  		$req = $db->query($requete);
  		if ($db->num_rows($req) > 0)
  		{
  			$row = $db->read_assoc($req);
  			$password_base = $row['password'];
  			$id_base = $row['ID'];
  			$id_joueur = null;
  			//Vérification si joueur banni
  			if($row['statut'] != 'ban' OR ($row['statut'] == 'ban' AND $row['fin_ban'] <= time()))
  			{
  				if(($row['statut'] == 'hibern' AND $row['fin_ban'] >= time()))
  				{
  					$erreur_login = 'Vous êtes en hibernation pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
  				  return false;
  				}
  			}
  			else
  			{
  				$erreur_login = 'Vous avez été banni pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
  				return false;
  			}
  		}
      else
      {
  			$erreur_login = 'Pseudo inconnu.';
  			return false;
      }
    }
		
		//Tout est Ok, on connecte le joueur.
		if ($password === $password_base)
		{
			//Si il n'y a pas de session
			if(!array_key_exists('nom', $_SESSION) && $api == false)
			{
				//Insertion dans les logs
				$requete = "INSERT INTO log_connexion VALUES(NULL, ".($id_base?$id_base:-$id_joueur).", ".time().", '".$_SERVER['REMOTE_ADDR']."', 'Ok')";
				$db->query($requete);
			}
			if($id_base)
			{
  			$_SESSION['nom'] = $nom;
  			$_SESSION['race'] = $race;
  			$_SESSION['grade'] = $grade;
  			$_SESSION['ID'] = $id_base;
  			//Mis à jour de la dernière connexion
  			$requete = "UPDATE perso SET dernier_connexion = ".time().", statut = 'actif' WHERE ID = ".$_SESSION['ID'];
  			$db->query($requete);
      }
      if($id_joueur)
      {
  			$_SESSION['pseudo'] = $pseudo;
  			$_SESSION['nbr_perso'] = $nbr_perso;
  			$_SESSION['droits'] = $droits;
  			$_SESSION['id_joueur'] = $id_joueur;
      }
			if($autologin)
			{
        if( $id_joueur )
				  setcookie('nom', $pseudo, (time() + 3600 * 24 * 30));
				else
				  setcookie('nom', $nom, (time() + 3600 * 24 * 30));
				setcookie('password', $password, (time() + 3600 * 24 * 30));
			}
		}
		else
		{
			//Si il n'y a pas de session
			if(!array_key_exists('nom', $_SESSION))
			{
				//Insertion dans les logs
				$requete = "INSERT INTO log_connexion VALUES(NULL, ".($id_base?$id_base:-$id_joueur).", ".time().", '".$_SERVER['REMOTE_ADDR']."', 'Erreur mot de passe')";
				$db->query($requete);
			}
			$erreur_login = 'Erreur de mot de passe.';
			return false;
		}
		echo '<p style="color:white;">ok ('.$id_joueur.' - '.$id_base.')</p>';
		if($id_base)
		  return true;
    else
      return 0;
	}
	
	function deconnexion()
	{
		//Deprecated
		/*session_unregister('ID');
		session_unregister('nom');
		session_unregister('race');
		session_unregister('grade');*/
		unset($_SESSION['grade']);
		unset($_SESSION['race']);
		unset($_SESSION['nom']);
		unset($_SESSION['ID']);
		setcookie('nom', '', (time() - 1));
		setcookie('password', '', (time() - 1));
	}
}
?>