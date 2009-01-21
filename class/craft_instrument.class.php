<?php
class craft_instrument
{
	public $id;
	public $nom;
	public $type;
	public $description;
	public $requis;
	public $pa;
	public $mp;
	public $prix;
	public $alchimie;
	
	/**	
		*	Constructeur permettant la cr?ation d'un terrain_batiment.
		*	Les valeurs nombrer d?faut sont celles de la base de donn?e.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_instrument() qui construit un etat "vide".
		*		-craft_instrument($id) qui va chercher l'etat dont l'id est $id
		*		-craft_instrument($array) qui associe les champs de $array ? l'objet.
	**/
	function __construct($id = 0, $nom = '', $type = '', $description = '', $requis = 0, $pa = 0, $mp = 0, $prix = 0, $alchimie = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, type, description, requis, pa, mp, prix, alchimie FROM craft_instrument WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->type, $this->description, $this->requis, $this->pa, $this->mp, $this->prix, $this->alchimie) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->type = $id['type'];
			$this->description = $id['description'];
			$this->requis = $id['requis'];
			$this->pa = $id['pa'];
			$this->mp = $id['mp'];
			$this->prix = $id['prix'];
			$this->alchimie = $id['alchimie'];
					}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->description = $description;
			$this->requis = $requis;
			$this->pa = $pa;
			$this->mp = $mp;
			$this->prix = $prix;
			$this->alchimie = $alchimie;
						$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_instrument SET ';
			$requete .= 'nom = "'.$this->nom.'", type = "'.$this->type.'", description = "'.$this->description.'", requis = '.$this->requis.', pa = '.$this->pa.', mp = '.$this->mp.', prix = '.$this->prix.', alchimie = '.$this->alchimie;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_instrument (nom, type, description, requis, pa, mp, prix, alchimie) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->type.'", "'.$this->description.'", '.$this->requis.', '.$this->pa.', '.$this->mp.', '.$this->prix.', '.$this->alchimie.')';
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
			$requete = 'DELETE FROM craft_instrument WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', nom = '.$this->nom.', type = '.$this->type.', description = '.$this->description.', requis = '.$this->requis.', pa = '.$this->pa.', mp = '.$this->mp.', prix = '.$this->prix.', alchimie = '.$this->alchimie;
	}
}
?>