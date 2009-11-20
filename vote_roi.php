<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE id =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
	<h2 class="ville_titre"><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> -', $joueur->get_pos()); ?> <?php echo '<a href="qg.php" onclick="return envoiInfo(this.href, \'carte\')">';?> Quartier Général </a></h2>
		<?php include_once(root.'ville_bas.php');?>	
	<div class="ville_test">
<?php
$check = false;
if( $W_row['type'] == 1 )
  $check = true;
elseif( $batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $Trace[$joueur->get_race()]['numrace']) )
  $check = $batiment['type'] == 'bourg';
if( $check )
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			case 'vote' :
			  $roi = $joueur->get_grade()->get_id() == 6;
				$elections = elections::get_prochain_election($R->get_id(), $roi);
      	if( count($elections) )
      	{
  				$prochain_election = $elections[0];
  				$requete = "SELECT id FROM vote WHERE id_perso = ".$joueur->get_id()." AND id_election = ".$prochain_election->get_id();
  				$db->query($requete);
  				if($db->num_rows > 0)
  				{
  					echo '<h5>Vous avez déjà voté !</h5>';
  				}
  				else
  				{
  				    //validate_integer_value($_GET['id_candidat']);
  					//validate_against_printf_predicate($_GET['id_candidat'], "select count(`id`) from candidat where `date` = '$date' and `id_perso` = '%d'", 1);
  					$candidat = new candidat($_GET['id_candidat']);
  					$requete = "INSERT INTO vote ( `id` , `id_perso`, `id_candidat`, `id_election`) VALUES('', ".$joueur->get_id().", ".$candidat->get_id_perso().", ".$prochain_election->get_id().")";
  					if($db->query($requete))
  					{
  						echo 'Votre vote a bien été pris en compte';
  					}
  				}
				}
        else
        {
    			echo "<h5>Vous n'avez pas d'élections de prévu !</h5>";
        }
			break;
		}
	}
	else
	{
  	$roi = $joueur->get_grade()->get_id() == 6;
  	$elections = elections::get_prochain_election($R->get_id(), $roi);
  	if( count($elections) )
  	{
      $prochain_election = $elections[0];
      if( $prochain_election->get_type() == 'universel' )
      {
      	?>
      	Pour qui allez vous voter ?<br />
      	<?php
      }
      else
      {
      	?>
      	Qui allez vous nommer ?<br />
      	<?php
      }
    	$requete = "SELECT * FROM candidat WHERE id_election = ".$prochain_election->get_id();
    	$req = $db->query($requete);
    	?>
    	<select name="id_candidat" id="id_candidat" onchange="envoiInfo('info_candidat.php?id_candidat=' + $('id_candidat').value, 'info_candidat');">
    		<?php
    		$i = 0;
    		while($row = $db->read_assoc($req))
    		{
    			if($i == 0) $_GET['id_candidat'] = $row['id'];
    			?>
    			<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?> / pour <?php echo $row['duree']; ?> mois / Prochaine élection : <?php echo $row['type']; ?></option>
    			<?php
    			$i++;
    		}
    		?>
    	</select>
    	<div id="info_candidat">
    		<?php
    		include('info_candidat.php');
    		?>
    	</div>
    	<input type="button" onclick="envoiInfo('vote_roi.php?action=vote&id_candidat=' + document.getElementById('id_candidat').value, 'carte');" value="Voter !">
    	<?php
    		//echo $url;
    }
    else
    {
			echo "<h5>Vous n'avez pas d'élections de prévu !</h5>";
    }
	}
}
?>