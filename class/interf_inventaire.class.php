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
    $script = new interf_bal_smpl('script');
    $script->set_attribut('type', 'text/javascript');
    $script->set_attribut('src', './javascript/inventaire.js');
    $this->add($script);
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
    $cont = $this->add( new interf_bal_cont('div', 'inventaire_slot') );
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
  			  $objet_d = decompose_objet($invent);
    			if($objet_d['identifier'])
    			{
    				switch ($objet_d['categorie'])
    				{
    					//Si c'est une arme
    					case 'a' :
    						$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$image = 'image/arme/arme'.$row['id'].'.png';
    						$mains = explode(';', $row['mains']);
    						$partie = $mains[0];
    					break;
    					//Si c'est un objet de pet
    					case 'd' :
    						$requete = "SELECT * FROM objet_pet WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    					break;
    					//Si c'est une protection
    					case 'p' :
    						$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    						$image = 'image/armure/'.$partie.'/'.$partie.$row['id'].'.png';
    					break;
    					case 'o' :
    						$requete = "SELECT * FROM objet WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    					break;
    					case 'g' :
    						$requete = "SELECT * FROM gemme WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    						$row['prix'] = pow(10, $row['niveau']) * 10;
    					break;
    					case 'r' :
    						$requete = "SELECT * FROM objet_royaume WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    						$row['utilisable'] = 'y';

    						if($row['type'] == "arme_de_siege")
    							$arme_de_siege++;
    					break;
    					case 'm' :
    						$requete = "SELECT * FROM accessoire WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = 'accessoire';
    						$image = 'image/accessoire/accessoire'.$row['id'].'.png';
    						$row['utilisable'] = 'n';
    					break;
    					case 'l' :
    						$requete = "SELECT * FROM grimoire WHERE ID = ".$objet_d['id_objet'];
    						//Récupération des infos de l'objet
    						$req = $db->query($requete);
    						$row = $db->read_array($req);
    						$partie = $row['type'];
    						$row['utilisable'] = 'y';
    					break;
    				}
            $nom = $row['nom'];
    			}
          else
            $nom = 'Objet non indentifié';
  			  if ($objet_d['identifier'])
  				  $echo = description_objet($invent);
  				else
  					$echo = 'Objet non indentifié';
    			if($objet_d['stack'] > 1)
            $nom .= ' X '.$objet_d['stack'];
          /*$p = $cont->add( new interf_bal_cont('p') );
          $p->set_attribut('style', 'width:400px;');
          $span1 = $p->add( new interf_bal_cont('span') );
          $span1->set_attribut('name', 'overlib');
          $span1->set_attribut('onmouseover', 'return '.make_overlib($echo));
          $span1->set_attribut('onmouseout', 'return nd();');
          $span2 = $span1->add( new interf_bal_cont('span', $objet_d['id'], 'drag_'.$partie.' ui-draggable') );
          $span2->set_attribut('style', 'position: relative; display:block;');
          $img = $span2->add( new  interf_bal_smpl('img') );*/
          $div = $cont->add( new interf_bal_cont('div', /*$objet_d['id']*/'invent_slot'.$i, 'drag_'.$partie) );
          $div->set_attribut('style', 'width:33%;position: relative;');
          $img = $div->add( new  interf_bal_smpl('img') );
          $img->set_attribut('src', $image);
          $span1 = $div->add( new interf_bal_smpl('span', $nom) );
          $span1->set_attribut('name', 'overlib');
          $span1->set_attribut('onmouseover', 'return '.make_overlib($echo));
          $span1->set_attribut('onmouseout', 'return nd();');
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
?>
