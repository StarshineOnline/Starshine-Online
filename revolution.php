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
	<fieldset><legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> > ', $joueur->get_pos()); ?> <?php echo '<a href="revolution.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Révolution </a></legend>
<?php
//Uniquement si le joueur se trouve sur une case de ville ou un bourg
$check = false;
if( $W_row['type'] == 1 )
  $check = true;
elseif( $batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $Trace[$joueur->get_race()]['numrace']) )
  $check = $batiment['type'] == 'bourg';
if( $check )
{
	include('ville_bas.php');
	//Si on est dans notre royaume
	if($R->get_diplo($joueur->get_race()) == 127)
	{
		//Si il y a une révolution en cours
		$is_revolution = revolution::is_mois_revolution($R->get_id());
		if($is_revolution)
		{
			$revolution = revolution::get_prochain_revolution($R->get_id());
			$requete = "SELECT id FROM vote_revolution WHERE id_revolution = ".$revolution[0]->get_id()." AND id_perso = ".$joueur->get_id();
			$req = $db->query($requete);
			//Il a déjà voté
			if($db->num_rows($req) > 0)
			{
				echo '<h5>Vous avez déjà voté !</h5>';
			}
			else
			{
				if(array_key_exists('vote', $_GET))
				{
					if($_GET['vote'] == 'pour') $pour = 1;
					else $pour = 0;
					$vote = new vote_revolution();
					$vote->set_id_revolution($revolution[0]->get_id());
					$vote->set_id_perso($joueur->get_id());
					$vote->set_pour($pour);
					$vote->set_poid_vote($joueur->get_level());
					$vote->sauver();
					echo '<h6>Vote bien pris en compte</h6>';
				}
				else
				{
					echo '<a href="revolution.php?vote=pour" onclick="return envoiInfo(this.href, \'carte\');">Voter <strong>POUR</strong> la révolution<a><br />
					<a href="revolution.php?vote=contre" onclick="return envoiInfo(this.href, \'carte\');">Voter <strong>CONTRE</strong> la révolution<a>';
				}
			}
		}
		//Si pas de révolution, on cherche pour en déclencher une
		else
		{
			if(array_key_exists('action', $_GET) && $_GET['action'] == 'declenche')
			{
				$revolution = new revolution();
				$revolution->set_id_royaume($R->get_id());
				$revolution->set_date(date('Y-m-d', mktime(0, 0, 0, date("m") + 1 , 1, date("Y"))));
				$revolution->set_id_perso($joueur->get_id());
				$revolution->sauver();
				echo '<h6>Une révolution est déclenchée !</h6>';
			}
			else echo '<a href="revolution.php?action=declenche" onclick="return envoiInfo(this.href, \'carte\');">Êtes vous sur de vouloir déclencher une révolution ?</a>';
		}
	}
}
?>
</fieldset>