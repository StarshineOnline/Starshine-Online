<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class craft_recette
{
	public $id;
	public $nom;
	public $description;
	public $pa;
	public $mp;
	public $type;
	public $difficulte;
	public $resultat;
	public $royaume_alchimie;
	public $prix;

	/**	
		*	Constructeur permettant la cr?ation d'une recette.
		*	Les valeurs par d?faut sont celles de la base de donn?e.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-craft_recette() qui construit un etat "vide".
		*		-craft_recette($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recette($array) qui associe les champs de $array ? l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '', $pa = 0, $mp = 0, $type = '', $difficulte = 0, $resultat = '', $royaume_alchimie = 0, $prix = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, pa, mp, type, difficulte, resultat, royaume_alchimie, prix FROM craft_recette WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->pa, $this->mp, $this->type, $this->difficulte, $this->resultat, $this->royaume_alchimie, $this->prix) = $db->read_row($requeteSQL);
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
			$this->pa = $id['pa'];
			$this->mp = $id['mp'];
			$this->type = $id['type'];
			$this->difficulte = $id['difficulte'];
			$this->resultat = $id['resultat'];
			$this->royaume_alchimie = $id['royaume_alchimie'];
			$this->prix = $id['prix'];
		}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->pa = $pa;
			$this->mp = $mp;
			$this->type = $type;
			$this->difficulte = $difficulte;
			$this->id = $id;
			$this->resultat = $resultat;
			$this->royaume_alchimie = $royaume_alchimie;
			$this->prix = $prix;
		}
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recette SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'", pa = '.$this->pa.', mp = '.$this->mp.', type = "'.$this->type.'", difficulte = '.$this->difficulte.', resultat = "'.$this->resultat.'", royaume_alchimie = '.$this->royaume_alchimie.', prix = '.$this->prix;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recette (nom, description, pa, mp, type, difficulte, resultat, royaume_alchimie, prix) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'", '.$this->pa.', '.$this->mp.', "'.$this->type.'", '.$this->difficulte.', "'.$this->resultat.'", '.$this->royaume_alchimie.', '.$this->prix.')';
			$db->query($requete);
			//R?cuperation du dernier ID ins?r?.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM craft_recette WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}
	
	function get_ingredients()
	{
		global $db;
		$this->ingredients = array();
		$requete = "SELECT id_recette, id_ingredient, nombre, secret FROM craft_recette_ingredient WHERE id_recette = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->ingredients[] = new craft_recette_ingredient($row);
		}
		return $this->ingredients;
	}

	function get_instruments()
	{
		global $db;
		$this->instruments = array();
		$requete = "SELECT id, id_recette, type FROM craft_recette_instrument WHERE id_recette = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->instruments[] = new craft_recette_instrument($row);
		}
		return $this->instruments;
	}

	function get_recipients()
	{
		global $db;
		$this->recipients = array();
		$requete = "SELECT id, id_recette, id_objet, resultat, prefixe FROM craft_recette_recipient WHERE id_recette = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->recipients[] = new craft_recette_recipient($row);
		}
		return $this->recipients;
	}

	function get_info_joueur($joueur, $R)
	{
		global $db/*, $R*/;
		$types = array();
		$types['mortier'] = array();
		$types['four'] = array();
		$types['cornue'] = array();
		//Si on est en ville
		if(verif_ville($joueur->get_x(), $joueur->get_y()))
		{
			if($R->get_diplo($joueur->get_race()) == 127)
			{
				//On récupère toutes les infos sur le labo du joueur (ou pas)
				$terrain = new terrain();
				$terrain = $terrain->recoverByIdJoueur($joueur->get_id());
				//Si il poss?de un terrain
				if(is_object($terrain))
				{
					if($terrain->get_laboratoire())
					{
						$instruments = $terrain->laboratoire->get_laboratoire_instrument();
						foreach($instruments as $instrument)
						{
							$instru = $instrument->get_instrument();
							$types[$instru->type]['pa'] = $instru->pa;
							$types[$instru->type]['mp'] = $instru->mp;
							$types[$instru->type]['cout'] = 0;
						}
					}
				}
			}
			//La ville
			foreach($types as $key => $type)
			{
				if(count($type) == 0)
				{
					$requete = "SELECT pa, mp, prix FROM craft_instrument WHERE type = '".$key."' AND requis = 0";
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$taxe = 1 + ($R->get_taxe_diplo($joueur->get_race()) / 100);
					$prix = round($row['prix'] * $taxe / 100);
					$types[$key]['pa'] = $row['pa'];
					$types[$key]['mp'] = $row['mp'];
					$types[$key]['cout'] = $prix;
				}
			}
		}
		return $types;
	}
}
?>