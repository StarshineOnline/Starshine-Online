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

$W_requete = 'SELECT royaume, type FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
		<fieldset><legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> > ', $joueur->get_pos()); ?> <?php echo '<a href="candidature.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Candidature </a></legend>
<?php
$check = false;
if( $W_row['type'] == 1 )
{
  $check = true;
  include_once('ville_bas.php');
}
elseif( $batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $Trace[$joueur->get_race()]['numrace']) )
  $check = $batiment['type'] == 'bourg';
$is_election = elections::is_mois_election($R->get_id());
if( $check && $is_election )
{
	if(array_key_exists('type', $_GET))
	{
		$election = elections::get_prochain_election($Trace[$joueur->get_race()]['numrace'], true);
		$candidats = candidat::create(array('id_perso', 'id_election'), array($joueur->get_id(), $election[0]->get_id()));
		//On vérifie qu'il n'est pas déjà candidat
		if(count($candidats) == 0)
		{
			$candidat = new candidat();
			$candidat->set_id_perso($joueur->get_id());
			$candidat->set_nom($joueur->get_nom());
			$candidat->set_royaume($Trace[$joueur->get_race()]['numrace']);
			$candidat->set_id_election($election[0]->get_id());
			$candidat->set_duree(sSQL($_GET['duree']));
			$candidat->set_type(sSQL($_GET['type']));
			$candidat->set_programme(sSQL($_GET['programme']));
			
			$save = true;
			if($_GET['ministre_economie'] != '')
			{
				$economie = perso::create(array('nom', 'race'), array(sSQL($_GET['ministre_economie']), $joueur->get_race()));
				if(count($economie) == 1) $candidat->set_id_ministre_economie($economie[0]->get_id());
				else
				{
          log_admin::log('debug', 'Ministre de l\'économie introuvable ('.count($economie).') : '.$_GET['ministre_economie'].' ('.$joueur->get_race().') -> '.mysql_real_escape_string($_GET['ministre_economie']), true);
					echo '<h5>Ministre de l\'économie introuvable !</h5>';
					$save = false;
				}
			}
			if($_GET['ministre_militaire'] != '')
			{
				$militaire = perso::create(array('nom', 'race'), array(sSQL($_GET['ministre_militaire']), $joueur->get_race()));
				if(count($militaire) == 1) $candidat->set_id_ministre_militaire($militaire[0]->get_id());
				else
				{
          log_admin::log('debug', 'Ministre militaire introuvable ('.count($militaire).') : '.$_GET['ministre_militaire'].' ('.$joueur->get_race().') -> '.mysql_real_escape_string($_GET['ministre_militaire']), true);
					echo '<h5>Ministre militaire introuvable !</h5>';
					$save = false;
				}
			}
			if($save)
			{
				echo '<h6>Candidature acceptée</h6>';
				$candidat->sauver();
			}
		}
		else
		{
			echo '<h5>Vous êtes déjà candidat !</h5>';
		}
	}
	else
	{
	?>
	<h2>Candidature</h2>
	<form method="post" action="candidature.php" id="formCandidature">
		Programme électoral :<br />
		<textarea style="width : 300px; height : 200px;" name="programme" id="programme"></textarea><br />
		<br />
		Ministre militaire : <input type="text" name="ministre_militaire" id="ministre_militaire" onkeyup="javascript:suggestion(this.value, 'suggestion_mil', this.id);"/>
	<div id="suggestion_mil"></div>
	<br />
		Ministre économie : <input type="text" name="ministre_economie" id="ministre_economie" onkeyup="javascript:suggestion(this.value, 'suggestion_eco', this.id);"/>
	<div id="suggestion_eco"></div>
	<br />
		Type de la prochaine élection
		<select name="type" id="type">
			<option value="universel">Universelle</option>
			<option value="nomination">Nomination</option>
		</select><br />
		Durée de votre mandat : 
		<select name="duree" id="duree">
			<option value="1">1 mois</option>
			<option value="2">2 mois</option>
			<option value="3">3 mois</option>
			<option value="6">6 mois</option>
			<option value="12">1 an</option>
		</select>
		<input type="button" onclick="envoiInfo('candidature.php?programme='+encodeURIComponent($('#programme').val())+'&ministre_militaire='+encodeURIComponent($('#ministre_militaire').val())+'&ministre_economie='+encodeURIComponent($('#ministre_economie').val())+'&type='+encodeURIComponent($('#type').val())+'&duree='+encodeURIComponent($('#duree').val()), 'carte');" value="Me présenter à la prochaine élection" />
	</form>
	<?php
	}	
}
?>
