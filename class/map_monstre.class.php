<?php // -*- tab-width:2; mode: php -*-

class map_monstre
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $type;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $x;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $y;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $hp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $level;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(40)
    */
	private $lib;

	/**
    * @access private
    * @var int(10)
    */
	private $mort_naturelle;

	public $nonexistant = false;
	
	/**
	* @access public

	* @param int(10) id attribut
	* @param mediumint(8) type attribut
	* @param mediumint(8) x attribut
	* @param mediumint(8) y attribut
	* @param mediumint(8) hp attribut
	* @param tinyint(3) level attribut
	* @param varchar(50) nom attribut
	* @param varchar(40) lib attribut
	* @param int(10) mort_naturelle attribut
	* @return none
	*/
	function __construct($id = 0, $type = 0, $x = 0, $y = 0, $hp = 0, $level = 0, $nom = '', $lib = '', $mort_naturelle = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT mm.type, mm.x, mm.y, mm.hp, m.level, m.nom, m.lib, mm.mort_naturelle FROM map_monstre mm, monstre m WHERE mm.type = m.id AND mm.id = ".$id);
      if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->type, $this->x, $this->y, $this->hp, $this->level, $this->nom, $this->lib, $this->mort_naturelle) = $db->read_array($requeteSQL);
			}
			else
			{
				$this->__construct();
				$this->nonexistant = true;
			}
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->type = $id['type'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->hp = $id['hp'];
			$this->level = $id['level'];
			$this->nom = $id['nom'];
			$this->lib = $id['lib'];
			$this->mort_naturelle = $id['mort_naturelle'];
			}
		else
		{
			$this->type = $type;
			$this->x = $x;
			$this->y = $y;
			$this->hp = $hp;
			$this->level = $level;
			$this->nom = $nom;
			$this->lib = $lib;
			$this->mort_naturelle = $mort_naturelle;
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
				if($force) $champs = 'type = '.$this->type.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', mort_naturelle = "'.mysql_escape_string($this->mort_naturelle).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE map_monstre SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO map_monstre (type, x, y, hp, mort_naturelle) VALUES(';
			$requete .= ''.$this->type.', '.$this->x.', '.$this->y.', '.$this->hp.', "'.mysql_escape_string($this->mort_naturelle).'")';
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
			$requete = 'DELETE FROM map_monstre WHERE id = '.$this->id;
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

		$requete = "SELECT mm.id, mm.type, mm.x, mm.y, mm.hp, m.level, m.nom, m.lib, mm.mort_naturelle FROM map_monstre mm, monstre m WHERE mm.type = m.id AND ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new map_monstre($row);
				else $return[$row[$keys]][] = new map_monstre($row);
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
		return 'id = '.$this->id.', type = '.$this->type.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', level = '.$this->level.', nom = '.$this->nom.', lib = '.$this->lib.', mort_naturelle = '.$this->mort_naturelle;
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
	* @return mediumint(8) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $x valeur de l'attribut x
	*/
	function get_x()
	{
		return $this->x;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $y valeur de l'attribut y
	*/
	function get_y()
	{
		return $this->y;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $level valeur de l'attribut level
	*/
	function get_level()
	{
		return $this->level;
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
	* @return varchar(40) $lib valeur de l'attribut lib
	*/
	function get_lib()
	{
		return $this->lib;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $mort_naturelle valeur de l'attribut mort_naturelle
	*/
	function get_mort_naturelle()
	{
		return $this->mort_naturelle;
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
	* @param mediumint(8) $type valeur de l'attribut
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
	* @param mediumint(8) $x valeur de l'attribut
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
	* @param mediumint(8) $y valeur de l'attribut
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
	* @param mediumint(8) $hp valeur de l'attribut
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
	* @param tinyint(3) $level valeur de l'attribut
	* @return none
	*/
	function set_level($level)
	{
		$this->level = $level;
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
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(40) $lib valeur de l'attribut
	* @return none
	*/
	function set_lib($lib)
	{
		$this->lib = $lib;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $mort_naturelle valeur de l'attribut
	* @return none
	*/
	function set_mort_naturelle($mort_naturelle)
	{
		$this->mort_naturelle = $mort_naturelle;
		$this->champs_modif[] = 'mort_naturelle';
	}
	//fonction
	function get_buff($nom = false, $champ = false, $type = true)
	{
		if(!$nom)
		{
			$this->buff = buff_monstre::create(id_monstre, $this->id, 'id ASC', 'type');
			return $this->buff;
		}
		else
		{
			if(!isset($this->buff)) $this->get_buff();
			if(!$type)
			{
				$get = 'get_'.$champ;
				return $this->buff[0]->$get();
			}
			else
				foreach($this->buff as $buff)
				{
					if($buff->get_type() == $nom)
					{
						$get = 'get_'.$champ;
						return $buff->$get();
					}
				}
		}
	}

	/**
	 * Permet de savoir si le joueur est sous le buff nom
	 * @param $nom le nom du buff
	 * @param $type si le nom est le type du buff
	 * @return true si le perso est sous le buff false sinon.
 	*/
	function is_buff($nom = '', $type = true)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buffe = false;

		if(is_array($this->buff))
		{
			if(!empty($nom))
			{
				foreach($this->buff as $key => $buff)
				{
					if($type)
					{
						if($key == $nom) $buffe = true;
					}
					else if($buff->get_nom() ==  $nom)
					{
						$buffe = true;
					}
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;

		return $buffe;
	}
	
	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}

	function get_race()
	{
		return 'neutre';
	}

	function kill_monstre_de_donjon()
	{
		global $db;
		switch ($this->type)
		{
		case 64: //Si c'est Devorsis on fait pop le fossoyeur
			$requete = "INSERT INTO map_monstre VALUES(NULL, '65','3','212','4800',"
        .(time() + 2678400).")";
			$db->query($requete);
			echo '<strong>Rha, tu me détruis aujourdhui mais le fossoyeur saura saisir ton âme... tu es déja mort !</strong><br/>';
			break;
			
		case 65: //Si c'est le fossoyeur on fait pop finwir
			$requete = "INSERT INTO map_monstre VALUES(NULL, '75','24','209','8000',"
        .(time() + 2678400).")";
			$db->query($requete);
			echo '<strong>Tu ne fait que retarder l\'inévitable, Le maître saura te faire payer ton insolence !</strong><br/>';
			break;
			
		case 75: //Si c'est Finrwirr on fait pop le gros monstre
			echo '<strong>Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!</strong><br/>';
      $req = $db->query("select decor from map where x = 20 and y = 227");
      $row = $db->read_assoc($req);
			if ($row['decor'] == 1601) // Si le gros monstre n'a pas ete vaincu, le passage vers le portail est encore un mur
			{
				$requete = "INSERT INTO map_monstre VALUES(NULL,116,18,227,10000,"
          .(time() + 2678400).")";
				$db->query($requete);
				// Ouverture du passage vers le gros monstre
				$requete = "UPDATE map set decor = 1539, info = 15 where x = 4 and (y >= 216 and y <= 219)";
				$db->query($requete);
			}
			else
			{
				echo '<em>Seul le silence répond à cet appel, Adenaïos le nécromant a déjà été vaincu ...</em><br/>';
			}
			break;

    case 116: // Si c'est le gros monstre, on ouvre le chemin vers le portail
    {
      echo "Maitre Aâzgruth reprend mon âme, ahhharghh, vous ne savez rien de ce qui vous attends... <em>Le squelette du nécromant se brise sous vos yeux. Une silhouette noir s'en dégage pendant quelque secondes avant d'être subitement avalée par le mur situé en face de vous ... qui eclate comme un miroir.</em><br/>";
      $requete = "update map set decor = 1539, info = 15 where y = 227 and (x = 20 or x = 21)";
      $db->query($requete);
      $requete = "update map set decor = 1676 where y = 226 and (x = 20 or x = 21)";
      $db->query($requete);
    }
			
		case 125:	//Si c'est un draconide
		case 126:
			//Si les 2 sont morts, on fait pop le roi gobelin
			$requete = "SELECT type FROM map_monstre WHERE type IN (125, 126)";
			$req_d = $db->query($requete);
			//Si il n'est pas là on le fait pop
			if($db->num_rows($req_d) == 0)
			{
				$requete = "INSERT INTO map_monstre VALUES(NULL,123,44,293,5800,"
          .(time() + 2678400).")";
				$db->query($requete);
				echo '<strong>Un bruit de mécanisme eveille votre attention, mais il vous est impossible de savoir d\'où provient ce son.</strong>';
			}
			break;

		case 123: //Le roi des gobs fait pop le second roi des gobs
			$requete = "INSERT INTO map_monstre select NULL, id, 17, 292, hp, ".(time() + 2678400)." from monstre where lib = 'roi_goblin_2' limit 1";
			$db->query($requete);
			echo '<strong>Le roi gobelin Ziwek Rustog pousse un cri d\'une frénésie grotesque, se mettant à lancer tout un tas de babioles aux les quatre coins de la pièce. Vous regardez les objets voler tout autour de vous, tentant de les éviter ou les laissant ricocher sur vos armures. Cela devient presque un jeu. Vous reprenez peu à peu vos esprits, revenant vers le roi narquois, et vous comprenez que ce dernier vous a ensorcelé et s\'est carapaté. Devant vous, vous apercevez un petit passage avec des traces fraîches.</strong>';
			break;
			
		default:
			// Rien à faire
		}
	}

	function kill_monstre_de_donjon2()
	{
		global $db;
		$requete = "select * from monstre_special where type = $this->type";
		$req_d = $db->query($requete);
		if ($db->num_rows($req_d))
		{
			$row = $db->read_assoc($req);
			my_dump($row);
			// On va vérifier les prérequis
			$prerequis = true;
			if ($row['condition_sql'] !== null)
			{
				$req_p = $db->query($row['condition_sql']);
				if ($db->num_rows($req_d) == 0)
					$prerequis = false;
			}
			if ($row['non_condition_sql'] !== null)
			{
				$req_p = $db->query($row['non_condition_sql']);
				if ($db->num_rows($req_d) != 0)
					$prerequis = false;
			}
			if ($row['eval_condition'] !== null)
				$prerequis = $prerequis || eval($row['eval_condition']);
			if ($prerequis == true)
			{
				if ($row['texte'] !== null)
					echo "<strong>$row[texte]</strong>\n";
				if ($row['eval_action'] !== null)
					eval($row['eval_action']);
				if ($row['pop_type'] !== null)
				{
					$mort_naturelle = time() + 2678400;
					$x = $row['pop_x'];
					$y = $row['pop_y'];
					if ($x === null) $x = rand(1, 150);
					if ($y === null) $y = rand(1, 150);
					$requete = "INSERT INTO map_monstre SELECT null, id, ".
						"$x, $y, hp, $mort_naturelle ".
						"FROM monstre WHERE id = $row[pop_type]";
					$db->query($requete);
				}
			}
		}
	}

	function check_monstre()
	{
		global $db;
		// On supprime tous les buffs périmés
		$requete = "DELETE FROM buff_monstre WHERE fin <= ".time();
		$req = $db->query($requete);
	}
}