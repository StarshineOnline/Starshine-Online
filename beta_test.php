<?php
if (file_exists('root.php'))
  include_once('root.php');

	include_once(root.'inc/fp.php');
	$joueur = new perso($_SESSION['ID']);
	
	if(array_key_exists('action', $_GET))
	{
		$joueur->set_beta(1);
		if($joueur->get_beta() > 0)
		{
			switch($_GET['action'])
			{
				case 'hp' :
					$joueur->set_hp($joueur->get_hp_max());
				break;
				case 'mp' :
					$joueur->set_mp($joueur->get_mp_max());
				break;
				case 'pa' :
					$joueur->set_pa(180);
				break;
				case 'star' :
					$joueur->set_star($joueur->get_star() + 10000);
				break;
			}
		}
		$joueur->sauver();
	}
?>
Vous avez <?php echo $joueur->get_beta(); ?> points beta test.<br />
En utilisez un pour :<br />
- <a href="beta_test.php?action=hp" onclick="return envoiInfo(this.href, 'popup_content');">Full HP</a><br />
- <a href="beta_test.php?action=mp" onclick="return envoiInfo(this.href, 'popup_content');">Full MP</a><br />
- <a href="beta_test.php?action=pa" onclick="return envoiInfo(this.href, 'popup_content');">Full PA</a><br />
- <a href="beta_test.php?action=star" onclick="return envoiInfo(this.href, 'popup_content');">Recevoir 10 000 stars</a><br />