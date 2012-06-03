<?php
	if (file_exists('../root.php'))
  	include_once('../root.php');
	include_once(root.'inc/fp.php');
	$joueur = new perso($_SESSION['ID']);
	$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);

	// Emplacement de l'image
	if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
		echo '<p>Cette page vous est interdite</p>';
	else
	{

		// Description du contenu pour les navigateurs
		header("Content-Type: image/png");
		$ls_image = '../image/cart/carte_roy_'.$joueur->get_race().'.png';

		// Lire le fichier et renvoyer le flux
		readfile($ls_image);
	}
	
?>
