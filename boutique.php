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

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

		?>
		<h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="boutique.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Marchand d'<?php echo $_GET['type']; ?>s </a></h2>
		<?php
		if($_GET['type'] == 'armure')
		{
			$url = 'boutique.php?type=arme&amp;poscase='.$W_case.'&amp;order=';
			$batiment = 'armurerie';

		}
		else
		{
			$url = 'boutique.php?type=armure&amp;poscase='.$W_case.'&amp;order=';
			$batiment = 'forgeron';
		}
		?>
<?php
//Uniquement si le joueur se trouve sur une case de ville
if($W_row['type'] == 1)
{
	//On recherche le niveau de la construction
	$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R->get_id();
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
						$requete = "SELECT id, prix FROM arme WHERE id = ".sSQL($_GET['id']);
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
						$requete = "SELECT id, prix, type FROM armure WHERE id = ".sSQL($_GET['id']);
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
	$url = 'boutique.php?type='.$_GET['type'].'&amp;poscase='.$W_case.'&amp;order=';
	?>
	
	Trier par :	<a href="<?php echo $url; ?>prix" onclick="return envoiInfo(this.href, 'carte')">Prix</a> :: <a href="<?php echo $url; ?>type" onclick="return envoiInfo(this.href, 'carte')">Type</a> :: <a href="<?php echo $url; if($_GET['type'] == 'arme') echo 'degat'; else echo 'pp'; ?>" onclick="return envoiInfo(this.href, 'carte')">Effets</a> :: <a href="<?php echo $url; ?>forcex" onclick="return envoiInfo(this.href, 'carte')">Force</a><br />
	<br />
	
	<?php
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
		$url2 = 'boutique.php?type=arme&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
	?>

		<p class="ville_haut"><a href="<?php echo $url2; ?>&amp;part=arc" onclick="return envoiInfo(this.href, 'carte')">Arc</a> - <a href="<?php echo $url2; ?>&amp;part=dague" onclick="return envoiInfo(this.href, 'carte')">Dague</a> - <a href="<?php echo $url2; ?>&amp;part=epee" onclick="return envoiInfo(this.href, 'carte')">Epée</a> - <a href="<?php echo $url2; ?>&amp;part=hache" onclick="return envoiInfo(this.href, 'carte')">Hache</a> - <a href="<?php echo $url2; ?>&amp;part=bouclier" onclick="return envoiInfo(this.href, 'carte')">Bouclier</a> - <a href="<?php echo $url2; ?>&amp;part=baton" onclick="return envoiInfo(this.href, 'carte')">Baton</a></p>

		
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
			</td>
			<td>
				Type
			</td>
			<td>
				Mains
			</td>
			<td>
				Dégats
			</td>
			<td>
				<span onClick="return nd();" onmouseover="return <?php echo make_overlib('Coéf Arc = '.$joueur->get_coef_distance().'<br />Coéf Mélée = '.$joueur->get_coef_melee().'<br />Coéf Incantation = '.$joueur->get_coef_incantation()); ?>" onmouseout="return nd();">Coéf.</span>
			</td>
	<?php
	foreach($types[$type] as $typ)
	{
		echo '
			<td>
				'.$typ[0].'
			</td>';
	}
	?>
			<td>
				Stars
			</td>
			<td>
				Achat
			</td>
		</tr>
		
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
				$echo = 'Arme équipée : '.$row_arme['nom'].' - '.$row_arme['type'].' - Dégats '.$row_arme['degat'];
			}
			else
				$echo = 'Rien n\'est équipé';
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td onmouseover="return <?php echo make_overlib(addslashes($echo)); ?>" onClick="return nd();" onmouseout="nd();">
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['type']; ?>
			</td>
			<td>
				<?php
				echo count(explode(';', $row['mains']));
				?>
			</td>
			<td>
				<?php echo $row['degat']; ?>
			</td>
			<td>
				<span class="<?php echo over_price($coef, $coef_joueur); ?>"><?php echo ($coef); ?>
			</td>
	<?php
	$check = true;
	foreach($types[$type] as $typ)
	{
		if($typ[1] != 'var1')
		{
			$accesseur = "get_$typ[1]";
			echo '
				<td>
					<span class="'.over_price($row[$typ[1]], $joueur->{$accesseur}()).'">'.$row[$typ[1]].'</span>
				</td>';
			if (over_price($row[$typ[1]], $joueur->{$accesseur}()) == 'achat_over') $check = false;
		}
	}
	?>
			<td>
				<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout; ?></span>
			</td>
			<td>
			
			<?php 

			if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($coef, $coef_joueur) == 'achat_normal' AND $check AND over_price($cout, $joueur->get_star())== 'achat_normal')
			{
			?>
				<a href="boutique.php?action=achat&amp;type=arme&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
			<?php
			}
			?>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		</div>

