<?php

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

 
/**************************************
 *	
 *	Fonction Permettant de récuperer le timestamp le plus précis possible
 *
 *	Paramètres : aucun
 *	Renvoi : réel (timestamp en ms)
 *
 *	v0.1 20040227
 *************************************/
function zero_before($numero, $lenght)
{
	
	$nb_iteration = strlen($numero);
	for($i = 0 ; $i  < $lenght - $nb_iteration ; $i++)
	{
		$numero = "0".$numero;
	}
	return $numero;
}
function getmicrotime()
{
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
}

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
	if($W_time > 0)
	{
		if(empty($minutes) && (!empty($heures) || !empty($jours)) ) { $string .= ' '.zero_before($minutes, 2).'mins'; };
		
		$string .= ' '.zero_before($W_time, 2).'s';
	}
	if(empty($W_time) && (!empty($minutes) || !empty($heures) || !empty($jours)) ) { $string .= ' '.zero_before($W_time, 2).'s'; };
		
	return $string;
}
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
function date_prochain_mandat()
{
	return date("Y-m", mktime(0, 0, 0, date("m")+1, 1,  date("Y")));
}

function heure_sso()
{
	$heure = intval(date("H", (time() / 18 * 24)));
	return $heure;
}

function date_sso()
{
	$date = date("H:i:s", (time() / 18 * 24));
	return $date;
}

function moment_jour()
{
	$heure = heure_sso();
	if($heure > 5 AND $heure < 10) $moment = 'Matin';
	elseif($heure > 9 AND $heure < 16) $moment = 'Journee';
	elseif($heure > 15 AND $heure < 20) $moment = 'Soir';
	else $moment = 'Nuit';
	return $moment;
}

?>
