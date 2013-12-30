<?php
/// @addtogroup Interface
/**
 * @file interf_inventaire.class.php
 * Gestion de l'affichage de l'inventaire
 */
 
/**
 * Classe gérant l'affichage de l'inventaire
 */
class interf_inventaire extends interf_cont
{
  protected $perso;  ///< Objet représentant le personnage dont il faut afficher l'inventaire.
  protected $adresse = 'inventaire.php';  ///< Adresse de la page.
  protected $invent;  ///< Inventaire à afficher.
  protected $slot;  ///< Slot à afficher.
  protected $onglets;  ///< Onglets principal.
  protected $onglets_slots;  ///< Onglets des slots.
  /**
   * Constructeur
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire.
   * @param $page       Inventaire à afficher
   * @param $slot       Slot à afficher
   */
  function __construct(&$perso, $adresse, $invent, $slot)
  {
    $this->perso = &$perso;
    $this->adresse = $adresse;
    $this->invent = $invent;
    $this->slot = $slot;
    // Javascript
    $script = $this->add( new interf_bal_smpl('script') );
    $script->set_attribut('type', 'text/javascript');
    $script->set_attribut('src', './javascript/inventaire.js');
    // onglets principal
    $this->onglets = $this->add( new interf_onglets('onglets_princ', 'invent') );
    $this->onglets->add_onglet('Personnage', 'inventaire.php?page=perso', 'perso', $invent=='perso');
    $this->onglets->add_onglet('Créature', 'inventaire.php?page=pet', 'pet', $invent=='pet');
    $this->onglets->add_onglet('Actions', 'inventaire.php?page=actions', 'actions', $invent=='actions');
    // un peu d'espace
    //$this->add( new interf_bal_smpl('div', '', false, 'spacer') );
    $this->add( new interf_bal_smpl('br') );
    // onglets des slots
    $this->onglets_slots = $this->add( new interf_onglets('onglets_sots', 'slots') );
    $this->onglets_slots->add_onglet('Utile', 'inventaire.php?slot=utile', 'utile', $slot=='utile');
    $this->onglets_slots->add_onglet('Equip.', 'inventaire.php?slot=equipement', 'equip', $slot=='equip');
    $this->onglets_slots->add_onglet('Roy.', 'inventaire.php?slot=royaume', 'royaume', $slot=='royaume');
  }
  
