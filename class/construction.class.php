<?php // -*- mode: php; -*-
/**
 * @file construction.class.php
 * Définition de la classe construction représentant un bâtiment sur la carte
 */
 
/**
 * Classe construction
 * Classe représentant un bâtiment sur la carte
 */
class construction extends entitenj_constr
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
	// @{
	protected $rechargement; ///< Date à laquelle l'arme de siège pourra à nouveau tirer
	protected $rattrapage;  ///< Temps à rattrapper pour les tirs.
	protected $date_construction; ///< Date de construction du bâtiment
	
	/// Renvoie la date à laquelle l'arme de siège pourra à nouveau tirer
	function get_rechargement()
	{
		return $this->rechargement;
	}
	/// Modifie la date à laquelle l'arme de siège pourra à nouveau tirer
	function set_rechargement($rechargement)
	{
		$this->rechargement = $rechargement;
		$this->champs_modif[] = 'rechargement';
	}

	/// Renvoie la date à laquelle l'arme de siège pourra à nouveau tirer
	function get_rattrapage()
	{
		return $this->rattrapage;
	}
	/// Modifie la date à laquelle l'arme de siège pourra à nouveau tirer
	function set_rattrapage($rattrapage)
	{
		$this->rattrapage = $rattrapage;
		$this->champs_modif[] = 'rattrapage';
	}

	/// Renvoie la date de construction du bâtiment
	function get_date_construction()
	{
		return $this->date_construction;
	}
	/// Modifie la date de construction du bâtiment
	function set_date_construction($date_construction)
	{
		$this->date_construction = $date_construction;
		$this->champs_modif[] = 'date_construction';
	}
	/// Date de pose ou construction de l'entité
	function get_date_debut()
	{
		return $this->date_construction;
	}
	
	/// Renvoie l'image du bâtiment
  function get_image()
  {
  	return $this->make_url_image($this->get_batiment()->get_image());
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
	// @{
	/**
	* Constructeur
	* @param id                  Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	* @param $id_batiment        Id de la définition du bâtiment
	* @param x                   Position x du bâtiment
	* @param y                   Position y du bâtiment
	* @param royaume             Royaume auquel appartient le bâtiment
	* @param hp                  HP du bâtiment
	* @param nom                 Nom du bâtiment
	* @param type                Type du bâtiment
	* @param rez                 Pourcentage de HP/MP à la rez ou distance de vision des tours
	* @param rechargement        Date à laquelle l'arme de siège pourra à nouveau tirer
	* @param date_construction   Date de construction du bâtiment
	* @param point_victoire      Nombre de points de victoire gagnés lorsque le bâtiment est détruit
	* @param rattrapage          
	*/
	function __construct($id = 0, $id_batiment = 0, $x = 0, $y = 0, $royaume = 0, $hp = 0, $nom = '', $type = '', $rez = 0, $rechargement = 0, $date_construction = 0, $point_victoire = 0, $rattrapage = 0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			entitenj_constr::__construct($id, $id_batiment, $x, $y, $royaume, $hp, $nom, $type, $rez, $point_victoire);
			$this->rechargement = $rechargement;
			$this->date_construction = $date_construction;
			$this->rattrapage = $rattrapage;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
	protected function init_tab($vals)
	{
		entitenj_constr::init_tab($vals);
		$this->rechargement = $vals['rechargement'];
		$this->date_construction = $vals['date_construction'];
		$this->rattrapage = $vals['rattrapage'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
		return entitenj_constr::get_liste_champs().', rechargement, date_construction, rattrapage';
	}
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return entitenj_constr::get_valeurs_insert().', '.$this->rechargement.', '.$this->date_construction.', '.$this->rattrapage.'';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return entitenj_constr::get_liste_update().', rechargement = '.$this->rechargement.', date_construction = '.$this->date_construction.', rattrapage = '.$this->rattrapage.'';
	}
	
	/// Renvoie le nom de la table (par défaut le nom de la classe)
	static function get_table()
	{
		return 'construction';
	}
	// @}
	/// Renvoie le coût en PA de l'attaque
	function get_cout_attaque(&$perso, $cible=null)
	{
		global $G_PA_attaque_batiment;
		if( $perso->is_buff('convalescence') )
		{
    	interf_alerte::enregistre(interf_alerte::msg_avertis, 'Le coût en PA passe à '.$G_PA_attaque_batiment.' à cause de votre convalescence.');
			return $G_PA_attaque_batiment;
		}
		else
			return 0;
	}
	/// Indique si l'entité peut attaquer
	function peut_attaquer()
	{
		return $this->get_rechargement() < time();
	}
	/// Actions effectuées à la fin d'un combat pour l'attaquant
	function fin_attaque(&$cible)
	{
		$recharg = $this->get_batiment()->get_bonus('rechargement');
		//$date_tir = $this->get_rechargement() - $reduc;
		$retard = time() - $this->get_rechargement();
		// On remet à zéro si le dernier tir date de plus de 24h (on considère qu'il n'y avait pas de combat avant donc pas de rattrapage)
		if( $retard > 86400 )
		{
			$reduc = 0;
			$this->set_rattrapage(0);
		}
		else
		{
			$rattrap = $this->get_rattrapage();
			// On augmente pas le rattrapage sur les cibles posées récement (après le moment où on pouvait tirer moins le temps à rattrapper)
			$diff = $cible->get_date_debut() - $this->get_rechargement() + $rattrap;
			if( $diff > 0 )
				$retard -= $diff;
			if( $retard > 0 )
				$rattrap += $retard;
			// Calcul du moment du prochain tir
			$reduc = min($rattrap, $recharg/2);
			$rattrap -= $reduc;
			$this->set_rattrapage( $rattrap );
		}
		$this->set_rechargement( time() + $recharg - $reduc );
		$this->sauver();
	}
	
	/// Récupère les buffs du bâtiment
	protected function constr_buff()
	{
		$this->buff = buff_batiment::create('id_construction', $this->id, 'id ASC', 'type', false, true);
	}

  /**
   * Renvoie les images et positions des constructions dans une zone donnée sous forme de tableau
   * @param  $x_min     Valeur minimale de la coordonnée x
   * @param  $x_max    Valeur maximale de la coordonnée x
   * @param  $y_min     Valeur minimale de la coordonnée y
   * @param  $y_max    Valeur maximale de la coordonnée y
   * @return    tableau contenant les positions et l'image
   */
  static function get_images_zone($x_min, $x_max, $y_min, $y_max, $grd_img=true, $cond='1', $royaume=false)
  {
    global $db;
		$requete = 'SELECT x, y, b.image, b.nom, royaume, c.type, b.quete FROM '.static::get_table().' AS c INNER JOIN batiment AS b ON c.id_batiment = b.id WHERE x >= '.$x_min.' AND x <= '.$x_max.' AND y >= '.$y_min.' AND y <= '.$y_max.' AND '.$cond;
    $req = $db->query($requete);
    $res = array();
    while( $row = $db->read_object($req) )
    {
      $row->image = self::make_url_image($row->image, $grd_img, ($royaume && $row->type == 'arme_de_siege') ? $row->royaume : false);
      $res[] = $row;
    }
    return $res;
  }

  static function make_url_image($image, $grd_img=true, $royaume=false)
  {
  	global $Trace;
    return 'image/batiment'.($grd_img ? '' : '_low').'/'.$image.'_'.($royaume ? $Trace['liste'][$royaume] : '04').'.png';
  }
  
  static function get_nom_proche($obj, $exclu=false)
  {
  	global $db;
  	$requete = 'SELECT nom FROM construction WHERE royaume > 0 AND type IN ("tour", "bourg", "fort")';
  	if( $exclu )
  		$requete .= ' AND id != '.$exclu;
  	$requete .= ' ORDER BY GREATEST(ABS(CAST(x AS SIGNED) - '.$obj->get_x().'), ABS(CAST(y AS SIGNED) - '.$obj->get_y().')) LIMIT 1';
  	$req = $db->query($requete);
  	$row = $db->read_array($req);
  	if( $row )
  		return $row[0];
  	return '';
	}
  
  static function get_nom_aleatoire($exclu=false)
  {
  	global $db;
  	$requete = 'SELECT nom FROM construction WHERE royaume > 0 AND type IN ("tour", "bourg", "fort")';
  	if( $exclu )
  		$requete .= ' AND id != '.$exclu;
  	$requete .= ' ORDER BY RAND() LIMIT 1';
  	$req = $db->query($requete);
  	$row = $db->read_array($req);
  	if( $row )
  		return $row[0];
  	return '';
	}
}
?>
