<?php
class sort_jeu
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
    * @var tinyint(3)
    */
	private $pa;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

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
    * @var mediumint(8)
    */
	private $incantation;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $comp_requis;

	/**
    * @access private
    * @var varchar(50)
    */
	private $effet;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $effet2;

	/**
    * @access private
    * @var int(11)
    */
	private $duree;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $cible;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $portee;

	/**
    * @access private
    * @var text
    */
	private $requis;

	/**
    * @access private
    * @var int(10)
    */
	private $difficulte;

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
	* @param tinyint(3) pa attribut
	* @param mediumint(8) mp attribut
	* @param varchar(50) type attribut
	* @param varchar(50) comp_assoc attribut
	* @param varchar(50) carac_assoc attribut
	* @param mediumint(9) carac_requis attribut
	* @param mediumint(8) incantation attribut
	* @param mediumint(9) comp_requis attribut
	* @param varchar(50) effet attribut
	* @param tinyint(4) effet2 attribut
	* @param int(11) duree attribut
	* @param tinyint(4) cible attribut
	* @param tinyint(3) portee attribut
	* @param text requis attribut
	* @param int(10) difficulte attribut
	* @param int(11) prix attribut
	* @param tinyint(3) lvl_batiment attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $pa = '', $mp = '', $type = '', $comp_assoc = '', $carac_assoc = '', $carac_requis = '', $incantation = '', $comp_requis = '', $effet = '', $effet2 = '', $duree = '', $cible = '', $portee = '', $requis = '', $difficulte = '', $prix = '', $lvl_batiment = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, description, pa, mp, type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet, effet2, duree, cible, portee, requis, difficulte, prix, lvl_batiment FROM sort_jeu WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->pa, $this->mp, $this->type, $this->comp_assoc, $this->carac_assoc, $this->carac_requis, $this->incantation, $this->comp_requis, $this->effet, $this->effet2, $this->duree, $this->cible, $this->portee, $this->requis, $this->difficulte, $this->prix, $this->lvl_batiment) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->pa = $id['pa'];
			$this->mp = $id['mp'];
			$this->type = $id['type'];
			$this->comp_assoc = $id['comp_assoc'];
			$this->carac_assoc = $id['carac_assoc'];
			$this->carac_requis = $id['carac_requis'];
			$this->incantation = $id['incantation'];
			$this->comp_requis = $id['comp_requis'];
			$this->effet = $id['effet'];
			$this->effet2 = $id['effet2'];
			$this->duree = $id['duree'];
			$this->cible = $id['cible'];
			$this->portee = $id['portee'];
			$this->requis = $id['requis'];
			$this->difficulte = $id['difficulte'];
			$this->prix = $id['prix'];
			$this->lvl_batiment = $id['lvl_batiment'];
			}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->pa = $pa;
			$this->mp = $mp;
			$this->type = $type;
			$this->comp_assoc = $comp_assoc;
			$this->carac_assoc = $carac_assoc;
			$this->carac_requis = $carac_requis;
			$this->incantation = $incantation;
			$this->comp_requis = $comp_requis;
			$this->effet = $effet;
			$this->effet2 = $effet2;
			$this->duree = $duree;
			$this->cible = $cible;
			$this->portee = $portee;
			$this->requis = $requis;
			$this->difficulte = $difficulte;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", pa = "'.mysql_escape_string($this->pa).'", mp = "'.mysql_escape_string($this->mp).'", type = "'.mysql_escape_string($this->type).'", comp_assoc = "'.mysql_escape_string($this->comp_assoc).'", carac_assoc = "'.mysql_escape_string($this->carac_assoc).'", carac_requis = "'.mysql_escape_string($this->carac_requis).'", incantation = "'.mysql_escape_string($this->incantation).'", comp_requis = "'.mysql_escape_string($this->comp_requis).'", effet = "'.mysql_escape_string($this->effet).'", effet2 = "'.mysql_escape_string($this->effet2).'", duree = "'.mysql_escape_string($this->duree).'", cible = "'.mysql_escape_string($this->cible).'", portee = "'.mysql_escape_string($this->portee).'", requis = "'.mysql_escape_string($this->requis).'", difficulte = "'.mysql_escape_string($this->difficulte).'", prix = "'.mysql_escape_string($this->prix).'", lvl_batiment = "'.mysql_escape_string($this->lvl_batiment).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					foreach($this->champs_add as $champ)
					{
						$champs[] .= $champ['nom'].' = '.$champ['nom'].' + '.$champ['valeur'];
					}
					foreach($this->champs_del as $champ)
					{
						$champs[] .= $champ['nom'].' = '.$champ['nom'].' - '.$champ['valeur'];
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE sort_jeu SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO sort_jeu (nom, description, pa, mp, type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet, effet2, duree, cible, portee, requis, difficulte, prix, lvl_batiment) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->pa).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->comp_assoc).'", "'.mysql_escape_string($this->carac_assoc).'", "'.mysql_escape_string($this->carac_requis).'", "'.mysql_escape_string($this->incantation).'", "'.mysql_escape_string($this->comp_requis).'", "'.mysql_escape_string($this->effet).'", "'.mysql_escape_string($this->effet2).'", "'.mysql_escape_string($this->duree).'", "'.mysql_escape_string($this->cible).'", "'.mysql_escape_string($this->portee).'", "'.mysql_escape_string($this->requis).'", "'.mysql_escape_string($this->difficulte).'", "'.mysql_escape_string($this->prix).'", "'.mysql_escape_string($this->lvl_batiment).'")';
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
			$requete = 'DELETE FROM sort_jeu WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Supprime de la base de donnée
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
			echo $where;
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

		$requete = "SELECT id, nom, description, pa, mp, type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet, effet2, duree, cible, portee, requis, difficulte, prix, lvl_batiment FROM sort_jeu WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new sort_jeu($row);
				else $return[$row[$keys]][] = new sort_jeu($row);
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
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', pa = '.$this->pa.', mp = '.$this->mp.', type = '.$this->type.', comp_assoc = '.$this->comp_assoc.', carac_assoc = '.$this->carac_assoc.', carac_requis = '.$this->carac_requis.', incantation = '.$this->incantation.', comp_requis = '.$this->comp_requis.', effet = '.$this->effet.', effet2 = '.$this->effet2.', duree = '.$this->duree.', cible = '.$this->cible.', portee = '.$this->portee.', requis = '.$this->requis.', difficulte = '.$this->difficulte.', prix = '.$this->prix.', lvl_batiment = '.$this->lvl_batiment;
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
	* @return mediumint(8) $incantation valeur de l'attribut incantation
	*/
	function get_incantation()
	{
		return $this->incantation;
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
	* @return tinyint(4) $effet2 valeur de l'attribut effet2
	*/
	function get_effet2()
	{
		return $this->effet2;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $duree valeur de l'attribut duree
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
	* @return tinyint(3) $portee valeur de l'attribut portee
	*/
	function get_portee()
	{
		return $this->portee;
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
	* @return int(10) $difficulte valeur de l'attribut difficulte
	*/
	function get_difficulte()
	{
		return $this->difficulte;
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
	function set_id($id, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->id = $id;
				$this->champs_modif[] = 'id';
			break;
			case 'add' :
				$this->id += $id;
				$this->champs_add[] = array('nom' => 'id', 'valeur' => $id);
			break;
			case 'del' :
				$this->id -= $id;
				$this->champs_del[] = array('nom' => 'id', 'valeur' => $id);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->nom = $nom;
				$this->champs_modif[] = 'nom';
			break;
			case 'add' :
				$this->nom += $nom;
				$this->champs_add[] = array('nom' => 'nom', 'valeur' => $nom);
			break;
			case 'del' :
				$this->nom -= $nom;
				$this->champs_del[] = array('nom' => 'nom', 'valeur' => $nom);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $description valeur de l'attribut
	* @return none
	*/
	function set_description($description, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->description = $description;
				$this->champs_modif[] = 'description';
			break;
			case 'add' :
				$this->description += $description;
				$this->champs_add[] = array('nom' => 'description', 'valeur' => $description);
			break;
			case 'del' :
				$this->description -= $description;
				$this->champs_del[] = array('nom' => 'description', 'valeur' => $description);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $pa valeur de l'attribut
	* @return none
	*/
	function set_pa($pa, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->pa = $pa;
				$this->champs_modif[] = 'pa';
			break;
			case 'add' :
				$this->pa += $pa;
				$this->champs_add[] = array('nom' => 'pa', 'valeur' => $pa);
			break;
			case 'del' :
				$this->pa -= $pa;
				$this->champs_del[] = array('nom' => 'pa', 'valeur' => $pa);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $mp valeur de l'attribut
	* @return none
	*/
	function set_mp($mp, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->mp = $mp;
				$this->champs_modif[] = 'mp';
			break;
			case 'add' :
				$this->mp += $mp;
				$this->champs_add[] = array('nom' => 'mp', 'valeur' => $mp);
			break;
			case 'del' :
				$this->mp -= $mp;
				$this->champs_del[] = array('nom' => 'mp', 'valeur' => $mp);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->type = $type;
				$this->champs_modif[] = 'type';
			break;
			case 'add' :
				$this->type += $type;
				$this->champs_add[] = array('nom' => 'type', 'valeur' => $type);
			break;
			case 'del' :
				$this->type -= $type;
				$this->champs_del[] = array('nom' => 'type', 'valeur' => $type);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $comp_assoc valeur de l'attribut
	* @return none
	*/
	function set_comp_assoc($comp_assoc, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->comp_assoc = $comp_assoc;
				$this->champs_modif[] = 'comp_assoc';
			break;
			case 'add' :
				$this->comp_assoc += $comp_assoc;
				$this->champs_add[] = array('nom' => 'comp_assoc', 'valeur' => $comp_assoc);
			break;
			case 'del' :
				$this->comp_assoc -= $comp_assoc;
				$this->champs_del[] = array('nom' => 'comp_assoc', 'valeur' => $comp_assoc);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $carac_assoc valeur de l'attribut
	* @return none
	*/
	function set_carac_assoc($carac_assoc, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->carac_assoc = $carac_assoc;
				$this->champs_modif[] = 'carac_assoc';
			break;
			case 'add' :
				$this->carac_assoc += $carac_assoc;
				$this->champs_add[] = array('nom' => 'carac_assoc', 'valeur' => $carac_assoc);
			break;
			case 'del' :
				$this->carac_assoc -= $carac_assoc;
				$this->champs_del[] = array('nom' => 'carac_assoc', 'valeur' => $carac_assoc);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $carac_requis valeur de l'attribut
	* @return none
	*/
	function set_carac_requis($carac_requis, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->carac_requis = $carac_requis;
				$this->champs_modif[] = 'carac_requis';
			break;
			case 'add' :
				$this->carac_requis += $carac_requis;
				$this->champs_add[] = array('nom' => 'carac_requis', 'valeur' => $carac_requis);
			break;
			case 'del' :
				$this->carac_requis -= $carac_requis;
				$this->champs_del[] = array('nom' => 'carac_requis', 'valeur' => $carac_requis);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $incantation valeur de l'attribut
	* @return none
	*/
	function set_incantation($incantation, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->incantation = $incantation;
				$this->champs_modif[] = 'incantation';
			break;
			case 'add' :
				$this->incantation += $incantation;
				$this->champs_add[] = array('nom' => 'incantation', 'valeur' => $incantation);
			break;
			case 'del' :
				$this->incantation -= $incantation;
				$this->champs_del[] = array('nom' => 'incantation', 'valeur' => $incantation);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $comp_requis valeur de l'attribut
	* @return none
	*/
	function set_comp_requis($comp_requis, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->comp_requis = $comp_requis;
				$this->champs_modif[] = 'comp_requis';
			break;
			case 'add' :
				$this->comp_requis += $comp_requis;
				$this->champs_add[] = array('nom' => 'comp_requis', 'valeur' => $comp_requis);
			break;
			case 'del' :
				$this->comp_requis -= $comp_requis;
				$this->champs_del[] = array('nom' => 'comp_requis', 'valeur' => $comp_requis);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $effet valeur de l'attribut
	* @return none
	*/
	function set_effet($effet, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->effet = $effet;
				$this->champs_modif[] = 'effet';
			break;
			case 'add' :
				$this->effet += $effet;
				$this->champs_add[] = array('nom' => 'effet', 'valeur' => $effet);
			break;
			case 'del' :
				$this->effet -= $effet;
				$this->champs_del[] = array('nom' => 'effet', 'valeur' => $effet);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $effet2 valeur de l'attribut
	* @return none
	*/
	function set_effet2($effet2, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->effet2 = $effet2;
				$this->champs_modif[] = 'effet2';
			break;
			case 'add' :
				$this->effet2 += $effet2;
				$this->champs_add[] = array('nom' => 'effet2', 'valeur' => $effet2);
			break;
			case 'del' :
				$this->effet2 -= $effet2;
				$this->champs_del[] = array('nom' => 'effet2', 'valeur' => $effet2);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $duree valeur de l'attribut
	* @return none
	*/
	function set_duree($duree, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->duree = $duree;
				$this->champs_modif[] = 'duree';
			break;
			case 'add' :
				$this->duree += $duree;
				$this->champs_add[] = array('nom' => 'duree', 'valeur' => $duree);
			break;
			case 'del' :
				$this->duree -= $duree;
				$this->champs_del[] = array('nom' => 'duree', 'valeur' => $duree);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $cible valeur de l'attribut
	* @return none
	*/
	function set_cible($cible, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->cible = $cible;
				$this->champs_modif[] = 'cible';
			break;
			case 'add' :
				$this->cible += $cible;
				$this->champs_add[] = array('nom' => 'cible', 'valeur' => $cible);
			break;
			case 'del' :
				$this->cible -= $cible;
				$this->champs_del[] = array('nom' => 'cible', 'valeur' => $cible);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $portee valeur de l'attribut
	* @return none
	*/
	function set_portee($portee, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->portee = $portee;
				$this->champs_modif[] = 'portee';
			break;
			case 'add' :
				$this->portee += $portee;
				$this->champs_add[] = array('nom' => 'portee', 'valeur' => $portee);
			break;
			case 'del' :
				$this->portee -= $portee;
				$this->champs_del[] = array('nom' => 'portee', 'valeur' => $portee);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $requis valeur de l'attribut
	* @return none
	*/
	function set_requis($requis, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->requis = $requis;
				$this->champs_modif[] = 'requis';
			break;
			case 'add' :
				$this->requis += $requis;
				$this->champs_add[] = array('nom' => 'requis', 'valeur' => $requis);
			break;
			case 'del' :
				$this->requis -= $requis;
				$this->champs_del[] = array('nom' => 'requis', 'valeur' => $requis);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $difficulte valeur de l'attribut
	* @return none
	*/
	function set_difficulte($difficulte, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->difficulte = $difficulte;
				$this->champs_modif[] = 'difficulte';
			break;
			case 'add' :
				$this->difficulte += $difficulte;
				$this->champs_add[] = array('nom' => 'difficulte', 'valeur' => $difficulte);
			break;
			case 'del' :
				$this->difficulte -= $difficulte;
				$this->champs_del[] = array('nom' => 'difficulte', 'valeur' => $difficulte);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $prix valeur de l'attribut
	* @return none
	*/
	function set_prix($prix, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->prix = $prix;
				$this->champs_modif[] = 'prix';
			break;
			case 'add' :
				$this->prix += $prix;
				$this->champs_add[] = array('nom' => 'prix', 'valeur' => $prix);
			break;
			case 'del' :
				$this->prix -= $prix;
				$this->champs_del[] = array('nom' => 'prix', 'valeur' => $prix);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $lvl_batiment valeur de l'attribut
	* @return none
	*/
	function set_lvl_batiment($lvl_batiment, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->lvl_batiment = $lvl_batiment;
				$this->champs_modif[] = 'lvl_batiment';
			break;
			case 'add' :
				$this->lvl_batiment += $lvl_batiment;
				$this->champs_add[] = array('nom' => 'lvl_batiment', 'valeur' => $lvl_batiment);
			break;
			case 'del' :
				$this->lvl_batiment -= $lvl_batiment;
				$this->champs_del[] = array('nom' => 'lvl_batiment', 'valeur' => $lvl_batiment);
			break;
		}
	}

		//fonction
}
?>
