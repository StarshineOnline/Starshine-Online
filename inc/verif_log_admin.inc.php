<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
	$verif = false;
	if(array_key_exists('nom_admin', $_POST))
	{
		$_SESSION['admin_nom'] = $_POST['nom_admin'];
		$_SESSION['admin_pass'] = $_POST['pass'];

    // Test en base
    $n = sSQL($_POST['nom_admin']);
    $p = sSQL($_POST['pass']);
    $requete = "select statut from jabber_admin where nom = '$n' and password = MD5('$p')";
    $req = $db->query($requete);
    if ($row = $db->read_row($req)) {
      $_SESSION['admin_db_auth'] = $row[0];
    }
	}
  if (isset($_SESSION['admin_db_auth']) && $_SESSION['admin_db_auth'] != '') {
			$verif = true;
			$R['statut'] = $_SESSION['admin_db_auth'];
  }
	//Vérification du login mdp
	elseif(array_key_exists('admin_nom', $_SESSION))
	{
		//Vérification du nom et du mot de passe
		if($_SESSION['admin_nom'] == 'admin' AND sha1(md5($_SESSION['admin_pass'])) == 'c6fbe6c72d199b0353c23d8a0d4cb61cd3ac2f87')
		{
			$verif = true;
			$R['statut'] = 'admin';
		}
		elseif($_SESSION['admin_nom'] == 'modo' AND sha1(md5($_SESSION['admin_pass'])) == '23da528c728a7454786b82500c7e3cc037080b3d')
		{
			$verif = true;
			$R['statut'] = 'modo';
		}
		else
		{
			echo 'Erreur mot de passe';
			$verif = false;
			echo sha1(md5($_POST['pass']));
		}
	}
	if(!$verif)
	{
		echo 'Veuillez vous connecter pour accéder à l\'administration.';
		//Formulaire de login
		?>
		<form action="admin_index.php" method="post">
			Login : <input type="text" name="nom_admin" /><br />
			Pass : <input type="password" name="pass" /><br />
			<input type="submit" value="Ok" />
		</form>
		<?php
		exit();
	}
	else
	{
		require_once(root.'connect_log.php');
		//On l'enregistre dans les logs
		$requete = "INSERT INTO log_admin VALUES(NULL, '".$R['statut']."', '".$_SERVER['REMOTE_ADDR']."', ".time().")";
		$db_log->query($requete);
		
	}
?>