<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class royaume
{
/**
    * @access private
    * @var int(11)
    */
	private $id;

	/**
    * @access private
    * @var int(10)
    */
	private $taxe_time;

	/**
    * @access private
    * @var varchar(50)
    */
	private $race;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(50)
    */
	private $capitale;

	/**
    * @access private
    * @var int(10)
    */
	private $star;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $star_nouveau_joueur;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $taxe;

	/**
    * @access private
    * @var text
    */
	private $diplo_time;

	/**
    * @access private
    * @var int(10)
    */
	private $honneur_candidat;

	/**
    * @access private
    * @var int(10)
    */
	private $bourg;

	/**
    * @access private
    * @var int(10)
    */
	private $pierre;

	/**
    * @access private
    * @var int(10)
    */
	private $bois;

	/**
    * @access private
    * @var int(10)
    */
	private $eau;

	/**
    * @access private
    * @var int(10)
    */
	private $sable;

	/**
    * @access private
    * @var int(10)
    */
	private $charbon;

	/**
    * @access private
    * @var int(10)
    */
	private $essence;

	/**
    * @access private
    * @var int(10)
    */
	private $food;

	/**
    * @access private
    * @var int(10)
    */
	private $alchimie;

	
	/**
	* @access public

	* @param int(11) id attribut
	* @param int(10) taxe_time attribut
	* @param varchar(50) race attribut
	* @param varchar(50) nom attribut
	* @param varchar(50) capitale attribut
	* @param int(10) star attribut
	* @param mediumint(8) star_nouveau_joueur attribut
	* @param tinyint(3) taxe attribut
	* @param text diplo_time attribut
	* @param int(10) honneur_candidat attribut
	* @param int(10) bourg attribut
	* @param int(10) pierre attribut
	* @param int(10) bois attribut
	* @param int(10) eau attribut
	* @param int(10) sable attribut
	* @param int(10) charbon attribut
	* @param int(10) essence attribut
	* @param int(10) food attribut
	* @param int(10) alchimie attribut
	* @return none
	*/
	function __construct($id = 0, $taxe_time = 0, $race = '', $nom = '', $capitale = '', $star = '', $star_nouveau_joueur = '', $taxe = '', $diplo_time = '', $honneur_candidat = '', $bourg = '', $pierre = '', $bois = '', $eau = '', $sable = '', $charbon = '', $essence = '', $food = '', $alchimie = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id, taxe_time, race, nom, capitale, star, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie FROM royaume WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id, $this->taxe_time, $this->race, $this->nom, $this->capitale, $this->star, $this->star_nouveau_joueur, $this->taxe, $this->diplo_time, $this->honneur_candidat, $this->bourg, $this->pierre, $this->bois, $this->eau, $this->sable, $this->charbon, $this->essence, $this->food, $this->alchimie) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->taxe_time = $id['taxe_time'];
			$this->race = $id['race'];
			$this->nom = $id['nom'];
			$this->capitale = $id['capitale'];
			$this->star = $id['star'];
			$this->star_nouveau_joueur = $id['star_nouveau_joueur'];
			$this->taxe = $id['taxe'];
			$this->diplo_time = $id['diplo_time'];
			$this->honneur_candidat = $id['honneur_candidat'];
			$this->bourg = $id['bourg'];
			$this->pierre = $id['pierre'];
			$this->bois = $id['bois'];
			$this->eau = $id['eau'];
			$this->sable = $id['sable'];
			$this->charbon = $id['charbon'];
			$this->essence = $id['essence'];
			$this->food = $id['food'];
			$this->alchimie = $id['alchimie'];
			}
		else
		{
			$this->id = $id;
			$this->taxe_time = $taxe_time;
			$this->race = $race;
			$this->nom = $nom;
			$this->capitale = $capitale;
			$this->star = $star;
			$this->star_nouveau_joueur = $star_nouveau_joueur;
			$this->taxe = $taxe;
			$this->diplo_time = $diplo_time;
			$this->honneur_candidat = $honneur_candidat;
			$this->bourg = $bourg;
			$this->pierre = $pierre;
			$this->bois = $bois;
			$this->eau = $eau;
			$this->sable = $sable;
			$this->charbon = $charbon;
			$this->essence = $essence;
			$this->food = $food;
			$this->alchimie = $alchimie;
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
				if($force) $champs = 'id = '.$this->id.', taxe_time = '.$this->taxe_time.', race = "'.mysql_escape_string($this->race).'", nom = "'.mysql_escape_string($this->nom).'", capitale = "'.mysql_escape_string($this->capitale).'", star = "'.mysql_escape_string($this->star).'", star_nouveau_joueur = "'.mysql_escape_string($this->star_nouveau_joueur).'", taxe = "'.mysql_escape_string($this->taxe).'", diplo_time = "'.mysql_escape_string($this->diplo_time).'", honneur_candidat = "'.mysql_escape_string($this->honneur_candidat).'", bourg = "'.mysql_escape_string($this->bourg).'", pierre = "'.mysql_escape_string($this->pierre).'", bois = "'.mysql_escape_string($this->bois).'", eau = "'.mysql_escape_string($this->eau).'", sable = "'.mysql_escape_string($this->sable).'", charbon = "'.mysql_escape_string($this->charbon).'", essence = "'.mysql_escape_string($this->essence).'", food = "'.mysql_escape_string($this->food).'", alchimie = "'.mysql_escape_string($this->alchimie).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE royaume SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO royaume (id, taxe_time, race, nom, capitale, star, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie) VALUES(';
			$requete .= ''.$this->id.', '.$this->taxe_time.', "'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->capitale).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->star_nouveau_joueur).'", "'.mysql_escape_string($this->taxe).'", "'.mysql_escape_string($this->diplo_time).'", "'.mysql_escape_string($this->honneur_candidat).'", "'.mysql_escape_string($this->bourg).'", "'.mysql_escape_string($this->pierre).'", "'.mysql_escape_string($this->bois).'", "'.mysql_escape_string($this->eau).'", "'.mysql_escape_string($this->sable).'", "'.mysql_escape_string($this->charbon).'", "'.mysql_escape_string($this->essence).'", "'.mysql_escape_string($this->food).'", "'.mysql_escape_string($this->alchimie).'")';
			$db->query($requete);
			//Récuperation du dernier id inséré.
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
			$requete = 'DELETE FROM royaume WHERE id = '.$this->id;
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

		$requete = "SELECT id, taxe_time, race, nom, capitale, star, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie FROM royaume WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows() > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new royaume($row);
				else $return[$row[$keys]][] = new royaume($row);
			}
		}
		else $return = false;
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
		return 'id = '.$this->id.', taxe_time = '.$this->taxe_time.', race = '.$this->race.', nom = '.$this->nom.', capitale = '.$this->capitale.', star = '.$this->star.', star_nouveau_joueur = '.$this->star_nouveau_joueur.', taxe = '.$this->taxe.', diplo_time = '.$this->diplo_time.', honneur_candidat = '.$this->honneur_candidat.', bourg = '.$this->bourg.', pierre = '.$this->pierre.', bois = '.$this->bois.', eau = '.$this->eau.', sable = '.$this->sable.', charbon = '.$this->charbon.', essence = '.$this->essence.', food = '.$this->food.', alchimie = '.$this->alchimie;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $taxe_time valeur de l'attribut taxe_time
	*/
	function get_taxe_time()
	{
		return $this->taxe_time;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $race valeur de l'attribut race
	*/
	function get_race()
	{
		return $this->race;
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
	* @return varchar(50) $capitale valeur de l'attribut capitale
	*/
	function get_capitale()
	{
		return $this->capitale;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $star valeur de l'attribut star
	*/
	function get_star()
	{
		return $this->star;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $star_nouveau_joueur valeur de l'attribut star_nouveau_joueur
	*/
	function get_star_nouveau_joueur()
	{
		return $this->star_nouveau_joueur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $taxe valeur de l'attribut taxe
	*/
	function get_taxe()
	{
		return $this->taxe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $diplo_time valeur de l'attribut diplo_time
	*/
	function get_diplo_time()
	{
		return $this->diplo_time;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $honneur_candidat valeur de l'attribut honneur_candidat
	*/
	function get_honneur_candidat()
	{
		return $this->honneur_candidat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $bourg valeur de l'attribut bourg
	*/
	function get_bourg()
	{
		return $this->bourg;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $pierre valeur de l'attribut pierre
	*/
	function get_pierre()
	{
		return $this->pierre;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $bois valeur de l'attribut bois
	*/
	function get_bois()
	{
		return $this->bois;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $eau valeur de l'attribut eau
	*/
	function get_eau()
	{
		return $this->eau;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $sable valeur de l'attribut sable
	*/
	function get_sable()
	{
		return $this->sable;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $charbon valeur de l'attribut charbon
	*/
	function get_charbon()
	{
		return $this->charbon;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $essence valeur de l'attribut essence
	*/
	function get_essence()
	{
		return $this->essence;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $food valeur de l'attribut food
	*/
	function get_food()
	{
		return $this->food;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $alchimie valeur de l'attribut alchimie
	*/
	function get_alchimie()
	{
		return $this->alchimie;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $id valeur de l'attribut
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
	* @param int(10) $taxe_time valeur de l'attribut
	* @return none
	*/
	function set_taxe_time($taxe_time)
	{
		$this->taxe_time = $taxe_time;
		$this->champs_modif[] = 'taxe_time';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $race valeur de l'attribut
	* @return none
	*/
	function set_race($race)
	{
		$this->race = $race;
		$this->champs_modif[] = 'race';
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
	* @param varchar(50) $capitale valeur de l'attribut
	* @return none
	*/
	function set_capitale($capitale)
	{
		$this->capitale = $capitale;
		$this->champs_modif[] = 'capitale';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $star valeur de l'attribut
	* @return none
	*/
	function set_star($star)
	{
		$this->star = $star;
		$this->champs_modif[] = 'star';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $star_nouveau_joueur valeur de l'attribut
	* @return none
	*/
	function set_star_nouveau_joueur($star_nouveau_joueur)
	{
		$this->star_nouveau_joueur = $star_nouveau_joueur;
		$this->champs_modif[] = 'star_nouveau_joueur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $taxe valeur de l'attribut
	* @return none
	*/
	function set_taxe($taxe)
	{
		$this->taxe = $taxe;
		$this->champs_modif[] = 'taxe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $diplo_time valeur de l'attribut
	* @return none
	*/
	function set_diplo_time($diplo_time)
	{
		$this->diplo_time = $diplo_time;
		$this->champs_modif[] = 'diplo_time';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $honneur_candidat valeur de l'attribut
	* @return none
	*/
	function set_honneur_candidat($honneur_candidat)
	{
		$this->honneur_candidat = $honneur_candidat;
		$this->champs_modif[] = 'honneur_candidat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $bourg valeur de l'attribut
	* @return none
	*/
	function set_bourg($bourg)
	{
		$this->bourg = $bourg;
		$this->champs_modif[] = 'bourg';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $pierre valeur de l'attribut
	* @return none
	*/
	function set_pierre($pierre)
	{
		$this->pierre = $pierre;
		$this->champs_modif[] = 'pierre';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $bois valeur de l'attribut
	* @return none
	*/
	function set_bois($bois)
	{
		$this->bois = $bois;
		$this->champs_modif[] = 'bois';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $eau valeur de l'attribut
	* @return none
	*/
	function set_eau($eau)
	{
		$this->eau = $eau;
		$this->champs_modif[] = 'eau';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $sable valeur de l'attribut
	* @return none
	*/
	function set_sable($sable)
	{
		$this->sable = $sable;
		$this->champs_modif[] = 'sable';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $charbon valeur de l'attribut
	* @return none
	*/
	function set_charbon($charbon)
	{
		$this->charbon = $charbon;
		$this->champs_modif[] = 'charbon';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $essence valeur de l'attribut
	* @return none
	*/
	function set_essence($essence)
	{
		$this->essence = $essence;
		$this->champs_modif[] = 'essence';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $food valeur de l'attribut
	* @return none
	*/
	function set_food($food)
	{
		$this->food = $food;
		$this->champs_modif[] = 'food';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $alchimie valeur de l'attribut
	* @return none
	*/
	function set_alchimie($alchimie)
	{
		$this->alchimie = $alchimie;
		$this->champs_modif[] = 'alchimie';
	}

	//fonction
	function get_diplo($race_joueur)
	{
		global $db;
		if(!isset($this->diplo))
		{
			$this->diplo = 5;
			if($this->id != 0)
			{
				//Sélection de la diplomatie et des taxes
				$requete_diplo = "SELECT ".$this->race." FROM diplomatie WHERE race = '".$race_joueur."'";
				$req_diplo = $db->query($requete_diplo);
				$row_diplo = $db->read_row($req_diplo);
				$Roy_row['taxe_base'] = $Roy_row['taxe'];
				
				$Roy_row['taxe'] = taux_taxe($Roy_row['taxe'], $row_diplo[0]);
			
				$Roy_row['diplo'] = $row_diplo[0];
			}
		}
		return $this->diplo;
	}
}
?>
