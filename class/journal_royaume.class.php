<?php

class journal_royaume extends journal
{
	protected $id_royaume;
	protected $id_passif;

	// Renvoie l'id du royaume
	function get_id_royaume()
	{
		return $this->id_royaume;
	}
	/// Modifie l'id du royaume
	function set_id_royaume($id)
	{
		$this->id_royaume = $id;
	}

	// Renvoie l'id du passif
	function get_id_passif()
	{
		return $this->id_passif;
	}
	/// Modifie l'id du passif
	function set_id_passif($id)
	{
		$this->id_passif = $id;
	}
	
	/**	
		*	Constructeur permettant la création d'un combat
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-journal() qui construit un journal "vide".
		*		-journal($id) qui va chercher le journal dont l'id est $id
		*		-journal($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id_perso = 0, $action = '', $actif = '', $passif = '', $time = '', $valeur = '', $valeur2 = 0, $x = 0, $y = 0, $id_passif=0, $id_royaume=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id_perso);
		}
		else
		{
			$this->id = $id;
			$this->id_perso = $id_perso;
			$this->action = $action;
			$this->actif = $actif;
			$this->passif = $passif;
			$this->time = $time;
			$this->valeur = $valeur;
			$this->valeur2 = $valeur2;
			$this->x = $x;
			$this->y = $y;
			$this->id_passif = $id_passif;
			$this->id_royaume = $id_royaume;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->id_passif = $vals['id_passif'];
		$this->id_royaume = $vals['id_royaume'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['id_passif']='i';
    $tbl['id_royaume']='i';
		return $tbl;
	}
	
	static function ecrire($action, $id_royaume, $id_perso = 0, $actif = '', $id_passif=0, $passif = '', $valeur = '', $valeur2 = 0, $x = 0, $y = 0)
	{
		$time = date('Y-m-d H:i:s', time());
		$entree = new journal_royaume($id_perso, $action, $actif, $passif, $time, $valeur, $valeur2, $x, $y, $id_passif, $id_royaume);
		$entree->sauver();
	}
	
	static function ecrire_perso($action, $passif=null, $valeur = '', $valeur2 = 0, $x = 0, $y = 0)
	{
		global $Trace;
		$perso::get_perso();
		$id_passif = $passif ? $passif->get_id() : 0;
		$nom_passif = $passif ? $passif->get_nom() : '';
		self::ecrire('bourse_vente', $Trace[$perso->get_race()]['numrace'], $perso->get_id(), $perso->get_nom(), $id_passif, $nom_passif, $valeur, $valeur2, $x, $y);
	}
}

?>