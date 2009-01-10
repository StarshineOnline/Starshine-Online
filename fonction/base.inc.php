<?php //  -*- tab-width:2  -*-
/**
 * @file base.inc.php
 * Fonctions de base 
 */ 


/**
 * Convertion de la position d'un nombre en deux nombres.
 * Donne les coordonées x et y d'un points à partir de la version compressée.
 *  
 * @param $pos Position sous forme compressée.
 *  
 * @return ['x'] Position horizontale.
 * @return ['y'] Position verticale.
 */ 
function convert_in_coord($pos)
{
	$coord['y'] = floor($pos / 1000);
	$coord['x'] = $pos - ($coord['y'] * 1000);
	return $coord;
}

/**
 * Convertion des coordonnés de deux nombre en un nombre.
 * Donne la version compressée de la position à partir des coordonnées x et y.
 *  
 * @param $x Position horizontale.
 * @param $y Position verticale.
 *  
 * @return Position sous forme compressée.
 */ 
function convert_in_pos($x, $y)
{
	$pos = $y * 1000 + $x;
	return $pos;
}

/**
 * Convertion de la position de deux nombre en un nombre dans un donjon.
 * Donne les coordonées x et y d'un points situé dans un donjon à partir de la
 * version compressée.
 *  
 * @param $pos Position sous forme compressée.
 *  
 * @return ['x'] Position horizontale.
 * @return ['y'] Position verticale.
 */ 
function convertd_in_coord($pos)
{
	$coord['y'] = floor($pos / 1000);
	$coord['x'] = $pos - ($coord['y'] * 1000);
	return $coord;
}

/**
 * Convertion des coordonnés de deux nombre en un nombre dans un donjon.
 * Donne la version compressée de la position dans un donjon à partir des
 * coordonnées x et y.
 *  
 * @param $x Position horizontale.
 * @param $y Position verticale.
 *  
 * @return Position sous forme compressée.
 */ 
function convertd_in_pos($x, $y)
{
	$pos = $y * 1000 + $x;
	return $pos;
}

/**
 * Detecte si l'attaquant est sur la meme case.
 * Donne le maximum entre les distances horizontale et varticale entre deux personnages.
 * 
 * @param $posjoueur1 position, sous forme compressée, du premier personnage.
 * @param $posjoueur2 position, sous forme compressée, du deuxième personnage.
 *  
 * @return maximum entre les distances horizontale et varticale (0 s'ils sont sur 
 * la même case, non nul sinon).
 */ 
function detection_distance($posjoueur1, $posjoueur2)
{
	$W_coord_joueur1 = convert_in_coord($posjoueur1);
	$W_coord_joueur2 = convert_in_coord($posjoueur2);
	$R_distance = max(abs($W_coord_joueur1['x'] - $W_coord_joueur2['x']), abs($W_coord_joueur1['y'] - $W_coord_joueur2['y']));
	return $R_distance;
}

/**
 * Calcule la distance "non pythagoriciene" entre deux personnages.
 * Cette distance est la somme des distance horizontale et verticale.
 * 
 * @param $posjoueur1 position, sous forme compressée, du premier personnage.
 * @param $posjoueur2 position, sous forme compressée, du deuxième personnage.
 *  
 * @return distance entre les distances horizontale et varticale.
 */ 
function calcul_distance($posjoueur1, $posjoueur2)
{
	$W_coord_joueur1 = convert_in_coord($posjoueur1);
	$W_coord_joueur2 = convert_in_coord($posjoueur2);
	$R_distance = abs($W_coord_joueur1['x'] - $W_coord_joueur2['x']) + abs($W_coord_joueur1['y'] - $W_coord_joueur2['y']);
	return $R_distance;
}

/**
 * Calcule la distance "pythagoriciene" entre deux personnages.
 * La distance est calculée avec la formule de Pythagore (distance "classique").
 * 
 * @param $posjoueur1 position, sous forme compressée, du premier personnage.
 * @param $posjoueur2 position, sous forme compressée, du deuxième personnage.
 *  
 * @return distance entre les distances horizontale et varticale.
 */ 
function calcul_distance_pytagore($posjoueur1, $posjoueur2)
{
	$W_coord_joueur1 = convert_in_coord($posjoueur1);
	$W_coord_joueur2 = convert_in_coord($posjoueur2);
	$R_distance = ceil(sqrt(pow(abs($W_coord_joueur1['x'] - $W_coord_joueur2['x']), 2) + pow(abs($W_coord_joueur1['y'] - $W_coord_joueur2['y']), 2)));
	return $R_distance;
}

/**
 *
 */
function dimension_map($x, $y, $champ_vision)
{
	$case_affiche = ($champ_vision * 2) + 1;
	$dimensions = array();

	if($x < ($champ_vision + 1))			{ $dimensions['xmin'] = 1;		$dimensions['xmax'] = $x + ($case_affiche - ($x)); }
	elseif($x > (150 - $champ_vision))		{ $dimensions['xmax'] = 150;		$dimensions['xmin'] = $x - ($case_affiche - (150 - $x + 1)); }
	else								{ $dimensions['xmin'] = $x - $champ_vision;	$dimensions['xmax'] = $x + $champ_vision; };
	
	if($y < ($champ_vision + 1))		{ $dimensions['ymin'] = 1;		$dimensions['ymax'] = $y + ($case_affiche - ($y)); }
	elseif($y > (150 - $champ_vision))	{ $dimensions['ymax'] = 150;		$dimensions['ymin'] = $y - ($case_affiche - (150 - $y + 1)); }
	else								{ $dimensions['ymin'] = $y - $champ_vision; 	$dimensions['ymax'] = $y + $champ_vision; }

	return $dimensions;
}

/**
 * Indique si on est dans un donjon.
 *  
 * @param $x Position horizontale.
 * @param $y Position verticale.
 *  
 * @return true si on est dans un donjon, non sinon.
 */ 
function is_donjon($x, $y)
{
	if($x > 150 OR $y > 150)
	{
		return true;
	}
	return false;
}

/**
 * Donne le nombre de bourg que possède un royaume
 * 
 * @param $id_royaume ID du royaume.
 * 
 * @return nombre de bourgs.
 */
function nb_bourg($id_royaume)
{
	global $db;
	$bourgs = 0;
	//Nombre de bourg déjà construits
	$requete = "SELECT bourg FROM royaume WHERE ID = ".$id_royaume;
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$bourgs = $row[0];
	//Nombre de bourgs en construction
	//Nombre de bourgs dans le qg
	return $bourgs;
}

/**
 * Donne le nombre de cases que possède un royaume
 * 
 * @param $id_royaume ID du royaume.
 * 
 * @return nombre de cases.
 */
function nb_case($id_royaume)
{
	global $db;
	$requete = "SELECT COUNT(*) FROM map WHERE royaume = ".$id_royaume;
	$req = $db->query($requete);
	$row = $db->read_row($req);
	return $row[0];
}

/**
 * Donne le nombre d'habitants que possède un royaume
 * 
 * @param $id_royaume ID du royaume.
 * 
 * @return nombre d'habitants.
 */
function nb_habitant($race)
{
	global $db;
	$requete = "SELECT COUNT(*) FROM perso WHERE statut = 'actif' AND race = '".$race."'";
	$req = $db->query($requete);
	$row = $db->read_row($req);
	return $row[0];
}

/**
 * Renvoi le type de terrain, et le type de terrain affichable.
 * Le "type de terrain affichable" coreespond à ce qui est affiché dans le jeu.
 * 
 * @param $info Numéro du type de terrain
 * 
 * @return [0] type de terrain (usage interne).
 * @return [1] type de terrain (pour afficher).
 */ 
function type_terrain($info)
{
	//Initialise les variables de terrain
	$typeterrain[1][0] = 'plaine';
	$typeterrain[1][1] = 'Plaine';
	$typeterrain[2][0] = 'foret';
	$typeterrain[2][1] = 'Forêt';
	$typeterrain[3][0] = 'desert';
	$typeterrain[3][1] = 'Désert';
	$typeterrain[4][0] = 'glace';
	$typeterrain[4][1] = 'Glace';
	$typeterrain[5][0] = 'eau';
	$typeterrain[5][1] = 'Eau';
	$typeterrain[6][0] = 'montagne';
	$typeterrain[6][1] = 'Montagne';
	$typeterrain[7][0] = 'marais';
	$typeterrain[7][1] = 'Marais';
	$typeterrain[8][0] = 'route';
	$typeterrain[8][1] = 'Route';
	$typeterrain[10][0] = 'objet';
	$typeterrain[10][1] = 'Bâtiment';
	$typeterrain[11][0] = 'terre_maudite';
	$typeterrain[11][1] = 'Terre Maudite';
	$typeterrain[15][0] = 'donjon';
	$typeterrain[15][1] = 'Donjon';
	$typeterrain[16][0] = 'mur_donjon';
	$typeterrain[16][1] = 'Mur de Donjon';
	/* TEMPLATE
	$typeterrain[][0] = '';
	$typeterrain[][1] = '';
	*/
	//Type de terrain
	$return[0] = $typeterrain[$info][0];
	//Type de terrain en texte a affiché
	$return[1] = $typeterrain[$info][1];
	return $return;
}

/**
 * Renvoi la liste des ressource produite par ce terrain.
 * 
 * @param $terrain Nom du type de terrain
 * 
 * @return array liste des ressources.
 */ 
function ressource_terrain($terrain)
{
	$ress = array();
	$ress['Plaine']['Pierre'] = 4;
	$ress['Plaine']['Bois'] = 4;
	$ress['Plaine']['Eau'] = 5;
	$ress['Plaine']['Sable'] = 2;
	$ress['Plaine']['Nourriture'] = 8;
	$ress['Plaine']['Star'] = 0;
	$ress['Plaine']['Charbon'] = 0;
	$ress['Plaine']['Essence Magique'] = 0;
	
	$ress['Forêt']['Pierre'] = 3;
	$ress['Forêt']['Bois'] = 8;
	$ress['Forêt']['Eau'] = 4;
	$ress['Forêt']['Sable'] = 0;
	$ress['Forêt']['Nourriture'] = 5;
	$ress['Forêt']['Star'] = 0;
	$ress['Forêt']['Charbon'] = 0;
	$ress['Forêt']['Essence Magique'] = 3;
	
	$ress['Désert']['Pierre'] = 6;
	$ress['Désert']['Bois'] = 0;
	$ress['Désert']['Eau'] = 0;
	$ress['Désert']['Sable'] = 8;
	$ress['Désert']['Nourriture'] = 2;
	$ress['Désert']['Star'] = 0;
	$ress['Désert']['Charbon'] = 2;
	$ress['Désert']['Essence Magique'] = 4;
	
	$ress['Montagne']['Pierre'] = 8;
	$ress['Montagne']['Bois'] = 4;
	$ress['Montagne']['Eau'] = 3;
	$ress['Montagne']['Sable'] = 5;
	$ress['Montagne']['Nourriture'] = 2;
	$ress['Montagne']['Star'] = 0;
	$ress['Montagne']['Charbon'] = 0;
	$ress['Montagne']['Essence Magique'] = 1;
	
	$ress['Marais']['Pierre'] = 0;
	$ress['Marais']['Bois'] = 1;
	$ress['Marais']['Eau'] = 1;
	$ress['Marais']['Sable'] = 3;
	$ress['Marais']['Nourriture'] = 2;
	$ress['Marais']['Star'] = 0;
	$ress['Marais']['Charbon'] = 4;
	$ress['Marais']['Essence Magique'] = 8;
	
	$ress['Terre Maudite']['Pierre'] = 2;
	$ress['Terre Maudite']['Bois'] = 2;
	$ress['Terre Maudite']['Eau'] = 0;
	$ress['Terre Maudite']['Sable'] = 1;
	$ress['Terre Maudite']['Nourriture'] = 1;
	$ress['Terre Maudite']['Star'] = 0;
	$ress['Terre Maudite']['Charbon'] = 8;
	$ress['Terre Maudite']['Essence Magique'] = 5;
	
	$ress['Glace']['Pierre'] = 1;
	$ress['Glace']['Bois'] = 0;
	$ress['Glace']['Eau'] = 8;
	$ress['Glace']['Sable'] = 0;
	$ress['Glace']['Nourriture'] = 2;
	$ress['Glace']['Star'] = 0;
	$ress['Glace']['Charbon'] = 2;
	$ress['Glace']['Essence Magique'] = 3;
	
	$ress['Route']['Pierre'] = 0;
	$ress['Route']['Bois'] = 0;
	$ress['Route']['Eau'] = 0;
	$ress['Route']['Sable'] = 0;
	$ress['Route']['Nourriture'] = 0;
	$ress['Route']['Star'] = 30;
	$ress['Route']['Charbon'] = 0;
	$ress['Route']['Essence Magique'] = 0;
	
	return $ress[$terrain];
}

/**
 * Indique si une case fait partie d'une ville.
 * 
 * @param $case position compressée de la ville.
 * 
 * @return numéro du type de terrain si la case appartient à un royaume false sinon.
 */
function is_ville($case)
{
	global $db;
	$requete = "SELECT type, royaume FROM map WHERE ID = ".$case;
	$req = $db->query($requete);
	$row = $db->read_row($req);
	if($row[1] != 0) return $row[0];
	else return false;
}

/**
 * Donne le coût en PA d'un déplacement en fonction du terrain et de la race.
 * Une valeur de 50 indique que l'on ne peut pas aller sur la case. Si le type de
 * terrain n'est pas trouvé, le coût est fixé à 6 PA.
 * 
 * @param $info Type de terrain.
 * @param $race Race.
 * 
 * @return Coût en PA. 
 */