<?php
	}
	else
	{
		$url2 = 'boutique.php?type=armure&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
	?>

		

		<p class="ville_haut"><a href="<?php echo $url2; ?>&amp;part=ceinture" onclick="return envoiInfo(this.href, 'carte')">Ceinture</a> - <a href="<?php echo $url2; ?>&amp;part=chaussure" onclick="return envoiInfo(this.href, 'carte')">Chaussure</a> - <a href="<?php echo $url2; ?>&amp;part=jambe" onclick="return envoiInfo(this.href, 'carte')">Jambe</a> - <a href="<?php echo $url2; ?>&amp;part=main" onclick="return envoiInfo(this.href, 'carte')">Main</a> - <a href="<?php echo $url2; ?>&amp;part=tete" onclick="return envoiInfo(this.href, 'carte')">Tête</a> - <a href="<?php echo $url2; ?>&amp;part=torse" onclick="return envoiInfo(this.href, 'carte')">Torse</a><br />
		<a href="<?php echo $url2; ?>&amp;part=cou" onclick="return envoiInfo(this.href, 'carte')">Cou</a> - <a href="<?php echo $url2; ?>&amp;part=dos" onclick="return envoiInfo(this.href, 'carte')">Dos</a> - <a href="<?php echo $url2; ?>&amp;part=doigt" onclick="return envoiInfo(this.href, 'carte')">Doigt</a></p><br />
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
			</td>
			<td>
				Type
			</td>
			<td>
				PP
			</td>
			<td>
				PM
			</td>
			<td>
				Force
			</td>
			<td>
				Stars
			</td>
			<td>
				Achat
			</td>
		</tr>
		<?php
		
		$color = 1;
		$where = 'lvl_batiment <= '.$level_batiment;
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM armure WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($row['forcex'] > $joueur->get_force() OR $cout > $joueur->get_star()) $couleur = 3;

			if($joueur->inventaire()->$row['type'] != '' AND $joueur->inventaire()->$row['type'] !== 0)
			{
				$armure = decompose_objet($joueur->inventaire()->$row['type']);
				$requete = "SELECT * FROM armure WHERE id = ".$armure['id_objet'];
				$req_armure = $db->query($requete);
				$row_armure = $db->read_array($req_armure);
				$echo = 'Armure équipée : '.$row_armure['nom'].' - PP = '.$row_armure['PP'].' / PM = '.$row_armure['PM'];
			}
			else $echo = 'Armure équipée : Aucune';
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td onmouseover="return <?php echo make_overlib($echo); ?>" onClick="return nd();" onmouseout="return nd();">
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['type']; ?>
			</td>
			<td>
				<?php echo $row['PP']; ?>
			</td>
			<td>
				<?php echo $row['PM']; ?>
			</td>
			<td>
				<span class="<?php echo over_price($row['forcex'], $joueur->get_force()); ?>"><?php echo $row['forcex']; ?></span>
			</td>
			<td>
				<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout; ?></span>
			</td>
			<td>
			<?php
				if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($row['forcex'], $joueur->get_force()) == 'achat_normal')
				{
				?>	
				<a href="boutique.php?action=achat&amp;type=armure&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
				<?php 
				}
				?>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		

<?php
	}
}
?>
