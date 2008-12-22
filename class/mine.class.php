<?php
class mine extends construction
{
	/**	
	    *  	Constructeur permettant la création d'une mine.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-mine() qui construit un etat "vide".
	    *		-mine($id) qui va chercher l'etat dont l'id est $id
	    *		-mine($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, rez, rechargement, image FROM construction WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->rez, $this->rechargement, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['royaume'];
			$this->id_batiment = $id['id_batiment'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->hp = $id['hp'];
			$this->nom = $id['nom'];
			$this->rez = $id['rez'];
			$this->rechargement = $id['rechargement'];
			$this->image = $id['image'];
		}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->id_batiment = $id_batiment;
			$this->x = $x;
			$this->y = $y;
			$this->hp = $hp;
			$this->nom = $nom;
			$this->rez = $rez;
			$this->rechargement = $rechargement;
			$this->image = $image;
			$this->id = $id;
		}
		$this->type = 'mine';
	}

	function get_ressources()
	{
		global $db;
		$requete = "SELECT info FROM map WHERE ID = ".convert_in_pos($this->x, $this->y)."";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$terrain = type_terrain($row['info']);
		$this->ressources = ressource_terrain($terrain[1]);
		$requete = "SELECT bonus1, bonus2 FROM batiment WHERE id = ".$this->id_batiment;
		$req = $db->query($requete);
		$batiment = $db->read_assoc($req);
		if($batiment['bonus2'] != 0)
		{
			switch($batiment['bonus2'])
			{
				case 1 :
					$this->ressources = array('Pierre' => $batiment['bonus1'] * $this->ressources['Pierre']);
				break;
				case 2 :
					$this->ressources = array('Bois' => $batiment['bonus1'] * $this->ressources['Bois']);
				break;
				case 3 :
					$this->ressources = array('Eau' => $batiment['bonus1'] * $this->ressources['Eau']);
				break;
				case 4 :
					$this->ressources = array('Sable' => $batiment['bonus1'] * $this->ressources['Sable']);
				break;
				case 5 :
					$this->ressources = array('Nourriture' => $batiment['bonus1'] * $this->ressources['Nourriture']);
				break;
				case 6 :
					$this->ressources = array('Star' => $batiment['bonus1'] * $this->ressources['Star']);
				break;
				case 7 :
					$this->ressources = array('Charbon' => $batiment['bonus1'] * $this->ressources['Charbon']);
				break;
				case 8 :
					$this->ressources = array('Essence Magique' => $batiment['bonus1'] * $this->ressources['Essence Magique']);
				break;
			}
		}
		else
		{
			foreach($this->ressources as $key => $value)
			{
				$this->ressources[$key] = $batiment['bonus1'] * $value;
			}
		}
	}
	
	function get_evolution()
	{
		global $db;
		$requete = "SELECT * FROM batiment WHERE cond1 = ".$mine->id_batiment;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		
		$this->evolution[] = $row;
	}
	
	function get_hp_max()
	{
		global $db;
		$requete = "SELECT hp FROM batiment WHERE id = ".$mine->id_batiment;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		
		$this->hp_max = $row['hp'];
		return $this->hp_max;
	}
}
?>