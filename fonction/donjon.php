<?php //  -*- tab-width:2  -*-
if (file_exists('../root.php')) {
  include_once('../root.php');
}

function kill_monstre_de_donjon($map_monstre)
{
	global $db;
	switch ($map_monstre->get_type())
	{
	case 64: //Si c'est Devorsis on fait pop le fossoyeur
		$requete = "INSERT INTO map_monstre VALUES(NULL, '65','3','212','4800', 6, '".addslashes('Le Fossoyeur')."','fossoyeur', ".(time() + 2678400).")";
		$db->query($requete);
		echo '<strong>Rha, tu me détruis aujourdhui mais le fossoyeur saura saisir ton âme... tu es déja mort !</strong>';
		break;
		
	case 65: //Si c'est le fossoyeur on fait pop finwir
		$requete = "INSERT INTO map_monstre VALUES(NULL, '75','24','209','8000', 7, '".addslashes('Finrwirr le serviteur')."','finrwirr', ".(time() + 2678400).")";
		$db->query($requete);
		echo '<strong>Tu ne fait que retarder l\'inévitable, Le maître saura te faire payer ton insolence !</strong>';
		break;
		
	case 75: //Si c'est Finrwirr on fait pop le gros monstre
		$requete = "INSERT INTO map_monstre VALUES(NULL, '116','24','209','10000', 8, '".addslashes('Adenaïos le nécromant')."','adennaios', ".(time() + 2678400).")";
		$db->query($requete);
		echo '<strong>Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!</strong>';
		break;
		
	case 125:	//Si c'est un draconide
	case 126:
		//Si les 2 sont morts, on fait pop le roi gobelin
		$requete = "SELECT type FROM map_monstre WHERE type IN (125, 126)";
		$req_d = $db->query($requete);
		//Si il n'est pas là on le fait pop
		if($db->num_rows($req_d) == 0)
		{
			$requete = "INSERT INTO map_monstre VALUES(NULL,'123','44','293','5800', 18, 'Roi Goblin','roi_goblin', ".(time() + 2678400).")";
			$db->query($requete);
			echo '<strong>Un bruit de mécanisme eveille votre attention, mais il vous est impossible de savoir d\'où provient ce son.</strong>';
		}
		break;

	default:
		// Rien à faire
	}
}

function kill_monstre_de_donjon2($map_monstre)
{
	global $db;
	$requete = 'select * from monstre_special where type = '.
		$map_monstre->get_type();
	$req_d = $db->query($requete);
	if ($db->num_rows($req_d))
	{
		$row = $db->read_row($req);
		// On va vérifier les prérequis
		$prerequis = true;
		if ($row['condition_sql'] !== null)
		{
			$req_p = $db->query($row['condition_sql']);
			if ($db->num_rows($req_d) == 0)
				$prerequis = false;
		}
		if ($row['non_condition_sql'] !== null)
		{
			$req_p = $db->query($row['non_condition_sql']);
			if ($db->num_rows($req_d) != 0)
				$prerequis = false;
		}
		if ($row['eval_condition'] !== null)
		{
			$prerequis = eval($row['eval_condition']);
		}
		if ($prerequis == true)
		{
			if ($row['texte'] !== null)
				echo "<strong>$row[texte]</strong>\n";
			if ($row['eval_action'] !== null)
			  eval($row['eval_action']);
			if ($row['pop_type'] !== null)
			{
				$mort_naturelle = time() + 2678400;
				$x = $row['pop_x'];
				$y = $row['pop_y'];
				if ($x === null) $x = rand(1, 150);
				if ($y === null) $y = rand(1, 150);
				$requete = "INSERT INTO map_monstre SELECT null, $row[pop_type], ".
					"$x, $y, hp, level, nom, lib, $mort_naturelle ".
					"FROM monstre WHERE id = $row[pop_type]";
				$db->query($requete);
			}
		}
	}
}

?>