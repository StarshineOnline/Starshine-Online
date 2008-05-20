<?php
	if(array_key_exists('nom_admin', $_POST))
	{
		$_SESSION['admin_nom'] = $_POST['nom_admin'];
		$_SESSION['admin_pass'] = $_POST['pass'];
	}
	$verif = false;
	//Vérification du login mdp
	if(array_key_exists('admin_nom', $_SESSION))
	{
		//Vérification du nom et du mot de passe
		if($_SESSION['admin_nom'] == 'admin' AND sha1(md5($_SESSION['admin_pass'])) == 'c6fbe6c72d199b0353c23d8a0d4cb61cd3ac2f87')
		{
			$verif = true;
			$R['statut'] = 'admin';
		}
		elseif($_SESSION['admin_nom'] == 'modo' AND sha1(md5($_SESSION['admin_pass'])) == '8015633540bbcfdef5e33c99f01c7495584aad21')
		{
			$verif = true;
			$R['statut'] = 'modo';
		}
		else
		{
			echo 'Erreur mot de passe';
			$verif = false;
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
?>