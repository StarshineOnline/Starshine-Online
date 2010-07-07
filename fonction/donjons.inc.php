<?php /* */
/**
 * Verifie les conditions pour le TP de donjon
 * @param $W_row ligne de base pour le donjon considere
 * @param $joueur joueur
 */
function verif_tp_donjon($W_row, $joueur)
{
	global $db;
	$unlock = false;
	
	//Myriandre locké si pas fini la quête
	if($W_row['nom'] == 'Myriandre')
	{
		$quete_fini = explode(';', $joueur->get_quete_fini());
		if (in_array('53', $quete_fini)) $unlock = true;
	}
	elseif($W_row['nom'] == 'Teleport')
	{
		$requete_dragon = 'SELECT id FROM map_monstre WHERE type = 125 OR type = 126';
		$req_dragon = $db->query($requete_dragon);
		
		$num_rows = $db->num_rows;
		//Si les 2 dragons sont morts alors on ouvre
		if($num_rows == 0) $unlock = true;
	}
	else $unlock = true;
	
	return $unlock;
}
