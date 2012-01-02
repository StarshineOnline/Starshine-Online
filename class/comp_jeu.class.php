<?php
class comp_jeu_db

{
/**
    * @access private
    * @var mediumint(8)
    */
	private $id;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var text
    */
	private $description;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $pa;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var varchar(50)
    */
	private $comp_assoc;

	/**
    * @access private
    * @var varchar(50)
    */
	private $carac_assoc;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $carac_requis;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $comp_requis;

	/**
    * @access private
    * @var varchar(50)
    */
	private $arme_requis;

	/**
    * @access private
    * @var varchar(50)
    */
	private $effet;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $effet2;

	/**
    * @access private
    * @var int(10)
    */
	private $duree;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $cible;

	/**
    * @access private
    * @var text
    */
	private $requis;

	/**
    * @access private
    * @var int(11)
    */
	private $prix;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $lvl_batiment;

	
	/**
	* @access public

	* @param mediumint(8) id attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param mediumint(8) mp attribut
	* @param tinyint(3) pa attribut
	* @param varchar(50) type attribut
	* @param varchar(50) comp_assoc attribut
	* @param varchar(50) carac_assoc attribut
	* @param mediumint(9) carac_requis attribut
	* @param mediumint(9) comp_requis attribut
	* @param varchar(50) arme_requis attribut
	* @param varchar(50) effet attribut
	* @param tinyint(3) effet2 attribut
	* @param int(10) duree attribut
	* @param tinyint(4) cible attribut
	* @param text requis attribut
	* @param int(11) prix attribut
	* @param tinyint(3) lvl_batiment attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $mp = '', $pa = '', $type = '', $comp_assoc = '', $carac_assoc = '', $carac_requis = '', $comp_requis = '', $arme_requis = '', $effet = '', $effet2 = '', $duree = '', $cible = '', $requis = '', $prix = '', $lvl_batiment = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, description, mp, pa, type, comp_assoc, carac_assoc, carac_requis, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment FROM comp_jeu WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->mp, $this->pa, $this->type, $this->comp_assoc, $this->carac_assoc, $this->carac_requis, $this->comp_requis, $this->arme_requis, $this->effet, $this->effet2, $this->duree, $this->cible, $this->requis, $this->prix, $this->lvl_batiment) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->mp = $id['mp'];
			$this->pa = $id['pa'];
			$this->type = $id['type'];
			$this->comp_assoc = $id['comp_assoc'];
			$this->carac_assoc = $id['carac_assoc'];
			$this->carac_requis = $id['carac_requis'];
			$this->comp_requis = $id['comp_requis'];
			$this->arme_requis = $id['arme_requis'];
			$this->effet = $id['effet'];
			$this->effet2 = $id['effet2'];
			$this->duree = $id['duree'];
			$this->cible = $id['cible'];
			$this->requis = $id['requis'];
			$this->prix = $id['prix'];
			$this->lvl_batiment = $id['lvl_batiment'];
			}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->mp = $mp;
			$this->pa = $pa;
			$this->type = $type;
			$this->comp_assoc = $comp_assoc;
			$this->carac_assoc = $carac_assoc;
			$this->carac_requis = $carac_requis;
			$this->comp_requis = $comp_requis;
			$this->arme_requis = $arme_requis;
			$this->effet = $effet;
			$this->effet2 = $effet2;
			$this->duree = $duree;
			$this->cible = $cible;
			$this->requis = $requis;
			$this->prix = $prix;
			$this->lvl_batiment = $lvl_batiment;
			$this->id = $id;
		}
	}

	/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", mp = "'.mysql_escape_string($this->mp).'", pa = "'.mysql_escape_string($this->pa).'", type = "'.mysql_escape_string($this->type).'", comp_assoc = "'.mysql_escape_string($this->comp_assoc).'", carac_assoc = "'.mysql_escape_string($this->carac_assoc).'", carac_requis = "'.mysql_escape_string($this->carac_requis).'", comp_requis = "'.mysql_escape_string($this->comp_requis).'", arme_requis = "'.mysql_escape_string($this->arme_requis).'", effet = "'.mysql_escape_string($this->effet).'", effet2 = "'.mysql_escape_string($this->effet2).'", duree = "'.mysql_escape_string($this->duree).'", cible = "'.mysql_escape_string($this->cible).'", requis = "'.mysql_escape_string($this->requis).'", prix = "'.mysql_escape_string($this->prix).'", lvl_batiment = "'.mysql_escape_string($this->lvl_batiment).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE comp_jeu SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO comp_jeu (nom, description, mp, pa, type, comp_assoc, carac_assoc, carac_requis, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->pa).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->comp_assoc).'", "'.mysql_escape_string($this->carac_assoc).'", "'.mysql_escape_string($this->carac_requis).'", "'.mysql_escape_string($this->comp_requis).'", "'.mysql_escape_string($this->arme_requis).'", "'.mysql_escape_string($this->effet).'", "'.mysql_escape_string($this->effet2).'", "'.mysql_escape_string($this->duree).'", "'.mysql_escape_string($this->cible).'", "'.mysql_escape_string($this->requis).'", "'.mysql_escape_string($this->prix).'", "'.mysql_escape_string($this->lvl_batiment).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}

	/**
	* Supprime de la base de donnée
	* @access public
	* @param none
	* @return none
	*/
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM comp_jeu WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
	* @access static
	* @param array|string $champs champs servant a trouver les résultats
	* @param array|string $valeurs valeurs servant a trouver les résultats
	* @param string $ordre ordre de tri
	* @param bool|string $keys Si false, stockage en tableau classique, si string stockage avec sous tableau en fonction du champ $keys
	* @return array $return liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = "SELECT id, nom, description, mp, pa, type, comp_assoc, carac_assoc, carac_requis, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment FROM comp_jeu WHERE ".$where." ORDER BY ".$ordre;
    //echo $requete.'<br/>';
    $req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
        /*print_r($row);
        echo '<br/>';*/
				if(!$keys) $return[] = new comp_jeu($row);
				else $return[$row[$keys]] = new comp_jeu($row);
			}
		}
		else $return = array();
		return $return;
	}

	/**
	* Affiche l'objet sous forme de string
	* @access public
	* @param none
	* @return string objet en string
	*/
	function __toString()
	{
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', mp = '.$this->mp.', pa = '.$this->pa.', type = '.$this->type.', comp_assoc = '.$this->comp_assoc.', carac_assoc = '.$this->carac_assoc.', carac_requis = '.$this->carac_requis.', comp_requis = '.$this->comp_requis.', arme_requis = '.$this->arme_requis.', effet = '.$this->effet.', effet2 = '.$this->effet2.', duree = '.$this->duree.', cible = '.$this->cible.', requis = '.$this->requis.', prix = '.$this->prix.', lvl_batiment = '.$this->lvl_batiment;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $description valeur de l'attribut description
	*/
	function get_description()
	{
		return $this->description;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $mp valeur de l'attribut mp
	*/
	function get_mp()
	{
		return $this->mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $pa valeur de l'attribut pa
	*/
	function get_pa()
	{
		return $this->pa;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $comp_assoc valeur de l'attribut comp_assoc
	*/
	function get_comp_assoc()
	{
		return $this->comp_assoc;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $carac_assoc valeur de l'attribut carac_assoc
	*/
	function get_carac_assoc()
	{
		return $this->carac_assoc;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $carac_requis valeur de l'attribut carac_requis
	*/
	function get_carac_requis()
	{
		return $this->carac_requis;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $comp_requis valeur de l'attribut comp_requis
	*/
	function get_comp_requis()
	{
		return $this->comp_requis;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $arme_requis valeur de l'attribut arme_requis
	*/
	function get_arme_requis()
	{
		return $this->arme_requis;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $effet valeur de l'attribut effet
	*/
	function get_effet()
	{
		return $this->effet;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $effet2 valeur de l'attribut effet2
	*/
	function get_effet2()
	{
		return $this->effet2;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $duree valeur de l'attribut duree
	*/
	function get_duree()
	{
		return $this->duree;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $cible valeur de l'attribut cible
	*/
	function get_cible()
	{
		return $this->cible;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $requis valeur de l'attribut requis
	*/
	function get_requis()
	{
		return $this->requis;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $prix valeur de l'attribut prix
	*/
	function get_prix()
	{
		return $this->prix;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $lvl_batiment valeur de l'attribut lvl_batiment
	*/
	function get_lvl_batiment()
	{
		return $this->lvl_batiment;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $id valeur de l'attribut
	* @return none
	*/
	function set_id($id)
	{
		$this->id = $id;
		$this->champs_modif[] = 'id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $description valeur de l'attribut
	* @return none
	*/
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $mp valeur de l'attribut
	* @return none
	*/
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $pa valeur de l'attribut
	* @return none
	*/
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $comp_assoc valeur de l'attribut
	* @return none
	*/
	function set_comp_assoc($comp_assoc)
	{
		$this->comp_assoc = $comp_assoc;
		$this->champs_modif[] = 'comp_assoc';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $carac_assoc valeur de l'attribut
	* @return none
	*/
	function set_carac_assoc($carac_assoc)
	{
		$this->carac_assoc = $carac_assoc;
		$this->champs_modif[] = 'carac_assoc';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $carac_requis valeur de l'attribut
	* @return none
	*/
	function set_carac_requis($carac_requis)
	{
		$this->carac_requis = $carac_requis;
		$this->champs_modif[] = 'carac_requis';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $comp_requis valeur de l'attribut
	* @return none
	*/
	function set_comp_requis($comp_requis)
	{
		$this->comp_requis = $comp_requis;
		$this->champs_modif[] = 'comp_requis';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $arme_requis valeur de l'attribut
	* @return none
	*/
	function set_arme_requis($arme_requis)
	{
		$this->arme_requis = $arme_requis;
		$this->champs_modif[] = 'arme_requis';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $effet valeur de l'attribut
	* @return none
	*/
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $effet2 valeur de l'attribut
	* @return none
	*/
	function set_effet2($effet2)
	{
		$this->effet2 = $effet2;
		$this->champs_modif[] = 'effet2';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $duree valeur de l'attribut
	* @return none
	*/
	function set_duree($duree)
	{
		$this->duree = $duree;
		$this->champs_modif[] = 'duree';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $cible valeur de l'attribut
	* @return none
	*/
	function set_cible($cible)
	{
		$this->cible = $cible;
		$this->champs_modif[] = 'cible';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $requis valeur de l'attribut
	* @return none
	*/
	function set_requis($requis)
	{
		$this->requis = $requis;
		$this->champs_modif[] = 'requis';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $prix valeur de l'attribut
	* @return none
	*/
	function set_prix($prix)
	{
		$this->prix = $prix;
		$this->champs_modif[] = 'prix';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $lvl_batiment valeur de l'attribut
	* @return none
	*/
	function set_lvl_batiment($lvl_batiment)
	{
		$this->lvl_batiment = $lvl_batiment;
		$this->champs_modif[] = 'lvl_batiment';
	}

}

class comp_jeu extends comp_jeu_db {
  function __construct($id = 0, $nom = '', $description = '', $mp = '', $pa = '', $type = '', $comp_assoc = '', $carac_assoc = '', $carac_requis = '', $comp_requis = '', $arme_requis = '', $effet = '', $effet2 = '', $duree = '', $cible = '', $requis = '', $prix = '', $lvl_batiment = '') {
    if( (func_num_args() == 1) && (is_numeric($id) || is_array($id)))
      parent::__construct($id);
    else
      parent::__construct($id, $nom, $description, $mp, $pa, $type, $comp_assoc, $carac_assoc, $carac_requis, $comp_requis, $arme_requis, $effet, $effet2, $duree, $cible, $requis, $prix, $lvl_batiment);
  }


  //fonction

}
?>
