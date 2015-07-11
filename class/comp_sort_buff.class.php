<?php
/**
 * @file comp_sort_buff.class.php
 * Définition de la classe comp_sort_buff servant de base aux compétences sorts et buffs
 */

/**
 * Classe comp_sort_buff
 * Classe comp_sort_buff servant de base aux compétences sorts et buffs
 */
class comp_sort_buff extends table
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
  protected $type;   ///< Type générique.
	protected $effet;  ///< Effet principal.
	protected $duree;  ///< Durée.
	protected $nom;   ///< Nom du buff


	/// Renvoie le type générique
	function get_type($complet=false)
	{
		if( $complet )
			return $this->type;
		else
  		return explode('-', $this->type)[0];
	}
	/// Modifie le type générique
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/// Renvoie l'effet principal
	function get_effet()
	{
		return $this->effet;
	}
	/// Modifie l'effet principal
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
	}

	/// Renvoie la durée
	function get_duree()
	{
		return $this->duree;
	}
	/// Modifie la durée
	function set_duree($duree)
	{
		$this->duree = $duree;
		$this->champs_modif[] = 'duree';
	}

	/// Renvoie le nom du buff
	function get_nom()
	{
		return $this->nom;
	}
	/// Modifie le nom du buff
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id       Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type     Type générique.
	 * @param effet    Effet principal.
	 * @param duree    Durée
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->type = $type;
			$this->effet = $effet;
			$this->duree = $duree;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
		$this->id = $vals['id'];
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->effet = $vals['effet'];
		$this->duree = $vals['duree'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'nom, type, effet, duree';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->type).'", '.$this->effet.', '.$this->duree;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", effet = '.$this->effet.', duree = '.$this->duree;
	}
	// @}
	/// Formate la description
	function formate_description($texte)
	{
  	while(preg_match("`%([a-z0-9]*)%`i",$texte, $regs))
  	{
  		$get = 'get_'.$regs[1];
  		$texte = str_replace('%'.$regs[1].'%', $this->$get(), $texte);
  	}
  	// Evaluation
  	$valeur = '';
  	while(preg_match('`@(.*)@`', $texte, $regs))
  	{
  		$r = $regs[1];
  		eval("\$valeur = ".$r.";");
  		$texte = str_replace('@'.$regs[1].'@', $valeur, $texte);
  	}
  	return $texte;
  }
}
 

?>
