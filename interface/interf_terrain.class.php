<?php
/**
 * @file interf_terrain.class.php
 * Classes pour le quartier général
 */
include_once(root.'interface/interf_ville.class.php');

/// Classe pour le quartier général
class interf_terrain extends interf_ville_onglets
{
	function __construct(&$royaume, $id=0)
	{
		global $db, $Gtrad;
		parent::__construct($royaume);
		$perso = joueur::get_perso();
		// Icone
		$icone = $this->set_icone_centre('ville5');
		$icone->set_tooltip('Votre terrain');
		$utilisation = 0;
		
		$terrain = terrain::recoverByIdJoueur($perso->get_id());
		$constructions = $terrain->get_constructions();
		$chantiers = $terrain->get_chantiers();
		
		$types = array();
		$ids = array();
		$ids_constr = array();
		// Chantiers
		/// TODO: centraliser la taille max (ou la lire dans la bdd ?)
		$aggrandissement = $terrain->nb_case < 5;
		if( count($chantiers) )
		{
			$liste = new interf_bal_cont('ul');
			foreach($chantiers as $chantier)
			{
				$batiment = $chantier->get_batiment();
				if($batiment->type == 'agrandissement')
					$aggrandissement = false;
				$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
				$li->add( new interf_jauge_bulle('Avancement', $chantier->point, $batiment->point_structure, false, 'avance', false, 'jauge_case') );
				$li->add( new interf_bal_smpl('span', $batiment->nom) );
				$li->add( new interf_bal_smpl('span', $chantier->star_point.' stars par point', false, 'xsmall') );
				$types[] = '"'.$batiment->type.'"';
				$ids[] = $batiment->id;
			}
		}
		else
			$liste = false;
		// Constructions
		/// TODO: sélectionner un onglet par défaut quand il n'y a pas d'onglet "chantiers"
		foreach($constructions as $construction)
		{
			$batiment = $construction->get_batiment();
			$types[] = '"'.$batiment->type.'"';
			$ids_constr[] = $batiment->id;
			$ids[] = $batiment->id;
			switch($batiment->type)//$utilisation = 0;
			{
			case 'coffre':
				$coffre = new coffre($construction->id);
				$utilisation += count($coffre->get_coffre_inventaire()) / $batiment->effet * $batiment->nb_case;
				break;
			case 'laboratoire':
				/// TODO: à faire
				break;
			case 'ecurie':
				$utilisation += $this->perso->nb_pet_ecurie_self() / $batiment->effet * $batiment->nb_case;
				break;
			case 'grenier':
				/// TODO: à faire
				break;
			}
			if( $batiment->type == 'ecurie' )
				continue;
			$div = $this->onglets->add_onglet(ucwords($batiment->type), 'terrain.php?ajax=2&action=onglet&id='.$construction->id, 'tab_'.$batiment->type, 'ecole_mag', $id==$construction->id);
			if( $id == $construction->id )
			{
				switch($batiment->type)
				{
				case 'coffre':
					$div->add( new interf_coffre($royaume, $construction) );
					break;
				case 'laboratoire':
					$div->add( new interf_laboratoire($royaume, $construction) );
					break;
				case 'grenier':
					$div->add( new interf_grenier($royaume, $construction) );
					break;
				}
			}
		}
		// Améliorations possibles
		/// TODO: passer à l'objet
		$types = implode(', ', $types);
		$not_in_types = $implode_types ? 'AND type NOT IN ('.$types.')' : '';
		$ids_constr = implode(', ', $ids_constr);
		$or_in = $ids_constr ? 'OR (requis IN ('.$ids_constr.') AND type != "agrandissement" )' : '';
		$ids = implode(', ', $ids);
		$not_in_ids = $ids ? ' AND id NOT IN ('.$ids.')' : '';
		$requete = 'SELECT id, nom, point_structure FROM terrain_batiment WHERE ( (requis = 0 '.$not_in.' AND nb_case <= '.$terrain->place_restante().') '.$or_in.' OR ( type = "agrandissement" AND requis='.$terrain->nb_case.') )'.$not_in_ids;
		$req = $db->query($requete);
		$n_chant = $db->num_rows;
		// Chantier
		if($n_chant || $aggrandissement || $liste )
		{
			$div_ch = $this->onglets->add_onglet('Chantiers', '', 'tab_chantiers', 'ecole_mag', !$id);
			if( !$id )
			interf_alerte::aff_enregistres($div_ch);
			// agrandissement
			$div_ch->add( new interf_bal_smpl('p', 'Place restante : '.$terrain->place_restante().' / '.$terrain->nb_case) );
			if( $aggrandissement )
			{
				/// TODO: passer à l'objet
				$requete = "SELECT id, point_structure FROM terrain_batiment WHERE type = 'agrandissement' AND requis = ".$terrain->nb_case;
				$req2 = $db->query($requete);
				$row = $db->read_assoc($req);
				$div_ch->add( new interf_lien('Agrandir', 'terrrain?action=aggrandir', false, 'btn btn-primary') );
			}
			// Constructions en cours
			if( $liste )
			{
				$div_ch->add( new interf_bal_smpl('h4', 'Liste des batiments en construction :') );
				$div_ch->add( $liste );
			}
			// nouveau chantier
			if( $n_chant > 0 )
			{
				/// TODO: faire dépendre le max 
				$div_ch->add( new interf_bal_smpl('h4', 'Nouveau chantier :') );
				$form = $div_ch->add( new interf_form('terrrain?action=chantier', 'nouv_chantier') );
				$div_sel = $form->add( new interf_bal_cont('div', false, 'input-group') );
				$div_sel->add( new interf_bal_smpl('span', 'Construire', false, 'input-group-addon') );
				$sel = $div_sel->add( new interf_select_form('batiment', false, false, 'form-control') );
				/// TODO: ne sélectionner que ceux qui sont constructibles avec les stars disponibles
				while($row = $db->read_assoc($req))
				{
					$sel->add_option($row['nom'].' ('.$row['point_structure'].' points de structure)', $row['id']);
				}
				/// TODO: faire dépendre le max des stars du personnage et du nombre de points de structure
		    $chp1 = $form->add_champ_bs('number', 'stars', null, '0', 'Rémunéreration des travailleurs', 'stars / point');
		    $chp1->set_attribut('min', 1);
		    $chp1->set_attribut('step', 1);
		    $btn = $form->add( new interf_chp_form('submit', false, false, 'Construire', null, 'btn btn-primary') );
		    $btn->set_attribut('onclick', 'charger_formulaire(\'nouv_chantier\');');
			}
		}
		// jauge extérieure
		$this->set_jauge_ext($terrain->nb_case, 5, 'avance', 'Taille de la maison : ');
		$this->set_jauge_int(round($utilisation*100, 1), $terrain->nb_case * 100, 'pa', 'Utilisation : ');
	}
}

