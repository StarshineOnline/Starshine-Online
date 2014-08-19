<?php
/**
 * @file interf_hotel_vente.class.php
 * Classes pour l'interface de l'hotel des ventes
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'école de magie
class interf_hotel_vente extends interf_ville_onglets
{
	function __construct(&$royaume, $type, $categorie)
	{
		global $db;
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('encheres');
		//$this->recherche_batiment('ecole_magie');
    // Nombre d'objets en vente
    /// TODO: à améliorer
    $requete = 'SELECT COUNT(*) FROM hotel WHERE type = "vente" AND id_vendeur = '.$this->perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$objet_max = 10;
		$bonus_craft = ceil($this->perso->get_artisanat() / 5);
		$objet_max += $bonus_craft;
		$this->set_jauge_int($row[0], $objet_max, 'pa', 'Vos objets en vente : ');
		
		// Onglets
		$armes = new interf_bal_smpl('span', '', false, 'icone icone-forge');
		$armes->set_tooltip('Armes');
		$this->onglets->add_onglet($armes, 'hotel.php?type='.$type.'&categorie=arme&ajax=2', 'tab_arme', 'ecole_mag', $categorie=='arme');
		$armures = new interf_bal_smpl('span', '', false, 'icone icone-casque');
		$armures->set_tooltip('Armures');
		$this->onglets->add_onglet($armures, 'hotel.php?type='.$type.'&categorie=armure&ajax=2', 'tab_armure', 'ecole_mag', $categorie=='armure');
		$dressage = new interf_bal_smpl('span', '', false, 'icone icone-faucon');
		$dressage->set_tooltip('Dressage');
		$this->onglets->add_onglet($dressage, 'hotel.php?type='.$type.'&categorie=objet_pet&ajax=2', 'tab_objet_pet', 'ecole_mag', $categorie=='objet_pet');
		$accessoires = new interf_bal_smpl('span', '', false, 'icone icone-pentacle');
		$accessoires->set_tooltip('Accessoires');
		$this->onglets->add_onglet($accessoires, 'hotel.php?type='.$type.'&categorie=accessoire&ajax=2', 'tab_accessoire', 'ecole_mag', $categorie=='accessoire');
		$objets = new interf_bal_smpl('span', '', false, 'icone icone-alchimie');
		$objets->set_tooltip('Objets');
		$this->onglets->add_onglet($objets, 'hotel.php?type='.$type.'&categorie=objet&ajax=2', 'tab_objet', 'ecole_mag', $categorie=='objet');
		$gemmes = new interf_bal_smpl('span', '', false, 'icone icone-diament');
		$gemmes->set_tooltip('Gemmes');
		$this->onglets->add_onglet($gemmes, 'hotel.php?type='.$type.'&categorie=gemme&ajax=2', 'tab_gemme', 'ecole_mag', $categorie=='gemme');
		$grimmoires = new interf_bal_smpl('span', '', false, 'icone icone-livres');
		$grimmoires->set_tooltip('Grimmoires');
		$this->onglets->add_onglet($grimmoires, 'hotel.php?type='.$type.'&categorie=grimmoire&ajax=2', 'tab_grimmoire', 'ecole_mag', $categorie=='grimmoire');
		$objt_perso = new interf_bal_smpl('span', '', false, 'icone icone-inventaire2');
		$objt_perso->set_tooltip('Mes objets');
		$this->onglets->add_onglet($objt_perso, 'hotel.php?type='.$type.'&categorie=perso&ajax=2', 'tab_perso', 'ecole_mag', $categorie=='perso');
				
		// Vente / achat
		$haut = $this->onglets->get_haut();
		$vente = $haut->add( new interf_elt_menu(new interf_bal_smpl('span', '', false, 'icone icone-argent4'), 'hotel.php?type=vente&categorie='.$categorie, 'return charger(\'hotel.php?type=vente&categorie=\'+hdv_type);', false, 'action'.($type=='vente'?' actif':'')) );
		$vente->set_tooltip('Vente');
		$achat = $haut->add( new interf_elt_menu(new interf_bal_smpl('span', '', false, 'icone icone-argent'), 'hotel.php?type=achat&categorie='.$categorie, 'return charger(\'hotel.php?type=achat&categorie=\'+hdv_type);', false, 'action'.($type=='achat'?' actif':'')) );
		$achat->set_tooltip('Achat');
		//interf_base::code_js('$("#tab_ville").on("shown.bs.tab", function (e) { var ind=e.target.indexOf("#tab_"); var cat = e.target.substr(ind+5); alert(ind+" : "+cat); });');
		interf_base::code_js('var hdv_type = "'.$type.'";$("#tab_ville").on("shown.bs.tab", function (e) { var url=e.target.toString(); var ind=url.indexOf("#tab_"); var cat = url.substr(ind+5); hdv_type = cat; });');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		// Liste d'objets
		$onglet = $this->onglets->get_onglet('tab_'.$categorie);
		if( $type == 'vente' )
			$onglet->add( new interf_vente_hdv($royaume, $categorie, $n) );
		else
			$onglet->add( new interf_achat_hdv($royaume, $categorie, $n) );
	}
}

/// Classe de base pour les listes d'achats de sorts
class interf_achat_hdv extends interf_liste_achat
{	
	const url = 'hotel.php';
	protected $categorie;
	function __construct(&$royaume, $categorie, $nbr_alertes=0)
	{
		global $db;
		$this->categorie = $categorie;
		$mois = 60 * 60 * 24 * 31;
		/// TODO: passer par un objet
		if( $categorie == 'perso' )
		{
			$requete = 'SELECT * FROM hotel WHERE type = "vente" AND id_vendeur='.joueur::get_perso()->get_id();
			$this->txt_achat = 'Récupérer';
		}
		else
		{
			$abbr = objet_invent::get_abbrev($categorie);
			$royaumes = self::get_royaumes($royaume);
			$requete = 'SELECT * FROM hotel WHERE type = "vente" AND race IN ('.implode($royaumes, ',').') AND SUBSTRING(objet FROM 1 FOR 1)="'.$abbr.'" AND time>'.(time() - $mois). ' AND id_vendeur != '.joueur::get_perso()->get_id();
		}
		$objets = array();
		$req = $db->query($requete);
		while( $res = $db->read_assoc($req) )
		{
			$obj = objet_invent::factory( $res['objet'] );
			$obj->set_id( $res['id'] );
			$obj->set_prix( $res['prix'] );
			$obj->time = $res['time'] + $mois - time();
			$objets[] = $obj;
		}
		parent::__construct($royaume, 'tbl_'.$categorie, $objets, $nbr_alertes);
	}
	
	/// Renvoie tout les royaumes qui peuvent avoir des items en commun
	static function get_royaumes($royaume)
	{
		global $db;
		$royaumes = array();
		$req = $db->query('SELECT * FROM diplomatie WHERE race="'.sSQL($royaume->get_race()).'";');
		if($db->num_rows($req) > 0)
		{
			$res = $db->read_assoc($req);
			foreach($res as $race => $diplomatie) 
			{ 
				if( (($diplomatie <= 5) || ($diplomatie == 127)) && ($diplomatie != 'race') )
				{
					$royaumes[] = '"'.$race.'"'; 
				}
			}
		}
		return $royaumes;
	}
	function aff_titres_col()
	{
		switch( $this->categorie )
		{
		case 'arme':
		case 'armure':
		case 'accessoire':
			$this->tbl->nouv_cell('Slot');
			break;
		/*case 'objet':
			$this->tbl->nouv_cell('Nombre');
			break;*/
		case 'gemme':
			$this->tbl->nouv_cell('Niveau');
			break;
		}
		$this->tbl->nouv_cell('Tps restant');
	}
	
	function aff_cont_col(&$elt)
	{
		switch( $this->categorie )
		{
		case 'arme':
		case 'armure':
		case 'accessoire':
			$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_info_enchant(), false, 'xsmall') );
			break;
		/*case 'objet':
			$this->tbl->nouv_cell( $elt->get_nombre() );
			break;*/
		case 'gemme':
			$this->tbl->nouv_cell( $elt->get_niveau() );
			break;
		}
		$this->tbl->nouv_cell( transform_min_temp($elt->time) );
	}
	
	protected function peut_acheter(&$elt)
	{	
		return $this->perso->get_star() >= $elt->get_prix() || $this->categorie = 'perso';
	}
}

