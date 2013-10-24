<?php // -*- mode: php; tab-width:2 -*-
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
	$echo = 'Revenir dans votre ville natale';
	$spawn_ville = 'ok';
	if($amende)
	{
		if($amende['respawn_ville'] == 'n')
		{
			$echo = 'Revenir dans le refuge des criminels';
			$spawn_ville = 'wrong';
		}
	}

		
		
if(isset($_GET['choix']))
{
$choix = $_GET['choix'];
$rez = false;

	if($choix == 1) // Ville
	{
		$rez = true;
		$pourcent = $capitale_rez_p;
		$$duree_debuff = 43200;
		$multiplicateur_mouvement = 2;
		
		if($spawn_ville == 'ok')
		{ // Capitale
			$joueur->set_x($Trace[$joueur->get_race()]['spawn_x']);
			$joueur->set_y($Trace[$joueur->get_race()]['spawn_y']);
		}
		else
		{ // Refuge des criminels
			$joueur->set_x($Trace[$joueur->get_race()]['spawn_c_x']);
			$joueur->set_y($Trace[$joueur->get_race()]['spawn_c_y']);
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
				$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'rez', '".$rezzeur->get_nom()."', '', NOW(), '', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
				$db->query($requete);
				$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$rezzeur->get_id().", 'rrez', '".$joueur->get_nom()."', '', NOW(), '', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
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
	echo '<img src="image/pixel.gif" onload="window.location = \'interface.php\';" />';
}
else
{
?>
	<div id='mort'>
	<fieldset>
		Que voulez vous faire ?
		<ul>
		<?php
		//Supprime les Rez plus valides
		$requete = "DELETE FROM rez WHERE TIMESTAMPDIFF(MINUTE , time, NOW()) > 1440";
		//$db->query($requete);
		// Liste des rez
		$requete = "SELECT * FROM rez WHERE id_perso = ".$joueur->get_id();
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			while($row = $db->read_assoc($req))
			{
				echo '<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=2&amp;rez='.$row['id'].'">Vous faire ressusciter par '.$row['nom_rez'].' ('.($row['pourcent'] + $bonus).'% HP / '.($row['pourcent'] + $bonus).' MP)</li>';
			}
		}
		// Fort le plus proche (si on le personnage n'est pas dans un donjon)
		if($bat > 0 AND !is_donjon($joueur->get_x(), $joueur->get_y()))
			echo '<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=3&amp;rez='.$row_d['id'].'">Revenir dans le fort le plus proche (x : '.$row_b['x'].' / y : '.$row_b['y'].') ('.($row_b['rez'] + $bonus).'% HP / '.($row_b['rez'] + $bonus).'% MP)</li>';
		if($arene)// sortie de l'arène
			echo '<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=1">Sortir de l\'arène</a></li>';
		else // Capitale ou refuge des criminels
			echo '<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=1">'.$echo.' ('.($capitale_rez_p + $bonus).'% HP / '.($capitale_rez_p + $bonus).'% MP)</a></li>';
		?>
			<li style="padding-top:5px;padding-bottom:5px;"><a href="index.php?deco=ok">Vous déconnecter</a></li>
			<li style="padding-top:5px;padding-bottom:5px;">Vous pouvez attendre qu'un autre joueur vous ressucite</li>
		</ul>
		<a href="index.php">Index du jeu</a> - <a href="http://forum.starshine-online.com">Accéder au forum</a>  - <a href="http://forum.starshine-online.com/jappix/">Accéder au Tchat</a>
	</fieldset>
	</div>
<?php
}
 
?>

