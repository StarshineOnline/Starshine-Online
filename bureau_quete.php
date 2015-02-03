<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($R->is_raz() && $W_row['type'] == 1 && $joueur->get_x() <= 190 && $joueur->get_y() <= 190)
{
	echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	exit (0);
}

?>
<fieldset>
		<legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> >', $joueur->get_pos()); ?> <?php echo '<a href="bureau_quete.php" onclick="return envoiInfo(this.href,\'carte\')">';?> Bureau des Quêtes </a></legend>
 		<?php include_once(root.'ville_bas.php');?>

		<div class="ville_test">
		<p>Voici les différentes Quêtes disponibles :</p>
<?php
if($R->get_diplo($joueur->get_race()) <= 6 OR $R->get_diplo($joueur->get_race()) == 127)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Description de la quète
			case 'description' :
				$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
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
				<a href="bureau_quete.php?action=prendre&amp;id=<?php echo $_GET['id']; ?>" onclick="return envoiInfo(this.href, 'carte')">Prendre cette quête</a><br />
				</div>
			<?php
			break;
			//Prise de la quète
			case 'prendre' :
				$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
				if($joueur->prend_quete($row['id']))
				{
					echo 'Merci de votre aide !<br />';
					if($row['fournisseur'] == '') $link = 'bureau_quete';
					elseif($row['fournisseur'] == 'ecole_combat') $link = 'ecolecombat';
					elseif($row['fournisseur'] == 'boutique') $link = 'alchimiste';
					elseif($row['fournisseur'] == 'magasin') $link = 'alchimiste';
					else $link = $row['fournisseur'];
				}
				else echo $G_erreur.'<br />';
				?>
				<a href="<?php echo $link; ?>.php" onclick="return envoiInfo(this.href, 'carte')">Revenir en arrière</a>
				<?php
			break;
			case 'prendre_tout' :
				$message = prend_quete_tout($joueur);
				echo $message;
				?>
				<a href="bureau_quete.php" onclick="return envoiInfo(this.href, 'carte')">Revenir en arrière</a>
				<?php
			break;
		}
	}
	else
	{
		//Affichage des quêtes

		$interf_princ->add( $G_interf->creer_bureau_quete($R) );
	/*	$return = affiche_quetes('bureau_quete', $joueur);
		if($return[1] > 0)
			echo '<br /><br /><a href="bureau_quete.php?action=prendre_tout" onclick="return envoiInfo(this.href, \'carte\')">Prendre toutes les quêtes.</a>';
*/
	}
}
?>
</div>
</fieldset>
