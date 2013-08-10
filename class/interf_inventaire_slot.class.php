<?php
/// @addtogroup Interface
/**
 * @file interf_inventaire_slot.class.php
 * Gestion de l'affichage des slots de l'inventaire
 */

/**
 * Classe gérant l'affichage des slots de l'inventaire
 */
class interf_inventaire_slot extends interf_bal_cont
{
  protected $perso;  ///< Objet représentant le personnage dont il faut afficher l'inventaire.
  protected $adresse;  ///< Adresse de la page.
  protected $slot;  ///< Slot à afficher.
  /**
   * Constructeur
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire.
   * @param $adresse    Adresse de la page
   * @param $slot       Slot à afficher
   * @param $modif      indique si on peut modifier l'inventaire
   */
  function __construct(&$perso, $adresse, $slot, $modif)
  {
  return;
    global $db, $filtre_url, $W_row, $G_taux_vente;
    interf_bal_cont::__construct('ul');
    $this->perso = &$perso;
    $this->adresse = $adresse;
    $this->slot = $slot;
    
    if($this->perso->get_inventaire_slot() != '')
    {
      $i = 0;
      $arme_de_siege = 0;
    	$this->perso->restack_objet();
    	foreach($this->perso->get_inventaire_slot_partie() as $invent)
    	{
    		if($invent !== 0 AND $invent != '')
    		{
    			$objet_d = decompose_objet($invent);
    			//echo '<!-- '; var_dump($objet_d); echo '-->';
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
    			}
    			else
    			{
    				$row['nom'] = 'Objet non-identifié';
    			}
    			//Filtrage
    			if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre']; else $filtre = 'utile';
    			$check = false;
    			$liste_categorie = array('o', 'a', 'p', 'l');
    			if((($objet_d['categorie'] == 'o' AND $filtre == 'utile')
    					OR ($objet_d['categorie'] == 'l' AND $filtre == 'utile')
    					OR ($objet_d['categorie'] == 'a' AND $filtre == 'arme')
    					OR ($objet_d['categorie'] == 'p' AND $filtre == 'armure'))
    				 AND $objet_d['identifier'])
    			{
    				$check = true;
    			}
    			elseif(!in_array($objet_d['categorie'], $liste_categorie) AND $filtre == 'autre') $check = true;
    			if($check)
    			{
    			  if ($objet_d['identifier'])
    				  $echo = description_objet($invent);
    				else
    					$echo = 'Objet non indentifié';
    			
            $li = $this->add( new interf_bal_cont('li') );
            $li->set_attribut('onmouseover', 'return '.make_overlib($echo));
            $li->set_attribut('onmouseout', 'return nd();');
            $span = $li->add(new interf_bal_cont('span', false, 'inventaire_span') );
            $span->set_attribut('style', 'width:150px');
            $span->add( new interf_txt($row['nom']) );
      			$modif_prix = 1;
      			if($objet_d['stack'] > 1)
              $span->add( new interf_txt(' X '.$objet_d['stack']) );
      			if($objet_d['slot'] > 0)
      			{
              $span->add( new interf_bal_smpl('span', 'Slot niveau '.$objet_d['slot'], false, 'xsmall') );
      				$modif_prix = 1 + ($objet_d['slot'] / 5);
      			}
      			if($objet_d['slot'] == '0')
      			{
              $span->add( new interf_bal_smpl('span', 'Slot impossible', false, 'xsmall') );
      				$modif_prix = 0.9;
      			}
      			if($objet_d['enchantement'] > '0')
      			{
      				$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['enchantement'];
      				$req = $db->query($requete);
      				$row_e = $db->read_assoc($req);
      				$modif_prix = 1 + ($row_e['niveau'] / 2);
              $span->add( new interf_bal_smpl('span', 'Enchantement de '.$row_e['enchantement_nom'], false, 'xsmall') );
      			}
      			if($objet_d['identifier'] && $modif)
      			{
      				if($objet_d['categorie'] == 'g')
      				{
                $mod = new interf_bal_cont('span', false, 'inventaire_span');
                $mod->set_attribut('style', 'width:60px');
                $li->add($mod);
                $lien = new interf_bal_smpl('a', 'Enchasser', false, 'inventaire_span');
                $lien->set_attribut('href', $this->adresse.'action=enchasse&amp;key_slot='.$i);
                $lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod->add($lien);
                $mod->add( new interf_bal_smpl('span', '(20 PA)', false, 'xsmall') );
      				}
      				elseif($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm')
      				{
                $mod = new interf_bal_cont('span', false, 'inventaire_span');
                $mod->set_attribut('style', 'width:60px');
                $li->add($mod);
                $lien = new interf_bal_smpl('a', 'Equiper', false, 'inventaire_span');
                $lien->set_attribut('href', $this->adresse.'action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie']);
                $lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod->add($lien);
      				}
      				elseif($objet_d['categorie'] == 'd')
      				{
                $mod = new interf_bal_cont('span', false, 'inventaire_span');
                $mod->set_attribut('style', 'width:60px');
                $li->add($mod);
                $lien = new interf_bal_smpl('a', 'Equiper', false, 'inventaire_span');
                $lien->set_attribut('href', 'inventaire_pet.php?action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie'].$filtre_url);
                $lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod->add($lien);
      				}
      				elseif($objet_d['categorie'] == 'o' OR $objet_d['categorie'] == 'r')
      				{
      					if($row['utilisable'] == 'y')
      					{
                  $mod = new interf_bal_cont('span', false, 'inventaire_span');
                  $mod->set_attribut('style', 'width:60px');
                  $li->add($mod);
                  $lien = new interf_bal_smpl('a', 'Utiliser', false, 'inventaire_span');
                  $lien->set_attribut('href', $this->adresse.'action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i);
                  $lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                  $mod->add($lien);
                }
      					if($W_row['type'] == 1 AND $objet_d['categorie'] == 'r')
      					{
                  $mod5 = new interf_bal_cont('span', false, 'inventaire_span');
                  //$mod5->set_attribut('style', 'width:60px');
                  $li->add($mod5);
                  $lien5 = new interf_bal_smpl('a', 'Déposer au dépot', false, 'inventaire_span');
                  $lien5->set_attribut('href', $this->adresse.'action=depot&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i);
                  $lien5->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                  $mod5->add($lien5);
                }
      				}
      				elseif($objet_d['categorie'] == 'l')
      				{
                $mod = new interf_bal_cont('span', false, 'inventaire_span');
                $mod->set_attribut('style', 'width:50px');
                $li->add($mod);
                $lien = new interf_bal_smpl('a', 'Lire', false, 'inventaire_span');
                $lien->set_attribut('href', $this->adresse.'action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type=grimoire&amp;key_slot='.$i);
                $lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod->add($lien);
      				}
      				if ($W_row['type'] == 1 AND $objet_d['categorie'] != 'r' AND $objet_d['categorie'] != 'h')
      				{
      					$prix = floor($row['prix'] * $modif_prix / $G_taux_vente);
                $mod2 = new interf_bal_cont('span', false, 'inventaire_span');
                $mod2->set_attribut('style', 'width:100px');
                $li->add($mod2);
                $lien2 = new interf_bal_smpl('a', 'Vendre '.$prix.' Stars', false, 'inventaire_span');
                $lien2->set_attribut('href', $this->adresse.'action=vente&amp;id_objet='.$objet_d['id'].'&amp;key_slot='.$i);
                $lien2->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod2->add($lien2);
                $mod2->add( new interf_txt(' / ') );
                $lien3 = new interf_bal_smpl('a', 'Hotel des ventes', false, 'inventaire_span');
                $lien3->set_attribut('href', $this->adresse.'action=ventehotel&amp;id_objet='.$objet_d['categorie'].$objet_d['id_objet'].'&amp;key_slot='.$i);
                $lien3->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod2->add($lien3);
      				}
      				if(($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm') AND $objet_d['slot'] == '' AND $objet_d['enchantement'] == '')
      				{
                $mod3 = new interf_bal_cont('span', false, 'inventaire_span');
                $mod3->set_attribut('style', 'width:120px');
                $li->add($mod3);
                $lien4 = new interf_bal_smpl('a', 'Mettre un slot', false, 'inventaire_span');
                $lien4->set_attribut('href', $this->adresse.'action=slot&amp;key_slot='.$i);
                $lien4->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
                $mod3->add($lien4);
      				}
      			}
            unset($span, $li, $mod, $lien, $mod2, $mod3, $mod5, $lien2, $lien3, $lien4, $lien5);
    			}
    			$i++;
    		}
    	}
    }
  }
}
?>
