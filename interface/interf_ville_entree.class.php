<?php
/**
 * @file interf_ville_entree.class.php
 * Entrée dans la ville 
 */

/// Classe de base pour l'interface de l'entrée dans la ville
class interf_ville_entree_base extends interf_gauche
{
	protected $royaume;
	protected $message;
	protected $ville;
	
	function __construct(&$royaume)
	{
		$this->royaume = &$royaume;
		parent::__construct('carte');
		// Icone et titre
		$this->set_icone_centre('ville');
		$this->barre_haut->add( new interf_txt($royaume->get_nom()) );
		// Jauges
		$this->set_jauge_ext($royaume->get_capitale_hp (), 50000, 'hp', 'HP : ');
		
		// Ligne pour le message
		$this->message = $this->centre->add( new interf_bal_cont('p') ); 
		/// TODO: à améliorer
		$this->message->add( new interf_txt('&nbsp;') );
		
		// Cadre principal
		$this->ville = $this->centre->add( new interf_bal_cont('div', 'ville') );
		$this->ville->set_attribut('style', 'background: url(\'image/ville/ville_'.$royaume->get_race().'.png\')');
	}
}

// Classe gérant l'interface de l'entrée dans la ville
class interf_ville_entree extends interf_ville_entree_base
{
	function __construct(&$royaume)
	{
		$perso = joueur::get_perso();
		parent::__construct($royaume);
		
		// Dernier pobjet mis en vente à l'Hôtel des ventes
		$this->aff_vente();
		
		// Quartier marchand
		if($royaume->get_id() != 0)
		{// Si ca n'est pas en royaume neutre, on peut acheter (nécessaire ?)
			$quartier_marchand = array
			(
				'Forgeron' => 'boutique.php?type=arme',
				'Armurerie' => 'boutique.php?type=armure',
				'Enchanteur' => 'boutique.php?type=accessoire'/*'enchanteur.php'*/,
				'Dresseur' => 'boutique.php?type=dressage',
				'Hôtel des ventes' => 'hotel.php'
			);
			$this->aff_quartier('Quartier marchand', 'quartier_marchand', $quartier_marchand);
		}
		// Quartier royal
		$quartier_royal = array( 'Bureau des quêtes' => 'bureau_quete.php' );
		if( $royaume->get_diplo($perso->get_race()) == 127 ) // Si on est dans notre royaume
			$quartier_royal['Quartier général'] = 'qg.php';
		$quartier_royal['Pierre de Téléportation'] = 'teleport.php';
		$quartier_royal['Tribunal'] = 'tribunal.php';
		$this->aff_quartier('Quartier royal', 'quartier_royal', $quartier_royal);
		// Haut quartier (vérifier royaume neutre ?)
		$haut_quartier = array
		(
			'Université' => 'universite.php',
			'École de magie' => 'ecole.php?type=sort',
			'École de combat' => 'ecole.php?type=comp',
			'Alchimiste' => 'alchimiste.php',
		);
		$this->aff_quartier('Haut quartier', 'haut_quartier', $haut_quartier);
		// Bas quartier
		$bas_quartier = array
		(
			'Taverne' => 'taverne.php',
			'Écuries' => 'ecurie.php'
		);
		if( $royaume->get_diplo($perso->get_race()) == 127 ) // Si on est dans notre royaume
		{
			if( terrain::recoverByIdJoueur($perso->get_id()) )
				$bas_quartier['Votre terrain'] = 'terrain.php';
			else
				$bas_quartier['Vente de terrain'] = 'vente_terrain.php';
			$bas_quartier['Bâtiments en chantier'] = 'terrain_chantier.php';
		}
		$this->aff_quartier('Bas quartier', 'bas_quartier', $bas_quartier);
	}
	
	protected function aff_vente()
	{
		global $db;
		///TODO: à améliorer
		//Récupère tout les royaumes qui peuvent avoir des items dans l'HV
		$requete = 'SELECT * FROM diplomatie WHERE race = "'.$this->royaume->get_race().'"';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$races = array();
		$keys = array_keys($row);
		for($i=0; $i < count($row); $i++)
		{
			if( ( $row[$keys[$i]] <= 5 || $row[$keys[$i]] == 127 ) && $keys[$i] != 'race' )
				$races[] = '"'.$keys[$i].'"';
		}
		$races = implode(',', $races);
					
		//Recherche tous les objets correspondants à ces races
		$requete = 'SELECT * FROM hotel WHERE race IN ('.$races.') ORDER BY id DESC LIMIT 1';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if($db->num_rows > 0)
			$this->message->add( new interf_txt('Dernier objet en vente : '.nom_objet($row['objet']).' pour '.$row['prix'].' stars.') );
	}
	
	protected function aff_quartier($quartier, $id, $batiments)
	{
		$div = $this->ville->add( new interf_panneau($quartier, $id, 'h3', false, false, 'default', false) );
		//$menu = $div->add( new interf_bal_cont('ul', false, 'list-group') );
		$menu = $div->add( new interf_bal_cont('div', false, 'list-group') );
		foreach($batiments as $nom=>$lien)
		{
			//$menu->add(  new interf_elt_menu($nom, $lien, 'charger(this.href);', false, 'list-group-item') );
			$menu->add(  new interf_lien($nom, $lien, false, 'list-group-item') );
		}
	}
}

// Classe gérant l'interface de la ville en cas d'amende
class interf_ville_amende extends interf_ville_entree_base
{
	function __construct(&$royaume, &$amende, $erreur=false)
	{
		$perso = joueur::get_perso();
		parent::__construct($royaume);
		
		///TODO: utiliser $Gtrad
		if( $amende['statut'] != 'normal' )
			$this->message->add( new interf_txt('Vous êtes considéré comme '.$amende['statut'].' par votre royaume.') );
		if( $erreur )
		{
			$div = $this->ville->add( new interf_panneau('Amende', 'amende', 'h3', false, false, 'danger') );
			$div->add( new interf_txt('Vous n\'avez pas assez de stars !') );
		}
		else
		{
			$div = $this->ville->add( new interf_panneau('Amende', 'amende', 'h3', false, false, 'warning') );
			$div->add( new interf_txt('Il vous faut payer une amende de '.$amende['montant'].' stars pour accéder à la ville.') );
			$div->add( new interf_lien('Payer l\'amende', 'ville.php?action=paye_amende', false, 'btn btn-warning') );
		}
	}
}
?>