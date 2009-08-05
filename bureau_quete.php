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

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

?>
		<h2 class="ville_titre"><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="bureau_quete.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Bureau des Quêtes </a></h2>
 		<?php include_once(root.'ville_bas.php');?>

		<div class="ville_test">
		<p>Voici les différentes Quêtes disponibles :</p>
<?php
$W_distance = detection_distance($W_case, $joueur->get_pos());
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
			//Description de la quète
			case 'description' :
			?>
				<div class="texte_normal">
				<h3 style="margin-bottom : 3px;"><?php echo $row['nom']; ?></h3>
				<span style="font-style : italic;">Niveau conseillé <?php echo $row['lvl_joueur']; ?><br />
				Répétable : <?php if($row['repete'] == 'y') echo 'Oui'; else echo 'Non'; ?><br />
				<?php if($row['mode'] == 'g') echo 'Groupe'; else echo 'Solo'; ?></span><br />
				<br />
				<?php echo nl2br($row['description']); ?>
				<h3>Récompense</h3>
				<ul>
					<li>Stars : <?php echo $row['star']; ?></li>
					<li>Expérience : <?php echo $row['exp']; ?></li>
					<li>Honneur : <?php echo $row['honneur']; ?></li>
					<li><strong>Objets</strong> :</li>
					<?php
					$rewards = explode(';', $row['reward']);
					$r = 0;
					while($r < count($rewards))
					{
						$reward_exp = explode('-', $rewards[$r]);
						$reward_id = $reward_exp[0];
						$reward_id_objet = mb_substr($reward_id, 1);
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
								echo '<li>Objet aléatoire</li>';
							break;
						}
						$r++;
					}
					?>
				</ul>
				<br />
				<a href="bureau_quete.php?action=prendre&amp;id=<?php echo $_GET['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Prendre cette quête</a><br />
				</div>
			<?php
			break;
			//Prise de la quète
			case 'prendre' :
				if($joueur->prend_quete($quete))
				{
					echo 'Merci de votre aide !<br />';
					if($row['fournisseur'] == '') $link = 'bureau_quete';
					elseif($row['fournisseur'] == 'ecole_combat') $link = 'ecolecombat';
					elseif($row['fournisseur'] == 'boutique') $link = 'alchimiste';
					else $link = $row['fournisseur'];
				}
				else echo $G_erreur.'<br />';
				?>
				<a href="<?php echo $link; ?>.php?poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Revenir en arrière</a>
				<?php
			break;
		}
	}
	else
	{	
		//Affichage des quêtes
		$return = affiche_quetes('', $joueur);
		echo $return[0];

	}
}
?>
</div>

