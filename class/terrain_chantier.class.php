<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class terrain_chantier extends table
{
	public $id;
	public $id_terrain;
	public $id_batiment;
	public $point;
	public $star_point;
	public $upgrade_id_construction;
		
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_chantier() qui construit un etat "vide".
		*		-terrain_chantier($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_chantier($array) qui associe les champs de $array é l'objet.
	**/
	function __construct($id = 0, $id_terrain = 0, $id_batiment = 0, $point = 0, $star_point = 0, $upgrade_id_construction = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_terrain, id_batiment, point, star_point, upgrade_id_construction FROM terrain_chantier WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_terrain, $this->id_batiment, $this->point, $this->star_point, $this->upgrade_id_construction) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_terrain = $id['id_terrain'];
			$this->id_batiment = $id['id_batiment'];
			$this->point = $id['point'];
			$this->star_point = $id['star_point'];
			$this->upgrade_id_construction = $id['upgrade_id_construction'];
		}
		else
		{
			$this->id_terrain = $id_terrain;
			$this->id_batiment = $id_batiment;
			$this->point = $point;
			$this->star_point = $star_point;
			$this->upgrade_id_construction = $upgrade_id_construction;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain_chantier SET ';
			$requete .= 'id_terrain = '.$this->id_terrain.', id_batiment = '.$this->id_batiment.', point = '.$this->point.', star_point = '.$this->star_point.', upgrade_id_construction = '.$this->upgrade_id_construction;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_chantier (id_terrain, id_batiment, point, star_point, upgrade_id_construction) VALUES(';
			$requete .= $this->id_terrain.', '.$this->id_batiment.', '.$this->point.', '.$this->star_point.', '.$this->upgrade_id_construction.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM terrain_chantier WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_terrain = '.$this->id_terrain.', id_batiment = '.$this->id_batiment.', point = '.$this->point.', star_point = '.$this->star_point.', upgrade_id_construction = '.$this->upgrade_id_construction;
	}

	function get_batiment()
	{
		global $db;
		$requete = "SELECT id, nom, description, type, effet, nb_case, prix, requis, point_structure FROM terrain_batiment WHERE id = ".$this->id_batiment;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return new terrain_batiment($row);
	}
	
	function get_id_batiment()
	{
		return $this->id_batiment;
	}
}
?>