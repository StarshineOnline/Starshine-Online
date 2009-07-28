<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$pos = convert_in_pos($joueur->get_x(), $joueur->get_y());
$W_requete = "SELECT royaume FROM map WHERE id = ".$pos;
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);

if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
?>
    	<h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville('<a href="ville.php?poscase='.$pos.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R['nom'].'</a> - ', $pos); ?> <?php echo '<a href="taverne.php?poscase='.$pos.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Alchimiste </a></h2>
				<?php include_once(root.'ville_bas.php');?>
		<?php
$W_distance = detection_distance($pos, $_SESSION["position"]);
$W_coord = convert_in_coord($pos);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				$requete = "SELECT id, prix FROM objet WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$taxe = ceil($row['prix'] * $R['taxe'] / 100);
				$cout = $row['prix'] + $taxe;
				if ($joueur->get_star() >= $cout)
				{
					if(prend_objet('o'.$row['id'], $joueur))
					{
						$joueur->set_star($joueur->get_star() - $cout);
						$joueur->sauver();
						//Récupération de la taxe
						if($taxe > 0)
						{
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET magasin = magasin + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h6>Objet acheté !</h6>';
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
			case 'achat_recette' :
				$recette = new craft_recette($_GET['id']);
				$taxe = ceil($recette->prix * $R['taxe'] / 100);
				$cout = $recette->prix + $taxe;
				if ($joueur->get_star() >= $cout)
				{
					$perso = new perso_recette();
					$perso_recette = $perso->recov($joueur['ID'], $_GET['id']);
					if(!$perso_recette)
					{
						$perso_recette = new perso_recette();
						$perso_recette->id_perso = $joueur['ID'];
						$perso_recette->id_recette = $_GET['id'];
						$perso_recette->sauver();
						$joueur->set_star($joueur->get_star() - $cout);
						$joueur->sauver();
						//Récupération de la taxe
						if($taxe > 0)
						{
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET magasin = magasin + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h6>Recette achetée !</h6>';
					}
					else
					{
						echo '<h5>Vous avez déjà cette recette</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de Stars</h5>';
				}
			break;
			//Recherche
			case 'recherche' :
				if($joueur['pa'] >= 10)
				{
					//Combien il augmente la recherche ?
					$recherche = rand(1, $joueur['alchimie']);
					$requete = "UPDATE royaume SET alchimie = alchimie + ".$recherche." WHERE ID = ".$R['ID'];
					$db->query($requete);
					$R['alchimie'] += $recherche;
					echo '<h6>Vous augmentez la recherche de votre royaume en alchimie de '.$recherche.' points</h6>';
					$joueur->set_pa($joueur->get_pa() - 10);
					//Augmentation de la compétence d'architecture
					$augmentation = augmentation_competence('alchimie', $joueur, 2);
					if ($augmentation[1] == 1)
					{
						$joueur->set_alchimie($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['alchimie'].' en alchimie</span><br />';
					}
					$joueur->sauver();
				}
				else echo '<h5>Vous n\'avez pas assez de PA</h5>';
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
	$url = 'alchimiste.php?order='.$fort;

		$types = array();
	?>
		<?php
		//Affichage des quêtes
		$return = affiche_quetes('magasin', $joueur);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo '<div class="ville_test"><span class="texte_normal">';
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
			echo '</span></div><br />';
		}
		?>
		<div class="ville_test">
			<span class="texte_normal">
				<?php
				$requete = "SELECT royaume_alchimie FROM craft_recette WHERE royaume_alchimie < ".$R['alchimie']." ORDER BY royaume_alchimie DESC LIMIT 0, 1";
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$min = $row['royaume_alchimie'];
				$requete = "SELECT royaume_alchimie FROM craft_recette WHERE royaume_alchimie > ".$R['alchimie']." ORDER BY royaume_alchimie ASC LIMIT 0, 1";
				$req = $db->query($requete);
				$row = $db->read_assoc($req);
				$max = $row['royaume_alchimie'];
				if ($max == 0) echo 'Plus de recettes à chercher !<br />';
				else {
					$total = $max - $min;
					$actuel = $R['alchimie'] - $min;
					$pourcent = round((($actuel / $total) * 100), 2);
					echo $pourcent.'% du déblocage de la prochaine recette !<br />';
					if($R['diplo'] == 127)
						{
				?>
				<a href="alchimiste.php?action=recherche" onclick="return envoiInfo(this.href, 'carte');">Faire des recherches en alchimie (10 PA)</a>
				<?php
					 }
				}
				?>
			</span>
		</div>
		<br />
		<div class="ville_test">
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
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
		$where = " achetable = 'y'";
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".sSQL($_GET['part'])."'";
		}
		$requete = "SELECT * FROM objet WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($row['forcex'] > $joueur->get_force() OR $row['melee'] > $joueur->get_melee() OR $cout > $joueur->get_star() OR $row['distance'] > $joueur->get_distance()) $couleur = 3;
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td>
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<a href="alchimiste.php?action=achat&amp;id=<?php echo $row['id']; ?>&amp;type=<?php echo $row['type']; ?><?php echo $fort; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		</div>
		<div class="ville_test">
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
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
		$requete = "SELECT * FROM craft_recette WHERE royaume_alchimie <= ".$R['alchimie']." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($cout > $joueur->get_star()) $couleur = 3;
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td>
				Recette <?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<a href="alchimiste.php?action=achat_recette&amp;id=<?php echo $row['id']; ?><?php echo $fort; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
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
?>
