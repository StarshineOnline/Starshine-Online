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

$W_requete = 'SELECT royaume, type FROM map WHERE id =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
<fieldset>
	<legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> &gt; <?php echo '<a href="vie_royaume.php" onclick="return envoiInfo(this.href,\'carte\')">';?> Vie du royaume </a></legend>
<?php
//Uniquement si le joueur se trouve sur une case de ville
if($W_row['type'] == 1)
{
	include_once('ville_bas.php');
	//Si on est dans notre royaume
	if($R->get_diplo($joueur->get_race()) == 127)
	{
		$is_election = elections::is_mois_election($R->get_id());
		if( $is_election )
		{
  		if(date("d") >= 5 && date("d") < 15)
  		{
  			?>
  			<li>
  				<a href="candidature.php" onclick="return envoiInfo(this.href, 'carte')">Candidature</a>
  			</li>
  			<?php
  		}
  		elseif(date("d") >= 15)
  		{
        $elections = elections::get_prochain_election($R->get_id(), true);
  		  if( $elections[0]->get_type() == 'universel' )
  		  {
    			?>
    			<li>
    				<a href="vote_roi.php" onclick="return envoiInfo(this.href, 'carte')">Vote</a>
    			</li>
  			<?php
        }
        elseif( $joueur->get_grade()->get_id() == 6 )
        {
    			?>
    			<li>
    				<a href="vote_roi.php" onclick="return envoiInfo(this.href, 'carte')">Nomination</a>
    			</li>
  			<?php
        }
  		}
    }
		else
		{
		  //Pas d'élection prévue prochainement, on peut renverser le pouvoir
		  $is_revolution = revolution::is_mois_revolution($R->get_id());
		  if( $is_revolution )
      {
        // Il y a une révolution : on l'indique et propose de voter
			  ?>
  			<li>
  			  <p><b>Une révolution a été déclenchée !</b></p>
  				<a href="revolution.php" onclick="return envoiInfo(this.href, 'carte')">Voter pour ou contre la révolution</a>
  			</li>
  			<?php
      }
      elseif( date("d") <= 20 )
		  {
		    // Il n'y a pas de révolution : on propose d'en déclencher une 
			  ?>
  			<li>
  				<a href="revolution.php" onclick="return envoiInfo(this.href, 'carte')">Déclencher une révolution</a>
  			</li>
  			<?php
      }
		}
	}
}

?>
</fieldset>
