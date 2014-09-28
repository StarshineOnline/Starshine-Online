<?php
/**
 * @file interf_vente_terrain.class.php
 * Classes pour le quartier général
 */
 

/// Classe pour le quartier général
class interf_vente_terrain extends interf_ville
{
	function __construct(&$royaume)
	{
		global $db;
		parent::__construct($royaume);
		$perso = joueur::get_perso();
		$nbr_cases = $royaume->get_nbr_cases();
		// Icone
		$icone = $this->set_icone_centre('encheres2');
		$icone->set_tooltip('Vente de terrain');
		if( $nbr_cases >= 500 )
		{
			$this->centre->add( new interf_bal_smpl('p', 'Liste des terrains à vendre') );
			$nbr_alertes = interf_alerte::aff_enregistres($this->centre);
			$tbl = $this->centre->add( new interf_data_tbl('vente_terrain', '', false, false, 383 - $nbr_alertes * 30, 0) );
			$tbl->nouv_cell('Fin');
			$tbl->nouv_cell('Prix');
			$tbl->nouv_cell('Enchérir');
			/// @todo passer à l'objet
			$requete = "SELECT id, id_royaume, date_fin, id_joueur, prix FROM vente_terrain WHERE id_royaume = ".$royaume->get_id()." AND date_fin > ".time();
			$req = $db->query($requete);
			$tps_max = 60 * 60 * 24 * 7;
			$min = $tps_max;
			$max = 0;
			while( $row = $db->read_assoc($req) )
			{
				$vente_terrain = new vente_terrain($row);
				$ok = $perso->get_star() >= $row['prix'];
				$tbl->nouv_ligne(false, $ok ? '' : 'non-achetable');
				$tps = $row['date_fin'] - time();
				$min = min($min, $tps);
				$max = max($max, $tps);
				$tbl->nouv_cell( date("d-m-Y", $row['date_fin']) );
				$tbl->nouv_cell($row['prix'], false, $ok ? false : 'text-danger');
				if( $ok && $row['id_joueur'] != $perso->get_id() )
					$tbl->nouv_cell( new interf_lien('Enchérir ('.$vente_terrain->prochain_prix().' stars)', 'vente_terrain.php?action=encherir&id='.$row['id']) );
				else
					$tbl->nouv_cell( '&nbsp;' );
			}
			// jauges
			$this->set_jauge_ext($max, $tps_max, 'temps'/*, 'Nombre de cases nécessaires : '*/);
			$this->set_jauge_int($min, $tps_max, 'temps'/*, 'Temps avant possible mise en vente : '*/);
		}
		else
		{
			// jauges
			$this->set_jauge_ext($nbr_cases, 500, 'avance', 'Nombre de cases nécessaires : ');
			$this->set_jauge_int(date('j'), date('t'), 'temps'/*, 'Temps avant possible mise en vente : '*/);
			$this->centre->add( new interf_bal_smpl('p', 'Pas encore de terrains à vendre') );
		}
	}
}

?>