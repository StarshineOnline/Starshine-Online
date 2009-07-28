<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class diplomatie
{
/**
    * @access private
    * @var varchar(50)
    */
	private $race;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $barbare;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $elfebois;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $elfehaut;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $humain;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $humainnoir;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $mortvivant;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $nain;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $orc;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $scavenger;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $troll;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $vampire;

	
	/**
	* @access public

	* @param varchar(50) race attribut
	* @param tinyint(3) barbare attribut
	* @param tinyint(3) elfebois attribut
	* @param tinyint(3) elfehaut attribut
	* @param tinyint(3) humain attribut
	* @param tinyint(3) humainnoir attribut
	* @param tinyint(3) mortvivant attribut
	* @param tinyint(3) nain attribut
	* @param tinyint(3) orc attribut
	* @param tinyint(3) scavenger attribut
	* @param tinyint(3) troll attribut
	* @param tinyint(3) vampire attribut
	* @return none
	*/
	function __construct($race = '', $barbare = '', $elfebois = '', $elfehaut = '', $humain = '', $humainnoir = '', $mortvivant = '', $nain = '', $orc = '', $scavenger = '', $troll = '', $vampire = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($race) )
		{
			$requeteSQL = $db->query("SELECT barbare, elfebois, elfehaut, humain, humainnoir, mortvivant, nain, orc, scavenger, troll, vampire FROM diplomatie WHERE race = ".$race);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->barbare, $this->elfebois, $this->elfehaut, $this->humain, $this->humainnoir, $this->mortvivant, $this->nain, $this->orc, $this->scavenger, $this->troll, $this->vampire) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->race = $race;
		}
		elseif( (func_num_args() == 1) && is_array($race) )
		{
			$this->race = $race['race'];
			$this->barbare = $race['barbare'];
			$this->elfebois = $race['elfebois'];
			$this->elfehaut = $race['elfehaut'];
			$this->humain = $race['humain'];
			$this->humainnoir = $race['humainnoir'];
			$this->mortvivant = $race['mortvivant'];
			$this->nain = $race['nain'];
			$this->orc = $race['orc'];
			$this->scavenger = $race['scavenger'];
			$this->troll = $race['troll'];
			$this->vampire = $race['vampire'];
			}
		else
		{
			$this->barbare = $barbare;
			$this->elfebois = $elfebois;
			$this->elfehaut = $elfehaut;
			$this->humain = $humain;
			$this->humainnoir = $humainnoir;
			$this->mortvivant = $mortvivant;
			$this->nain = $nain;
			$this->orc = $orc;
			$this->scavenger = $scavenger;
			$this->troll = $troll;
			$this->vampire = $vampire;
			$this->race = $race;
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
		if( $this->race > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'barbare = "'.mysql_escape_string($this->barbare).'", elfebois = "'.mysql_escape_string($this->elfebois).'", elfehaut = "'.mysql_escape_string($this->elfehaut).'", humain = "'.mysql_escape_string($this->humain).'", humainnoir = "'.mysql_escape_string($this->humainnoir).'", mortvivant = "'.mysql_escape_string($this->mortvivant).'", nain = "'.mysql_escape_string($this->nain).'", orc = "'.mysql_escape_string($this->orc).'", scavenger = "'.mysql_escape_string($this->scavenger).'", troll = "'.mysql_escape_string($this->troll).'", vampire = "'.mysql_escape_string($this->vampire).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE diplomatie SET ';
				$requete .= $champs;
				$requete .= ' WHERE race = '.$this->race;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO diplomatie (barbare, elfebois, elfehaut, humain, humainnoir, mortvivant, nain, orc, scavenger, troll, vampire) VALUES(';
			$requete .= '"'.mysql_escape_string($this->barbare).'", "'.mysql_escape_string($this->elfebois).'", "'.mysql_escape_string($this->elfehaut).'", "'.mysql_escape_string($this->humain).'", "'.mysql_escape_string($this->humainnoir).'", "'.mysql_escape_string($this->mortvivant).'", "'.mysql_escape_string($this->nain).'", "'.mysql_escape_string($this->orc).'", "'.mysql_escape_string($this->scavenger).'", "'.mysql_escape_string($this->troll).'", "'.mysql_escape_string($this->vampire).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->race = $db->last_insert_id();
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
		if( $this->race > 0 )
		{
			$requete = 'DELETE FROM diplomatie WHERE race = '.$this->race;
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
	static function create($champs, $valeurs, $ordre = 'race ASC', $keys = false, $where = false)
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

		$requete = "SELECT race, barbare, elfebois, elfehaut, humain, humainnoir, mortvivant, nain, orc, scavenger, troll, vampire FROM diplomatie WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows() > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new diplomatie($row);
				else $return[$row[$keys]][] = new diplomatie($row);
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
		return 'race = '.$this->race.', barbare = '.$this->barbare.', elfebois = '.$this->elfebois.', elfehaut = '.$this->elfehaut.', humain = '.$this->humain.', humainnoir = '.$this->humainnoir.', mortvivant = '.$this->mortvivant.', nain = '.$this->nain.', orc = '.$this->orc.', scavenger = '.$this->scavenger.', troll = '.$this->troll.', vampire = '.$this->vampire;
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
	* @return tinyint(3) $barbare valeur de l'attribut barbare
	*/
	function get_barbare()
	{
		return $this->barbare;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $elfebois valeur de l'attribut elfebois
	*/
	function get_elfebois()
	{
		return $this->elfebois;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $elfehaut valeur de l'attribut elfehaut
	*/
	function get_elfehaut()
	{
		return $this->elfehaut;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $humain valeur de l'attribut humain
	*/
	function get_humain()
	{
		return $this->humain;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $humainnoir valeur de l'attribut humainnoir
	*/
	function get_humainnoir()
	{
		return $this->humainnoir;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $mortvivant valeur de l'attribut mortvivant
	*/
	function get_mortvivant()
	{
		return $this->mortvivant;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $nain valeur de l'attribut nain
	*/
	function get_nain()
	{
		return $this->nain;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $orc valeur de l'attribut orc
	*/
	function get_orc()
	{
		return $this->orc;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $scavenger valeur de l'attribut scavenger
	*/
	function get_scavenger()
	{
		return $this->scavenger;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $troll valeur de l'attribut troll
	*/
	function get_troll()
	{
		return $this->troll;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $vampire valeur de l'attribut vampire
	*/
	function get_vampire()
	{
		return $this->vampire;
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
	* @param tinyint(3) $barbare valeur de l'attribut
	* @return none
	*/
	function set_barbare($barbare)
	{
		$this->barbare = $barbare;
		$this->champs_modif[] = 'barbare';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $elfebois valeur de l'attribut
	* @return none
	*/
	function set_elfebois($elfebois)
	{
		$this->elfebois = $elfebois;
		$this->champs_modif[] = 'elfebois';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $elfehaut valeur de l'attribut
	* @return none
	*/
	function set_elfehaut($elfehaut)
	{
		$this->elfehaut = $elfehaut;
		$this->champs_modif[] = 'elfehaut';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $humain valeur de l'attribut
	* @return none
	*/
	function set_humain($humain)
	{
		$this->humain = $humain;
		$this->champs_modif[] = 'humain';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $humainnoir valeur de l'attribut
	* @return none
	*/
	function set_humainnoir($humainnoir)
	{
		$this->humainnoir = $humainnoir;
		$this->champs_modif[] = 'humainnoir';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $mortvivant valeur de l'attribut
	* @return none
	*/
	function set_mortvivant($mortvivant)
	{
		$this->mortvivant = $mortvivant;
		$this->champs_modif[] = 'mortvivant';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $nain valeur de l'attribut
	* @return none
	*/
	function set_nain($nain)
	{
		$this->nain = $nain;
		$this->champs_modif[] = 'nain';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $orc valeur de l'attribut
	* @return none
	*/
	function set_orc($orc)
	{
		$this->orc = $orc;
		$this->champs_modif[] = 'orc';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $scavenger valeur de l'attribut
	* @return none
	*/
	function set_scavenger($scavenger)
	{
		$this->scavenger = $scavenger;
		$this->champs_modif[] = 'scavenger';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $troll valeur de l'attribut
	* @return none
	*/
	function set_troll($troll)
	{
		$this->troll = $troll;
		$this->champs_modif[] = 'troll';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $vampire valeur de l'attribut
	* @return none
	*/
	function set_vampire($vampire)
	{
		$this->vampire = $vampire;
		$this->champs_modif[] = 'vampire';
	}

		//fonction
}
?>
