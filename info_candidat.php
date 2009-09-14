<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE id =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
if($W_row['type'] == 1)
{
	$candidat = new candidat($_GET['id_candidat']);
	$ministre_economie = new perso($candidat->get_id_ministre_economie());
	$ministre_militaire = new perso($candidat->get_id_ministre_militaire());
	?>
	Ministre de l'économie : <?php echo $ministre_economie->get_nom(); ?><br />
	Ministre militaire : <?php echo $ministre_militaire->get_nom(); ?><br />
	<br />
	<h3>Programme :</h3>
	<?php
	echo $candidat->get_programme();
}
?>