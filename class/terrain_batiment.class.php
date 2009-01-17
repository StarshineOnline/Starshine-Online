<?php
class terrain_batiment
{
	public $id;
	public $nom;
	public $description;
	public $type;
	public $effet;
	public $nb_case;
	public $prix;
	public $requis;
	public $point_structure;
		
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_batiment() qui construit un etat "vide".
		*		-terrain_batiment($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_batiment($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = 0, $description = 0, $type = 0, $effet = 0, $nb_case = 0, $prix = 0, $requis = 0, $point_structure = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, type, effet, nb_case, prix, requis, point_structure FROM terrain_batiment WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->type, $this->effet, $this->nb_case, $this->prix, $this->requis, $this->point_structure) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->type = $id['type'];
			$this->effet = $id['effet'];
			$this->nb_case = $id['nb_case'];
			$this->prix = $id['prix'];
			$this->requis = $id['requis'];
			$this->point_structure = $id['point_structure'];
		}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->type = $type;
			$this->effet = $effet;
			$this->nb_case = $nb_case;
			$this->prix = $prix;
			$this->requis = $requis;
			$this->point_structure = $point_structure;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain_batiment SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'", type = "'.$this->type.'", effet = "'.$this->effet.'", nb_case = '.$this->nb_case.', prix = '.$this->prix.', requis = '.$this->requis.', point_structure = '.$this->point_structure;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_batiment (nom, description, type, effet, nb_case, prix, requis, point_structure) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'", "'.$this->type.'", "'.$this->effet.'", '.$this->nb_case.', '.$this->prix.', '.$this->requis.', '.$this->point_structure.')';
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
			$requete = 'DELETE FROM terrain_batiment WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', type = '.$this->type.', effet = '.$this->effet.', nb_case = '.$this->nb_case.', prix = '.$this->prix.', requis = '.$this->requis.', point_structure = '.$this->point_structure;
	}
}
?>