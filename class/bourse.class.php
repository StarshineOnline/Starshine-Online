<?php
class bourse
{
	public $id_royaume;
	
	/**	
	    *  	Constructeur permettant la cr?ation d'un objet pour la gestion de messagerie d'un perso.
	**/
	function __construct($id_royaume = 0)
	{
		if($id_royaume > 0)
		{
			$this->id_royaume = $id_royaume;
		}
		else return false;
	}
	
	//R?cup?ration de tous les ench?res disponibles
	function get_encheres($tri_date = 'ASC', $where = 1)
	{
		global $db;
		$this->encheres = array();
		$requete = "SELECT id_bourse_royaume, id_royaume, ressource, nombre, id_royaume_acheteur, prix, fin_vente, actif FROM bourse_royaume WHERE ".$where." ORDER BY ressource ASC, fin_vente ".$tri_date;
		$req = $db->query($requete);
		$i = 0;
		while($row = $db->read_assoc($req))
		{
			$this->encheres[$i] = new bourse_royaume($row['id_bourse_royaume'], $row['id_royaume'], $row['ressource'], $row['nombre'], $row['id_royaume_acheteur'], $row['prix'], $row['fin_vente'], $row['actif']);
			$i++;
		}
		return $this->encheres;
	}
	
	//Finalisation d'ench?res
	function check_encheres()
	{
		global $db;
		$requete = "SELECT id_bourse_royaume, id_royaume, ressource, nombre, id_royaume_acheteur, prix, fin_vente, actif FROM bourse_royaume WHERE fin_vente < NOW() AND actif = 1";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$enchere = new bourse_royaume($row['id_bourse_royaume'], $row['id_royaume'], $row['ressource'], $row['nombre'], $row['id_royaume_acheteur'], $row['prix'], $row['fin_vente'], $row['actif']);
			//On rend l'ench?re inactive
			$enchere->actif = 0;
			$enchere->sauver();
			//Si il y a un acheteur
			if($enchere->id_royaume_acheteur != 0)
			{
				//On donne les stars au royaume concern?
				$requete = "UPDATE royaume SET star = star + ".$enchere->prix." WHERE ID = ".$enchere->id_royaume;
				$db->query($requete);
				//On donne les ressources ? l'autre royaume
				$requete = "UPDATE royaume SET ".$enchere->ressource." = ".$enchere->ressource." + ".$enchere->nombre." WHERE ID = ".$enchere->id_royaume_acheteur;
				$db->query($requete);
			}
			//Sinon on rend les ressources au royaume
			else
			{
				//On donne les ressources ? l'autre royaume
				$requete = "UPDATE royaume SET ".$enchere->ressource." = ".$enchere->ressource." + ".$enchere->nombre." WHERE ID = ".$enchere->id_royaume;
				$db->query($requete);
			}
		}
	}
}
?>