function cout_pa($info, $race)
{
	//Initialisation des variables de déplacement, 50 = infranchissable.
	/*** BARBARES ***/
	$coutpa['barbare']['plaine'] = 4;
	$coutpa['barbare']['foret'] = 6;
	$coutpa['barbare']['desert'] = 5;
	$coutpa['barbare']['glace'] = 4;
	$coutpa['barbare']['marais'] = 5;
	$coutpa['barbare']['montagne'] = 6;
	$coutpa['barbare']['eau'] = 50;
	$coutpa['barbare']['route'] = 2;
	$coutpa['barbare']['objet'] = 2;
	$coutpa['barbare']['terre_maudite'] = 5;
	$coutpa['barbare']['donjon'] = 5;
	$coutpa['barbare']['mur_donjon'] = 50;

	/*** ELFE DES BOIS ***/
	$coutpa['elfebois']['plaine'] = 4;
	$coutpa['elfebois']['foret'] = 5;
	$coutpa['elfebois']['desert'] = 5;
	$coutpa['elfebois']['glace'] = 5;
	$coutpa['elfebois']['marais'] = 5;
	$coutpa['elfebois']['montagne'] = 6;
	$coutpa['elfebois']['eau'] = 50;
	$coutpa['elfebois']['route'] = 2;
	$coutpa['elfebois']['objet'] = 2;
	$coutpa['elfebois']['terre_maudite'] = 5;
	$coutpa['elfebois']['donjon'] = 5;
	$coutpa['elfebois']['mur_donjon'] = 50;

	/*** HAUT ELFE ***/
	$coutpa['elfehaut']['plaine'] = 4;
	$coutpa['elfehaut']['foret'] = 5;
	$coutpa['elfehaut']['desert'] = 5;
	$coutpa['elfehaut']['glace'] = 5;
	$coutpa['elfehaut']['marais'] = 5;
	$coutpa['elfehaut']['montagne'] = 6;
	$coutpa['elfehaut']['eau'] = 50;
	$coutpa['elfehaut']['route'] = 2;
	$coutpa['elfehaut']['objet'] = 2;
	$coutpa['elfehaut']['terre_maudite'] = 5;
	$coutpa['elfehaut']['donjon'] = 5;
	$coutpa['elfehaut']['mur_donjon'] = 50;

	/*** HUMAIN ***/
	$coutpa['humain']['plaine'] = 4;
	$coutpa['humain']['foret'] = 6;
	$coutpa['humain']['desert'] = 5;
	$coutpa['humain']['glace'] = 5;
	$coutpa['humain']['marais'] = 5;
	$coutpa['humain']['montagne'] = 6;
	$coutpa['humain']['eau'] = 50;
	$coutpa['humain']['route'] = 2;
	$coutpa['humain']['objet'] = 2;
	$coutpa['humain']['terre_maudite'] = 5;
	$coutpa['humain']['donjon'] = 5;
	$coutpa['humain']['mur_donjon'] = 50;

	/*** HUMAINS NOIRS ***/
	$coutpa['humainnoir']['plaine'] = 4;
	$coutpa['humainnoir']['foret'] = 6;
	$coutpa['humainnoir']['desert'] = 5;
	$coutpa['humainnoir']['glace'] = 5;
	$coutpa['humainnoir']['marais'] = 5;
	$coutpa['humainnoir']['montagne'] = 6;
	$coutpa['humainnoir']['eau'] = 50;
	$coutpa['humainnoir']['route'] = 2;
	$coutpa['humainnoir']['objet'] = 2;
	$coutpa['humainnoir']['terre_maudite'] = 5;
	$coutpa['humainnoir']['donjon'] = 5;
	$coutpa['humainnoir']['mur_donjon'] = 50;

	/*** MORTS VIVANTS ***/
	$coutpa['mortvivant']['plaine'] = 4;
	$coutpa['mortvivant']['foret'] = 6;
	$coutpa['mortvivant']['desert'] = 5;
	$coutpa['mortvivant']['glace'] = 5;
	$coutpa['mortvivant']['marais'] = 4;
	$coutpa['mortvivant']['montagne'] = 6;
	$coutpa['mortvivant']['eau'] = 50;
	$coutpa['mortvivant']['route'] = 2;
	$coutpa['mortvivant']['objet'] = 2;
	$coutpa['mortvivant']['terre_maudite'] = 5;
	$coutpa['mortvivant']['donjon'] = 5;
	$coutpa['mortvivant']['mur_donjon'] = 50;

	/*** NAINS ***/
	$coutpa['nain']['plaine'] = 4;
	$coutpa['nain']['foret'] = 6;
	$coutpa['nain']['desert'] = 5;
	$coutpa['nain']['glace'] = 5;
	$coutpa['nain']['marais'] = 5;
	$coutpa['nain']['montagne'] = 5;
	$coutpa['nain']['eau'] = 50;
	$coutpa['nain']['route'] = 2;
	$coutpa['nain']['objet'] = 2;
	$coutpa['nain']['terre_maudite'] = 5;
	$coutpa['nain']['donjon'] = 5;
	$coutpa['nain']['mur_donjon'] = 50;

	/*** ORC ***/
	$coutpa['orc']['plaine'] = 4;
	$coutpa['orc']['foret'] = 6;
	$coutpa['orc']['desert'] = 5;
	$coutpa['orc']['glace'] = 5;
	$coutpa['orc']['marais'] = 5;
	$coutpa['orc']['montagne'] = 6;
	$coutpa['orc']['eau'] = 50;
	$coutpa['orc']['route'] = 2;
	$coutpa['orc']['objet'] = 2;
	$coutpa['orc']['terre_maudite'] = 5;
	$coutpa['orc']['donjon'] = 5;
	$coutpa['orc']['mur_donjon'] = 50;

	/*** SCAVENGERS ***/
	$coutpa['scavenger']['plaine'] = 4;
	$coutpa['scavenger']['foret'] = 6;
	$coutpa['scavenger']['desert'] = 4;
	$coutpa['scavenger']['glace'] = 5;
	$coutpa['scavenger']['marais'] = 5;
	$coutpa['scavenger']['montagne'] = 6;
	$coutpa['scavenger']['eau'] = 50;
	$coutpa['scavenger']['route'] = 2;
	$coutpa['scavenger']['objet'] = 2;
	$coutpa['scavenger']['terre_maudite'] = 5;
	$coutpa['scavenger']['donjon'] = 5;
	$coutpa['scavenger']['mur_donjon'] = 50;

	/*** TROLL ***/
	$coutpa['troll']['plaine'] = 4;
	$coutpa['troll']['foret'] = 6;
	$coutpa['troll']['desert'] = 5;
	$coutpa['troll']['glace'] = 5;
	$coutpa['troll']['marais'] = 5;
	$coutpa['troll']['montagne'] = 6;
	$coutpa['troll']['eau'] = 50;
	$coutpa['troll']['route'] = 2;
	$coutpa['troll']['objet'] = 2;
	$coutpa['troll']['terre_maudite'] = 5;
	$coutpa['troll']['donjon'] = 5;
	$coutpa['troll']['mur_donjon'] = 50;

	/*** VAMPIRES ***/
	$coutpa['vampire']['plaine'] = 4;
	$coutpa['vampire']['foret'] = 6;
	$coutpa['vampire']['desert'] = 5;
	$coutpa['vampire']['glace'] = 5;
	$coutpa['vampire']['marais'] = 5;
	$coutpa['vampire']['montagne'] = 6;
	$coutpa['vampire']['eau'] = 50;
	$coutpa['vampire']['route'] = 2;
	$coutpa['vampire']['objet'] = 2;
	$coutpa['vampire']['terre_maudite'] = 4;
	$coutpa['vampire']['donjon'] = 5;
	$coutpa['vampire']['mur_donjon'] = 50;

	/*** RACE ***/
	/* TEMPLATE
	$coutpa['']['plaine'] = ;
	$coutpa['']['foret'] = ;
	$coutpa['']['desert'] = ;
	$coutpa['']['glace'] = ;
	$coutpa['']['eau'] = ;
	$coutpa['']['route'] = ;
	$coutpa['']['objet'] = ;
	$coutpa['']['donjon'] = 5;
	$coutpa['']['mur_donjon'] = 50;
	*/
	if ($coutpa[$race][$info] == '') $coutpa[$race][$info] = 6;
	return $coutpa[$race][$info];
}

/**
 * Ajuste le coût en PA d'un déplacement en fonction de tous les parmètres qui le modifient.
 * Prend en compte la diagonale, le royaume de la case, les buffs et les débuffs.
 * Le coût minimal est de 3 en diagonal et 2 sinon. 
 * 
 * @param $coutpa     coût en PA de base.
 * @param $joueur     taleau contenant les informaton sur le joueur
 * @param $case       position compressée de la ville.
 * @param $diagonale  true si le déplacement est en diagonal, false sinon.
 * 
 * @return Coût en PA modifié.  
 */
function cout_pa2($coutpa, $joueur, $case, $diagonale)
{
	global $Trace;
	$coord = convert_in_coord($case['ID']);
	//Si on est sur son royaume => Cout en PA réduit de 1, minimum 1
	if($case['royaume'] == $Trace[$joueur['race']]['numrace'])
	{
		if($coutpa > 2) $coutpa -= 1;
	}
	//Buff rapide comme le vent
	if(array_key_exists('rapide_vent', $joueur['buff']))
	{
		if($coutpa > 2) $coutpa -= 1;
	}
	
	if ($diagonale) $coutpa++;
	//Mal de rez
	if(array_key_exists('debuff_rez', $joueur['debuff']))
	{
		$coutpa = $coutpa * $joueur['debuff']['debuff_rez']['effet'];
	}
	//Maladies
	if(array_key_exists('cout_deplacement', $joueur['debuff'])) $coutpa = ceil($coutpa / $joueur['debuff']['cout_deplacement']['effet']);
	if(array_key_exists('plus_cout_deplacement', $joueur['debuff'])) $coutpa = ceil($coutpa * $joueur['debuff']['plus_cout_deplacement']['effet']);
	//Bâtiment qui augmente le coût de PA
	if($batiment = batiment_map($coord['x'], $coord['y']))
	{
		$coutpa = $coutpa * $batiment['augmentation_pa'];
		//Si on est sur son royaume réduction du cout de PA par 2
		if($case['royaume'] == $Trace[$joueur['race']]['numrace'])
		{
			$coutpa = ceil($coutpa / 2);
		}
	}	
	return $coutpa;
}

/**
 * Fonction permettant de récupérer les infos essentielles du perso (nom, race, classe, level, ID, rang_royaume).
 * Si l'ID est égale à une chaîne vide affiche un message demandant au joueur de se
 * reconnecter et arrête l'interprétation.  
 * 
 * @param $ID       ID du personnage.
 * @param $select   liste des informations (entrées de la base de donnée) à récupérer.
 *  
 * @return Informations demandées sous forme de tableau associatif.
 */ 
function recupperso_essentiel($ID, $select = 'ID, nom, level, rang_royaume, race, classe')
{
	global $db;
	if(is_numeric($ID))
	{
		if($ID != '')
		{
			$requete = 'SELECT '.$select.' FROM perso WHERE ID = '.$ID;
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$R_perso = $db->read_assoc($req);
				return $R_perso;
			}
			else
			{
				return false;
			}
		}
		else
		{
			echo 'Vous êtes déconnectés, veuillez vous reconnecter.';
			exit();
		}
	}
}

/**
 * Fonction permettant de récupérer les infos des perso, et de les renvoyer.
 * Si l'ID est égale à une chaîne vide affiche un message demandant au joueur de se
 * reconnecter et arrête l'interprétation.  
 * 
 * @param $ID   ID du personnage.
 * 
 * #return      Informatiosn du persdonnages sus forme de tableau associatif.
 */ 