class interf_coffre extends interf_cont
{
	protected $id;
	protected $perso;
	function __construct(&$royaume, $construction)
	{
		$this->perso = joueur::get_perso();
		$this->id = $construction->id;
		$batiment = $construction->get_batiment();
		$coffre = new coffre($construction->id);
		$coffre_inventaire = $coffre->get_coffre_inventaire();
		interf_alerte::aff_enregistres($this);
		$div = $this->add( new interf_bal_cont('div') );
		// Objets dans le coffre
		$div_coffre = $div->add( new interf_bal_cont('div', 'coffre') );
		$div_coffre->add( new interf_bal_smpl('h6', 'Coffre ('.count($coffre_inventaire).' / '.$batiment->effet.')') );
		$liste_coffre = $div_coffre->add( new interf_bal_cont('ul') );
		foreach($coffre_inventaire as $index => $objet)
		{
			$this->aff_objet($liste_coffre, $index, $objet->objet, true);
		}
		// Inventaire
		$inventaire = $this->perso->get_inventaire_slot_partie();
		$div_invent = $div->add( new interf_bal_cont('div', 'coffre_invent') );
		$div_invent->add( new interf_bal_smpl('h6', 'Inventaire ('.count($inventaire).' / 20)') );
		$liste_invent = $div_invent->add( new interf_bal_cont('ul') );
		foreach($inventaire as $index => $objet)
		{
			$this->aff_objet($liste_invent, $index, $objet, false);
		}
	}
	function aff_objet(&$liste, $index, $objet, $coffre)
	{
		/// TODO: possibilité de ne transférer qu'une partie d'un stack
		$obj = objet_invent::factory($objet);
		$li = $liste->add( new interf_bal_cont('li') );
		$url = 'terrain.php?id='.$this->id.'&action='.($coffre?'prendre':'deposer').'&index='.$index;
		$li->add( new interf_lien('', $url, false, 'icone icone-'.($coffre?'droite':'gauche')) );
		$nom = $obj->est_identifie() ? $obj->get_nom() : 'Objet non indentifié';
		if($obj->get_nombre() > 1)
			$nom .= ' x '.$obj->get_nombre();
		$id = ($coffre?'coffre_':'invent_').$index;
		$lien = $li->add( new interf_bal_smpl('a', $nom, $id) );
		$lien->set_attribut('onclick', 'chargerPopover(\''.$id.'\', \'infos_'.$id.'\', \'right\', \'inventaire.php?action=infos&id='.$objet.'\', \''.$nom.'\')');
	}
}

