<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$pos = convert_in_pos($joueur['x'], $joueur['y']);
$W_requete = "SELECT royaume FROM map WHERE ID = ".$pos;
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
?>
    	<h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville('<a href="ville.php?poscase='.$pos.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R['nom'].'</a> - ', $pos); ?> <?php echo '<a href="taverne.php?poscase='.$pos.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Magasin </a></h2>
				<?php include('ville_bas.php');?>
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
				if ($joueur['star'] >= $cout)
				{
					if(prend_objet('o'.$row['id'], $joueur))
					{
						$joueur['star'] = $joueur['star'] - $cout;
						$requete = "UPDATE perso SET star = ".$joueur['star']." WHERE ID = ".$_SESSION['ID'];
						$req = $db->query($requete);
						//Récupération de la taxe
						if($taxe > 0)
						{
							$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
							$db->query($requete);
							$requete = "UPDATE argent_royaume SET magasin = magasin + ".$taxe." WHERE race = '".$R['race']."'";
							$db->query($requete);
						}
						echo '<h5>Objet acheté !</h5>';
					}
					else
					{
						echo $G_erreur;
					}
				}
				else
				{
					echo 'h5>Vous n\'avez pas assez de Stars</h5>';
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
					echo '<h6>Vous augmentez la recherche de votre royaume en alchimie de '.$recherche.' points</h6>';
					$requete = "UPDATE perso SET pa = pa - 10 WHERE ID = ".$joueur['ID'];
					$db->query($requete);
					//Augmentation de la compétence d'architecture
					$augmentation = augmentation_competence('alchimie', $joueur, 2);
					if ($augmentation[1] == 1)
					{
						$joueur['alchimie'] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['alchimie'].' en alchimie</span><br />';
						$requete = "UPDATE perso SET alchimie = ".$joueur['alchimie']." WHERE ID = ".$joueur['ID'];
						$db->query($requete);
					}
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
				<a href="alchimiste.php?action=recherche" onclick="return envoiInfo(this.href, 'carte');">Faire des recherches en alchimie (10 PA)</a>
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
			if($row['forcex'] > $joueur['force'] OR $row['melee'] > $joueur['melee'] OR $cout > $joueur['star'] OR $row['distance'] > $joueur['distance']) $couleur = 3;
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

<?php
}
?>