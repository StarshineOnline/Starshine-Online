<?php
/**
 * @file interf_entretien.class.php
 * Interface de 
 */ 

class interf_entretien extends interf_onglets
{
	function __construct(&$royaume, $onglet='balance')
	{
		global $G_url;
		parent::__construct('ongl_entretien', 'entretien');
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Balance (hier)', $url->get('onglet', 'balance'), 'ongl_balance', 'ongl_gest', $onglet=='balance');
		$this->add_onglet('Recettes mensuelles', $url->get('onglet', 'recettes'), 'ongl_recettes', 'ongl_gest', $onglet=='recettes');
		$this->add_onglet('Évolution', $url->get('onglet', 'evolution'), 'ongl_evolution', 'ongl_gest', $onglet=='evolution');
		$this->add_onglet('Répartition', $url->get('onglet', 'repartition'), 'ongl_repartition', 'ongl_gest', $onglet=='repartition');
		
		$div = $this->get_onglet('ongl_'.$onglet);
		switch($onglet)
		{
		case 'balance':
			$div->add( new interf_balance_hier($royaume) );
			break;
		case 'recettes':
			$div->add( new interf_recettes($royaume) );
			break;
		case 'evolution':
			$div->add( new interf_evol_gains($royaume) );
			break;
		case 'repartition':
			$div->add( new interf_repart_gains($royaume) );
			break;
		}
	}
}


class interf_balance_hier extends interf_data_tbl
{
	protected $royaume;
	protected $cell_recettes = array();
	function __construct(&$royaume)
	{
		$this->royaume = &$royaume;
		parent::__construct('tbl_entretien', '', false, false);
		$this->nouv_cell('Nom');
		$this->nouv_cell('Type');
		$this->nouv_cell('X');
		$this->nouv_cell('Y');
		$this->nouv_cell('Base dépense');
		$this->nouv_cell('Dépenses', false, 'danger');
		$this->nouv_cell('Recettes', false, 'success');
		
		$this->aff_bat_internes();
		$this->aff_bat_externes();
		$this->aff_taxes();
		$this->aff_balance();
	}
	protected function aff_bat_internes()
	{
		global $db;
		$ratio = $this->royaume->get_facteur_entretien();
		/// @todo passer à l'objet.
		$requete = "SELECT *, construction_ville.id as id_const FROM construction_ville RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE construction_ville.statut = 'actif' AND id_royaume = ".$this->royaume->get_id()." ORDER BY entretien DESC";
		$req = $db->query($requete);
		$this->totaux['bat_int'] = 0;
		while($row = $db->read_assoc($req))
		{
			$entretien = ceil($row['entretien'] * $ratio);
			$this->nouv_ligne();
			$this->nouv_cell($row['nom']);
			$this->nouv_cell('bâtiment interne');
			$this->nouv_cell('&nbsp;');
			$this->nouv_cell('&nbsp;');
			$this->nouv_cell($row['entretien']);
			$this->nouv_cell($entretien, false, 'danger');
			$this->cell_recettes[ $row['type'] ] = $this->nouv_cell(null, false, 'success');
			//$royaumes[$row['id_royaume']]['batiments'][$row['id_const']] = $entretien;
			$this->totaux['bat_int'] += $entretien;
		}
	}
	protected function aff_bat_externes()
	{
		global $db;
		$ratio = $this->royaume->get_facteur_entretien();
		/// @todo passer à l'objet.
		$requete = "SELECT *, construction.id AS id_const, batiment.nom AS nom_b, construction.x AS x_c, construction.y AS y_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id WHERE royaume = ".$this->royaume->get_id()." AND x <= 190 AND y <= 190 ORDER BY entretien DESC";
		$req = $db->query($requete);
		$this->totaux['bat_ext'] = 0;
		while($row = $db->read_assoc($req))
		{
			$entretien = ceil($row['entretien'] * $ratio);
			$this->nouv_ligne();
			$this->nouv_cell($row['nom']);
			$this->nouv_cell($row['nom_b']);
			$this->nouv_cell($row['x_c']);
			$this->nouv_cell($row['y_c']);
			$this->nouv_cell($row['entretien']);
			$this->nouv_cell($entretien, false, 'danger');
			/// @todo mettre gain extracteurs routes ici
			$this->nouv_cell('&nbsp;', false, 'success');
			$this->totaux['bat_ext'] += $entretien;
		}
	}
	protected function aff_taxes()
	{
		global $db;
		$sources = array();
		$sources[2] = 'Hotel des ventes';
		$sources[3] = 'Taverne';
		$sources[4] = 'forgeron';//'Forgeron';
		$sources[5] = 'armurerie';//'Armurerie';
		$sources[6] = 'Alchimiste';
		$sources[7] = 'Enchanteur';
		$sources[8] = 'ecole_magie';//'Ecole de Magie';
		$sources[9] = 'ecole_combat';//'Ecole de Combat';
		$sources[10] = 'Teleportation';
		$sources[11] = 'Monstres';
		$sources[24] = 'Mines';
		$time = mktime(0, 0, 0, date("m") , date("d") - (date("G") > 4 ? 1 : 2), date("Y"));
		/// @todo passer à l'objet.
		$requete = "SELECT ".$this->royaume->get_race()." FROM stat_jeu WHERE date = '".date("Y-m-d", $time)."'";
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$stats = explode(';', $row[$this->royaume->get_race()]);
		$i = 0;
		$count = count($stats);
		$this->totaux['taxes'] = 0;
		while($i < $count)
		{
			if(array_key_exists($i, $sources))
			{
				if( array_key_exists($sources[$i], $this->cell_recettes) )
					$this->cell_recettes[ $sources[$i] ]->add( new interf_txt($stats[$i]) );
				else
				{
					$this->nouv_ligne();
					$this->nouv_cell($sources[$i]);
					$this->nouv_cell('taxes');
					$this->nouv_cell('&nbsp;');
					$this->nouv_cell('&nbsp;');
					$this->nouv_cell('&nbsp;');
					$this->nouv_cell('&nbsp;', false, 'danger');
					$this->nouv_cell($stats[$i], false, 'success');
				}
				$this->totaux['taxes'] += $stats[$i];
			}
			$i++;
		}
	}
	protected function aff_balance()
	{
		// total dépenses
		$this->nouv_total('Bâtiments internes', $this->totaux['bat_int']);
		$this->nouv_total('Bâtiments externes', $this->totaux['bat_ext']);
		$this->nouv_total('Total dépenses', $this->totaux['bat_int'] + $this->totaux['bat_ext']);
		$this->nouv_total('Total recettes', '&nbsp', $this->totaux['taxes']);
		$balance = $this->totaux['taxes'] - ($this->totaux['bat_int'] + $this->totaux['bat_ext']);
		if( $balance > 0 )
			$this->nouv_total('Balance', '&nbsp', $balance);
		else
			$this->nouv_total('Balance', -$balance, '&nbsp');
	}
	protected function nouv_total($nom, $depense, $recette='&nbsp;')
	{
		$this->nouv_ligne(false, false, interf_tableau::pied);
		$this->nouv_cell($nom);
		$this->nouv_cell('total');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell($depense, false, 'danger');
		$this->nouv_cell($recette, false, 'success');
	}
}

