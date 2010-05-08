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

$W_requete = 'SELECT royaume, type FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
	<fieldset><legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> > ', $joueur->get_pos()); ?> <?php echo '<a href="vote_roi.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Vote </a></legend>
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
	$roi = $joueur->get_grade()->get_id() == 6;
  $is_election = elections::is_mois_election($R->get_id(), $roi);
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			case 'vote' :
      	if( $is_election && date("d") >= 15 )
      	{
				  $elections = elections::get_prochain_election($R->get_id(), $roi);
  				$prochain_election = $elections[0];
  				$requete = "SELECT id FROM vote WHERE id_perso = ".$joueur->get_id()." AND id_election = ".$prochain_election->get_id();
  				$db->query($requete);
  				if($db->num_rows > 0)
  				{
            if( $prochain_election->get_type() == 'universel' )
              echo '<h5>Vous avez déjà voté !</h5>';
  					else
              echo "<h5>Vous avez déjà nommé quelqu'un !</h5>";  					 
  				}
  				else
  				{
  				    //validate_integer_value($_GET['id_candidat']);
  					//validate_against_printf_predicate($_GET['id_candidat'], "select count(`id`) from candidat where `date` = '$date' and `id_perso` = '%d'", 1);
  					$candidat = new candidat($_GET['id_candidat']);
  					$requete = "INSERT INTO vote ( `id` , `id_perso`, `id_candidat`, `id_election`) VALUES('', ".$joueur->get_id().", ".$candidat->get_id_perso().", ".$prochain_election->get_id().")";
  					if($db->query($requete))
  					{
              if( $prochain_election->get_type() == 'universel' )
  						  echo 'Votre vote a bien été pris en compte';
  						else
  						  echo 'Votre nomination a bien été prise en compte';
  					}
  				}
				}
        else
        {
    			echo "<h5>Vous n'avez pas d'élection de prévu !</h5>";
        }
			break;
		}
	}
	else
	{
  	if( $is_election )
  	{
  	  $elections = elections::get_prochain_election($R->get_id(), $roi);
      $prochain_election = $elections[0];
      if( $prochain_election->get_type() == 'universel' )
      {
        $label_btn = "Voter !";
      	?>
      	Pour qui allez vous voter ?<br />
      	<?php
      }
      else
      {
        $label_btn = "Nommer !";
      	?>
      	Qui allez vous nommer ?<br />
      	<?php
      }
    	$candidats = candidat::create('id_election', $prochain_election->get_id());
    	?>
    	<select name="id_candidat" id="id_candidat" onchange="envoiInfo('info_candidat.php?id_candidat=' + $('#id_candidat').val(), 'info_candidat');">
    		<?php
    		$i = 0;
    		foreach($candidats as $candidat)
    		{
    			if($i == 0) $_GET['id_candidat'] = $candidat->get_id();
    			?>
    			<option value="<?php echo $candidat->get_id(); ?>"><?php echo $candidat->get_nom(); ?> / pour <?php echo $candidat->get_duree(); ?> mois / Prochaine élection : <?php echo $candidat->get_type(); ?></option>
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
    	<input type="button" onclick="envoiInfo('vote_roi.php?action=vote&id_candidat=' + document.getElementById('id_candidat').value, 'carte');" value="<?php echo $label_btn; ?>">
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