<?php
//Inclusion de la classe abstraite objet
include_once('objet.class.php');

class Armure extends Objet
{
	protected $pp;
	protected $pm;
	protected $forceReq;
	protected $effet;
	protected $batimentReq;
	
	/**	
	   *  	Constructeur permettant la création d'une armure
	   *	Les valeurs par défaut sont celles de la base de donnée.
	   *	Le constructeur accepte plusieurs types d'appels:
	   *		-Armure() qui construit un objet "vide".
	   *		-Armure($id) qui va chercher l'objet dont l'id est $id dans la base.
	   *		-Armure($nom,...,$forceReq) qui construit un nouvel objet à partir des valeurs.
	**/
	function __construct($nom = '', $type = '', $prix = 0, $effet = '', $batimentReq = 0, $pp = 0, $pm = 0, $forceReq = 0)
	{
		//Traitement lors de l'appel Armure=($id_armure)
		$num_args = func_num_args();
		if( $num_args == 1 && is_numeric($nom) )
		{
			$requeteSQL = mysql_query($req = 'SELECT nom, pp, pm, forcex, type, effet, prix, lvl_batiment FROM armure WHERE id = '.$nom);
			//Si l'identifiant n'est pas dans la table, on remplace l'objet par un objet vide.
			if( mysql_num_rows($requeteSQL) > 0 )
			{
				//Assigne les valeurs contenues dans la base à l'objets
				list($this->nom, $this->pp, $this->pm, $this->forceReq, $this->type, $this->effet, $this->prix, $this->batimentReq) = 
				mysql_fetch_row($requeteSQL);
				//Mise à jour de l'id
				$this->id = $nom;
			}
			else
				$this->__construct();
		}
		else
		{
			parent::__construct($nom, $type, $prix);
			$this->pp = $pp;
			$this->pm = $pm;
			$this->forceReq = $forceReq;
			$this->effet = $effet;
			$this->batimentReq = $batimentReq;
		}
	}
	
	//Liste des Accesseurs
	function getPP()
	{
		return $this->pp;
	}
	
	function getPM()
	{
		return $this->pm;
	}
	
	function getEffet()
	{
		return $this->effet;
	}
	
	function getBatiment()
	{
		return $this->batimentReq;
	}
	
	function getForce()
	{
		return $this->forceReq;
	}
	
	//Liste des modifieurs
	function setPP($pp)
	{
		$this->pp = $pp;
	}
	
	function setPM($pm)
	{
		$this->pm = $pm;
	}
	
	function setEffet($effet)
	{
		$this->effet = $effet;
	}
	
	function setBatimentReq($batiment)
	{
		$this->batimentReq = $batiment;
	}
	
	function setForce($force)
	{
		$this->forceReq = $force;
	}
	
	//Fonctions de la classe abstraite
	
	//Ajout/Modification d'une armure
	function sauver()
	{
		//Verification de l'existence de l'objet dans la base
		if( $id > 0 )
		{
			$requete = 'UPDATE TABLE armure SET '.$this->modifBase().', ';
			$requete .= 'pp = "'.$this->pp.'", pm = "'.$this->pm.'", "';
			$requete .= 'forcex = "'.$this->forceReq.'", effet = "'.$this->effet.'", ';
			$requete .= 'lvl_batiment = "'.$this->batimentReq.'" WHERE id = '.$this->id;
			mysql_query($requete);
		}
		else
		{
			$requete = 'INSERT INTO armure (nom, type, prix, pp, pm, forcex, effet, lvl_batiment) VALUES(';
			$requete .= $this->insertBase().', "'.$this->pp.'", "'.$this->pm.'", "'.$this->forceReq.'", "';
			$requete .= $this->effet.'", "'.$this->batimentReq.'")';
			mysql_query($requete);
			//On récupère le dernier ID inseré. Pour mettre à jour celui de l'objet nouvellement ajouté.
			list($this->id) = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	//Permet de supprimer l'objet de la table
	function supprimer()
	{
		parent::supprimer('armure');
	}
	
	function __toString()
	{
		return parent::__toString().', '.$this->effet.', '.$this->pp.', '.$this->pm.', '.$this->forceReq.', '.$this->batimentReq;
	}
}
?>