<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
?>
	<h2 class="ville_titre"><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> -', $W_case); ?> <?php echo '<a href="qg.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Quartier Général </a></h2>
		<?php include_once(root.'ville_bas.php');?>	
	<div class="ville_test">
<?php
if($W_row['type'] == 1)
{
	if($joueur['honneur'] >= $R->get_honneur_candidat())
	{
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				case 'vote' :
					$elections = elections::get_prochain_election($R->get_id());
					$prochain_election = $elections[0];
					$requete = "SELECT id FROM vote WHERE id_perso = ".$joueur->get_id()." AND id_election = ".$prochain_election->get_id();
					$db->query($requete);
					if($db->num_rows > 0)
					{
						echo '<h5>Vous avez déjà voté !</h5>';
					}
					else
					{
					    validate_integer_value($_GET['id_candidat']);
						validate_against_printf_predicate($_GET['id_candidat'], "select count(`id`) from candidat where `date` = '$date' and `id_perso` = '%d'", 1);
						$requete = "INSERT INTO vote ( `id` , `id_perso`, `id_candidat`, `id_election`) VALUES('', ".$joueur->get_id().", ".sSQL($_GET['id_candidat']).", ".$prochain_election->get_id().")";
						if($db->query($requete))
						{
							echo 'Votre vote a bien été pris en compte';
						}
					}
				break;
			}
		}
		else
		{
	?>
	Pour qui allez vous voter ?<br />
	<select name="id_candidat" id="id_candidat">
		<?php
		$elections = elections::get_prochain_election($R->get_id());
		$prochain_election = $elections[0];
		$requete = "SELECT * FROM candidat WHERE id_election = '".$prochain_election->get_id();
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			?>
			<option value="<?php echo $row['id_perso']; ?>"><?php echo $row['nom']; ?> / pour <?php echo $row['duree']; ?> mois / Prochaine élection : <?php echo $row['type']; ?></option>
			<?php
		}
		?>
	</select>
		<?php
		$url = "vote_roi.php?action=vote&amp;id_candidat=' + document.getElementById('id_candidat').value + '&amp;poscase=".$W_case;
		?>
	<input type="button" onclick="envoiInfo('<?php echo $url; ?>', 'carte');" value="Voter !">
	<?php
		//echo $url;
		}
	}	
}
?>