/// @todo supprimer les doublons de code
class interf_recettes extends interf_data_tbl
{
	protected $cell_recettes = array();
	function __construct(&$royaume)
	{
		global $db;
		$sources = array();
		$sources[2] = 'Hotel des ventes';
		$sources[3] = 'Taverne';
		$sources[4] = 'Forgeron';
		$sources[5] = 'Armurerie';
		$sources[6] = 'Alchimiste';
		$sources[7] = 'Enchanteur';
		$sources[8] = 'Ecole de Magie';
		$sources[9] = 'Ecole de Combat';
		$sources[10] = 'Teleportation';
		$sources[11] = 'Monstres';
		$sources[24] = 'Mines';
		
		parent::__construct('tbl_recettes', '', false, false);
		$this->nouv_cell('Nom');
		$this->nouv_cell('Gains');
		$this->nouv_cell('Ratio');
		
		$time = mktime(0, 0, 0, date("m") , date("d") - (date("G") > 4 ? 1 : 2), date("Y"));
		$requete = "SELECT ".$royaume->get_race().", date, UNIX_TIMESTAMP(date) as stamp FROM stat_jeu WHERE EXTRACT(YEAR_MONTH FROM date) = '".date("Ym", time())."'";
		$req = $db->query($requete);
		$total_source = array();
		$total_total = 0;
		$jours = 0;
		$data = array();
		while($row = $db->read_array($req))
		{
			$stats = explode(';', $row[$royaume->get_race()]);
			$i = 0;
			$total = 0;
			$count = count($stats);
			while($i < $count)
			{
				if(array_key_exists($i, $sources))
				{
					$data[$sources[$i]][$row['stamp']] = $stats[$i];
					$total += $stats[$i];
					$total_total += $stats[$i];
					$total_source[$i] += $stats[$i];
				}
				$i++;
			}
			$jours++;
		}
		
		$datas = array();
		foreach($total_source as $key => $value)
		{
			$datas[] = '["'.$sources[$key].'",'.$value.']';
			$pourcent = round(($value / $total_total), 4) * 100;
			$this->nouv_ligne();
			$this->nouv_cell($sources[$key]);
			$this->nouv_cell($value);
			$this->nouv_cell($pourcent);
		}
		$jours = $jours > 0 ? $jours : 1;
		
		$this->nouv_ligne(false, false, interf_tableau::pied);
		$this->nouv_cell('Total');
		$this->nouv_cell($total_total);
		$this->nouv_cell(round(($total_total / $jours), 2).' / jour');
	}
}


