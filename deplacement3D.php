<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
$joueur = new perso($_SESSION['ID']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$coord['x'] = $joueur->get_x();
$coord['y'] = $joueur->get_y();
$coord['xavant'] = $joueur->get_x();
$coord['yavant'] = $joueur->get_y();

//Si coordonées supérieur à 100 alors c'est un donjon
if($joueur->get_x() > 190 OR $joueur->get_y() > 190)
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
				$coord['x'] = $coord['x'] + 1;
				$diagonale = true;
			
			break;
			case 'bas' :
				$coord['y'] = $coord['y'] - 1;
				$coord['x'] = $coord['x'] - 1;
				$diagonale = true;

			break;
			case 'gauche' :
				$coord['y'] = $coord['y'] + 1;
				$coord['x'] = $coord['x'] - 1;
				$diagonale = true;

			break;
			case 'droite' :
				$coord['y'] = $coord['y'] + 1;
				$coord['x'] = $coord['x'] + 1;
				$diagonale = true;
			
			break;
			
			//Diagonale
			case 'hautgauche' :
				$coord['y'] = $coord['y'] - 1;
			
			break;
			case 'hautdroite' :
				$coord['x'] = $coord['x'] + 1;
			break;
			case 'basgauche' :
				$coord['x'] = $coord['x'] - 1;
			
			break;
			case 'basdroite' :
				$coord['y'] = $coord['y'] + 1;
			
			break;		
		}
	}
	//Déplacement normal
	else
	{
		switch($_GET['deplacement'])
		{
			case 'haut' :
				if (($coord['y'] > 1) AND ($coord['x'] < ($G_colonne - 1)))
				{
					$coord['y'] = $coord['y'] - 1;
					$coord['x'] = $coord['x'] + 1;
					$diagonale = true;
				}
				else $mouvement = false;
			
			break;
			case 'bas' :
				if (($coord['y'] < ($G_ligne - 1)) AND ($coord['x'] > 1))
				{
					$coord['y'] = $coord['y'] + 1;
					$coord['x'] = $coord['x'] - 1;
					$diagonale = true;
				}
				else $mouvement = false;
			
			break;
			case 'gauche' :
				if (($coord['y'] > 1) AND ($coord['x'] > 1))
				{
					$coord['y'] = $coord['y'] - 1;
					$coord['x'] = $coord['x'] - 1;
					$diagonale = true;
				}
				else $mouvement = false;
			
			break;
			case 'droite' :
				if (($coord['y'] < ($G_ligne - 1)) AND ($coord['x'] < ($G_colonne - 1)))
				{
					$coord['y'] = $coord['y'] + 1;
					$coord['x'] = $coord['x'] + 1;
					$diagonale = true;
				}
				else $mouvement = false;
			
			break;
			
			//Diagonale
			case 'hautgauche' :
				if ($coord['y'] > 1) $coord['y'] = $coord['y'] - 1;
				else $mouvement = false;


			break;
			case 'hautdroite' :
				if ($coord['x'] < ($G_colonne - 1)) $coord['x'] = $coord['x'] + 1;
				else $mouvement = false;
			
			break;
			case 'basgauche' :
				if ($coord['x'] > 1) $coord['x'] = $coord['x'] - 1;
				else $mouvement = false;
			
			break;
			case 'basdroite' :
				if ($coord['y'] < ($G_ligne - 1)) $coord['y'] = $coord['y'] + 1;
				else $mouvement = false;
			
			break;		
		}
	}
	if($_GET['deplacement'] == 'centre') $mouvement = false;
	if($mouvement)
	{
		$W_requete = 'SELECT info FROM map WHERE x ='.$coord['x'].' and y = '.$coord['y'];
		$W_req = $db->query($W_requete);
		$W_row = $db->read_array($W_req);
		$num_rows = $db->num_rows;
		
		$type_terrain = type_terrain($W_row['info']);
		$coutpa = cout_pa($type_terrain[0], $joueur->get_race());
		$coutpa_base = $coutpa;
		$case = new map_case(array('x' => $coord['x'], 'y' => $coord['y']));
		$coutpa = cout_pa2($coutpa, $joueur, $case, $diagonale);
		//Si le joueur a un buff ou débuff qui l'empèche de bouger
		if($joueur->is_buff('buff_forteresse') OR $joueur->is_buff('buff_position') OR $joueur->is_buff('debuff_enracinement') OR $joueur->is_buff('bloque_deplacement') OR $joueur->is_buff('petrifie'))
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
		if (($joueur->get_pa() >= $coutpa) AND ($coutpa_base < 50) AND $peu_bouger)
		{
			//Si debuff blizard
			if($joueur->is_buff('blizzard'))
			{
				$joueur->set_hp($joueur->get_hp() - round(($joueur->get_buff('blizzard', 'effet') / 100) * $joueur->get_hp_maximum()));
			}
			//Déplacement du joueur
			$joueur->set_pa($joueur->get_pa() - $coutpa);
			$joueur->set_x($coord['x']);
			$joueur->set_y($coord['y']);
			$joueur->sauver();
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
					<img src="image/pixel.gif" onLoad="envoiInfo('attaque.php?id_monstre=<?php echo $row[0]; ?>&type=monstre', 'information'); javascript:alert('Un monstre vous attaque sauvagement !');" />
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
//Si c'est un donjon
if($donjon)
{
	include_once(root.'donjon.php');
	if ($peu_bouger)
		$joueur->trigger_arene();
}
else 
{
	include_once(root.'map3D.php');
}
if(!$peu_bouger AND $cause != '') echo '<img src="image/pixel.gif" onLoad="alert(\''.$cause.'\');" />';
?>

