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
  //protected $perso;  ///< Objet représentant le personnage dont il faut afficher l'inventaire.
  const url = 'inventaire.php';  ///< Adresse de la page.
  /*protected $invent;  ///< Inventaire à afficher.
  protected $slot;  ///< Slot à afficher.
  protected $onglets;  ///< Onglets principal.
  protected $onglets_slots;  ///< Onglets des slots.*/
  /**
   * Constructeur
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire.
   * @param $invent     Inventaire à afficher
   * @param $slot       Slot à afficher
   * @param $modif      Indique si on peut modifier l'interface.
   */
  function __construct(&$perso, $invent, $slot, $modif=true)
  {
    /*$this->perso = &$perso;
    //$this->adresse = $adresse;
    $this->invent = $invent;
    $this->slot = $slot;*/
    // Javascript
    $script = $this->add( new interf_bal_smpl('script') );
    $script->set_attribut('type', 'text/javascript');
    $script->set_attribut('src', './javascript/inventaire.js');
    // onglets principal
    $onglets = $this->add( new interf_onglets('onglets_princ', 'invent', 'invent') );
    $onglets->add_onglet('Personnage', self::url.'?action=princ&page=perso', 'perso', $invent=='perso');
    $onglets->add_onglet('Créature', self::url.'?action=princ&page=pet', 'pet', $invent=='pet');
    $onglets->add_onglet('Actions', self::url.'?action=princ&page=actions', 'actions', $invent=='actions');
    $onglets->add( new interf_invent_equip($perso, $invent, $modif) );
    // un peu d'espace
    $this->add( new interf_bal_smpl('br') );
    // onglets des slots
    $onglets_slots = $this->add( new interf_onglets('onglets_sots', 'slots', 'invent') );
    $onglets_slots->add_onglet('Utile', self::url.'?action=sac&slot=utile', 'utile', $slot=='utile');
    $onglets_slots->add_onglet('Equipement', self::url.'?action=sac&slot=equipement', 'equip', $slot=='equipement');
    $onglets_slots->add_onglet('Royaume', self::url.'?action=sac&slot=royaume', 'royaume', $slot=='royaume');
    $onglets_slots->add_onglet('Artisanat', self::url.'?action=sac&slot=artisanat', 'royaume', $slot=='artisanat');
    $onglets_slots->add( new interf_invent_sac($perso, $slot, $modif) );
    interf_base::code_js( '$( "#slots" ).droppable({accept: ".equipe", activeClass: "invent_cible", hoverClass: "invent_hover", drop: drop_func});' );
  }
}

class interf_invent_equip extends interf_tableau
{
  function __construct(&$perso, $type, $modif=true)
  {
    interf_tableau::__construct();
    $this->set_entete(false);
    $tab_loc = array();
    switch($type)
    {
    case 'perso':
      $style = 'background: url(\'image/666.png\') center no-repeat; height: 300px;';

      $emplacements = array(  'grand_accessoire',   'tete',       'cou',
                              'main_droite',        'torse',      'main_gauche',
                              'main',               'ceinture',   'doigt',
                              'moyen_accessoire',   'jambe',      'dos',
                              'petit_accessoire_1', 'chaussure',  'petit_accessoire_2');
      $invent = $perso->inventaire();
      break;
    case 'pet':
      $style = 'background: url(\'image/creature.png\') center no-repeat;';

      $emplacements = array(  'cou_pet',   'selle',       'dos_pet',
                              'arme_pet',  'torse_pet',   ' ');
      $invent = $perso->inventaire_pet();
      break;
    case 'actions':
      $emplacements = array();
      if( is_ville($perso->get_x(), $perso->get_y()) == 1 )
        $emplacements = array_merge($emplacements, array('vendre_marchand', 'hotel_vente', 'depot'));
      $emplacements = array_merge($emplacements, array('slot_1',  'slot_2',  'slot_3') );
      $emplacements = array_merge($emplacements, array('utiliser', 'identifier', 'enchasser'));
      $invent = null;
    }
    $this->set_attribut('style', $style);
    $this->set_attribut('cellspacing', 3);
    $this->set_attribut('width', '100%');

    $color = 2;
    $compteur=0;
    foreach($emplacements as $loc)
    {
    	if( ($compteur % 3) == 0 && $compteur )
  		{
        $tr = $this->nouv_ligne();
        $tr->set_attribut('style', 'height : 55px;');
  		}
  		$td = $this->nouv_cell();
      if( $loc != ' ' )
      {
        $objet = $invent ? $invent->$loc : '';
        if( $objet != '' && $objet != 'lock' )
        {
          $desequip = $modif;
          $obj = objet_invent::factory( $objet );
    		}
        else
        {
          $desequip = false;
          $obj = new zone_invent($loc, $objet === 'lock', $perso);
    		}//.($desequip?' equip':'')
        $td->add( new interf_objet_invent($obj, $desequip, $loc, $desequip?'equipe':'', 'drop_'.$loc) );
        if( $desequip )
          interf_base::code_js( '$( "#drop_'.$loc.'" ).draggable({ helper: "original", tolerance: "touch", revert: "invalid" });' );
      }
      $compteur++;
      interf_base::code_js( '$( "#drop_'.$loc.'" ).droppable({accept: ".drag_'.substr($loc, 0, 15).'", activeClass: "invent_cible", hoverClass: "invent_hover", drop: drop_func});' );
    }
    interf_base::code_js('page = "'.$type.'"');
  }
}

