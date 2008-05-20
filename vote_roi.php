<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
    <h2 class="ville_titre"><?php if(verif_ville($joueur['x'], $joueur['y'])) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="javascript:envoiInfo(\'qg.php?poscase='.$W_case.'\', \'carte\')">';?> Quartier Général </a></h2>
		<?php include('ville_bas.php');?>	
	<div class="ville_test">
<?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if($joueur['honneur'] >= $R['honneur_candidat'])
	{
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				case 'vote' :
					$date = date_prochain_mandat();
					$requete = "SELECT * FROM vote WHERE id_perso = ".$joueur['ID']." AND date = '".$date."'";
					$db->query($requete);
					if($db->num_rows > 0)
					{
						echo '<h5>Vous avez déjà voté !</h5>';
					}
					else
					{

						
					    validate_integer_value($_GET['id_candidat']);
						validate_against_printf_predicate($_GET['id_candidat'], "select count(`id`) from candidat where `date` = '$date' and `id_perso` = '%d'", 1);
						$requete = "INSERT INTO vote ( `id` , `id_perso`, `id_candidat`, `date` , `royaume`) VALUES('', ".$joueur['ID'].", ".sSQL($_GET['id_candidat']).", '".$date."', ".$R['ID'].")";
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
		$date = date_prochain_mandat();
		$requete = "SELECT * FROM candidat WHERE date = '".$date."' AND royaume = ".$R['ID'];
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			?>
			<option value="<?php echo $row['id_perso']; ?>"><?php echo $row['nom']; ?></option>
			<?php
		}
		?>
	</select>
		<?php
		$url = "vote_roi.php?action=vote&amp;id_candidat=' + document.getElementById('id_candidat').value + '&amp;poscase=".$W_case;
		?>
	<input type="button" onclick="javascript:envoiInfo('<?php echo $url; ?>', 'carte');" value="Voter !">
	<?php
		//echo $url;
		}
	}	
}
?>