<?php
class bourse_royaume
{
	public $id_bourse_royaume;
	public $id_royaume;
	public $ressource;
	public $nombre;
	public $id_royaume_acheteur;
	public $prix;
	public $fin_vente;
	
	/**	
	    *  	Constructeur permettant la création d'un etat de message.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un etat "vide".
	    *		-Objets($id) qui va chercher l'etat dont l'id est $id_bourse_royaume dans la base.
	**/
	function __construct($id_bourse_royaume = 0, $id_royaume = 0, $ressource = 'pierre', $nombre = 0, $id_royaume_acheteur = 0, $prix = 0, $fin_vente = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_bourse_royaume) )
		{
			$requeteSQL = $db->query('SELECT id_royaume, ressource, nombre, id_royaume_acheteur, prix, fin_vente FROM bourse_royaume WHERE id_bourse_royaume = '.$id_bourse_royaume);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->ressource, $this->nombre, $this->id_royaume_acheteur, $this->prix, $this->fin_vente) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			$this->id_royaume = $id_bourse_royaume;
			$this->ressource = $ressource;
			$this->nombre = $nombre;
			$this->id_royaume_acheteur = $id_royaume_acheteur;
			$this->prix = $prix;
			$this->fin_vente = $fin_vente;
		}
		$this->id_bourse_royaume = $id_bourse_royaume;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $this->id_bourse_royaume > 0 )
		{
			$requete = 'UPDATE bourse_royaume SET ';
			$requete .= 'id_royaume = '.$this->id_royaume.', ressource = "'.$this->ressource.'", nombre = '.$this->nombre.', id_royaume_acheteur = '.$this->id_royaume_acheteur.', prix = '.$this->prix.', fin_vente = "'.$this->fin_vente.'"';
			$requete .= ' WHERE id_bourse_royaume = '.$this->id_bourse_royaume;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bourse_royaume (id_royaume, ressource, nombre, id_royaume_acheteur, prix, fin_vente) VALUES(';
			$requete .= $this->id_royaume.', "'.$this->ressouce.'", '.$this->nombre.', '.$this->id_royaume_acheteur.', '.$this->prix.', "'.$this->fin_vente.'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_bourse_royaume) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_bourse_royaume > 0 )
		{
			$requete = 'DELETE FROM bourse_royaume WHERE id_bourse_royaume = '.$this->id_bourse_royaume;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume.', '.$this->ressource.', '.$this->nombre.', '.$this->id_royaume_acheteur.', '.$this->prix.', '.$this->fin_vente;
	}
}
?>