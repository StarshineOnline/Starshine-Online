<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
/**
* @file time.inc.php
* Fonction relatives au temps / dates / durées
* 
*/


/***************************************************************************
                                 time.inc
 *                         ----------------------
 *   begin                : Vendredi 27 février 2004
 *   copyright            : (C)2004 Triumastra
 *   email                : masterob1@chello.fr
 *
 *   $Id: time.inc, v0.01 20040227
 *
 ***************************************************************************/

/***************************************************************************
 *
 * Fonction relatives au temps / dates / durées
 * 
 *
 * Connection à la bdd : non
 * Utilisé dans les fichiers : index.php
 *
 ***************************************************************************/

 
/**
 * Fonction ajoutant des zeros avant un nombre afin qu'il y ait le nombre de chiffres voulu.
 * 
 * @param $numero Nombre auquel il faut rajouter des zeros.
 * @param $lenght Nombre de chiffres désiré.
 * @return Nombre avec les zeros devant si nécessaire. 
 */   
function zero_before($numero, $lenght)
{
	
	$nb_iteration = strlen($numero);
	for($i = 0 ; $i  < $lenght - $nb_iteration ; $i++)
	{
		$numero = "0".$numero;
	}
	return $numero;
}

/**
* Fonction permettant de récuperer le timestamp le plus précis possible
* 
* @return [float] timestamp en ms
* 
* @version 0.1
* @date 27/02/2004
* @note A partir de PHP 5, microtime(true) à le même effet.
*/
function getmicrotime()
{
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
}

/**
 * Formate l'affichage du temps en jours, heures, minutes et secondes.
 * Les jours ne sont affichés que s'il y en a au moins un. Pareil pour les heures et les minutes. 
 * 
 * @param $time Temps à formater.
 * @return Temps formaté.
 */    
function transform_sec_temp($time)
{
	$W_time = $time;
	$string = '';
	//Affichage en jours
	if($W_time >= 86400)
	{
		$jours = floor($W_time / 86400);
		$W_time = $W_time - ($jours * 86400);
		$string .= $jours.'j';
	}
	//Affichage en heures
	if($W_time >= 3600)
	{
		$heures = floor($W_time / 3600);
		$W_time = $W_time - ($heures * 3600);
		$string .= ' '.zero_before($heures, 2).'h';
	}
	
	//Affichage en minutes
	if($W_time >= 60)
	{
		if(empty($heures) && !empty($jours)) { $string .= ' '.zero_before($heures, 2).'h'; };
		
		$minutes = floor($W_time / 60);
		$W_time = $W_time - ($minutes * 60);
		$string .= ' '.zero_before($minutes, 2).'mins';
	}
	// Affichage des secondes
	if($W_time > 0)
	{
		if(empty($minutes) && (!empty($heures) || !empty($jours)) ) { $string .= ' '.zero_before($minutes, 2).'mins'; };
		
		$string .= ' '.zero_before($W_time, 2).'s';
	}
	if(empty($W_time) && (!empty($minutes) || !empty($heures) || !empty($jours)) ) { $string .= ' '.zero_before($W_time, 2).'s'; };
		
	return $string;
}

/**
 * Formate l'affichage du temps en jours, heures et minutes.
 * Les jours ne sont affichés que s'il y en a au moins un. Pareil pour les heures. 
 * 
 * @param $time Temps à formater.
 * @return Temps formaté.
 */    
function transform_min_temp($time)
{
	$W_time = $time;
	$string = '';
	//Affichage en jours
	if($W_time >= 86400)
	{
		$jours = floor($W_time / 86400);
		$W_time = $W_time - ($jours * 86400);
		$string .= $jours.'j';
	}
	//Affichage en heures
	if($W_time >= 3600)
	{
		$heures = floor($W_time / 3600);
		$W_time = $W_time - ($heures * 3600);
		$string .= ' '.zero_before($heures, 2).'h';
	}
	//Affichage en minutes
	if($W_time >= 60)
	{
		if(empty($heures) && !empty($jours)) { $string .= ' '.zero_before($heures, 2).'h'; };
		
		$minutes = floor($W_time / 60);
		$W_time = $W_time - ($minutes * 60);
		$string .= ' '.zero_before($minutes, 2).'mins';
	}
	if(empty($minutes) && (!empty($heures) || !empty($jours)) ) { $string .= ' '.zero_before($minutes, 2).'mins'; };
		
	return $string;
}

