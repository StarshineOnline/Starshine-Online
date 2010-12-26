<?php
if (file_exists('root.php'))
  include_once('root.php');

/**
*
* Permet l'affichage des informations d'une case en fonction du joueur.
*
*/
include_once(root.'inc/fp.php');
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Case et coordonnées de la case
$W_case = $_GET['case'];

//Vérifie si il y a eu des modifications sur la case (fin de batiments drapeaux et autres)
$case = new map_case($W_case);
$case->check_case();

$monstre = new map_monstre($_GET['id']);
$distance = detection_distance($joueur->get_pos(), $monstre->get_pos());
?>
<fieldset>
	<legend>Dressage</legend>
<?php
//On vérifie qu'il est sur la même case que le mob
if($distance == 0)
{
	if(!array_key_exists('fin', $_GET))
	{
		//On vérifie si il a pas déjà le buff dressage sur lui
		if($joueur->is_buff('dressage'))
		{
			if($joueur->get_pa() >= 10)
			{
				//On compare l'id du mob avec l'id du mob qu'il été en train de dresser, et si c'est le même on continue.
				if($joueur->get_buff('dressage', 'effet2') == $_GET['id'])
				{
					//Si il reste moins d'un jour sur le buff, on propose au joueur de finir le dressage
					if(($joueur->get_buff('dressage', 'fin') - time()) < 86400)
					{
						?>
						<a href="dressage.php?id=<?php echo $monstre->get_id(); ?>&fin" onclick="return envoiInfo(this.href, 'information')">Finir le dressage</a>
						<?php
					}
					else
					{
						//Calcul du potentiel du joueur => Eventuellement rajouter les connaissances
						$potentiel = ($joueur->get_dressage() * 3) + $joueur->get_survie();
						$rand = rand(0, $potentiel);
						//On modifie le buff
						$buff = $joueur->get_buff('dressage');
						$buff->set_effet($buff->get_effet() + $rand);
						$buff->sauver();
						$augmentation = augmentation_competence('dressage', $joueur, 1);
						if ($augmentation[1] == 1) $joueur->set_dressage($augmentation[0]);
						$joueur->set_pa($joueur->get_pa() - 10);
						$joueur->sauver();
						?>
						Vous apprenez quelques tours à votre animal. Il semble se familiariser de plus en plus à vous.<br />
						<?php
						echo '
						<a href="dressage.php?id='.$_GET['id'].'" onclick="return envoiInfo(this.href, \'information\')">Continuer le dressage</a>';
					}
				}
				//Sinon erreur
				else
				{
					?>
					<h5>Vous tentez déjà de dresser un monstre.</h5>
					<?php
				}
			}
			else
			{
				?>
				<h5>Vous n'avez pas assez de PA</h5>
				<?php
			}
		}
		//Sinon on commence le dressage sur ce monstre
		else
		{
			//On vérifie qu'il a le niveau en dressage requis pour dresser un monstre de ce niveau
			if($joueur->can_dresse($monstre))
			{
				if($joueur->nb_pet() < $joueur->get_comp('max_pet'))
				{
					lance_buff('dressage', $joueur->get_id(), 0, $_GET['id'], 172800, 'Dressage', 'On dresse le monstre', 'perso', 1, 0, 0, 0);
					?>
					<h6>Dressage en cours</h6>
					<br />
					<a href="dressage.php?id=<?php echo $_GET['id']; ?>" onclick="return envoiInfo(this.href, 'information')">Continuer le dressage</a><br />
					<br />
					Le dressage a commencé.<br />
					Vous pouvez continuer le dressage (10 PA) pour augmenter vos chances de dresser cette créature.<br />
					Un jour après avoir commencer le dressage, vous pouvez décider de finir le dressage de cette créature et ainsi savoir si il est réussi ou non.<br />
					Attention, vous ne pouvez plus ni bouger ni attaquer lorsque vous êtes en train de dresser une créature.<br />
					De plus, si un joueur vous attaque, le dressage sera arrêté.<br />
					<br />
					Vous pouvez à tout moment décider d'arrêter le dressage, pour cela rendez vous dans la partie "dressage" du jeu.
					<?php
				}
				else echo '<h5>Vous ne pouvez pas dresser plus de '.$joueur->get_comp('max_pet').' créatures</h5>';
			}
			else
			{
				echo '<h5>Vous n\'avez pas assez en dressage pour dresser ce monstre</h5>';
			}
		}
	}
	//Fin du dressage
	else
	{
		//Fin du dressage
		if(($joueur->get_buff('dressage', 'fin') - time()) < 86400)
		{
			$pet = new monstre($monstre->get_type());
			//On regarde si le joueur a assez dressé
			if($joueur->get_buff('dressage', 'effet') > $pet->get_dressage())
			{
				echo '<h6>Vous avez réussi à dresser ce monstre</h6>';
				//On le met dans son inventaire de monstre
				if($joueur->add_pet($pet->get_id(), $monstre->get_hp(), ($pet->get_energie() * 10)))
				{
					$buff = $joueur->get_buff('dressage');
					$buff->supprimer();
					$monstre->supprimer();
					echo '<h6>Le '.$pet->get_nom().' est maintenant votre créature.</h6>';
					
					$drop = $pet->get_drops();
					//Drop d'un objet ?
					$drops = explode(';', $drop);
					if($drops[0] != '')
					{
						$count = count($drops);
						$i = 0;
						while($i < $count)
						{
							$share = explode('-', $drops[$i]);
							$objet = $share[0];
							$taux = ceil($share[1] / ($G_drop_rate*1.5));
							if($joueur->get_race() == 'humain') $taux = $taux / 1.3;
							if($joueur->is_buff('fouille_gibier')) $taux = $taux / (1 + ($joueur->get_buff('fouille_gibier', 'effet') / 100));
							if ($taux < 2) $taux = 2; // Comme ca, pas de 100%
							$tirage = rand(1, floor($taux));

							if($tirage == 1 AND ($objet[0] == "h" OR $objet[0] == "l"))
							{
								$type_obj = '';
								//Nom de l'objet
								switch($objet[0])
								{
									case 'h' :
										$objet_nom = 'Objet non identifié';
										//Gemme aléatoire
										if($objet[1] == 'g')
										{
											//Niveau de la gemme
											$niveau_gemme = $objet[2];
											//Recherche des gemmes de ce niveau
											$ids = array();
											$requete = "SELECT id FROM gemme WHERE niveau = ".$niveau_gemme;
											$req_g = $db->query($requete);
											while($row = $db->read_row($req_g))
											{
												$ids[] = $row[0];
											}
											$num = rand(0, (count($ids) - 1));
											$objet = 'hg'.$ids[$num];
										}
									break;
									case 'l' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM grimoire WHERE id = $id_objet";
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = 'Grimoire : '.$row[0];
									break;
								}
								echo 'Vous fouillez le corps du monstre et découvrez "'.$objet_nom.'" !<br />';
								//Si le joueur a un groupe
								if($joueur->get_groupe() > 0)
								{
									//Répartition en fonction du mode de distribution
									switch($groupe->get_partage())
									{
										//Aléatoire
										case 'r' :
											echo 'Répartition des objets aléatoire.<br />';
											$chance = count($groupe->membre);
											$aleat = rand(1, $chance);
											$gagnant = new perso($groupe->membre[($aleat - 1)]->get_id_joueur());
										break;
										//Par tour
										case 't' :
											echo 'Répartition des objets par tour.<br />';
											$gagnant = new perso($groupe->get_prochain_loot());
											//Changement du prochain loot
											$j_g = $groupe->trouve_position_joueur($groupe->get_prochain_loot());
											//Si c'est pas le dernier alors suivant
											if((count($groupe->membre) - 1) != $j_g)
											{
												$groupe->set_prochain_loot($groupe->membre[($j_g + 1)]->get_id_joueur());
											}
											//Sinon premier
											else
											{
												$groupe->set_prochain_loot($groupe->membre[0]->get_id_joueur());
											}
											$groupe->sauver();
										break;
										//Leader
										case 'l' :
											echo 'Répartition des objets au leader.<br />';
											$gagnant = new perso($groupe->get_id_leader());
										break;
										//Celui qui trouve garde
										case 'k' :
											echo 'Répartition des objets, celui qui trouve garde.<br />';
											$gagnant = new perso($joueur->get_id());
										break;
									}
									echo $gagnant->get_nom().' reçoit "'.$objet_nom.'"<br />';
								}
								else
								{
									$gagnant = new perso($joueur->get_id());
								}
								//Insertion du loot dans le journal du gagnant
								$requete = "INSERT INTO journal VALUES(NULL, ".$gagnant->get_id().", 'loot', '', '', NOW(), '".mysql_escape_string($objet_nom)."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
								
								$gagnant->restack_objet();
								$gagnant->prend_objet($objet);
							}
							$i++;
						}
					}
				}
				else
				{
					echo '<h5>Vous n\'avez plus de place pour ce monstre</h5>';
				}
			}
			//Sinon tant pis
			else
			{
				echo '<h5>Vous n\'avez pas réussi à dresser ce monstre</h5>';
				$buff = $joueur->get_buff('dressage');
				$buff->supprimer();
			}
		}
		else
		{
			echo '<h5>Vous n\'avez pas fini de dresser ce monstre</h5>';
		}
	}
}
else
{
	?>
	<h5>Vous êtes trop loin !</h5>
	<?php
}
refresh_perso();
?>
</fieldset>