/// Classe de base pour les listes d'achats de sorts
class interf_vente_hdv extends interf_cont
{	
	function __construct(&$royaume, $categorie, $nbr_alertes=0)
	{
		global $db;
		$duree = 60 * 60 * 24 * 31 * 3;
		$perso = joueur::get_perso();
		
		$this->tbl = $this->add( new interf_data_tbl('tbl_'.$categorie, '', false, false, 358 - $nbr_alertes * 30, $this->ordre) );
		$this->tbl->nouv_cell('Nom');
		if( $categorie == 'objet' )
			$this->tbl->nouv_cell('Nombre');
		$this->tbl->nouv_cell('Tps restant');
		$this->tbl->nouv_cell('Stars');
		$this->tbl->nouv_cell('Vente');
		
		// Contenu
		if( $categorie == 'perso' )
		{
			$requete = 'SELECT * FROM hotel WHERE type = "achat" AND id_vendeur='.joueur::get_perso()->get_id();
		}
		else
		{
			$abbr = objet_invent::get_abbrev($categorie);
			$royaumes = interf_achat_hdv::get_royaumes($royaume);
			$requete = 'SELECT * FROM hotel WHERE type = "achat" AND race IN ('.implode($royaumes, ',').') AND SUBSTRING(objet FROM 1 FOR 1)="'.$abbr.'" AND time>'.(time() - $duree). ' AND id_vendeur != '.joueur::get_perso()->get_id();
		}
		$req = $db->query($requete);
		while( $res = $db->read_assoc($req) )
		{
			$e = objet_invent::factory( $res['objet'] );
			$ob = explode('x', $res['objet']);
			$vente = $perso->recherche_objet( $ob[0] );
			$this->tbl->nouv_ligne(false, $vente ? '' : 'non-achetable');
			$this->tbl->nouv_cell( $e->get_nom() );
			if( $categorie == 'objet' )
				$this->tbl->nouv_cell( $e->get_nombre() );
			$this->tbl->nouv_cell( transform_min_temp($res['time'] + $duree - time()) );
			$this->tbl->nouv_cell( $res['prix'] );
			if( $categorie == 'perso' )
				$this->tbl->nouv_cell( new interf_lien('Retirer', 'hotel.php?type=vente&categorie='.$categorie.'&action=suppr&id='.$res['id']) );
			else if( $vente )
			{
				if( $categorie == 'objet' && $e->get_nombre() > 1 )
					$this->tbl->nouv_cell( new interf_lien('Vendre', 'hotel.php?type=vente&categorie='.$categorie.'&action=vente&id='.$res['id']) );
				else
					$this->tbl->nouv_cell( new interf_lien('Vendre', 'hotel.php?type=vente&categorie='.$categorie.'&action=vente&id='.$res['id']) );
			}
			else
				$this->tbl->nouv_cell('&nbsp;');
		}
		
		// Lien pour déposer une offre d'achat
		$this->add( new interf_lien('Déposer une offre d\'achat', 'hotel.php?action=offre&type=achat&categorie='.$categorie, false, 'offre_achat') );
	}
}