class interf_laboratoire extends interf_cont
{
	protected $id;
	protected $perso;
	function __construct(&$royaume, $construction)
	{
		global $db;
		$this->perso = joueur::get_perso();
		$this->id = $construction->id;
		$types = array();
		//on cherche si il a des instruments
		/// TODO: passer à l'objet
		$requete = "SELECT id, id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE id_laboratoire = ".$construction->id;
		$req = $db->query($requete);
		interf_alerte::aff_enregistres($this);
		$div = $this->add( new interf_bal_cont('div', 'instruments') );;
		$div_instr = $div->add( new interf_bal_cont('div', 'instruments') );
		$div_instr->add( new interf_bal_smpl('h6', 'Vos instruments') );
		$liste_instr = $div_instr->add( new interf_bal_cont('ul') );
		while($row = $db->read_assoc($req))
		{
			$types[] = '"'.$row['type'].'"';
			$instrument = new terrain_laboratoire($row);
			$instru = $instrument->get_instrument();
			$requete = "SELECT id, nom, prix FROM craft_instrument WHERE requis = ".$instrument->id_instrument;
			$req_i = $db->query($requete);
			if($db->num_rows > 0)
			{
				$taxe = 1 + ($royaume->get_taxe_diplo($this->perso->get_race()) / 100);
				$row_i = $db->read_assoc($req_i);
				$prix = round($row_i['prix'] * $taxe);
				$id = $row_i['id'];
			}
			else
				$prix = $id = false;
			$this->aff_instrument($liste_instr, $instru->nom, $prix, $id, false);
		}
		$types = implode(', ', $types);
		$not_in = $types ? ' AND type NOT IN ('.$types.')' : '';
		$requete = "SELECT id, nom, prix FROM craft_instrument WHERE requis = 0".$not_in;
		$req = $db->query($requete);
		if( $db->num_rows )
		{
			$div_achat = $div->add( new interf_bal_cont('div', 'achat') );
			$div_achat->add( new interf_bal_smpl('h6', 'Acheter') );
			$liste_achat = $div_achat->add( new interf_bal_cont('ul') );
			$taxe = 1 + ($royaume->get_taxe_diplo($this->perso->get_race()) / 100);
			while($row = $db->read_assoc($req))
			{
				$prix = round($row['prix'] * $taxe);
				$this->aff_instrument($liste_achat, $row['nom'], $prix, $row['id'], true);
			}
		}
	}
	function aff_instrument(&$liste, $nom, $prix, $id, $achat)
	{
		$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
		if($prix)
		{
			$lien = $li->add( new interf_lien_cont('terrain.php?id='.$this->id.'&action='.($achat?'achat':'ameliore').'_instr&id_instr='.$id) );
			$lien->add( new interf_bal_smpl('span', '', false, 'icone icone-'.($achat?'argent':'haut')) );
			$lien->add( new interf_bal_smpl('span', $prix, false, 'xsmall') );
			$lien->set_tooltip(($achat?'Acheter ':'Améliorer').' pour '.$prix.' stars');
		}
		$li->add( new interf_bal_smpl('span', $nom) );
	}
}

class interf_grenier extends interf_cont
{
	protected $id;
	protected $perso;
	function __construct(&$royaume, $construction)
	{
		/// TODO: à faire
		/*
					if(array_key_exists('famine', $_GET))
					{
						if($joueur->get_pa() >= 10)
						{
							$check = false;
							foreach($joueur['debuff'] as $key => $debuff)
							{
								if($debuff['type'] == 'famine')
								{
									$check = true;
									$id_buff = $debuff['id'];
									$key_debuff = $key;
								}
							}
							if($check)
							{
								//Si effet = 1 on supprime le debuff
								if($joueur['debuff'][$key_debuff]['effet'] <= 1)
								{
									$requete = "DELETE FROM buff WHERE id = ".$id_buff;
								}
								//Sinon on réduit
								else
								{
									$requete = "UPDATE buff SET effet = effet - 1 WHERE id = ".$id_buff;
								}
								$db->query($requete);
								$requete = "UPDATE perso SET pa = pa - 10 WHERE ID = ".$joueur->get_id();
								$db->query($requete);
								refresh_perso();
								echo '<h6>Famine réduite de 1%</h6>';
							}
							echo '<h5>Vous n\'avez pas de famine</h5>';
						}
						else
						{
							echo '<h5>Vous n\'avez pas assez de PA</h5>';
						}
					}
					echo '<a href="terrain.php?id_construction='.$construction->id.'&amp;famine" onclick="return envoiInfo(this.href, \'carte\');">Réduire de 1% la famine (10 PA)</a>';
		*/
	}
}


?>