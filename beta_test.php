<?php
if (file_exists('root.php'))
  include_once('root.php');

	include_once(root.'inc/fp.php');
	$joueur = new perso($_SESSION['ID']);
	
	if(array_key_exists('action', $_GET))
	{
		if($joueur['beta'] > 0)
		{
			$joueur['beta']--;
			switch($_GET['action'])
			{
				case 'hp' :
					$requete = "UPDATE perso SET hp = FLOOR(hp_max), beta = ".$joueur['beta']." WHERE ID = ".$_SESSION['ID'];
				break;
				case 'mp' :
					$requete = "UPDATE perso SET mp = FLOOR(mp_max), beta = ".$joueur['beta']." WHERE ID = ".$_SESSION['ID'];
				break;
				case 'pa' :
					$requete = "UPDATE perso SET pa = 180, beta = ".$joueur['beta']." WHERE ID = ".$_SESSION['ID'];
				break;
				case 'star' :
					$requete = "UPDATE perso SET star = star + 10000, beta = ".$joueur['beta']." WHERE ID = ".$_SESSION['ID'];
				break;
			}
			$db->query($requete);
		}
	}
?>
Vous avez <?php echo $joueur['beta']; ?> points beta test.<br />
En utilisez un pour :<br />
- <a href="beta_test.php?action=hp" onclick="return envoiInfo(this.href, 'popup_content');">Full HP</a><br />
- <a href="beta_test.php?action=mp" onclick="return envoiInfo(this.href, 'popup_content');">Full MP</a><br />
- <a href="beta_test.php?action=pa" onclick="return envoiInfo(this.href, 'popup_content');">Full PA</a><br />
- <a href="beta_test.php?action=star" onclick="return envoiInfo(this.href, 'popup_content');">Recevoir 10 000 stars</a><br />