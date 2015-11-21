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
	const code = 'l';   ///< Code de l'objet.

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

	/// Méthode renvoyant l'image de l'objet
	/*public function get_image()
  {
    switch( $this->type )
    {
    case 'sort_jeu':
    case 'comp_jeu':
      $class = $this->type;
      $cible = new $class($this->id_apprend);
      $image = 'image/buff/'.$cible->get_type().'.png';
      if( file_exists($image) )
        return $image;
    default:
      return null;
    }
  }*/

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    global $Gtrad;
    $noms = array('Description');
    if( !in_array($this->type, array('attr_perso', 'alchimie', 'forge')) )
    {
    	$class = $this->type;
      $cible = new $class($this->id_apprend);
      if( $class == 'sort_jeu' or  $class == 'sort_combat' )
        $noms[] = 'Incantation';
      $noms[] = $Gtrad[$cible->get_comp_assoc()];
      if( $cible->get_requis() && $cible->get_requis() != 999 )
        $noms[] = 'Requiert';
    }
    $noms[] = 'Encombrement';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $class = $this->type;
    switch($class)
    {
    case 'comp_jeu':
    case 'comp_combat':
      $type = 'le sort';
      $description = null;
      break;
    case 'comp_jeu':
    case 'comp_combat':
      $type = 'la compétence';
      $description = null;
      break;
    case 'alchimie':
    	$recette = new craft_recette($this->id_apprend);
    	return array( 'Apprend la recette pour '.$recette->get_nom() );
    case 'forge':
    	$recette = new forge_recette($this->id_apprend);
    	return array( 'Apprend la recette pour '.$recette->get_nom() );
    case 'attr_perso':
      return array( 'Entraîne la compétence '.traduit($this->attr_perso).' de '.$this->ajout_attr );
    }
    $cible = new $class($this->id_apprend);
    $vals = array( 'Apprend '.$type.' '.$cible->get_nom() );
    if( $class == 'sort_jeu' or  $class == 'sort_combat' )
      $vals[] = $cible->get_incantation();
    $vals[] = $cible->get_comp_requis();
    $requis = $cible->get_requis();
    if( $requis && $requis != 999 )
    {
      $req = new $class($requis);
      $vals[] = $req->get_nom();
    }
    $vals[] = $this->encombrement;

    return $vals;
  }

	function get_colone_int($partie)
  {
    if( $partie == 'utile' )
      return 1;
    else
      return false;
  }

  function est_utilisable() { return true; }

  function utiliser(&$perso, &$princ)
  {
  	global $db;
    if( $this->classe_requis )
    {
      $classes_autorisees = explode(';', $this->classe_requis);
  		if( !in_array($perso->get_classe(), $classes_autorisees) )
      {
        $princ->add( new interf_alerte('danger', true) )->add_message('Impossible de lire ce grimoire : il n\'est pas destiné à votre classe !');
  			return false;
  		}
    }
    switch( $this->type )
    {
    case 'comp_jeu':
    case 'comp_combat':
      if( !apprend_competence($this->type, $this->id_apprend, $perso, null, true, $princ) )
      	return false;
      break;
    case 'sort_jeu':
    case 'sort_combat':
      if( !apprend_sort($this->type, $this->id_apprend, $perso, null, true, $princ) )
      	return false;
      break;
    case 'attr_perso':
      $comp = $perso->get_comp( $this->attr_perso );
      if( $comp === false )
      {
        $princ->add( new interf_alerte('danger', true) )->add_message('Impossible d\'entraîner cette compétence : vous ne la connaissez pas !');
  			return false;
      }
      $permet = classe_permet::create('id_classe', $perso->get_classe_id());
      if( $comp >= $permet->get_permet() )
      {
        $princ->add( new interf_alerte('danger', true) )->add_message('Impossible d\'entraîner cette compétence : vous en connaissez toutes les arcanes !');
  			return false;
      }
      $comp += $this->ajout_attr;
      if( $comp >= $permet->get_permet() )
        $comp = $permet->get_permet();
      $perso->set_comp($this->attr_perso, $comp);
      $princ->add( new interf_alerte('success', true) )->add_message('Compétence entraînée.');
		case 'alchimie':
			/// @todo passer à l'objet
			$requete = 'SELECT * FROM perso_recette WHERE id_perso = '.$perso->get_id().' AND id_recette = '.$this->id_apprend;
			$req = $db->query($requete);
			if( $db->num_rows )
			{
        $princ->add( new interf_alerte('danger', true) )->add_message('Vous connaissez déjà cette recette.');
  			return false;
			}
			$requete = 'INSERT INTO perso_recette (id_perso, id_recette) VALUES ('.$perso->get_id().', '.$this->id_apprend.')';
			$db->query($requete);
    	$princ->add( new interf_alerte('success', true) )->add_message('Recette apprise.');
		case 'forge':
			/// @todo passer à l'objet
			$requete = 'SELECT * FROM perso_forge WHERE id_perso = '.$perso->get_id().' AND id_recette = '.$this->id_apprend;
			$req = $db->query($requete);
			if( $db->num_rows )
			{
        $princ->add( new interf_alerte('danger', true) )->add_message('Vous connaissez déjà ce manuel.');
  			return false;
			}
			$requete = 'INSERT INTO perso_forge (id_perso, id_recette) VALUES ('.$perso->get_id().', '.$this->id_apprend.')';
			$db->query($requete);
    	$princ->add( new interf_alerte('success', true) )->add_message('Recette apprise.');
    	break;
		default:
			log_admin::log('erreur', 'Type de grimoire inconnu : '.$this->type);
			return false;
    }
    $perso->supprime_objet($this->get_texte(), 1);
		return true;
  }
}

?>