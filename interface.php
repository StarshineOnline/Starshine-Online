<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
$interface_v2 = true;
$interf_obj = true;
include_once(root.'haut.php');
if(array_key_exists('ID', $_SESSION) && !empty($_SESSION['ID']))
	$joueur = new perso($_SESSION['ID']);
else
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	var_dump($_SESSION);
	exit();
}


//Si c'est pour entrer dans un donjon
if(array_key_exists('donjon_id', $_GET))
{
	$id = $_GET['donjon_id'];

	$requete = "SELECT x, y, x_donjon, y_donjon FROM donjon WHERE id = ".$id;
	
  if (isset($G_disallow_donjon) && $G_disallow_donjon == true) {
    $disallowed = true;
    if (isset($G_allow_donjon_for) && is_array($G_allow_donjon_for))
      foreach ($G_allow_donjon_for as $allowed)
        if ($allowed == $joueur->get_nom())
          $disallowed = false;
    if ($disallowed)
      security_block(URL_MANIPULATION);
  }
	$req = $db->query($requete);
	
	$row = $db->read_assoc($req);

	// Verification que les conditions sont reunies
	$unlock = verif_tp_donjon($row, $joueur);
	if ($unlock == false)
		security_block(URL_MANIPULATION);

	//sortie
	if(array_key_exists('type', $_GET))
	{
		if($joueur->get_x() == $row['x_donjon'] AND $joueur->get_y() == $row['y_donjon'])
		{
			$joueur->set_x($row['x']);
			$joueur->set_y($row['y']);
			$joueur->sauver();
		}
	}
	//Entrée
	else
	{
		if($joueur->get_x() == $row['x'] AND $joueur->get_y() == $row['y'])
		{
			$joueur->set_x($row['x_donjon']);
			$joueur->set_y($row['y_donjon']);
			$joueur->sauver();
		}
	}
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$joueur->check_perso();

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());


$princ = $G_interf->creer_jeu();
$princ->set_gauche();
$princ->set_droite();
?>
