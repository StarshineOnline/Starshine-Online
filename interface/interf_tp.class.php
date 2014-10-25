<?php
/**
 * @file interf_tp.class.php
 * Classes pour le quartier général
 */

/// Classe pour le quartier général
class interf_tp extends interf_ville
{
	function __construct(&$royaume, &$case)
	{
		global $db, $Gtrad;
		parent::__construct($royaume, $case);
		$perso = joueur::get_perso();
		// Icone jauges
		$icone = $this->set_icone_centre('teleportation');
		$icone->set_tooltip('Pierre de téléportation');
		//$this->recherche_batiment('', false);
		/// @todo passer par les objets
		$requete = 'SELECT count(*) as nbr FROM map WHERE royaume = '.$royaume->get_id().' AND x <= 190 AND y <= 190';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		/// @todo à vérifier
		$max = ceil($row['nbr']/250);
		$requete = 'SELECT count(*) as nbr FROM construction WHERE royaume = '.$royaume->get_id().' AND x <= 190 AND y <= 190 AND type ="bourg"';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$this->set_jauge_int($row['nbr'], $max, 'avance', 'Nombre de bourgs : ');
		
		// Centre
		$this->centre->add( new interf_bal_smpl('p', 'Téléportation à partir d\'une diplomatie en paix.') );
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		interf_alerte::aff_enregistres($div);
		// Capitales
		$div->add( new interf_bal_smpl('h4', 'Liste des villes possibles pour téléportation :') );
		$liste = $div->add( new interf_menu(false, 'lieu', null) );
		/// @todo à revoir complètement (supprimer cette base)
		$requete = 'SELECT * FROM teleport';
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			// Bastien : Si coût = 0 (pas NULL), on saute l'entrée
			if ($row['cout'] == '0') continue;
			//Récupération du royaume du téléport
			$requete_roy = 'SELECT * FROM map WHERE x = '.$row['posx'].' and y = '.$row['posy'];
			$req_roy = $db->query($requete_roy);
			$row_roy = $db->read_array($req_roy);
			//Récupération de la race du royaume
			$requete_race = 'SELECT * FROM royaume WHERE ID = '.$row_roy['royaume'];
			$req_race = $db->query($requete_race);
			$row_race = $db->read_array($req_race);
			
			if($row_race['race'] != '')
			{
				//Sélection de la diplomatie
				$requete_diplo = "SELECT ".$row_race['race']." FROM diplomatie WHERE race = '".$royaume->get_race()."'";
				$req_diplo = $db->query($requete_diplo);
				$row_diplo = $db->read_row($req_diplo);
				$distance = $perso->calcule_distance($row['posx'], $row['posy']);
				$cout =  $distance * 10;
				$cout = ceil(($cout * $royaume->get_taxe_diplo($perso->get_race()) / 100) + $cout);
			}
			else
				$row_diplo[0] = 8;
			// Si au moins en paix
			if(($row_diplo[0] <= 3 || $row_diplo[0] == 127) && $distance > 2 && $cout != 0)
				$this->aff_dest($liste, $row_race['capitale'], $cout, $row['posx'], $row['posy'], $Gtrad[$row_race['race']], $row_roy['royaume']);
		}
		// bourgs
		if($royaume->get_diplo($perso->get_race()) == 127)
		{
	    //Séléction de tous les téléport disponibles
	    /// @todo passer par les objets
	    $requete = "SELECT c.id, x, y, c.nom, b.nom as nom_bat FROM construction as c INNER JOIN batiment as b ON c.id_batiment = b.id WHERE b.type = 'bourg' AND royaume = ".$royaume->get_id();
	    $req = $db->query($requete);
			if( $db->num_rows($req) )
			{
				$div->add( new interf_bal_smpl('h4', 'Liste des bourgs possibles pour téléportation :') );
				$liste = $div->add( new interf_menu(false, 'lieu', null) );
		    while($row = $db->read_array($req))
		    {
					// Bastien : Si coût = 0 (pas NULL), on saute l'entrée
	        if ($row['cout'] === '0') continue;
			    $distance = $perso->calcule_distance($row['x'], $row['y']);
			    if ($distance == 0) continue;
			    $cout =  $distance * 7;
			    $cout = ceil(($cout * $royaume->get_taxe_diplo($perso->get_race()) / 100) + $cout);
					$this->aff_dest($liste, $row['nom'], $cout, $row['x'], $row['y'], $row['nom_bat'], $row['id'], 'bourg');
		    }
			}
		}
	}
	function aff_dest(&$liste, $nom, $stars, $x, $y, $descr, $id, $type='ville')
	{
		$elt = $liste->add( new interf_elt_menu('', 'carte.php?x='.$x.'&y='.$y, 'charger(this.ref);') );
		$elt->get_lien()->set_attribut('class', 'icone icone-carte2');
		$elt->get_lien()->set_attribut('onclick', 'return charger(this.href);');
		$tp = $elt->add( new interf_bal_cont('a') );
		$tp->set_attribut('href', 'teleport.php?action=tp&type='.$type.'&id='.$id);
		$tp->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vous téléporter à '.addslashes($nom).' ?\');');
		$tp->add( new interf_bal_smpl('span', 'Téléportation à '.$nom) );
		$texte = ($descr ? $descr. ' - ' : '').'x='.$x.' / y='.$y.' - '.$stars.' Stars et 5 PA';
		$tp->add( new interf_bal_smpl('span', $texte, false, 'xsmall') );
	}
}

?>