/**
 * Boite de dialogue pour la vente d'objet en plusieurs exemplaires
 */
class interf_vente_objets extends interf_dialogBS
{
  function __construct($objet, $categorie, $obj_perso)
  {
  	$obj = objet_invent::factory($objet['objet']);
  	interf_dialogBS::__construct('Vente d\'objets');
		/// TODO: passer par un objet
    $this->add( new interf_bal_smpl('p', 'Objet à vendre : '.$obj->get_nom()) );
    $form = $this->add( new interf_form('hotel.php?action=vente&type=vente&categorie='.$categorie.'&id='.$objet['id'], 'vente_hdv') );
		$form->set_attribut('name', 'formulaire');
		$chp = $form->add_champ_bs('number', 'nombre', null, 1, 'Nombre');
    $chp->set_attribut('min', 1);
    $chp->set_attribut('max', min($obj->get_nombre(), $obj_perso[0]));
    $chp->set_attribut('step', 1);
    
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Vendre', '$(\'#modal\').modal(\'hide\');charger_formulaire(\'vente_hdv\');', 'primary');
  }
}

/**
 * Boite de dialogue pour le dépôt d'une offre d'achat
 */
class interf_offre_achat extends interf_dialogBS
{
  function __construct($categorie, $taxe)
  {
  	interf_dialogBS::__construct('Offre d\'achat');
    $form = $this->add( new interf_form('hotel.php?action=offre&type=vente&categorie='.$categorie, 'achat_hdv') );
    $form->set_attribut('name', 'formulaire');
    $div_sel = $form->add( new interf_bal_cont('div', false, 'input-group') );
    $div_sel->add( new interf_bal_smpl('span', 'Objet', false, 'input-group-addon') );
    $sel = $div_sel->add( new interf_select_form('objet', false, false, 'form-control') );
    $nombre = false;
    switch($categorie)
    {
    case 'arme':
    	$objets = arme::create(null, null, 'type, coefficient ASC', false, 'lvl_batiment < 9');
    	break;
    case 'armure':
    	$objets = armure::create(null, null, 'lvl_batiment, prix ASC', false, 'lvl_batiment < 9');
    	break;
    case 'accessoire':
    	$objets = accessoire::create(null, null, 'type, effet ASC', false, 'lvl_batiment < 9');
    	break;
    case 'objet_pet':
    	$objets = objet_pet::create(null, null, 'type, dressage ASC', false, 'lvl_batiment < 9');
    	break;
    case 'objet':
    	$objets = objet::create(null, null, 'type, prix ASC', false, 'achetable = "n" AND type NOT LIKE "objet_quete"');
    	$nombre = true;
    	break;
    case 'gemme':
    	$objets = gemme::create(null, null, 'nom ASC', false, 'niveau = 1');
    	break;
    case 'grimoire':
    	$objets = grimoire::create(null, null, 'prix ASC', false, 'type NOT LIKE "attr_perso"');
    	break;
    /// TODO: log defaut
		}
		foreach($objets as $obj)
		{
			$obj->recompose_texte();
			$sel->add_option($obj->get_nom(), $obj->get_texte());
		}
	  $form->add( new interf_bal_smpl('br') );
    if( $nombre )
    {
			$chp0 = $form->add_champ_bs('number', 'nombre', null, 1, 'Nombre');
	    $chp0->set_attribut('min', 1);
	    $chp0->set_attribut('step', 1);
	    $chp0->set_attribut('onchange', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).') * formulaire.nombre.value');
	    $chp0->set_attribut('onkeyup', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).') * formulaire.nombre.value');
	    $form->add( new interf_bal_smpl('br') );
		}
		else
			$form->add_champ_bs('hidden', 'nombre', null, 1);
    $chp1 = $form->add_champ_bs('number', 'prix', null, '0', 'Prix de vente', 'stars');
    $chp1->set_attribut('min', 0);
    $chp1->set_attribut('step', 1);
    if( $nombre )
    {
	    $chp1->set_attribut('onchange', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).') * formulaire.nombre.value');
	    $chp1->set_attribut('onkeyup', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).') * formulaire.nombre.value');
    }
		else
		{
	    $chp1->set_attribut('onchange', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).')');
	    $chp1->set_attribut('onkeyup', 'formulaire.taxe.value = Math.round(formulaire.prix.value * '.$taxe.');formulaire.cout.value = Math.round(formulaire.prix.value * '.(1+$taxe).')');
		}
    $form->add( new interf_bal_smpl('br') );
    $chp2 = $form->add_champ_bs('text', 'taxe', null, '0', 'Taxe', 'stars');
    $chp2->set_attribut('disabled', 'true');
    $form->add( new interf_bal_smpl('br') );
    $chp3 = $form->add_champ_bs('text', 'cout', null, '0', 'Coût total', 'stars');
    $chp3->set_attribut('disabled', 'true');
    
    $this->add( new interf_bal_smpl('p', 'Attention les stars vous seront prélevées tout de suite. Elles vous seront rendues, moins les taxes, si vous retirez votre offre.') );
    
    $this->ajout_btn('Annuler', 'fermer');
    $btn = $this->ajout_btn('Déposer', '$(\'#modal\').modal(\'hide\');charger_formulaire(\'achat_hdv\');', 'primary');
	}
}
?>