<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php //	 -*- tab-width:	 2 -*-

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

?><h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="ecolecombat.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Ecole de combat </a></h2>
<?php include_once(root.'ville_bas.php');?>
<?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
$cout_app = 500;
if($W_row['type'] == 1)
{
	//On recherche le niveau de la construction
	$batiment = 'ecole_combat';
	$requete = "SELECT * FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE batiment_ville.type = '".$batiment."' AND construction_ville.id_royaume = ".$R->get_id();
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Si le batiment est inactif, on met le batiment au niveau 1, sinon c'est bon
	if($row['statut'] == 'inactif') $level_batiment = 1; else $level_batiment = $row['level'];
	if(isset($_GET['ecole']))
	{
		$ecole = $_GET['ecole'];
		if($ecole == 'comp_jeu')
		{
			$nom_autre_ecole = 'En combat';
			$autre_ecole = 'comp_combat';
		}
		else
		{
			$nom_autre_ecole = 'Hors combat';
			$autre_ecole = 'comp_jeu';
		}
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				//Achat
				case 'apprendre' :
					apprend_competence($ecole, sSQL($_GET['id']), $joueur, $R, false);
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
		$url = 'ecolemagie.php?ecole='.$_GET['ecole'].'&amp;type='.$_GET['type'].'&amp;poscase='.$W_case.'&amp;part='.$_GET['part'].'&amp;order=';
		?>
		<div class="ville_test">
		Passer à une autre école de combat : <a href="ecolecombat.php?ecole=<?php echo $autre_ecole; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')"><?php echo $nom_autre_ecole; ?></a>
		<br /><br />

		<?php
		//Affichage du magasin des armes
		$url2 = 'ecolecombat.php?ecole='.$_GET['ecole'].'&amp;order='.$_GET['order'];
		?>
			<p class="ville_haut"><a href="<?php echo $url2; ?>&amp;part=melee" onclick="return envoiInfo(this.href, 'carte')">Mélée</a> | <a href="<?php echo $url2; ?>&amp;part=distance" onclick="return envoiInfo(this.href, 'carte')">Distance</a> | <a href="<?php echo $url2; ?>&amp;part=esquive" onclick="return envoiInfo(this.href, 'carte')">Esquive</a> | <a href="<?php echo $url2; ?>&amp;part=blocage" onclick="return envoiInfo(this.href, 'carte')">Blocage</a></p>
			<table class="marchand" cellspacing="0px">
			<tr class="header trcolor2">
				<td>
					Nom
				</td>
				<td>
					MP
				</td>
				<td>
					Effet
				</td>
				<td>
					Compétence
				</td>
				<td>
					Arme
				</td>
				<td>
					Stars
				</td>
				<td>
					Apprendre
				</td>
			</tr>
			
			<?php
			
			$color = 1;
			$where = 'lvl_batiment <= '.$level_batiment;
			if(array_key_exists('part', $_GET))
			{
				$where .= " AND comp_assoc = '".sSQL($_GET['part'])."'";
			}
			$get = 'get_'.$ecole;
			$comps = explode(';', $joueur->$get());
			$count = count($comps);
			$comps = implode(', ', $comps);
			if($comps != '' AND $count > 0) $where .= " AND id NOT IN (".$comps.") ";
			$requete = "SELECT * FROM ".$ecole." WHERE ".$where." ORDER BY".$ordre;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				if($row['requis'] != '999')
				{
					$taxe = ceil($row['prix'] * $R->get_taxe() / 100);
					$cout = $row['prix'] + $taxe;
					$couleur = $color;
					$get_carac = 'get_'.$row['carac_assoc'];
					$carac_joueur = $joueur->$get_carac();
					$get_comp = 'get_'.$row['comp_assoc'];
					$comp_joueur = $joueur->$get_comp();
					if (isset($joueur->bonus_ignorables)) {
						if (isset($joueur->bonus_ignorables[$row['carac_assoc']]))
							$carac_joueur -= $joueur->bonus_ignorables[$row['carac_assoc']];
						if (isset($joueur->bonus_ignorables[$row['comp_assoc']]))
							$comp_joueur -= $joueur->bonus_ignorables[$row['comp_assoc']];
					}
					if($row['carac_requis'] > $carac_joueur OR $row['comp_requis'] > $comp_joueur OR $cout > $joueur->get_star()) $couleur = 3;
					$row['cible2'] = $G_cibles[$row['cible']];
				?>
				<tr class="element trcolor<?php echo $couleur; ?>">
					<td onmouseover="return <?php echo make_overlib(description('[%cible2%] '.$row['description'], $row)); ?>;" onmouseout="return nd();">
						<?php echo $row['nom']; ?>
					</td>
					<td>
						<?php echo $row['mp']; ?>
					</td>
					<td>
						<?php echo $row['effet']; ?>
					</td>
					<td>
						<span class="<?php echo over_price($row['comp_requis'], $comp_joueur); ?>"><?php echo $Gtrad[$row['comp_assoc']].' ('.$row['comp_requis'].')'; ?></span>
					</td>
					<td>
						<?php
						$arme_requis = explode(';', $row['arme_requis']);
						$arme_requis = implode(' - ', $arme_requis);
						echo $arme_requis;
						?>
					</td>
					<td>
						<span class="<?php echo over_price($cout, $joueur->get_star()); ?>"><?php echo $cout; ?></span>
					</td>
					<td>
					<?php
					if (over_price($cout, $joueur->get_star()) == 'achat_normal' AND over_price($row['comp_requis'], $comp_joueur) == 'achat_normal')
					{	
					?>
						<a href="ecolecombat.php?ecole=<?php echo $_GET['ecole']; ?>&amp;action=apprendre&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Apprendre</span></a>
					<?php 
					}
					?>
					</td>
				</tr>
				<?php
					if($color == 1) $color = 2; else $color = 1;
				}
			}
			?>
				</table>
				</div>

				
		<?php
	}
	elseif(array_key_exists('app', $_GET))
	{
		$taxe = ceil($cout_app * $R->get_taxe() / 100);
		$cout = $cout_app + $taxe;
		if($joueur->get_star() >= $cout)
		{
			$joueur->set_star($joueur->get_star() - $cout);
			$joueur->sauver();
			echo 'L\'apprentissage de '.$Gtrad[$_GET['app']].' est un succès !<br />';
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars<br />';
		}
		?>
		<a href="ecolecombat.php" onclick="return envoiInfo(this.href, 'carte')">Retour à l&rsquo;école de combat</a>
		<?php
	}
	else
	{
			?>

		<?php
		//Affichage des quêtes
		$return = affiche_quetes('ecole_combat', $joueur);
		if($return[1] > 0)
		{
			echo '<div class="ville_test"><span class="texte_normal">';
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
			echo '</span></div><br />';
		}
		?>
		<div class="ville_test">
			<ul class="ville">
			<?php
			$taxe = ceil($cout_app * $R->get_taxe() / 100);
			$cout = $cout_app + $taxe;
			?>
				<li>
					<a href="ecolecombat.php?ecole=comp_jeu&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Compétences hors combat</a>
				</li>
				<li>
					<a href="ecolecombat.php?ecole=comp_combat&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Compétences de combat</a>
				</li>
			</ul>
			</div>

			
			<?php
		}
}
?>