function recupperso($ID)
{
	global $db, $G_buff;  // $G_buff inutilié
	if(is_numeric($ID))
	{
		if($ID != '')
		{
			$requete = 'SELECT * FROM perso WHERE ID = '.$ID;
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_array($req);
				
				$R_perso['ID'] = $row['ID'];
				$R_perso['nom'] = $row['nom'];
				$R_perso['exp'] = $row['exp'];
				$R_perso['honneur'] = $row['honneur'];
				$R_perso['level'] = $row['level'];
				$R_perso['rang_royaume'] = $row['rang_royaume'];  // ID du grade.
			
				//Récupération du grade
				$requete = "SELECT nom, rang FROM grade WHERE id = ".$R_perso['rang_royaume'];
				$req = $db->query($requete);
				$row_grade = $db->read_assoc($req);
				$R_perso['grade'] = $row_grade['nom'];  // Nom du grade.
				$R_perso['rang_grade'] = $row_grade['rang'];  //Rang du grade
			
				$R_perso['race'] = $row['race'];
				$R_perso['classe'] = $row['classe'];
				$R_perso['classe_id'] = $row['classe_id'];
				$R_perso['vie'] = $row['vie'];  // Constitution
				$R_perso['force'] = $row['forcex'];
				$R_perso['dexterite'] = $row['dexterite'];
				$R_perso['puissance'] = $row['puissance'];
				$R_perso['volonte'] = $row['volonte'];
				$R_perso['energie'] = $row['energie'];
				$R_perso['reserve'] = ceil(2.1 * ($row['energie'] + floor(($row['energie'] - 8) / 2)));  // RM
				//bonus vampire
				if($R_perso['race'] == 'vampire')
				{
					$R_perso['reserve'] += 2;
					if(moment_jour() == 'Nuit')
					{
						$R_perso['reserve'] += 3;
						$R_perso['dexterite'] += 2;
						$R_perso['volonte'] += 2;
					}
					//Malus Vampire 2
					elseif(moment_jour() == 'Journee')
					{
						$R_perso['reserve'] -= 1;
						$R_perso['dexterite'] -= 1;
						$R_perso['volonte'] -= 1;
					}
				}
				//Bonus Haut Elfe
				if($R_perso['race'] == 'elfehaut' AND moment_jour() == 'Nuit')
				{
					$R_perso['reserve'] += 2;
					$R_perso['dexterite'] += 1;
					$R_perso['volonte'] += 1;
				}
				$R_perso['pa'] = $row['pa'];
				$R_perso['action_a'] = $row['action_a'];  // Script d'attaque.
				$R_perso['action_d'] = $row['action_d'];  // Script de défense.
				if($row['action_d'] == 0) $row['action_d'] = $row['action_a'];
				$R_perso['dernieraction'] = $row['dernieraction'];  // Moment où a eu lieu le dernier gain de PA.
				$R_perso['dernier_connexion'] = $row['dernier_connexion'];
				$R_perso['regen_hp'] = $row['regen_hp'];  // Date et heure de la dernière régénération de HP et MP.
				$R_perso['maj_hp'] = $row['maj_hp'];  // Moment de la dernière augmentation de HP.
				$R_perso['maj_mp'] = $row['maj_mp'];  // Moment de la dernière augmentation de MP.
				$R_perso['point_sso'] = $row['point_sso'];  // Points shine.
				$R_perso['star'] = $row['star'];
				$R_perso['groupe'] = $row['groupe'];  // ID du groupe (0 s'il n'en a pas).
				$R_perso['x'] = $row['x'];  // Position
				$R_perso['y'] = $row['y'];  // Position
				$R_perso['hp'] = $row['hp'];  // HP actuels
				$R_perso['hp_max_1'] = $row['hp_max'];  // HP max réels (non entiers pour les augmentations)
				$R_perso['hp_max'] = floor($row['hp_max']);  // HP max pris en compte
				$R_perso['mp'] = $row['mp'];  // MP actuels
				$R_perso['mp_max_1'] = $row['mp_max'];  // MP max réels (non entiers pour les augmentations)
				$R_perso['mp_max'] = floor($row['mp_max']);  // MP max pris en compte
				// Compétences
				$R_perso['melee'] = $row['melee'];
				$R_perso['distance'] = $row['distance'];
				$R_perso['esquive'] = $row['esquive'];
				$R_perso['blocage'] = $row['blocage'];
				$R_perso['incantation'] = $row['incantation'];
				$R_perso['sort_vie'] = $row['sort_vie'];
				$R_perso['sort_element'] = $row['sort_element'];
				$R_perso['sort_mort'] = $row['sort_mort'];
				$R_perso['identification'] = $row['identification'];
				$R_perso['forge'] = $row['forge'];
				$R_perso['alchimie'] = $row['alchimie'];
				$R_perso['architecture'] = $row['architecture'];
				$R_perso['craft'] = $row['craft'];
				$R_perso['artisanat'] = round(sqrt($R_perso['architecture'] + $R_perso['forge'] + $R_perso['alchimie']));
				$R_perso['survie'] = $row['survie'];
				$R_perso['facteur_magie'] = $row['facteur_magie'];  // Facteur multiplicateur, dépendant de la classe, des coûts et pré-requis pour les sorts. 
				$R_perso['facteur_sort_vie'] = $row['facteur_sort_vie'];  // Inutilisé.
				$R_perso['facteur_sort_element'] = $row['facteur_sort_element'];  // Inutilisé.
				$R_perso['facteur_sort_mort'] = $row['facteur_sort_mort'];  // Inutilisé.
				$R_perso['resistmagique'] = $row['resistmagique'];  // Inutilisé.
				$R_perso['frag'] = $row['frag'];
				$R_perso['mort'] = $row['mort'];
				$R_perso['crime'] = $row['crime'];
				$R_perso['teleport_roi'] = $row['teleport_roi'];  // Indique si la téléportation du roi a été utilisé ce mois-ci (booléen).
				$R_perso['statut'] = $row['statut'];  // Statut du personnage : 'actif', 'inactif', 'hibern' ou 'ban'.
				$R_perso['cache_classe'] = $row['cache_classe'];  // 0 si on ne cache pas sa classe, 1 si on la cache aux autre races et 1 si on la cache à tout le monde.
				$R_perso['cache_stat'] = $row['cache_stat'];  // 0 si on ne cache pas ses stats, 1 si on les cache aux autre races et 1 si on les cache à tout le monde.
				// Bonus additif de PM des Nains
				if($R_perso['race'] == 'nain') $R_perso['PM'] = 10;
				else $R_perso['PM'] = 1;
				// Bonus additif de PP des Barbares
				if($R_perso['race'] == 'barbare') $R_perso['PP'] = 10;
				else $R_perso['PP'] = 0;
			
				//Récupération des autres compétences (maîtrises, survies, ...)
				$R_perso['competences'] = array();
				$requete = "SELECT * FROM comp_perso WHERE id_perso = ".$R_perso['ID'];
				$req = $db->query($requete);
				while($row_c = $db->read_assoc($req))
				{
					$R_perso['competences'][$row_c['competence']] = $row_c['valeur'];
				}
				$R_perso['sort_jeu'] = $row['sort_jeu'];  // Sorts hors combat.
				$R_perso['sort_combat'] = $row['sort_combat'];  // Sorts de combat.
				$R_perso['comp_combat'] = $row['comp_combat'];  // Compétences de combat.
				$R_perso['comp_jeu'] = $row['comp_jeu'];  // Compétences hors combat.
				$R_perso['quete'] = unserialize($row['quete']);
				$R_perso['quete_fini'] = $row['quete_fini'];
				$R_perso['inventaire_slot'] = unserialize($row['inventaire_slot']);
				$R_perso['inventaire'] = unserialize($row['inventaire']);
				$R_perso['arme'] = $R_perso['inventaire']->main_droite;
				$R_perso['enchantement'] = array();  // Enchantement de gemme
				$R_perso['objet_effet'] = array();  // Effets magiques des objets
				$objet_effet_id = 0;
				
				//Main droite
				if ($R_perso['arme'] != '')
				{
					$arme_d = decompose_objet($R_perso['arme']);
					//print_r($arme_d);
					$requete = "SELECT nom, type, degat, distance_tir, var1, effet FROM arme WHERE id = ".$arme_d['id_objet'];
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$R_perso['arme_nom'] = $row['nom'];
					$R_perso['arme_type'] = $row['type'];
					$R_perso['arme_degat'] = $row['degat'];
					$R_perso['arme_distance'] = $row['distance_tir']; // Distance à laquelle peut toucher l'arme
					$R_perso['arme_var1'] = $row['var1'];  // Bonus de potentiel des bâtons ou malus d'esquive des autres armes.
					// Effets magiques
					if($row['effet'] != '')
					{
						$effet = explode(';', $row['effet']);
						foreach($effet as $eff)
						{
							$explode = explode('-', $eff);
							$R_perso['objet_effet'][$objet_effet_id]['id'] = $explode[0];
							$R_perso['objet_effet'][$objet_effet_id]['effet'] = $explode[1];
							$objet_effet_id++;
						}
					}
					// Gemmes
					if($arme_d['enchantement'] > 0)
					{
						$R_perso = enchant($arme_d['enchantement'], $R_perso);
					}
					//echo $R_perso['arme_degat'];
					$R_perso['arme_droite'] = $R_perso['arme_degat'];
				}
				else
				{
					$R_perso['arme_type'] = '';
					$R_perso['arme_degat'] = 0;
				}
				
				//Main gauche
				if ($R_perso['inventaire']->main_gauche != '' AND $R_perso['inventaire']->main_gauche != 'lock')
				{
					$gauche_d = decompose_objet($R_perso['inventaire']->main_gauche);
					$requete = "SELECT nom, type, degat, var1, effet FROM arme WHERE id = ".$gauche_d['id_objet'];
					$req = $db->query($requete);
					$row = $db->read_array($req);
					if($row['type'] == 'bouclier')
					{
						$R_perso['bouclier'] = true;
						$R_perso['bouclier_type'] = 'bouclier';
						$R_perso['bouclier_nom'] = $row['nom'];
						$R_perso['bouclier_degat'] = $row['degat'];
						$R_perso['bouclier_var1'] = $row['var1'];  // Inutilisé.
					}
					elseif($row['type'] == 'dague')
					{
						$R_perso['bouclier'] = false;
						$R_perso['bouclier_type'] = '';
						$R_perso['arme_gauche'] = $row['degat'];
					}
					else
					{
						$R_perso['bouclier'] = false;
						$R_perso['bouclier_type'] = '';
					}
					// Gemmes
					if($gauche_d['enchantement'] > 0)
					{
						$R_perso = enchant($gauche_d['enchantement'], $R_perso);
					}
					// Effets magiques
					if($row['effet'] != '')
					{
						$effet = explode(';', $row['effet']);
						foreach($effet as $eff)
						{
							$explode = explode('-', $eff);
							$R_perso['objet_effet'][$objet_effet_id]['id'] = $explode[0];
							$R_perso['objet_effet'][$objet_effet_id]['effet'] = $explode[1];
							$objet_effet_id++;
						}
					}
					$R_perso['arme_degat'] += $R_perso['arme_gauche'];
				}
				else
				{
					$R_perso['bouclier'] = false;
				}
				
				// Pièces d'armure
				$partie_armure = array('tete', 'torse', 'main', 'ceinture', 'jambe', 'chaussure', 'dos', 'cou', 'doigt');
				foreach($partie_armure as $partie)
				{
					if($partie != '')
					{
						$partie_d = decompose_objet($R_perso['inventaire']->$partie);
						if($partie_d['id_objet'] != '')
						{
							$requete = "SELECT PP, PM, effet FROM armure WHERE ID = ".$partie_d['id_objet'];
							$req = $db->query($requete);
							$row = $db->read_row($req);
							$R_perso['PP'] += $row[0];
							$R_perso['PM'] += $row[1];
					    // Effets magiques
							$effet = explode(';', $row[2]);
							foreach($effet as $eff)
							{
								$explode = explode('-', $eff);
								$R_perso['objet_effet'][$objet_effet_id]['id'] = $explode[0];
								$R_perso['objet_effet'][$objet_effet_id]['effet'] = $explode[1];
							}
							$objet_effet_id++;
						}
					  // Gemmes
						if($partie_d['enchantement'] > 0)
						{
							$R_perso = enchant($partie_d['enchantement'], $R_perso);
						}
					}
				}

				//Accessoire
				$R_perso['accessoire']['id'] = $R_perso['inventaire']->accessoire;
				if($R_perso['accessoire']['id'] !== 0)
				{
					$partie_d = decompose_objet($R_perso['accessoire']['id']);
					if($partie_d['id_objet'] != '')
					{
						$requete = "SELECT type, effet FROM accessoire WHERE ID = ".$partie_d['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_row($req);
						$R_perso['accessoire']['type'] = $row[0];
						$R_perso['accessoire']['effet'] = $row[1];
						if($row[0] == 'rm') $R_perso['reserve'] += $row[1];
					}
					// Gemmes
					if($partie_d['enchantement'] > 0)
					{
						$R_perso = enchant($partie_d['enchantement'], $R_perso);
					}
				}

				// Effets des objets magiques
				foreach($R_perso['objet_effet'] as $effet)
				{
					switch($effet['id'])
					{
						case '4' :
							$R_perso['PM'] += $effet['effet'];
						break;
						case '5' :
							$R_perso['volonte'] += $effet['effet'];
						break;
						case '7' :
							$R_perso['dexterite'] += $effet['effet'];
						break;
					}
				}
				
				//Chiffre de base gardés à partir d'ici
				$R_perso['PM_base'] = $R_perso['PM'];
				$R_perso['PP_base'] = $R_perso['PP'];
				$R_perso['reserve_base'] = $R_perso['reserve'];

				// Bonus raciaux multipilcatif de PM & PP
				if($R_perso['race'] == 'nain') $R_perso['PM'] = round($R_perso['PM'] * 1.1);
				if($R_perso['race'] == 'scavenger') $R_perso['PM'] = round($R_perso['PM'] * 1.05);
				if($R_perso['race'] == 'scavenger') $R_perso['PP'] = round($R_perso['PP'] * 1.15);
				if($R_perso['race'] == 'barbare') $R_perso['PP'] = round($R_perso['PP'] * 1.3);
				// Mort vivant
				if($R_perso['race'] == 'mortvivant' AND moment_jour() == 'Soir')
				{
					$R_perso['PP'] = round($R_perso['PP'] * 1.15);
					$R_perso['PM'] = round($R_perso['PM'] * 1.15);
				}

				//Récupération des buffs
				$R_perso['buff'] = array();
				$R_perso['debuff'] = array();
				$requete = "SELECT * FROM buff WHERE id_perso = ".$R_perso['ID']." ORDER BY debuff ASC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					if($row['debuff'] == 1) $col = 'debuff'; else $col = 'buff';
					$R_perso[$col][$row['type']] = $row;
				}
				//Buffs
				//print_r($R_perso['buff']);
				if(array_key_exists('buff_bouclier', $R_perso['buff'])) $R_perso['PP'] = round($R_perso['PP'] * (1 + ($R_perso['buff']['buff_bouclier']['effet'] / 100)));
				if(array_key_exists('buff_barriere', $R_perso['buff'])) $R_perso['PM'] = round($R_perso['PM'] * (1 + ($R_perso['buff']['buff_barriere']['effet'] / 100)));
				if(array_key_exists('buff_inspiration', $R_perso['buff'])) $R_perso['reserve'] = $R_perso['reserve'] + $R_perso['buff']['buff_inspiration']['effet'];
				if(array_key_exists('buff_sacrifice', $R_perso['buff'])) $R_perso['reserve'] += $R_perso['buff']['buff_sacrifice']['effet'];
				if(array_key_exists('longue_portee', $R_perso['buff']) AND $R_perso['arme_type'] == 'arc') $R_perso['arme_distance'] += 1;
				if(array_key_exists('buff_forteresse', $R_perso['buff']))
				{
					$R_perso['PP'] = round($R_perso['PP'] * (1 + (($R_perso['buff']['buff_forteresse']['effet']) / 100)));
					$R_perso['PM'] = round($R_perso['PM'] * (1 + (($R_perso['buff']['buff_forteresse']['effet2']) / 100)));
				}
				if(array_key_exists('buff_cri_protecteur', $R_perso['buff'])) $R_perso['PP'] = round($R_perso['PP'] * (1 + ($R_perso['buff']['buff_cri_protecteur']['effet'] / 100)));
				if(array_key_exists('maladie_degenerescence', $R_perso['debuff'])) $R_perso['reserve'] = ceil($R_perso['reserve'] / (1 + ($R_perso['debuff']['maladie_degenerescence']['effet'] / 100)));
				if(array_key_exists('debuff_desespoir', $R_perso['debuff'])) $R_perso['PM'] = round($R_perso['PM'] / (1 + (($R_perso['debuff']['debuff_desespoir']['effet']) / 100)));
				if(array_key_exists('famine', $R_perso['debuff']))
				{
					$R_perso['hp_max'] = round($R_perso['hp_max'] - ((($R_perso['debuff']['famine']['effet'] * $R_perso['hp_max']) / 100)));
					$R_perso['mp_max'] = round($R_perso['mp_max'] - ((($R_perso['debuff']['famine']['effet'] * $R_perso['mp_max']) / 100)));
				}
				// Calcul des coefficients
				$R_perso['coef_melee'] = $R_perso['force'] * $R_perso['melee'];
				$R_perso['coef_incantation'] = $R_perso['puissance'] * $R_perso['incantation'];
				$R_perso['coef_distance'] =  round(($R_perso['force'] + $R_perso['dexterite']) / 2) * $R_perso['distance'];
				$R_perso['coef_blocage'] =  round(($R_perso['force'] + $R_perso['dexterite']) / 2) * $R_perso['blocage'];
				//Maladie suppr_defense
				if(array_key_exists('suppr_defense', $R_perso['debuff'])) $joueur['PP'] = 0;
				return $R_perso;
			}
			else
			{
				return false;
			}
		}
		else
		{
			echo 'Vous êtes déconnectés, veuillez vous reconnecter.';
			exit();
		}
	}
}

