<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Inclusion de la classe abstraite objet
include_once(root.'objet.class.php');

class ObjetRoyaume extends Objet
{
	protected $grade;
	protected $batiment;
	
	/**	
	    *  	Constructeur permettant la création d'un accessoire.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-ObjetRoyaume() qui construit un objet "vide".
	    *		-ObjetRoyaume($id) qui va chercher l'objet dont l'id est $id dans la base.
	    *		-ObjetRoyaume($nom,...,$forceReq) qui construit un nouvel objet à partir des valeurs.	   
	**/
	function __construct($nom = '', $type = '', $prix = 0, $grade = 0, $batiment = 0 )
	{
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$requeteSQL = mysql_query('SELECT nom, type, prix, grade, id_batiment FROM objet_royaume WHERE id = '.$nom);
			if( mysql_num_rows($requeteSQL) > 0 )
			{
				$this->id = $nom;
				list($this->nom, $this->type, $this->prix, $this->grade, $this->batiment) = 
				mysql_fetch_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			parent::__construct($nom, $type, $prix);
			$this->grade = $grade;
			$this->batiment = $batiment;
		}
	}
	
	//Accesseurs
	function getGrade()
	{
		return $this->grade;
	}
	
	function getBatiment()
	{
		return $this->batiment;
	}
	
	//Modifieurs
	function setGrade($grade)
	{
		$this->grade = $grade;
	}
	
	function setBatiment($batiment)
	{
		$this->batiment = $batiment;
	}
	
	//Fonctions de la classe abstraite
	
	//Fonction d'ajout/modification
	function sauver()
	{
		//Verification de l'existence de l'objet dans la base
		if( $id > 0 )
		{
			$requete = 'UPDATE TABLE objet_royaume SET '.$this->modifBase().', ';
			$requete .= 'grade = "'.$this->grade.'", id_batiment = "'.$this->batiment.'" WHERE id = '.$this->id;
			mysql_query($requete);
		}
		else
		{
			$requete = 'INSERT INTO objet_royaume (nom, type, prix, grade, id_batiment) VALUES(';
			$requete .= $this->insertBase().', "'.$this->grade.'", "'.$this->batiment.'")';
			mysql_query($requete);
			//On récupère le dernier ID inseré. Pour mettre à jour celui de l'objet nouvellement ajouté.
			list($this->id) = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
		}
	}

	//Fonction permettant de suppriemr l'objet de la base.
	function supprimer()
	{
		parent::supprimer('objet_royaume');
	}
	
	function __toString()
	{
		return parent::__toString().', '.$this->grade.', '.$this->batiment;
	}
}