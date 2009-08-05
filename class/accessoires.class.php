<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Inclusion de la classe abstraite objet
include_once(root.'objet.class.php');

class Accessoire extends Objet
{
	protected $puissance;
	protected $description;
	protected $effet;
	protected $batimentReq;
	protected $achetable;
	
	/**	
	    *  	Constructeur permettant la création d'un accessoire.
	    *	Les valeurs par défaut sont celles de la base de donnée.
    	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Accessoire() qui construit un objet "vide".
	    *		-Accessoire($id) qui va chercher l'objet dont l'id est $id dans la base.
	    *		-Accessoire($nom,...,$forceReq) qui construit un nouvel objet à partir des valeurs.	   
	**/
	function __construct($nom = '', $type = '', $prix = 0, $effet = 0, $batimentReq = 0, $description = '', $puissance = 0, $achetable = 'y')
	{
		//on verifie le nombre d'arguments
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$requeteSQL = mysql_query('SELECT nom, type, prix, effet, lvl_batiment, description, puissance, achetable FROM accessoire WHERE id = '.$nom);
			if( mysql_num_rows($requeteSQL) > 0 )
			{
				$this->id = $nom;
				list($this->nom, $this->type, $this->prix, $this->effet, $this->batimentReq, $this->description, $this->puissance, $this->achetable) = 
				mysql_fetch_row($requeteSQL);
				$this->description = stripslashes($this->description);
			}
			else
				$this->__construct();
		}
		else
		{
			parent::__construct($nom, $type, $prix);
			$this->effet = $effet;
			$this->batimentReq = $batimentReq;
			$this->achetable = $achetable;
			$this->puissance = $puissance;
			$this->description = $description;
		}
	}
	
	//Accesseurs
	function getPuissance()
	{
		return $this->puissance;
	}
	
	function getDescription()
	{
		return $this->description;
	}
	
	function getEffet()
	{
		return $this->effet;
	}
	
	function getBatiment()
	{
		return $this->batimentReq();
	}
	
	/**
	    *Retourne un booleen
	    *True si achetable est 'y'.
	    *False sinon.
	**/
	function isAchetable()
	{
		return !strcmp($this->achetable, 'y');
	}
	
	//Modifieurs
	function setPuissance($pow)
	{
		$this->puissance = $pow;
	}
	
	/**
	    * Les caractères spéciaux sont échappés lors de la sauvegarde par conséquent
	    * $description ne doit pas contenir de caractères échappés.s
	**/
	function setDescription($description)
	{
		$this->description = $description;
	}
	
	function setEffet($effet)
	{
		$this->effet = $effet;
	}
	
	/**
	    * La fonction prend comme argument un booleen.
	**/
	function setAchetable($bool)
	{
		$this->achetable = $bool ? 'y' : 'n';
	}
	
	//Fonctions de la classe abstraite
	
	//Fonction d'ajout/modification
	function sauver()
	{
		if( $this->id > 0 )
		{
			$requete = 'UPDATE TABLE armure SET '.$this->modifBase().', ';
			$requete .= 'effet = "'.$this->effet.'", description = "'.addslashes($this->description).'", ';
			$requete .= 'puissance = "'.$this->puissance.'", achetable = "'.$this->achetable.'", ';
			$requete .= 'lvl_batiment = "'.$this->batimentReq.'" WHERE id = '.$this->id;
			mysql_query($requete);
		}
		else
		{
			$requete = 'INSERT INTO accessoire (nom, type, prix, effet, lvl_batiment, description, puissance, achetable) VALUES(';
			$requete .= $this->insertBase().', "'.$this->effet.'", "'.$this->batimentReq.'", "'.addslashes($this->description).'", "';
			$requete .= $this->puissance.'", "'.$this->achetable.'")';
			mysql_query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	//Fonction de suppression
	function supprimer()
	{
		parent::supprimer('accessoire');
	}
	
	//Infobulle de l'accessoire
	function infobulle()
	{
		$milieu = '<tr><td>Effet:</td><td>'.$this->description.'</td></tr>';
		$milieu .= '<tr><td>Puissance n&eacute;cessaire:</td></tr><tr><td>'.$this->puissance.'</td></tr>';
		return bulleBase($milieu).'<br />';
	}
	
	function __toString()
	{
		return parent::__toString().', '.$this->effet.', '.$this->puissance.', '.$this->achetable.', '.$this->description.', '.$this->batimentReq;
	}

}
?>