/**
 * Récupère l'ID de la lignée du personnage.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   ID de la lignée.
 */
function recupperso_lignee($id_perso)
{
	global $db;
	$requete = "SELECT id_lignee FROM lignee_perso WHERE id_perso = ".$id_perso;
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_array($req);
		return $row[0];
	}
	else
	{
		return 0;
	}
}

/**
 * Récupère les options.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   Options.
 */
function recup_option($id_perso)
{
	global $db;
	$requete = "SELECT valeur, nom FROM options WHERE id_perso = ".$id_perso;
	$req = $db->query($requete);
	$options = array();
	while($row = $db->read_assoc($req))
	{
		$options[$row['nom']] = $row['valeur'];
	}
	return $options;
}

/**
 * Récupère une lignée.
 * 
 * @param $id_perso   ID de la lignée.
 *  
 * @return   Lignée sous forme de tableau associatif.
 */
function recup_lignee($id_lignee)
{
	global $db;
	$requete = "SELECT * FROM lignee WHERE id = ".$id_lignee;
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_assoc($req);
		return $row;
	}
	else
	{
		return false;
	}
}

/**
 * Récupère une amande.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   Amande sous forme de tableau associatif ou false s'il n'y en a pas.
 */
function recup_amende($id_perso)
{
	global $db;
	$requete = "SELECT * FROM amende WHERE id_joueur = ".$id_perso;
	$req = $db->query($requete);
	if($db->num_rows > 0) $amende = $db->read_assoc($req);
	else $amende = false;
	return $amende;
}

/**
 * Récupère les titres honorifiques d'un personnages.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   Titres sous forme de tableau de tableaux associatif.
 */
function recup_titre_honorifique($id_perso)
{
	global $db;
	$titres = array();
	$requete = "SELECT * FROM titre_honorifique WHERE id_perso = ".$id_perso;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$titres[] = $row['titre'];
	}
	return $titres;
}

/**
 * Récupère les effets des bonus shine d'un personnages.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   Etats dû aux bonus sous forme de tableau indexé par les IDs des bonus.
 */
function recup_bonus($id_perso)
{
	global $db;
	$requete = "SELECT * FROM bonus_perso WHERE id_perso = ".$id_perso;
	$req = $db->query($requete);
	$bonus = array();
	while($row = $db->read_assoc($req))
	{
		$bonus[$row['id_bonus']] = $row['etat'];
	}
	return $bonus;
}

/**
 * Récupère toutes les infos des bonus shine d'un personnages.
 * 
 * @param $id_perso   ID du perso.
 *  
 * @return   Bonus sous forme de tableau de tableaux associatif.
 */
function recup_bonus_total($id_perso)
{
	global $db;
	$requete = "SELECT * FROM bonus_perso WHERE id_perso = ".$id_perso;
	$req = $db->query($requete);
	$bonus = array();
	while($row = $db->read_assoc($req))
	{
		$bonus[$row['id_bonus']] = $row;
	}
	return $bonus;
}

/**
 * Vérifie si on affiche les informations qui peuvent être caché par un bonus shine.
 * 
 * @param $bonus    Type de bonus.
 * @param $joueur   Tableau décrivant le personnage du joueur. 
 * @param $perso    Tableau décrivant le personnage concerné par les informations.
 * 
 * @return    true si on affiche, false sinon. 
 */ 
function check_affiche_bonus($bonus, $joueur, $perso)
{
	switch($bonus)
	{
		case 0 :
			return true;
		break;
		case 1 :
			if($joueur['race'] == $perso['race']) return true;
			else return false;
		break;
		case 2 :
			if($joueur['ID'] == $perso['ID']) return true;
			else return false;
		break;
	}
	return false;
}

/**
 * Ajoute un bonus shine à un personnage.
 * 
 * @param $bonus      ID du bonus.
 * @param $id_perso   ID du personnage. 
 * 
 * @return    true si ça a réussi, false sinon. 
 */ 
function ajout_bonus($id_bonus, $id_perso)
{
	global $db;
	$requete = "INSERT INTO bonus_perso(id_perso, id_bonus) VALUES(".$id_perso.", ".$id_bonus.")";
	if($db->query($requete)) return true;
	else return false;
}

/**
 * Met un jour un personnage.
 * Gère l'augmentation et la régénération des HP & MP, la régénération des PA et supprime
 * les buffs et ban périmés.
 * 
 * @param $joueur     Tableau associatif décrivant le personnage.
 * 
 * @param $joueur     Tableau associatif décrivant le personnage après mise-à-jour.
 */
function check_perso($joueur)
{
	$modif = false;	 // Indique si le personnage a été modifié.
	global $db, $G_temps_regen_hp, $G_temps_maj_hp, $G_temps_maj_mp, $G_temps_PA, $G_PA_max, $G_pourcent_regen_hp, $G_pourcent_regen_mp;
	// On vérifie que le personnage est vivant
	if($joueur['hp'] > 0)
	{
		// On augmente les HP max si nécessaire
		$temps_maj = time() - $joueur['maj_hp']; // Temps écoulé depuis la dernière augmentation de HP.
		$temps_hp = $G_temps_maj_hp;  // Temps entre deux augmentation de HP.
		if ($temps_maj > $temps_hp && $temps_hp > 0) // Pour ne jamais diviser par 0 ...
		{
			$time = time();
			$nb_maj = floor($temps_maj / $temps_hp);
			$hp_gagne = $nb_maj * (sqrt($joueur['vie']) * 2.7);
			$joueur['hp_max_1'] = $joueur['hp_max_1'] + $hp_gagne;
			$joueur['maj_hp'] += $nb_maj * $temps_hp;
			$modif = true;
		}
		// On augmente les MP max si nécessaire
		$temps_maj = time() - $joueur['maj_mp']; // Temps écoulé depuis la dernière augmentation de MP.
		$temps_mp = $G_temps_maj_mp;  // Temps entre deux augmentation de MP.
		if ($temps_maj > $temps_mp)
		{
			$time = time();
			$nb_maj = floor($temps_maj / $temps_mp);
			$mp_gagne = $nb_maj * (($joueur['energie'] - 3) / 4);
			$joueur['mp_max_1'] = $joueur['mp_max_1'] + $mp_gagne;
			$joueur['maj_mp'] += $nb_maj * $temps_mp;
			$modif = true;
		}
		// Régénération des HP et MP
		$temps_regen = time() - $joueur['regen_hp']; // Temps écoulé depuis la dernière régénération.
		if ($temps_regen > $G_temps_regen_hp)
		{
			$time = time();
			$nb_regen = floor($temps_regen / $G_temps_regen_hp);
			$regen_hp = $G_pourcent_regen_hp;
			$regen_mp = $G_pourcent_regen_mp;
			//Buff préparation du camp
			if(array_key_exists('preparation_camp', $joueur['buff']))
			{
				// Le buff a-t-il été lancé après la dernière régénération ?
				if($joueur['buff']['preparation_camp']['effet2'] > $joueur['regen_hp'])
				{
					// On calcule le moment où doit avoir lieu la première régénération après le lancement du buff 
					$regen_cherche = $joueur['regen_hp'] + ($G_temps_regen_hp * floor(($joueur['buff']['preparation_camp']['effet2'] - $joueur['regen_hp']) / $G_temps_regen_hp));
				}
				else $regen_cherche = $joueur['regen_hp'];
				// Le buff s'est-il arrêté entre temps ?
				if($joueur['buff']['preparation_camp']['fin'] > time()) $fin = time();
				else $fin = $joueur['buff']['preparation_camp']['fin'];
				// On calcule le nombre de régénération pour lesquels le buff doit être pris en compte 
				$nb_regen_avec_buff = floor(($fin - $regen_cherche) / $G_temps_regen_hp);
				//bonus buff du camp
				$bonus_camp = 1 + ((($nb_regen_avec_buff / $nb_regen) * $joueur['buff']['preparation_camp']['effet']) / 100);
				$regen_hp = $regen_hp * $bonus_camp;
				$regen_mp = $regen_mp * $bonus_camp;
			}
			// Bonus raciaux
			if($joueur['race'] == 'troll') $regen_hp = $regen_hp * 1.2;
			if($joueur['race'] == 'elfehaut') $regen_mp = $regen_mp * 1.1;
			// Accessoires
			if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'regen_hp') $bonus_accessoire = $joueur['accessoire']['effet']; else $bonus_accessoire = 0;
			if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'regen_mp') $bonus_accessoire_mp = $joueur['accessoire']['effet']; else $bonus_accessoire_mp = 0;
			// Effets magiques des objets
			foreach($joueur['objet_effet'] as $effet)
			{
				switch($effet['id'])
				{
					case '1' :
						$bonus_accessoire += $effet['effet'];
					break;
					case '10' :
						$bonus_accessoire_mp += $effet['effet'];
					break;
				}
			}
			// Calcul des HP et MP récupérés
			$hp_gagne = $nb_regen * (floor($joueur['hp_max'] * $regen_hp) + $bonus_accessoire);
			$mp_gagne = $nb_regen * (floor($joueur['mp_max'] * $regen_mp) + $bonus_accessoire_mp);
			//DéBuff lente agonie
			if(array_key_exists('lente_agonie', $joueur['debuff']))
			{
			  // Le débuff a-t-il été lancé après la dernière régénération ?
				if($joueur['debuff']['lente_agonie']['effet2'] > $joueur['regen_hp'])
				{
					$regen_cherche = $joueur['regen_hp'] + ($G_temps_regen_hp * floor(($joueur['debuff']['lente_agonie']['effet2'] - $joueur['regen_hp']) / $G_temps_regen_hp));
				}
				else $regen_cherche = $joueur['regen_hp'];
				// Le débuff s'est-il arrêté entre temps ?
				if($joueur['debuff']['lente_agonie']['fin'] > time()) $fin = time();
				else $fin = $joueur['debuff']['lente_agonie']['fin'];
				// On calcule le nombre de régénération pour lesquels le débuff doit être pris en compte 
				$nb_regen_avec_buff = floor(($fin - $regen_cherche) / $G_temps_regen_hp);
				// Calcul du malus
				$malus_agonie = ((1 - ($nb_regen_avec_buff / $nb_regen)) - (($nb_regen_avec_buff / $nb_regen) * $joueur['debuff']['lente_agonie']['effet']));
				$hp_gagne = $hp_gagne * $malus_agonie;
			}
			//Maladie regen negative
			if(array_key_exists('regen_negative', $joueur['debuff']) AND !array_key_exists('lente_agonie', $joueur['debuff']))
			{
				$hp_gagne = $hp_gagne * -1;
				$mp_gagne = $mp_gagne * -1;
				// On diminue le nombre de régénération pendant lesquels la maladie est active ou supprime s'il n'y en  plus
				if($joueur['debuff']['regen_negative']['effet'] > 1)
				{
					$requete = "UPDATE buff SET effet = ".($joueur['debuff']['regen_negative']['effet'] - 1)." WHERE id = ".$joueur['debuff']['regen_negative']['id'];
				}
				else
				{
					$requete = "DELETE FROM buff WHERE id = ".$joueur['debuff']['regen_negative']['id'];
				}
				$db->query($requete);
			}
			//Maladie high regen
			if(array_key_exists('high_regen', $joueur['debuff']))
			{
				$hp_gagne = $hp_gagne * 3;
				$mp_gagne = $mp_gagne * 3;
				// On diminue le nombre de régénération pendant lesquels la maladie est active ou supprime s'il n'y en  plus
				if($joueur['debuff']['high_regen']['effet'] > 1)
				{
					$requete = "UPDATE buff SET effet = ".($joueur['debuff']['high_regen']['effet'] - 1)." WHERE id = ".$joueur['debuff']['high_regen']['id'];
				}
				else
				{
					$requete = "DELETE FROM buff WHERE id = ".$joueur['debuff']['high_regen']['id'];
				}
				$db->query($requete);
			}
			//Maladie mort_regen
			if(array_key_exists('high_regen', $joueur['debuff']) AND $hp_gagne != 0 AND $mp_gagne != 0)
			{
				$hp_gagne = $joueur['hp'];
			}
			// Mise à jour des HP
			$joueur['hp'] = $joueur['hp'] + $hp_gagne;
			if ($joueur['hp'] > $joueur['hp_max']) $joueur['hp'] = floor($joueur['hp_max']);
			// Mise à jour des MP
			$joueur['mp'] = $joueur['mp'] + $mp_gagne;
			if ($joueur['mp'] > $joueur['mp_max']) $joueur['mp'] = floor($joueur['mp_max']);
			$joueur['regen_hp'] = $joueur['regen_hp'] + ($nb_regen * $G_temps_regen_hp);
			$modif = true;
		}
		//Calcul des PA du joueur
		$time = time();
		$temps_pa = $G_temps_PA;
		// Nombre de PA à ajouter 
		$panew = floor(($time - $joueur['dernieraction']) / $temps_pa);
		$prochain = ($joueur['dernieraction'] + $temps_pa) - $time;
		if ($prochain < 0) $prochain = 0;
		// Mise à jour des PA
		$joueur['pa'] = $joueur['pa'] + $panew;
		if ($joueur['pa'] > $G_PA_max) $joueur['pa'] = $G_PA_max;
		// Calcul du moment où a eu lieu le dernier gain de PA
		$j_d_a = (floor($time / $temps_pa)) * $temps_pa;
		if($j_d_a > $joueur['dernieraction']) $joueur['dernieraction'] = $j_d_a;
		$modif = true;
		
		// Mise-à-jour du personnage dans la base de donnée s'il y a eut modificaton	
		if ($modif)
		{
			$requete = "UPDATE perso SET regen_hp = '".$joueur['regen_hp']."', maj_mp = '".$joueur['maj_mp']."', maj_hp = '".$joueur['maj_hp']."', hp = '".$joueur['hp']."', hp_max = '".$joueur['hp_max_1']."', mp = '".$joueur['mp']."', mp_max = '".$joueur['mp_max_1']."', pa = '".$joueur['pa']."', dernieraction = '".$joueur['dernieraction']."' WHERE ID = '".$joueur['ID']."'";
			$req = $db->query($requete);
		}
	} // if($joueur['hp'] > 0)
	// On supprime tous les buffs périmés
	$requete = "DELETE FROM buff WHERE fin <= ".time();
	$req = $db->query($requete);
	// On enlève le ban s'il y en a un et qu'il est fini
	$requete = "UPDATE perso SET statut = 'actif' WHERE statut = 'ban' AND fin_ban <= ".time();
	$db->query($requete);

	return $joueur;
}