class interf_invent_sac extends interf_cont
{
  private $cols;
  function __construct(&$perso, $type, $modif=true)
  {
    $this->cols[0] = $this->add( new interf_bal_cont('div', 'col1', 'col_invent') );
    $this->cols[1] = $this->add( new interf_bal_cont('div', 'col2', 'col_invent') );
    $this->cols[2] = $this->add( new interf_bal_cont('div', 'col3', 'col_invent') );
    switch( $type )
    {
    case 'utile':
      $cols = array('alchimie', 'grimoires', 'quêtes');
      break;
    case 'equipement':
      $cols = array('armes', 'armures', 'accessoires');
      break;
    case 'royaume':
      $cols = array('sièges', 'drapeaux', 'bâtiments');
      break;
    case 'artisanat':
      $cols = array('ingrédients', 'outils', 'gemmes');
      break;
    }
    for($i=0; $i<3; $i++)
    {
      $this->cols[$i]->add( new interf_bal_smpl('span', $cols[$i], false, 'xsmall') );
    }

    if($perso->get_inventaire_slot() != '')
    {
      $i = 0;
      $arme_de_siege = 0;
    	$perso->restack_objet();
    	foreach($perso->get_inventaire_slot_partie() as $invent)
    	{
        $objet = objet_invent::factory($invent);
        $col = $objet->get_colone($type);
        if( $col !== false )
        {
          if( $objet->est_identifie() )
          {
            $drags = '';
            if( $type == 'equipement' )
              $drags .= 'drag_'.$objet->get_emplacement();
            if( is_ville($perso->get_x(), $perso->get_y()) == 1 )
            {
              if( $type == 'royaume' )
                $drags .= ' drag_depot';
              else
                $drags .= ' drag_vendre_marchand drag_hotel_vente';
            }
            if( $objet->est_utilisable() )
                $drags .= ' drag_utiliser';
            if( $objet->est_slotable() )
                $drags .= ' drag_slot_1 drag_slot_2 drag_slot_3';
            if( $objet->est_enchassable() )
                $drags .= ' drag_enchasser';
          }
          else
              $drags = 'drag_identifier';
            $div = $this->cols[$col]->add( new interf_objet_invent($objet, false, null, $drags, 'invent_slot'.$i) );
          if( $objet->est_identifie() )
            $div->set_attribut('onclick', 'chargerPopover(\'invent_slot'.$i.'\', \'infos_'.$i.'\', \'left\', \''.'inventaire.php?action=infos&id='.$invent.'\', \''.$objet->get_nom().'\')');
          interf_base::code_js( '$( "#invent_slot'.$i.'" ).draggable({ helper: "original", tolerance: "touch", revert: "invalid" });' );
        }
        $i++;
      }
    }
    interf_base::code_js('slot = "'.$type.'"');
  }
}

/**
 * Classe gérant l'affichage de l'inventaire
 */
