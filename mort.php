<?php // -*- mode: php; tab-width:2 -*-
/**
 * @file mort.php
 * Page pour les personnages morts 
 */
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'haut.php');

$joueur = new perso($_SESSION['ID']);

	// Personnage dans une arène ?
    $arene = false;
    if( $joueur->in_arene() )
    {
      $event = event::create_from_arenes_joueur($joueur);
      if( $event )
        $perso_ar = $event->get_arenes_joueur('id_perso='.$joueur->get_id().' AND statut='.arenes_joueur::en_cours);
      else
        $perso_ar = arenes_joueur::creer(0, 'arenes_joueur', 'id_perso='.$joueur->get_id().' AND statut='.arenes_joueur::en_cours);
      // Si a on trouvé les infos sur son TP, alors traitement spécial
      if( $perso_ar )
      {
        // rez non autorisée ou choix de sortir de l'arène
        if( ( $event !== null && !$event->rez_possible($joueur->get_id()) ) || $var == 2 )
        {
          // renvoie hors de l'arène
          $perso_ar[0]->teleporte( $joueur->get_nom() );
          return;
        }
        $arene = true;
      }
    }
	$R = new royaume($Trace[$joueur->get_race()]['numrace']);
	if ($R->is_raz()) $capitale_rez_p = 5;
	else $capitale_rez_p = 20;
	
	//Recherche du fort le plus proche
	$requete = "SELECT *, (ABS(".$joueur->get_x()." - cast(x as signed integer)) + ABS(".$joueur->get_y()." - cast(y as signed integer))) AS plop FROM `construction` WHERE rez > 0 AND type = 'fort' AND royaume = ".$Trace[$joueur->get_race()]['numrace']." ORDER BY plop ASC";
	$req_b = $db->query($requete);
	$bat = $db->num_rows;
	$row_b = $db->read_assoc($req_b);
	
	//Bonus mort-vivant
	if($joueur->get_race() == 'mortvivant') $bonus = 10;
	else $bonus = 0;
	
	//Vérifie s'il y a une amende qui empêche le spawn en ville
	$amende = recup_amende($joueur->get_id());
	//$echo = 'Revenir dans votre ville natale';
	$spawn_ville = 'ok';
	if($amende)
	{
		if($amende['respawn_ville'] == 'n')
		{
			//$echo = 'Revenir dans le refuge des criminels';
			$spawn_ville = 'wrong';
		}
	}

$interf_princ = $G_interf->creer_jeu();
		
if(isset($_GET['choix']))
{
$choix = $_GET['choix'];
$rez = false;

	if($choix == 1) // Ville
	{
		$rez = true;
		$pourcent = $capitale_rez_p;
		$duree_debuff = 43200;
		$multiplicateur_mouvement = 2;
		
		if($spawn_ville == 'ok')
		{ // Capitale
			$joueur->set_x($Trace[$joueur->get_race()]['spawn_x']);
			$joueur->set_y($Trace[$joueur->get_race()]['spawn_y']);
			
			$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rrez', '".$joueur->get_nom()."', 'Capitale', NOW(), '".$pourcent."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
			$db->query($requete);
		}
		else
		{ // Refuge des criminels
			$joueur->set_x($Trace[$joueur->get_race()]['spawn_c_x']);
			$joueur->set_y($Trace[$joueur->get_race()]['spawn_c_y']);
			
			$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rrez', '".$joueur->get_nom()."', 'Repère des criminels', NOW(), '".$pourcent."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
			$db->query($requete);
		}
	}
	elseif($choix == 2) //Rez d'un joueur
	{
		if(array_key_exists('rez', $_GET))
		{
			$requete = "SELECT pourcent, duree, malus, id_rez FROM rez WHERE id = '".sSQL($_GET['rez'])."' AND id_perso = ".$joueur->get_id();
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			if(is_array($row))
			{
				$rez = true;
				$pourcent = $row['pourcent'];
				$duree_debuff = $row['duree'];
				$multiplicateur_mouvement = $row['malus'];
			
				$rezzeur = new perso($row['id_rez']);
				$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$rezzeur->get_id().", 'rez', '".$rezzeur->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$pourcent."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
				$db->query($requete);
				$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rrez', '".$joueur->get_nom()."', '".$rezzeur->get_nom()."', NOW(), '".$pourcent."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
				$db->query($requete);
			}
		}
	}
	elseif($choix == 3) // Fort
	{
		$joueur->set_x($row_b['x']);
		$joueur->set_y($row_b['y']);
		
		$rez = true;
		$pourcent = $row_b['rez'];
		$duree_debuff = 43200;
		$multiplicateur_mouvement = 2;
		
		$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rrez', '".$joueur->get_nom()."', '".$row_b['nom']."', NOW(), '".$pourcent."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
		$db->query($requete);
	}
	
	if($rez)
	{
		$requete = "DELETE FROM rez WHERE id_perso = ".$joueur->get_id();
		$db->query($requete);
		
		$pourcent += $bonus;
		$joueur->set_hp($joueur->get_hp_maximum() * $pourcent / 100);
		$joueur->set_mp($joueur->get_mp_maximum() * $pourcent / 100);
		
		$joueur->set_regen_hp(time());

		//Téléportation dans sa ville avec PV et MP modifiés
		$joueur->sauver();

		//Vérifie si il a déjà un mal de rez
		$requete = "SELECT fin FROM buff WHERE id_perso = ".$joueur->get_id()." AND type = 'debuff_rez'";
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_row($req);
			$duree = $row[0] - time();
		}
		else $duree = 0;
		$duree_debuff += $duree;
		//Suppression des buffs
		$requete = "DELETE FROM buff WHERE id_perso = ".$joueur->get_id()." AND supprimable = 1";
		$db->query($requete);
		//Si rez en ville ou sur fort, on débuff le déplacement
		if($duree_debuff > 0)
		{
			//Déplacement * 2
			$effet = 2;
			lance_buff('debuff_rez', $joueur->get_id(), $effet, $multiplicateur_mouvement, $duree_debuff, 'Mal de résurrection', 'Mulitplie vos coûts de déplacement par '.$effet, 'perso', 1, 0, 0, 0);
		}
		// Second mal de res
		lance_buff('convalescence', $joueur->get_id(), 2, 2, 86400, 'Convalescence', 'Diminue votre efficacité pour le RvR.', 'perso', 1, 0, 0, 0);
	}
	$interf_princ->recharger_interface();
}
else
{
	$interf_princ->verif_mort($joueur, false);
	if( array_key_exists('rafraichir', $_GET) && $_GET['rafraichir']=='tout' )
		$interf_princ->maj_perso(true);
}
 