/**
 * Vérifie les modifications de la case.
 * 
 * @param $coord    Coordonnées de la case sous forme de tableau associatif ou 'all'.  
 */ 
function check_case($coord)
{
	global $db, $Gtrad;
	// Toutes les cases ou seulement une en particulier ?
	if($coord == 'all')
	{
		$where = '1';
	}
	else $where = '(x = '.$coord['x'].') AND (y = '.$coord['y'].')';
	// Recherche des constructions terminées
	$requete = "SELECT * FROM placement WHERE ".$where." AND fin_placement <= ".time();
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		//echo time().' > '.$row['fin_placement'];
		//Si c'est un drapeau, on transforme le royaume
		if($row['type'] == 'drapeau')
		{
			$pos = convert_in_pos($row['x'], $row['y']);
			//Mis à jour de la carte
			$requete = "UPDATE map SET royaume = ".$row['royaume']." WHERE ID = ".$pos;
			//echo $requete;
			$db->query($requete);
			//Suppression du drapeau
			$requete = "DELETE FROM placement WHERE id = ".$row['id'];
			$db->query($requete);
		}
		//Si c'est un bâtiment ou une arme de siège, on le construit
		elseif($row['type'] == 'fort' OR $row['type'] == 'tour' OR $row['type'] == 'bourg' OR $row['type'] == 'mur' OR $row['type'] == 'arme_de_siege' OR $row['type'] == 'mine')
		{
			$rechargement = time();
			//Cas spécifique des mines
			if($row['type'] == 'mine')
			{
				$rechargement = $row['rez'];
				$row['rez'] = 0;
			}
			//Insertion de la construction
			$requete = "INSERT INTO construction VALUES('', ".$row['id_batiment'].", ".$row['x'].", ".$row['y'].", ".$row['royaume'].", ".$row['hp'].", '".$row['nom']."', '".$row['type']."', ".$row['rez'].", ".$rechargement.", '".$Gtrad[$row['nom']]."')";
			//echo $requete;
			$db->query($requete);
			//Suppression du fort
			$requete = "DELETE FROM placement WHERE id = ".$row['id'];
			$db->query($requete);
		}
	}
}

/**
 * Récupère les informations sur un monstre.
 * 
 * @param $ID             ID d'un mosntre présent sur la carte ou type du monstre.
 * @param $map_monstre    true si on veut voir un monstre présent sur la carte, false si le monstre est créé.
 * 
 * @return    Informations sur le monstre sous forme de tableau associatif.
 */
function recupmonstre($ID, $map_monstre = true)
{
	global $db;
	if($map_monstre)
	{
		$requete = 'SELECT type, hp, id FROM map_monstre WHERE ID = '.$ID;
		$req = $db->query($requete);	
		$row = $db->read_array($req);
		$R_monstre['hp'] = $row['hp'];
		$R_monstre['hp_max'] = $row['hp'];
		$R_monstre['type'] = $row['type'];
		$R_monstre['id'] = $row['id'];
	}
	else
	{
		$row['type'] = $ID;
	}
	
	$requete = 'SELECT * FROM monstre WHERE id = '.$row['type'];
	$req = $db->query($requete);	
	$row = $db->read_array($req);
	$R_monstre['nom'] = $row['nom'];
	$R_monstre['level'] = $row['level'];
	$R_monstre['force'] = $row['forcex'];
	$R_monstre['vie'] = 15;  // Constitution
	$R_monstre['dexterite'] = $row['dexterite'];
	$R_monstre['puissance'] = $row['puissance'];
	$R_monstre['volonte'] = $row['volonte'];
	$R_monstre['reserve'] = ceil(2.1 * ($row['energie'] + floor(($row['energie'] - 8) / 2)));
	$R_monstre['melee'] = $row['melee'];
	$R_monstre['classe_id'] = 4;  // ID de la classe du monstre.
	$R_monstre['esquive'] = $row['esquive'];
	$R_monstre['incantation'] = $row['incantation'];
	$R_monstre['sort_vie'] = $row['sort_vie'];
	$R_monstre['sort_element'] = $row['sort_element'];
	$R_monstre['sort_mort'] = $row['sort_mort'];
	$R_monstre['PP'] = $row['pp'];
	$R_monstre['PM'] = $row['pm'];
	$R_monstre['hp_max_1'] = $row['hp'];
	$R_monstre['bouclier'] = false;  // Indique si le monstre a un bouclier.
	$R_monstre['competences'] = array();
	$R_monstre['race'] = 'scavenger';
	$R_monstre['description'] = $row['description'];
	$R_monstre['image'] = $row['lib'].'.png';
	if($row['arme'] != '') $R_monstre['arme_type'] = $row['arme'];
	else $R_monstre['arme_type'] = 'epee';
	if($row['arme'] == 'arc') $R_monstre['distance'] = $row['melee'];
	$R_monstre['buff'] = array();
	$R_monstre['action_d'] = $row['action'];  // Script défensif.
	$R_monstre['enchantement'] = array();
	$R_monstre['espece'] = $row['type'];
	// On supprime les buffs périmés
	$requete = "DELETE FROM buff_monstre WHERE fin <= ".time();
	$req = $db->query($requete);

	//Récupération des buffs
	$R_monstre['buff'] = array();
	$R_monstre['debuff'] = array();
	$requete = "SELECT * FROM buff_monstre WHERE id_monstre = ".$ID;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		if($row['debuff'] == 1) $col = 'debuff'; else $col = 'buff';
		$R_monstre[$col][$row['type']] = $row;
	}
	// Application de certains buffs
	if(array_key_exists('maladie_degenerescence', $R_monstre['debuff'])) $R_monstre['reserve'] = ceil($R_monstre['reserve'] / $R_monstre['debuff']['maladie_degenerescence']['effet']);
	if(array_key_exists('debuff_desespoir', $R_monstre['debuff'])) $R_monstre['PM'] = round($R_monstre['PM'] / (1 + (($R_monstre['debuff']['debuff_desespoir']['effet']) / 100)));
	$R_monstre['objet_effet'] = array();
	
	return $R_monstre;
}

/**
 * Récupère les informations sur un bâtiment.
 * Les informations peuvent être réparties dans deux bases, "batiment" pour tous
 * et "placement" pour les bâtiments en constructions ou "rechargement" pour ceux
 * qui ont u temps de rechargment (armes de siège). 
 * 
 * @param $ID       ID du bâtiment.
 * @param $table    Table complémentaire de la base de donnée dans laquelle est le bâtiment, 'none' s'il n'y en a pas.
 * 
 * @return    Informations sur le bâtiment sous forme de tableau associatif.
 */
function recupbatiment($ID, $table)
{
	global $db;
	if($table == 'none')
	{
		$row_p['id_batiment'] = $ID;
	}
	else
	{
	  // Champs à récupérer dans la base de donnée en fonction de la table
		if($table == 'placement') $champ = ', fin_placement, x, y, royaume, debut_placement';
		else $champ = ', rechargement';
		// Récupération des informations dépendant de la table
		$requete = 'SELECT id_batiment, hp, type '.$champ.' FROM '.$table.' WHERE id = '.$ID;
		$req = $db->query($requete);	
		$row_p = $db->read_array($req);
		$R_monstre['hp'] = $row_p['hp'];
		$R_monstre['type'] = $row_p['type'];
		$R_monstre['ID'] = $row_p['id_batiment'];
	}
	$requete = 'SELECT * FROM batiment WHERE id = '.$row_p['id_batiment'];
	$req = $db->query($requete);	
	$row = $db->read_array($req);
	if($table == 'construction')
	{
		$R_monstre['rechargement'] = $row_p['rechargement'];  // Heure à partir de laquelle une arme de siège peut tirer.
		$coeff = 1;
	}
	elseif($table == 'placement')
	{
		$requete = "SELECT race FROM royaume WHERE id = ".$row_p['royaume'];
		$req = $db->query($requete);
		$row_b = $db->read_array($req);
		$distance = calcul_distance(convert_in_pos($Trace[$row_r['race']]['spawn_x'], $Trace[$row_r['race']]['spawn_y']), convert_in_pos($row_p['x'], $row_p['y']));
		$temps_total = $row_p['fin_placement'] - $row_p['debut_placement'];
		$temps_restant = time() - $row_p['debut_placement'];
		$coeff = $temps_restant / $temps_total;
	}

	$R_monstre['id'] = $row['id'];
	$R_monstre['hp_max'] = $row['hp'];
	$R_monstre['hp_max_1'] = $row['hp'];
	$R_monstre['nom'] = $row['nom'];
	$R_monstre['level'] = 0;
	$R_monstre['cout'] = $row['cout'];  // Coût du bâtiment à la construction.
	$R_monstre['force'] = ceil($coeff * $row['carac']);
	$R_monstre['arme_degat'] = $row['bonus1'];  // Facteur de dégâts contre les bâtiments.
	$R_monstre['arme_degat2'] = $row['bonus2'];  // Facteur de dégâts contre les armes de sièges.
	$R_monstre['reload'] = $row['bonus3'];  // Temps entre deux tirs.
	$R_monstre['dexterite'] = ceil($coeff * $row['carac']);
	$R_monstre['puissance'] = ceil($coeff * $row['carac']);
	$R_monstre['volonte'] = ceil($coeff * $row['carac']);
	$R_monstre['classe_id'] = 4;  // ID de la classe du bâtiment.
	if($row['type'] == 'arme_de_siege') $facteur = 40;
	else $facteur = 100;
	$R_monstre['esquive'] = $facteur * ceil($coeff * $row['carac']);
	$R_monstre['PP'] = $row['PP'];
	$R_monstre['PM'] = $row['PM'];
	$R_monstre['augmentation_pa'] = $row['augmentation_pa'];  // Facteur multiplicateur augmentant les PA des déplacement sur le bâtiment.
	$R_monstre['bouclier'] = false;
	$R_monstre['competences'] = array();
	$R_monstre['buff'] = array();
	$R_monstre['debuff'] = array();
	$R_monstre['enchantement'] = array();
	$R_monstre['objet_effet'] = array();
	$R_monstre['melee'] = $row['bonus5'];

	return $R_monstre;
}

/**
 * Donne le bâtiment situé sur une certaine case.
 * 
 * @param $coordx   Coordonnée x de la case.
 * @param $coordy   Coordonnée y de la case.
 * 
 * @return  ID du bâtiment s'il en a un, false sinon.
 */
function batiment_map($coordx, $coordy)
{
	global $db;
	$coords = convert_in_pos($coordx, $coordy);
	$requete = "SELECT id_batiment FROM construction WHERE x = ".$coordx." AND y = ".$coordy;
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_row($req);
		return recupbatiment($row[0], 'none');
	}
	else
	{
		return false;
	}
}