class interf_evol_gains extends interf_bal_cont
{
	function __construct($royaume)
	{
		global $db;
		parent::__construct('div', 'evol_recettes');
		
		$sources = array();
		$sources[2] = 'Hotel des ventes';
		$sources[3] = 'Taverne';
		$sources[4] = 'Forgeron';
		$sources[5] = 'Armurerie';
		$sources[6] = 'Alchimiste';
		$sources[7] = 'Enchanteur';
		$sources[8] = 'Ecole de Magie';
		$sources[9] = 'Ecole de Combat';
		$sources[10] = 'Teleportation';
		$sources[11] = 'Monstres';
		$sources[24] = 'Mines';
		
		$time = mktime(0, 0, 0, date("m") , date("d") - (date("G") > 4 ? 1 : 2), date("Y"));
		$requete = "SELECT ".$royaume->get_race().", date, UNIX_TIMESTAMP(date) as stamp FROM stat_jeu WHERE EXTRACT(YEAR_MONTH FROM date) = '".date("Ym", time())."'";
		$req = $db->query($requete);
		$total_source = array();
		$total_total = 0;
		$jours = 0;
		$data = array();
		while($row = $db->read_array($req))
		{
			$stats = explode(';', $row[$royaume->get_race()]);
			$i = 0;
			$total = 0;
			$count = count($stats);
			while($i < $count)
			{
				if(array_key_exists($i, $sources))
				{
					$data[$sources[$i]][$row['stamp']] = $stats[$i];
					$total += $stats[$i];
					$total_total += $stats[$i];
					$total_source[$i] += $stats[$i];
				}
				$i++;
			}
			$jours++;
		}
		
		$d = array();
		foreach($data as $ressource => $da)
		{
			$d = array();
			foreach($da as $date => $m)
			{
				$d[] = '['.$date.'*1000,'.$m.']';
			}
			$datas[] = '{data:['.implode(', ', $d).'], name: "'.$ressource.'"}';
		}
		
		self::code_js('evol_recettes(['.implode(', ', $datas).']);');
	}
}

class interf_repart_gains extends interf_bal_cont
{
	function __construct($royaume)
	{
		global $db;
		parent::__construct('div', 'repart_recettes');
		
		$sources = array();
		$sources[2] = 'Hotel des ventes';
		$sources[3] = 'Taverne';
		$sources[4] = 'Forgeron';
		$sources[5] = 'Armurerie';
		$sources[6] = 'Alchimiste';
		$sources[7] = 'Enchanteur';
		$sources[8] = 'Ecole de Magie';
		$sources[9] = 'Ecole de Combat';
		$sources[10] = 'Teleportation';
		$sources[11] = 'Monstres';
		$sources[24] = 'Mines';
		
		$time = mktime(0, 0, 0, date("m") , date("d") - (date("G") > 4 ? 1 : 2), date("Y"));
		$requete = "SELECT ".$royaume->get_race().", date, UNIX_TIMESTAMP(date) as stamp FROM stat_jeu WHERE EXTRACT(YEAR_MONTH FROM date) = '".date("Ym", time())."'";
		$req = $db->query($requete);
		$total_source = array();
		$total_total = 0;
		$jours = 0;
		$data = array();
		while($row = $db->read_array($req))
		{
			$stats = explode(';', $row[$royaume->get_race()]);
			$i = 0;
			$total = 0;
			$count = count($stats);
			while($i < $count)
			{
				if(array_key_exists($i, $sources))
				{
					$data[$sources[$i]][$row['stamp']] = $stats[$i];
					$total += $stats[$i];
					$total_total += $stats[$i];
					$total_source[$i] += $stats[$i];
				}
				$i++;
			}
			$jours++;
		}
		
		$d = array();
		foreach($data as $ressource => $da)
		{
			$d = array();
			foreach($da as $date => $m)
			{
				$d[] = '['.$date.'*1000,'.$m.']';
			}
			$datas[] = '{data:['.implode(', ', $d).'], name: "'.$ressource.'"}';
		}
		
		self::code_js('repart_recettes(['.implode(', ', $datas).']);');
	}
}

?>