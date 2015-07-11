<?php
/**
 * @file interf_bat_drap.class.php
 * Interface de la bourse des royaume 
 */ 

class interf_bat_drap extends interf_onglets
{
	function __construct(&$royaume, $onglet, $x=false, $y=false)
	{
		global $G_url, $db;
		parent::__construct('ongl_bat_drap', 'bat_drap');
		$url = $G_url->copie('ajax', 2);
		
		/// @todo passer à l'objet
		// Liste des drapeaux et Armes en Construction sur votre territoire
		$req = $db->query("SELECT COUNT(*) FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE (placement.type = 'drapeau' OR placement.type = 'arme_de_siege') AND placement.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190");
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('Invasions <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'invasions'), 'ongl_invasions', 'ongl_gest', $onglet=='invasions');
		// Liste de vos bâtiments en construction
		$req = $db->query("SELECT COUNT(*) FROM placement WHERE royaume = ".$royaume->get_id()." AND type != 'drapeau' AND x <= 190 AND y <= 190");
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('Constructions <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'constructions'), 'ongl_constructions', 'ongl_gest', $onglet=='constructions');
		// Liste des Armes de sièges sur votre territoire
		$req = $db->query("SELECT COUNT(*) FROM construction LEFT JOIN map ON (map.y = construction.y AND construction.x = map.x) WHERE construction.type = 'arme_de_siege' AND construction.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190");
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('AdS étrangères <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'ads'), 'ongl_ads', 'ongl_gest', $onglet=='ads');
		// Liste de vos drapeaux sur territoire ennemi
		$req = $db->query("SELECT COUNT(*) FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190 ORDER BY fin_placement ASC");
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('Drapeaux <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'ads_drapeaux'), 'ongl_drapeaux', 'ongl_gest', $onglet=='drapeaux');
		// Liste de vos bâtiments
		$req = $db->query("SELECT COUNT(*) FROM construction WHERE royaume = ".$royaume->get_id()." AND x <= 190 AND y <= 190 ORDER BY type, date_construction ASC");
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('Bâtiments <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'batiments'), 'ongl_batiments', 'ongl_gest', $onglet=='batiments');
		// Liste des objets disponibles dans votre dépôt militaire
		$req = $db->query("SELECT COUNT(*) FROM depot_royaume, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_royaume = ".$royaume->get_id());
		$row = $db->read_array($req);
		if( $row[0] > 0 )
			$this->add_onglet('Dépôt militaire <span class="badge">'.$row[0].'</span>', $url->get('onglet', 'ads_depot'), 'ongl_depot', 'ongl_gest', $onglet=='depot');
		
		$div = $this->get_onglet('ongl_'.$onglet);
		if( !$div )
		{
			$cles = array_keys($this->divs);
			$div = $this->divs[$cles[0]];
		}
		if( $div )
		{
			switch($onglet)
			{
			case 'invasions':
				$div->add( new interf_bd_invasions($royaume, $x, $y) );
				break;
			case 'constructions':
				$div->add( new interf_bd_constructions($royaume, $x, $y) );
				break;
			case 'ads':
				$div->add( new interf_bd_ads($royaume, $x, $y) );
				break;
			case 'drapeaux':
				$div->add( new interf_bd_drapeaux($royaume, $x, $y) );
				break;
			case 'batiments':
				$div->add( new interf_bd_batiments($royaume, $x, $y) );
				break;
			case 'depot':
				$div->add( new interf_bd_depot($royaume, $x, $y) );
				break;
			}
		}
	}
}

abstract class interf_bd_liste extends interf_cont
{
	protected $tbl;
	const nbr_col = 3;
	function __construct(&$royaume, $x=false, $y=false)
	{
  	global $db, $G_url;
  	$G_url->add('onglet', static::id);
  	
  	$droite = $this->add( new interf_bal_cont('div', false, 'bat_drap_droite') );
  	$droite->add( new interf_bal_smpl('h4', 'Distances de pose') );
  	$droite->add( new interf_bal_smpl('strong', 'Bourgs : ') );
  	$liste1 = $droite->add( new interf_bal_cont('ul') );
  	$liste1->add( new interf_bal_smpl('li', 'Avec un autre bourg : 7') );
  	$liste1->add( new interf_bal_smpl('li', 'Avec une capitale : 5') );
  	$droite->add( new interf_bal_smpl('strong', 'Forts : ') );
  	$liste2 = $droite->add( new interf_bal_cont('ul') );
  	$liste2->add( new interf_bal_smpl('li', 'Avec un autre fort : 4') );
  	$liste2->add( new interf_bal_smpl('li', 'Avec une capitale : 7') );
  	$carte = $this->add( new interf_bal_cont('div', 'minicarte_'.static::id, 'bat_drap_droite minicarte') );
  	if( $x && $y )
  		$carte->add( new interf_carte($x, $y, interf_carte::aff_gestion, 5, 'carte_'.static::id) );
		  	
		// tableau
		$gauche = $this->add( new interf_bal_cont('div', false, 'bat_drap_gauche') );
  	$gauche->add( new interf_bal_smpl('h4', static::titre) );
		$this->tbl = $gauche->add( new interf_data_tbl('tbl_'.static::id, '', false, false) );
		$this->tbl->nouv_ligne(false, false, false, false, interf_tableau::entete);
		$this->tbl->nouv_cell('&nbsp;');
		$this->tbl->nouv_cell('Nom');
		$this->aff_entete();
		
		$req = $db->query( $this->get_requete($royaume) );
		while($row = $db->read_assoc($req))
		{
			$batiment = new batiment($row['id_batiment']);
			$this->tbl->nouv_ligne();
			$img = $row['type'] == 'drapeau' ? '../image/drapeaux/drapeau_'.$royaume->get_id().'.png' : '../image/batiment/'.$batiment->get_image().'_04.png';
			$this->tbl->nouv_cell( new interf_img($img) );
			$this->aff_element($row, $batiment);
		}
	}
	protected function aff_entete()
	{
		$this->tbl->nouv_cell('Royaume');
		$this->tbl->nouv_cell('X');
		$this->tbl->nouv_cell('Y');
	}
	protected function aff_element(&$elt, &$batiment)
	{
		global $Gtrad, $G_url;
		$royaume_req = new royaume($elt['r']);
		$this->tbl->nouv_cell( new interf_lien($elt['nom'],$G_url->get(array('x'=>$elt['x'], 'y'=>$elt['y']))) );
		$this->tbl->nouv_cell($Gtrad[$royaume_req->get_race()]);
		$this->tbl->nouv_cell($elt['x']);
		$this->tbl->nouv_cell($elt['y']);
	}
	abstract protected function get_requete(&$royaume);
}

class interf_bd_invasions extends interf_bd_liste
{
	const id = 'invasions';
	const titre = 'Liste des drapeaux et armes de siège en construction sur votre territoire';
	protected function get_requete(&$royaume)
	{
		return "SELECT *, placement.royaume AS r, placement.type FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE (placement.type = 'drapeau' OR placement.type = 'arme_de_siege') AND placement.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190";
	}
	protected function aff_entete()
	{
		parent::aff_entete();
		$this->tbl->nouv_cell('Temps restant');
	}
	protected function aff_element(&$elt, &$batiment)
	{
		parent::aff_element($elt, $batiment);
		$this->tbl->nouv_cell( transform_sec_temp($row['fin_placement'] - time()) );
	}
}

class interf_bd_constructions extends interf_bd_liste
{
	const id = 'constructions';
	const titre = 'Liste de vos bâtiments en construction';
	protected function get_requete(&$royaume)
	{
		return "SELECT *, placement.royaume AS r, placement.type FROM placement WHERE royaume = ".$royaume->get_id()." AND type != 'drapeau' AND x <= 190 AND y <= 190";
	}
	protected function aff_entete()
	{
		parent::aff_entete();
		$this->tbl->nouv_cell('Temps restant');
		$this->tbl->nouv_cell('HP');
	}
	protected function aff_element(&$elt, &$batiment)
	{
		parent::aff_element($elt, $batiment);
		$this->tbl->nouv_cell( transform_sec_temp($elt['fin_placement'] - time()) );
		$this->tbl->nouv_cell($elt['hp'].' / '.$batiment->get_hp());
	}
}

class interf_bd_ads extends interf_bd_liste
{
	const id = 'ads';
	const titre = 'Liste des Armes de sièges sur votre territoire';
	protected function get_requete(&$royaume)
	{
		return "SELECT *, construction.royaume AS r, construction.type FROM construction LEFT JOIN map ON (map.y = construction.y AND construction.x = map.x) WHERE construction.type = 'arme_de_siege' AND construction.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190";
	}
}

class interf_bd_drapeaux extends interf_bd_liste
{
	const id = 'drapeaux';
	const titre = 'Liste de vos drapeaux sur territoire ennemi';
	protected function get_requete(&$royaume)
	{
		return "SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190 ORDER BY fin_placement ASC";
	}
}

class interf_bd_batiments extends interf_bd_liste
{
	const id = 'batiments';
	const titre = 'Liste de vos bâtiments';
	protected function get_requete(&$royaume)
	{
		return "SELECT *, id FROM construction WHERE royaume = ".$royaume->get_id()." AND x <= 190 AND y <= 190 ORDER BY type, date_construction ASC";
	}
	protected function aff_entete()
	{
		$this->tbl->nouv_cell('Type');
		$this->tbl->nouv_cell('X');
		$this->tbl->nouv_cell('Y');
		$this->tbl->nouv_cell('HP');
		$this->tbl->nouv_cell('Actions');
	}
	protected function aff_element(&$elt, &$batiment)
	{
		global $G_url;
		$this->tbl->nouv_cell( new interf_lien($elt['nom'],$G_url->get(array('x'=>$elt['x'], 'y'=>$elt['y']))) );
		$this->tbl->nouv_cell($batiment->get_nom());
		$this->tbl->nouv_cell($elt['x']);
		$this->tbl->nouv_cell($elt['y']);
		$this->tbl->nouv_cell( new interf_jauge_bulle('HP', $elt['hp'], $batiment->get_hp(), false, 'hp', false, 'jauge_case') );
		$actions = $this->tbl->nouv_cell(null);
		if( $batiment->get_type() != 'arme_de_siege' )
		{
			$renom = $actions->add( new interf_lien('', $G_url->get( array('id'=>$elt['id'], 'action'=>'renommer') ), false, 'icone icone-modifier') );
			$renom->set_tooltip('Renommer');
		}
		$buffs = buff_batiment::create(array('id_construction', 'type'),array($elt['id'], 'assiege'));
		if( count($buffs) == 0 )
		{
			$suppr = $actions->add( new interf_lien('', $G_url->get( array('id'=>$elt['id'], 'action'=>'suppr') ), false, 'icone icone-poubelle') );
			$suppr->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir supprimer ce bâtiment ?\');');
			$suppr->set_tooltip('Supprimer');
		}
	}
}

class interf_bd_depot extends interf_bd_liste
{
	const id = 'depot';
	const titre = 'Liste des objets disponibles dans votre dépôt militaire';
	protected function get_requete(&$royaume)
	{
		return "SELECT objet_royaume.*, COUNT(depot_royaume.id_objet) AS nbr_objet, depot_royaume.id_objet, depot_royaume.id AS id_depot FROM depot_royaume, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_royaume = ".$royaume->get_id()." GROUP BY depot_royaume.id_objet ASC";
	}
	protected function aff_entete()
	{
		$this->tbl->nouv_cell('Nombre');
	}
	protected function aff_element(&$elt, &$batiment)
	{
		$this->tbl->nouv_cell($row['nom']);
		$this->tbl->nouv_cell($elt['nbr_objet']);
	}
}

class interf_batiment_nom extends interf_dialogBS
{
	function __construct(&$construction)
	{
		global $G_url;
		parent::__construct('Renommer bâtiment');
		$form = $this->add( new interf_form($G_url->get('action', 'modif_nom'), 'nom_batiment') );
		$nbr = $form->add_champ_bs('text', 'nom', null, $construction->get_nom(), 'Nom');
		$nbr = $form->add_champ_bs('hidden', 'id', null, $construction->get_id());
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Modifier', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'nom_batiment\');', 'primary');
	}
}

?>