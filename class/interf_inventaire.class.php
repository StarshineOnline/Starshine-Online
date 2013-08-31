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
  protected $adresse;  ///< Adresse de la page.
  protected $slot;  ///< Slot à afficher.
  protected $onglets;  ///< Onglets principal.
  protected $onglets_slots;  ///< Onglets des slots.
  /**
   * Constructeur
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire.
   * @param $adresse    Adresse de la page
   * @param $slot       Slot à afficher
   */
  function __construct(&$perso, $adresse, $slot)
  {
    $this->perso = &$perso;
    $this->adresse = $adresse;
    $this->slot = $slot;
    $this->onglets = new interf_onglets();
    $this->onglets->add_onglet('Personnage', 'inventaire.php');
    $this->onglets->add_onglet('Créature', 'inventaire_pet.php');
    $this->add( $this->onglets );
  }
  
  /**
   * Affiche le contenu de l'inventaire
   * @param $type   'perso' ou 'pet'.
   * @param $modif   Indique si on peut modifier l'interface.
   */
  function set_contenu($type='perso', $modif=true)
  {
    $tbl = new interf_tableau();
    $this->onglets->add( $tbl );
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
    	if( ($compteur % 3) == 0 )
  		{
        $tr = $tbl->nouv_ligne();
        $tr->set_attribut('style', 'height : 55px;');
        //$tbl->add($tr);
  		}
  		$td = $tbl->nouv_cell(null, null, ($loc['type']!='vide')?'inventaire2':null);
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
    global $G_place_inventaire, $interf;
    $this->add( new interf_bal_smpl('p', 'Place restante dans l\'inventaire :'. ($G_place_inventaire - count($this->perso->get_inventaire_slot_partie())).' / '.$G_place_inventaire) );
    /*$this->onglets_slots = new interf_onglets('inventaire_slot');
    $this->onglets_slots->add_onglet('Utile', $this->adresse, $this->slot=='utile');
    $this->onglets_slots->add_onglet('Arme', $this->adresse, $this->slot=='arme');
    $this->onglets_slots->add_onglet('Armure', $this->adresse, $this->slot=='armure');
    $this->onglets_slots->add_onglet('Autre', $this->adresse, $this->slot=='autre');
    $this->add( $this->onglets_slots );*/
    // version temporaire
    $div = new interf_bal_cont('div', 'messagerie_menu');
    $this->add( $div );
    $span1 = new interf_bal_smpl('span', 'Utile', false, ($filtre == 'utile')?'seleted':false);
    $span1->set_attribut('onclick', 'envoiInfo(\'inventaire_slot.php?javascript=ok&amp;filtre=utile\', \'inventaire_slot\')');
    $div->add($span1);
    $span2 = new interf_bal_smpl('span', 'Arme', false, ($filtre == 'arme')?'seleted':false);
    $span2->set_attribut('onclick', 'envoiInfo(\'inventaire_slot.php?javascript=ok&amp;filtre=arme\', \'inventaire_slot\')');
    $div->add($span2);
    $span3 = new interf_bal_smpl('span', 'Armure', false, ($filtre == 'armure')?'seleted':false);
    $span3->set_attribut('onclick', 'envoiInfo(\'inventaire_slot.php?javascript=ok&amp;filtre=armure\', \'inventaire_slot\')');
    $div->add($span3);
    $span4 = new interf_bal_smpl('span', 'Autre', false, ($filtre == 'autre')?'seleted':false);
    $span4->set_attribut('onclick', 'envoiInfo(\'inventaire_slot.php?javascript=ok&amp;filtre=autre\', \'inventaire_slot\')');
    $div->add($span4);
    $slot = new interf_bal_cont('div', 'inventaire_slot');
    $slot->add( $interf->creer_inventaire_slot($this->perso, $this->adresse.'?filtre='.$this->slot.'&amp;', $this->slot, $modif)  );
    $this->add($slot);
  }
}
?>
