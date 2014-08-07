<?php
/**
 * @file interf_terrain_chantier.class.php
 * Classes pour le quartier général
 */
 

/// Classe pour le quartier général
class interf_terrain_chantier extends interf_ville
{
	protected $perso;
	protected $liste;
	protected $points = 0;
	protected $max_points = 0;
	function __construct(&$royaume)
	{
		global $db;
		parent::__construct($royaume);
		$this->perso = joueur::get_perso();
		// Icone & jauge extérieure
		$icone = $this->set_icone_centre('architecture');
		$icone->set_tooltip('Bâtiments en chantier');
		/// TODO: passer à l'objet
		$requete = 'SELECT count(*) FROM terrain LEFT JOIN perso ON terrain.id_joueur = perso.ID WHERE perso.race = "'.$royaume->get_race().'"';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$tot = $row[0];
		$requete = 'SELECT count(*) FROM terrain_chantier LEFT JOIN terrain ON terrain.id = terrain_chantier.id_terrain LEFT JOIN perso ON terrain.id_joueur = perso.ID WHERE perso.race = "'.$royaume->get_race().'"';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$this->set_jauge_ext($row[0], $tot, 'pa', 'Terrains en construction : ');
		
		//
		$this->centre->add( new interf_bal_smpl('p', 'Liste des chantiers disponibles') );
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		interf_alerte::aff_enregistres($div);
		$this->liste = $div->add( new interf_bal_cont('ul') );
		/// TODO: passer à l'objet
		$requete = 'SELECT terrain_chantier.id as id, id_terrain, id_batiment, point, star_point FROM terrain_chantier LEFT JOIN terrain ON terrain.id = terrain_chantier.id_terrain LEFT JOIN perso ON terrain.id_joueur = perso.ID WHERE perso.race = "'.$royaume->get_race().'" ORDER BY star_point DESC';
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->aff_chantier( new terrain_chantier($row) );
		}
		// Jauge intérieure
		$this->set_jauge_int($this->points, $this->max_points, 'avance', 'Avancement des travaux : ');
	}
	function aff_chantier(&$chantier)
	{
		$batiment = $chantier->get_batiment();
		$taxe = floor(($chantier->star_point * 100) * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
		$prix = ($chantier->star_point * 100) - $taxe;
		$this->points += $chantier->point;
		$this->max_points += $batiment->point_structure;
		// affichage
		$li = $this->liste->add( new interf_bal_cont('li', false, 'info_case') );
		if( $this->perso->get_pa() >= 10 )
		{
			$construire = $li->add( new interf_lien_cont('terrain_chantier.php?action=construire&id='.$chantier->get_id()) );
			$construire->add( new interf_bal_smpl('span', '', false, 'icone icone-architecture') );
			$construire->add( new interf_bal_smpl('span', '10 PA', false, 'xsmall') );		
			$construire->set_tooltip('Constuire (10 PA)');
		}
		/// TODO: revoir objets
		$hp = $li->add( new interf_jauge_bulle('Points de construction', $chantier->point, $batiment->point_structure, false, 'avance', false, 'jauge_case') );
		$li->add( new interf_bal_smpl('span', ucwords($batiment->type)) );
		$li->add( new interf_bal_smpl('span', $prix.' stars par 100 point', false, 'xsmall') );
	}
}

?>