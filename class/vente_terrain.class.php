<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class vente_terrain
{
	public $id;
	public $id_royaume;
	public $date_fin;
	public $id_joueur;
	public $prix;

	/**	
		*	Constructeur permettant la cr�ation d'une cente_terrain.
		*	Les valeurs nombrer d�faut sont celles de la base de donn�e.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-vente_terrain() qui construit un etat "vide".
		*		-vente_terrain($id) qui va chercher l'etat dont l'id est $id
		*		-vente_terrain($array) qui associe les champs de $array � l'objet.
	**/
	function __construct($id = 0, $id_royaume = 0, $date_fin = 0, $id_joueur = 0, $prix = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_royaume, date_fin, id_joueur, prix FROM vente_terrain WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on cr�e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->date_fin, $this->id_joueur, $this->prix) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['id_royaume'];
			$this->date_fin = $id['date_fin'];
			$this->id_joueur = $id['id_joueur'];
			$this->prix = $id['prix'];
		}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->date_fin = $date_fin;
			$this->id_joueur = $id_joueur;
			$this->prix = $prix;
			$this->id = $id;
		}
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE vente_terrain SET ';
			$requete .= 'id_royaume = '.$this->id_royaume.', date_fin = '.$this->date_fin.', id_joueur = '.$this->id_joueur.', prix = '.$this->prix;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO vente_terrain (id_royaume, date_fin, id_joueur, prix) VALUES(';
			$requete .= $this->id_royaume.', '.$this->date_fin.', '.$this->id_joueur.', '.$this->prix.')';
			$db->query($requete);
			//R�cuperation du dernier ID ins�r�.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM vente_terrain WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}

	function prochain_prix()
	{
		return round($this->prix * 1.1);
	}
	
	function enchere($id_joueur)
	{
		global $db;
		//On rend l'argent � l'ancien joueur
		if($this->id_joueur != 0)
		{
			$requete = "UPDATE perso SET star = star + ".$this->prix." WHERE ID = ".$this->id_joueur;
			$db->query($requete);
		}
		$this->prix = $this->prochain_prix();
		$this->id_joueur = $id_joueur;
		$temps_restant = $this->date_fin - time();
		$h4 = 60 * 60 * 4;
		//Si il reste moins de 4 heures, on repousse la vente de 24 heures
		if($temps_restant < $h4)
		{
			$h24 = 60 * 60 * 24;
			$this->date_fin += $h24;
		}
		$this->sauver();
		//On prend l'argent au nouveau joueur
		$requete = "UPDATE perso SET star = star - ".$this->prix." WHERE ID = ".$this->id_joueur;
		$db->query($requete);
	}
	
	function verif_joueur($joueur)
	{
		global $Trace, $db;
		//On v�rifie qu'il a assez de star
		if($joueur->get_star() < $this->prochain_prix())
		{
			$this->erreur = 'star';
			return false;
		}
		//On v�rifie qu'il est du bon royaume
		elseif($Trace[$joueur->get_race()]['numrace'] != $this->id_royaume)
		{
			$this->erreur = 'race';
			return false;
		}
		//On v�rifie qu'il a pas d�j� de terrain
		else
		{
			$requete = "SELECT id_joueur FROM terrain WHERE id_joueur = ".$joueur->get_id();
			$req = $db->query($requete);
			if($db->num_rows($req) > 0)
			{
				$this->erreur = 'terrain';
				return false;
			}
			//On v�rifie qu'il a pas d�j� d'ench�re
			else
			{
				$requete = "SELECT id_joueur FROM vente_terrain WHERE id_joueur = ".$joueur->get_id();
				$req = $db->query($requete);
				if($db->num_rows($req) > 0)
				{
					$this->erreur = 'enchere';
					return false;
				}
				else return true;
			}
		}
	}
}
?>