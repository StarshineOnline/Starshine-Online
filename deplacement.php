<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
$joueur = recupperso($_SESSION['ID']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$requete = 'SELECT x, y FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($requete);
$row = $db->read_array($req);
$coord['x'] = $row['x'];
$coord['y'] = $row['y'];
$coord['xavant'] = $row['x'];
$coord['yavant'] = $row['y'];

//Si coordonées supérieur à 100 alors c'est un donjon
if($joueur['x'] > 150 OR $joueur['y'] > 150)
{
	$donjon = true;
}
else $donjon = false;

$peu_bouger = true;
//Déplacement du joueur
if (isset($_GET['deplacement']))
{
	$mouvement = true;
	$diagonale = false;
	//Déplacement donjon
	if($donjon)
	{
		switch($_GET['deplacement'])
		{
			case 'haut' :
				$coord['y'] = $coord['y'] - 1;
			break;
			case 'bas' :
				$coord['y'] = $coord['y'] + 1;
			break;
			case 'gauche' :
				$coord['x'] = $coord['x'] - 1;
			break;
			case 'droite' :
				$coord['x'] = $coord['x'] + 1;
			break;
			
			//Diagonale
			case 'hautgauche' :
				$coord['y'] = $coord['y'] - 1;
				$coord['x'] = $coord['x'] - 1;
				$diagonale = true;
			break;
			case 'hautdroite' :
				$coord['y'] = $coord['y'] - 1;
				$coord['x'] = $coord['x'] + 1;
				$diagonale = true;
			break;
			case 'basgauche' :
				$coord['y'] = $coord['y'] + 1;
				$coord['x'] = $coord['x'] - 1;
				$diagonale = true;
			break;
			case 'basdroite' :
				$coord['y'] = $coord['y'] + 1;
				$coord['x'] = $coord['x'] + 1;
				$diagonale = true;
			break;		
		}
	}
	//Déplacement normal
	else
	{
		switch($_GET['deplacement'])
		{
			case 'haut' :
				if ($coord['y'] > 1) $coord['y'] = $coord['y'] - 1;
				else $mouvement = false;
			break;
			case 'bas' :
				if ($coord['y'] < ($G_ligne - 1)) $coord['y'] = $coord['y'] + 1;
				else $mouvement = false;
			break;
			case 'gauche' :
				if ($coord['x'] > 1) $coord['x'] = $coord['x'] - 1;
				else $mouvement = false;
			break;
			case 'droite' :
				if ($coord['x'] < ($G_colonne - 1)) $coord['x'] = $coord['x'] + 1;
				else $mouvement = false;
			break;
			
			//Diagonale
			case 'hautgauche' :
				if (($coord['y'] > 1) AND ($coord['x'] > 1))
				{
					$coord['y'] = $coord['y'] - 1;
					$coord['x'] = $coord['x'] - 1;
					$diagonale = true;
				}
				else $mouvement = false;
			break;
			case 'hautdroite' :
				if (($coord['y'] > 1) AND ($coord['x'] < ($G_colonne - 1)))
				{
					$coord['y'] = $coord['y'] - 1;
					$coord['x'] = $coord['x'] + 1;
					$diagonale = true;
				}
				else $mouvement = false;
			break;
			case 'basgauche' :
				if (($coord['y'] < ($G_ligne - 1)) AND ($coord['x'] > 1))
				{
					$coord['y'] = $coord['y'] + 1;
					$coord['x'] = $coord['x'] - 1;
					$diagonale = true;
				}
				else $mouvement = false;
			break;
			case 'basdroite' :
				if (($coord['y'] < ($G_ligne - 1)) AND ($coord['x'] < ($G_colonne - 1)))
				{
					$coord['y'] = $coord['y'] + 1;
					$coord['x'] = $coord['x'] + 1;
					$diagonale = true;
				}
				else $mouvement = false;
			break;		
		}
	}
	if($mouvement)
	{
		if($donjon) $W_pos = convertd_in_pos($coord['x'], $coord['y']);
		else $W_pos = convert_in_pos($coord['x'], $coord['y']);
		$W_requete = 'SELECT * FROM map WHERE ID ='.$W_pos;
		$W_req = $db->query($W_requete);
		$W_row = $db->read_array($W_req);
		$num_rows = $db->num_rows;
		
		$type_terrain = type_terrain($W_row['info']);
		$coutpa = cout_pa($type_terrain[0], $joueur['race']);
		$coutpa_base = $coutpa;
		$coutpa = cout_pa2($coutpa, $joueur, $W_row, $diagonale);
		//Si le joueur a un buff ou débuff qui l'empèche de bouger
		if(array_key_exists('buff_forteresse', $joueur['buff']) OR array_key_exists('buff_position', $joueur['buff']) OR array_key_exists('debuff_enracinement', $joueur['debuff']) OR array_key_exists('bloque_deplacement', $joueur['debuff']))
		{
			$peu_bouger = false;
			$cause = 'Un buff vous empèche de bouger';
		}
		//Si en donjon et case n'existe pas, le joueur ne peut pas bouger
		if($num_rows == 0)
		{
			$peu_bouger = false;
			$cause = '';
		}
		//if($peu_bouger) echo 'ok';
		if (($joueur['pa'] >= $coutpa) AND ($coutpa_base < 50) AND $peu_bouger)
		{
			//Si debuff blizard
			if(array_key_exists('blizzard', $joueur['debuff']))
			{
				$joueur['hp'] -=  round(($joueur['debuff']['blizzard']['effet'] / 100) * $joueur['hp_max']);
			}
			//Déplacement du joueur
			$joueur['pa'] = $joueur['pa'] - $coutpa;
			$requete = 'UPDATE perso SET x = \''.$coord['x'].'\', y = \''.$coord['y'].'\', pa = \''.$joueur['pa'].'\', hp = \''.$joueur['hp'].'\'  WHERE ID = '.$_SESSION['ID'];
			$db->query($requete);
			//Si ya un monstre, paf il attaque le joueur
			if($donjon)
			{
				$requete = "SELECT id FROM map_monstre WHERE x = ".$coord['x']." AND y = ".$coord['y']." ORDER BY hp DESC";
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
					$row = $db->read_row($req);
					$_SESSION['attaque_donjon'] = 'ok';
					?>
					<img src="image/pixel.gif" onLoad="envoiInfo('attaque_monstre.php?ID=<?php echo $row[0]; ?>&poscase=<?php echo $W_pos; ?>', 'information'); javascript:alert('Un monstre vous attaque sauvagement !');" />
					<?php
				}
			}
		}
		else
		{
			$coord['x'] = $coord['xavant'];
			$coord['y'] = $coord['yavant'];
		}
	}
}

$W_pos = convert_in_pos($coord['x'], $coord['y']);
$_SESSION['position'] = $W_pos;
//Si c'est un donjon
if($donjon)
{
	include('donjon.php');
}
else include('map2.php');
if(!$peu_bouger AND $cause != '') echo '<img src="image/pixel.gif" onLoad="alert(\''.$cause.'\');" />';
?>

<img src="image/pixel.gif" onLoad="refresh('./menu_carteville.php', 'carteville');" />
<img src="image/pixel.gif" onLoad="refresh('./infoperso.php', 'perso');" />