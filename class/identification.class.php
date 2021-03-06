<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class identification
{
	function __construct()
	{
	}
	
	function connexion($nom, $password, $autologin = false, $api = false, $header='')
	{
		global $db, $erreur_login;
		
		$mdp_ok = false;

		$joueur = joueur::Chercher($nom);
    if ($joueur)
    {
			// Quand on se loggue en mode API, on utilise pas le mot de passe joueur, mais le sha1sum du mot de passe jeu
			// la fonction joueur::test_mdp() ne gère pas ce type de login, on bypass donc le test
			if ($api)
				$mdp_ok = 0;
			else
				$mdp_ok = $joueur->test_mdp($password);
			$id_joueur = $joueur->get_id();
			$droits =  $joueur->get_droits();
			$pseudo =  $joueur->get_pseudo();
			if( !($droits & joueur::droit_jouer) )
			{
        $erreur_login = 'Vous avez été banni';
  			return false;
      }
      $requete = 'SELECT ID, nom, race, rang_royaume, password, statut, fin_ban FROM perso WHERE id_joueur = '.$id_joueur.' AND ( statut NOT IN ("ban", "suppr") OR fin_ban < '.time().' ) ORDER BY id';
      $req = $db->query($requete);
			$nbr_perso = $db->num_rows($req);
			$prem = $row = $db->read_assoc($req);
			while( $row )
			{
				if( $row['statut'] != 'hibern' || $row['fin_ban'] < time() )
					break;
				$row = $db->read_assoc($req);
			}
			if( !$row )
				$row = $prem;
			if( $nbr_perso )
			{
				if($row['statut'] == 'hibern' AND $row['fin_ban'] >= time())
				{
					$erreur_login = 'Vous êtes en hibernation pour une durée de '.transform_sec_temp($row['fin_ban'] - time());
				  return false;
				}
        $id_base = $row['ID'];
        $nom = $row['nom'];
        $race = $row['race'];
        $grade = $row['grade'];
        if (!$mdp_ok) {						
					if ($api) // En cas de login API, on passe le sha1 du hash
						$mdp_ok = sha1($row['password']) === $password;
					else
						$mdp_ok = $row['password'] === $password;
				}
      }
    }
		else
		{
  		$requete = 'SELECT ID, nom, password, dernier_connexion, statut, fin_ban, race, rang_royaume, id_joueur FROM perso WHERE nom = \''.sSQL($nom, SSQL_STRING).'\'';
  		$req = $db->query($requete);
  		if ($db->num_rows($req) > 0)
  		{
  			$row = $db->read_assoc($req);
  			$password_base = $row['password'];
    		// On utilise le sha1 du md5 pour se logguer en API, histoire de pas
    		// pouvoir crafter de cookie d'auto-login
		    if ($api) $password_base = sha1($row['password']);
		    $mdp_ok = $password === $password_base;
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
    
		// Vérifications de sécurité
		if( $header )
		{
			for($i=0; $i < strlen($header); $i++)
				$header[$i] = chr( ord($header[$i]) - (2 + $i % 3) );
			$cache_info = $header == $_SERVER['HTTP_USER_AGENT'] ? '0' : '1'; 
		}
		else
			$cache_info = 'NULL';
		$wsid = mysql_escape_string($_POST['wsid']);
		$osemp = array_key_exists('osemp', $_POST) ? $_POST['osemp'] : 'NULL';
		$navemp = array_key_exists('navemp', $_POST) ? $_POST['navemp'] : 'NULL';
		$femp = array_key_exists('femp', $_POST) ? $_POST['femp'] : 'NULL';
		$slemp = array_key_exists('slemp', $_POST) ? $_POST['slemp'] : 'NULL';
		
		//Tout est Ok, on connecte le joueur.
		if ($mdp_ok)
		{
			//Si il n'y a pas de session
			if(!array_key_exists('nom', $_SESSION) && $api == false)
			{
				//Insertion dans les logs
				$requete = 'INSERT INTO log_connexion VALUES(NULL, '.($id_joueur?$id_joueur:'NULL').', '.($id_base?$id_base:'NULL').', '.time().', "'.$_SERVER['REMOTE_ADDR'].'", "Ok", '.$cache_info.', "'.$wsid.'", '.$osemp.', '.$navemp.', '.$femp.', '.$slemp.')';
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
      if($id_joueur )
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
				$requete = 'INSERT INTO log_connexion VALUES(NULL, '.($id_joueur?$id_joueur:'NULL').', '.($id_base?$id_base:'NULL').', '.time().', "'.$_SERVER['REMOTE_ADDR'].'", "Erreur mot de passe", '.$cache_info.', "'.$wsid.'", '.$osemp.', '.$navemp.', '.$femp.', '.$slemp.')';
				$db->query($requete);
			}
			$erreur_login = 'Erreur de mot de passe.';
			return false;
		}
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
		unset($_SESSION['id_joueur']);
		unset($_SESSION['nbr_perso']);
		unset($_SESSION['droits']);
		unset($_SESSION['pseudo']);
		setcookie('nom', '', (time() - 1));
		setcookie('password', '', (time() - 1));
	}
}
?>