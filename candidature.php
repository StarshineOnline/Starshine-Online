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
		<h2 class="ville_titre"><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href,\'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="vie_royaume.php" onclick="return envoiInfo(this.href,\'carte\')">';?> Vie du royaume </a></h2>
<?php
if($W_row['type'] == 1)
{
	if(array_key_exists('type', $_POST))
	{
		$election = elections::get_prochain_election($Trace[$joueur->get_race()]['numrace']);
		$candidats = candidat::create(array('id_perso', 'id_election'), array($joueur->get_id(), $election[0]->get_id()));
		//On vérifie qu'il n'est pas déjà candidat
		if(count($candidats) == 0)
		{
			$candidat = new candidat();
			$candidat->set_id_perso($joueur->get_id());
			$candidat->set_nom($joueur->get_nom());
			$candidat->set_royaume($Trace[$joueur->get_race()]['numrace']);
			$candidat->set_id_election($election[0]->get_id());
			$candidat->set_duree($_POST['duree']);
			$candidat->set_type($_POST['type']);
			$candidat->set_programme($_POST['programme']);
			$save = true;
			if($_POST['ministre_economie'] != '')
			{
				$economie = perso::create(array('nom', 'race'), array($_POST['ministre_economie'], $joueur->get_race()));
				if(count($economie) == 1) $candidat->set_id_ministre_economie($economie[0]->get_id());
				else
				{
					echo '<h5>Ministre de l\'économie introuvable !</h5>';
					$save = false;
				}
			}
			if($_POST['ministre_militaire'] != '')
			{
				$militaire = perso::create(array('nom', 'race'), array($_POST['ministre_militaire'], $joueur->get_race()));
				if(count($militaire) == 1) $candidat->set_id_ministre_militaire($militaire[0]->get_id());
				else
				{
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
	<form action="candidature.php" id="formCandidature" onsubmit="new Ajax.Updater('carte','candidature.php',{asynchronous:true,parameters:$('formCandidature').serialize(this)}); return false;">
		Programme électoral :<br />
		<textarea style="width : 300px; height : 200px;" name="programme"></textarea><br />
		<br />
		Ministre militaire : <input type="text" name="ministre_militaire" id="ministre_militaire" /><br />
		Ministre économie : <input type="text" name="ministre_economie" id="ministre_economie" /><br />
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
		<input type="submit" value="Me présenter à la prochaine élection" />
	</form>
	<?php
	}	
}
?>
