<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$W_requete = 'SELECT * FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
?>
	<div id="carte">
	<fieldset class='ville_<?php echo $R->get_race(); ?>'>
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
	
	if($amende)
	{
	//Payer l'amende
	?>
	<p style='text-align:center;color:#EF3B3B;background-color:#EEE;-moz-border:12px;'>Vous êtes considéré comme criminel par votre royaume.<br />
	Il vous faut payer une amende de <?php echo $amende['montant']; ?> stars pour ne plus l'être.<br />
	<a href="" onclick="return envoiInfo('ville.php?direction=paye_amende', 'carte')">Pour payer l'amende, cliquez ici</a></p>
	<?php
	}

	//Affichage de la ville uniquement pour les persos qui ne sont pas en guerre, et qui n'ont pas d'amende
	if(($R->get_diplo($joueur->get_race()) < 7 OR $R->get_diplo($joueur->get_race()) == 127) AND $acces_ville)
	{
		?>
		<legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> </legend>
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
					<span class="small" style='margin-left:-145px;'>Dernier objet en vente : <?php echo nom_objet($row['objet']); ?> pour <?php echo $row['prix']; ?> stars !</span>
					<?php
					}
				}
				?>
							<div class='quartier_marchand'>
							<span class="quartier_marchand_titre">
								Quartier Marchand
							</span>
							<?php
							//Si ca n'est pas en royaume neutre, on peut acheter
							if($R->get_id() != 0)
							{
								?>
							<ul>
								<li onclick="envoiInfo('boutique.php?type=arme', 'carte')">Forgeron</li>
								<li onclick="envoiInfo('boutique.php?type=armure', 'carte')">Armurerie</li>
								<li onclick="envoiInfo('enchanteur.php', 'carte')">Enchanteur</li>
								<li onclick="envoiInfo('alchimiste.php', 'carte')">Alchimiste</li>
								<li onclick="envoiInfo('hotel.php', 'carte')">Hôtel des ventes</li>
							</ul>
							</div>
							<?php
							}
							?>
							<div class='quartier_royal'>
							<span class="quartier_royal_titre">Quartier Royal</span>
							<ul>							
									<li onclick="envoiInfo('bureau_quete.php', 'carte')">Bureau des quêtes</li>
							<?php
									//Si on est dans notre royaume
									if($R->get_diplo($joueur->get_race()) == 127)
									{
							?>
									<li onclick="envoiInfo('vie_royaume.php', 'carte')">Vie du royaume</li>
									<li onclick="envoiInfo('qg.php', 'carte')">Quartier général</li>
									<li onclick="envoiInfo('vente_terrain.php', 'carte')">Vente de terrain</li>
							<?php
										if($joueur->get_rang_royaume() == 6 ||
											 $R->get_ministre_economie() == $joueur->get_id() ||
											 $R->get_ministre_militaire() == $joueur->get_id() )
										{
							?>
									<li>
										<a href="roi/">Gestion du royaume</a>
									</li>
							<?php
										}
									}
							?>
					<li onclick="envoiInfo('teleport.php', 'carte')">Pierre de Téléportation</li>
			</ul>
			</div>
			<div class='quartier_haut'>
				<span class="quartier_haut_titre">Haut Quartier</span>
				<ul>
					<li onclick="envoiInfo('ecolemagie.php', 'carte')">École de magie</li>
					<li onclick="envoiInfo('ecolecombat.php', 'carte')">École de combat</li>
					<li onclick="envoiInfo('universite.php', 'carte')">Université</li>
					<li onclick="envoiInfo('tribunal.php', 'carte')">Tribunal</li>
				</ul>
			</div>
			<div class='quartier_bas'>
			<span class="quartier_bas_titre">Bas Quartier</span>
			<ul>		
				<li onclick="envoiInfo('taverne.php', 'carte')">Taverne</li>
				<li onclick="envoiInfo('ecurie.php', 'carte')">Ecurie</li>
				<li onclick="envoiInfo('poste.php', 'carte')">Poste</li>
				<li onclick="envoiInfo('show_arenes.php', 'carte')">Arènes</li>
<?php
		if($R->get_diplo($joueur->get_race()) == 127)
		{
			?>
				<li onclick="envoiInfo('terrain_chantier.php', 'carte')">Bâtiments en chantier</li>
			<?php
			$terrain = new terrain();
			if($terrain = $terrain->recoverByIdJoueur($joueur->get_id()))
			{
			?>
					<li onclick="envoiInfo('terrain.php', 'carte')">Votre terrain</li>
			<?php
			}
		}
?>
			</ul>
			</div>
<?php
	}
	else
	{
		if($R->get_diplo($joueur->get_race()) >= 7 AND $R->get_diplo($joueur->get_race()) != 127)	echo 'Vous êtes en guerre avec ce royaume !';
	}
}
?>
		</ul>
	    <img src="image/pixel.gif" onLoad="envoiInfo('menu_carteville.php?javascript=oui&amp;ville=ok', 'carteville');" />
	    </fieldset>
	</div>