  /**
   * Affiche le contenu de l'inventaire
   * @param $type   'perso' ou 'pet'.
   * @param $modif   Indique si on peut modifier l'interface.
   */
  function set_contenu($type='perso', $modif=true)
  {
    $tbl = $this->onglets/*->get_onglet($this->invent)*/->add( new interf_tableau() );
    $tab_loc = array();
    switch($type)
    {
    case 'perso':
      $style = 'background: url(\'image/666.png\') center no-repeat;';

      $tab_loc[0]['loc'] = 'accessoire';
      $tab_loc[0]['type'] = 'accessoire';
      $tab_loc[1]['loc'] = 'tete';
      $tab_loc[1]['type'] = 'armure';
      $tab_loc[2]['loc'] = 'cou';
      $tab_loc[2]['type'] = 'armure';

      $tab_loc[3]['loc'] = 'main_droite';
      $tab_loc[3]['type'] = 'arme';
      $tab_loc[4]['loc'] = 'torse';
      $tab_loc[4]['type'] = 'armure';
      $tab_loc[5]['loc'] = 'main_gauche';
      $tab_loc[5]['type'] = 'arme';

      $tab_loc[6]['loc'] = 'main';
      $tab_loc[6]['type'] = 'armure';
      $tab_loc[7]['loc'] = 'ceinture';
      $tab_loc[7]['type'] = 'armure';
      $tab_loc[8]['loc'] = 'doigt';
      $tab_loc[8]['type'] = 'armure';

      $tab_loc[9]['loc'] = ' ';
      $tab_loc[9]['type'] = 'vide';
      $tab_loc[10]['loc'] = 'jambe';
      $tab_loc[10]['type'] = 'armure';
      $tab_loc[11]['loc'] = 'dos';
      $tab_loc[11]['type'] = 'armure';

      $tab_loc[12]['loc'] = ' ';
      $tab_loc[12]['type'] = 'vide';
      $tab_loc[13]['loc'] = 'chaussure';
      $tab_loc[13]['type'] = 'armure';
      $tab_loc[14]['loc'] = ' ';
      $tab_loc[14]['type'] = 'vide';
      break;
    case 'pet':
      $style = 'background: url(\'image/creature.png\') center no-repeat;';
      
      $tab_loc[0]['loc'] = 'cou';
      $tab_loc[0]['type'] = 'armure';
      $tab_loc[1]['loc'] = 'selle';
      $tab_loc[1]['type'] = 'armure';
      $tab_loc[2]['loc'] = 'dos';
      $tab_loc[2]['type'] = 'armure';

      $tab_loc[3]['loc'] = 'arme';
      $tab_loc[3]['type'] = 'arme';
      $tab_loc[4]['loc'] = 'torse';
      $tab_loc[4]['type'] = 'armure';
      $tab_loc[5]['loc'] = ' ';
      $tab_loc[5]['type'] = 'vide';
    }
    $tbl->set_attribut('style', $style);
    $tbl->set_attribut('cellspacing', 3);
    $tbl->set_attribut('width', '100%');
    
    $color = 2;
    $compteur=0;
    foreach($tab_loc as $loc)
    {
    	if( ($compteur % 3) == 0 && $compteur )
  		{
        $tr = $tbl->nouv_ligne();
        $tr->set_attribut('style', 'height : 55px;');
        //$tbl->add($tr);
  		}
  		$td = $tbl->nouv_cell(null, null, ($loc['type']!='vide')?'inventaire2':null);
			if ($loc['type']=='vide' && ($compteur % 3) != 2)
			{
				if((is_ville($this->perso->get_x(), $this->perso->get_y()) == 1) AND (!array_key_exists('ville', $_GET) OR (array_key_exists('ville', $_GET) AND $_GET['ville'] == 'no')))
				{
          $td->set_attribut('class', 'inventaireville');
					if(($compteur/3)==3)
					{
						$domprop .= " id='hdv'";
					}
					elseif (($compteur/3)==4)
					{
            $td->set_attribut('id', 'marchand');
            $td->set_attribut('alt', 'marchand');
					}
				}
			}
			else
        $td->set_attribut('id', 'drop_'.$loc['loc']);
      if( $type == 'perso' )
        $this->case_perso($td, $loc, $modif);
      else
        $this->case_pet($td, $loc, $modif);
      $compteur++;
    }

  }
  
