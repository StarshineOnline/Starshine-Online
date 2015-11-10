<?php
/// @deprecated
if (file_exists('../../root.php'))
  include_once('../../root.php');

//Inclusion du haut du document html
include(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);


//Véifie si le perso est mort

$R = new royaume($Trace[$joueur->get_race()]['numrace']);
$R->get_diplo($joueur->get_race());

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);


if (isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case "update_objet_royaume":
			$requete = "SELECT * FROM objet_royaume WHERE id = ".sSQL($_GET['id']);
			$nombre = $_GET['nbr'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$check = true;
			//Si c'est pour une bourgade on vérifie combien il y en a déjà
			if($row['type'] == 'bourg')
			{
				$nb_bourg = nb_bourg($R->get_id());
				$nb_case = nb_case($R->get_id());
				if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250)) $check = false;
			}
			//On vérifie les stars
			if($R->get_star() >= ($row['prix'] * $nombre) && $check)
			{
				//On vérifie les ressources
				if(($R->get_pierre() >= $row['pierre'] * $nombre) && ($R->get_bois() >= $row['bois'] * $nombre) && ($R->get_eau() >= $row['eau'] * $nombre) && ($R->get_charbon() >= $row['charbon'] * $nombre) && ($R->get_sable() >= $row['sable'] * $nombre) && ($R->get_essence() >= $row['essence'] * $nombre))
				{
					$i = 0;
					while($i < $nombre)
					{
						//Achat
						$requete = "INSERT INTO depot_royaume VALUES (NULL, ".$row['id'].", ".$R->get_id().")";
						$db->query($requete);
						//On rajoute un bourg au compteur
						if($row['type'] == 'bourg')
						{
							$R->set_bourg($R->get_bourg() + 1);
						}
						//On enlève les stars au royaume
						$R->set_star($R->get_star() - $row['prix']);
						$R->set_eau($R->get_eau() - $row['eau']);
						$R->set_pierre($R->get_pierre() - $row['pierre']);
						$R->set_bois($R->get_bois() - $row['bois']);
						$R->set_sable($R->get_sable() - $row['sable']);
						$R->set_essence($R->get_essence() - $row['essence']);
						$R->set_charbon($R->get_charbon() - $row['charbon']);
						$R->sauver();
						$i++;
					}
					if($nombre > 1)
					{
                                               $tab = array("Drapeau"=>"Drapeaux","Poste avancé"=>"Postes avancés", "Fortin"=>"Fortins", "Fort"=>"Forts", "Forteresse"=>"Forteresses", "Tour de guet"=>"Tours de guet", "Tour de garde"=>"Tours de garde", "Tour de mages"=>"Tours de mages", "Tour d archers"=>"Tours d'archers", "Bourgade"=>"Bourgades", "Palissade"=>"Palissades", "Mur"=>"Murs", "Muraille"=>"Murailles", "Grande muraille"=>"Grandes murailles", "Bélier"=>"Béliers", "Catapulte"=>"Catapultes", "Trébuchet"=>"Trébuchets", "Baliste"=>"Balistes", "Grand drapeau"=>"Grands drapeaux", "Étendard"=>"Étendards", "Grand étendard"=>"Grands étendards", "Petit drapeau"=>"Petits drapeaux") ; 
                                               if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
                                               {
                                                    echo '<h6>'.$nombre.' '.$tab[$row['nom']].' bien achetées</h6><br />';
                                               }
                                               else
                                               {
                                                    echo '<h6>'.$nombre.' '.$tab[$row['nom']].' bien achetés</h6><br />';
                                               }
                                               
                                       }
                                       else
                                       {
                                               if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
                                               {
                                                    echo '<h6>'.$nombre.' '.$row['nom'].' bien achetée</h6><br />';
                                               }
                                               else
                                               {
                                                    echo '<h6>'.$nombre.' '.$row['nom'].' bien acheté</h6><br />';
                                               }
                                       }
				}
				else echo '<h5>Il vous manque des ressources !</h5>';
			}
			elseif(!$check)
			{
				echo '<h5>Il y a déjà trop de bourg sur votre royaume.</h5><br />
				Actuellement : '.$nb_bourg.'<br />
				Maximum : '.ceil($nb_case / 250);
			}
			else
			{
				echo '<h5>Le royaume n\'a pas assez de stars</h5>';
			}
		break;
		case 'update_motk':		
			$message = addslashes($_GET['message']);
			if ($message != '')
			{
				$requete = "UPDATE motk SET message = '".$message."', date = ".time()." WHERE id_royaume = ".$R->get_id();
				if($req = $db->query($requete)) 
				{
					echo '<h6>Message du roi bien modifié !</h6>';
				}
				else echo('<h5>Erreur lors de l\'envoi du message</h5>');
			}
			else
			{
				echo '<h5>Vous n\'avez pas saisi de message</h5>';
			}
		break;

		
	}
}
?>