<?php
/**
 * @file grimoire.class.php
 * Gestion des grimoires
 */
include_once(root.'class/effect.class.php');

/**
 * Classe gérant les grimoires
 * Correspond à la table du même nom dans la bdd.
 */
class grimoire extends objet_invent
{
	protected $id_apprend;  ///< Id du sort ou de la compétence apprise.
	protected $attr_perso;  ///< Nom de l'attribut à améliorer.
	protected $ajout_attr;  ///< Valeur à ajoutée à l'attribut.
	protected $classe_requis;  ///< Classes donnant accès au grimoire.

	/// Retourne l'id du sort ou de la compétence apprise
	function get_id_apprend()
	{
		return $this->id_apprend;
	}
	/// Modifie l'id du sort ou de la compétence apprise
	function set_id_apprend($val)
	{
		$this->id_apprend = $val;
		$this->champs_modif[] = 'id_apprend';
	}

	/// Retourne le nom de l'attribut à améliorer
	function get_attr_perso()
	{
		return $this->attr_perso;
	}
	/// Modifie le nom de l'attribut à améliorer
	function set_attr_perso($val)
	{
		$this->attr_perso = $val;
		$this->champs_modif[] = 'attr_perso';
	}

	/// Retourne la valeur à ajoutée à l'attribut
	function get_ajout_attr()
	{
		return $this->ajout_attr;
	}
	/// Modifie la valeur à ajoutée à l'attribut
	function set_ajout_attr($val)
	{
		$this->ajout_attr = $val;
		$this->champs_modif[] = 'ajout_attr';
	}

	/// Retourne les classes donnant accès au grimoire
	function get_classe_requis()
	{
		return $this->classe_requis;
	}
	/// Modifie les classes donnant accès au grimoire
	function set_classe_requis($val)
	{
		$this->classe_requis = $val;
		$this->champs_modif[] = 'classe_requis';
	}

	/**
	 * Constructeur
	 * @param  $nom                  nom de l'objet.
	 * @param  $type	               type de l'objet.
	 * @param  $prix	               prix de l'objet em magasin.
	 * @param  $id_apprend		       Id du sort ou de la compétence apprise.
	 * @param  $attr_perso		       Nom de l'attribut à améliorer.
	 * @param  $ajout_attr		       Valeur à ajoutée à l'attribut.
	 * @param  $classe_requis		     Classes donnant accès au grimoire.
	 */
	function __construct($nom='', $type='', $prix=0, $id_apprend=null, $attr_perso=null, $ajout_attr=null, $classe_requis=null)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->prix = $prix;
			$this->id_apprend = $id_apprend;
			$this->attr_perso = $attr_perso;
			$this->ajout_attr = $ajout_attr;
			$this->classe_requis = $classe_requis;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->id_apprend = $vals['id_apprend'];
		$this->attr_perso = $vals['attr_perso'];
		$this->ajout_attr = $vals['ajout_attr'];
		$this->classe_requis = $vals['classe_requis'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['id_apprend']='i';
    $tbl['attr_perso']='s';
    $tbl['ajout_attr']='i';
    $tbl['classe_requis']='s';
		return $tbl;
	}

	//Fonction permettant d'ajouter un nouvel objet dans la base
	public function infobulle()
  {
  }
}

?>