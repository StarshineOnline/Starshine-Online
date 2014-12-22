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









	
/*	
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
*/
?>