class interf_objet_invent extends interf_bal_cont
{
  function __construct($objet, $desequip, $partie, $drags, $id=false)
  {
    global $Gtrad, $id_elt_ajax, $db;
    interf_bal_cont::__construct('div', $id, ($objet?'inventaire2 ':' ').$drags);
    if( $objet->est_identifie() )
    {
      $nom = $objet->get_nom();
      $nbr = $objet->get_nombre();
      if( $nbr > 1 )
        $nom .= ' X '.$nbr;
    }
    else
      $nom = 'Objet non indentifié';
    $image = $objet->get_image();
    if($image or $desequip)
    {
      $img = new  interf_bal_smpl('img');
      $img->set_attribut('src', $image);
      $img->set_attribut('style', 'float : left;');
      $img->set_attribut('title', 'Déséquiper');
      $img->set_attribut('alt', 'Déséquiper');
  		if($desequip)
  		{
        $lien = new interf_bal_cont('a');
        $lien->set_attribut('href', 'inventaire.php?action=desequip&amp;partie='.$partie.'&amp;filtre='.$slot);
        $lien->set_attribut('onclick', 'return envoiInfo(this.href, \''.$id_elt_ajax.'\');');
        $this->add($lien);
        $lien->add($img);
  		}
  		else
        $this->add($img);
    }
    $this->add( new interf_bal_smpl('strong', $nom) );
    $enchant = $objet->get_info_enchant();
    if( $enchant )
    {
      $this->add( new interf_bal_smpl('br') );
      $this->add( new interf_bal_smpl('span', $enchant, false, 'xsmall') );
    }
    $infos = $objet->get_info_princ();
    if( $infos )
    {
      $this->add( new interf_bal_smpl('br') );
      $this->add( new interf_txt($infos) );
    }
  }
}

/**
 * Interface pour afficher les information sur un objet
 */
