<?php // -*- mode: php; -*-
class construction
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $id_batiment;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $x;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $y;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $royaume;

	/**
    * @access private
    * @var int(10)
    */
	private $hp;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $rez;

	/**
    * @access private
    * @var int(10)
    */
	private $rechargement;

	/**
    * @access private
    * @var varchar(50)
    */
	private $image;

	/**
    * @access private
    * @var int(10)
    */
	private $date_construction;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $point_victoire;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param tinyint(3) id_batiment attribut
	* @param tinyint(3) x attribut
	* @param tinyint(3) y attribut
	* @param tinyint(3) royaume attribut
	* @param int(10) hp attribut
	* @param varchar(50) nom attribut
	* @param varchar(50) type attribut
	* @param tinyint(3) rez attribut
	* @param int(10) rechargement attribut
	* @param varchar(50) image attribut
	* @param int(10) date_construction attribut
	* @param tinyint(3) point_victoire attribut
	* @return none
	*/
	function __construct($id = 0, $id_batiment = 0, $x = 0, $y = 0, $royaume = 0, $hp = 0, $nom = '', $type = '', $rez = '', $rechargement = '', $image = '', $date_construction = '', $point_victoire = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_batiment, x, y, royaume, hp, nom, type, rez, rechargement, image, date_construction, point_victoire FROM construction WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_batiment, $this->x, $this->y, $this->royaume, $this->hp, $this->nom, $this->type, $this->rez, $this->rechargement, $this->image, $this->date_construction, $this->point_victoire) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_batiment = $id['id_batiment'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->royaume = $id['royaume'];
			$this->hp = $id['hp'];
			$this->nom = $id['nom'];
			$this->type = $id['type'];
			$this->rez = $id['rez'];
			$this->rechargement = $id['rechargement'];
			$this->image = $id['image'];
			$this->date_construction = $id['date_construction'];
			$this->point_victoire = $id['point_victoire'];
			}
		else
		{
			$this->id_batiment = $id_batiment;
			$this->x = $x;
			$this->y = $y;
			$this->royaume = $royaume;
			$this->hp = $hp;
			$this->nom = $nom;
			$this->type = $type;
			$this->rez = $rez;
			$this->rechargement = $rechargement;
			$this->image = $image;
			$this->date_construction = $date_construction;
			$this->point_victoire = $point_victoire;
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
				if($force) $champs = 'id_batiment = '.$this->id_batiment.', x = '.$this->x.', y = '.$this->y.', royaume = '.$this->royaume.', hp = '.$this->hp.', nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", rez = "'.mysql_escape_string($this->rez).'", rechargement = "'.mysql_escape_string($this->rechargement).'", image = "'.mysql_escape_string($this->image).'", date_construction = "'.mysql_escape_string($this->date_construction).'", point_victoire = "'.mysql_escape_string($this->point_victoire).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE construction SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO construction (id_batiment, x, y, royaume, hp, nom, type, rez, rechargement, image, date_construction, point_victoire) VALUES(';
			$requete .= ''.$this->id_batiment.', '.$this->x.', '.$this->y.', '.$this->royaume.', '.$this->hp.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->rez).'", "'.mysql_escape_string($this->rechargement).'", "'.mysql_escape_string($this->image).'", "'.mysql_escape_string($this->date_construction).'", "'.mysql_escape_string($this->point_victoire).'")';
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
			$requete = 'DELETE FROM construction WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_batiment, x, y, royaume, hp, nom, type, rez, rechargement, image, date_construction, point_victoire FROM construction WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new construction($row);
				else $return[$row[$keys]][] = new construction($row);
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
		return 'id = '.$this->id.', id_batiment = '.$this->id_batiment.', x = '.$this->x.', y = '.$this->y.', royaume = '.$this->royaume.', hp = '.$this->hp.', nom = '.$this->nom.', type = '.$this->type.', rez = '.$this->rez.', rechargement = '.$this->rechargement.', image = '.$this->image.', date_construction = '.$this->date_construction.', point_victoire = '.$this->point_victoire;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $id_batiment valeur de l'attribut id_batiment
	*/
	function get_id_batiment()
	{
		return $this->id_batiment;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $x valeur de l'attribut x
	*/
	function get_x()
	{
		return $this->x;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $y valeur de l'attribut y
	*/
	function get_y()
	{
		return $this->y;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $royaume valeur de l'attribut royaume
	*/
	function get_royaume()
	{
		return $this->royaume;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
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
	* @return tinyint(3) $rez valeur de l'attribut rez
	*/
	function get_rez()
	{
		return $this->rez;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $rechargement valeur de l'attribut rechargement
	*/
	function get_rechargement()
	{
		return $this->rechargement;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $image valeur de l'attribut image
	*/
	function get_image()
	{
		return $this->image;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $date_construction valeur de l'attribut date_construction
	*/
	function get_date_construction()
	{
		return $this->date_construction;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $point_victoire valeur de l'attribut point_victoire
	*/
	function get_point_victoire()
	{
		return $this->point_victoire;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id valeur de l'attribut
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
	* @param tinyint(3) $id_batiment valeur de l'attribut
	* @return none
	*/
	function set_id_batiment($id_batiment)
	{
		$this->id_batiment = $id_batiment;
		$this->champs_modif[] = 'id_batiment';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $x valeur de l'attribut
	* @return none
	*/
	function set_x($x)
	{
		$this->x = $x;
		$this->champs_modif[] = 'x';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $y valeur de l'attribut
	* @return none
	*/
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $royaume valeur de l'attribut
	* @return none
	*/
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $hp valeur de l'attribut
	* @return none
	*/
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
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
	* @param tinyint(3) $rez valeur de l'attribut
	* @return none
	*/
	function set_rez($rez)
	{
		$this->rez = $rez;
		$this->champs_modif[] = 'rez';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $rechargement valeur de l'attribut
	* @return none
	*/
	function set_rechargement($rechargement)
	{
		$this->rechargement = $rechargement;
		$this->champs_modif[] = 'rechargement';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $image valeur de l'attribut
	* @return none
	*/
	function set_image($image)
	{
		$this->image = $image;
		$this->champs_modif[] = 'image';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $date_construction valeur de l'attribut
	* @return none
	*/
	function set_date_construction($date_construction)
	{
		$this->date_construction = $date_construction;
		$this->champs_modif[] = 'date_construction';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $point_victoire valeur de l'attribut
	* @return none
	*/
	function set_point_victoire($point_victoire)
	{
		$this->point_victoire = $point_victoire;
		$this->champs_modif[] = 'point_victoire';
	}
//fonction

	private $buff = null;
	function get_buff()
	{
		if ($this->buff == null) {
			$this->buff = get_construction_buff($this->id);
		}
		return $this->buff;
	}

	static function get_construction_buff($id)
	{
		global $db;
		$buff = array();
		$req = $db->query("select * from buff_batiment where id_construction = '$id'");
		if ($req) {
			while ($b = $db->read_object($req)) {
				$buff[] = $b;
			}
		}
		return $buff;
	}

	static function check_buff()
	{
		$req = $db->query("delete from buff_batiment where date_fin <= ".time());
	}
}
?>
