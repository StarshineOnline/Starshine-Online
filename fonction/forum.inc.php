<?php
/**
 * @file forum.inc.php
 * Interactions avec le forum
 */
 
require_once(root.'connect_forum.php');

/// Préfixe des noms des tables
$G_prefixe_forum = "punbb";


/**
 * Crée un post dans le forum
 * 
 * @param  $titre     Titre du post.
 * @param  $msg       Message du post.
 * @param  $forum     id du forum.
 * @param  $posteur   id du posteur.
 */
function creer_sujet_forum($titre, $msg, $forum, $posteur)
{
  global $G_prefixe_forum, $db_forum;
  
  // Nom du posteur
  $requete = "SELECT username FROM ".$G_prefixe_forum."users WHERE id = $posteur";
  $db_forum->query($requete);
  $row = $db_forum->read_assoc($req);
  $nom = $row["username"];
  
	// Creation du sujet
	$time = time();
	$requete = "INSERT INTO ".$G_prefixe_forum."topics (poster, subject, posted, last_post, last_poster, forum_id) VALUES('$nom', '$titre', $time, $time, '$nom', $forum)";
	$db_forum->query($requete);
	$id_sujet = $db_forum->last_insert_id();
	
	// Création du post
	$requete = "INSERT INTO ".$G_prefixe_forum."posts (poster, poster_id, message, posted, topic_id) VALUES('$nom', $posteur, '$msg', $time, $id_sujet)";
	$db_forum->query($requete);
	$id_post = $db_forum->last_insert_id();
	
	// Ajout de l'id du dernier post au sujet
	$requete = "UPDATE ".$G_prefixe_forum."topics SET last_post_id=$id_post, first_post_id=$id_post WHERE id=$id_sujet";
	$db_forum->query($requete);
	
	// Incrémentation du nombre de posts
	$requete = "UPDATE ".$G_prefixe_forum."users SET num_posts=num_posts+1, last_post=$time WHERE id=$posteur";
	$db_forum->query($requete);
}
 
/**
 * Crée un post dans le forum annonce
 * 
 * @param  $titre     Titre du post.
 * @param  $msg       Message du post.
 * @param  $posteur   id du posteur.
 */
function creer_annonce($titre, $msg, $posteur=0)
{
  global $G_id_forum;
  
  if( !$posteur)
    $posteur = isset($G_id_forum) ? $G_id_forum : 2;
  creer_sujet_forum($titre, $msg, 5, $posteur);
}

/**
 * Crée un complément du nom en adaptant la prépsisiton au non.
 * Utilise "de" ou "d'" suivant que le nom commence par une consonne ou une voyelle
 * 
 * @param  nom    Nom
 * 
 * @return  Complément du nom sous la bonne forme  
 */
function creer_cdn($nom)
{
  switch( strtolower($nom[0]) )
  {
  case "a":
  case "e":
  case "i":
  case "o":
  case "i":
    return "d'$nom";
  default:
    return "de $nom";
  }
}

/**
 * Renvoie le nom du mois en français
 * 
 * @param  $mois    Numéro du mois
 * 
 * @return   nom du mois    
 */ 
function nom_mois($mois=0)
{
  if( !$mois )
    $mois = date("m");
  $noms = Array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", 
    "Septembre", "Octobre", "Novembre", "Décembre");
  print_r($mois);
  return $noms[$mois-1];
}

/**
 * Renvoie le nom du mois précédant en français
 * 
 * @param  $mois    Numéro du mois
 * 
 * @return   nom du mois    
 */ 
function nom_mois_prec($mois=0)
{
  if( !$mois )
    $mois = date("m");
  $mois--;
  if( !$mois )
    $mois = 12;
  return nom_mois($mois);
}
?> 