/**
 * Renvoie la date du prochain mandat royal.
 * 
 * @return Date sous la forme "AAAA-MM".
*/     
function date_prochain_mandat()
{
	return date("Y-m", mktime(0, 0, 0, date("m")+1, 1,  date("Y")));
}

/**
 * Renvoie l'heure (uniquement) qu'il est dans le jeu
 * 
 * @return Heure du jeu au format "HH" (sur 24h).  
*/
function heure_sso($time = 0)
{
	if ($time == 0) {
		$time = time();
	}
	$heure = intval(date("H", ($time / 18 * 24)));
	return $heure;
}

/**
 * Renvoie l'heure (avec minutes et secondes) qu'il est dans le jeu
 * 
 * @return Heure du jeu au format "HH:MM:SS" (heure sur 24h).  
*/
function date_sso($time = 0)
{
	if ($time == 0) {
		$time = time();
	}
	$date = date("H:i:s", ($time / 18 * 24));
	return $date;
}

/**
 * Indique quel est le moment de la journée (matin, journée, soir ou nuit).
 * 
 * @return Moment de la journée.  
*/
function moment_jour($id_joueur = 0)
{
	global $joueur, $db;
	if (isset($joueur) && $joueur != null)
	{
		$x = $joueur->get_x();
		$y = $joueur->get_y();
	}
	else
	{
		// On ne peut pas créer d'objet perso car cette fonction est appelée dans son constructeur
	  	$id_joueur = empty($id_joueur) ? $_SESSION['ID'] : $id_joueur;
    	$requete = "SELECT x,y FROM perso WHERE id =".$id_joueur;
    	$req = $db->query($requete);
  		if( $row = $db->read_assoc($req) )
  		{
				$x = $row["x"];
		  	$y = $row["y"];
  		}
	}
	$temps = time();
	if ($x >= 200)
	{
		$q = "select * from arenes where x <= $x and $x < x + size ".
			"and y <= $y and $y < y + size $filter";
		$req = $db->query($q);
		if ($row = $db->read_object($req)) {
			$temps += $row->decal;
		}
	}
	$heure = heure_sso($temps);
	if($heure > 5 AND $heure < 10) $moment = 'Matin';
	elseif($heure > 9 AND $heure < 16) $moment = 'Journee';
	elseif($heure > 15 AND $heure < 20) $moment = 'Soir';
	else $moment = 'Nuit';
	return $moment;
}

/**
 * Calcule un decalage temporel
 * @param $moment_voulu le moment de la journée à atteindre
 * @param $heure l'heure auquel le moment doit être atteint (0 = NOW)
 * @param $percent_moment l'avancement du moment voulu (0 <= P < 100)
 * @return le decalage a appliquer
 */
function calcul_decal($moment_voulu, $heure = 0, $percent_moment = 0)
{
	switch ($moment_voulu) {
	case 'Matin':
		$temps_vise = 5 * 3600 + round(($percent_moment / 100) * 5 * 3600);
		break;
	case 'Journee':
		$temps_vise = 9 * 3600 + round(($percent_moment / 100) * 7 * 3600);
		break;
	case 'Soir':
		$temps_vise = 15 * 3600 + round(($percent_moment / 100) * 5 * 3600);
		break;
	case 'Nuit':
		$temps_vise = 20 * 3600 + round(($percent_moment / 100) * 7 * 3600);
		break;
	}
	$heure_visee = $temps_vise / 24 * 18;

	if ($heure == 0) $heure = time();
}

?>
