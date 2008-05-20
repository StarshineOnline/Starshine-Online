<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

$position = convert_in_pos($joueur['x'], $joueur['y']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$verif_ville = verif_ville($joueur['x'], $joueur['y']);
$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$_SESSION['position'] = $position;
?>
	<div id="carte">
<?php

$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
if($W_distance == 0 AND $verif_ville)
{
	$amende = recup_amende($joueur['ID']);
	if($_GET['direction'] == 'paye_amende')
	{
		if($amende['montant'] > $joueur['star']) echo 'Vous n\'avez pas assez de stars !';
		else
		{
			//On supprime l'amende du joueur
			$requete = "UPDATE perso SET star = star - ".floor($amende['montant']).", crime = 0, amende = 0 WHERE ID = ".$joueur['ID'];
			$db->query($requete);
			$requete = "DELETE FROM amende WHERE id = ".$amende['id'];
			$db->query($requete);
			//On partage l'amende a tous les joueurs du royaume
			$requete = "SELECT * FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' AND ID <> ".$joueur['ID'];
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
				$requete = "UPDATE perso SET star = star + ".$star_joueur." WHERE race  = '".$joueur['race']."' AND statut = 'actif' AND ID <> ".$joueur['ID'];
				$db->query($requete);
				foreach($joueurs as $j)
				{
					//Inscription dans son journal de l'amende
					$requete = "INSERT INTO journal VALUES('', ".$j['ID'].", 'r_amende', '".$j['nom']."', '".$joueur['nom']."', NOW(), '".$star_joueur."', 0, 0, 0)";
					$db->query($requete);
				}
			}
			if($star_royaume > 0)
			{
				$requete = "UPDATE royaume SET star = star + ".$star_royaume." WHERE race = '".$joueur['race']."'";
				$db->query($requete);
			}
			//Si le joueur avait des primes sur la tête, elles sont effacées
			if($amende['prime'] > 0)
			{
				$requete = "SELECT * FROM prime_criminel WHERE id_criminel = ".$joueur['ID'];
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$requete = "UPDATE perso SET star = star + ".$row['montant']." WHERE ID = ".$row['id_joueur'];
					$db->query($requete);
				}
				$requete = "DELETE FROM prime_criminel WHERE id_criminel = ".$joueur['id'];
				$db->query($requete);
			}
			$amende = recup_amende($joueur['ID']);
			$joueur = recupperso($joueur['ID']);
		}
	}
	if($amende)
	{
		if($amende['acces_ville'] == 'y') $acces_ville = true;
		else $acces_ville = false;
	}
	else $acces_ville = false;
	//Affichage de la ville uniquement pour les persos qui ne sont pas en guerre, et qui n'ont pas d'amende
	if(($R['diplo'] < 7 OR $R['diplo'] == 127) AND !$acces_ville)
	{
		?>
		<h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\',\'centre\')">';?><?php echo $R['nom'];?></a> </h2>
					<?php include('ville_bas.php');?>

				<?php
				if($R['ID'] != 0)
				{
					//Récupère tout les royaumes qui peuvent avoir des items dans l'HV
					$requete = "SELECT * FROM diplomatie WHERE race = '".$R['race']."'";
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
							if($R['ID'] != 0)
							{
								?>
							<ul class="ville">
								<li>
									<a href="javascript:envoiInfo('boutique.php?type=arme&amp;poscase=<?php echo $W_case; ?>', 'carte')">Forgeron</a>
								</li>
								<li>
									<a href="javascript:envoiInfo('boutique.php?type=armure&amp;poscase=<?php echo $W_case; ?>', 'carte')">Armurerie</a>
								</li>
								<li>
									<a href="javascript:envoiInfo('enchanteur.php?poscase=<?php echo $W_case; ?>', 'carte')">Enchanteur</a>
								</li>
								<li>
									<a href="javascript:envoiInfo('magasin.php?poscase=<?php echo $W_case; ?>', 'carte')">Alchimiste</a>
								</li>
								<li>
									<a href="javascript:envoiInfo('hotel.php?poscase=<?php echo $W_case; ?>', 'carte')">Hotel des ventes</a>
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
							if($R['diplo'] == 127)
							{
								if(date("d") >= 15 AND date("d") < 20)
								{
							?>
									<li>
										<a href="javascript:envoiInfo('candidature.php?poscase=<?php echo $W_case; ?>', 'carte')">Candidature</a>
									</li>
							<?php
								}
										if(date("d") >= 20)
										{
							?>
									<li>
										<a href="javascript:envoiInfo('vote_roi.php?poscase=<?php echo $W_case; ?>', 'carte')">Vote</a>
									</li>
							<?php
										}
							}
							?>
							
									<li>
										<a href="javascript:envoiInfo('bureau_quete.php?poscase=<?php echo $W_case; ?>','carte')">Bureau des quètes</a>
									</li>
							<?php
									if($R['diplo'] == 127)
									{
							?>
									<li>
										<a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>', 'carte')">Quartier général</a>
									</li>
							<?php
										if($joueur['rang_royaume'] == 6)
										{
							?>
									<li>
										<a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>', 'carte')">Gestion du royaume</a>
									</li>
							<?php
										}
									}
							?>
					<li>
						<a href="javascript:envoiInfo('teleport.php?poscase=<?php echo $W_case; ?>', 'carte')">Pierre de Téléportation</a>
					</li>
			</ul>
			</td>
		</tr>
		<tr style="width : 100%;">
			<td class="ville_test">
				<p class="ville_haut">Haut Quartier</p>
				<ul class="ville">
					<li>
						<a href="javascript:envoiInfo('ecolemagie.php?poscase=<?php echo $W_case; ?>', 'carte')">Ecole de magie</a>
					</li>
					<li>
						<a href="javascript:envoiInfo('ecolecombat.php?poscase=<?php echo $W_case; ?>', 'carte')">Ecole de combat</a>
					</li>
					<li>
						<a href="javascript:envoiInfo('universite.php?poscase=<?php echo $W_case; ?>', 'carte')">Université</a>
					</li>
					<li>
						<a href="javascript:envoiInfo('tribunal.php?poscase=<?php echo $W_case; ?>', 'carte')">Tribunal</a>
					</li>
<?php
		if($R['diplo'] == 127)
		{
/*?>
					<li>
						<a href="javascript:envoiInfo('maison_lignee.php?poscase=<?php echo $W_case; ?>','carte')">Maison de lignées</a>
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
					<a href="javascript:envoiInfo('taverne.php?poscase=<?php echo $W_case; ?>', 'carte')">Taverne</a>
				</li>
				<li>
					<a href="javascript:envoiInfo('poste.php?poscase=<?php echo $W_case; ?>', 'carte')">Poste</a>
				</li>
			</ul>
						</tr>
					</table>


<?php
	}
	else
	{
		if($R['diplo'] >= 7 AND $R['diplo'] != 127)	echo 'Vous êtes en guerre avec ce royaume !';
	}
	if($amende)
	{
	//Payer l'amende
	?>
	Vous êtes considérez comme criminel par votre royaume.<br />
	Il vous faut payer une amende de <?php echo $amende['montant']; ?> stars pour redevenir libre.<br />
	<a href="javascript:envoiInfo('ville.php?poscase=<?php echo $W_case; ?>&amp;direction=paye_amende', 'carte')">Pour payer l'amende, cliquez ici</a>
	<?php
	}
}
?>
		</ul>
	    <img src="image/pixel.gif" onLoad="envoiInfo('menu_carteville.php?javascript=oui&amp;ville=ok', 'carteville');" />
	</div>