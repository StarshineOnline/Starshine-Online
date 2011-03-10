<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($joueur->get_race() != $R->get_race() &&
		$R->get_diplo($joueur->get_race()) > 6)
{
	echo "<h5>Impossible de commercer avec un tel niveau de diplomatie</h5>";
	exit (0);
}

	if(!isset($_GET['type'])) $_GET['type'] = 'arme';

		?>
		<fieldset>
		<legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> > <?php echo '<a href="boutique.php?type=armure" onclick="return envoiInfo(this.href,\'carte\')">';?> Marchand d'<?php echo $_GET['type']; ?>s </a></legend>
<?php
	if($_GET['type'] == 'armure')
	{
		$url = 'boutique.php?type=arme&amp;order=';
		$batiment = 'armurerie';
	}
	else
	{
	  $url = 'boutique.php?type=armure&amp;order=';
		$batiment = 'forgeron';
	}

//Uniquement si le joueur se trouve sur une case de ville
if($W_row['type'] == 1)
{
	//On recherche le niveau de la construction
	$requete = "SELECT level,statut FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R->get_id();
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	?>
	<?php include_once(root.'ville_bas.php');?>
	<div class="ville_test">
	<?php
	//Si le batiment est inactif, on met le batiment au niveau 1, sinon c'est bon
	if($row['statut'] == 'inactif') $level_batiment = 1; else $level_batiment = $row['level'];
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :

				switch ($_GET['type'])
				{
					case 'arme' :
						$requete = "SELECT id, prix, lvl_batiment FROM arme WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur->get_star() >= $cout)
						{
							if($joueur->prend_objet('a'.$row['id']))
							{
								$joueur->set_star($joueur->get_star() - $cout);
								$joueur->sauver();
								
								// Lien trafiqué
								if($row['lvl_batiment'] > $level_batiment && false)
								{
									$joueur->supprime_objet('a'.$row['id'], 1);
									security_block(URL_MANIPULATION, 'Batiment non disponible dans cette ville');
								}
								
								//Récupération de la taxe
								if($taxe > 0)
								{
									$R->set_star($R->get_star() + $taxe);
									$R->sauver();
									$R->add_forgeron($taxe);
								}
								echo '<h6>Arme achetée !</h6>
								<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'armure' :
						$requete = "SELECT id, prix, type, lvl_batiment FROM armure WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur->get_star() >= $cout)
						{
							if($joueur->prend_objet('p'.$row['id']))
							{
								$joueur->set_star($joueur->get_star() - $cout);
								$joueur->sauver();
								
								// Lien trafiqué
								if($row['lvl_batiment'] > $level_batiment && false)
								{
									$joueur->supprime_objet('p'.$row['id'], 1);
									security_block(URL_MANIPULATION, 'Batiment non disponible dans cette ville');
								}
								
								//Récupération de la taxe
								if($taxe > 0)
								{
									$R->set_star($R->get_star() + $taxe);
									$R->sauver();
									$R->add_armurerie($taxe);
								}
								echo '<h6>Armure achetée !</h6>
								<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
					case 'dressage' :
						$requete = "SELECT id, prix, type, lvl_batiment FROM objet_pet WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur->get_star() >= $cout)
						{
							if($joueur->prend_objet_pet('d'.$row['id']))
							{
								$joueur->set_star($joueur->get_star() - $cout);
								$joueur->sauver();
								
								// Lien trafiqué
								if($row['lvl_batiment'] > $level_batiment && false)
								{
									$joueur->supprime_objet('d'.$row['id'], 1);
									security_block(URL_MANIPULATION, 'Batiment non disponible dans cette ville');
								}
								
								//Récupération de la taxe
								if($taxe > 0)
								{
									$R->set_star($R->get_star() + $taxe);
									$R->sauver();
									$R->add_armurerie($taxe);
								}
								echo '<h6>Armure achetée !</h6>
								<img src="image/pixel.gif" onLoad="envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');" />';
							}
							else
							{
								echo $G_erreur;
							}
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de Stars</h5>';
						}
					break;
				}
			break;
		}
	}
	
	if(!isset($_GET['order']) OR ($_GET['order'] == '')) $_GET['order'] = 'type,prix';
	$order = explode(',', $_GET['order']);
	$i = 0;
	$ordre = '';
	while($i < count($order))
	{
		if($i != 0) $ordre .= ',';
		$ordre .= ' '.$order[$i].' ASC';
		$i++;
	}
	//Affichage du menu de séléction et de tri
	$url = 'boutique.php?type='.$_GET['type'].'&amp;order=';
	/*
	?>
	
	Trier par :	<a href="<?php echo $url; ?>prix" onclick="return envoiInfo(this.href, 'carte')">Prix</a> :: <a href="<?php echo $url; ?>type" onclick="return envoiInfo(this.href, 'carte')">Type</a> :: <a href="<?php echo $url; if($_GET['type'] == 'arme') echo 'degat'; else echo 'pp'; ?>" onclick="return envoiInfo(this.href, 'carte')">Effets</a> :: <a href="<?php echo $url; ?>forcex" onclick="return envoiInfo(this.href, 'carte')">Force</a><br />
	<br />
	
	<?php
	*/
	//Affichage du magasin des armes
	if($_GET['type'] == 'arme')
	{
		if(!array_key_exists('part', $_GET)) $_GET['part'] = 'arc';
		$types = array();
		$types['arc'][0][] = 'Portée';
		$types['arc'][0][] = 'distance_tir';
		$types['baton'][0][] = 'Bonus Cast';
		$types['baton'][0][] = 'var1';
		$types['autre'] = array();
		if($_GET['part'] == 'arc') $type = 'arc';
		elseif($_GET['part'] == 'baton') $type = 'baton';
		else $type = 'autre';
		$url2 = 'boutique.php?type=arme&amp;order='.$_GET['order'];
	
		echo "
		<div class='ville_haut'>
		<ul id='hotel_liste_type'>
		<li onclick=\"envoiInfo('".$url2."&amp;part=arc', 'carte')\">Arc|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=dague', 'carte')\">Dague|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=epee', 'carte')\">Epée|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=hache', 'carte')\">Hache|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=bouclier', 'carte')\">Bouclier|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=baton', 'carte')\">Baton</li>
		
		</ul>
		</div>
		";
		?>
		<ul id='boutique'>

		<li class='head'>
			<span class='image'></span>
			<?php
			if(!$types[$type]){$class='style="width:44% !important;"';}else{$class='';}
			?>

			<span class='nom' <?php echo $class;?>>
				Nom
			</span>
			<span class='mains'>
				Mains
			</span>
			<span class='degats'>
				Dégâts
			</span>
			<span class='coef'>
				<span onClick="return nd();" onmouseover="return <?php echo make_overlib('Coéf Arc = '.$joueur->get_coef_distance().'<br />Coéf Mélée = '.$joueur->get_coef_melee().'<br />Coéf Incantation = '.$joueur->get_coef_incantation()).'<br />Coéf Blocage = '.$joueur->get_blocage; ?>" onmouseout="return nd();">Coéf.</span>
			</span>
	<?php
	foreach($types[$type] as $typ)
	{
		echo '
			<span class="coef_type">';
			if ($typ[0] == 'Bonus Cast'){echo 'Bonus';}else {echo $typ[0];}
			echo '</span>';
	}
	?>
			<span class='stars'>
				Stars
			</span>
			<span class='achat'>
				Achat
			</span>
		</li>
		
		<?php
		
		$color = 1;
		$where = 'lvl_batiment <= '.$level_batiment;
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM arme WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			$mains = explode(';', $row['mains']);
			$main = $mains[0];
			if($row['type'] == 'arc') $skill = 'distance';
			elseif($row['type'] == 'baton') $skill = 'incantation';
			elseif($row['type'] == 'bouclier') $skill = 'blocage';
			else $skill = 'melee';
			if($row['type'] == 'baton' OR $row['type'] == 'bouclier') $coef = $row['forcex'] * $row['melee'];
			else $coef = $row['forcex'] * $row[$skill];
			$coef_joueur = $joueur->{'get_coef_'.$skill}();
			if($coef_joueur <= $coef OR $cout > $joueur->get_star()) $couleur = 3;

			$arme = decompose_objet($joueur->inventaire()->$main);
			if($arme['id_objet'] != '')
			{
				$requete = "SELECT * FROM arme WHERE id = ".$arme['id_objet'];
				$req_arme = $db->query($requete);
				$row_arme = $db->read_array($req_arme);
				$echo = 'Arme équipée : '.$row_arme['nom'].' - '.$row_arme['type'].' - Dégâts '.$row_arme['degat'];
			}
			else
				$echo = 'Rien n\'est équipé';
		?>
		<li class="element trcolor<?php echo $couleur; ?>">
	
		<span class='image'>	
		<?php echo '<img src="image/arme/arme'.$row['id'].'.png" style="height:24px;" />'; 		
		
			if(!$types[$type]){$class='style="width:44% !important;"';}else{$class='';}
			?>
			</span>
			
			<span class='nom' <?php echo $class;?> onmouseover="return <?php echo make_overlib(addslashes($echo)); ?>" onClick="return nd();" onmouseout="nd();">
				
				<?php echo $row['nom']; ?>
			</span>
			<span class='mains'>
				<?php
				echo count(explode(';', $row['mains']));
				?>
			</span>
			<span class='degats'>
				<?php echo $row['degat']; ?>
			</span>
			<span class='coef'>
				<span class="<?php echo over_price($coef, $coef_joueur); ?>"><?php echo ($coef); ?></span>
			</span>
	<?php
	$check = true;
	foreach($types[$type] as $typ)
	{
		echo '
			<span class="coef_type">
				<span>'.$row[$typ[1]].'</span>
			</span>';
	}
	?>
			<span class='stars'>
				<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout ?></span>
			</span>
			<span class='achat'>
			
			<?php 

			if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($coef, $coef_joueur) == 'achat_normal' AND $check AND over_price($cout, $joueur->get_star())== 'achat_normal')
			{
			?>
				<a href="boutique.php?action=achat&amp;type=arme&amp;id=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
			<?php
			}
			?>
			</span>
		</li>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
	
	
		</div>

<?php
	}
	else
	{
		$url2 = 'boutique.php?type=armure&amp;order='.$_GET['order'];
	

		
		echo "<div class='ville_haut'>
		<ul id='hotel_liste_type'>

		
		<li onclick=\"envoiInfo('".$url2."&amp;part=ceinture', 'carte');\">Taille|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=chaussure', 'carte');\">Pieds|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=jambe', 'carte');\">Jambe|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=main', 'carte');\">Main|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=tete', 'carte');\">Tête|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=torse', 'carte');\">Torse|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=cou', 'carte');\">Cou|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=dos', 'carte');\">Dos|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=doigt', 'carte');\">Doigt|</li>
		<li onclick=\"envoiInfo('".$url2."&amp;part=dressage', 'carte');\">Dressage</li>
		
		</ul>";
		
		?>
		</div>
		<ul id='boutique'>
		<li class='head'>
			<span class='image'>
				
			</span>
		
			<span class='nom'>
				Nom
			</span>
			<span class='pp'>
				PP
			</span>
			<span class='pm'>
				PM
			</span>
			<span class='force'>
				<?php if(array_key_exists('part', $_GET) AND $_GET['part'] == 'dressage') echo 'Dressage'; else echo 'Force'; ?>
			</span>
			<span class='stars'>
				Stars
			</span>
			<span class='achat'>
				Achat
			</span>
		</li>
		<?php
		
		$color = 1;
		$where = 'lvl_batiment <= '.$level_batiment;
		if(array_key_exists('part', $_GET) AND $_GET['part'] == 'dressage')
		{
			$requete = "SELECT * FROM objet_pet ORDER BY".$ordre;
		}
		elseif(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
			$requete = "SELECT * FROM armure WHERE ".$where." ORDER BY".$ordre;
		}
		else
		{
			$where .= " AND type = 'ceinture'";
			$requete = "SELECT * FROM armure WHERE ".$where." ORDER BY".$ordre;
		}
		$req = $db->query($requete);
		
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($_GET['part'] == "dressage" AND ($row['dressage'] > $joueur->get_dressage() OR $cout > $joueur->get_star())) $couleur = 3;
			elseif($_GET['part'] != "dressage" AND ($row['forcex'] > $joueur->get_force() OR $cout > $joueur->get_star())) $couleur = 3;

			switch($_GET['part'])
			{
				case 'dressage' :
					if($joueur->inventaire_pet()->$row['type'] != '' AND $joueur->inventaire_pet()->$row['type'] !== 0)
					{
						$armure = decompose_objet($joueur->inventaire_pet()->$row['type']);
						$requete = "SELECT * FROM objet_pet WHERE id = ".$armure['id_objet'];
						$req_armure = $db->query($requete);
						$row_armure = $db->read_array($req_armure);
						$echo = 'Armure équipée sur votre pet : '.$row_armure['nom'].' - PP = '.$row_armure['PP'].' / PM = '.$row_armure['PM'];
					}
					else $echo = 'Armure ('.$row['type'].') équipée sur votre pet : Aucune';
				break;
				default :
					if($joueur->inventaire()->$row['type'] != '' AND $joueur->inventaire()->$row['type'] !== 0)
					{
						$armure = decompose_objet($joueur->inventaire()->$row['type']);
						$requete = "SELECT * FROM armure WHERE id = ".$armure['id_objet'];
						$req_armure = $db->query($requete);
						$row_armure = $db->read_array($req_armure);
						$echo = 'Armure équipée : '.$row_armure['nom'].' - PP = '.$row_armure['PP'].' / PM = '.$row_armure['PM'];
					}
					else $echo = 'Armure équipée : Aucune';
				break;
			}
			
			if($_GET['part'] == "dressage")
			{
		?>
		<li class="element trcolor<?php echo $couleur; ?>" onmouseover="return <?php echo make_overlib($echo); ?>" onClick="return nd();" onmouseout="return nd();">
			<span class='image'>
				<?php echo '<img src="image/armure/'.$row['type'].'/'.$row['type'].''.$row['id'].'.png" style="height:24px;" />'; 	?>	

			</span>

			<span class='nom'>
				<?php echo $row['nom']; ?>
			</span>
			<span class='pp'>
				<?php echo $row['PP']; ?>
			</span>
			<span class='pm'>
				<?php echo $row['PM']; ?>
			</span>
			<span class='force'>
				<span class="<?php echo over_price($row['dressage'], $joueur->get_dressage()); ?>"><?php echo $row['dressage']; ?></span>
			</span>
			<span class='stars'>
				<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout; ?></span>
			</span>
			<span class='achat'>
			<?php
				if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($row['forcex'], $joueur->get_force()) == 'achat_normal')
				{
				?>	
				<a href="boutique.php?action=achat&amp;type=dressage&amp;id=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
				<?php 
				}
				?>
			</span>
		</li>
		<?php
				if($color == 1) $color = 2; else $color = 1;
			}
			else
			{
			?>
		<li class="element trcolor<?php echo $couleur; ?>" onmouseover="return <?php echo make_overlib($echo); ?>" onClick="return nd();" onmouseout="return nd();">
			<span class='image'>
				<?php echo '<img src="image/armure/'.$row['type'].'/'.$row['type'].''.$row['id'].'.png" style="height:24px;" />'; 	?>	

			</span>

			<span class='nom'>
				<?php echo $row['nom']; ?>
			</span>
			<span class='pp'>
				<?php echo $row['PP']; ?>
			</span>
			<span class='pm'>
				<?php echo $row['PM']; ?>
			</span>
			<span class='force'>
				<span class="<?php echo over_price($row['forcex'], $joueur->get_force()); ?>"><?php echo $row['forcex']; ?></span>
			</span>
			<span class='stars'>
				<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout; ?></span>
			</span>
			<span class='achat'>
			<?php
				if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($row['forcex'], $joueur->get_force()) == 'achat_normal')
				{
				?>	
				<a href="boutique.php?action=achat&amp;type=armure&amp;id=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
				<?php 
				}
				?>
			</span>
		</li>
		<?php
				if($color == 1) $color = 2; else $color = 1;
			}
		}
		
		?>
		
		</ul>
		</fieldset>

<?php
	}
}
?>
