<?php
//Inclusion de la classe abstraite objet
include_once('objet.class.php');

class Objets extends Objet
{
	protected $achetable;
	protected $effet;
	protected $stack;
	protected $utilisable;
	protected $description;
	
	/**	
	    *  	Constructeur permettant la création d'un accessoire.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un objet "vide".
	    *		-Objets($id) qui va chercher l'objet dont l'id est $id dans la base.
	    *		-Objets($nom,...,$forceReq) qui construit un nouvel objet à partir des valeurs.	   
	**/
	function __construct($nom = '', $type = '', $prix = 0, $achetable = 'y',  $effet = 0, $stack = 0,  $utilisable = 'y', $description = '')
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$requeteSQL = mysql_query('SELECT nom, type, prix, effet, stack, description, utilisable, achetable FROM objet WHERE id = '.$nom);
			//Si l'objet est dans la base, on le charge sinon on crée un objet vide.
			if( mysql_num_rows($requeteSQL) > 0 )
			{
				$this->id = $nom;
				list($this->nom, $this->type, $this->prix, $this->effet, $this->stack, $this->description, $this->utilisable, $this->achetable) = 
				mysql_fetch_row($requeteSQL);
				$this->description = stripslashes($this->description);
			}
			else
				$this->__construct();
		}
		else
		{
			parent::__construct($nom, $type, $prix);
			$this->achetable = $achetable;
			$this->effet = $effet;
			$this->stack = $stack;
			$this->utilisable = $utilisable;
			$this->description = $description;
		}
	}
	
	//Accesseurs
	//Retourne un booleen. true si 'y' false sinon
	function isAchetable()
	{
		return !strcmp($this->achetable, 'y');
	}
	
	function getEffet()
	{
		return $this->effet;
	}
	
	function getStack()
	{
		return $this->stack;
	}
	
	//Retourne un booleen. true si 'y' false sinon
	function isUtilisable()
	{
		return !strcmp($this->utilisable,'y');
	}
	
	function getDescription()
	{
		return $this->description;
	}
	
	//Modifieurs
	//La fonction prend pour argument un booleen
	function setAchetable($bool)
	{
		$this->achetable = $bool ? 'y': 'n';
	}
	
	function setEffet($effet)
	{
		$this->effet = $effet;
	}
	
	function setStack($stack)
	{
		$this->stack = $stack;
	}
	
	//La fonction prend pour argument un booleen
	function setUtilisable($bool)
	{
		$this->utilisable = $bool ? 'y' : 'n';
	}
	
	function setDescription($description)
	{
		$this->description = $description;
	}
	
	//Fonction d'ajout/modification les caractère spéciaux sont échapés pour les descriptions.
	function sauver()
	{
		if( $id > 0 )
		{
			$requete = 'UPDATE TABLE objet SET '.$this->modifBase().', ';
			$requete .= 'effet = "'.$this->effet.'", description = "'.addslashes($this->description).'", ';
			$requete .= 'stack = "'.$this->stack.'", achetable = "'.$this->achetable.'", ';
			$requete .= 'utilisable = "'.$this->utilisable.'" WHERE id = '.$this->id;
			mysql_query($requete);
		}
		else
		{
			$requete = 'INSERT INTO objet (nom, type, prix, effet, utilisable, description, stack, achetable) VALUES(';
			$requete .= $this->insertBase().', "'.$this->effet.'", "'.$this->utilisable.'", "'.addslashes($this->description).'", "';
			$requete .= $this->stack.'", "'.$this->achetable.'")';
			mysql_query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	function supprimer()
	{
		parent::supprimer('objet');
	}
	
	function __toString()
	{
		return parent::__toString().', '.$this->effet.', '.$this->utilisable.', '.$this->stack.', '.$this->achetable.', '.$this->description;
	}
}
?>