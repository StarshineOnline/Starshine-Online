<?php // -*- tab-width: 2 -*-
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$joueur_id = $_SESSION['ID'];
}
$joueur = new perso($joueur_id);
//Filtre
//Filtre
if(array_key_exists('filtre', $_GET))
{
  $filtre = $_GET['filtre'];
  $filtre_url = '&amp;filtre='.$_GET['filtre'];
}
else
{
  $filtre = 'utile';
  $filtre_url = '&amp;filtre=utile';
}
$W_requete = 'SELECT royaume, type FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);


$princ = $G_interf->creer_princ_droit('Inventaire du Personnage');
$invent = $G_interf->creer_inventaire($joueur, 'inventaire.php', $filtre);
$princ->add($invent);
?>
<?php
//Switch des actions
if(!$visu AND isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'desequip' :
			if($joueur->desequip($_GET['partie'], true))
			{
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
		case 'equip' :
			if($joueur->equip_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), true))
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
				$joueur->sauver();
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
	}
	refresh_perso();
}

$invent->set_contenu('pet');
$invent->affiche_slots();

?>
