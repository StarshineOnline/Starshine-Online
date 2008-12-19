<?php
class mine
{
	public $id_mine;
	public $id_royaume;
	public $id_batiment;
	public $x;
	public $y;
	public $hp;
	public $nom;
	public $type;
	public $rez;
	public $rechargement;
	public $image;
	
	/**	
	    *  	Constructeur permettant la création d'un etat de message.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un etat "vide".
	    *		-Objets($id) qui va chercher l'etat dont l'id est $id_bourse_royaume dans la base.
	**/
	function __construct($id_mine = 0, $id_royaume = 0, $id_batiment = 0, $x = 0, $y = 0, $hp = 0, $nom = '', $rez = 0, $rechargement = 0, $image = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_mine) )
		{
			$requeteSQL = $db->query('SELECT royaume, id_batiment, x, y, hp, nom, rez, rechargement, image FROM construction WHERE id = '.$id_mine);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->x, $this->y, $this->hp, $this->nom, $this->rez, $this->rechargement, $this->image) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_mine = $id_mine;
		}
		elseif( (func_num_args() == 1) && is_array($id_mine) )
		{
			$this->id_mine = $id_mine['id'];
			$this->id_royaume = $id_mine['royaume'];
			$this->id_batiment = $id_mine['id_batiment'];
			$this->x = $id_mine['x'];
			$this->y = $id_mine['y'];
			$this->hp = $id_mine['hp'];
			$this->nom = $id_mine['nom'];
			$this->rez = $id_mine['rez'];
			$this->rechargement = $id_mine['rechargement'];
			$this->image = $id_mine['image'];
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
			$this->id_mine = $id_mine;
		}
		$this->type = 'mine';
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id_mine > 0 )
		{
			$requete = 'UPDATE construction SET ';
			$requete .= 'royaume = '.$this->id_royaume.', id_batiment = '.$this->id_batiment.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', nom = "'.$this->nom.'", rez = '.$this->rez.', rechargement = '.$this->rechargement.', image = "'.$this->image.'"';
			$requete .= ' WHERE id = '.$this->id_mine;
			//echo $requete;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO construction (royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image) VALUES(';
			$requete .= $this->id_royaume.', '.$this->id_batiment.', '.$this->x.', '.$this->y.', '.$this->hp.', "'.$this->nom.'", "'.$this->type.'", '.$this->rez.', '.$this->rechargement.', "'.$this->image.'")';
			//echo $requete;
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_mine) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_mine > 0 )
		{
			$requete = 'DELETE FROM construction WHERE id_mine = '.$this->id_mine;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}
	
	function get_ressources()
	{
		global $db;
		$requete = "SELECT info FROM map WHERE ID = ((".$this->x." * 1000) + ".$this->y.")";
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
}
?>