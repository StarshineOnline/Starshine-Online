<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
$requete = "SELECT * FROM perso";
$req = $db->query($requete);
$tab_loc = array();
$tab_loc[0] = 'main_droite';
$tab_loc[1] = 'main_gauche';
$tab_loc[2] = 'tete';
$tab_loc[3] = 'torse';
$tab_loc[4] = 'main';
$tab_loc[5] = 'ceinture';
$tab_loc[6] = 'jambe';
$tab_loc[7] = 'chaussure';
$tab_loc[8] = 'dos';
$tab_loc[9] = 'doigt';
$tab_loc[10] = 'cou';
while($row = $db->read_assoc($req))
{
	$joueur = recupperso($row['ID']);
	foreach($tab_loc as $tab)
	{
		desequip($tab, $joueur);
	}
}
?>