<?php
/**
 *	@file taverne.class.php
 * Classe représentant les services de repos de la taverne.
 */
 
 
/**
 * Classe représentant un service de repos de la taverne.
 */
class taverne extends table
{
	protected $nom;  ///< Nom pour les personnages masculins
	protected $nom_f;  ///< Nom pour les personnages féminins
	protected $pa;  ///< Cout en PA
	protected $honneur;  ///< Cout fixe en honneur
	protected $honneur_pc;  ///< Cout proportionnel en honneur
	protected $hp;  ///< Gain fixe en HP
	protected $hp_pc;  ///< Gain proportionnel en HP
	protected $mp;  ///< Gain fixe en MP
	protected $mp_pc;  ///< Gain proportionnel en MP
	protected $star;  ///< Cout en stars
	protected $pute;  ///< 1 si c'est un service de courtisan(nes), 0 sinon
	protected $requis;  ///< Prérequis pour avoir accès à ce service
	
	/// Méthode renvoyant le nom pour les personnages masculins
	function get_nom()
	{
		return $this->nom;
	}
	/// Méthode modfiant le nom pour les personnages masculins
	function set_nom($valeur)
	{
		$this->nom = $valeur;
	}
	/// Méthode renvoyant le nom pour les personnages féminins
	function get_nom_f()
	{
		return $this->nom_f;
	}
	/// Méthode modfiant le nom pour les personnages féminins
	function set_nom_f($valeur)
	{
		$this->nom_f = $valeur;
	}
	/// Méthode renvoyant le cout en PA
	function get_pa()
	{
		return $this->pa;
	}
	/// Méthode modfiant le cout en PA
	function set_pa($valeur)
	{
		$this->pa = $valeur;
	}
	/// Méthode renvoyant le cout fixe en honneur
	function get_honneur()
	{
		return $this->honneur;
	}
	/// Méthode modfiant le cout fixe en honneur
	function set_honneur($valeur)
	{
		$this->honneur = $valeur;
	}
	/// Méthode renvoyant le cout proportionnel en honneur
	function get_honneur_pc()
	{
		return $this->honneur_pc;
	}
	/// Méthode modfiant le cout proportionnel en honneur
	function set_honneur_pc($valeur)
	{
		$this->honneur_pc = $valeur;
	}
	/// Méthode renvoyant le gain fixe en HP
	function get_hp()
	{
		return $this->hp;
	}
	/// Méthode modfiant le gain fixe en HP
	function set_hp($valeur)
	{
		$this->hp = $valeur;
	}
	/// Méthode renvoyant le gain proportionnel en HP
	function get_hp_pc()
	{
		return $this->hp_pc;
	}
	/// Méthode modfiant le gain proportionnel en HP
	function set_hp_pc($valeur)
	{
		$this->hp_pc = $valeur;
	}
	/// Méthode renvoyant le gain fixe en MP
	function get_mp()
	{
		return $this->mp;
	}
	/// Méthode modfiant le gain fixe en MP
	function set_mp($valeur)
	{
		$this->mp = $valeur;
	}
	/// Méthode renvoyant le gain proportionnel en MP
	function get_mp_pc()
	{
		return $this->mp_pc;
	}
	/// Méthode modfiant le gain proportionnel en MP
	function set_mp_pc($valeur)
	{
		$this->mp_pc = $valeur;
	}
	/// Méthode renvoyant le cout en stars
	function get_star()
	{
		return $this->star;
	}
	/// Méthode renvoyant le cout en stars
	function get_prix()
	{
		return $this->star;
	}
	/// Méthode modfiant le cout en stars
	function set_star($valeur)
	{
		$this->star = $valeur;
	}
	/// Méthode renvoyant si c'est un service de courtisan(nes) ou non
	function get_pute()
	{
		return $this->pute;
	}
	/// Méthode modfiant si c'est un service de courtisan(nes) ou non
	function set_pute($valeur)
	{
		$this->pute = $valeur;
	}
	/// Méthode renvoyant le prérequis pour avoir accès à ce service
	function get_requis()
	{
		return $this->requis;
	}
	/// Méthode modfiant le prérequis pour avoir accès à ce service
	function set_requis($valeur)
	{
		$this->requis = $valeur;
	}
	
	/**
	 * Constructeur	
	 */	
	function __construct($id=0, $nom='', $nom_f='', $pa=0, $honneur=0, $honneur_pc=0, $hp=0, $hp_pc=0, $mp=0, $mp_pc=0, $star=0, $pute=0, $requis=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->nom_f = $nom_f;
			$this->pa = $pa;
			$this->honneur = $honneur;
			$this->honneur_pc = $honneur_pc;
			$this->hp = $hp;
			$this->hp_pc = $hp_pc;
			$this->mp = $mp;
			$this->mp_pc = $mp_pc;
			$this->star = $star;
			$this->pute = $pute;
			$this->requis = $requis;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
		$this->id = $vals['id'];
		$this->nom = $vals['nom'];
		$this->nom_f = $vals['nom_f'];
		$this->pa = $vals['pa'];
		$this->honneur = $vals['honneur'];
		$this->honneur_pc = $vals['honneur_pc'];
		$this->hp = $vals['hp'];
		$this->hp_pc = $vals['hp_pc'];
		$this->mp = $vals['mp'];
		$this->mp_pc = $vals['mp_pc'];
		$this->star = $vals['star'];
		$this->pute = $vals['pute'];
		$this->requis = $vals['requis'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'nom_f'=>'s', 'pa'=>'i', 'honneur'=>'i', 'honneur_pc'=>'i', 'hp'=>'i', 'hp_pc'=>'i', 'mp'=>'i', 'mp_pc'=>'i', 'star'=>'i', 'pute'=>'i', 'requis'=>'i');
	}

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    return array('Cout en honneur', 'Gain en HP', 'Gain en MP');
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    return array($this->honneur.' + '.$this->honneur_pc.' %', $this->hp.' + '.$this->hp_pc.' %', $this->mp.' + '.$this->mp_pc.' %');
  }
}



?>