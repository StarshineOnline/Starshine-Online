<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//V�rifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
		<h2 class="ville_titre"><?php if(verif_ville($joueur['x'], $joueur['y'])) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="javascript:envoiInfo(\'bureau_quete.php?poscase='.$W_case.'\',\'carte\')">';?> Bureau des Qu�tes </a></h2>
 		<?php include('ville_bas.php');?>

		<div class="ville_test">
		<p>Voici les diff�rentes Qu�tes disponibles :</p>
<?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
		$req = $db->query($requete);
		$row = $db->read_array($req);
		switch ($_GET['action'])
		{
			//Description de la qu�te
			case 'description' :
			?>
				<div class="texte_normal">
				<h3 style="margin-bottom : 3px;"><?php echo $row['nom']; ?></h3>
				<span style="font-style : italic;">Niveau conseill� <?php echo $row['lvl_joueur']; ?><br />
				R�p�table : <?php if($row['repete'] == 'y') echo 'Oui'; else echo 'Non'; ?><br />
				<?php if($row['mode'] == 'g') echo 'Groupe'; else echo 'Solo'; ?></span><br />
				<br />
				<?php echo nl2br($row['description']); ?>
				<h3>R�compense</h3>
				<ul>
					<li>Stars : <?php echo $row['star']; ?></li>
					<li>Exp�rience : <?php echo $row['exp']; ?></li>
					<li>Honneur : <?php echo $row['honneur']; ?></li>
					<li><strong>Objets</strong> :</li>
					<?php
					$rewards = explode(';', $row['reward']);
					$r = 0;
					while($r < count($rewards))
					{
						$reward_exp = explode('-', $rewards[$r]);
						$reward_id = $reward_exp[0];
						$reward_id_objet = substr($reward_id, 1);
						$reward_nb = $reward_exp[1];
						switch($reward_id[0])
						{
							case 'r' :
								$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
								$req_r = $db->query($requete);
								$row_r = $db->read_assoc($req_r);
								echo '<li>Recette de '.$row_r['nom'].' X '.$reward_nb.'</li>';
							break;
							case 'x' :
								echo '<li>Objet al�atoire</li>';
							break;
						}
						$r++;
					}
					?>
				</ul>
				<br />
				<a href="javascript:envoiInfo('bureau_quete.php?action=prendre&amp;id=<?php echo $_GET['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Prendre cette qu�te</a><br />
				</div>
			<?php
			break;
			//Prise de la qu�te
			case 'prendre' :
				$numero_quete = (count($joueur['quete']));
				$valid = true;
				//V�rifie si le joueur n'a pas d�j� pris la qu�te.
				if($joueur['quete'] != '')
				{
					foreach($joueur['quete'] as $quest)
					{
						if($quest['id_quete'] == $_GET['id']) $valid = false;
					}
				}
				else
				{
					$numero_quete = 0;
				}
				if($valid)
				{
					$quete = unserialize($row['objectif']);
					$count = count($quete);
					$i = 0;
					while($i < $count)
					{
						$joueur['quete'][$numero_quete]['objectif'][$i]->cible = $quete[$i]->cible;
						$joueur['quete'][$numero_quete]['objectif'][$i]->requis = $quete[$i]->requis;
						$joueur['quete'][$numero_quete]['id_quete'] = $row['id'];
						$joueur['quete'][$numero_quete]['objectif'][$i]->nombre = 0;
						$i++;
					}
					$joueur_quete = serialize($joueur['quete']);
					$requete = "UPDATE perso SET quete = '".$joueur_quete."' WHERE ID = ".$_SESSION['ID'];
					$req = $db->query($requete);
					echo 'Merci de votre aide !<br />';
					if($row['fournisseur'] == '') $link = 'bureau_quete';
					else $link = $row['fournisseur'];
					?>
					<a href="javascript:envoiInfo('<?php echo $link; ?>.php?poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Revenir en arri�re</a>
					<?php
				}
				else
				{
					echo 'Vous avez d�j� cette qu�te en cours !<br />';
					?>
					<a href="javascript:envoiInfo('<?php echo $link; ?>.php?poscase=<?php echo $_GET['poscase']; ?>', 'carte')">Revenir en arri�re</a>
					<?php
				}
			break;
		}
	}
	else
	{	
		//Affichage des qu�tes
		$return = affiche_quetes('', $joueur);
		echo $return[0];

	}
}
?>
</div>

