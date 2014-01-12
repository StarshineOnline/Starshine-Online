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
    $this->onglets->add_onglet('Personnage', 'inventaire.php?action=princ&page=perso', 'perso', $invent=='perso');
    $this->onglets->add_onglet('Créature', 'inventaire.php?action=princ&page=pet', 'pet', $invent=='pet');
    $this->onglets->add_onglet('Actions', 'inventaire.php?action=princ&page=actions', 'actions', $invent=='actions');
    // un peu d'espace
    //$this->add( new interf_bal_smpl('div', '', false, 'spacer') );
    $this->add( new interf_bal_smpl('br') );
    // onglets des slots
    $this->onglets_slots = $this->add( new interf_onglets('onglets_sots', 'slots') );
    $this->onglets_slots->add_onglet('Utile', 'inventaire.php?action=sac&slot=utile', 'utile', $slot=='utile');
    $this->onglets_slots->add_onglet('Equipement', 'inventaire.php?action=sac&slot=equipement', 'equip', $slot=='equip');
    $this->onglets_slots->add_onglet('Royaume', 'inventaire.php?action=sac&slot=royaume', 'royaume', $slot=='royaume');
    $this->onglets_slots->add_onglet('Artisanat', 'inventaire.php?action=sac&slot=artisanat', 'royaume', $slot=='royaume');
  }
  
  /**
   * Affiche le contenu de l'inventaire
   * @param $type   'perso' ou 'pet'.
   * @param $modif   Indique si on peut modifier l'interface.
   */
  function set_contenu($type='perso', $modif=true)
  {
    $this->onglets->add( new interf_invent_equip($this->perso, $type, $modif) );
  }
  
  //
  function affiche_slots($modif=true)
  {
    $this->onglets_slots->add( new interf_invent_sac($this->perso, $this->slot) );
  }
}

class interf_invent_equip extends interf_tableau
{
  function __construct(&$perso, $type)
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
                              'petit_accessoire',   'chaussure',  'petit_accessoire');
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
    		}
        $td->add( new interf_objet_invent($obj, $desequip, $loc, $this->slot, 'drop_'.$loc) );
      }
      $compteur++;
      interf_base::code_js( '$( "#drop_'.$loc.'" ).droppable({accept: ".drag_'.$loc.'", activeClass: "invent_cible", hoverClass: "invent_hover", drop: drop_func});' );
      //interf_base::code_js( '$( "#drop_'.$loc.'" ).droppable({accept: "#invent_slot12", activeClass: "invent_cible", hoverClass: "invent_hover", drop: drop_func);' );
    }
    //interf_base::code_js( 'init_dragndrop();' );
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
      //$js = 'function init_dragndrop() {';
    	foreach($perso->get_inventaire_slot_partie() as $invent)
    	{
        $objet = objet_invent::factory($invent);
        $col = $objet->get_colone($type);
        if( $col !== false )
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
          $div = $this->cols[$col]->add( new interf_objet_invent($objet, false, null, $drags, 'invent_slot'.$i) );
          if( $objet->est_identifie() )
            $div->set_attribut('onclick', 'chargerPopover(\'invent_slot'.$i.'\', \'infos_'.$i.'\', \'left\', \''.'inventaire.php?action=infos&id='.$invent.'\', \''.$objet->get_nom().'\')');
          /*if( $type == 'equipement' )
            $js .= 'dragndrop("#invent_slot'.$i.'", "#drop_'.$objet->get_emplacement().'", "inventaire.php");';
          if( is_ville($perso->get_x(), $perso->get_y()) == 1 )
          {
            if( $type == 'royaume' )
              $js .= 'dragndrop("#invent_slot'.$i.'", "#drop_depot", "inventaire.php");';
            else
            {
              $js .= 'dragndrop("#invent_slot'.$i.'", "#drop_vendre_marchand", "inventaire.php");';
              $js .= 'dragndrop("#invent_slot'.$i.'", "#drop_hotel_vente", "inventaire.php");';
            }
          }*/
          interf_base::code_js( '$( "#invent_slot'.$i.'" ).draggable({ helper: "original", tolerance: "touch", revert: "invalid" });' );
        }
        $i++;
      }
      /*$script = $this->add( new interf_bal_smpl('script', $js.'} init_dragndrop();') );
      $script->set_attribut('type', 'text/javascript');*/
    }
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
    $btn = $this->ajout_btn('Vendre', '', 'primary');
    $btn->set_attribut('onclick', '');

    $objet =  objet_invent::factory( $perso->get_inventaire_slot_partie($index) );
    $prix = $objet->get_prix_vente() * 2;
		$prixmax = $prix * 10;
    $case = new map_case( $perso->get_pos() );
    $R = new royaume( $case->get_royaume() );

    $taxe = $R->get_taxe_diplo($perso->get_race()) / 100;
    $form = $this->add( new interf_form('javascript:envoiInfo(\'inventaire.php\', \'information\');', 'get') );
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
  }
}
?>
