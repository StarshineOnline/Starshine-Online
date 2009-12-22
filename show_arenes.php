<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($joueur->get_race() != $R->get_race() &&
		$R->get_diplo($joueur->get_race()) > 6)
{
	echo "<h5>Impossible d'entrer avec un tel niveau de diplomatie</h5>";
	exit (0);
}

echo '<h2 class="ville_titre">';
if (verif_ville($joueur->get_x(), $joueur->get_y())) {
	return_ville('<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> -', $joueur->get_pos());
}
echo '<a href="show_arenes.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')"> Arènes </a></h2>';
include_once(root.'ville_bas.php');

$requete = "select nom from arenes where open = 1";
$req = $db->query($requete);
$found = false;
while ($arene = $db->read_object($req)) {
	if (!$found)
		echo '<strong>Les arènes suivantes sont ouvertes : </strong><br />';
	$nom = $arene->nom;
	echo "<a href=\"show_arenes2.php?nom=${nom}\">${nom}</a><br />\n";
	$found = true;
}

if (!$found) {
	echo "<h5>Toutes les arènes sont fermées</h5>";
}


?>