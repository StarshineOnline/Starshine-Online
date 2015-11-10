<?php
/**
 * @file interf_tribunal.class.php
 * Classes pour le tribunal
 */

/// Classe pour le tribunal
class interf_tribunal extends interf_ville
{
	function __construct(&$royaume)
	{
		global $db, $Gtrad;
		parent::__construct($royaume);
		$perso = joueur::get_perso();
		
		// Icone jauges
		$icone = $this->set_icone_centre('justice');
		$icone->set_tooltip('Tribunal');
		// TODO : utiliser objets
		$requete = 'SELECT max(crime) FROM perso WHERE statut = "actif"';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$this->set_jauge_ext($perso->get_crime(), $row[0], 'mp'/*, 'Vos points crimes : '*/);
		$requete = 'SELECT count(*) FROM amende WHERE id_royaume='.$royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$max = $row[0];
		$requete = 'SELECT count(*) FROM amende';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$this->set_jauge_int($row[0], $max, 'pa', 'Proportion de concitoyens : ');
		
		// titre & alertes
		$this->centre->add( new interf_bal_smpl('p', 'Liste des bandits & criminels') );
		/// @todo ajuster la taille des alertes
		$nbr_alertes = interf_alerte::aff_enregistres($this->centre);
		// criminels
		$tbl = $this->centre->add( new interf_lst_criminels($royaume, 383 - $nbr_alertes * 30) );
	}
}

class interf_lst_criminels extends interf_data_tbl
{
	function __construct(&$royaume, $hauteur)
	{
		global $Gtrad, $db;
		parent::__construct('criminels', '', false, false, $hauteur, 4);
		$this->nouv_cell('Nom');
		//$this->nouv_cell('Race');
		$this->nouv_cell('Statut');
		$this->nouv_cell('Points de crime');
		$this->nouv_cell('Prime');
		$this->nouv_cell('&nbsp');
		
		/// @todo passer par un objet
		$requete = 'SELECT * FROM perso RIGHT JOIN amende ON amende.id_joueur = perso.id WHERE perso.amende > 0 AND amende.statut IN ("bandit", "criminel") AND race = "'.$royaume->get_race().'"';
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->nouv_ligne();
			$this->nouv_cell( $row['nom'] );
			//$this->nouv_cell( $Gtrad[$row['race']] );
			$this->nouv_cell( $row['statut'] );
			$this->nouv_cell( $row['crime'] );
			$this->nouv_cell( $row['prime'] );
			$this->nouv_cell( new interf_lien('ajouter', 'tribunal.php?action=prime&id='.$row['id']) );
		}
	}
}

/**
 * Boite de dialogue pour la mise d'une prime
 */
class interf_prime extends interf_dialogBS
{
  function __construct($id, &$cible)
  {
  	interf_dialogBS::__construct('Prime');
		/// @todo passer par un objet
    $this->add( new interf_bal_smpl('p', 'Cible : '.$cible) );
    $form = $this->add( new interf_form('tribunal.php?action=prime&id='.$id, 'prime') );
		$form->set_attribut('name', 'formulaire');
		$chp = $form->add_champ_bs('number', 'star', null, 1, 'Stars');
    $chp->set_attribut('min', 1);
    $chp->set_attribut('max', joueur::get_perso()->get_star());
    $chp->set_attribut('step', 1);
    
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Valider', '$(\'#modal\').modal(\'hide\');charger_formulaire(\'prime\');', 'primary');
  }
}

?>