/**
 * Récupère les information sur une gemme
 * 
 * @param $objet  Chaine contenant le type de l'objet en premier caractère et ensuite son ID.
 * 
 * @return    Informations sur la gemme sous forme de tableau associatif. 
 */
function recupobjet($objet)
{
	global $db;
	$type = mb_substr($objet, 0, 1);
	$id = mb_substr($objet, 1);
	$return = array();
	switch($type)
	{
	  // ?
		case 'g' :
			$requete = "SELECT * FROM gemme WHERE id = ".$id;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$return['nom'] = $row['nom'];
			$return['type'] = $row['type'];
			$return['niveau'] = $row['niveau'];
		break;
	  // ?
		case 'h' :
			$requete = "SELECT * FROM gemme WHERE id = ".$id;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$return['nom'] = $row['nom'];
			$return['type'] = $row['type'];
			$return['niveau'] = $row['niveau'];
		break;
	}
	return $return;
}

/**
 * Récupère un script.
 * 
 * @param $action   ID du script.
 * 
 * @return  Contenu du script sous forme de chaine de caractère.    
 */
function recupaction($action)
{
	global $db;
	$requete = "SELECT action FROM action_perso WHERE id = ".$action;
	$req = $db->query($requete);
	$row = $db->read_row($req);
	return $row[0];
}

/**
 * Récupère toutes les informations à propos d'un script.
 * 
 * @param $action   ID du script.
 * 
 * @return  Informations sur le script sous forme de tableau associatif.    
 */
function recupaction_all($action)
{
	global $db;
	$requete = "SELECT * FROM action_perso WHERE id = ".$action;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	return $row;
}

/**
 * Récupère le maximum atteignable d'une compétence pour une classe.
 * 
 * @param  $competence    Nom interne de la compétence.
 * @param  $classe        ID de la classe.
 * 
 * @return Maximum atteignable.
 */
function recup_max_comp($competence, $classe)
{
	global $db, $Tmaxcomp;
	//Récupère les limitations de la classe
	$requete = "SELECT * FROM classe_permet WHERE id_classe = ".$classe." AND competence = '".$competence."'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($db->num_rows > 0)
	{
		$max = $row['permet'];
	}
	else
	{
		$max = $Tmaxcomp[$competence];
	}
	return $max;
}

/**
 * Calcul du facteur de difficulté pour augmenter une compétence liée aux sorts.
 * 
 * @param  $difficulte    Difficulté du sort.
 * @param  $joueur        Informations sur le personnage sous forme de tableau associatif.
 * @param  $type          Compétence pour laquelle on veut connaitre la difficulté. 
 * @param  $sortpa        Coût en PA du sort.
 * @param  $sortmp        Coût en MP du sort.
 * 
 * @return    Difficulté.  
 */
function diff_sort($difficulte, $joueur, $type, $sortpa, $sortmp)
{
	if($type == 'incantation')
	{
		$facteur1 = 2;
		$facteur2 = 1;
	}
	else
	{
		$facteur1 = 4;
		$facteur2 = 1.5;
	}
	$difficulte = $difficulte / $facteur1;
	$pamp = 7 / sqrt($sortpa * $sortmp);
	$total = ($facteur2 * $pamp * sqrt($joueur[$type] / $difficulte));
	//echo $total.'<br />';
	return $total;
}

//Fonction permetter de savoir si lors d'une action la compétence augmente de 1, et retourne la nouvelle valeur de la compétence.
//Plus le chiffre de difficulte est fort, plus il est difficile de l'apprendre
function augmentation_competence($competence, $joueur, $difficulte)
{
	global $db, $Tmaxcomp, $G_apprentissage_rate, $debugs;
	//Récupère les limitations de la classe
	$requete = "SELECT * FROM classe_permet WHERE id_classe = ".$joueur['classe_id']." AND competence = '".$competence."'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($db->num_rows > 0)
	{
		$max = $row['permet'];
	}
	else
	{
		$max = $Tmaxcomp[$competence];
	}
	echo '
	<div id="debug'.$debugs.'" class="debug" style="color : #ff00c0;">
	Maximum de la compétence '.$competence.' = '.$max.'<br />';
	$val_competence = $joueur[$competence];
	echo 'Valeur actuel de la compétence : '.$val_competence.'<br />
	Difficulté : '.$difficulte.'<br />';
	if($val_competence < $max)
	{
		$reussite = ceil(10000 / $G_apprentissage_rate);
		$numero = rand(1, $reussite);
		if($joueur['race'] == 'humain' OR $joueur['race'] == 'humainnoir') $apprentissage = 1.1; else $apprentissage = 1;
		if(in_array('apprenti_vent', $joueur['buff'])) $apprentissage = $apprentissage * (1 + ($joueur['buff']['apprenti_vent']['effet'] / 100));
		if($val_competence > 0) $chance = (10000 * $apprentissage) / (sqrt($val_competence) * $difficulte); else $chance = 0;
		$R_retour[1] = false;
		echo 'Chances : dé de : '.$reussite.' doit être inférieur à '.$chance.' <i>'.($chance * 100 / $reussite).'% de chance</i><br />';
		echo 'Résultat : '.$numero.'<br />';
		//Si le numero est inférieur a chance, alors la compétence augmente d'un
		if($numero < $chance)
		{
			//Augmentation de la compétence
			$R_retour[0] = $val_competence + 1;
			//Indique que la compétence a augmenté
			$R_retour[1] = true;
		}
	}
	echo '</div>';
	$debugs++;
	return $R_retour;
}

//Fonction permettant de calculer les dés de dégat en fonction de la force et de l'arme de la personne
function de_degat($force, $degat_arme)
{
	$tab_de = array();
	$tab_de[0][0] = 2;
	$tab_de[0][1] = 3;
	$tab_de[0][2] = 4;
	$tab_de[0][3] = 5;
	$tab_de[0][4] = 6;
	$tab_de[0][5] = 7;
	$tab_de[0][6] = 11;
	$tab_de[1][0] = 2;
	$tab_de[1][1] = 4;
	$tab_de[1][2] = 6;
	$tab_de[1][3] = 8;
	$tab_de[1][4] = 10;
	$tab_de[1][5] = 12;
	$tab_de[1][6] = 20;
	$potentiel = ceil($force / 3) + $degat_arme;
	$de_degat = array();
	while($potentiel > 1)
	{		
		if (($potentiel > 7) AND ($potentiel < 15))
		{
			$des = array_search(ceil($potentiel / 2), $tab_de[0]);
			$potentiel = $potentiel - $tab_de[0][$des];
			$de[] = $tab_de[1][$des];
		}
		else
		{
			$z = 6;
			$check = true;
			while($z >= 0 && $check)
			{
				if ($potentiel >= $tab_de[0][$z])
				{
					$potentiel = $potentiel - $tab_de[0][$z];
					$de[] = $tab_de[1][$z];
					$check = false;
				}
				$z--;
			}
		}
	}
	return $de;
}

//Fonction permettant de calculer les dés de soins
function de_soin($force, $degat_arme)
{
	$tab_de = array();
	$tab_de[0][0] = 3;
	$tab_de[0][1] = 4;
	$tab_de[0][2] = 5;
	$tab_de[1][0] = 4;
	$tab_de[1][1] = 6;
	$tab_de[1][2] = 8;
	$potentiel = ceil($force / 3) + $degat_arme;
	$de_degat = array();
	while($potentiel > 2)
	{		
		if (($potentiel > 7) AND ($potentiel < 15))
		{
			$des = array_search(ceil($potentiel / 2), $tab_de[0]);
			$potentiel = $potentiel - $tab_de[0][$des];
			$de[] = $tab_de[1][$des];
		}
		else
		{
			for ($z = 2; $z >= 0; $z--)
			{
				if ($potentiel >= $tab_de[0][$z])
				{
					$potentiel = $potentiel - $tab_de[0][$z];
					$de[] = $tab_de[1][$z];
				}
			}
		}
	}
	return $de;
}

function prend_objet($id_objet, $joueur)
{
	global $db, $G_erreur, $G_place_inventaire;
	$trouver = false;
	$stack = false;
	$objet_d = decompose_objet($id_objet);
	if($objet_d['categorie'] != 'o')
	{
		$row['stack'] = 0;
	}
	else
	{
		$id_reel_objet = $objet_d['id_objet'];
		//Recherche de l'objet
		$requete = "SELECT * FROM objet WHERE id = ".$id_reel_objet;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
	}
	//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
	$i = 0;
	while(($i < $G_place_inventaire) AND !$trouver)
	{
		$objet_i = decompose_objet($joueur['inventaire_slot'][$i]);
		if($objet_i['sans_stack'] == $objet_d['sans_stack'] AND intval($objet_i['stack']) < $row['stack'])
		{
			$trouver = true;
			$stack = true;
		}
		else $i++;
	}
	if(!$trouver)
	{
		//Recherche un emplacement libre
		$i = 0;
		while(($i < $G_place_inventaire) AND !$trouver)
		{
			if($joueur['inventaire_slot'][$i] === 0 OR $joueur['inventaire_slot'][$i] == '')
			{
				$trouver = true;
			}
			else $i++;
		}
	}
	//Inventaire plein
	if(!$trouver)
	{
		$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
		return $false;
	}
	else
	{
		if(!$stack)
		{
			$joueur['inventaire_slot'][$i] = $id_objet;
		}
		else
		{
			$stacks = $objet_i['stack'] + 1;
			if($stacks == 1) $stacks = 2;
			$joueur['inventaire_slot'][$i] = $objet_i['sans_stack'].'x'.$stacks;
		}
		$inventaire_slot = serialize($joueur['inventaire_slot']);
		$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur['ID'];
		//echo $requete;
		$req = $db->query($requete);
		return $joueur;
	}
	return $joueur;
}

function prend_recette($id_recette, $joueur)
{
	global $db, $G_erreur;
	$id_reel_recette = mb_substr($id_recette, 1);
	//Recherche si il a pas déjà cette recette
	$requete = "SELECT id, nombre FROM perso_recette WHERE id_recette = ".$id_reel_recette." AND id_perso = ".$joueur['ID'];
	$db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_row($req);
		if($row[1] > 0)
		{
			$requete = "UPDATE perso_recette SET nombre = nombre + 1 WHERE id = ".$row[0];
		}
	}
	else
	{
		$requete = "INSERT INTO perso_recette VALUES('', ".$id_reel_recette.", ".$joueur['ID'].", 1)";
	}
	$db->query($requete);
}

function get_royaume_info($race_joueur, $royaume_id)
{
	global $db;
	$Roy_requete = 'SELECT * FROM royaume WHERE ID = '.$royaume_id;
	$Roy_req = $db->query($Roy_requete);
	$Roy_row = $db->read_array($Roy_req);
	if ($Roy_row === false) {
	  error_log('Gros probleme : row === false');
	}
	$Roy_row['diplo'] = 5;
	$Roy_row['diplo_time'] = unserialize($Roy_row['diplo_time']);
	if($Roy_row['ID'] != 0)
	{
		//Sélection de la diplomatie
		$requete_diplo = "SELECT ".$Roy_row['race']." FROM diplomatie WHERE race = '".$race_joueur."'";
		$req_diplo = $db->query($requete_diplo);
		$row_diplo = $db->read_row($req_diplo);
		$Roy_row['taxe_base'] = $Roy_row['taxe'];
		
		$Roy_row['taxe'] = taux_taxe($Roy_row['taxe'], $row_diplo[0]);
	
		$Roy_row['diplo'] = $row_diplo[0];
	}
	return $Roy_row;
}

function taux_taxe($taxe, $diplo)
{
	//Calcul de la taxe
	switch($diplo)
	{
		case 127 :
			$taxe = floor($taxe / 4);
		break;
		case 0 :
			$taxe = ceil($taxe / 4);
		break;
		case 1 :
			$taxe = floor($taxe / 3);
		break;
		case 2 :
			$taxe = ceil($taxe / 3);
		break;
		case 3 :
			$taxe = floor($taxe / 2);
		break;
		case 4 :
			$taxe = ceil($taxe / 2);
		break;
		case 5 :
			$taxe = ceil($taxe / 1.5);
		break;
		case 6 :
			$taxe = ceil($taxe / 1);
		break;
		case 7 :
			$taxe = 0;
		break;
		case 8 :
			$taxe = 0;
		break;
		case 9 :
			$taxe = 0;
		break;
		case 10 :
			$taxe = 0;
		break;
		case 11 :
			$taxe = 0;
		break;
	}
	return $taxe;
}

function description($texte, $array)
{
	while(eregi("%([a-z0-9]*)%", $texte, $regs))
	{
		$texte = str_replace('%'.$regs[1].'%', $array[$regs[1]], $texte);
	}
	$valeur = '';
	while(eregi("@(.*)@", $texte, $regs))
	{
		$r = $regs[1];
		eval("\$valeur = ".$r.";");
		$texte = str_replace('@'.$regs[1].'@', $valeur, $texte);
	}
	return $texte;
}

//Affiche un lien pour afficher le menu ville
function return_ville($texte, $poscase)
{
	if(is_ville($poscase) == 1)
	{
		echo '<a href="ville.php?poscase='.$poscase.'" onclick="return envoiInfo(this.href, \'centre\')">'.$texte.'</a>';
	}
}

//Affiche un lien pour afficher le menu gestion royaume
function return_gestion_royaume($texte, $poscase)
{
	echo '<a href="ville.php?poscase='.$poscase.'" onclick="return envoiInfo(this.href, \'centre\')">'.$texte.'</a>';
}

