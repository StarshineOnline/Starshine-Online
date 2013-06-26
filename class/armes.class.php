<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion de la classe abstraite objet
include_once(root.'objet.class.php');

class Arme extends Objet
{
	protected $degat;
	protected $forceReq;
	protected $melee;
	protected $distance;
	protected $portee;
	protected $mains;
	protected $var1; //What is it?
	protected $effet;
	protected $batimentReq;
	protected $image;
	
	/**	
	   *  	Constructeur permettant la création d'une armure
	   *	Les valeurs par défaut sont celles de la base de donnée.
	   *	Le constructeur accepte plusieurs types d'appels:
	   *		-Arme() qui construit un objet "vide".
	   *		-Arme($id) qui va chercher l'objet dont l'id est $id dans la base.
	   *		-Arme($nom,...,$image) qui construit un nouvel objet à partir des valeurs.
	**/
	function __construct($nom = '', $type = '', $prix = 0, $effet = '', $batimentReq = 0, $degat = 1, $melee = 0, $distance = 0, 
				     $portee = 0, $forceReq = 0, $var1 = '', $mains = 'main_droite',  $image = '')
	{
		//Traitement lors de l'appel Armure=($id_armure)
		$num_args = func_num_args();
		if( $num_args == 1 && is_numeric($nom) )
		{
			$requeteSQL = mysql_query(
			$req = 'SELECT nom, forcex, type, effet, prix, lvl_batiment, degat, melee, distance, distance_tir, mains, var1, image  FROM arme WHERE id = '
			.$nom);
			//Si l'identifiant n'est pas dans la table, on remplace l'objet par un objet vide.
			if( mysql_num_rows($requeteSQL) > 0 )
			{
				//Assigne les valeurs contenues dans la base à l'objets
				list($this->nom, $this->forceReq, $this->type, $this->effet, $this->prix, $this->batimentReq, $this->degat, $this->melee, 
				     $this->distance, $this->portee, $this->mains, $this->var1, $this->image) = mysql_fetch_row($requeteSQL);
				//Mise à jour de l'id
				$this->id = $nom;
			}
			else
				$this->__construct();
		}
		else
		{
			parent::__construct($nom, $type, $prix);
			$this->image = $image;
			$this->degat = $degat;
			$this->portee = $portee;
			$this->forceReq = $forceReq;
			$this->effet = $effet;
			$this->mains = $mains;
			$this->var1 = $var1;
			$this->distance = $distance;
			$this->mains = $main;
			$this->batimentReq = $batimentReq;
			$this->melee = $melee;
		}
	}
	
	//Accesseurs
	function getDegat()
	{
		return $this->degat;
	}

	function getMelee()
	{
		return $this->melee;
	}
	
	function getDistance()
	{
		return $this->distance;
	}
	
	function getPortee()
	{
		return $this->portee;
	}
	
	function getMain()
	{
		return $this->mains;
	}
	
	function getVar1()
	{
		return $this->var1;
	}
	
	function getEffet()
	{
		return $this->effet;
	}
	
	function getBatiment()
	{
		return $this->batimentReq;
	}
	
	function getImage()
	{
		return $this->image();
	}
	
	function afficheImage()
	{
		return '<img src="'.$this->image().'" alt="'.$this->nom.'" />';
	}
	
	function getForce()
	{
		return $this->forceReq;
	}
	

	//Modifieurs
	function setDegat($degat)
	{
		$this->degat = $degat;
	}
	
	function setMelee($melee)
	{
		$this->melee = $melee;
	}
	
	function setDistance($distance)
	{
		$this->distance = $distance;
	}
	
	function setPortee($portee)
	{
		$this->portee = $portee;
	}
	
	function setEffet($effet)
	{
		$this->effet = $effet;
	}
	
	function setForce($force)
	{
		$this->forceReq = $force;
	}
	
	function setBatiment($batiment)
	{
		$this->batiment = $batiment;
	}

	function setVar1($var1)
	{
		$this->var1 = $var1;
	}
	
	function setImage($image)
	{
		$this->image = $image;
	}
	
	function setMains($mains)
	{
		$this->mains = $mains;
	}
	
	function setNumMains($mains)
	{
		switch($mains)
		{
			case 1:
				$this->main = 'main_gauche';
				break;
			case 2:
				$this->main = 'deux_mains';
				break;
			default:
				$this->main = 'main_droite';
				break;
		}
	}

	//Fonctions de la classe abstraite
	
	//Ajout/Modification d'une arme
	function sauver()
	{
		//Verification de l'existence de l'objet dans la base
		if( $id > 0 )
		{
			$requete = 'UPDATE TABLE arme SET '.$this->modifBase().', ';
			$requete .= 'degat = "'.$this->degat.'", melee = "'.$this->melee.'", "';
			$requete .= 'forcex = "'.$this->forceReq.'", effet = "'.$this->effet.'", ';
			$requete .= 'lvl_batiment = "'.$this->batimentReq.', distance_tir = "'.$this->portee.'", ';
			$requete .= 'var1 = "'.$this->var1.'", image = "'.$this->image.'" WHERE id = '.$this->id;
			mysql_query($requete);
		}
		else
		{
			$requete = 'INSERT INTO arme (nom, type, prix, degat, melee, forcex, effet, lvl_batiment, distance_tir, var1, image) VALUES(';
			$requete .= $this->insertBase().', "'.$this->degat.'", "'.$this->melee.'", "'.$this->forceReq.'", "';
			$requete .= $this->effet.'", "'.$this->batimentReq.'", "'.$this->portee.'", "'.$this->var1.'", "'.$this->image.'")';
			mysql_query($requete);
			//On récupère le dernier ID inseré. Pour mettre à jour celui de l'objet nouvellement ajouté.
			list($this->id) = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	//Permet de supprimer l'objet de la table
	function supprimer()
	{
		parent::supprimer('arme');
	}
	
	//Infobulle de l'arme
	function infobulle()
	{
		$milieu = '<tr><td>Nombre de mains:</td><td>'.(strcmp($this->mains, 'main_droite') != -1 ? 1 : 2).'</td></tr>';
		$milieu .= '<tr><td>D&eacute;gats:</td></tr><tr><td>'.$this->degat.'</td></tr>';
		$milieu .= '<tr><td>Port&eacute;e:</td></tr><tr><td>'.$this->portee.'</td></tr>';
		$milieu .= ( !empty($this->effet) ? '<tr><td>Effet:</td></tr><tr><td>'.$this->effet.'</td></tr>' : '');
		$milieu .= '<tr><td>Force n&eacute;cessaire:</td></tr><tr><td>'.$this->forceReq.'</td></tr>';
		//Gemmage?
		return bulleBase($milieu).'<br />'.$this->effet;
	}
	
	function __toString()
	{
		return parent::__toString().', '.$this->effet.', '.$this->degat.', '.$this->portee.', '.$this->forceReq.', '.$this->batimentReq.', '.$this->mains.', '.$this->melee.', '.$this->var1.', '.$this->distance.', '.$this->image;
	}
}