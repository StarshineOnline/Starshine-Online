<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
// Infos sur un objet
if( $action == 'infos' )
{
  $G_interf->creer_infos_objet($_GET['id']);
  exit;
}

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
    $perso = new perso($_GET['id_perso']);
	}
	else exit();
}
else
{
  $perso = joueur::get_perso();
	$visu = $perso->est_mort();
}

switch( $action )
{
case 'princ':
  $princ = new interf_princ_cont();
  $princ->add( $G_interf->creer_invent_equip($perso, $_GET['page'], !$visu) );
  exit;
case 'sac':
  $princ = new interf_princ_cont();
  $princ->add( $G_interf->creer_invent_sac($perso, $_GET['slot'], !$visu) );
  exit;
case 'hotel_vente':
  $princ = $G_interf->creer_vente_hotel($perso, $_GET['objet']);
  exit;
case 'gemme':
  $princ = $G_interf->creer_enchasser($perso, $_GET['objet']);
  exit;
}

//Filtres
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 'perso';
$slot = array_key_exists('slot', $_GET) ? $_GET['slot'] : 'utile';

$princ = $G_interf->creer_princ_droit('Inventaire du Personnage');
//Switch des actions
if( !$visu && $action )
{
  if( array_key_exists('objet', $_GET) )
    $obj = $perso->get_inventaire_slot_partie($_GET['objet']);
	switch($action)
	{
    /// TODO : faire plus de vérifications
    case 'grand_accessoire':
    case 'tete':
    case 'cou':
    case 'main_droite':
    case 'torse':
    case 'main_gauche':
    case 'main':
    case 'ceinture':
    case 'doigt':
    case 'moyen_accessoire':
    case 'jambe':
    case 'dos':
    case 'petit_accessoire_1':
    case 'chaussure':
    case 'petit_accessoire_2':
			if($perso->equip_objet($obj))
			{
				//On supprime l'objet de l'inventaire
				$perso->supprime_objet($obj, 1);
				$perso->sauver();
			}
			else
				$princ->add_message($G_erreur, false);
		break;
		case 'utilise' :
				// Pose d'un bâtiment ou ADS
			if($_GET['type'] == 'fort' OR $_GET['type'] == 'tour' OR $_GET['type'] == 'bourg' OR $_GET['type'] == 'mur' OR $_GET['type'] == 'arme_de_siege')
			{
				if ($joueur->is_buff('debuff_rvr'))
				{
          $princ->add_message('<h5>RvR impossible pendant la trêve</h5>', false);
					break;
				}
				if ($W_row['type'] == 1)
				{
          $princ->add_message('<h5>Vous ne pouvez pas poser de bâtiment sur une ville</h5>', false);
					break;
				}

				//Cherche infos sur l'objet
				$requete = "SELECT batiment.id AS batiment_id FROM objet_royaume RIGHT JOIN batiment ON batiment.id = objet_royaume.id_batiment WHERE objet_royaume.id = ".sSQL($_GET['id_objet']);
				$req = $db->query($requete);
				if ($db->num_rows($req) == 0)
				{
					die('<h5>Erreur SQL</h5>');
				}
				$row = $db->read_assoc($req);
				$batiment = new batiment($row['batiment_id']);
				if($R->get_diplo($joueur->get_race()) != 127 && $_GET['type'] != 'arme_de_siege' && $batiment->get_id() != 1) // id=1 : poste avancé
				{
          $princ->add_message('Vous ne pouvez poser un bâtiment uniquement sur un territoire qui vous appartient', false);
					break;
				}

				//On vérifie si ya pas déjà un batiment en construction
				$requete = "SELECT id FROM placement WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
				$req = $db->query($requete);
  			if($db->num_rows > 0)
  			{
          $princ->add_message('Il y a déjà un bâtiment en construction sur cette case !', false);
  				break;
  			}

				//On vérifie si ya pas déjà un batiment
				$requete = "SELECT id FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
          $princ->add_message('Il y a déjà un bâtiment sur cette case !', false);
					break;
				}

        $nbr_mur = 0;
        if( $_GET['type'] == 'mur' )
        {
					//   Debut Evolution #581
					// Pour pouvoir construire un mur, il faut ne pas avoir plus de 2 murs autour de celui que l'on construit.
					// Attention, les tests pour voir s'il n'y a pas trop de murs autour seront uniquement
					// fait sur les 4 cases juste au nord, a l'est, au sud et a l'ouest (avec la meme limite qu'actuellement).
					// Il faut aussi tester pour chacune des ces cases ou il y a deja des murs si la limite ne sera pas depassee une fois le mur pose

					// On commence par extraire la position des murs ou des constructions de murs a 2 cases de distance de la case a traiter
					//   000000
					//   000000
					//   000000
					$position_murs=array();
					$position_murs[0]=array(0,0,0,0,0);
					$position_murs[1]=array(0,0,0,0,0);
					$position_murs[2]=array(0,0,0,0,0);
					$position_murs[3]=array(0,0,0,0,0);
					$position_murs[4]=array(0,0,0,0,0);
	
					// Il y a donc 25 positions a recuperer
					$requete  = 'SELECT x,y FROM construction WHERE ABS(CAST(x AS SIGNED) -'.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur"';
					$requete  = 'SELECT id,x,y FROM construction WHERE ABS(CAST(x AS SIGNED) - '.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur" UNION SELECT id,x,y FROM placement WHERE ABS(CAST(x AS SIGNED) - '.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur"';
					$req = $db->query($requete);

					// Stockage des positions dans la matrice
					while($row = $db->read_assoc($req))
					{
						$position_murs[$row[x]-$joueur->get_x()+2][$row[y]-$joueur->get_y()+2]=1;
					}
					// Rajout de la position du nouveau mur dans la matrice pour les tests (il est au milieu de la matrice).
					$position_murs[2][2]=1;

					// DEBUG message
					/*echo '<h4> Matrice des murs deja poses ou en construction:</h4>';
					for ($i=0;$i<5;$i++)
					{
						echo '<h4>';
						for ($j=0;$j<5;$j++)
						{
							// Attention, les x sont verticaux la !!!
							echo ' '.$position_murs[$j][$i];
						}
						echo '</h4>';
					}*/

					// Gestion des cardinalites (somme du nombre de murs adjacent au nord, ouest, est, sud de chaque position en comptant la position courante
					// Cette matrice n'est pas utilisee directement, elle sert juste pour le debug (attention a la variable max_nb_murs si on la retire !)
					$murs_cardinalite=array();
					$murs_cardinalite[0]=array(0,0,0);
					$murs_cardinalite[1]=array(0,0,0);
					$murs_cardinalite[2]=array(0,0,0);
					$max_nb_murs=0;
					for ($x = 1; $x<=3 ; $x+=1)
					{
						for ($y = 1; $y<=3 ; $y+=1)
						{
							$murs_cardinalite[$x-1][$y-1]=$position_murs[$x-1][$y]+$position_murs[$x+1][$y]+$position_murs[$x][$y-1]+$position_murs[$x][$y+1]+ $position_murs[$x][$y];
							$max_nb_murs=max($max_nb_murs,$murs_cardinalite[$x-1][$y-1]);
						}
					}

					// DEBUG MESSAGE
					/*
					echo '<h4>Nombre de murs estimes (en incluant le futur mur) autour de la position ('.$row[x]-$joueur->get_x().','.$row[y]-$joueur->get_y().'):</h4>';
					echo '<h4>'.$murs_cardinalite[0][0].'  '.$murs_cardinalite[1][0].' '.$murs_cardinalite[2][0].'</h4>';
					echo '<h4>'.$murs_cardinalite[0][1].'  '.$murs_cardinalite[1][1].' '.$murs_cardinalite[2][1].'</h4>';
					echo '<h4>'.$murs_cardinalite[0][2].'  '.$murs_cardinalite[1][2].' '.$murs_cardinalite[2][2].'</h4>';
					echo '<h4>Le maximum est :'.$max_nb_murs.'</h4>';
					*/
					// Il reste maintenant a verifier que toutes les conditions sont réunies
					// Si une des cases vaut 4 ou plus, alors erreur
					$nbr_mur = $max_nb_murs;
					//   Fin Evolution #581
        }
				if( $nbr_mur > 3 )
        {
          $princ->add_message('<h5>Il y a déjà trop de murs autour !</h5>', false);
					break;
				}

				// Règles des distance entre bâtiments
				$isOk = true;
				if($_GET['type'] == 'bourg' || $_GET['type'] == 'fort')
        {
					// Distance d'une capitale
					$distanceMax = $_GET['type'] == 'bourg' ? 5 : 7;

					$requete = "SELECT 1 FROM map"
									." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
									." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
									." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
									." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
									." AND type = 1";
					$req = $db->query($requete);
					if($db->num_rows > 0)
          {
            $princ->add_message('Il y a une capitale à moins de '.$distanceMax.' cases !', false);
						$isOk = false;
					}
					
					// Distance entre Bourgs
					if($isOk && $_GET['type'] == 'bourg'){
						// dist entre 2 bourgs
            if( construction::batiments_proche($joueur->get_x(), $joueur->get_y(), 'bourg', $R->get_dist_bourgs(), $R->get_id())
              or placement::batiments_proche($joueur->get_x(), $joueur->get_y(), 'bourg', $R->get_dist_bourgs(), $R->get_id()) )
            {
              $princ->add_message('Vous avez un bourg à moins de '.$R->get_dist_bourgs().' cases !', false);
							$isOk = false;
            }
            else
            {
              $dist_r = $R->get_dist_bourgs(true);
              $bats = construction::batiments_proche($joueur->get_x(), $joueur->get_y(), 'bourg', $dist_r, $R->get_id(), true, true);
              if( !bats )
                $bats = placement::batiments_proche($joueur->get_x(), $joueur->get_y(), 'bourg', $dist_r, $R->get_id(), true, true);
              if( $bats )
              {
                $isOk = true;
                foreach($bats as $b)
                {
                $r_bourg = new royaume($b['royaume']);
                $d_max = min($r_bourg->get_dist_bourgs(true), $dist_r);
                  $dist = detection_distance(convert_in_pos($b['x'], $b['y']), convert_in_pos($joueur->get_x(), $joueur->get_y()));
                  if( $dist <= $d_max )
                  {
                    $princ->add_message('Il y a un bourg à '.$dist.' cases !', false);
      							$isOk = false;
                    break;
                  }
                }
              }
            }
					}

					// Distance entre forts
					else if($isOk && $_GET['type'] == 'fort'){
						// dist entre 2 forts du même royaume
            if( construction::batiments_proche($joueur->get_x(), $joueur->get_y(), 'fort', $R->get_dist_forts(), $R->get_id())
              or placement::batiments_proche($joueur->get_x(), $joueur->get_y(), 'fort', $R->get_dist_forts(), $R->get_id()) )
            {
              $princ->add_message('Vous avez un fort à moins de '.$R->get_dist_bourgs().' cases !', false);
							$isOk = false;
            }
            else if( construction::batiments_proche($joueur->get_x(), $joueur->get_y(), 'fort', $R->get_dist_forts(true), $R->get_id(), true)
              or placement::batiments_proche($joueur->get_x(), $joueur->get_y(), 'fort', $R->get_dist_forts(true), $R->get_id(), true) )
            {
              $princ->add_message('Il y a un fort à moins de '.$R->get_dist_forts(true).' cases !', false);
							$isOk = false;
            }
					}
				}
				if(!$isOk){
					break;
				}


				if( $joueur->is_buff('convalescence') && $joueur->get_pa() < 10 )
				{
          $princ->add_message('Vous n\'avez pas assez de PA !', false);
					break;
				}

				//Positionnement de la construction
				if($_GET['type'] == 'arme_de_siege')
				{
					$distance = 1;
					$rez = 0;
				}//max($row['temps_construction'] * $distance, $row['temps_construction_min']);
				else
				{
					$distance = calcul_distance(convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']), ($joueur->get_pos()));
					$rez = $batiment->get_bonus('rez');
				}
        $time = time() + max($batiment->get_temps_construction() * $distance, $batiment->get_temps_construction_min());

        $requete = 'INSERT INTO placement (type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez, point_victoire) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
        $types = 'siiiiiiisii';
        $params = array($_GET['type'], $joueur->get_x(),
                        $joueur->get_y(), 
                        $Trace[$joueur->get_race()]['numrace'],
                        time(), $time, $batiment->get_id(),
                        $batiment->get_hp(), $batiment->get_nom(),
                        $rez, $batiment->get_point_victoire());
        $db->param_query($requete, $params, $types);
				// Coût en PA si en convalescence
				if( $joueur->is_buff('convalescence') )
				{
				  $joueur->set_pa( $joueur->get_pa() - 10 );
        }
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
				$joueur->sauver();
        $princ->add_message($batiment->get_nom().' posé avec succès');

				if($_GET['type'] == 'mur')
				{
					// Augmentation du compteur de l'achievement
					$achiev = $joueur->get_compteur('pose_murs');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}

				$princ->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible d\'équiper cet objet.');
      break;
	  case 'desequip':
			if(!$perso->desequip($_GET['zone'], $page=='pet'))
        $princ->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible de deséquiper cet objet.');
      break;
	  case 'utiliser':
      $objet = objet_invent::factory( $obj );
      $objet->utiliser($perso, $princ);
      break;
	  case 'depot':
      $objet = objet_invent::factory( $obj );
      $objet->deposer($perso, $princ);
      break;
	  case 'slot_1':
	  case 'slot_2':
	  case 'slot_3':
      $objet = objet_invent::factory( $obj );
      if( $objet->mettre_slot($perso, $princ, $action[5]) )
      {
        $perso->set_inventaire_slot_partie($objet->get_texte(), $_GET['objet']);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->sauver();
      }
			switch($_GET['type'])
			{
				case 'drapeau' :
					if ($joueur->is_buff('debuff_rvr'))
					{
						$princ->add_message('RvR impossible pendant la trêve', false);
						break;
					}
					if ($W_row['type'] != 1 && $W_row['type'] != 4)
					{
						//Cherche infos sur l'objet
						$requete = "
							SELECT *, b.id AS batiment_id, b.nom batiment_nom
							FROM objet_royaume o INNER JOIN batiment b ON b.id = o.id_batiment
							WHERE o.id = ".sSQL($_GET['id_objet'])."
						";
						$req = $db->query($requete);
						$row = $db->read_assoc($req);

						//Si terrain neutre ou pas a nous ET que c'est pas dans un donjon
						if((($R->get_diplo($joueur->get_race()) > 6 && $R->get_diplo($joueur->get_race()) != 127) OR $R->get_nom() == 'Neutre') AND !is_donjon($joueur->get_x(), $joueur->get_y()))
						{
							//Si c'est un petit drapeau, on vérifie qu'on est uniquement sur neutre
							if($row['batiment_nom'] == 'Petit Drapeau' && $R->get_nom() != 'Neutre')
							{
								$princ->add_message('Vous ne pouvez pas poser de petit drapeau sur une case non neutre !', false);
							}
							else
							{
								//On vérifie si ya pas déjà un batiment en construction
								$requete = "SELECT id FROM placement WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
								$req = $db->query($requete);
								if($db->num_rows <= 0)
								{
									//On vérifie si ya pas déjà un batiment
									$requete = "SELECT id FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
									$req = $db->query($requete);
									if($db->num_rows <= 0)
									{
										if( !$joueur->is_buff('convalescence') OR $joueur->get_pa() >= 10 )
										{
											//Positionnement du drapeau
											$distance = calcul_distance(convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']), ($joueur->get_pos()));
											$time = time() + max($row['temps_construction'] * $distance, $row['temps_construction_min']);
											$requete = "
												INSERT INTO placement (id, type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez)
												VALUES ('', 'drapeau', '".$joueur->get_x()."', '".$joueur->get_y()."', '".$Trace[$joueur->get_race()]['numrace']."', ".time().", '".$time."', '".$row['batiment_id']."', '".$row['hp']."', '".$db->escape($row['batiment_nom'])."', 0)
											";
											$db->query($requete);
											// Coût en PA si en convalescence
											if( $joueur->is_buff('convalescence') )
											{
												$joueur->set_pa( $joueur->get_pa() - 10 );
											}
											//On supprime l'objet de l'inventaire
											$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
											$joueur->sauver();
											$princ->add_message('Drapeau posé avec succès');

											// Augmentation du compteur de l'achievement
											$achiev = $joueur->get_compteur('pose_drapeaux');
											$achiev->set_compteur($achiev->get_compteur() + 1);
											$achiev->sauver();

											if ($W_row['info'] == 1)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_plaine');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 2)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_foret');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 3)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_sable');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 4)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_glace');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 6)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_montagne');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 7)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_marais');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 8)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_route');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
											if ($W_row['info'] == 9)
											{
												// Augmentation du compteur de l'achievement
												$achiev = $joueur->get_compteur('pose_drapeaux_terremaudite');
												$achiev->set_compteur($achiev->get_compteur() + 1);
												$achiev->sauver();
											}
										}
										else
										{
											$princ->add_message('Vous n\'avez pas assez de PA !', false);
										}
									}
									else
									{
										$princ->add_message('Il y a déjà un batiment sur cette case !', false);
									}
								}
								else
								{
									$princ->add_message('Il y a déjà un batiment en construction sur cette case !', false);
								}
							}
						}
						else
						{
							$princ->add_message('Vous ne pouvez poser un drapeau uniquement sur les royaumes avec lesquels vous êtes en guerre', false);
						}
					}
					else
					{
						$princ->add_message('Vous ne pouvez pas poser de drapeau sur ce type de terrain', false);
					}
				break;
				case 'identification' :
					$fin = false;
					$i = 0;
					$materiel = $joueur->recherche_objet('o2');
					//my_dump($materiel);
					if ($materiel == false) {
            $princ->add_message('Vous n\'avez pas de materiel d\'identification', false);
						$fin = true;
					}
					elseif ($joueur->get_pa() < 10) {
            $princ->add_message('Vous n\'avez pas assez de PA !', false);
						$fin = true;
					}
					else {
						$joueur->add_pa(-10); /* pas oublier que ca coute 10 PA */
					}
					$count = count($joueur->get_inventaire_slot_partie());
					while(!$fin AND $i < $count)
					{
						//echo $joueur->get_inventaire_slot()[$i];
						$stack = explode('x', $joueur->get_inventaire_slot_partie($i));
						$id_objet = $stack[0];
						if(mb_substr($joueur->get_inventaire_slot_partie($i), 0, 1) == 'h')
						{
							$augmentation = augmentation_competence('identification', $joueur, 3);
							if ($augmentation[1] == 1)
							{
								$joueur->set_comp('identification', $augmentation[0]);
							}
							//echo $id_objet;
							$requete = "SELECT * FROM gemme WHERE id = ".mb_substr($id_objet, 2);
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							$player = rand(0, $joueur->get_identification());
							$thing = rand(0, pow(10, $row['niveau']));
							//echo $joueur['identification'].' / '.pow(10, $row['niveau']).' ---- '.$player.' VS '.$thing;
							//Si l'identification réussie
							if($player > $thing)
							{
								//On remplace la gemme par celle identifiée
								$gemme = mb_substr($joueur->get_inventaire_slot_partie($i), 1);
								$joueur->set_inventaire_slot_partie($gemme, $i);
								$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
                $princ->add( new interf_txt('Identification réussie !') );
                $princ->add( new interf_bal_smpl('br') );
                $princ->add( new interf_txt('Votre gemme est une '.$row['nom']) );
								$log_admin = new log_admin();
								$message = $joueur->get_nom().' a identifié '.$row['nom'];
								$log_admin->send($joueur->get_id(), 'identification', $message);
							}
							else
							{
                $princ->add_message('L\'identification n\'a pas marché…', false);
							}
							//On supprime l'objet de l'inventaire
							$joueur->supprime_objet('o2', 1);
							$fin = true;
						}
						$i++;
					}
					$joueur->sauver(); /* On sauve a la fin pour les PA */
				break;
				case 'potion_vie' :
					if($joueur->get_hp() > 0)
					{
						$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
						if(check_utilisation_objet($joueur, $objet))
						{
							$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
               				echo 'Vous utilisez une '.$row['nom'].' elle vous redonne '.$row['effet'].' points de vie<br />';
							?>
	<img src="image/pixel.gif"
		onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
								<?php
                				$joueur->add_hp($row['effet']);
                				$joueur->add_mp(-$row['mp']);
                				$joueur->add_pa(-$row['pa']);
                				$joueur->sauver();
								
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('use_potion');
								$achiev->set_compteur($achiev->get_compteur() + 1);
								$achiev->sauver();
						}
					}
					else echo 'Vous êtes mort !';
				break;
				case 'potion_guerison' :
					if($joueur->get_hp() > 0)
					{
						$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
						if(check_utilisation_objet($joueur, $objet))
						{
							foreach($joueur->get_buff() as $buff)
							{
								if($buff->get_debuff() == 1)
								{
					if($buff->get_supprimable() == 1) {
$buff_tab[count($buff_tab)] = $buff->get_id();
};
								}
							}
							if(count($buff_tab) > 0)
							{
								$db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
                $princ->add_message('Une malédiction a été correctement supprimée');
							}
							else $princ->add_message('Vous n\'avez pas de malédiction a supprimer', false);
						}
					}
					else echo 'Vous êtes mort !';
				break;
				case 'robustesse' :
				  die('code mort');
				break;
				case 'globe_pa' :
					$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
					if(check_utilisation_objet($joueur, $objet))
					{
						$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$joueur->set_pa($joueur->get_pa() + $row['effet']);
						if($joueur->get_pa() > floor($G_PA_max)) $joueur->set_pa(floor($G_PA_max));
						echo 'Vous utilisez un '.$row['nom'].'<br />';
						?>
	<img src="image/pixel.gif"
		onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
						<?php
						$joueur->set_mp(max(0, ($joueur->get_mp() - $row['mp'])));
						$joueur->sauver();
					}
				break;
				case 'parchemin_tp' :
					$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
					$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$W_case = convertd_in_pos($joueur->get_x(), $joueur->get_y());
					//Calcul de la distance entre le point où est le joueur et sa ville natale
					$distance = detection_distance($W_case, convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']));
					if($row['effet'] >= $distance)
					{
						if(check_utilisation_objet($joueur, $objet))
						{
							//Téléportation du joueur
              $princ->add( new interf_txt('Vous utilisez un '.$row['nom']) );
              $princ->add( new interf_bal_smpl('br') );
              $img = new interf_bal_smpl('img');
              $img->set_attribut('src', 'image/pixel.gif');
              $img->set_attribut('onLoad', 'envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');');
              $princ->add( $img );
							$requete = "UPDATE perso SET x = ".$Trace[$joueur->get_race()]['spawn_x'].", y = ".$Trace[$joueur->get_race()]['spawn_y'].", pa = pa - ".$row['pa'].", mp = mp - ".$row['mp']." WHERE ID = ".$joueur->get_id();
							$db->query($requete);
						}
					}
					else
					{
            $princ->add_message('Vous êtes trop loin de la ville pour utiliser ce parchemin.', false);
					}
				break;
				case 'objet_quete' :
					$stack = explode('x', $joueur->get_inventaire_slot_partie($_GET['key_slot']));
					$id_objet = $stack[0];
					$id_objet_reel = mb_substr($id_objet, 1);
					if($id_objet_reel == 27)
					{
						?>
	Journal du mage Demtros - Unité aigle -<br /> < Le journal est en très
	mauvais état, certaines pages sont déchirées, ou rendues illisibles par
	l'humidité et des taches de sang ><br /> <br /> 12 Dulfandal : Nous
	approchons enfin de la cité de Myriandre. Je ne peux expliquer le
	trouble qui me saisi depuis plusieurs jours mais je sais que cela à un
	rapport avec les ruines de cette ville. Le malaise que je ressens est
	presque palpable, mais impossible d'en déterminer la cause. J'en ai
	fais part a Frankriss mais malgré toute la considération de ce dernier,
	je sens bien qu'ils me pense juste un peu nerveux...<br /> Je vais
	prendre deux tours de garde ce soir, de toute façon, je n'arriverais
	pas à dormir.<br /> <br /> 13 Dulfandal : Nous voici enfin arrivés,
	Achen et les autres sont partis en reconnaissance. Je scanne l'espace
	astral dans l'espoir de déterminer d'où peux provenir la perturbation
	que je ressens mais en vain.. Je sens malgré tout qu'il y a quelque
	chose d'anormal.<br /> <br /> 14 Dulfandal : Achen est mort, Dubs est
	l'agonie, dans son délire il est quand même parvenu à nous dire ce qui
	c'est passé, il semblerait qu'il ai été attaqués par des goules surgie
	de nulle part, Dubs a réussi à s'enfuir mais pas achen. il nous dis que
	les goules ont disparues d'un coup... frankriss est préoccupé, mais il
	gère la situation avec son sang froid coutumier. Nous allons former un
	groupe d'assaut pour aller récupérer Achen, ou ce qu'il en reste...
	L'unité aigle ne laisse jamais un compagnon derrière elle.<br /> Je ne
	sais pas ce qui se passe dans cette ville mais j'ai un mauvais
	pressentiment.<br /> 15 Dulfandal : le sort était camouflé, c'est pour
	ça que je l'ai pas perçu, celui qui l'a lancer doit être un mage
	exceptionnel pour arriver à camoufler une telle portion de terrain...
	nous nous déplaçons les épées sorties et près au combat, l'ennemi peut
	surgir de n'importe ou. Je n'arrive pas a démêler la trame du sort,
	trop puissant pour moi ... ( taches de sang )<br /> Il faut que
	j'écrive ( taches de sang ) sachent ce qui se passe ( taches de sang ),
	un nécromancien ( la suite est déchirée ).<br />
						<?php
					}
				break;
			case 'grimoire':
				$stack = explode('x', $joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$id_objet = $stack[0];
				$id_objet_reel = mb_substr($id_objet, 1);
				$ok = utilise_grimoire($id_objet_reel, $joueur, $princ);
				if ($ok)
				{
					$joueur->supprime_objet($id_objet, 1);
				}
      break;
		case 'vente_hotel':
      $objet = objet_invent::factory( $obj );
      $objet->vendre_hdv($perso, $princ, $_GET['prix']);
		  break;
		case 'vente':
      $objets = explode('-', $_GET['objets']);
      $stars = 0;
      foreach($objets as $objet)
      {
        $obj = explode('x', $objet);
        $objet = objet_invent::factory( $perso->get_inventaire_slot_partie($obj[0]) );
        if( $objet->get_nombre() >= $obj[1] )
          $stars += $objet->vendre_marchand($perso, $princ, $obj[1]);
        else
          $princ->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas assez d\'exemplaires de '.$objet->get_nom().' !');
        
      }
      if( $stars )
        $princ->add( new interf_alerte('success') )->add_message('Objet(s) vendu(s) pour '.$stars.' stars.');
		  break;
		case 'enchasse':
      $objet = objet_invent::factory( $obj );
      $gemme = objet_invent::factory( $perso->get_inventaire_slot_partie($_GET['gemme']) );
      if( $objet->enchasser($perso, $princ, $gemme) )
      {
        $perso->set_inventaire_slot_partie($objet->get_texte(), $_GET['objet']);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->supprime_objet($gemme->get_texte(), 1);
      }
      $perso->sauver();
		  break;
		case 'recup_gemme':
      $objet = objet_invent::factory( $obj );
      $objet->recup_gemme($perso, $princ);
		  break;
		case 'identifier':
      $objet = objet_invent::factory( $obj );
      $objet->identifier($perso, $princ, $_GET['objet']);
		  break;
	}
	refresh_perso();
}

$princ->add( $G_interf->creer_inventaire($perso, $page, $slot, !$visu) );

// Augmentation du compteur de l'achievement
$achiev = $perso->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
