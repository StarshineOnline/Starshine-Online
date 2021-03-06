<?php
/**
 * @file interf_debug.class.php
 * Affichage des in formatuions de débugage 
 */
 
/**
 * classe gérant l'affichage des informations de débugage
 */
class interf_debug extends interf_bal_cont
{
	protected static $num = 0;
	protected static $debugs = array();
	 
	function __construct($texte=null, $classe='')
	{
		parent::__construct('div', 'debug'.self::$num, 'debug '.$classe);
		self::$num++;
		if( $texte )
			$this->add_message($texte);
	}
	
	function add_message($texte)
	{
		if( $this->fils )
			$this->add( new interf_bal_smpl('br') );
		if( is_object($texte) )
			$this->add( $texte );
		else
			$this->add( new interf_txt($texte) );
	}
	
	static function &creer($parent, $texte)
	{
		return $parent->add( new interf_debug($texte) );
	}
	
	static function dump($parent, $var, $classe='')
	{
		return self::creer($parent, new interf_bal_smpl('pre', var_export($var, true))); 
	}
  
  /**
   * Crée et enregistre un message qui sera affiché grâce à la methode affiche_enregistres
   * @param  $type		type du message (cf. constantes msg_*)
   * @param  $texte		textre du message
   * @return 		l'objet interf_alerte créé.
   */  
  static function &enregistre($texte=null, $classe='')
  {
  	$debug = new interf_debug($texte, $classe);
  	self::$debugs[] = &$debug;
  	return $debug;
	}
	
	static function dump_enreg($var, $classe='')
	{
		return self::enregistre(new interf_bal_smpl('pre', var_export($var, true)), $classe); 
	}
	
	/**
	 * Affiche les message précédement enregistrés
	 * @param  $parent  	Objet parent des alerte
	 */	
	static function aff_enregistres($parent)
	{
		foreach(self::$debugs as $d)
		{
			$parent->add($d);
			unset($d);
		}
		self::$debugs = array();
	}
	
	/// Indique si le bouton pour accéder aux informations de débugage doit etre affiché
	static function doit_aff_bouton()
	{
		return self::$num > 0;
	}
}

?>