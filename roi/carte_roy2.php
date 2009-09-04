<?php
if (file_exists('../root.php'))
  include_once('../root.php');

// Votre fonction de vérification des accès à l'image
	include_once(root.'roi/carte_roy.php');
	// Emplacement de l'image
	$gs_dirImage = root."image/cart/";
	$ls_image = $gs_dirImage.'carte_roy_'.$joueur->get_race().'.png';
	
	if($joueur->get_rang_royaume() != 6)
		echo '<p>Cheater</p>';
	else
	{
		// Description du contenu pour les navigateurs
		header("Content-type: image/png");

		// Lire le fichier et renvoyer le flux
		readfile($ls_image);
	}
?>
