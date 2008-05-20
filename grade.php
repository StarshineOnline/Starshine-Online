<?php
$races = array_keys($Trace);
$count_race = count($races);
$iii = 0;
$week = 60 * 60 * 24 * 7;
$time_limit = time() - $week;
//On met tout le monde au rang de citoyen avant de faire le script
$requete = "UPDATE perso SET rang_royaume = 7 WHERE rang_royaume <> 6";
$db->query($requete);
while($iii < $count_race)
{
	$tab_perso = array();
	$requete = "SELECT * FROM perso WHERE race = '".$races[$iii]."' AND dernier_connexion >= ".$time_limit." AND rang_royaume <> 6 AND statut = 'actif' ORDER BY honneur DESC";
	//echo $requete.'<br />';
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$tab_perso[] = $row;
	}
	$i = 0;
	$count = count($tab_perso);
	$check = true;
	
	$requete = "SELECT * FROM grade WHERE facteur <> 0 ORDER BY facteur";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req) AND $check)
	{
		$tot_grade = 0;
		$nb_grade = pow(2, $row['facteur']);
		$check2 = true;
		while($check2)
		{
			if($tab_perso[$i]['honneur'] >= $row['honneur'])
			{
				echo $row['nom'].' des '.$races[$iii].' - '.$tab_perso[$i]['nom'].'<br />';
				//mis à jour du perso
				$requete = "UPDATE perso SET rang_royaume = ".$row['id']." WHERE ID = ".$tab_perso[$i]['ID']." AND rang_royaume != ".$row['id'];
				$req_up = $db->query($requete);
				//Si ya changement
				if($db->rows_affected > 0)
				{
					//Si le grade est chef ou plus et qu'il n'est pas criminel on donne accès au forum des officiers
					if($row['rang'] > 2 AND $tab_perso[$i]['amende'] == 0)
					{
						$requete = "UPDATE punbbusers SET group_id = ".$groupe[$tab_perso[$i]['race']][2]." WHERE username = '".$tab_perso[$i]['nom']."'";
					}
					//Sinon on supprime l'accès
					else
					{
						$requete = "UPDATE punbbusers SET group_id = ".$groupe[$tab_perso[$i]['race']][0]." WHERE username = '".$tab_perso[$i]['nom']."'";
					}
					$db->query($requete);
				}
				$tot_grade++;
				if($tot_grade == $nb_grade) $check2 = false;
				$i++;
				if($i == $count) $check = false;
			}
			else
			{
				//echo $tab_perso[$i]['nom'].'bip<br />';
				$check2 = false;
			}
		}
	}
	$iii++;
}
?>