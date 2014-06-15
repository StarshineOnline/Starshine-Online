<?php
/**
 * @file comp_jeu.class.php
 * Définition de la classe comp_sort servant de base aux compétences hors combat
 */

/**
 * Classe comp_jeu
 * Classe comp_jeu servant de base aux compétences hors combat
 */
class comp_jeu extends comp
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $pa;   ///< Coût en PA de la compétence
	
	/// Renvoie le coût de la comptétence
	function get_pa(&$joueur = null)
	{
		return $this->pa;
	}
	/// Modifie le coût de la comptétence
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}
	function get_image()
	{
		return 'image/buff/'.$this->type.'.png';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id             Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type           Type générique.
	 * @param effet          Effet principal.
	 * @param duree          Durée
	 * @param comp_assoc     Compétence associée
	 * @param carac_assoc    Caractéristique associée
	 * @param comp_requis    Requis dans la compétence
	 * @param carac_requis   Requis dans la caractéristique (inutilisé)
	 * @param effet2         Deuxième effet
	 * @param requis         Compétence ou sort requis pour apprendre celui-ci
	 * @param cible          Cible de la compétence ou du sort
	 * @param description    Description du buff
	 * @param mp             Coût en MP ou en RM
	 * @param prix           Prix de la compétence ou le sort
	 * @param lvl_batiment   Niveau de l'école qui vent la compétence ou le sort
	 * @param arme_requis    Arme requise pour utiliser la compétence
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $arme_requis='', $pa=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $arme_requis);
			$this->pa = $pa;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp::init_tab($vals);
		$this->pa = $vals['pa'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp::get_liste_champs().', pa';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp::get_valeurs_insert().', '.$this->pa;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp::get_liste_update().', pa = '.$this->pa;
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM comp_jeu WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      case 'preparation_camp':
        return new comp_preparation_camp($row);
      case 'repos_interieur':
        return new comp_repos_interieur($row);
      case 'esprit_libre':
        return new comp_esprit_libre($row);
      case 'invocation_pet':
        return new comp_invocation_pet($row);
      case 'sabotage':
        return new comp_sabotage($row);
      case 'longue_portee':
        return new comp_longue_portee($row);
      default:
        return new comp_jeu($row);
      }
  	}
  }
	// @}

	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
    global $db;
    $action = false;
    $cibles = $this->get_liste_cibles( $perso );
    foreach($cibles as $cible)
		{
			//Mis en place du buff
			if(lance_buff($this->get_type(), $cible->get_id(), $this->get_effet(), $this->get_effet2(), $this->get_duree(), $this->get_nom(), $this->get_description(true), 'perso', 0, $cible->get_nb_buff(), $cible->get_grade()->get_nb_buff()))
			{
				$action = true;
				echo $cible->get_nom().' a bien reçu le buff<br />';
				//Insertion du buff dans le journal du receveur
				if( count($cibles)>1 && $cible->get_id() != $perso->get_id() )
				{
  				$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", 'rgbuff', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', 0, 0, 0)";
  				$db->query($requete);
        }
			}
			else
			{
				if($G_erreur == 'puissant') echo $cibles.' bénéficie d\'un buff plus puissant<br />';
				else echo $cible->get_nom().' a trop de buffs.<br />';
			}
		}
		if (substr_count($this->get_type(), "buff_cri"))
		{
			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('cri');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
			}
		if( $action )
		{
			$requete = "INSERT INTO journal VALUES('', ".$perso->get_id().", '".(count($cibles)>1?"gbuff":"buff")."', '".$perso->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', 0, 0, 0)";
			$db->query($requete);
    }
		return $action;
  }
}

class comp_preparation_camp extends comp_jeu
{

	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
    global $db;
    $cibles = $this->get_liste_cibles( $perso );
    $action = false;
    foreach($cibles as $cible)
		{
			//Mis en place du buff
			if(lance_buff($this->get_type(), $cible->get_id(), $this->get_effet(), time(), $this->get_duree(), $this->get_nom(), $this->get_description(true), 'perso', 0, $cible->get_nb_buff(), $cible->get_grade()->get_nb_buff()))
			{
				$action = true;
				echo $cible->get_nom().' a bien reçu le buff<br />';
				//Insertion du buff dans le journal du receveur
				$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", 'rgbuff', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', 0, 0, 0)";
				$db->query($requete);
			}
			else
			{
				if($G_erreur == 'puissant') echo $cibles.' bénéficie d\'un buff plus puissant<br />';
				else echo $cible->get_nom().' a trop de buffs.<br />';
			}
		}
		return $action;
  }
}

class comp_repos_interieur extends comp_jeu
{

	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
		if($perso->is_buff('repos_interieur') AND $perso->get_buff('repos_interieur', 'effet') >= 10)
		{
			echo 'Vous avez trop utilisé repos intérieur pour le moment !';
		}
		else
		{
			//echo '$joueur->get_buff(\'repos_interieur\', \'effet\') => '.$joueur->get_buff('repos_interieur', 'effet').'<br />';
			if($perso->is_buff('repos_interieur')) $effet = $perso->get_buff('repos_interieur', 'effet') + 1;
			else $effet = 1;
			//echo '$effet => '.$effet.'<br />';
			if(lance_buff('repos_interieur', $perso->get_id(), $effet, 0, (60 * 60 * 24), $this->get_nom(), $this->formate_description($this->get_description().'<br /> Utilisation '.$effet.' / 10'), 'perso', 1, 0, 0, 0))
			{
				echo 'Le buff a été envoyé<br />';
				$perso->set_pa($perso->get_pa() + 2);
				echo '<a href="competence_jeu.php?ID='.$this->get_id().'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau cette compétence</a>';
		    return true;
			}
		}
		return false;
  }
}

