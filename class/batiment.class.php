<?php
class batiment
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
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var int(11)
    */
	private $cout;

	/**
    * @access private
    * @var int(11)
    */
	private $entretien;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $cond1;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $cond2;

	/**
    * @access private
    * @var int(10)
    */
	private $hp;

	/**
    * @access private
    * @var int(10)
    */
	private $PP;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $PM;

	/**
    * @access private
    * @var int(10)
    */
	private $carac;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus1;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus2;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus3;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus4;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus5;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus6;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $bonus7;

	/**
    * @access private
    * @var int(11)
    */
	private $upgrade;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $augmentation_pa;

	/**
    * @access private
    * @var int(10)
    */
	private $temps_construction;

	/**
    * @access private
    * @var varchar(50)
    */
	private $image;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $point_victoire;

	
	/**
	* @access public

	* @param mediumint(8) id attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param varchar(50) type attribut
	* @param int(11) cout attribut
	* @param int(11) entretien attribut
	* @param mediumint(9) cond1 attribut
	* @param mediumint(9) cond2 attribut
	* @param int(10) hp attribut
	* @param int(10) PP attribut
	* @param mediumint(8) PM attribut
	* @param int(10) carac attribut
	* @param mediumint(9) bonus1 attribut
	* @param mediumint(9) bonus2 attribut
	* @param mediumint(9) bonus3 attribut
	* @param mediumint(9) bonus4 attribut
	* @param mediumint(9) bonus5 attribut
	* @param mediumint(9) bonus6 attribut
	* @param mediumint(9) bonus7 attribut
	* @param int(11) upgrade attribut
	* @param tinyint(3) augmentation_pa attribut
	* @param int(10) temps_construction attribut
	* @param varchar(50) image attribut
	* @param tinyint(3) point_victoire attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $type = '', $cout = '', $entretien = '', $cond1 = '', $cond2 = '', $hp = '', $PP = '', $PM = '', $carac = '', $bonus1 = '', $bonus2 = '', $bonus3 = '', $bonus4 = '', $bonus5 = '', $bonus6 = '', $bonus7 = '', $upgrade = '', $augmentation_pa = '', $temps_construction = '', $image = '', $point_victoire = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, description, type, cout, entretien, cond1, cond2, hp, PP, PM, carac, bonus1, bonus2, bonus3, bonus4, bonus5, bonus6, bonus7, upgrade, augmentation_pa, temps_construction, image, point_victoire FROM batiment WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->type, $this->cout, $this->entretien, $this->cond1, $this->cond2, $this->hp, $this->PP, $this->PM, $this->carac, $this->bonus1, $this->bonus2, $this->bonus3, $this->bonus4, $this->bonus5, $this->bonus6, $this->bonus7, $this->upgrade, $this->augmentation_pa, $this->temps_construction, $this->image, $this->point_victoire) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->type = $id['type'];
			$this->cout = $id['cout'];
			$this->entretien = $id['entretien'];
			$this->cond1 = $id['cond1'];
			$this->cond2 = $id['cond2'];
			$this->hp = $id['hp'];
			$this->PP = $id['PP'];
			$this->PM = $id['PM'];
			$this->carac = $id['carac'];
			$this->bonus1 = $id['bonus1'];
			$this->bonus2 = $id['bonus2'];
			$this->bonus3 = $id['bonus3'];
			$this->bonus4 = $id['bonus4'];
			$this->bonus5 = $id['bonus5'];
			$this->bonus6 = $id['bonus6'];
			$this->bonus7 = $id['bonus7'];
			$this->upgrade = $id['upgrade'];
			$this->augmentation_pa = $id['augmentation_pa'];
			$this->temps_construction = $id['temps_construction'];
			$this->image = $id['image'];
			$this->point_victoire = $id['point_victoire'];
			}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->type = $type;
			$this->cout = $cout;
			$this->entretien = $entretien;
			$this->cond1 = $cond1;
			$this->cond2 = $cond2;
			$this->hp = $hp;
			$this->PP = $PP;
			$this->PM = $PM;
			$this->carac = $carac;
			$this->bonus1 = $bonus1;
			$this->bonus2 = $bonus2;
			$this->bonus3 = $bonus3;
			$this->bonus4 = $bonus4;
			$this->bonus5 = $bonus5;
			$this->bonus6 = $bonus6;
			$this->bonus7 = $bonus7;
			$this->upgrade = $upgrade;
			$this->augmentation_pa = $augmentation_pa;
			$this->temps_construction = $temps_construction;
			$this->image = $image;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", type = "'.mysql_escape_string($this->type).'", cout = "'.mysql_escape_string($this->cout).'", entretien = "'.mysql_escape_string($this->entretien).'", cond1 = "'.mysql_escape_string($this->cond1).'", cond2 = "'.mysql_escape_string($this->cond2).'", hp = "'.mysql_escape_string($this->hp).'", PP = "'.mysql_escape_string($this->PP).'", PM = "'.mysql_escape_string($this->PM).'", carac = "'.mysql_escape_string($this->carac).'", bonus1 = "'.mysql_escape_string($this->bonus1).'", bonus2 = "'.mysql_escape_string($this->bonus2).'", bonus3 = "'.mysql_escape_string($this->bonus3).'", bonus4 = "'.mysql_escape_string($this->bonus4).'", bonus5 = "'.mysql_escape_string($this->bonus5).'", bonus6 = "'.mysql_escape_string($this->bonus6).'", bonus7 = "'.mysql_escape_string($this->bonus7).'", upgrade = "'.mysql_escape_string($this->upgrade).'", augmentation_pa = "'.mysql_escape_string($this->augmentation_pa).'", temps_construction = "'.mysql_escape_string($this->temps_construction).'", image = "'.mysql_escape_string($this->image).'", point_victoire = "'.mysql_escape_string($this->point_victoire).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE batiment SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO batiment (nom, description, type, cout, entretien, cond1, cond2, hp, PP, PM, carac, bonus1, bonus2, bonus3, bonus4, bonus5, bonus6, bonus7, upgrade, augmentation_pa, temps_construction, image, point_victoire) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->cout).'", "'.mysql_escape_string($this->entretien).'", "'.mysql_escape_string($this->cond1).'", "'.mysql_escape_string($this->cond2).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->PP).'", "'.mysql_escape_string($this->PM).'", "'.mysql_escape_string($this->carac).'", "'.mysql_escape_string($this->bonus1).'", "'.mysql_escape_string($this->bonus2).'", "'.mysql_escape_string($this->bonus3).'", "'.mysql_escape_string($this->bonus4).'", "'.mysql_escape_string($this->bonus5).'", "'.mysql_escape_string($this->bonus6).'", "'.mysql_escape_string($this->bonus7).'", "'.mysql_escape_string($this->upgrade).'", "'.mysql_escape_string($this->augmentation_pa).'", "'.mysql_escape_string($this->temps_construction).'", "'.mysql_escape_string($this->image).'", "'.mysql_escape_string($this->point_victoire).'")';
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
			$requete = 'DELETE FROM batiment WHERE id = '.$this->id;
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

		$requete = "SELECT id, nom, description, type, cout, entretien, cond1, cond2, hp, PP, PM, carac, bonus1, bonus2, bonus3, bonus4, bonus5, bonus6, bonus7, upgrade, augmentation_pa, temps_construction, image, point_victoire FROM batiment WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new batiment($row);
				else $return[$row[$keys]] = new batiment($row);
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
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', type = '.$this->type.', cout = '.$this->cout.', entretien = '.$this->entretien.', cond1 = '.$this->cond1.', cond2 = '.$this->cond2.', hp = '.$this->hp.', PP = '.$this->PP.', PM = '.$this->PM.', carac = '.$this->carac.', bonus1 = '.$this->bonus1.', bonus2 = '.$this->bonus2.', bonus3 = '.$this->bonus3.', bonus4 = '.$this->bonus4.', bonus5 = '.$this->bonus5.', bonus6 = '.$this->bonus6.', bonus7 = '.$this->bonus7.', upgrade = '.$this->upgrade.', augmentation_pa = '.$this->augmentation_pa.', temps_construction = '.$this->temps_construction.', image = '.$this->image.', point_victoire = '.$this->point_victoire;
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
	* @return int(11) $cout valeur de l'attribut cout
	*/
	function get_cout()
	{
		return $this->cout;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $entretien valeur de l'attribut entretien
	*/
	function get_entretien()
	{
		return $this->entretien;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $cond1 valeur de l'attribut cond1
	*/
	function get_cond1()
	{
		return $this->cond1;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $cond2 valeur de l'attribut cond2
	*/
	function get_cond2()
	{
		return $this->cond2;
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
	* @return int(10) $PP valeur de l'attribut PP
	*/
	function get_PP()
	{
		return $this->PP;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $PM valeur de l'attribut PM
	*/
	function get_PM()
	{
		return $this->PM;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $carac valeur de l'attribut carac
	*/
	function get_carac()
	{
		return $this->carac;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus1 valeur de l'attribut bonus1
	*/
	function get_bonus1()
	{
		return $this->bonus1;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus2 valeur de l'attribut bonus2
	*/
	function get_bonus2()
	{
		return $this->bonus2;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus3 valeur de l'attribut bonus3
	*/
	function get_bonus3()
	{
		return $this->bonus3;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus4 valeur de l'attribut bonus4
	*/
	function get_bonus4()
	{
		return $this->bonus4;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus5 valeur de l'attribut bonus5
	*/
	function get_bonus5()
	{
		return $this->bonus5;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus6 valeur de l'attribut bonus6
	*/
	function get_bonus6()
	{
		return $this->bonus6;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $bonus7 valeur de l'attribut bonus7
	*/
	function get_bonus7()
	{
		return $this->bonus7;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $upgrade valeur de l'attribut upgrade
	*/
	function get_upgrade()
	{
		return $this->upgrade;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $augmentation_pa valeur de l'attribut augmentation_pa
	*/
	function get_augmentation_pa()
	{
		return $this->augmentation_pa;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $temps_construction valeur de l'attribut temps_construction
	*/
	function get_temps_construction()
	{
		return $this->temps_construction;
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
	* @return tinyint(3) $point_victoire valeur de l'attribut point_victoire
	*/
	function get_point_victoire()
	{
		return $this->point_victoire;
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
	* @param int(11) $cout valeur de l'attribut
	* @return none
	*/
	function set_cout($cout)
	{
		$this->cout = $cout;
		$this->champs_modif[] = 'cout';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $entretien valeur de l'attribut
	* @return none
	*/
	function set_entretien($entretien)
	{
		$this->entretien = $entretien;
		$this->champs_modif[] = 'entretien';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $cond1 valeur de l'attribut
	* @return none
	*/
	function set_cond1($cond1)
	{
		$this->cond1 = $cond1;
		$this->champs_modif[] = 'cond1';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $cond2 valeur de l'attribut
	* @return none
	*/
	function set_cond2($cond2)
	{
		$this->cond2 = $cond2;
		$this->champs_modif[] = 'cond2';
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
	* @param int(10) $PP valeur de l'attribut
	* @return none
	*/
	function set_PP($PP)
	{
		$this->PP = $PP;
		$this->champs_modif[] = 'PP';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $PM valeur de l'attribut
	* @return none
	*/
	function set_PM($PM)
	{
		$this->PM = $PM;
		$this->champs_modif[] = 'PM';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $carac valeur de l'attribut
	* @return none
	*/
	function set_carac($carac)
	{
		$this->carac = $carac;
		$this->champs_modif[] = 'carac';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus1 valeur de l'attribut
	* @return none
	*/
	function set_bonus1($bonus1)
	{
		$this->bonus1 = $bonus1;
		$this->champs_modif[] = 'bonus1';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus2 valeur de l'attribut
	* @return none
	*/
	function set_bonus2($bonus2)
	{
		$this->bonus2 = $bonus2;
		$this->champs_modif[] = 'bonus2';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus3 valeur de l'attribut
	* @return none
	*/
	function set_bonus3($bonus3)
	{
		$this->bonus3 = $bonus3;
		$this->champs_modif[] = 'bonus3';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus4 valeur de l'attribut
	* @return none
	*/
	function set_bonus4($bonus4)
	{
		$this->bonus4 = $bonus4;
		$this->champs_modif[] = 'bonus4';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus5 valeur de l'attribut
	* @return none
	*/
	function set_bonus5($bonus5)
	{
		$this->bonus5 = $bonus5;
		$this->champs_modif[] = 'bonus5';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus6 valeur de l'attribut
	* @return none
	*/
	function set_bonus6($bonus6)
	{
		$this->bonus6 = $bonus6;
		$this->champs_modif[] = 'bonus6';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $bonus7 valeur de l'attribut
	* @return none
	*/
	function set_bonus7($bonus7)
	{
		$this->bonus7 = $bonus7;
		$this->champs_modif[] = 'bonus7';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $upgrade valeur de l'attribut
	* @return none
	*/
	function set_upgrade($upgrade)
	{
		$this->upgrade = $upgrade;
		$this->champs_modif[] = 'upgrade';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $augmentation_pa valeur de l'attribut
	* @return none
	*/
	function set_augmentation_pa($augmentation_pa)
	{
		$this->augmentation_pa = $augmentation_pa;
		$this->champs_modif[] = 'augmentation_pa';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $temps_construction valeur de l'attribut
	* @return none
	*/
	function set_temps_construction($temps_construction)
	{
		$this->temps_construction = $temps_construction;
		$this->champs_modif[] = 'temps_construction';
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
	* @param tinyint(3) $point_victoire valeur de l'attribut
	* @return none
	*/
	function set_point_victoire($point_victoire)
	{
		$this->point_victoire = $point_victoire;
		$this->champs_modif[] = 'point_victoire';
	}

//fonction
	function get_image_full($root, $resolution = 'high')
	{
		if($resolution != 'high') $image = $root."image/batiment_low/";
		else $image = $root."image/batiment/";

		if(file_exists($image.$this->image."_04.png")) 		{ $image .= $this->image."_04.png"; }
		elseif(file_exists($image.$this->image."_04.gif")) 	{ $image .= $this->image."_04.gif"; }
		else 														{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
		return $image;
	}

	function get_suivant()
	{
		return $this->upgrade;
	}
}
