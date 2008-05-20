<?php
// Votre fonction de vérification des accès à l'image
if($joueur['rang_royaume'] == 6)
{
	include('carte_roy.php');
	// Emplacement de l'image
	$gs_dirImage = "image/cart/";
	$ls_image = $gs_dirImage.'carte_roy_'.$_GET["url"].'.png';

	// Description du contenu pour les navigateurs
	header("Content-type: image/png");

	// Lire le fichier et renvoyer le flux
	readfile($ls_image);
}
?>
