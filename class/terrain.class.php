<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class terrain
{
	public $id;
	public $id_joueur;
	public $nb_case;

	/**	
		*	Constructeur permettant la création d'un terrain.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain() qui construit un etat "vide".
		*		-terrain($id) qui va chercher l'etat dont l'id est $id
		*		-terrain($array) qui associe les champs de $array é l'objet.
	**/
	function __construct($id = 0, $id_joueur = 0, $nb_case = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_joueur, nb_case FROM terrain WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_joueur, $this->nb_case) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_joueur = $id['id_joueur'];
			$this->nb_case = $id['nb_case'];
		}
		else
		{
			$this->id_joueur = $id_joueur;
			$this->nb_case = $nb_case;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain SET ';
			$requete .= 'id_joueur = '.$this->id_joueur.', nb_case = '.$this->nb_case;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain (id_joueur, nb_case) VALUES(';
			$requete .= $this->id_joueur.', '.$this->nb_case.')';
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
			$requete = 'DELETE FROM terrain WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}

	///TODO: à renommer
	static function recoverByIdJoueur($id_joueur)
	{
		global $db;
		$requete = "SELECT id, id_joueur, nb_case FROM terrain WHERE id_joueur = ".$id_joueur;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			return new terrain($db->read_assoc($req));
		}
		else return false;
	}

	function get_constructions()
	{
		global $db;
		$this->constructions = array();
		$requete = "SELECT id, id_terrain, id_batiment FROM terrain_construction WHERE id_terrain = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($res))
		{
			$this->constructions[] = new terrain_construction($row);
		}
		return $this->constructions;
	}

	function get_chantiers()
	{
		global $db;
		$this->chantiers = array();
		$requete = "SELECT id, id_terrain, id_batiment, point, star_point FROM terrain_chantier WHERE id_terrain = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($res))
		{
			$this->chantiers[] = new terrain_chantier($row);
		}
		return $this->chantiers;
	}

	function get_laboratoire()
	{
		global $db;
		$this->laboratoire = false;
		$requete = "SELECT terrain_construction.id as id, id_terrain, id_batiment FROM terrain_construction LEFT JOIN terrain_batiment ON terrain_construction.id_batiment = terrain_batiment.id WHERE id_terrain = ".$this->id." AND type = 'laboratoire'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($res))
		{
			$this->laboratoire = new terrain_construction($row);
		}
		return $this->laboratoire;
	}

	function place_restante($force = false)
	{
		if(!isset($this->place_restante) OR !$force)
		{
			global $db;
			$place_total = 0;
			//Nombre de construction
			$requete = "SELECT nb_case FROM terrain_construction LEFT JOIN terrain_batiment ON terrain_construction.id_batiment = terrain_batiment.id WHERE id_terrain = ".$this->id;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$place_total += $row['nb_case'];
			}
			//Nombre de batiment en construction
			$requete = "SELECT nb_case FROM terrain_chantier LEFT JOIN terrain_batiment ON terrain_chantier.id_batiment = terrain_batiment.id WHERE id_terrain = ".$this->id;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$place_total += $row['nb_case'];
			}
			$this->place_restante = $this->nb_case - $place_total;
		}
		return $this->place_restante;
	}
}
?>