  protected function case_perso(&$td, $loc, $modif)
  {
    global $Gtrad, $id_elt_ajax, $db;
    if( $this->perso->inventaire()->$loc['loc'] != '' )
    {
			$objet = decompose_objet($this->perso->get_inventaire_partie($loc['loc']));
			//On peut désequiper
			if($modif AND $this->perso->get_inventaire_partie($loc['loc']) != '' AND $this->perso->get_inventaire_partie($loc['loc']) != 'lock')
        $desequip = true;
      else
        $desequip = false;
			switch($loc['type'])
			{
				case 'arme' :
					if( $this->perso->get_inventaire_partie($loc['loc']) != 'lock' )
					{
						$requete = "SELECT * FROM `arme` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						$image = 'image/arme/arme'.$row['id'].'.png';
						$nom = $row['nom'];
					}
					else
					{
						$nom = 'Lock';
						$image = '';
					}
				break;
				case 'armure' :
					$requete = "SELECT * FROM `armure` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/armure/'.$loc['loc'].'/'.$loc['loc'].$row['id'].'.png';
					$nom = $row['nom'];

				break;
				case 'accessoire' :
					$requete = "SELECT * FROM `accessoire` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/accessoire/accessoire'.$row['id'].'.png';
					$nom = $row['nom'];
				break;
			}
			$img = new interf_bal_smpl('img');
			$img->set_attribut('src', $image);
			$img->set_attribut('style', 'float : left;');
			$img->set_attribut('title', 'Déséquiper');
			$img->set_attribut('alt', 'Déséquiper');
			if($desequip)
			{
        $span = new interf_bal_cont('span', 'drag_'.$objet["id_objet"]);
        $td->add($span);
        $lien = new interf_bal_cont('a');
        $lien->set_attribut('href', $this->adresse.'?action=desequip&amp;partie='.$loc['loc'].'&amp;filtre='.$this->slot);
        $lien->set_attribut('onclick', 'return envoiInfo(this.href, \''.$id_elt_ajax.'\');');
        $span->add($lien);
        $lien->add($img);
			}
			else
        $td->add($img);
      $td->add( new interf_bal_smpl('strong', $nom) );
      $txt_slot = '';
			if($objet['slot'] > 0)
        $txt_slot = 'Slot niveau '.$objet['slot'];
			elseif($objet['slot'] == '0')
        $txt_slot = 'Slot impossible';
			if($txt_slot)
			{
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_bal_smpl('span', $txt_slot, false, 'xsmall') );
      }
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_bal_smpl('span', 'Enchantement de '.$row_e['enchantement_nom'], false, 'xsmall') );
			}
    }
		else
      $td->add( new interf_txt($Gtrad[$loc['loc']]) );
      
		if($this->perso->get_inventaire_partie($loc['loc']) != '' AND $this->perso->get_inventaire_partie($loc['loc']) != 'lock')
		{
      $txt = '';
			switch($loc['type'])
			{
				case 'arme':
					if($loc['loc'] == 'main_droite')
            $txt = 'Dégâts : '.$this->perso->get_arme_degat('droite');
					else
					{
						if($row['type'] == 'dague')
              $txt =  'Dégâts : '.$this->perso->get_arme_degat('gauche');
						else
              $txt = 'Dégâts absorbés : '.$this->perso->get_bouclier()->degat;
					}
				break;
				case 'armure':
          $txt = 'PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
			if($txt)
			{
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_txt($txt) );
      }
		}
  }

  protected function case_pet(&$td, $loc, $modif)
  {
    global $Gtrad, $id_elt_ajax, $db;
		if($this->perso->inventaire_pet()->$loc['loc'] != '')
		{
			$objet = decompose_objet($this->perso->get_inventaire_partie($loc['loc'], true));
			//On peut désequiper
			if($modif AND $this->perso->get_inventaire_partie($loc['loc'], true) != '' AND $this->perso->get_inventaire_partie($loc['loc'], true) != 'lock')
        $desequip = true;
      else
        $desequip = false;
			switch($loc['type'])
			{
				case 'arme_pet' :
					if($this->perso->get_inventaire_partie($loc['loc'], true) != 'lock')
					{
						$requete = "SELECT * FROM `objet_pet` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						$image = 'image/objet_pet/arme_pet/arme'.$row['id'].'.png';
						$nom = $row['nom'];
					}
					else
					{
						$nom = 'Lock';
						$image = '';
					}
				break;
				case 'armure' :
				case 'selle':
				case 'collier':
				case 'carapacon':
					$requete = "SELECT * FROM `objet_pet` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/objet_pet/'.$loc['loc'].'/'.$loc['loc'].$row['id'].'.png';
					$nom = $row['nom'];
				break;
				case 'accessoire' :
					$requete = "SELECT * FROM `accessoire` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/accessoire/accessoire'.$row['id'].'.png';
					$nom = $row['nom'];
				break;
			}
			$img = new interf_bal_smpl('img');
			$img->set_attribut('src', $image);
			$img->set_attribut('style', 'float : left;');
			$img->set_attribut('title', 'Déséquiper');
			$img->set_attribut('alt', 'Déséquiper');
			if($desequip)
			{
        $lien = new interf_bal_cont('a');
        $lien->set_attribut('href', $this->adresse.'?action=desequip&amp;partie='.$loc['loc'].'&amp;filtre='.$this->slot);
        $lien->set_attribut('onclick', 'return envoiInfo(this.href, \''.$id_elt_ajax.'\');');
        $td->add($lien);
        $lien->add($img);
			}
			else
        $td->add($img);
      $td->add( new interf_bal_smpl('strong', $nom) );
      $txt_slot = '';
			if($objet['slot'] > 0)
        $txt_slot = 'Slot niveau '.$objet['slot'];
			elseif($objet['slot'] == '0')
        $txt_slot = 'Slot impossible';
			if($txt_slot)
			{
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_bal_smpl('span', $txt_slot, false, 'xsmall') );
      }
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_bal_smpl('span', 'Enchantement de '.$row_e['enchantement_nom'], false, 'xsmall') );
			}
		}
		else
      $td->add( new interf_txt($Gtrad[$loc['loc']]) );


		if($this->perso->get_inventaire_partie($loc['loc'], true) != '' AND $this->perso->get_inventaire_partie($loc['loc'], true) != 'lock')
		{
      $txt = '';
			switch($loc['type'])
			{
				case 'arme_pet' :
					$txt = 'Dégâts : '.$joueur->get_arme_degat('pet');
				break;
				case 'armure' :
				case 'selle' :
				case 'collier' :
				case 'carapacon' :
					$txt = 'PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
			if($txt)
			{
        $td->add( new interf_bal_smpl('br') );
        $td->add( new interf_txt($txt) );
      }
		}
  }
  
  //
  function affiche_slots($modif=true)
  {
    global $G_place_inventaire, $interf, $db;
    $cont = $this->onglets_slots->add( new interf_bal_cont('div', 'inventaire_slot') );
    if($this->perso->get_inventaire_slot() != '')
    {
      $i = 0;
      $arme_de_siege = 0;
    	$this->perso->restack_objet();
    	foreach($this->perso->get_inventaire_slot_partie() as $invent)
    	{
		    $image='image/interface/inventaire/Unknown.png';
    		if($invent !== 0 AND $invent != '')
    		{
          $objet = objet_invent::factory($invent);
          if( $objet->est_identifie() )
          {
            $nom = $objet->get_nom();
            $nbr = $objet->get_nombre();
            if( $nbr > 1 )
              $nom .= ' X '.$nbr;
  				  $echo = description_objet($invent);
          }
          else
          {
            $nom = 'Objet non indentifié';
  					$echo = 'Objet non indentifié';
          }
          $partie = $objet->get_type();
          $image = $objet->get_image();
          $div = $cont->add( new interf_bal_cont('div', 'invent_slot'.$i, 'drag_'.$partie) );
          $div->set_attribut('style', 'width:33%;position: relative;');
          $img = $div->add( new  interf_bal_smpl('img') );
          $img->set_attribut('src', $image);
          $span1 = $div->add( new interf_bal_smpl('span', $nom) );
          $span1->set_attribut('name', 'overlib');
          $div->set_attribut('onclick', 'chargerPopover(\'invent_slot'.$i.'\', \'infos_'.$i.'\', \'left\', \''.$this->adresse.'?action=infos&id='.$invent.'\', \''.$nom.'\')');
          //$script .= 'dragndrop(".drag_'.$partie.'", "#drop_'.$partie.'");'."\n";
          $script .= 'dragndrop("#invent_slot'.$i.'", "#drop_'.$partie.'", "'.$this->adresse.'");';
          unset($div, $p, $span1, $span2, $img);
        }
        $i++;
      }
      $js = $cont->add( new interf_bal_smpl('script', $script) );
      $js->set_attribut('type', 'text/javascript');
    }
  }
}

/**
 * Classe gérant l'affichage de l'inventaire
 */
class interf_objet_invent extends interf_cont
{
  function __construct($nom, $infos1=false, $infos2=false)
  {
  }
}

/**
 * Interface pour afficher les information sur un objet
 */
class interf_infos_objet extends interf_princ
{
  /**
   * Renvoie la bonne instance de la classe pour afficher les informations sur un objet (dans un popover)
   * @param $objet    objet sous forme textuelle
   */
  function __construct($objet)
  {
    $obj = objet_invent::factory($objet);
    $tbl = $this->add( new interf_tableau() );
    $noms = $obj->get_noms_infos();
    $vals = $obj->get_valeurs_infos();
    for($i=0; $i<count($noms); $i++)
    {
      $tbl->nouv_ligne();
      $tbl->nouv_cell($noms[$i], null, null, true);
      $tbl->nouv_cell($vals[$i]);
      //echo $i.' - '.$noms[$i].'='.$vals[$i].'<br/>';
    }
  }
}
?>
