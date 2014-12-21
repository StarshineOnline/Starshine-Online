<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
if( !$perso->get_rang() == 6 )
{
	$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
	if( $royaume->get_ministre_militaire() != $perso->get_id() && $royaume->get_ministre_economie() != $perso->get_id() )
	{
		/// @todo logguer triche
		exit;
	}
}

$G_interf->creer_royaume();






exit;












global $joueur;
$joueur = new perso($_SESSION['ID']);
$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);

if(($joueur->get_race() == $royaume->get_race() && $joueur->get_grade()->get_id() == 6) OR $joueur->get_id() == $royaume->get_ministre_economie() OR $joueur->get_id() == $royaume->get_ministre_militaire())
{
	$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));
	$requete = "SELECT food, nombre_joueur FROM stat_jeu ORDER BY date DESC";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($row['nombre_joueur'] != 0) $food_necessaire = $row['food'] / $row['nombre_joueur'];
	else $food_necessaire = 0;
	
	
	//Vérifie si le perso est mort
	verif_mort($joueur, 1);
	$joueur->check_perso();
	
	$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
	$check = false;
	if(verif_ville($joueur->get_x(), $joueur->get_y(), $royaume->get_id()))
	{
		$check = true;
	}
	elseif($batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $royaume->get_id()))
	{
		if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
		{
			$bourg = new batiment($batiment['id_batiment']);
			if($bourg->has_bonus('royaume')) $check = true;
		}
	}
	
	if($check)
	{
	echo "<script type='text/javascript'>
			// <![CDATA[\n";
	{ // Validation d'une bataille
	echo "		
	function validation_bataille()
	{
		data = 'nom=' + $('#nom').val() + '&description=' + $('#description').val() + '&x=' + $('#x').val() + '&y=' + $('#y').val() + '&new2'; ";
		$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			echo "
			if ($('#groupe_".$row['groupeid']."').val() == 1)
			{
				data = data+'&groupe_".$row['groupeid']."=1'
			}
			
			";
		}
		 
		echo "envoiInfo('gestion_bataille_new.php?'+data, 'message_confirm');
  }
	";
	}
	echo "	// ]]>
		  </script>";
?>
