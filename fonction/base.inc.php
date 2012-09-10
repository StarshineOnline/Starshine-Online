<?php //  -*- tab-width:2; mode: php  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

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
	elseif($x > (190 - $champ_vision))		{ $dimensions['xmax'] = 190;		$dimensions['xmin'] = $x - ($case_affiche - (190 - $x + 1)); }
	else								{ $dimensions['xmin'] = $x - $champ_vision;	$dimensions['xmax'] = $x + $champ_vision; };
	
	if($y < ($champ_vision + 1))		{ $dimensions['ymin'] = 1;		$dimensions['ymax'] = $y + ($case_affiche - ($y)); }
	elseif($y > (190 - $champ_vision))	{ $dimensions['ymax'] = 190;		$dimensions['ymin'] = $y - ($case_affiche - (190 - $y + 1)); }
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
	if($x > 190 OR $y > 190)
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

	//Nombre de bourg déjà construits
	$requete_construction = "SELECT count(id) cpt from construction where id_batiment in (10,11,12) and royaume = ".$id_royaume;
	//Nombre de bourgs en construction
	$requete_placement = "SELECT count(id) cpt from placement where type = 'bourg' and royaume = ".$id_royaume;
	//Nombre de bourgs dans le qg
	$requete_qg = "SELECT count(d.id) cpt from depot_royaume d, objet_royaume o where o.id = d.id_objet and o.type = 'bourg' and d.id_royaume = ".$id_royaume;
	// On aggrege ca
	$requete = "SELECT sum(cpt) from ( $requete_construction UNION ALL $requete_placement UNION ALL $requete_qg ) agg";
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$bourgs = $row[0];
	//nombre de bourgs dans les sacs
	//trouver les sacs avec des bourgs dedans
	$requete = "select p.id from perso p, royaume r where inventaire_slot like '%\"r10\"%' and p.race = r.race and r.id = ".$id_royaume;
	$req = $db->query($requete);
  while ($row = $db->read_row($req)) {
		$perso = new perso($row[0]);
		$inv = $perso->get_inventaire_slot_partie();
		foreach ($inv as $item)
			if ($item == "r10")
				$bourgs++;
	}
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
	$typeterrain[22][0] = 'plaine';
	$typeterrain[22][1] = 'Plaine';
	$typeterrain[2][0] = 'foret';
	$typeterrain[2][1] = 'Forêt';
	$typeterrain[3][0] = 'desert';
	$typeterrain[3][1] = 'Désert';
	$typeterrain[4][0] = 'glace';
	$typeterrain[4][1] = 'Glace';
	$typeterrain[24][0] = 'glace';
	$typeterrain[24][1] = 'Glace';
	$typeterrain[5][0] = 'eau';
	$typeterrain[5][1] = 'Eau';
	$typeterrain[6][0] = 'montagne';
	$typeterrain[6][1] = 'Montagne';
	$typeterrain[23][0] = 'montagne';
	$typeterrain[23][1] = 'Montagne';
	$typeterrain[7][0] = 'marais';
	$typeterrain[7][1] = 'Marais';
	$typeterrain[8][0] = 'route';
	$typeterrain[8][1] = 'Route';
	$typeterrain[9][0] = 'route';
	$typeterrain[9][1] = 'Route';
	$typeterrain[10][0] = 'objet';
	$typeterrain[10][1] = 'Bâtiment';
	$typeterrain[20][0] = 'objet';
	$typeterrain[20][1] = 'Bâtiment';
	$typeterrain[21][0] = 'objet';
	$typeterrain[21][1] = 'Bâtiment';
	$typeterrain[11][0] = 'terre_maudite';
	$typeterrain[11][1] = 'Terre Maudite';
	$typeterrain[15][0] = 'donjon';
	$typeterrain[15][1] = 'Donjon';
	$typeterrain[25][0] = 'donjon';
	$typeterrain[25][1] = 'Donjon';
	$typeterrain[35][0] = 'donjon';
	$typeterrain[35][1] = 'Donjon';
	$typeterrain[45][0] = 'donjon';
	$typeterrain[45][1] = 'Donjon';
	$typeterrain[55][0] = 'donjon';
	$typeterrain[55][1] = 'Donjon';
	$typeterrain[65][0] = 'donjon';
	$typeterrain[65][1] = 'Donjon';
	$typeterrain[75][0] = 'donjon';
	$typeterrain[75][1] = 'Donjon';
	$typeterrain[85][0] = 'donjon';
	$typeterrain[85][1] = 'Donjon';
	$typeterrain[95][0] = 'donjon';
	$typeterrain[95][1] = 'Donjon';
	$typeterrain[16][0] = 'mur_donjon';
	$typeterrain[16][1] = 'Mur de Donjon';
	$typeterrain[26][0] = 'mur_donjon';
	$typeterrain[26][1] = 'Mur de Donjon';
	$typeterrain[36][0] = 'mur_donjon';
	$typeterrain[36][1] = 'Mur de Donjon';
	$typeterrain[46][0] = 'mur_donjon';
	$typeterrain[46][1] = 'Mur de Donjon';
	$typeterrain[56][0] = 'mur_donjon';
	$typeterrain[56][1] = 'Mur de Donjon';
	$typeterrain[66][0] = 'mur_donjon';
	$typeterrain[66][1] = 'Mur de Donjon';
	$typeterrain[76][0] = 'mur_donjon';
	$typeterrain[76][1] = 'Mur de Donjon';
	$typeterrain[86][0] = 'mur_donjon';
	$typeterrain[86][1] = 'Mur de Donjon';
	$typeterrain[96][0] = 'mur_donjon';
	$typeterrain[96][1] = 'Mur de Donjon';
	$typeterrain[101][0] = 'mer';
  $typeterrain[101][1] = 'Zone innondée';
	$typeterrain[105][0] = 'taverne_donjon';
  $typeterrain[105][1] = 'Repère des renegats';
	$typeterrain[106][0] = 'taverne_donjon';
  $typeterrain[106][1] = 'Repère des renegats';
	$typeterrain[135][0] = 'donjon_aqua_level_1';
  $typeterrain[135][1] = 'Donjon';
	$typeterrain[145][0] = 'donjon_aqua_level_1';
  $typeterrain[145][1] = 'Donjon';
	$typeterrain[155][0] = 'donjon_aqua_level_1';
  $typeterrain[155][1] = 'Donjon';
	$typeterrain[165][0] = 'donjon_aqua_level_1';
  $typeterrain[165][1] = 'Donjon';
	$typeterrain[175][0] = 'donjon_aqua_level_1';
  $typeterrain[175][1] = 'Donjon';
	$typeterrain[185][0] = 'donjon_aqua_level_1';
  $typeterrain[185][1] = 'Donjon';
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
	$ress['Plaine']['Nourriture'] = 6;
	$ress['Plaine']['Star'] = 0;
	$ress['Plaine']['Charbon'] = 1;
	$ress['Plaine']['Essence Magique'] = 1;
	
	$ress['Forêt']['Pierre'] = 3;
	$ress['Forêt']['Bois'] = 8;
	$ress['Forêt']['Eau'] = 4;
	$ress['Forêt']['Sable'] = 0;
	$ress['Forêt']['Nourriture'] = 4;
	$ress['Forêt']['Star'] = 0;
	$ress['Forêt']['Charbon'] = 1;
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
	$ress['Glace']['Essence Magique'] = 5;
	
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
function is_ville($x, $y = false)
{
	if($y == false)
	{
		$xy = convert_in_coord($x);
		$x = $xy['x'];
		$y = $xy['y'];
	}
	global $db;
	$requete = "SELECT type, royaume FROM map WHERE x = $x and y = $y";
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
	$coutGeneral['plaine'] = 4;
	$coutGeneral['foret'] = 6;
	$coutGeneral['desert'] = 5;
	$coutGeneral['glace'] = 5;
	$coutGeneral['marais'] = 5;
	$coutGeneral['montagne'] = 6;
	$coutGeneral['eau'] = 50;
	$coutGeneral['route'] = 2;
	$coutGeneral['objet'] = 2;
	$coutGeneral['terre_maudite'] = 5;
	$coutGeneral['donjon'] = 5;
	$coutGeneral['taverne_donjon'] = 2;
	$coutGeneral['donjon_aqua_level_1'] = 4;
	$coutGeneral['mur_donjon'] = 50;
	$coutGeneral['mer'] = 6;
	
	/*** BARBARES ***/
	$coutpa['barbare'] = $coutGeneral;
	$coutpa['barbare']['glace'] = 4;

	/*** ELFE DES BOIS ***/
	$coutpa['elfebois'] = $coutGeneral;
	$coutpa['elfebois']['foret'] = 5;

	/*** HAUT ELFE ***/
	$coutpa['elfehaut'] = $coutGeneral;
	$coutpa['elfehaut']['foret'] = 5;

	/*** HUMAIN ***/
	$coutpa['humain'] = $coutGeneral;

	/*** HUMAINS NOIRS ***/
	$coutpa['humainnoir'] = $coutGeneral;

	/*** MORTS VIVANTS ***/
	$coutpa['mortvivant'] = $coutGeneral;
	$coutpa['mortvivant']['marais'] = 4;

	/*** NAINS ***/
	$coutpa['nain'] = $coutGeneral;
	$coutpa['nain']['montagne'] = 5;

	/*** ORC ***/
	$coutpa['orc'] = $coutGeneral;

	/*** SCAVENGERS ***/
	$coutpa['scavenger'] = $coutGeneral;
	$coutpa['scavenger']['desert'] = 4;

	/*** TROLL ***/
	$coutpa['troll'] = $coutGeneral;

	/*** VAMPIRES ***/
	$coutpa['vampire'] = $coutGeneral;
	$coutpa['vampire']['terre_maudite'] = 4;

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
	//Si on est sur son royaume => Cout en PA réduit de 1, minimum 2
	if($case->get_royaume() == $Trace[$joueur->get_race()]['numrace'])
	{
		if($coutpa > 2) $coutpa -= 1;
	}
	//Buff rapide comme le vent
	if($joueur->is_buff('rapide_vent', true) or $joueur->is_enchantement('course') OR $joueur->is_buff('buff_rush', true))
	{
		if($coutpa > 2) $coutpa -= 1;
	}
	
	if ($diagonale) $coutpa++;
	//Mal de rez
	if($joueur->is_buff('debuff_rez'))
	{
		$coutpa = $coutpa * $joueur->get_buff('debuff_rez', 'effet');
	}
	//Maladies
	if($joueur->is_buff('cout_deplacement')) $coutpa = ceil($coutpa / $joueur->get_buff('cout_deplacement', 'effet'));
	if($joueur->is_buff('plus_cout_deplacement')) $coutpa = ceil($coutpa * $joueur->get_buff('plus_cout_deplacement', 'effet'));
	//Bâtiment qui augmente le coût de PA
	if($batiment = batiment_map($case->get_x(), $case->get_y()))
	{
		if($batiment['augmentation_pa'] > 1)
		{
			//Si on est pas sur son royaume augmentation de PA
			if($case->get_royaume() != $Trace[$joueur->get_race()]['numrace'])
			{
				$coutpa = $coutpa * $batiment['augmentation_pa'];
			}
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
function recupperso_essentiel($id, $select = 'id, nom, level, rang_royaume, race, classe')
{
	global $db;
	if(is_numeric($id))
	{
		if($id != '')
		{
			$requete = 'SELECT '.$select.' FROM perso WHERE id = '.$id;
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
	if(is_numeric($ID) AND $ID != '')
	{
			$requete = 'SELECT * FROM perso WHERE id = '.$ID;
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_array($req);
				
				$R_perso['id'] = $row['id'];
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
				$R_perso['beta'] = $row['beta'];
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
				$R_perso['artisanat'] = round(sqrt(($R_perso['architecture'] + $R_perso['forge'] + $R_perso['alchimie']) * 10));
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

				//Effets des enchantements
				if (isset($R_perso['enchantement']['pourcent_pm']))
					$R_perso['PM'] += floor($R_perso['PM'] * $R_perso['enchantement']['pourcent_pm']['effet'] / 100);
				if (isset($R_perso['enchantement']['pourcent_pp']))
					$R_perso['PP'] += floor($R_perso['PP'] * $R_perso['enchantement']['pourcent_pp']['effet'] / 100);

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
				if(array_key_exists('buff_rune', $R_perso['buff'])) $R_perso['reserve'] = $R_perso['reserve'] + $R_perso['buff']['buff_rune']['effet'];
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
				if(array_key_exists('robustesse', $R_perso['buff']))
				{
					$R_perso['hp_max'] = round($R_perso['hp_max'] + ((($R_perso['buff']['robustesse']['effet'] * $R_perso['hp_max']) / 100)));
					$R_perso['mp_max'] = round($R_perso['mp_max'] + ((($R_perso['buff']['robustesse']['effet'] * $R_perso['mp_max']) / 100)));
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
		echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
		exit();
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
			if(is_array($perso)) $perso_race = $perso['race'];
			else $perso_race = $perso->get_race();
			if(is_array($joueur)) $joueur_race = $joueur['race'];
			else $joueur_race = $joueur->get_race();
			if($joueur_race == $perso_race) return true;
			else return false;
		break;
		case 2 :
			if(is_array($perso)) $perso_id = $perso['id'];
			else $perso_id = $perso->get_id();
			if(is_array($joueur)) $joueur_id = $joueur['id'];
			else $joueur_id = $joueur->get_id();
			if($joueur_id == $perso_id) return true;
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
	$requete = "INSERT INTO bonus_perso(id_perso, id_bonus, valeur) VALUES(".$id_perso.", ".$id_bonus.", '')";
	if($db->query($requete)) return true;
	else return false;
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
	$o_batiment = new batiment($ID);
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
		if ($temps_total == 0){$temps_total=1;}
		$coeff = $temps_restant / $temps_total;
	}

	$R_monstre['id'] = $row['id'];
	$R_monstre['hp_max'] = $row['hp'];
	$R_monstre['hp_max_1'] = $row['hp'];
	$R_monstre['nom'] = $row['nom'];
	$R_monstre['level'] = 0;
	$R_monstre['cout'] = $row['cout'];  // Coût du bâtiment à la construction.
	$R_monstre['force'] = ceil($coeff * $row['carac']);
	$R_monstre['arme_degat'] = $o_batiment->get_bonus('degats_bat');  // Facteur de dégâts contre les bâtiments.
	$R_monstre['arme_degat2'] = $o_batiment->get_bonus('degats_siege');  // Facteur de dégâts contre les armes de sièges.
	$R_monstre['reload'] = $o_batiment->get_bonus('rechargement');  // Temps entre deux tirs.
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
	$R_monstre['melee'] =  $o_batiment->get_bonus('precision');

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
function recupaction($action, $pet = false)
{
	global $db;
	if (!$pet)
		$requete = "SELECT action FROM action_perso WHERE id = ".$action;
	else
		$requete = "SELECT action FROM action_pet WHERE id = ".$action;
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
function recupaction_all($action, $pet = false)
{
	global $db;
	if (!$pet)
		$requete = "SELECT * FROM action_perso WHERE id = ".$action;
	else
		$requete = "SELECT * FROM action_pet WHERE id = ".$action;
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
		$facteur2 = 4;//1;
	}
	else
	{
		$facteur1 = 4;
		$facteur2 = 5;//1.5;
	}
	$difficulte = $difficulte / $facteur1;
	$pamp = 7 / $sortpa;//sqrt($sortpa * $sortmp);
	$total = ($facteur2 * $pamp * sqrt($joueur->get_comp($type) / $difficulte));
	//echo $total.'<br />';
	return $total;
}

function augmentation_competences($liste_augmentations, $joueur)
{
  /*echo 'augmentation_competences :';
  print_r($liste_augmentations);*/
  foreach($liste_augmentations['comp'] as $aug)
	{
		$retour = augmentation_competence($aug[0], $joueur, $aug[1]);
		if($retour[1]) $joueur->set_comp($aug[0], $retour[0]);
	}
	foreach($liste_augmentations['comp_perso'] as $aug)
	{
		$retour = augmentation_competence($aug[0], $joueur, $aug[1]);
		if($retour[1]) $joueur->set_comp($aug[0], $retour[0]);
	}
	return $joueur;
}

/**
 * Merge deux tableaux d'ameliorations en un
 *
 * @param				$a1			premier tableau
 * @param				$a2			second tableau
 *
 * @return			le premier tableau dans lequel on a merge le second
 */
function merge_augmentations($a1, $a2)
{
	foreach ($a2 as $target => $augg) {
		foreach ($augg as $type => $comps) {
			foreach ($comps as $acomp) {
				$a1[$target][$type][] = $acomp;
			}
		}
	}
	return $a1;
}

/**
 * Permet de savoir si lors d'une action la compétence augmente de 1.
 * Plus la difficulte est forte, plus il est difficile de l'apprendre.
 * 
 * @param  $competence    ID de la compétence.
 * @param  $joueur        Tableau associatif décrivant le joueur.
 * @param  $difficulte    Difficuté (plus le chiffre est fort, plus c'est difficile)
 * 
 * @return  [0]     Nouvelle valeur de la compétence.
 * @return  [1]     1 s'il y a augmentation, 0 sinon.
 */ 
function augmentation_competence($competence, $joueur, $difficulte)
{
	global $db, $Tmaxcomp, $G_apprentissage_rate, $debugs;
	$R_retour = array('melee', false);
	//On vérifie que la chose a bien une classe :D
	if(method_exists($joueur, 'get_classe_id'))
	{
		//Récupère les limitations de la classe
		$perso = new perso($joueur->get_id());
		$requete = "SELECT * FROM classe_permet WHERE id_classe = ".$joueur->get_classe_id()." AND competence = '".$competence."'";
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
		// On se base sur le joueur et non le perso, sinon on perds les montees
		// des rounds precedents vu qu'on ne sauve qu'a la fin
		$val_competence = $joueur->get_comp($competence, true);

		echo 'Valeur actuelle de la compétence : '.$val_competence.'<br />
		Difficulté : '.$difficulte.'<br />';
		// Si la compétence n'a pas atteint sa valeur maximale, on effectue le jet d'amélioration
		if($val_competence < $max)
		{
		  // Jet d'amélioration
			$reussite = ceil(10000 / $G_apprentissage_rate);
			$numero = rand(1, $reussite);
			// Valeur seuil
			if($perso->get_race() == 'humain' OR $perso->get_race() == 'humainnoir') $apprentissage = 1.1; else $apprentissage = 1;
			if($perso->is_buff('apprenti_vent', true)) $apprentissage = $apprentissage * (1 + ($perso->get_buff('apprenti_vent', 'effet', true) / 100));
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
		if ($R_retour[1] == true/* && $perso->get_id() == $_SESSION['ID'] */)
			print_montee_comp($perso->get_nom(), $R_retour[0], $competence);
	}
	return $R_retour;
}

/**
 * Fonction permettant de calculer les dés de dégat en fonction de la force et de l'arme de la personne
 * 
 * @param  $force         Force du personnage.
 * @param  $degat_arme    Facteur de dégâts de l'arme.
 * 
 * @return  Tableau contenant les dés à lancer (chaque dès apparait autant de fois dans la tableu qu'il faut le lancer).  
 */ 
function de_degat($force, $degat_arme)
{
  // tableau utilisé pour déterminer les dés 
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
	// Facteur de dégâts
	$potentiel = ceil($force / 3) + $degat_arme;
	// Tableau des dés à lancer
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

/**
 * Fonction permettant de calculer les dés de soins.
 * 
 * @param  $force         Caractéristique associée.
 * @param  $degat_arme    Effet du sort.
 * 
 * @return  Tableau contenant les dés à lancer (chaque dès apparait autant de fois dans la tableu qu'il faut le lancer). 
 */ 
function de_soin($force, $degat_arme)
{
  // tableau utilisé pour déterminer les dés 
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

/**
 * Fonction gérant l'acquisition d'un objet par un personnage.
 * 
 * @param  $id_objet    Objet acquis.
 * @param  $joueur      Tableau décrivant le personnage qui acquiert l'objet.
 * 
 * @return  Tableau décrivant le personnage qui acquiert l'objet.
 */
function prend_objet($id_objet, $joueur)
{
	global $db, $G_erreur, $G_place_inventaire;
	$trouver = false;
	$stack = false;
	$objet_d = decompose_objet($id_objet);
	// Maximum d'empilement possible
	if($objet_d['categorie'] != 'o')
	{
	  // Ne peut pas être empilé
		$row['stack'] = 0;
	}
	else
	{
	  // Récupération de la description de l'objet
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
		$objet_i = decompose_objet($joueur->get_inventaire_slot_partie($i));
		// Comparaison de la description ('sans_stack') et du nombre d'objet empilé par rapport au maximum
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
	if(!$trouver)
	{
	  //Inventaire plein
		$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
		return $false;
	}
	else
	{
	  // Ajout de l'objet...
		if(!$stack)
		{
		  // ...dans un emplacement vide
			$joueur['inventaire_slot'][$i] = $id_objet;
		}
		else
		{
		  // ...à une pile d'objet identiques
			$stacks = $objet_i['stack'] + 1;
			if($stacks == 1) $stacks = 2;
			$joueur['inventaire_slot'][$i] = $objet_i['sans_stack'].'x'.$stacks;
		}
		// Mise à jour la base de donnée
		$inventaire_slot = serialize($joueur['inventaire_slot']);
		$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."' WHERE ID = ".$joueur->get_id();
		//echo $requete;
		$req = $db->query($requete);
		return $joueur;
	}
	return $joueur;
}

/**
 * Fonction incrémentant le nombre d'utilisations possibles d'une recette par un personnage
 * 
 * @param  $id_recette  Id de la recette à apprendre.
 * @param  $joueur      Tableau décrivant le personnage qui acquiert l'objet.
 */
function prend_recette($id_recette, $joueur)
{
	global $db, $G_erreur;
	$id_reel_recette = mb_substr($id_recette, 1);
	//Recherche s'il a déjà cette recette
	$requete = "SELECT id, nombre FROM perso_recette WHERE id_recette = ".$id_reel_recette." AND id_perso = ".$joueur->get_id();
	$db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_row($req);
		// S'il a un nombre restreint d'utilisation on incrémente
		if($row[1] > 0)
		{
			$requete = "UPDATE perso_recette SET nombre = nombre + 1 WHERE id = ".$row[0];
		}
	}
	else
	{
	  // On ajoute la recette avec une utilsation de 1
		$requete = "INSERT INTO perso_recette VALUES('', ".$id_reel_recette.", ".$joueur->get_id().", 1)";
	}
	$db->query($requete);
}

/**
 * Renvoie les informations sur le royaume
 * 
 * @param  $race_joueur   Race du personnage (pour la diplomatie et les taxes.
 * @param  $royaume_id    Id du royaume.
 * 
 * @return    Tableau associatif contenant les informations sur le royaume.  
 */
function get_royaume_info($race_joueur, $royaume_id)
{
	global $db;
	$Roy_requete = 'SELECT * FROM royaume WHERE ID = '.$royaume_id;
	$Roy_req = $db->query($Roy_requete);
	$Roy_row = $db->read_array($Roy_req);
	if ($Roy_row === false) {
	  error_log('Gros probleme : row === false');
	}
	return $Roy_row;
}

/**
 * Calcul la taxe payé par un personnage
 * 
 * @param  $taxe    Taxe défini du royaume concerné.
 * @param  $diplo   Diplomatie entre le royaume concerné et celui du personnage.
 * 
 * @return  Taxe payé par le personnage.
 */
function taux_taxe($taxe, $diplo)
{
	//Calcul de la taxe
	switch($diplo)
	{
		case 127 : // même royaume
			$taxe = floor($taxe / 4);
		break;
		case 0 : // Alliance fraternelle
			$taxe = ceil($taxe / 4);
		break;
		case 1 : // Alliance
			$taxe = floor($taxe / 3);
		break;
		case 2 : // Paix durable
			$taxe = ceil($taxe / 3);
		break;
		case 3 : // Paix
			$taxe = floor($taxe / 2);
		break;
		case 4 : // En bons termes
			$taxe = ceil($taxe / 2);
		break;
		case 5 : // Neutre
			$taxe = ceil($taxe / 1.5);
		break;
		case 6 : // Mauvais termes
			$taxe = ceil($taxe / 1);
		break;
		case 7 : // Guerre
			$taxe = 0;
		break;
		case 8 : // Guerre durable
			$taxe = 0;
		break;
		case 9 : // Ennemis
			$taxe = 0;
		break;
		case 10 : // Ennemis eternels
			$taxe = 0;
		break;
		case 11 : // ?
			$taxe = 0;
		break;
	}
	return $taxe;
}

/**
 * Formate les descriptions.
 * Ce qui est entre % sont remplacé par la variable correspondante dans le tableau
 * fourni et ce qui est entre @ et évalué. 
 * 
 * @param  $texte   Description à formater.
 * @param  $array   Tableau assiociatif contenant les vaeurs à remplacer.
 * 
 * @return    Description formatée.  
 */
function description($texte, $objet)
{
  // Remplacement des variables

	while(preg_match("`%([a-z0-9]*)%`i",$texte, $regs))
	{
	    if(is_array($objet))
	    {
		$texte = str_replace('%'.$regs[1].'%', $objet[$regs[1]], $texte);
	    }
	    else
	    {
		$get = 'get_'.$regs[1];
		$texte = str_replace('%'.$regs[1].'%', $objet->$get(), $texte);
	    }
	}
	// Evaluation
	$valeur = '';
	while(preg_match('`@(.*)@`', $texte, $regs))
	{
		$r = $regs[1];
		eval("\$valeur = ".$r.";");
		$texte = str_replace('@'.$regs[1].'@', $valeur, $texte);
	}
	return $texte;
}

/**
 * Affiche un lien pour afficher le menu ville si le personnage est en ville
 * 
 * @param  $texte     Texte à afficher dans le lien.
 * @param  $poscase   Position du personnage.   
 */ 
function return_ville($texte, $poscase)
{
  $pos = convert_in_coord($poscase);
	if (is_ville($pos['x'], $pos['y']) == 1)
	{
		echo '<a href="ville.php?poscase='.$poscase.'" onclick="return envoiInfo(this.href, \'centre\')">'.$texte.'</a>';
	}
}

/**
* Affiche un lien pour afficher le menu gestion royaume
* @access public
* @param string $texte, int $poscase
* @return null
*/
function return_gestion_royaume($texte, $poscase)
{
	echo '<a href="ville.php?poscase='.$poscase.'" onclick="return envoiInfo(this.href, \'centre\')">'.$texte.'</a>';
}

/**
 * Lance un buff sur un personnage ou un monstre
 * 
 * @param  $type          Nom générique du sort.
 * @param  $id            Id de la cible.
 * @param  $effet         Effet du buff
 * @param  $effet2        Effet secondaire
 * @param  $duree         Durée du buff
 * @param  $nom           Nom du buff
 * @param  $description   Description du buff
 * @param  $type_cible    Type de cible ('perso' ou 'monstre')
 * @param  $debuff        1 si c'est un débuff, 0 si c'est un buff
 * @param  $nb_buff       Nombre de buffs déjà actifs sur la cible.
 * @param  $nb_buff_max   Nombre de buffs max de la cible de la cible (grade+).
 * @param  $supprimable   1 si le buff est supprimable, 0 sinon.
 * 
 * @return      true si le sort a été lancé et false sinon.
 */
function lance_buff($type, $id, $effet, $effet2, $duree, $nom, $description, $type_cible, $debuff, $nb_buff, $nb_buff_max, $supprimable = 1)
{
	global $db, $G_erreur;
	$lancement = true;
	// Choix de la table et du champ d'id
	if($type_cible == 'perso')
	{
		$table = 'buff';
		$champ = 'id_perso';
	}
	else
	{
		$table = 'buff_monstre';
		$champ = 'id_monstre';
		$duree = $duree * 6;
	}
	
	// Requête SQL
	$Buff_requete = 'SELECT * FROM '.$table.' WHERE '.$champ.' = '.$id.' AND type = \''.$type.'\'';
	//echo $Buff_requete;
	$Buff_req = $db->query($Buff_requete);
	$Buff_row = $db->read_array($Buff_req);
	//echo $db->num_rows;
	if($db->num_rows == 0)
	{
		// La cible n'a pas le sort d'encore lancé
		if($nb_buff < $nb_buff_max || $nb_buff_max == 0 || $debuff)
		{
			// On peut avoir autant de debuff qu'on veut
		  // Ajout du buff
			$requete = "INSERT INTO ".$table."(`type`, `effet`, `effet2`, `fin`, `duree`, `".$champ."`, `nom`, `description`, `debuff`, `supprimable`) VALUES('".$type."', ".$effet.", ".$effet2.", ".(time()+$duree).", ".$duree.", ".$id.", '".$nom."', '".addslashes($description)."', ".$debuff.", ".$supprimable.")";
			$db->query($requete);
		}
		else
		{
		  // plus de place
			$G_erreur = 'overbuff';
			$lancement = false;
		}
	}
	elseif($effet >= $Buff_row['effet'])
	{
	  // L'effet est plus grand (ou égal) : on met à jour
		$requete = "UPDATE ".$table." SET effet = ".$effet.", effet2 = ".$effet2.", fin = ".(time() + $duree).", nom = '".$nom."', description = '".addslashes($description)."' WHERE id = ".$Buff_row['id'];
		$db->query($requete);
	}
	else
	{
	  // La cible a déjà le sort (ou mieux).
		$G_erreur = 'puissant';
		$lancement = false;
	}
	return $lancement;
}

/**
 * Vérifie si le personnage est mort est si c'est le cas affiche la page de résurection ou effectue la rez 
 * 
 * @param  $pourcent                    Pourcentage de HP et MP récupérés lors de la rez.
 * @param  $var                         Choix de rez (1 : page de rez, 2 : capitale, 3 : sort, 4 : fort )
 * @param  $duree_debuff                Durée du mal de rez
 * @param  $multiplicateur_mouvement    Multiplicateur de mouvement du mal de rez.
 */
function verif_mort($pourcent, $var, $duree_debuff=0, $multiplicateur_mouvement=0)
{
	global $Trace, $db, $joueur;
	if ($joueur->get_hp() <= 0)
	{
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
		//Vérifie s'il y a une amende qui empèche le spawn en ville
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
		if($var == 1)
		{ // Page de résurection
			?>
<div id="conteneur_back">
<div id="conteneur" style='margin-top:-16px;'>

<div id='perso' style='padding:15px;min-height:80px;'>
<h2 class="ville_titre">Vous êtes mort</h2>
Votre dernier souvenir est l'endroit où vous êtes mort <?php echo 'x : '.$joueur->get_x().' / y : '.$joueur->get_y(); ?>
</div>
<div id='menu'>
	<?php
    if( array_key_exists('nbr_perso', $_SESSION) )
    {
  ?>
		<span class="changer" title='Changer de personnage' onclick="envoiInfo('changer_perso.php?info=information', 'information');">&nbsp;</span>
	<?php
    }
  ?>
</div>
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
							echo '
						<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=2&amp;rez='.$row['id'].'">Vous faire ressusciter par '.$row['nom_rez'].' ('.($row['pourcent'] + $bonus).'% HP / '.($row['pourcent'] + $bonus).' MP)</li>';
						}
					}
					// Fort le plus proche (si on le personnage n'est pas dans un donjon)
					if($bat > 0 AND !is_donjon($joueur->get_x(), $joueur->get_y()))
					{
							echo '
						<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=3&amp;rez='.$row_d['id'].'">Revenir dans le fort le plus proche (x : '.$row_b['x'].' / y : '.$row_b['y'].') ('.($row_b['rez'] + $bonus).'% HP / '.($row_b['rez'] + $bonus).'% MP)</li>';
					}
					// Capitale, refuge des criminels ou sort de l'arène
					if( $arene )
					{
            // sortie de l'arène
				  	echo '
						<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=1">Sortir de l\'arène</a></li>';
          }
          else
          {
					  // Capitale ou refuge des criminels
				  	echo '
						<li style="padding-top:5px;padding-bottom:5px;"><a href="mort.php?choix=1">'.$echo.' ('.($capitale_rez_p + $bonus).'% HP / '.($capitale_rez_p + $bonus).'% MP)</a></li>';
          }
					?>
						<li style="padding-top:5px;padding-bottom:5px;"><a href="index.php?deco=ok">Vous déconnecter</a></li>
						<li style="padding-top:5px;padding-bottom:5px;">Vous pouvez attendre qu&rsquo;un autre joueur vous ressucite</li>
					</ul>
					<a href="index.php">Index du jeu</a> - <a href="http://forum.starshine-online.com">Accéder au forum</a>  - <a href="http://www.starshine-online.com/tigase/">Accéder au Tchat</a>
</fieldset>
<fieldset>
					Vos dernières actions :
					<ul>
					<?php
					$requete = "SELECT * FROM journal WHERE id_perso = ".$joueur->get_id()." ORDER by time DESC, id DESC LIMIT 0, 15";
					$req = $db->query($requete);
					while($row = $db->read_assoc($req))
					{
						echo '<li>'.affiche_ligne_journal($row).'</li>';
					}
					?>
					</ul>
</fieldset>
</div>

<div id="information" style="float: right;">
</div>

			</div></div>
			<?php
			exit();
		}
		elseif($var == 2)
		{ // Rez en ville ou dans le refuge des criminels
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
		elseif($var == 4)
		{ // Fort le plus proche
			$joueur->set_x($row_b['x']);
			$joueur->set_y($row_b['y']);
			$pourcent = $row_b['rez'];
		}
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
	}
}

/**
 * Génère l'image de la barre de PA
 * 
 * @param  $joueur    Tableau associatif décrivant le personnage
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_pa($joueur)
{
	global $G_PA_max;
	//Barre PA
	$ratio_pa = floor(10 * ($joueur->get_pa() / $G_PA_max));
	if($ratio_pa > 10) $ratio_pa = 10;
	if($ratio_pa < 0) $ratio_pa = 0;
	$barre_pa = './image/barre/pa'.$ratio_pa.'.png';
	return $barre_pa;
}

/**
 * Génère l'image de la barre de durée des buffs
 * 
 * @param  $buff    Tableau associatif décrivant le buff
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_buff_duree($buff)
{//-- Barre durée restante du buff
	$ratio_buff_duree = floor(10 * (($buff->get_fin() - time()) / ($buff->get_duree())));
	if($ratio_buff_duree > 10) 	{ $ratio_buff_duree = 10; };
	if($ratio_buff_duree < 0) 	{ $ratio_buff_duree = 0; };
	$barre_buff_duree = "image/barre/buff_duree".$ratio_buff_duree.".png";
	return "<img src='".$barre_buff_duree."' class='buff_duree_restante' alt='duree buff' />";
}

/**
 * Génère l'image de la barre de HP
 * 
 * @param  $joueur    Tableau associatif décrivant le personnage
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_hp($joueur)
{
	//Barre HP
	$ratio_hp = floor(10 * ($joueur->get_hp() / floor($joueur->get_hp_maximum())));
	if($ratio_hp > 10) $ratio_hp = 10;
	if($ratio_hp < 0) $ratio_hp = 0;
	$barre_vie = './image/barre/vie'.$ratio_hp.'.png';
	return $barre_vie;
}

/**
 * Génère l'image de la barre de MP
 * 
 * @param  $joueur    Tableau associatif décrivant le personnage
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_mp($joueur)
{
	//Barre MP
	$ratio_mp = floor(10 * ($joueur->get_mp() / floor($joueur->get_mp_maximum())));
	if($ratio_mp > 10) $ratio_mp = 10;
	if($ratio_mp < 0) $ratio_mp = 0;
	$barre_mp = './image/barre/mp'.$ratio_mp.'.png';
	return $barre_mp;
}

/**
 * Génère l'image de la barre de HP d'un membre du groupe
 * 
 * @param  $joueur    Tableau associatif décrivant le personnage
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_hp_groupe($joueur)
{
	//Barre HP
	$ratio_hp = floor(10 * ($joueur->get_hp() / floor($joueur->get_hp_maximum())));
	if($ratio_hp > 10) $ratio_hp = 10;
	if($ratio_hp < 0) $ratio_hp = 0;
	$barre_vie = 'image/barre/g_vie'.$ratio_hp.'.png';
	return '<img src="'.$barre_vie.'" alt="HP = '.$joueur->get_hp().' / '.floor($joueur->get_hp_maximum()).'" title="HP = '.$joueur->get_hp().' / '.floor($joueur->get_hp_maximum()).'" style="height : 5px; width : 100px;" />';
}

/**
 * Génère l'image de la barre de MP d'un membre du groupe
 * 
 * @param  $joueur    Tableau associatif décrivant le personnage
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_mp_groupe($joueur)
{
	//Barre MP
	$ratio_mp = floor(10 * ($joueur->get_mp() / floor($joueur->get_mp_maximum())));
	if($ratio_mp > 10) $ratio_mp = 10;
	if($ratio_mp < 0) $ratio_mp = 0;
	$barre_mp = 'image/barre/g_mp'.$ratio_mp.'.png';
	return '<img src="'.$barre_mp.'" alt="MP = '.$joueur->get_mp().' / '.floor($joueur->get_mp_maximum()).'" title="MP = '.$joueur->get_mp().' / '.floor($joueur->get_mp_maximum()).'" style="height : 5px; width : 100px;" />';
}

/**
 * Génère l'image de la barre d'XP
 * 
 * @param  $xp_joueur       Expérience du joueur.
 * @param  $xp_p_n          Expérience du prochain niveau.
 * @param  $progression     Pourcentage d'XP pour passer au niveau suivant.
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_exp($xp_joueur, $xp_p_n, $progression)
{
	//Barre EXP
	$ration_xp = floor($progression / 10);
	if($ration_xp > 10) $ration_xp = 10;
	if($ration_xp < 0) $ration_xp = 0;
	$barre_xp = './image/barre/xp'.$ration_xp.'.png';
	return $barre_xp;
}

/**
 * Génère l'image de la barre de compétence
 * 
 * @param  $joueur        Tableau associatif contenant les valeurs des compétences.
 * @param  $competence    Compétence concernée.
 * @param  $comp_max      Maximum de la compétence.
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_comp($val_competence, $nom_competence, $comp_max)
{
	global $Gtrad;
	//Barre Comp
	$ratio_comp = floor(10 * ($val_competence / $comp_max));
	if($ratio_comp > 10) $ratio_comp = 10;
	if($ratio_comp < 0) $ratio_comp = 0;
	$barre_comp = 'image/barre/pa'.$ratio_comp.'.png';
	return '<img src="'.$barre_comp.'" alt="'.$Gtrad[$nom_competence].' = '.$val_competence.' / '.$comp_max.'" title="'.$Gtrad[$nom_competence].' = '.$val_competence.' / '.$comp_max.'" style="height : 8px; width : 100px;" />';
}

/**
 * Génère l'image de la barre de compétence
 * 
 * @param  $joueur        Tableau associatif décrivant le personnage
 * @param  $competence    Compétence concernée.
 * @param  $comp_max      Maximum de la compétence.
 * 
 * @return    chemin de l'image de la barre.  
 */
function genere_image_comp2($val_competence, $nom_competence, $comp_max)
{
	global $Gtrad;
	//Barre Comp
	if($comp_max == 0) $comp_max = 1;
	$ratio_comp = floor(10 * ($val_competence / $comp_max));
	if($ratio_comp > 10) $ratio_comp = 10;
	if($ratio_comp < 0) $ratio_comp = 0;
	$barre_comp = 'image/barre/pa'.$ratio_comp.'.png';
	return '<img src="'.$barre_comp.'" alt="'.$Gtrad[$nom_competence].' = '.$val_competence.' / '.$comp_max.'" title="'.$Gtrad[$nom_competence].' = '.$val_competence.' / '.$comp_max.'" style="height : 8px; width : 100px;" />';
}

/**
 * Affiche une ligne du journal avec la bonne couleur.
 * 
 * @param  $row   Tableau associatif contenant l'action à décrire.
 * 
 * @return    Code HTML de la ligne à afficher.    
 */
function affiche_ligne_journal($row)
{
	$date = strtotime($row['time']);
	$date = date("j/m H:i", $date);
	$perso = new perso($_SESSION['ID']);
	switch($row['action'])
	{
		case 'attaque' :
			if ($row['actif'] != $perso->get_nom()) // Equivaut à : l'attaquant est le pet
        return '<li class="jdegat"><span class="small">['.$date.']</span> Vous attaquez '.$row['passif'].' avec '.$row['actif'].' et lui faites '.$row['valeur'].' dégâts, il lui en fait '.$row['valeur2'].' - <a href="#" onClick="return envoiInfo(\'journal_combat.php?id='.$row['id'].'\',\'information\')">Voir</a></li>';
      else
        return '<li class="jdegat"><span class="small">['.$date.']</span> Vous attaquez '.$row['passif'].' et lui faites '.$row['valeur'].' dégâts, il vous en fait '.$row['valeur2'].' - <a href="#" onClick="return envoiInfo(\'journal_combat.php?id='.$row['id'].'\',\'information\')">Voir</a></li>';
		break;
		case 'defense' :
			 if ($row['actif'] != $perso->get_nom()) // Equivaut à : le defenseur est le pet
				return '<li class="jrdegat"><span class="small">['.$date.']</span> '.$row['passif'].' a attaqué '.$row['actif'].' et fait '.$row['valeur'].' dégâts et '.$row['actif'].' fait '.$row['valeur2'].' - <a href="#" onClick="return envoiInfo(\'journal_combat.php?id='.$row['id'].'\',\'information\')">Voir</a></li>';
			else
				return '<li class="jrdegat"><span class="small">['.$date.']</span> '.$row['passif'].' vous a attaqué et fait '.$row['valeur'].' dégâts et vous lui faites '.$row['valeur2'].' - <a href="#" onClick="return envoiInfo(\'journal_combat.php?id='.$row['id'].'\',\'information\')">Voir</a></li>';
		break;
		case 'tue' :
			return '<li class="jkill"><span class="small">['.$date.']</span> Vous tuez '.$row['passif'].'.</li>';
		break;
		case 'mort' :
			return '<li class="jmort"><span class="small">['.$date.']</span> '.$row['passif'].' vous a tué.</li>';
		break;
		case 'pet_leave' :
			return '<li class="jmort"><span class="small">['.$date.']</span> Votre '.$row['valeur'].' a échappé à votre contrôle et vous a quitté.</li>';
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
				return '<li class="jgbuff"><span class="small">['.$date.']</span> '.$row['actif'].' vous téléporte dans le jeu.</li>';
			else
				return '<li class="jgbuff"><span class="small">['.$date.']</span> '.$row['actif'].' vous téléporte dans l\'arène '.$row['valeur'].'.</li>';
		break;
		case 'rbalance' :
		case 'rgbalance' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> '.$row['passif'].' équilibre vos points de vie à '.$row['valeur'].' HP.</li>';
			break;
		case 'gbalance' :
		case 'balance' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> Vous équilibrez vos points de vie à '.$row['valeur'].' HP.</li>';
			break;
		case 'rez' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> Vous avez été ramené à la vie par '.$row['actif'].'</li>';
			break;
		case 'rrez' :
			return '<li class="jgsoin"><span class="small">['.$date.']</span> Vous avez ressucité '.$row['actif'].'</li>';
			break;
			
	}
}

/**
 * Renvoie l'expérience necessaire pour atteindre le niveau suivant
 * 
 * @param  $level   Niveau actuel.
 * 
 * @return    Expérience nécessaire.    
 */
function prochain_level($level)
{
	global $G_level_xp;
	return (($level * ($level + 1)) / 2) * $G_level_xp;
}

/**
 * Renvoie le niveau correspondant à une certaine valeur de l'expérience.
 * Le niveau est renvoyée sous forme décimale (les chiffres après la virgule indiquant
 * de combien le personnage est proche du prochain niveau). 
 * 
 * @param  $exp     Expérience.
 * 
 * @return    Niveau.  
 */
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

/**
 * Renvoie le pourcentage d'expérience déjà acquise pour passer au prochain niveau
 * 
 * @param  $level   Niveau sous forme décimale (tel que donné par level_courant()).
 * 
 *  @return     Pourcentage d'expérience déjà acquise 
 */
function progression_level($level)
{
	$progress = $level - floor($level);
	$progress = floor($progress * 100);
	return $progress;
}

/**
 * Renvoie la class HTML indiquant si l'acaht est possible ou non.
 * Les valeurs à comparer peuvent être le pric, le coefficient, la force, ... 
 * 
 * @param  $base      Valeur nécessaire poour acheter l'ojet.
 * @param  $joueur    Valeur du personnage.
 * 
 * @return    Classe HTML.   
 */
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

/**
 * Vérifie qu'il n'y a pas de caractères spéciaux dans la chaine fournie.
 * 
 * @param  $param   Chaine à vérifier.
 * 
 * @return    true s'il n'y a pas de caratères spéciaux, false s'il y en a.    
 */
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

/**
 * Calcul le pourcentage de dégâts absorbé en fonction de la PP
 * 
 * @param  $pp    PP.
 * 
 * @return    Pourcentage de dégâts absorbé.
 */
function calcul_pp($pp)
{
	return (1 - (sqrt($pp / 10) / 40));
}

/**
 * Renvoie l'image d'un sort
 * 
 * $param  $type    Nom générique du sort.
 * 
 * @return    Code HTML affichant l'image du sort.
 */
function image_sort($type)
{
	switch($type)
	{
		case 'vie' :
		case 'vie_pourcent' :
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
		case 'balance' :
			return '<img src="image/buff/balance.png" alt="" style="vertical-align : middle;" />';
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
		case 'maladie_mollesse' :
			return '<img src="image/buff/maladie_mollesse.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'maladie_degenerescence' :
			return '<img src="image/buff/maladie_degenerescence.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'maladie_amorphe' :
			return '<img src="image/buff/maladie_amorphe.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'souffrance_extenuante' :
			return '<img src="image/buff/souffrance_extenuante.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'esprit_sacrifie' :
			return '<img src="image/buff/esprit_sacrifie.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'lente_agonie' :
			return '<img src="image/buff/lente_agonie.png" alt="" style="vertical-align : middle;" />';
		break;
		case 'transfert_energie' :
			return '<img src="image/buff/transfert_energie.png" alt="" style="vertical-align : middle;" />';
		break;
	}
}

/**
 * Calcul le pourcentage de réussite d'un jet.
 * 
 * @param  $base          Valeur maximale du jet.
 * @param  $difficulte    Valeur maximale du jet en opposition.
 * 
 * @return    Pourcentage de réussite.  
 */
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

/**
 * Renvoie le code HTML permettant le rafraichissement des informations sur le personnage.
 * 
 * @return    Code HTML.
 */   
function refresh_perso()
{
	echo '<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
}

/**
 * Affiche une ligne d'un script de combat
 *
 * @param  $action    Ligne à afficher.
 * @param  $joueur    Tableau associatif décrivant le joueur.
 * 
 * @return    Code HTML décrivant la ligne.  
 */
function affiche_condition($action, $joueur, $check_pet)
{
	global $db, $Trace;
	$liste_etats = get_etats();
	$echo = '';
	if ($action[0] == '!')
	{ // Attaque simple sans condition
		$echo .= 'attaquer';
	}
	elseif ($action[0] == '~')
	{ // Sort sans condition
		$sort_sort = mb_substr($action, 1, strlen($action));
		$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if(!$check_pet) $mpsort = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		else $mpsort = $row['mp'];
		$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
	}
	elseif ($action[0] == '_')
	{ // Compétence sans condition
		$sort_sort = mb_substr($action, 1, strlen($action));
		$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$row['mp'].' réserves)</span>';
	}
	elseif ($action[0] == '#')
	{ // Conditions
	  // Séparations des conditions et de l'action
		$action2 = explode('@', $action);
		// Séparations des conditions entre elles
		$arguments = explode('µ', $action2[0]);
		$alors = $action2[1];
		$nb_arguments = count($arguments);
		$j = 0;  // Compteur des conditions
		$bool2 = 1;
		// Affichage des conditions
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
		// Affichage de l'action
		if ($alors[0] == '!')
		{ // Attaque simple
			$echo .= 'Attaquer<br />';
		}
		elseif ($alors[0] == '~')
		{ // Sort 
			$sort_sort = mb_substr($alors, 1, strlen($alors));
			$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			if(!$check_pet) $mpsort = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
			else $mpsort = $row['mp'];
			$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
		}
		elseif ($alors[0] == '_')
		{ // Compétence
			$sort_sort = mb_substr($alors, 1, strlen($alors));
			$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$row['mp'].' réserves)</span>';
		}
	}
	return $echo;
}

/**
 * Affiche une ligne d'un script de combat
 *
 * @param  $action    Ligne à afficher sous forme de tableau associatif.
 * @param  $joueur    Tableau associatif décrivant le joueur.
 * 
 * @return    Code HTML décrivant la ligne.  
 */
function affiche_condition_session($action, $joueur, $check_pet)
{
	global $db, $Trace;
	$etats = get_etats();
	$echo = '';
	$check = false;
	if(is_array($action) AND array_key_exists('condition', $action))
	{ // Conditions
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
	{ // attaque simple
		$echo .= 'attaquer';
	}
	elseif ($action['final'][0] == 's')
	{ // sort
		$sort_sort = mb_substr($action['final'], 1, strlen($action['final']));
		$requete = "SELECT nom, mp, comp_assoc, description, effet, effet2, duree FROM sort_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if(!$check_pet) $mpsort = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		else $mpsort = $row['mp'];
		$echo .= 'Lancer le sort <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$mpsort.' réserves)</span>';
	}
	elseif ($action['final'][0] == 'c')
	{ // compétence
		$sort_sort = mb_substr($action['final'], 1, strlen($action['final']));
		$requete = "SELECT nom, mp, description, effet, effet2, duree FROM comp_combat WHERE id = ".$sort_sort;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$echo .= 'Utiliser <strong onmouseover="return overlib(\'<ul><li class=\\\'overlib_titres\\\'>'.addslashes(description($row['description'], $row)).'</li></ul>\', BGCLASS, \'overlib\', BGCOLOR, \'\', FGCOLOR, \'\');" onmouseout="return nd();">'.$row['nom'].'</strong> <span class="small">('.$row['mp'].' réserves)</span>';
	}
	return $echo;
}

/**
 * Vérifie si une certaine position correspond a une ville
 * 
 * @param  $x   Coordonnée x.
 * @param  $y   Coodronnée y.
 * 
 * @return    true si c'est une ville, false sinon.    
 */
function verif_ville($x, $y, $r = false)
{
	global $db;
	if($r === false)
		$W_requete = 'SELECT type FROM map WHERE x = '.$x.' and y = '.$y;
	else
		$W_requete = 'SELECT type FROM map WHERE x = '.$x.' and y = '.$y.' AND royaume = '.$r;
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

/**
 *  Vérifie s'il y a un bâtiment appartenant à un royaume donné sur une case.
 * 
 * @param  $x   Coordonnée x.
 * @param  $y   Coodronnée y.
 * @param  $R   Royaume.
 * 
 * @return    tableau associatif contenant le nm et le type du bâtiment s'il y en
 *            a un, false sinon.   
 */
function verif_batiment($x, $y, $r)
{
	global $db;
	$requete = "SELECT nom, type, id_batiment FROM construction WHERE x = ".$x." AND y = ".$y." AND royaume = ".$r;
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_assoc($req);
		return $row;
	}
	else return false;
}

/**
 *  Affiche les informations d'une variable.
 *  Les informations sont celles données par la fonction PHP var_dump et entourées
 *  de balises <pre>
 *  
 * @param  $v   Variable dont ont veut afficher les informations.  
 */
function aff_var($v)
{
	echo '<pre>';
	var_dump($v);
	echo '</pre>';
}

/**
 *  Supprime un bourg dans le nombre de bourgs possédés par un royaume
 *  
 * @param   $royaume    Royaume auquel on doit supprimer un bourg.  
 *
 * @deprecated : utiliser la méthode de la classe royaume à la place
 */
function supprime_bourg($royaume)
{
	global $db;
	$requete = "UPDATE royaume SET bourg = bourg - 1 WHERE ID = ".$royaume." AND bourg > 0";
	$db->query($requete);
}

/// Liste des encodages possibles
define('ENCLIST', 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP, ISO-8859-1, WINDOWS-1252');

/**
 * 
 */
function normalize_entry_charset($fields)
{
  // Récupération du type d'encodage à partir de l'entête
	if (isset($_SERVER["CONTENT_TYPE"])) {
		$charset = stristr($_SERVER["CONTENT_TYPE"], 'charset=');
		if ($charset !== false) {
			$ch = explode('=', $charset);
			//var_dump($ch);
			$src = $ch[1];
			//echo "Detected(1) $src<br />\n";
		}
	}
	// Récupération du type d'encodage à l'aide des variables transmises par GET et POST
	if (!isset($src)) {
		$src = mb_http_input("G"); // GET
		if (!$src)
			$src = mb_http_input("P"); // POST
		if (!$src)
			unset($src);
		//else
			//echo "Detected(2) $src<br />\n";
	}
	foreach ($fields as $f) {
	  // Recherche des variables dans GET
		if (isset($_GET[$f])) {
			// Si l'encodage n'est pas connu on le détecte à partir de la variable
			if (!isset($src)) {
				$csrc = mb_detect_encoding($_GET[$f], ENCLIST);
				//echo "Detected(3) $csrc<br />\n";
			}
			else 
				$csrc = $src;
			// Si l'encodage n'est pas UTF8, on convertit en UTF8
			if (strcasecmp($csrc, 'UTF-8') && strcasecmp($csrc, 'UTF8')) {
				$newfield = iconv($csrc, 'UTF-8//TRANSLIT', $_GET[$f]);
				//echo "convert to UTF-8: $newfield <br />\n";
				$_GET[$f] = $newfield;
			}
		}
	  // Recherche des variables dans POST
		elseif (isset($_POST[$f])) {
			// Si l'encodage n'est pas connu on le détecte à partir de la variable
			if (!isset($src)) {
        $csrc = mb_detect_encoding($_POST[$f], ENCLIST);
				echo "Detected(3) $csrc<br />\n";
			}
      else
        $csrc = $src;
			// Si l'encodage n'est pas UTF8, on convertit en UTF8
			if (strcasecmp($csrc, 'UTF-8') && strcasecmp($csrc, 'UTF8')) {
        $newfield = iconv($csrc, 'UTF-8//TRANSLIT', $_POST[$f]);
        //echo "convert to UTF-8: $newfield <br />\n";
        $_POST[$f] = $newfield;
      }
		}
	}
}

/**
 * Calcul des joueurs dans la visu d'un autre
 * La distance utilisée est la distance pythagoricienne. 
 *
 * @param $joueur joueur qui sert de reference
 * @param $distance taille de la visu a generer
 *
 * @return les joueurs dans la visu sous forme d'un tableau de lignes PERSO
 */
function list_joueurs_visu($joueur, $distance) {
	global $db;

	// Calcul de la visu
	$x = $joueur->get_x(); $y = $joueur->get_y();
	$pos1 = convert_in_pos($x, $y);
	$lx = $x - $distance; $gx = $x + $distance;
	$ly = $y - $distance; $gy = $y + $distance;
	// Recherche des persos
	$requete = "select *, (ABS($x - x) + ABS($y - y)) as distance from perso where x >= $lx and x <= $gx and y >= $ly and y <= $gy AND statut = 'actif' ORDER BY distance ASC";
	$req = $db->query($requete);
	// Ajout des persos dans le tableau si la distance pythagoricienne est bonne
	$ret = array();
	if ($db->num_rows > 0) {
		while ($row = $db->read_assoc($req)) {
			$pos2 = convert_in_pos($row['x'], $row['y']);
			$dst = calcul_distance_pytagore($pos1, $pos2);
      $row['distance'] = $dst;
			if ($dst <= $distance)
			{
				$ret[] = $row;
			}
		}
	}
	return $ret;
}

/**
 * Calcul des constructions/armes de siege dans la visu d'un joueur
 * La distance utilisée est la distance pythagoricienne. 
 *
 * @param $joueur joueur qui sert de reference
 * @param $distance taille de la visu a generer
 *
 * @return les construction dans la visu sous forme d'un tableau de lignes
 */
function list_construction_visu($joueur, $distance) {
	global $db;
  global $Gtrad;

	$ret = array();

	// Calcul de la visu
	$x = $joueur->get_x(); $y = $joueur->get_y();
	$pos1 = convert_in_pos($x, $y);
	$lx = $x - $distance; $gx = $x + $distance;
	$ly = $y - $distance; $gy = $y + $distance;


  	// Recherche des constructions
	$requete = "select x, y, c.id, image, r.race ".
    "from construction c, royaume r ".
    "where r.id = c.royaume and x >= $lx and x <= $gx and y >= $ly and y <= $gy";
	$req = $db->query($requete);
	// Ajout des persos dans le tableau si la distance pythagoricienne est bonne
	$ret = array();
	if ($db->num_rows > 0) {
		while ($row = $db->read_assoc($req)) {
			$pos2 = convert_in_pos($row['x'], $row['y']);
			$dst = calcul_distance_pytagore($pos1, $pos2);
			if ($dst <= $distance && $dst != 0)
			{
        $bat = recupbatiment($row['id'], 'construction');
        $bat['distance'] = $dst;
        $bat['x'] = $row['x'];
        $bat['y'] = $row['y'];
        $bat['royaume'] = $Gtrad[$row['race']];
        $bat['image'] = 'batiment/'.$row['image'].'_04';
				$ret[] = $bat;
			}
		}
	}
	// Recherche des placements
	$requete = "select x, y, c.id, r.race, royaume ".
    "from placement c, royaume r ".
    "where r.id = c.royaume and x >= $lx and x <= $gx and y >= $ly and y <= $gy";
	$req = $db->query($requete);
	// Ajout des persos dans le tableau si la distance pythagoricienne est bonne
	if ($db->num_rows > 0) {
		while ($row = $db->read_assoc($req)) {
			$pos2 = convert_in_pos($row['x'], $row['y']);
			$dst = calcul_distance_pytagore($pos1, $pos2);
			if ($dst <= $distance)
			{
        $bat = recupbatiment($row['id'], 'placement');
        $bat['distance'] = $dst;
        $bat['x'] = $row['x'];
        $bat['y'] = $row['y'];
        $bat['royaume'] = $Gtrad[$row['race']];
        $bat['nom'] .= ' en construction';
        // Mieux à faire ici ??
        $bat['image'] = 'drapeaux/drapeau_'.$row['royaume'];
				$ret[] = $bat;
			}
		}
	}

	return $ret;
}

function corrige_bonus_ignorables($attaquant, $defenseur, $mode, &$args, &$args_def)
{
	if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
	if (isset(${$mode}->bonus_ignorables)) {
		foreach (${$mode}->bonus_ignorables as $key => $value) {
			$test = "$key =";
			$testlen = strlen($test);
			foreach ($args as &$arg) {
				if (strncmp($arg, $test, $testlen) == 0) {
					$parts = explode('=', $arg);
					$arg = $parts[0].'='.($parts[1] - $value);
					//echo "Correction de $key: reduction de $value: '$arg'<br />";
				}
			}
		}
	}
	if (isset(${$mode_def}->bonus_ignorables)) {
		foreach (${$mode_def}->bonus_ignorables as $key => $value) {
			$test = "$key =";
			$testlen = strlen($test);
			foreach ($args_def as &$arg) {
				if (strncmp($arg, $test, $testlen) == 0) {
					$parts = explode('=', $arg);
					$arg = $parts[0].'='.($parts[1] - $value);
					//echo "Correction de $key: reduction de $value: '$arg'<br />";
				}
			}
		}
	}
}

function sauve_sans_bonus_ignorables($joueur, $fields)
{
	global $db;
	$query = 'UPDATE perso SET ';
	$first = true;
	foreach ($fields as $field) {
		if ($first == false) {
			$query .= ', ';
		}
		else {
			$first = false;
		}
		$val = $joueur[$field];
		if (isset($joueur['bonus_ignorables']) &&
				isset($joueur['bonus_ignorables'][$field])) {
			$val -= $joueur['bonus_ignorables'][$field];
		}
		$query .= "`$field` = '$val'";
	}
	$query .= ' WHERE ID = '.$_SESSION['ID'];
	$db->query($query);
}

function is_nobuild_type($type)
{
	switch ($type)
	{
		case 1: // ville
		case 2: // donjon
		case 3: // point special
			return true;
	}
	return false;
}	

function pose_drapeau_roi($x, $y)
{
	global $joueur;
	global $db;
	global $Trace;

	if ($x > 190 || $x < 0 || $y > 190 || $y < 0) security_block(URL_MANIPULATION); // Case invalide
	if ($joueur->get_rang_royaume() != 6) security_block(URL_MANIPULATION); // Pas roi

	if (!verif_ville($joueur->get_x(), $joueur->get_y())) {
		echo "<h5>Vous n'êtes pas à la capitale !</h5>";
		return false;
	}

	$race = $Trace[$joueur->get_race()]['numrace'];

	$req = $db->query("select 1 from map where royaume = $race and ((x = $x + 1 and y = $y) or (x = $x - 1 and y = $y) or (x = $x and y = $y + 1) or (x = $x and y = $y - 1))");
	if ($db->num_rows($req) < 1) security_block(URL_MANIPULATION); // Pas de case adjacente
	$req = $db->query("select 1 from map where royaume = 0 and x = $x and y = $y and type != 1 and type != 2"); // ceinture et bretelles
	if ($db->num_rows($req) < 1) security_block(URL_MANIPULATION); // case pas libre !!
	$req = $db->query("select 1 from placement where x = $x and y = $y");
	if ($db->num_rows($req) > 0) security_block(URL_MANIPULATION); // case pas libre !!

	$req = $db->query("SELECT temps_construction, b.id id, o.id oid from depot_royaume d, objet_royaume o, batiment b where o.id = d.id_objet and o.id_batiment = b.id and o.type = 'drapeau' and b.hp = 1 and d.id_royaume = $race");
	if ($db->num_rows($req) < 1) {
		echo "<h5>Plus de drapeaux au dépôt</h5>";
		return false;
	}
	$row = $db->read_assoc($req);

	$drapeau_id = $row['id'];
	$req = $db->query("delete from depot_royaume where id_objet = $row[oid] and id_royaume = $race limit 1");
	$distance = abs($Trace[$joueur->get_race()]['spawn_x'] - $x) + abs($Trace[$joueur->get_race()]['spawn_y'] - $y);
	$time = time() + ($row['temps_construction'] * $distance);
	$requete = "INSERT INTO placement (type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez) VALUES('drapeau', $x, $y, '$race', ".
		time().", '$time', $drapeau_id, 1, 'drapeau', 0)";
	$req = $db->query($requete);
}

function pose_drapeau_roi_all()
{
	global $joueur;
	global $db;
	global $Trace;

	if ($x > 190 || $x < 0 || $y > 190 || $y < 0) security_block(URL_MANIPULATION); // Case invalide
	if ($joueur->get_rang_royaume() != 6) security_block(URL_MANIPULATION); // Pas roi

	if (!verif_ville($joueur->get_x(), $joueur->get_y())) {
		echo "<h5>Vous n'êtes pas à la capitale !</h5>";
		return false;
	}
	$race = $Trace[$joueur->get_race()]['numrace'];

	
	$req = $db->query("SELECT temps_construction, b.id id, o.id oid from depot_royaume d, objet_royaume o, batiment b where o.id = d.id_objet and o.id_batiment = b.id and o.type = 'drapeau' and b.hp = 1 and d.id_royaume = $race");
	$nb_drapeaux = $db->num_rows($req);
	if ($nb_drapeaux < 1) {
		echo "<h5>Plus de drapeaux au dépôt</h5>";
		return false;
	}
	$row = $db->read_assoc($req);

	make_tmp_adj_tables($race);
	$req = $db->query("select * from tmp_adj_lib");
	$nb_cases = $db->num_rows($req);

	$nb = min($nb_cases, $nb_drapeaux);
	$req = $db->query("delete from depot_royaume where id_objet = $row[oid] and id_royaume = $race limit $nb");
	$time = time();
	$expr_distance = '(abs('.$Trace[$joueur->get_race()]['spawn_x'].' - x) + abs('.$Trace[$joueur->get_race()]['spawn_y'].' - y))';
	$req = $db->query("insert into placement (type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez) select 'drapeau', x, y, $race, $time, $time + ($row[temps_construction] * $expr_distance), $row[id], 1, 'drapeau', 0 from tmp_adj_lib limit $nb;");
	return $nb;
}

function make_tmp_adj_tables($roy_id)
{
	global $db;

	$db->query("drop table if exists tmp_royaume, tmp_adj, tmp_adj_lib");
	// On va utiliser des tables temporaires car la requete kifaitout prends ~30 s à s'effectuer
	$req1 = "create temporary table tmp_royaume as select x,y from map where royaume = $roy_id";
	$db->query($req1); // on prends le royaume
	$req15 = "alter table tmp_royaume ADD INDEX (x), ADD INDEX (y), ADD INDEX (x, y)";
	$db->query($req15); // on crée des index pour éviter de faire des requêtes de 2 minutes 40 qui bouffent 98% du CPU
	$req2 = "create temporary table tmp_adj as select distinct m.x, m.y from map m, tmp_royaume t where m.royaume = 0 and m.info != 5 and m.type != 1 and m.type != 2 and ((m.x = t.x + 1 and m.y = t.y) or (m.x = t.x - 1 and m.y = t.y) or (m.x = t.x and m.y = t.y + 1) or (m.x = t.x and m.y = t.y - 1))";
	$db->query($req2); // on prends les cases neutres autour du royaume qui ne sont pas de l'eau ni une ville, ni du donjon
	$req3 = "create temporary table tmp_adj_lib as select * from tmp_adj";
	$db->query($req3); // on enleve les cases occupées par un placement ou un batiment: recopie
	$req35 = "alter table tmp_adj_lib ADD INDEX (x), ADD INDEX (y), ADD INDEX (x, y)";
	$db->query($req35); // on crée des index pour éviter de faire des requêtes de 2 minutes 40 qui bouffent 98% du CPU
	$req4 = "delete t from tmp_adj_lib t, placement p where t.x = p.x and t.y = p.y";
	$db->query($req4); // on enleve les cases occupées par un placement ou un batiment: virer placements
	$req5 = "delete t from tmp_adj_lib t, construction c where t.x = c.x and t.y = c.y";
	$db->query($req5); // on enleve les cases occupées par un placement ou un batiment: virer constructions
}

function ouvrePorteMaraudeurGeolier($x, $duree)
{
	global $db;
	if ($x == 43) $x = 44; 
	// On fait comme si on était en 44 car on a bougé le goellier pour éviter
	// les tirs sous buff longue portée depuis la prison
	$px = $x - 1;
	$py = 365;
	$db->query("update map set decor = 6570, info = 65 where x = $px and y = $py");
	$dt = "DATE_ADD(NOW(), interval $duree hour)";
	$sql = "update perso set hp = 0, y = y - 1 where x = $px and y = $py ;";
	$sql .= "update map set decor = 4605, info = 46 where x = $px and y = $py";
	$db->query("insert into calendrier(`date`, `sql`) values ($dt, '$sql')");
}

function pute_effets(&$joueur, $honneur_need, $specials = null, $specials_det = null)
{
	global $db;
  // Augmentation du compteur de l'achievement
  $achiev = $joueur->get_compteur('honneur_en_putes');
  $achiev->set_compteur($achiev->get_compteur() + $honneur_need);
  $achiev->sauver();
							
  // Augmentation du compteur de l'achievement
  $achiev = $joueur->get_compteur('nbr_putes');
  $achiev->set_compteur($achiev->get_compteur() + 1);
  $achiev->sauver();
								
  //tirage au sort :
  $moy = floor(($joueur->get_puissance() + $joueur->get_vie()) / 2);
  $de = rand(1, 10) + rand(1, $moy);
  if($de <= 2)
  {
    $debuff = true;
    $texte = 'Une affaire moyenne et vite expédiée. Vous avez l\'impression que vous avez fait la pire performance de votre vie. La fille de joie le pense aussi et vous chasse illico de la taverne, d\'où vous ne remettrez probablement pas les pieds pendant un moment. Vous sortez tout penaud en courant le plus vite possible pour ne plus entendre les rires et moqueries que s\'échangent les filles en vous montrant du doigt.';
  }
  elseif($de <= 5)
  {
    if(rand(1, 100) < 80)
    {
      $debuff = true;
    }
    $texte = 'Vous avez été mauvais. Très mauvais même. Vous sortez de la chambre le moral à zéro. Vous ne reviendrez pas de sitôt, comme vous l\'a fait comprendre la fille en s\'endormant en plein milieu de l\'acte.';
  }
  elseif($de <= 10)
  {
    if(rand(1, 100) < 20)
    {
      $debuff = true;
    }
    $texte = 'Une prestation moyenne. C\'est sur, vous avez connu mieux. C\'est peut être ce que vous avez mangé la veille qui vous a rendu si patraque. Une chose est sûre, il faut vous changer les idées ailleurs qu\'ici, et ne plus y remettre les pieds pendant un moment.';
  }
  elseif($de <= 14)
  {
    $texte = 'Une performance comme les autres. Une chose est sûre, la fille de joie vous a déja oublié sitôt sortit de cet endroit.';
  }
  elseif($de <= 17)
  {
    if(rand(1, 100) < 20)
    {
      $buff = true;
    }
    $texte = 'Une bonne performance. Vous sortez de la chambre un petit sourire aux lèvres. Une chose est sûre, cet endroit était convivial. Vous reviendrez.';
  }
  elseif($de <= 19)
  {
    if(rand(1, 100) < 40)
    {
      $buff = true;
    }
    $texte = 'Vous sortez de la pièce heureux de votre performance. Vous étiez en forme, c\'est une certitude. Vous laisserez d\'heureux souvenirs à la fille que vous avez cotoyé, cette nuit inoubliable restera gravée dans votre mémoire.';
  }
  elseif($de <= 21)
  {
    if(rand(1, 100) < 60)
    {
      $buff = true;
    }
    $texte = 'Une performance exceptionelle. Vous ne savez pas pourquoi, mais vous étiez dans une forme olympique. Ce fut toride et bestial. Vous vous sentez ragaillardi, et fermez la porte doucement, pour ne pas réveiller la fille de joie qui s\'est endormie aussitôt après l\'acte, tellement vus l\'avez épuisée. Un petit sourire orne le coin de vos lèvres quand vous l\'avez vu discrètement noter votre nom dans son carnet des personnes à recontacter.';
  }
  else
  {
    if(rand(1, 100) < 80)
    {
      $buff = true;
    }
    $texte = 'Magnifique est un qualificatif trop pauvre pour mesurer votre performance. Vous avez été tellement bon que la personne à qui vous avez fait l\'honneur de votre présence a eu une extinction de voix à force de crier votre nom. De plus, fait marquant, au milieu de l\'affaire, elle est partie et a appelé toute ses copines qui ont quitté leur chambre, laissant leurs clients sur la paille, pour venir profiter de votre journée de grâce. Ce n\'est donc pas moins que la taverne entière (tout sexe confondu) que vous avez honoré (sans payer plus cher). Une chose est sûre, vous avez rendu des gens heureux aujourd\'hui.';
  }

  //lancement du buff ou debuff
  if($buff)
  {
    //Liste des buffs possibles (Identifiants dans la bdd)
    $liste_buff = array(82, 83, 80, 86, 20, 51);
    // speciaux
    if ($specials !== null) {
      $liste_buff = array_merge($liste_buff, $specials);
    }
    //Tirage au sort de quel buff lancer
    $total_buff = count($liste_buff);
    $tirage = rand(0, $total_buff - 1);
    $sort = $liste_buff[$tirage];
    //On cherche le buff dans la bdd
    if (is_numeric($sort)) {
      $requete = "SELECT * FROM sort_jeu WHERE id = ".$sort;
      $req = $db->query($requete);
      $row = $db->read_assoc($req);
    }
    else {
      $row = $specials_det[$sort];
    }
    lance_buff($row['type'], $joueur->get_id(), $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, 0, $joueur->get_grade()->get_nb_buff());
    $texte .= '<br />En plus, vous recevez le buff : '.$row['nom'].' !!!';
								
								// Augmentation du compteur de l'achievement
    $achiev = $joueur->get_compteur('taverne_bonus');
    $achiev->set_compteur($achiev->get_compteur() + 1);
    $achiev->sauver();
  }
  elseif($debuff)
  {
    //Liste des debuff possibles (Identifiants dans la bdd)
    $liste_debuff = array(39, 35, 128, 133, 138);
    //Tirage au sort de quel buff lancer
    $total_debuff = count($liste_debuff);
    $tirage = rand(0, $total_debuff - 1); // array is 0-based
    $sort = $liste_debuff[$tirage];
    //echo $tirage;
    //On cherche le buff dans la bdd
    $requete = "SELECT * FROM sort_jeu WHERE id = ".$sort;
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    lance_buff($row['type'], $joueur->get_id(), $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0);
    $texte .= '<br />Ouch cette piètre prestation vous coupe le moral, vous recevez le debuff : '.$row['nom'].' !!!';
		
    // Augmentation du compteur de l'achievement
    $achiev = $joueur->get_compteur('taverne_malus');
    $achiev->set_compteur($achiev->get_compteur() + 1);
    $achiev->sauver();
  }
							
  //maladie
  $pourcent_risque = 5;
  if(rand(1, 100) <= $pourcent_risque)
  {
    //Liste des maladies possibles (Identifiants dans la bdd)
    $liste_maladie = array();
    $liste_maladie[0]['nom'] = 'Crise cardiaque';
    $liste_maladie[0]['description'] = '
								En sortant de la taverne, vous vous sentez faible. Très faible.<br />
								Vous vous apercevez trop tard que vous vous êtes trop démené.<br />
								Vous vous écroulez par terre, et n\'arrivez plus à respirer.<br />
								Vous mourez seul et abandonné de tous.';
    $liste_maladie[0]['effets'] = 'mort';
    $liste_maladie[1]['nom'] = 'Lumbago';
    $liste_maladie[1]['description'] = '
								A la sortie de la taverne, un violent mal de dos vous surprend.<br />
								Vous n\'auriez pas du tester la dernière position à la mode.<br />
								Votre corps n\'était pas prêt pour ca.';
    $liste_maladie[1]['effets'] = 'bloque_deplacement-12';
    $liste_maladie[2]['nom'] = 'Foulure du poignet';
    $liste_maladie[2]['description'] = '
								En sortant de la taverne, vous regrettez vos ébats physiques.<br />Vous n\'auriez pas du essayer de prouver votre force et de soulever le lit à une main pour impressioner votre partenaire, vous vous êtes foulé le poignet.';
    $liste_maladie[2]['effets'] = 'bloque_attaque-12;cout_deplacement-2.';
    $liste_maladie[0]['nom'] = 'Extinction de voix';
    $liste_maladie[3]['description'] = '
								En sortant de la taverne, vous ne pouvez plus parler.<br />
								L\'orgasme fut violent, tellement violent que le cri que vous avez poussé vous a déchiré l\'organe vocal.';
    $liste_maladie[3]['effets'] = 'bloque_sort-12';
    $liste_maladie[3]['nom'] = 'Vulnérabilité';
    $liste_maladie[4]['description'] = '
								En sortant de la taverne, vous vous sentez fragiles, très fragiles.<br />
								La fille que vous avez honoré vous a certainement refilé une maladie.<br />
								Les prochains combats vont être difficiles.';
    $liste_maladie[4]['effets'] = 'suppr_defense-12';
    $liste_maladie[4]['nom'] = 'Dernier sursaut';
    $liste_maladie[5]['description'] = '
								En sortant de la taverne, vous vous sentez revivre, vous vous sentez fort, très fort, trop fort.<br />
								Vous pensez qu\'il faut vite profiter de cet état de grace car le retour du baton va être violent.';
    $liste_maladie[5]['effets'] = 'cout_deplacement-2;cout_attaque-2;mort_regen';
    $liste_maladie[5]['nom'] = 'Grosse fatigue';
    $liste_maladie[6]['description'] = '
								En sortant de la taverne, vous vous sentez las.<br />
								Cet effort vous a épuisé, vous n\'auriez pas du faire cette partie de jambe en l\'air, ce n\'est plus de votre âge.';
    $liste_maladie[6]['effets'] = 'plus_cout_deplacement-2;plus_cout_attaque-2';
    $liste_maladie[7]['nom'] = 'Foulure de la cheville';
    $liste_maladie[7]['description'] = '
								En sortant de la taverne, vous appercevez que vous avez des difficultées à marcher.<br />
								Vous avez du vous fouler la cheville pendant l\'acte.<br />
								Decidement, mauvaise journée.';
    $liste_maladie[7]['effets'] = 'plus_cout_deplacement-2;cout_attaque-2';
    $liste_maladie[8]['nom'] = 'Hémoragie';
    $liste_maladie[8]['description'] = '
								En sortant de la taverne, vous vous mettez à saigner abondament.<br />
								Cette vile coquine a du vous mordre trop violement.';
    $liste_maladie[8]['effets'] = 'regen_negative-3';
    $liste_maladie[9]['nom'] = 'Régénération';
    $liste_maladie[9]['description'] = '
								En sortant de la taverne, vous vous sentez faible et fragile, mais vous sentez clairement que ca ira mieux à l\'avenir.<br />
								C\'est juste une mauvaise passe. ';
    $liste_maladie[9]['effets'] = 'low_hp;high_regen-3';
    //Tirage au sort de quel maladie lancer
    $total_maladie = count($liste_maladie);
    $tirage = rand(0, $total_maladie);
    $maladie = $liste_maladie[$tirage];
    $effets = explode(';', $maladie['effets']);
    foreach($effets as $effet)
    {
      $effet_explode = explode('-', $effet);
      switch($effet_explode[0])
      {
        case 'mort' :
          $joueur->set_hp(0);
          $bloque_regen = true;
          break;
        case 'bloque_deplacement' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('bloque_deplacement', $joueur->get_id(), 1, 0, $duree, $maladie['nom'], description('Vous ne pouvez plus vous déplacer', array()), 'perso', 1, 0, 0);
          break;
        case 'bloque_attaque' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('bloque_attaque', $joueur->get_id(), 1, 0, $duree, $maladie['nom'], description('Vous ne pouvez plus attaquer', array()), 'perso', 1, 0, 0);
          break;
        case 'cout_deplacement' :
          $duree = 12 * 60 * 60;
          lance_buff('cout_deplacement', $joueur->get_id(), 2, 0, $duree, $maladie['nom'], description('Vos couts en déplacements sont divisés par 2', array()), 'perso', 1, 0, 0);
          break;
        case 'bloque_sort' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('bloque_sort', $joueur->get_id(), 1, 0, $duree, $maladie['nom'], description('Vous ne pouvez plus lancer de sorts hors des combats.', array()), 'perso', 1, 0, 0);
          break;
        case 'suppr_defense' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('suppr_defense', $joueur->get_id(), 0, 0, $duree, $maladie['nom'], description('Votre PP est réduite à 0', array()), 'perso', 1, 0, 0);
          break;
        case 'cout_attaque' :
          $duree = 12 * 60 * 60;
          lance_buff('cout_attaque', $joueur->get_id(), 2, 0, $duree, $maladie['nom'], description('Vos couts pour attaquer sont divisés par 2', array()), 'perso', 1, 0, 0);
          break;
        case 'mort_regen' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('mort_regen', $joueur->get_id(), 1, 0, $duree, $maladie['nom'], description('Vous mourez lors de votre prochaine regénération', array()), 'perso', 1, 0, 0);
          break;
        case 'plus_cout_attaque' :
          $duree = 12 * 60 * 60;
          lance_buff('plus_cout_attaque', $joueur->get_id(), 2, 0, $duree, $maladie['nom'], description('Vos couts en attaque sont multipliés par 2', array()), 'perso', 1, 0, 0);
          break;
        case 'plus_cout_deplacement' :
          $duree = 12 * 60 * 60;
          lance_buff('plus_cout_deplacement', $joueur->get_id(), 2, 0, $duree, $maladie['nom'], description('Vos couts en déplacement sont multipliés par 2', array()), 'perso', 1, 0, 0);
          break;
        case 'regen_negative' :
          $duree = 48 * 60 * 60;
          lance_buff('regen_negative', $joueur->get_id(), $effet_explode[1], 0, $duree, $maladie['nom'], description('Vos 3 prochaines regénération vous fait perdre des HP / MP au lieu d\'en regagner.', array()), 'perso', 1, 0, 0);
          break;
        case 'low_hp' :
          $joueur->set_hp(1);
          $joueur->set_mp(1);
          $bloque_regen = true;
          break;
        case 'high_regen' :
          $duree = $effet_explode[1] * 60 * 60;
          lance_buff('high_regen', $joueur->get_id(), $effet_explode[1], 0, $duree, $maladie['nom'], description('Vos 3 prochaines regénération vous font gagner 3 fois plus de HP / MP', array()), 'perso', 1, 0, 0);
          break;
      }
    }
    $texte .= '<br />'.$maladie['description'];
  }
  return $texte;
}
