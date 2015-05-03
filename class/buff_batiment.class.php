<?php
/**
 * @file buff_batiment.class.php
 * Définition de la classe buff_batiment représentant les buffs sur les bâtiments
 */
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * Classe représentant les buffs sur les bâtiments
 */
class buff_batiment extends buff_base
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $id_placement;  ///< id du placement ayant le buff
	protected $id_construction;  ///< id de la construction ayant le buff
	protected $id_perso;  ///< id du personnage ayant lancé le buff

	/// Renvoie l'id du placement ayant le buff
	function get_id_placement()
	{
		return $this->id_monstre;
	}
	/// Modifie l'id du placement ayant le buff
	function set_id_placement($id)
	{
		$this->id_placement = $id;
		$this->champs_modif[] = 'id_placement';
	}

	/// Renvoie l'id de la construction ayant le buff
	function get_id_construction()
	{
		return $this->id_monstre;
	}
	/// Modifie l'id de la construction ayant le buff
	function set_id_construction($id)
	{
		$this->id_construction = $id;
		$this->champs_modif[] = 'id_construction';
	}

	/// Renvoie l'id du personnage ayant lancé le buff
	function get_id_perso()
	{
		return $this->id_perso;
	}
	/// Modifie l'id du personnage ayant lancé le buff
	function set_id_perso($id)
	{
		$this->id_perso = $id;
		$this->champs_modif[] = 'id_perso';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param $id           		Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param $id_placement   	Id du monstre ayant le buff
   * @param $id_construction	Id du monstre ayant le buff
   * @param $type         		Type générique.
	 * @param $effet        		Effet principal.
	 * @param $effet2       		Effet secondaire
	 * @param $duree        		Durée
	 * @param $fin          		Date et heure de fin du buff
	 * @param $nom          		Nom du buff
	 * @param $description  		Description du buff
	 * @param $debuff       		Pour un buff,  pour un debuff
	*/
	function __construct($id = 0, $id_placement=0, $id_construction=null, $type='', $effet=0, $effet2=0, $duree=0, $fin=0, $nom='', $description='', $debuff=0, $id_perso=0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      buff_base::__construct($id, $type, $effet, $duree);
			$this->id_placement = $id_placement;
			$this->id_construction = $id_construction;
			$this->id_perso = $id_perso;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    buff_base::init_tab($vals);
		$this->id_placement = $vals['id_placement'] != 'NULL' ? $vals['id_placement'] : null;
		$this->id_construction = $vals['id_construction'] != 'NULL' ? $vals['id_construction'] : null;
		$this->id_perso = $vals['id_perso'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return buff_base::get_liste_champs().', id_placement, id_construction, id_perso';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return buff_base::get_valeurs_insert().', '.$this->id_placement.', '.$this->id_construction.', '.$this->id_perso;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return buff_base::get_liste_update().', id_placement = '.$this->id_placement.', id_construction = '.$this->id_construction.', id_perso = '.$this->id_perso;
	}
	// @}

	/// Supprime tous les buffs lancés par un personnage (suite à sa mort)
	static function suppr_mort_perso(&$perso)
	{
		global $db;
		$db->query( 'DELETE FROM buff_batiment WHERE id_perso = '.$perso->get_id() );
	}
	
	/// Indique si le buff est actif (lanceur à 10 cases ou moins)
	function est_actif()
	{
		global $db;
		// Buff sans lanceur (assiégé) : toujours actif
		if( !$this->id_perso )
			return true;
		if( $this->id_construction )
			$requete = 'SELECT MAX(ABS(p.x - c.x), ABS(p.y - c.y)) AS dist FROM perso AS p, construction AS c WHERE p.id = '.$this->id_perso.' AND c.id = '.$this->id_construction;
		else
			$requete = 'SELECT MAX(ABS(p.x - c.x), ABS(p.y - c.y)) AS dist FROM perso AS p, placement AS c WHERE p.id = '.$this->id_perso.' AND c.id = '.$this->id_placement;
		$res = $db->query($requete);
		$row = $db->read_array($res);
		return $row[0] <= 10;
	}
}
?>
