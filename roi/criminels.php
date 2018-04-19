<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 || $royaume->is_raz() )
{
	/// @todo logguer triche
	exit;
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

$cadre = $G_interf->creer_royaume();

switch($action)
{
case 'suppr':
  $criminel = new perso($_GET['id']);
	$criminel->set_amende('0');
	$criminel->sauver();
	//On supprime l'amende du joueur
	/// @todo passer à l'objet
	$requete = "DELETE FROM amende WHERE id_joueur = ".$criminel->get_id();
	$db->query($requete);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Amende bien supprimée.');
	break;
case 'modifier':
  $criminel = new perso($_GET['id']);
  $amende_max = ($criminel->get_crime() * $criminel->get_crime()) * 10;
  //Vérification d'usage
  if($_GET['montant'] <= 0)
  {
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le montant de l\'amende doit être positif');
		break;
	}
	else if($_GET['montant'] > $amende_max)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le montant de l\'amende est trop élevé');
		break;
	}
	$spawn_ville = $_GET['rez_ville'] == 'true' ? 'y' : 'n';
	$acces_ville = $_GET['acces_ville'] == 'true' ? 'y' : 'n';
	//Inscription de l'amende dans la bdd
	$req_test = $db->query("SELECT * FROM amende WHERE id_joueur = ".$criminel->get_id()."");
	if ($db->num_rows>0)
	{
  	$requete = "UPDATE amende SET 	montant = '".sSQL($_GET['montant'])."',
								acces_ville = '".$acces_ville."',
  									respawn_ville = '".$spawn_ville."',
  									statut = '".sSQL($_GET['statut'])."'
  								WHERE id_joueur = '".$criminel->get_id()."'";
  	if($db->query($requete))
  	{
      	$amende = recup_amende($joueur->get_id());
      	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$criminel->get_id();
      	if($db->query($requete))
					interf_alerte::enregistre(interf_alerte::msg_succes, 'Amende bien prise en compte !');
  	}
	}
	else
	{
  	$requete = "INSERT INTO amende(id, id_joueur, id_royaume, montant, acces_ville, respawn_ville, statut) VALUES (NULL, ".$criminel->get_id().", ".$Trace[$criminel->get_race()]['numrace'].", ".sSQL($_GET['montant']).", '".$acces_ville."', '".$spawn_ville."', '".sSQL($_GET['statut'])."')";
  	if($db->query($requete))
  	{
      	$amende = recup_amende($criminel->get_id());
      	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$criminel->get_id();
      	if($db->query($requete))
					interf_alerte::enregistre(interf_alerte::msg_succes, 'Amende bien prise en compte !');
  	}
	}
	break;
case 'gerer':
	include_once(root.'interface/interf_gest_criminels.class.php');
  $criminel = new perso($_GET['id']);
	$cadre->set_dialogue( new interf_modif_criminel($criminel) );
	break;
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_gest_criminels($royaume) );
$cadre->maj_tooltips();



?>