class comp_esprit_libre extends comp_jeu
{
	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
    global $db;
    foreach ($perso->get_buff() as $debuff)
    {
		if($debuff->get_debuff() == 1)
			{
			  if($debuff->is_supprimable())
			  {
				$debuff_tab[] = $debuff->get_id();
			  }
			}
      }
      
    if(count($debuff_tab) > 0)
    {
		$id_debuff = $debuff_tab[rand(0, count($debuff_tab)-1)];
	}
	$buff = buff::create('id', $id_debuff);
	
	$debuff = sort_jeu::create('nom', $buff[0]->get_nom());
	
	// On recherche si le sort a un antécédent
    $nouv = $debuff[0]->get_obj_requis();
    $action= false;
    //Si il a un antécédent on le modifie
	if( $nouv )
    {
		  $buff[0]->set_nom( $nouv->get_nom() );
		  $buff[0]->set_effet( $nouv->get_effet() );
		  $buff[0]->set_effet2( $nouv->get_effet2() );
		  $buff[0]->set_description( $nouv->get_description() );
		  $buff[0]->sauver();
		  $action = true;
    }
    else //sinon on le supprime
    {
      $perso->supprime_buff( $buff[0]->get_type() );
	  $buff[0]->supprimer();
	  $action = true;
    }
		echo '<a href="competence_jeu.php?ID='.$this->get_id().'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau cette compétence</a>';
		return $action;
  }
}

class comp_longue_portee extends comp_jeu
{
	function get_pa(&$perso)
	{
		if ($perso->get_inventaire_partie('jambe') === 'p145')
			return $this->pa -1;	
		else
			return $this->pa;
	}
}

class comp_invocation_pet extends comp_jeu
{

	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
    global $db;
	$perso->check_materiel();
		$id_pet = 0;
		switch (strtolower($perso->get_classe()))
		{
			case 'dresseur':
				$pet = 'Esprit des forêts';
				break;
			case 'druide ollamh':
				$pet = 'Ange';
				break;
			case 'conjurateur':
				$pet = 'Élémentaire noble';
				break;
			case 'démoniste':
				$pet = 'Démon majeur';
				break;
			default:
				$pet = 'none';
				break;
		}
		$req = $db->query("select id from monstre where nom = '$pet'");
		if ($req)
		{
			$row = $db->read_assoc($req);
			if ($row)
			{
				$id_pet = $row['id'];
			}
		}
		$ecurie = $perso->get_pets();
		foreach ($ecurie as $cur_pet)
		{
			if ($cur_pet->get_monstre()->get_level() == 0)
			{
				echo "<h5>Vous avez déjà un compagnon invoqué</h5>";
				$id_pet = -1;
				break;
			}
		}
		if ($id_pet == -1)
			break;
		if ($id_pet != 0)
		{
			if ($perso->add_pet($id_pet))
			{
				echo "$pet bien invoqué";
				return true;
			}
			else
			{
				echo "<h5>Impossible d'ajouter le pet: trop de pets</h5>";
				return false;
			}
		}
		else
		{
			echo "<h5>Impossible de trouver un monstre à invoquer</h5>";
			return false;
		}
  }
}

class comp_sabotage extends comp_jeu
{

	/**
	 * Méthode gérant l'utilisation de la compétence
	 * @param $perso   Personnage lançant la coméptence
	 */
  function lance($perso)
  {
    global $db;
		$sql = 'select * from placement where x = '.$perso->get_x().' and y = '.
			$perso->get_y().' and type = \'arme_de_siege\'';
		$sql2 = 'select * from construction where x = '.$perso->get_x().
			' and y = '.$perso->get_y().' and type = \'arme_de_siege\'';
		$req = $db->query($sql);
		if ($req && $db->num_rows($req) > 0) {
			$type = 'placement';
			$b = $db->read_object($req);
		}
		else {
			$req = $db->query($sql2);
			if ($req && $db->num_rows($req) > 0) {
				$type = 'construction';
				$b = $db->read_object($req);
			}
		}
		if ($req && $db->num_rows($req) > 0) {
			$date_fin = time() + $this->get_duree();
			$sql3 = "insert into buff_batiment ".
				"(id_${type}, date_fin, duree, type, effet) values (".
				$b->id.", $date_fin, ".$this->get_duree().", 'sabotage', 1) ".
				'ON DUPLICATE KEY UPDATE date_fin = VALUES(date_fin)';
			$req = $db->query($sql3);
			if ($req) {
					echo "Bâtiment saboté.<br/>";
					return true;
			} else {
				echo "<h5>Erreur SQL ???</h5>";
			}
		}
		else {
			echo "<h5>Pas de cible sur la case</h5>";
		}
		return false;
  }
}
?>
