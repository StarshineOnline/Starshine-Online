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

$bourg = new construction($_GET['id_construction']);

//Informations sur le batiment
$batiment = new batiment($bourg->get_id_batiment());
?>
	<div id="carte">
	<fieldset>
		<legend><?php echo $batiment->get_nom(); ?></legend>
<?php

if($bourg->get_x() == $joueur->get_x() AND $bourg->get_y() == $joueur->get_y() AND $joueur->get_race() == $R->get_race())
{
	?>
	<ul class="ville">
	<?php
	if($batiment->get_bonus7() == 1)
	{
	?>
		<li>
			<a href="taverne.php" onclick="return envoiInfo(this.href, 'carte')">Taverne</a>
		</li>
		<li>
			<a href="poste.php" onclick="return envoiInfo(this.href, 'carte')">La Poste</a>
		</li>
	<?php
	}
	?>
		<li>
			<a href="bureau_quete.php" onclick="return envoiInfo(this.href, 'carte')">Bureau des quètes</a>
		</li>
		<li>
			<a href="ecurie.php" onclick="return envoiInfo(this.href, 'carte')">Ecurie</a>
		</li>
  <?php
  $is_election = elections::is_mois_election($R->get_id());
	if($is_election)
  {
    if( date("d") >= 5 AND date("d") < 15 )
  	{
    ?>
  		<li>
  			<a href="candidature.php" onclick="return envoiInfo(this.href, 'carte')">Candidature</a>
  		</li>
    <?php
  	}
  	elseif( date("d") >= 15 )
  	{
      $elections = elections::get_prochain_election($R->get_id(), true);
      if( $elections[0]->get_type() == 'universel' )
  		{
      ?>
    		<li>
    			<a href="vote_roi.php?poscase=<?php echo $joueur->get_pos(); ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">Vote</a>
    		</li>
      <?php
      }
      elseif( $joueur->get_grade()->get_id() == 6 )
      {
      ?>
    		<li>
    			<a href="vote_roi.php?poscase=<?php echo $joueur->get_pos(); ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">Nomination</a>
    		</li>
      <?php
      }
  	}
  }
  else
  {
	  //Si il y a une révolution en cours
	  $is_revolution = revolution::is_mois_revolution($R->get_id());
	  if( $is_revolution )
    {
      // Il y a une révolution : on propose de voter
		?>
			<li>
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
	if($batiment->get_bonus7() == 1 AND ($joueur->get_rang_royaume() == 6 ||
																			 $R->get_ministre_economie() == $joueur->get_id() ||
																			 $R->get_ministre_militaire() == $joueur->get_id() ))
	{
?>
		<li>
			<a href="roi/">Gestion du royaume</a>
		</li>
<?php
	}
?>
    	<li>
      		<a href="teleport.php" onclick="return envoiInfo(this.href, 'carte')">Pierre de Téléportation</a>
    	</li>
	</ul>
<?php
}
?>
</fieldset>
	</div>