function lance_buff($type, $id, $effet, $effet2, $duree, $nom, $description, $type_cible, $debuff, $nb_buff, $grade)
{
	global $db, $G_erreur;
	$lancement = true;
	if($type_cible == 'perso')
	{
		$table = 'buff';
		$champ = 'id_perso';
	}
	else
	{
		$table = 'buff_monstre';
		$champ = 'id_monstre';
	}
	$Buff_requete = 'SELECT * FROM '.$table.' WHERE '.$champ.' = '.$id.' AND type = \''.$type.'\'';
	//echo $Buff_requete;
	$Buff_req = $db->query($Buff_requete);
	$Buff_row = $db->read_array($Buff_req);
	//echo $db->num_rows;
	if($db->num_rows == 0)
	{
		if($nb_buff < ($grade + 2))
		{
			$requete = "INSERT INTO ".$table."(`type`, `effet`, `effet2`, `fin`, `duree`, `".$champ."`, `nom`, `description`, `debuff`) VALUES('".$type."', ".$effet.", ".$effet2.", ".(time()+$duree).", ".$duree.", ".$id.", '".$nom."', '".addslashes($description)."', ".$debuff.")";
			$db->query($requete);
		}
		else
		{
			$G_erreur = 'overbuff';
			$lancement = false;
		}
	}
	elseif($effet >= $Buff_row['effet'])
	{
		$requete = "UPDATE ".$table." SET effet = ".$effet.", effet2 = ".$effet2.", fin = ".(time() + $duree).", nom = '".$nom."', description = '".addslashes($description)."' WHERE id = ".$Buff_row['id'];
		$db->query($requete);
	}
	else
	{
		$G_erreur = 'puissant';
		$lancement = false;
	}
	return $lancement;
}

function verif_mort($pourcent, $var, $duree_debuff=0, $multiplicateur_mouvement=0)
{
	global $Trace, $db, $joueur;
	if ($joueur['hp'] <= 0)
	{
		//Recherche du fort le plus proche
		$requete = "SELECT *, (ABS(".$joueur['x']." - cast(x as signed integer)) + ABS(".$joueur['y']." - cast(y as signed integer))) AS plop FROM `construction` WHERE rez > 0 AND royaume = ".$Trace[$joueur['race']]['numrace']." ORDER BY plop ASC";
		$req_b = $db->query($requete);
		$bat = $db->num_rows;
		$row_b = $db->read_assoc($req_b);
		//Bonus mort-vivant
		if($joueur['race'] == 'mortvivant') $bonus = 10;
		else $bonus = 0;
		//Vérifie si amende qui empèche le spawn en ville
		$amende = recup_amende($joueur['ID']);
		$echo = 'Revenir dans votre ville natale';
		$spawn_ville = 'ok';
		if($amende)
		{
			if($amende['respawn_ville'] == 'y')
			{
				$echo = 'Revenir dans le refuge des criminels';
				$spawn_ville = 'wrong';
			}
		}
		if($var == 1)
		{
			?>
			<div id="presentation">
				<h2 class="ville_titre">Vous êtes mort</h2>
				<div class="ville_test">
					Position de votre cadavre : <?php echo 'X - '.$joueur['x'].' / Y - '.$joueur['y']; ?>
				</div>
				<p>
				</p>
				<div class="ville_test">
					Que voulez vous faire ?
					<ul class="ville">
					<?php
					//Supprime les Rez plus valident
					$requete = "DELETE FROM rez WHERE TIMESTAMPDIFF(MINUTE , time, NOW()) > 1440";
					//$db->query($requete);
					$requete = "SELECT * FROM rez WHERE id_perso = ".$joueur['ID'];
					$req = $db->query($requete);
					if($db->num_rows > 0)
					{
						while($row = $db->read_assoc($req))
						{
							echo '
						<li><a href="mort.php?choix=2&amp;rez='.$row['id'].'">Vous faire ressuciter par '.$row['nom_rez'].' ('.($row['pourcent'] + $bonus).'% HP / '.($row['pourcent'] + $bonus).' MP)</li>';
						}
					}
					if($bat > 0 AND !is_donjon($joueur['x'], $joueur['y']))
					{
							echo '
						<li><a href="mort.php?choix=3&amp;rez='.$row_d['id'].'">Revenir dans le fort le plus proche (x : '.$row_b['x'].' / y : '.$row_b['y'].') ('.($row_b['rez'] + $bonus).'% HP / '.($row_b['rez'] + $bonus).'% MP)</li>';
					}
					echo '
						<li><a href="mort.php?choix=1">'.$echo.' ('.(20 + $bonus).'% HP / '.(20 + $bonus).'% MP)</a></li>';
					?>
						<li><a href="index.php?deco=ok">Vous déconnecter</a></li>
						<li>Vous pouvez attendre qu&rsquo;un autre joueur vous ressucite</li>
					</ul>
				</div>
				<p>
				</p>
				<div class="ville_test">
					Vos dernières actions :
					<ul>
					<?php
					$requete = "SELECT * FROM journal WHERE id_perso = ".$joueur['ID']." ORDER by time DESC, id DESC LIMIT 0, 15";
					$req = $db->query($requete);
					while($row = $db->read_assoc($req))
					{
						echo '<li>'.affiche_ligne_journal($row).'</li>';
					}
					?>
					</ul>
				</div>
				<p>
				</p>
				<div class="ville_test">
					<a href="index.php">Index du jeu</a> - <a href="http://forum.starshine-online.com">Accéder au forum</a>
				</div>
			</div>
			<?php
			exit();
		}
		elseif($var == 2)
		{
			if($spawn_ville == 'ok')
			{
				$joueur['x'] = $Trace[$joueur['race']]['spawn_x'];
				$joueur['y'] = $Trace[$joueur['race']]['spawn_y'];
			}
			else
			{
				$joueur['x'] = $Trace[$joueur['race']]['spawn_c_x'];
				$joueur['y'] = $Trace[$joueur['race']]['spawn_c_y'];
			}
		}
		elseif($var == 4)
		{
			$joueur['x'] = $row_b['x'];
			$joueur['y'] = $row_b['y'];
			$pourcent = $row_b['rez'];
		}
		$pourcent += $bonus;
		$joueur['hp'] = $joueur['hp_max'] * $pourcent / 100;
		$joueur['mp'] = $joueur['mp_max'] * $pourcent / 100;		

		//Téléportation dans sa ville avec PV et MP modifiés
		$requete = 'UPDATE perso SET x = '.$joueur['x'].', y = '.$joueur['y'].', hp = '.$joueur['hp'].', mp = '.$joueur['mp'].', regen_hp = '.time().' WHERE ID = '.$joueur['ID'];
		$db->query($requete);
		$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

		//Vérifie si il a déjà un mal de rez
		$requete = "SELECT fin FROM buff WHERE id_perso = ".$joueur['ID']." AND type = 'debuff_rez'";
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_row($req);
			$duree = $row[0] - time();
		}
		else $duree = 0;
		$duree_debuff += $duree;
		//Suppression des buffs
		$requete = "DELETE FROM buff WHERE id_perso = ".$joueur['ID']." AND type <> 'debuff_rez'";
		$db->query($requete);
		//Si rez en ville ou sur fort, on débuff le déplacement
		if($duree_debuff > 0)
		{
			//Déplacement * 2
			$effet = 2;
			lance_buff('debuff_rez', $joueur['ID'], $effet, $multiplicateur_mouvement, $duree_debuff, 'Mal de résurrection', 'Mulitplie vos coûts de déplacement par '.$effet, 'perso', 1, 0, 0);
		}
	}
}

function genere_image_pa($joueur)
{
	global $G_PA_max;
	//Barre PA
	$ratio_pa = floor(10 * ($joueur['pa'] / $G_PA_max));
	if($ratio_pa > 10) $ratio_pa = 10;
	if($ratio_pa < 0) $ratio_pa = 0;
	$barre_pa = './image/barre/pa'.$ratio_pa.'.png';
	return $barre_pa;}

function genere_image_buff_duree($buff)
{//-- Barre durée restante du buff
	$ratio_buff_duree = floor(10 * (($buff['fin'] - time()) / ($buff['duree'])));
	if($ratio_buff_duree > 10) 	{ $ratio_buff_duree = 10; };
	if($ratio_buff_duree < 0) 	{ $ratio_buff_duree = 0; };
	$barre_buff_duree = "image/barre/buff_duree".$ratio_buff_duree.".png";
	return "<img src='".$barre_buff_duree."' class='buff_duree_restante' alt='duree buff' />";
}

function genere_image_hp($joueur)
{
	//Barre HP
	$ratio_hp = floor(10 * ($joueur['hp'] / $joueur['hp_max']));
	if($ratio_hp > 10) $ratio_hp = 10;
	if($ratio_hp < 0) $ratio_hp = 0;
	$barre_vie = './image/barre/vie'.$ratio_hp.'.png';
	return $barre_vie;
}

function genere_image_mp($joueur)
{
	//Barre MP
	$ratio_mp = floor(10 * ($joueur['mp'] / $joueur['mp_max']));
	if($ratio_mp > 10) $ratio_mp = 10;
	if($ratio_mp < 0) $ratio_mp = 0;
	$barre_mp = './image/barre/mp'.$ratio_mp.'.png';
	return $barre_mp;
}

function genere_image_hp_groupe($joueur)
{
	//Barre HP
	$ratio_hp = floor(10 * ($joueur['hp'] / $joueur['hp_max']));
	if($ratio_hp > 10) $ratio_hp = 10;
	if($ratio_hp < 0) $ratio_hp = 0;
	$barre_vie = 'image/barre/g_vie'.$ratio_hp.'.png';
	return '<img src="'.$barre_vie.'" alt="HP = '.$joueur['hp'].' / '.$joueur['hp_max'].'" title="HP = '.$joueur['hp'].' / '.$joueur['hp_max'].'" style="height : 5px; width : 100px;" />';
}

function genere_image_mp_groupe($joueur)
{
	//Barre MP
	$ratio_mp = floor(10 * ($joueur['mp'] / $joueur['mp_max']));
	if($ratio_mp > 10) $ratio_mp = 10;
	if($ratio_mp < 0) $ratio_mp = 0;
	$barre_mp = 'image/barre/g_mp'.$ratio_mp.'.png';
	return '<img src="'.$barre_mp.'" alt="MP = '.$joueur['mp'].' / '.$joueur['mp_max'].'" title="MP = '.$joueur['mp'].' / '.$joueur['mp_max'].'" style="height : 5px; width : 100px;" />';
}

function genere_image_exp($xp_joueur, $xp_p_n, $progression)
{
	//Barre EXP
	$ration_xp = floor($progression / 10);
	if($ration_xp > 10) $ration_xp = 10;
	if($ration_xp < 0) $ration_xp = 0;
	$barre_xp = './image/barre/xp'.$ration_xp.'.png';
	return $barre_xp;
}


function genere_image_comp($joueur, $competence, $comp_max)
{
	global $Gtrad;
	//Barre Comp
	$ratio_comp = floor(10 * ($joueur[$competence] / $comp_max));
	if($ratio_comp > 10) $ratio_comp = 10;
	if($ratio_comp < 0) $ratio_comp = 0;
	$barre_comp = 'image/barre/pa'.$ratio_comp.'.png';
	return '<img src="'.$barre_comp.'" alt="'.$Gtrad[$competence].' = '.$joueur[$competence].' / '.$comp_max.'" title="'.$Gtrad[$competence].' = '.$joueur[$competence].' / '.$comp_max.'" style="height : 8px; width : 100px;" />';
}

function genere_image_comp2($joueur, $competence, $comp_max)
{
	global $Gtrad;
	//Barre Comp
	$ratio_comp = floor(10 * ($joueur['competences'][$competence] / $comp_max));
	if($ratio_comp > 10) $ratio_comp = 10;
	if($ratio_comp < 0) $ratio_comp = 0;
	$barre_comp = 'image/barre/pa'.$ratio_comp.'.png';
	return '<img src="'.$barre_comp.'" alt="'.$Gtrad[$competence].' = '.$joueur['competences'][$competence].' / '.$comp_max.'" title="'.$Gtrad[$competence].' = '.$joueur['competences'][$competence].' / '.$comp_max.'" style="height : 8px; width : 100px;" />';
}

