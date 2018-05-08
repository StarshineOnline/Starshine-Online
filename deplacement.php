<?php // -*- mode: php; tab-width: 2 -*-
/**
 * @file deplacement.php
 * Affichage de la vue et gestion des déplacements
 */  
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut.php');
//$joueur = new perso($_SESSION['ID']);
$perso = joueur::get_perso();

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$interf_princ->verif_mort($perso);

$x_avant = $x = $perso->get_x();
$y_avant = $y = $perso->get_y();

$vrai_donjon = $perso->get_y() > 190;
$donjon = ($vrai_donjon OR $perso->get_x() > 190);

$peu_bouger = true;

$action = array_key_exists('action', $_GET) ?  $_GET['action'] : null;
//Déplacement du personnage
$mouvement = true;
$diagonale = false;
$complet = false;
// Ivresse
switch( $action )
{
case 'haut':
case 'bas':
case 'gauche':
case 'droite':
case 'haut-gauche':
case 'haut-droite':
case 'bas-gauche':
case 'bas-droite':
	$ivresse = $perso->get_buff('ivresse');
	if( $ivresse )
	{
		if( comp_sort::test_de(100, $ivresse->get_effet()) )
		{
			$dir = array('haut', 'bas', 'gauche', 'droite', 'haut-gauche', 'haut-droite', 'bas-gauche', 'bas-droite');
			$action = $dir[ rand(0, 7) ];
		}
	}
}
// action
switch( $action )
{
case 'haut':
	$y--;
	break;
case 'bas':
	$y++;
	break;
case 'gauche':
	$x--;
	break;
case 'droite':
	$x++;
	break;
case 'haut-gauche':
	$x--;
	$y--;
	$diagonale = true;
	break;
case 'haut-droite':
	$x++;
	$y--;
	$diagonale = true;
	break;
case 'bas-gauche':
	$x--;
	$y++;
	$diagonale = true;
	break;
case 'bas-droite':
	$x++;
	$y++;
	$diagonale = true;
	break;
case 'royaumes':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'affiche_royaume', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'jour':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'desactive_atm_all', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'meteo':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'desactive_atm', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'son':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'no_sound', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'monstres':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'cache_monstre', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'adsiege':
	$mouvement = false;
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'affiche_roy_ads', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'options':
	$mouvement = false;
	// niveau minimum des monstres
	$val = sSQL($_GET['niv_min'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_min_monstres', $val)";
	$db->query($requete);
	// niveau maximum des monstres
	$val = sSQL($_GET['niv_max'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_max_monstres', $val)";
	$db->query($requete);
	// ordre
	$val = sSQL($_GET['ordre'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'ordre_aff', $val)";
	$db->query($requete);
	// diplomatie - relation
	$val = sSQL($_GET['diplo_rel'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'diplo_aff_sup', $val)";
	$db->query($requete);
	// diplomatie - limite
	$val = sSQL($_GET['diplo'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'diplo_aff', $val)";
	$db->query($requete);
	// PNJ
	$val = (array_key_exists('pnj', $_GET) && $_GET['pnj']=='on') ? 0 : 1;
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'cache_pnj', $val)";
	$db->query($requete);
	$action = false;
	break;
case 'niveau':
	$mouvement = false;
	$action = false;
	$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_min_monstres"');
	$row = $db->read_array($req);
	$niv_min = $row ? $row[0] : '0';
	$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_max_monstres"');
	$row = $db->read_array($req);
	$niv_max = $row ? $row[0] : 255;
	$val = sSQL($_GET['niveau'], SSQL_INTEGER);
	// niveau minimum des monstres
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_min_monstres', $val)";
	$db->query($requete);
	// niveau maximum des monstres
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_max_monstres', $val)";
	$db->query($requete);
	break;
case 'tous_monstres':
	// niveau minimum des monstres
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_min_monstres', 0)";
	$db->query($requete);
	// niveau maximum des monstres
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'niv_max_monstres', 255)";
	$db->query($requete);
case 'rafraichir':
	$complet = true;
default:
	$mouvement = false;
}

if($mouvement)
{
	$W_requete = 'SELECT info FROM map WHERE x ='.$x.' and y = '.$y;
	$W_req = $db->query($W_requete);
	$W_row = $db->read_array($W_req);
	$num_rows = $db->num_rows;
	
	$type_terrain = type_terrain($W_row['info']);
	$coutpa = cout_pa($type_terrain[0], $perso->get_race());
	$coutpa_base = $coutpa;
	$case = new map_case(array('x' => $x, 'y' => $y));
	if ((($case->x == 244) AND ($case->y == 170 )) AND ($perso->get_tuto() == 1) AND ($perso->get_classe_id() == 1))
	{
		$perso->set_tuto($perso->get_tuto()+1);
		/// @todo à refaire
		// <script type="text/javascript"> echo 'affichePopUp(\'texte_tuto.php\');'; </script>
	}
	if ($perso->get_classe_id() == 2)
	{
		if ((($case->x == 241) AND ($case->y == 171 )) AND ($perso->get_tuto() == 1) )
		{
			$perso->set_tuto($perso->get_tuto()+1);
			/// @todo à refaire
			//<script type="text/javascript"> echo 'affichePopUp(\'texte_tuto.php\');'; </script>
		}
		if ((($case->x == 242) AND ($case->y == 168 )) AND ($perso->get_tuto() == 2) )
		{
			$perso->set_tuto($perso->get_tuto()+1);
		}
	}
	$coutpa = cout_pa2($coutpa, $perso, $case, $diagonale);
	if( $perso->is_buff('potion_plaine') && $type_terrain == 'plaine' )
		$cout -= $perso->get_buff('potion_plaine', 'effet');
	if( $perso->is_buff('potion_foret') && $type_terrain == 'foret' )
		$cout -= $perso->get_buff('potion_foret', 'effet');
	if( $perso->is_buff('potion_desert') && $type_terrain == 'desert' )
		$cout -= $perso->get_buff('potion_desert', 'effet');
	if( $perso->is_buff('potion_banquise') && $type_terrain == 'glace' )
		$cout -= $perso->get_buff('potion_banquise', 'effet');
	if( $perso->is_buff('potion_marais') && $type_terrain == 'marais' )
		$cout -= $perso->get_buff('potion_marais', 'effet');
	if( $perso->is_buff('potion_montagne') && $type_terrain == 'montagne' )
		$cout -= $perso->get_buff('potion_montagne', 'effet');
	if( $perso->is_buff('potion_terres_maudites') && $type_terrain == 'terre_maudite' )
		$cout -= $perso->get_buff('potion_terres_maudites', 'effet');
	//Si le joueur a un buff ou débuff qui l'empêche de bouger
	if($perso->is_buff('buff_forteresse') OR $perso->is_buff('buff_position') OR $perso->is_buff('bloque_deplacement') OR $perso->is_buff('dressage') OR $perso->is_buff('petrifie'))
	{
		$peu_bouger = false;
		$cause = 'Un buff vous empêche de bouger';
	}
  if ($perso->is_buff('debuff_bloque_deplacement_alea'))
  {
    if (is_bloque_Deplacement_alea(
          $perso->get_buff('debuff_bloque_deplacement_alea', 'effet'),
          $perso->get_buff('debuff_bloque_deplacement_alea', 'effet2'))) {
      $cause = 'Un buff vous empêche de bouger';
      $peu_bouger = false;
    }
  }
	//Si en donjon et case n'existe pas, le joueur ne peut pas bouger
	if($num_rows == 0)
	{
		$peu_bouger = false;
		$cause = '';
	}
	//if($peu_bouger) echo 'ok';
	if (($perso->get_pa() >= $coutpa) AND ($coutpa_base < 50) AND $peu_bouger)
	{
		//Si debuff blizard
		if($perso->is_buff('blizzard'))
		{
			$perso->set_hp($perso->get_hp() - round(($perso->get_buff('blizzard', 'effet') / 100) * $perso->get_hp_maximum()));
		}
		//Déplacement du joueur
		$perso->set_pa($perso->get_pa() - $coutpa);
		$perso->set_x($x);
		$perso->set_y($y);
		$perso->sauver();
		//Si ya un monstre, paf il attaque le joueur
		if($vrai_donjon)
		{
			$requete = "SELECT * FROM map_monstre WHERE x = ".$x." AND y = ".$y." ORDER BY hp DESC";
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_assoc($req);
				$map_monstre = new map_monstre($row);
				$_SESSION['attaque_donjon'] = 'ok';
				/// attaque
				$pet_def = false;
				if($perso->is_buff('defense_pet'))
				{
					$pet = $perso->get_pet();
					if (!$pet || $pet->get_hp() < 1)
						interf_debug::enregistre('Le pet est mort…');
					else
					{
						$defense = $perso->get_buff('defense_pet', 'effet');
						$collier = decompose_objet($perso->get_inventaire_partie('cou'));		
						if ($collier != '')
						{
							$requete = "SELECT * FROM armure WHERE ID = ".$collier['id_objet'];
							//Récupération des infos de l'objet
							$req = $db->query($requete);
							$row = $db->read_array($req);
							$effet = explode('-', $row['effet']);
							if ($effet[0] == '20')
							{
								$defense = $defense + $effet[1];
							}
						}
						$rand = rand(0, 100);
						//Défense par le pet
						interf_debug::enregistre('Defense par le pet: '.$rand.' VS '.$defense);
						if($rand < $defense)
						{
							interf_debug::enregistre('Defense par le pet OK');
							$pet_def = true;
						}
					}
				}
				if($pet_def)
					$attaquant = entite::factory('pet', $pet, $perso);
				else
				{
					$perso->action_do = $perso->recupaction('defense');
					$attaquant = entite::factory('perso', $perso);
				}
				$map_monstre->check_monstre();
				$defenseur = entite::factory('monstre', $map_monstre);
				$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Combat VS '.$defenseur->get_nom()) );
				$cadre->add( new interf_alerte(interf_alerte::msg_info, false, false, 'Un monstre vous attaque sauvagement !') );
				$attaque = new attaque($perso, $attaquant, $defenseur);
		    $interf = $cadre->add( $G_interf->creer_combat() );
				$attaque->set_interface( $interf );
				$R = new royaume( $case->get_royaume() );
		    $attaque->attaque(0, 'monstre', 0, $R, $pet, true);
				if( interf_debug::doit_aff_bouton() )
				{
					$lien = $interf->add( new interf_bal_smpl('a', '', 'debug_droit', 'icone icone-debug') );
					$lien->set_attribut('onclick', 'return debugs();');
				}	
			}
		}
	}
	else
	{
		$x = $x_avant;
		$y = $y_avant;
	}
}

/*$W_pos = convert_in_pos($x, $y);
//Si c'est un donjon
if($donjon)
{
	include_once(root.'donjon.php');
	if ($peu_bouger)
		$perso->trigger_arene();
}
else 
{
	include_once(root.'map2.php');
}*/
if( $donjon && $peu_bouger )
	$perso->trigger_arene();
// @todo à refaire
/*if(!$peu_bouger AND $cause != '') echo '<img src="image/pixel.gif" onLoad="alert(\''.$cause.'\');" />';
check_son_ambiance();*/
if( $action )
{
	// Options
	/// @todo passer à l'objet
	$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_min_monstres"');
	$row = $db->read_array($req);
	$niv_min = $row ? $row[0] : '0';
	$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_max_monstres"');
	$row = $db->read_array($req);
	$niv_max = $row ? $row[0] : 255;
	$options  = interf_carte::calcul_options( $perso->get_id() );
	//$carte = $interf_princ->set_carte( new interf_carte($x, $y, $options, 3, 'carte', $niv_min, $niv_max) );
	$interf_princ->set_carte( $cont = new interf_cont() );
	$cont->add( new interf_carte($x, $y, $options, 3, 'carte', $niv_min, $niv_max, $cont) );
	$interf_princ->maj_perso($complet);
	$interf_princ->maj_ville();
}
else
	$carte = $interf_princ->set_gauche();
$interf_princ->maj_tooltips();
