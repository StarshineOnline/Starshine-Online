<?php
class bourse
{
	public $id_royaume;
	
	/**	
	    *  	Constructeur permettant la création d'un objet pour la gestion de messagerie d'un perso.
	**/
	function __construct($id_royaume = 0)
	{
		if($id_royaume > 0)
		{
			$this->id_royaume = $id_royaume;
		}
		else return false;
	}
	
	//Récupération de tous les enchères disponibles
	function get_encheres($tri_date = 'ASC')
	{
		global $db;
		$where = 1;
		$this->encheres = array();
		$requete = "SELECT id_bourse_royaume, id_royaume, ressource, nombre, id_royaume_acheteur, prix, fin_vente FROM bourse_royaume WHERE ".$where." ORDER BY ressource ASC, fin_vente ".$tri_date;
		$req = $db->query($requete);
		$i = 0;
		while($row = $db->read_assoc($req))
		{
			$this->encheres[$i] = new bourse_royaume($row['id_bourse_royaume'], $row['id_royaume'], $row['ressource'], $row['nombre'], $row['id_royaume_acheteur'], $row['prix'], $row['fin_vente']);
			$i++;
		}
		return $this->encheres;
	}
}
?>