function affiche_ligne_journal($row)
{
	$date = strtotime($row['time']);
	$date = date("j/m H:i", $date);
	switch($row['action'])
	{
		case 'attaque' :
			return '<li class="jdegat"><span class="small">['.$date.']</span> Vous attaquez '.$row['passif'].' et lui faites '.$row['valeur'].' dégats, il vous en fait '.$row['valeur2'].'</li>';
		break;
		case 'defense' :
			return '<li class="jrdegat"><span class="small">['.$date.']</span> '.$row['passif'].' vous a attaqué et fait '.$row['valeur'].' dégats et vous lui faites '.$row['valeur2'].'</li>';
		break;
		case 'tue' :
			return '<li class="jkill"><span class="small">['.$date.']</span> Vous tuez '.$row['passif'].'.</li>';
		break;
		case 'mort' :
			return '<li class="jmort"><span class="small">['.$date.']</span> '.$row['passif'].' vous a tué.</li>';
		break;
		case 'soin' :
			return '<li class="jsoin"><span class="small">['.$date.']</span> Vous soignez '.$row['passif'].' de '.$row['valeur'].' HP.</li>';
		break;
		case 'rsoin' :
			return '<li class="jsoin"><span class="small">['.$date.']</span> '.$row['passif'].' vous soigne de '.$row['valeur'].' HP.</li>';
		break;
		case 'gsoin' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> Vous soignez votre groupe pour un total de '.$row['valeur'].' HP.</li>';
		break;
		case 'rgsoin' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> '.$row['passif'].' vous soigne (en groupe) de '.$row['valeur'].' HP.</li>';
		break;
		case 'vend' :
			return '<li><span class="small">['.$date.']</span> Vous avez vendu '.$row['valeur'].' pour '.$row['valeur2'].' stars.</li>';
		break;
		case 'loot' :
			return '<li class="jloot"><span class="small">['.$date.']</span> Vous avez obtenu '.$row['valeur'].' en tuant un monstre.</li>';
		break;
		case 'f_quete' :
			return '<li class="jquete"><span class="small">['.$date.']</span> Vous finissez la quête "'.$row['valeur'].'".</li>';
		break;
		case 'r_amende' :
			return '<li><span class="small">['.$date.']</span> '.$row['passif'].' paye sa dette envers la société, et vous recevez '.$row['valeur'].' stars</li>';
		break;
		case 'buff' :
			return '<li class="jbuff"><span class="small">['.$date.']</span> Vous lancez le buff '.$row['valeur'].' sur '.$row['passif'].'.</li>';
		break;
		case 'rbuff' :
			return '<li class="jbuff"><span class="small">['.$date.']</span> '.$row['passif'].' vous buff avec '.$row['valeur'].'.</li>';
		break;
		case 'gbuff' :
			return '<li class="jgbuff"><span class="small">['.$date.']</span> Vous lancez le buff '.$row['valeur'].' sur votre groupe.</li>';
		break;
		case 'rgbuff' :
			return '<li class="jgbuff"><span class="small">['.$date.']</span> '.$row['passif'].' vous buff (en groupe) avec '.$row['valeur'].'.</li>';
		break;
		case 'debuff' :
			return '<li class="jdebuff"><span class="small">['.$date.']</span> Vous lancez le debuff '.$row['valeur'].' sur '.$row['passif'].'.</li>';
		break;
		case 'rdebuff' :
			return '<li class="jdebuff"><span class="small">['.$date.']</span> '.$row['passif'].' vous debuff avec '.$row['valeur'].'.</li>';
		break;
	  case 'teleport' :
			if ($row['valeur'] == 'jeu')
				return '<li class="jgbuff"><span class="small">['.$date.']</span> '.$row['passif'].' vous téléporte dans le jeu.</li>';
			else
				return '<li class="jgbuff"><span class="small">['.$date.']</span> '.$row['passif'].' vous téléporte dans l\'arène '.$row['valeur'].'.</li>';
		break;
	}
}

function prochain_level($level)
{
	global $G_level_xp;
	return (($level * ($level + 1)) / 2) * $G_level_xp;
}

function level_courant($exp)
{
	global $G_level_xp;
	if($exp == 0)
	{
		return 0;
	}
	else
	{
		//calcul du delta
		$delta = pow(($G_level_xp / 2), 2) + (2 * $G_level_xp * $exp) - 1;
		//renvoi le niveau a virgule
		$niveau = 0.5 + (sqrt($delta) / $G_level_xp);
		return $niveau;
	}
}

function progression_level($level)
{
	$progress = $level - floor($level);
	$progress = floor($progress * 100);
	return $progress;
}

function over_price($base, $joueur)
{
	if($base > $joueur AND $joueur != '')
	{
		return 'achat_over';
	}
	else
	{
		return 'achat_normal';
	}
}

function check_secu($param)
{
	if( empty($param) )
	{
	  return false;
	}
	
	if( preg_match("/'+/", $param) == 1 )
	{
		return false;
	}
	
	return true;
}

function calcul_pp($pp)
{
	return (1 - (sqrt($pp / 10) / 40));
}

function image_sort($type)
{
	switch($type)
	{
		case 'vie' :
			return '<img src="image/sort/sort_soins1.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_critique' :
			return '<img src="image/buff/buff_critique.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_bouclier' :
			return '<img src="image/buff/buff_bouclier.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_evasion' :
			return '<img src="image/buff/buff_evasion.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_inspiration' :
			return '<img src="image/buff/buff_inspiration.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_force' :
			return '<img src="image/buff/buff_force.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_barriere' :
			return '<img src="image/buff/buff_barriere.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_colere' :
			return '<img src="image/buff/buff_colere.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_meditation' :
			return '<img src="image/buff/buff_meditation.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_surpuissance' :
			return '<img src="image/buff/buff_surpuissance.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_concentration' :
			return '<img src="image/buff/buff_concentration.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_furie_magique' :
			return '<img src="image/buff/buff_furie_magique.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_rapidite' :
			return '<img src="image/buff/buff_rapidite.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_rage_vampirique' :
			return '<img src="image/buff/buff_rage_vampirique.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_bouclier_sacre' :
			return '<img src="image/buff/buff_bouclier_sacre.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_armure_glace' :
			return '<img src="image/buff/buff_armure_glace.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_epine' :
			return '<img src="image/buff/buff_epine.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'buff_sacrifice' :
			return '<img src="image/buff/buff_sacrifice.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'debuff_aveuglement' :
			return '<img src="image/buff/debuff_aveuglement.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'debuff_desespoir' :
			return '<img src="image/buff/debuff_desespoir.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'debuff_ralentissement' :
			return '<img src="image/buff/debuff_ralentissement.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'debuff_enracinement' :
			return '<img src="image/buff/debuff_ralentissement.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'engloutissement' :
			return '<img src="image/buff/engloutissement.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'deluge' :
			return '<img src="image/buff/deluge.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'blizzard' :
			return '<img src="image/buff/blizzard.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'orage_magnetique' :
			return '<img src="image/buff/orage_magnetique.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'bouclier_eau' :
			return '<img src="image/buff/bouclier_eau.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'bouclier_feu' :
			return '<img src="image/buff/bouclier_feu.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'bouclier_terre' :
			return '<img src="image/buff/bouclier_terre.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'rez' :
			return '<img src="image/buff/rez.jpg" alt="" style="vertical-align : middle;" />';
		break;
		case 'teleport' :
			return '<img src="image/buff/teleport.jpg" alt="" style="vertical-align : middle;" />';
		break;
		case 'body_to_mind' :
			return '<img src="image/buff/body_to_mind.jpg" alt="" style="vertical-align : middle;" />';
		break;
		case 'repos_sage' :
			return '<img src="image/buff/repos_sage.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'bulle_sanctuaire' :
			return '<img src="image/buff/bulle_sanctuaire.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'guerison' :
			return '<img src="image/buff/guerison.png" alt="" style="vertical-align : middle;" />';
		break;
	}
}

function pourcent_reussite($base, $difficulte)
{
	if($base > $difficulte)
	{
		$chance = ($difficulte * ($difficulte + 1)) / 2;
		$chance += ($base - $difficulte) * ($difficulte + 1);
	}
	else $chance = ($base * ($base + 1)) / 2;
	$total = ($difficulte + 1) * ($base + 1);
	$chance_reussite = 100 * round(($chance / $total), 4);
	return $chance_reussite;
}

function refresh_perso()
{
	echo '<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
}

function affiche_condition($action, $joueur)
{
	global $db, $Trace;
	$liste_etats = get_etats();
	$echo = '';
	if ($action[0] == '!')
	{
		$echo .= 'attaquer';
	}
	elseif ($action[0] == '~')
	{
		$sort_sort = mb_substr($action, 1, strlen($action));
		$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$mpsort = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
	}
	elseif ($action[0] == '_')
	{
		$sort_sort = mb_substr($action, 1, strlen($action));
		$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$row['mp'].' réserves)</span>';
	}
	elseif ($action[0] == '#')
	{
		$action2 = explode('@', $action);
		$arguments = explode('µ', $action2[0]);
		$alors = $action2[1];
		$nb_arguments = count($arguments);
		$j = 0;
		$bool2 = 1;
		$echo = 'Si ';
		while ($j < $nb_arguments)
		{
			if($j != 0) $echo .= '<br />& ';
			$arg = mb_substr($arguments[$j], 1, 2);
			$operateur = $arguments[$j][3];
			$parametre = mb_substr($arguments[$j], 4, strlen($arguments[$j]));
			if ($arg == '00')
			{
				$echo .= 'HP '.$operateur.' '.$parametre;
			}
			if ($arg == '01')
			{
				$echo .= 'Réserve de mana '.$operateur.' '.$parametre;
			}
			if ($arg == '09')
			{
				$echo .= 'Round '.$operateur.' '.$parametre;
			}
			if ($arg == '10')
			{
				$echo .= 'vous n\'êtes pas '.$liste_etats[$parametre]['nom'];
			}
			if ($arg == '11')
			{
				$echo .= 'l\'ennemi n\'est pas '.$liste_etats[$parametre]['nom'];
			}
			if ($arg == '12')
			{
				$echo .= 'vous êtes '.$liste_etats[$parametre]['nom'];
			}
			if ($arg == '13')
			{
				$echo .= 'l\'ennemi est '.$liste_etats[$parametre]['nom'];
			}
			if ($arg == '14')
			{
				$echo .= 'utilisation de la compétence '.$operateur.' '.$parametre;
			}
			$j++;
		}
		$echo .= '<br />';
		if ($alors[0] == '!')
		{
			$echo .= 'Attaquer<br />';
		}
		elseif ($alors[0] == '~')
		{
			$sort_sort = mb_substr($alors, 1, strlen($alors));
			$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$mpsort = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
			$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
		}
		elseif ($alors[0] == '_')
		{
			$sort_sort = mb_substr($alors, 1, strlen($alors));
			$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$row['mp'].' réserves)</span>';
		}
	}
	return $echo;
}

function affiche_condition_session($action, $joueur)
{
	global $db, $Trace;
	$etats = get_etats();
	$echo = '';
	$check = false;
	if(is_array($action) AND array_key_exists('condition', $action))
	{
		foreach($action['condition'] as $condition)
		{
			if($check) $echo .='& ';
			else $echo .= 'Si ';
			$arg = $condition['si'];
			$operateur = $condition['op'];
			$parametre = $condition['valeur'];
			if ($arg == '00')
			{
				$echo .= 'HP '.$operateur.' '.$parametre;
			}
			elseif ($arg == '01')
			{
				$echo .= 'Réserve de mana '.$operateur.' '.$parametre;
			}
			elseif ($arg == '09')
			{
				$echo .= 'Round '.$operateur.' '.$parametre;
			}
			elseif ($arg == '10')
			{
				$echo .= 'vous n\'êtes pas '.$etats[$parametre]['nom'];
			}
			elseif ($arg == '11')
			{
				$echo .= 'l\'ennemi n\'est pas '.$etats[$parametre]['nom'];
			}
			elseif ($arg == '12')
			{
				$echo .= 'vous êtes '.$etats[$parametre]['nom'];
			}
			elseif ($arg == '13')
			{
				$echo .= 'l\'ennemi est '.$etats[$parametre]['nom'];
			}
			elseif ($arg == '14')
			{
				$echo .= 'Utilisation de la compétence '.$operateur.' '.$parametre;;
			}
			$echo .= '<br />';
			$check = true;
		}
	}
	if ($action['final'] == '!')
	{
		$echo .= 'attaquer';
	}
	elseif ($action['final'][0] == 's')
	{
		$sort_sort = mb_substr($action['final'], 1, strlen($action['final']));
		$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$mpsort = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
	}
	elseif ($action['final'][0] == 'c')
	{
		$sort_sort = mb_substr($action['final'], 1, strlen($action['final']));
		$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
	}
	return $echo;
}

function verif_ville($x, $y)
{
	global $db;
	$position = convert_in_pos($x, $y);
	$W_requete = 'SELECT type FROM map WHERE ID = '.$position;
	$W_req = $db->query($W_requete);
	$W_row = $db->read_assoc($W_req);
	//On est en ville
	if($W_row['type'] == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function aff_var($v)
{
	echo '<pre>';
	var_dump($v);
	echo '</pre>';
}

function supprime_bourg($royaume)
{
	global $db;
	$requete = "UPDATE royaume SET bourg = bourg - 1 WHERE ID = ".$royaume." AND bourg > 0";
	$db->query($requete);
}

define('ENCLIST', 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP, ISO-8859-1, WINDOWS-1252');
function normalize_entry_charset($fields)
{
	if (isset($_SERVER["CONTENT_TYPE"])) {
		$charset = stristr($_SERVER["CONTENT_TYPE"], 'charset=');
		if ($charset !== false) {
			$ch = explode('=', $charset);
			var_dump($ch);
			$src = $ch[1];
			//echo "Detected(1) $src<br />\n";
		}
	}
	if (!isset($src)) {
		$src = mb_http_input("G");
		if (!$src)
			$src = mb_http_input("P");
		if (!$src)
			unset($src);
		//else
			//echo "Detected(2) $src<br />\n";
	}
	foreach ($fields as $f) {
		if (isset($_GET[$f])) {
			if (!isset($src)) {
				$csrc = mb_detect_encoding($_GET[$f], ENCLIST);
				//echo "Detected(3) $csrc<br />\n";
			}
			else 
				$csrc = $src;
			if (strcasecmp($csrc, 'UTF-8') && strcasecmp($csrc, 'UTF8')) {
				$newfield = iconv($csrc, 'UTF-8//TRANSLIT', $_GET[$f]);
				//echo "convert to UTF-8: $newfield <br />\n";
				$_GET[$f] = $newfield;
			}
		}
		elseif (isset($_POST[$f])) {
			if (!isset($src)) {
        $csrc = mb_detect_encoding($_POST[$f], ENCLIST);
				echo "Detected(3) $csrc<br />\n";
			}
      else
        $csrc = $src;
			if (strcasecmp($csrc, 'UTF-8') && strcasecmp($csrc, 'UTF8')) {
        $newfield = iconv($csrc, 'UTF-8//TRANSLIT', $_POST[$f]);
        //echo "convert to UTF-8: $newfield <br />\n";
        $_POST[$f] = $newfield;
      }
		}
	}
}

?>