class interf_infos_objet extends interf_princ
{
  /**
   * Constructeur
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
    }
  }
}

/**
 * Boite de dialogue pour l'hotel des ventes
 */
class interf_vente_hotel extends interf_dialogBS
{
  function __construct(&$perso, $index)
  {
    global $db;

    interf_dialogBS::__construct('Hotel des ventes');

		//On vérifie qu'il a moins de 10 objets en vente actuellement
    /// TODO: à améliorer
		$requete = "SELECT COUNT(*) FROM hotel WHERE id_vendeur = ".$perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$objet_max = 10;
		$bonus_craft = ceil($perso->get_artisanat() / 5);
		$objet_max += $bonus_craft;

    if( $row[0] >= $objet_max )
    {
      $this->add( new interf_alerte('danger', false) )->add_message('Vous avez déjà '.$objet_max.' objets ou plus en vente.');
      $this->ajout_btn('Ok', 'fermer', 'danger');
      return;
    }
    $this->ajout_btn('Annuler', 'fermer');
    $btn = $this->ajout_btn('Vendre', '$(\'#modal\').modal(\'hide\');envoiFormulaire(\'vente_hdv\', \'information\');', 'primary');

    $objet =  objet_invent::factory( $perso->get_inventaire_slot_partie($index) );
    $prix = $objet->get_prix_vente() * 2;
		$prixmax = $prix * 10;
    $case = new map_case( $perso->get_pos() );
    $R = new royaume( $case->get_royaume() );

    $taxe = $R->get_taxe_diplo($perso->get_race()) / 100;
    $form = $this->add( new interf_form('inventaire.php?action=vente_hotel&objet='.$index, 'vente_hdv') );
    $form->set_attribut('name', 'formulaire');
    $chp1 = $form->add_champ_bs('number', 'prix', null, $prix, 'Prix de vente', 'stars');
    $chp1->set_attribut('onchange', 'formulaire.comm.value = Math.Round(formulaire.prix.value * '.$taxe.')');
    $chp1->set_attribut('onkeyup', 'formulaire.comm.value = Math.Round(formulaire.prix.value * '.$taxe.')');
    $chp1->set_attribut('min', 0);
    $chp1->set_attribut('max', $prixmax);
    $chp1->set_attribut('step', 1);
    $form->add( new interf_bal_smpl('br') );
    $chp2 = $form->add_champ_bs('text', 'comm', null, $prix * $taxe, 'Taxe', 'stars');
    $chp2->set_attribut('disabled', 'true');
    $form->add( new interf_bal_smpl('br') );
    $chp3 = $form->add_champ_bs('text', 'max', null, $prixmax, 'Maximum', 'stars');
    $chp3->set_attribut('disabled', 'true');
    $form->add( new interf_chp_form('hidden', 'action', false, 'ventehotel') );
    interf_base::code_js('ajout_filtre_form("vente_hdv");');
  }
}

/**
 * Boite de dialogue pour les gemmes
 */
class interf_enchasser extends interf_dialogBS
{
  function __construct(&$perso, $index)
  {
    global $G_place_inventaire;

    $objet = objet_invent::factory( $perso->get_inventaire_slot_partie($index) );
    // Chances de succès
    $niveau = $objet->est_enchassable();
		switch($niveau)
		{
			case 1 :
				$difficulte = 10;
			break;
			case 2 :
				$difficulte = 30;
			break;
			case 3 :
				$difficulte = 100;
			break;
		}
    $chances = pourcent_reussite($perso->get_forge(), $difficulte);;
    if( $objet->get_enchantement() )
    {
      interf_dialogBS::__construct('Enlever une gemme');
      $this->add( new interf_bal_smpl('span', 'Permet de récupérer une gemme enchasser dans un objet.', null, 'small') );
      $alert = $this->add( new interf_alerte('info', false) );
      $alert->add( new interf_bal_smpl('b', 'Attention, ') );
      $alert->add_message( 'l\'objet sera détruit !' );
      $this->add( new interf_bal_smpl('span', 'Chance de succès : '.$chances.' %.', null, 'small') );
      $this->ajout_btn('Annuler', 'fermer');
      $this->ajout_btn('Enlever', '$(\'#modal\').modal(\'hide\');envoiInfo(\'inventaire.php?action=recup_gemme&objet'.$index.'\', \'information\');', 'primary');
    }
    else if( $niveau )
    {
      interf_dialogBS::__construct('Enchasser une gemme');
      /// Doit-on chercher une gemme ou un objet sloté ?
      $obj_txt = $objet->get_texte();
      $objs = array();
      if( $obj_txt['0'] == 'g' )
      {
        $cle = 's'.$niveau;
        for($i=0; $i<$G_place_inventaire; $i++)
        {
          $obj = $perso->get_inventaire_slot_partie($i);
          if( strpos($obj, $cle) !== false && $obj[0] != 'h' )
          {
            $objs[$i] = $obj;
          }
        }
        $type = 'un objet avec un slot de niveau '.$niveau;
        $var1 = 'gemme';
        $var2 = 'objet';
      }
      else
      {
        for($i=0; $i<$G_place_inventaire; $i++)
        {
          $obj = $perso->get_inventaire_slot_partie($i);
          if( $obj[0] == 'g' )
          {
            $o = objet_invent::factory($obj);
            if( $o->get_niveau() == $niveau )
              $objs[$i] = $obj;
          }
        }
        $type = 'une gemme de niveau '.$niveau;
        $var1 = 'objet';
        $var2 = 'gemme';
      }
      // on recherce
      if( count($objs) )
      {
        $this->add( new interf_txt('Choisissez '.$type.' pour l\'enchassement : ') );
        $form = $this->add( new interf_form('inventaire.php?action=enchasse&'.$var1.'='.$index, 'enchasser') );
        $choix = $form->add( new interf_select_form($var2, 'enchasser') );
        foreach( $objs as $i=>$o )
        {
          $obj = objet_invent::factory($o);
          $choix->add_option($obj->get_nom(), $i);
        }
        $this->add( new interf_bal_smpl('span', 'Chance de succès : '.$chances.' %.', null, 'small') );
        $this->ajout_btn('Annuler', 'fermer');
        $btn = $this->ajout_btn('Enchasser', '$(\'#modal\').modal(\'hide\');envoiFormulaire(\'enchasser\', \'information\');', 'primary');
        interf_base::code_js('ajout_filtre_form("enchasser");');
      }
      else
      {
        $this->add( new interf_alerte('danger', false) )->add_message('Vous devez avoir '.$type.' !');
        $this->ajout_btn('Ok', 'fermer', 'danger');
      }
    }
    else
    {
      interf_dialogBS::__construct('Erreur');
      /// TODO: loguer
      $this->add( new interf_alerte('danger', false) )->add_message('Objet invalide !');
      $this->ajout_btn('Ok', 'fermer', 'danger');
    }
  }
}
?>
