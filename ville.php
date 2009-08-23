<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
?>
	<div id="carte">
<?php

$W_distance = detection_distance($W_case, $joueur->get_pos());

$W_coord = convert_in_coord($W_case);
if($W_row['type'] == 1)
{
	$amende = recup_amende($joueur->get_id());
	if($_GET['direction'] == 'paye_amende')
	{
		if($amende['montant'] > $joueur->get_star()) echo 'Vous n\'avez pas assez de stars !';
		else
		{
			//On supprime l'amende du joueur
			$requete = "UPDATE perso SET star = star - ".floor($amende['montant']).", crime = 0, amende = 0 WHERE ID = ".$joueur->get_id();
			$db->query($requete);
			$requete = "DELETE FROM amende WHERE id = ".$amende['id'];
			$db->query($requete);
			//On partage l'amende a tous les joueurs du royaume
			$requete = "SELECT * FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' AND ID <> ".$joueur->get_id();
			$req = $db->query($requete);
			$joueurs = array();
			while($row = $db->read_assoc($req))
			{
				$joueurs[] = $row;
			}
			$tot_joueurs = count($joueurs);
			$star_joueur = floor(floor($amende['montant']) / $tot_joueurs);
			$star_royaume = floor($amende['montant']) % $tot_joueurs;
			if($star_joueur > 0)
			{
				$requete = "UPDATE perso SET star = star + ".$star_joueur." WHERE race  = '".$joueur->get_race()."' AND statut = 'actif' AND ID <> ".$joueur->get_id();
				$db->query($requete);
				foreach($joueurs as $j)
				{
					//Inscription dans son journal de l'amende
					$requete = "INSERT INTO journal VALUES('', ".$j['ID'].", 'r_amende', '".$j['nom']."', '".$joueur->get_nom()."', NOW(), '".$star_joueur."', 0, 0, 0)";
					$db->query($requete);
				}
			}
			if($star_royaume > 0)
			{
				$requete = "UPDATE royaume SET star = star + ".$star_royaume." WHERE race = '".$joueur->get_race()."'";
				$db->query($requete);
			}
			//Si le joueur avait des primes sur la tête, elles sont effacées
			if($amende['prime'] > 0)
			{
				$requete = "SELECT * FROM prime_criminel WHERE id_criminel = ".$joueur->get_id();
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$requete = "UPDATE perso SET star = star + ".$row['montant']." WHERE ID = ".$row['id_joueur'];
					$db->query($requete);
				}
				$requete = "DELETE FROM prime_criminel WHERE id_criminel = ".$joueur->get_id();
				$db->query($requete);
			}
			$amende = recup_amende($joueur->get_id());
			$joueur = recupperso($joueur->get_id());
		}
	}
	if($amende)
	{
		if($amende['acces_ville'] == 'y') $acces_ville = true;
		else $acces_ville = false;
	}
	else $acces_ville = false;
	//Affichage de la ville uniquement pour les persos qui ne sont pas en guerre, et qui n'ont pas d'amende
	if(($R->get_diplo($joueur->get_race()) < 7 OR $R->get_diplo($joueur->get_race()) == 127) AND !$acces_ville)
	{
		?>
		<h2 class="ville_titre"><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> </h2>
					<?php include_once(root.'ville_bas.php');?>

				<?php
				if($R->get_id() != 0)
				{
					//Récupère tout les royaumes qui peuvent avoir des items dans l'HV
					$requete = "SELECT * FROM diplomatie WHERE race = '".$R->get_race()."'";
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$races = array();
					$keys = array_keys($row);
					$i = 0;
					$count = count($row);
					while($i < $count)
					{
						if((($row[$keys[$i]] <= 5) OR ($row[$keys[$i]] == 127)) && ($keys[$i] != 'race')) $races[] = "'".$keys[$i]."'";
						$i++;
					}
					$races = implode(',', $races);
					
					//Recherche tous les objets correspondants à ces races
					$requete = "SELECT * FROM hotel WHERE race IN (".$races.") ORDER BY id DESC LIMIT 1";
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					if($db->num_rows > 0)
					{
					?>
					<span class="small">Dernier objet en vente : <?php echo nom_objet($row['objet']); ?> pour <?php echo $row['prix']; ?> stars !</span>
					<?php
					}
				}
				?>
				<table style="width : 100%;">
					<tr style="width : 100%; vertical-align : top;">
						<td class="ville_test">
							<p class="ville_haut">
								Quartier Marchand
							</p>
							<?php
							//Si ca n'est pas en royaume neutre, on peut acheter
							if($R->get_id() != 0)
							{
								?>
							<ul class="ville">
								<li>
									<a href="boutique.php?type=arme" onclick="return envoiInfo(this.href, 'carte')">Forgeron</a>
								</li>
								<li>
									<a href="boutique.php?type=armure" onclick="return envoiInfo(this.href, 'carte')">Armurerie</a>
								</li>
								<li>
									<a href="enchanteur.php" onclick="return envoiInfo(this.href, 'carte')">Enchanteur</a>
								</li>
								<li>
									<a href="alchimiste.php" onclick="return envoiInfo(this.href, 'carte')">Alchimiste</a>
								</li>
								<li>
									<a href="hotel.php" onclick="return envoiInfo(this.href, 'carte')">Hôtel des ventes</a>
								</li>
							</ul>
							<?php
							}
							?>
						</td>
						<td class="ville_test">
							<p class="ville_haut">Quartier Royal</p>
							<ul class="ville">
							<?php
							//Si on est dans notre royaume
							if($R->get_diplo($joueur->get_race()) == 127)
							{
								$is_election = elections::is_mois_election($R->get_id());
								if($is_election && date("d") >= 5 && date("d") < 15)
								{
									?>
									<li>
										<a href="candidature.php" onclick="return envoiInfo(this.href, 'carte')">Candidature</a>
									</li>
									<?php
								}
								if($is_election && date("d") >= 15)
								{
									?>
									<li>
										<a href="vote_roi.php" onclick="return envoiInfo(this.href, 'carte')">Vote</a>
									</li>
									<?php
								}
							}
							?>
							
									<li>
										<a href="bureau_quete.php" onclick="return envoiInfo(this.href,'carte')">Bureau des quêtes</a>
									</li>
							<?php
									if($R->get_diplo($joueur->get_race()) == 127)
									{
							?>
									<li>
										<a href="qg.php" onclick="return envoiInfo(this.href, 'carte')">Quartier général</a>
									</li>
									<li>
										<a href="vente_terrain.php" onclick="return envoiInfo(this.href, 'carte')">Vente de terrain</a>
									</li>
							<?php
										if($joueur->get_rang_royaume() == 6)
										{
							?>
									<li>
										<a href="roi/">Gestion du royaume</a>
									</li>
							<?php
										}
									}
							?>
					<li>
						<a href="teleport.php" onclick="return envoiInfo(this.href, 'carte')">Pierre de Téléportation</a>
					</li>
			</ul>
			</td>
		</tr>
		<tr style="width : 100%;">
			<td class="ville_test">
				<p class="ville_haut">Haut Quartier</p>
				<ul class="ville">
					<li>
						<a href="ecolemagie.php" onclick="return envoiInfo(this.href, 'carte')">École de magie</a>
					</li>
					<li>
						<a href="ecolecombat.php" onclick="return envoiInfo(this.href, 'carte')">École de combat</a>
					</li>
					<li>
						<a href="universite.php" onclick="return envoiInfo(this.href, 'carte')">Université</a>
					</li>
					<li>
						<a href="tribunal.php" onclick="return envoiInfo(this.href, 'carte')">Tribunal</a>
					</li>
<?php
		if($R->get_diplo($joueur->get_race()) == 127)
		{
/*?>
					<li>
						<a href="" onclick="return envoiInfo('maison_lignee.php','carte')">Maison de lignées</a>
					</li>
<?php*/
		}
?>
				</ul>
			</td>
			<td class="ville_test" >
			<p class="ville_haut">Bas Quartier</p>
			<ul class="ville">		
				<li>
					<a href="taverne.php" onclick="return envoiInfo(this.href, 'carte')">Taverne</a>
				</li>
				<li>
					<a href="poste.php" onclick="return envoiInfo(this.href, 'carte')">Poste</a>
				</li>
<?php
		if($R->get_diplo($joueur->get_race()) == 127)
		{
			?>
				<li>
					<a href="terrain_chantier.php" onclick="return envoiInfo(this.href, 'carte')">Bâtiments en chantier</a>
				</li>
			<?php
			$terrain = new terrain();
			if($terrain = $terrain->recoverByIdJoueur($joueur->get_id()))
			{
			?>
					<li>
						<a href="terrain.php" onclick="return envoiInfo(this.href, 'carte')">Votre terrain</a>
					</li>
			<?php
			}
		}
?>
			</ul>
						</tr>
					</table>


<?php
	}
	else
	{
		if($R->get_diplo($joueur->get_race()) >= 7 AND $R->get_diplo($joueur->get_race()) != 127)	echo 'Vous êtes en guerre avec ce royaume !';
	}
	if($amende)
	{
	//Payer l'amende
	?>
	Vous êtes considéré comme criminel par votre royaume.<br />
	Il vous faut payer une amende de <?php echo $amende['montant']; ?> stars pour ne plus l'être.<br />
	<a href="" onclick="return envoiInfo('ville.php&amp;direction=paye_amende', 'carte')">Pour payer l'amende, cliquez ici</a>
	<?php
	}
}
?>
		</ul>
	    <img src="image/pixel.gif" onLoad="envoiInfo('menu_carteville.php?javascript=oui&amp;ville=ok', 'carteville');" />
	</div>
