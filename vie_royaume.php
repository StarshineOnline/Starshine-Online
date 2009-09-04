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
?>
	<h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="vie_royaume.php" onclick="return envoiInfo(this.href,\'carte\')">';?> Vie du royaume </a></h2>
<?php
//Uniquement si le joueur se trouve sur une case de ville
if($W_row['type'] == 1)
{
	include('ville_bas.php');
	//Si on est dans notre royaume
	if($R->get_diplo($joueur->get_race()) == 127)
	{
		$is_election = elections::is_mois_election($R->get_id());
		if($is_election && date("d") >= 5 && date("d") < 15)
		{
			?>
			<li>
				<a href="candidature.php" onclick="return envoiInfo(this.href, 'carte')">Candidature</a>
			</li>
			<?php
		}
		if($is_election && date("d") >= 15)
		{
			?>
			<li>
				<a href="vote_roi.php" onclick="return envoiInfo(this.href, 'carte')">Vote</a>
			</li>
			<?php
		}
		//Pas d'élection prévue prochainement, on peut renverser le pouvoir
		if(!$is_election)
		{
			?>
			<li>
				<a href="revolution.php" onclick="return envoiInfo(this.href, 'carte')">Déclencher une révolution</a>
			</li>
			<?php
		}
	}
}
?>
