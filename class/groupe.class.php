<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

class groupe
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var enum('r','t','l','k')
    */
	private $partage;

	/**
    * @access private
    * @var int(10)
    */
	private $prochain_loot;

	/**
    * @access private
    * @var varchar(100)
    */
	private $nom;

	/**
	 * @access private
	 */
	private $share_xp;
	
	/**
	 * @access private
	 */
	 private $lvl;
	
	/**
	* @access public

	* @param int(10) id attribut
	* @param enum('r','t','l','k') partage attribut
	* @param int(10) prochain_loot attribut
	* @param varchar(100) nom attribut
	* @return none
	*/
	function __construct($id = 0, $partage = 0, $prochain_loot = 0, $nom = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT partage, prochain_loot, nom FROM groupe WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->partage, $this->prochain_loot, $this->nom) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->partage = $id['partage'];
			$this->prochain_loot = $id['prochain_loot'];
			$this->nom = $id['nom'];
			}
		else
		{
			$this->partage = $partage;
			$this->prochain_loot = $prochain_loot;
			$this->nom = $nom;
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
				if($force) $champs = 'partage = '.$this->partage.', prochain_loot = '.$this->prochain_loot.', nom = "'.mysql_escape_string($this->nom).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE groupe SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO groupe (partage, prochain_loot, nom) VALUES(';
			$requete .= '\''.$this->partage.'\', '.$this->prochain_loot.', "'.mysql_escape_string($this->nom).'")';
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
			$requete = 'DELETE FROM groupe WHERE id = '.$this->id;
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

		$requete = "SELECT id, partage, prochain_loot, nom FROM groupe WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new groupe($row);
				else $return[$row[$keys]][] = new groupe($row);
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
		return 'id = '.$this->id.', partage = '.$this->partage.', prochain_loot = '.$this->prochain_loot.', nom = '.$this->nom;
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
	* @return enum('r','t','l','k') $partage valeur de l'attribut partage
	*/
	function get_partage()
	{
		return $this->partage;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $prochain_loot valeur de l'attribut prochain_loot
	*/
	function get_prochain_loot()
	{
		return $this->prochain_loot;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(100) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
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
	* @param enum('r','t','l','k') $partage valeur de l'attribut
	* @return none
	*/
	function set_partage($partage)
	{
		$this->partage = $partage;
		$this->champs_modif[] = 'partage';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $prochain_loot valeur de l'attribut
	* @return none
	*/
	function set_prochain_loot($prochain_loot)
	{
		$this->prochain_loot = $prochain_loot;
		$this->champs_modif[] = 'prochain_loot';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(100) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	//fonction
	
	/**
	 * Retourne le niveau du groupe
	 * @access public
	 * @param none
	 * @return int(10) $lvl niveau du groupe 
 	*/
	function get_level()
	{
		$level_groupe = 0;
		$somme_groupe = 0;
		$somme_groupe_carre = 0;
		$niveau = 0;
		$i = 0;
		$membre_groupe = $this->get_membre_joueur();
		$count = count($membre_groupe);
		while($i < $count)
		{
			$niveau = $membre_groupe[$i]->get_level();
			$somme_groupe += $niveau;
			$somme_groupe_carre += $niveau * $niveau;
			$group[] = $niveau;
			$i++;
		}
		
		$max_groupe = max($group);
		$moy_groupe = ($somme_groupe - $max_groupe) / count($group);
		$this->lvl = $max_groupe + ceil(($somme_groupe_carre) / ($max_groupe * 25)) + 1;
		return $this->lvl;
	}
	
	function get_share_xp($pos = false, $niv_adv=1)
	{
		if(!isset($this->membre_joueur)) $this->get_membre_joueur();
		$this->share_xp = 0;
		foreach($this->membre_joueur as $membre)
		{
			$distance = calcul_distance_pytagore($pos, $membre->get_pos());
			if($distance <= 10)
			{
				$share_xp = 100 * min($membre->get_level(), $niv_adv) / max($membre->get_level(), $niv_adv);
			}
			else
			{
				$tmp = (100 - $distance * 2) * $membre->get_level();
				$share_xp = (($tmp) > 0 ? $tmp : 0);
			}
			$membre->share_xp = $share_xp;
			$this->share_xp += $share_xp;
		}
		return $this->share_xp;
	}

	function set_share_xp($share)
	{
		$this->share_xp = $share;
	}

	function get_membre()
	{
		$this->membre = groupe_joueur::create('id_groupe', $this->id, 'leader ASC, id_joueur ASC');
		return $this->membre;
	}

	function get_membre_joueur()
	{
		if(!isset($this->membre)) $this->get_membre();
		$this->membre_joueur = array();
		foreach($this->membre as $membre)
		{
			$this->membre_joueur[] = new perso($membre->get_id_joueur());
		}
		return $this->membre_joueur;
	}

	function get_id_leader()
	{
		global $db;
		$W_requete = 'SELECT id_joueur FROM groupe_joueur WHERE leader = \'y\' AND id_groupe = '.$this->get_id();
		$W_query = $db->query($W_requete);
		$W_row = $db->read_array($W_query);
		return $W_row['id_joueur'];
	}
	
	function get_leader()
	{
		return $this->get_id_leader();
	}
	
	function trouve_position_joueur($id_perso)
	{
		if(!isset($this->membre)) $this->get_membre();
		$poursuite = true;
		
		$pos = 0;
		$i = 0;
		while($poursuite && $i < count($this->membre))
		{
			if($this->membre[$i]->get_id_joueur() == $id_perso)
			{
				$pos = $i;
				$poursuite = false;
			} 
			else
				$i++;
		}
		if($poursuite)
			$pos = false;
			
		return $pos;
	}
	
	function get_place_libre()
	{
		global $db, $G_nb_joueur_groupe;
		$invitations = invitation::create('groupe', $this->get_id());
		$nb_invitation = count($invitations);
		
		$membres = $this->get_membre();
		$nb_membre = count($membres);
		
		return ($G_nb_joueur_groupe - $nb_invitation - $nb_membre + 1);
	}
}
?>
