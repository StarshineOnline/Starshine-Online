<?php
/**
 * @file objet.class.php
 * Gestion des objets alchimiques, ingrédients, de quêtes, et autrs objets divers
 */

/**
 * Classe gérant les objets alchimiques, ingrédients, de quêtes, et autrs objets divers.
 * Correspond à la table du même nom dans la bdd.
 */
class objet extends objet_equip
{
	protected $stack;  ///< nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	protected $utilisable;  ///< indique si l'objet est utilisable.
	protected $description;  ///< description de l'objet.
	protected $pa;  ///< pa nécessaires pour utiliser l'objet.
	protected $mp;  ///< mp nécessaires pour utiliser l'objet.
  private $nombre;  ///< nombre d'exmplaires disponibles.

  /// Renvoie le nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	function get_stack()
	{
		return $this->stack;
	}
	/// Modifie le nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	function set_stack($stack)
	{
		$this->stack = $stack;
		$this->champs_modif[] = 'stack';
	}

  /// Renvoie la description de l'objet
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description de l'objet
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

  /// Renvoie si l'objet est utilisable.
	function is_utilisable()
	{
		return $this->utilisable == 'y';
	}
	/// Modifie si l'objet est utilisable.
	function set_utilisable($utilisable)
	{
		$this->utilisable = $utilisable ? 'y' : 'n';
		$this->champs_modif[] = 'utilisable';
	}

  /// Renvoie les pa nécessaires pour utiliser l'objet.
	function get_pa()
	{
		return $this->pa;
	}
	/// Modifie les pa nécessaires pour utiliser l'objet.
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

  /// Renvoie les mp nécessaires pour utiliser l'objet.
	function is_mp()
	{
		return $this->mp;
	}
	/// Modifie les mp nécessaires pour utiliser l'objet.
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

  /// Renvoie le nombre d'exmplaires disponibles.
	function get_nombre()
	{
		return $this->nombre;
	}
	/// Modifie le nombre d'exmplaires disponibles.
	function set_nombre($stack)
	{
    $this->nombre = $stack;
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet.
	 * @param  $type	         type de l'objet.
	 * @param  $prix	         prix de l'objet em magasin.
	 * @param  $effet	         valeur de l'effet de l'objet.
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible.
	 * @param  $stack          indique combien d'exmplaire on peut mettre dans un emplacement de l'inventaire.
	 * @param  $utilisable     indique si l'objet est utilisable.
	 * @param  $description    description de l'objet.
	 * @param  $pa             pa nécessaire pour utiliser l'objet.
	 * @param  $mp             mp nécessaire pour utiliser l'objet.
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $stack=0, $utilisable='y', $description='', $pa=0, $mp=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->prix = $prix;
			$this->effet = $effet;
			$this->lvl_batiment = $lvl_batiment;
			$this->stack = $stack;
			$this->utilisable = $utilisable;
			$this->description = $description;
			$this->pa = $pa;
			$this->mp = $mp;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_equip::init_tab($vals);
		$this->stack = $vals['stack'];
		$this->utilisable = $vals['utilisable'];
		$this->description = $vals['description'];
		$this->pa = $vals['pa'];
		$this->mp = $vals['mp'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['stack']='i';
    $tbl['utilisable']='s';
    $tbl['description']='s';
    $tbl['pa']='i';
    $tbl['mp']='i';
		return $tbl;
	}

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    if($complet)
    {
      return array('Stack', 'Description', 'Prix HT (en magasin)');
    }
    else
      return array('Stars');
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $vals = array($this->stack, $this->description, $this->prix);
    return $vals;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
    $infos = '';
    if( $this->pa )
      $infos .= 'PA : '.$this->pa;
    if( $this->mp )
    {
      if( $infos )
        $infos .= ' - ';
      $infos .= 'MP : '.$this->mp;
    }
    return $infos ? $infos : null;
  }

	function get_colone($partie)
  {
    switch( $this->type )
    {
    case 'fabrication':
      return ($partie == 'artisanat') ? 0 : false;
    case 'fiole':
    case 'identification':
      return ($partie == 'artisanat') ? 1 : false;
    case 'globe_pa':
    case 'parchemin_pa':
    case 'parchemin_tp':
    case 'potion_guerison':
    case 'potion_pm':
    case 'potion_vie':
      return ($partie == 'utile') ? 0 : false;
    case 'objet_quete':
    case 'repaation_canalisation':
      return ($partie == 'utile') ? 2 : false;
    default:
      return false;
    }
  }

  function get_emplacement()
  {
    return null;
  }

  function est_utilisable()
  {
    switch( $this->type )
    {
    case 'globe_pa':
    case 'parchemin_pa':
    case 'parchemin_tp':
    case 'potion_guerison':
    case 'potion_pm':
    case 'potion_vie':
    case 'objet_quete':
    case 'repaation_canalisation':
      return true;
    default:
      return false;
    }
  }

  function utiliser(&$perso, &$princ)
  {
    if( $perso->get_hp() <= 0 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous êtes mort !');
      return false;
    }
    if( $perso->get_pa() < $this->pa )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous n\'avez pas assez de PA');
      return false;
    }
    if( $perso->get_mp() < $this->mp )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous n\'avez pas assez de MP');
      return false;
    }
    $utilise = false;
    $modif_perso = false;
    switch( $this->type )
    {
		case 'potion_vie' :
      $princ->add( new interf_alerte(null, true) )->add_message('Vous utilisez une '.$this->nom.' elle vous redonne '.$this->effet.' points de vie');
      $perso->add_hp($row['effet']);
      $utilise = true;
      $modif_perso = true;

			// Augmentation du compteur de l'achievement
			$achiev = $joueur->get_compteur('use_potion');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
		break;
		case 'potion_guerison' :
      $buff_tab = array();
			foreach($perso->get_buff() as $buff)
			{
				if($buff->get_debuff() == 1)
				{
          if($buff->get_supprimable() == 1)
            $buff_tab[] = &$buff;
				}
			}
			if(count($buff_tab) > 0)
			{
        $ind = rand(0, count($buff_tab)-1);
        $buff_tab[$ind]->supprimer();
        $princ->add( new interf_alerte('success', true) )->add_message('Une malédiction a été correctement supprimée');
        $utilise = true;
        $modif_perso = true;
			}
			else
        $princ->add( new interf_alerte('warning', true) )->add_message('Vous n\'avez pas de malédiction a supprimer');
		break;
		case 'globe_pa' :
				$perso->add_pa( $this->effet );
        $princ->add( new interf_alerte('', true) )->add_message('UVous utilisez un '.$this->nom);
        $utilise = true;
        $modif_perso = true;
		break;
		/*case 'parchemin_tp' :
			$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$W_case = convertd_in_pos($joueur->get_x(), $joueur->get_y());
			//Calcul de la distance entre le point où est le joueur et sa ville natale
			$distance = detection_distance($W_case, convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']));
			if($row['effet'] >= $distance)
			{
				if(check_utilisation_objet($joueur, $objet))
				{
					//Téléportation du joueur
          $princ->add( new interf_txt('Vous utilisez un '.$row['nom']) );
          $princ->add( new interf_bal_smpl('br') );
          $img = new interf_bal_smpl('img');
          $img->set_attribut('src', 'image/pixel.gif');
          $img->set_attribut('onLoad', 'envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');');
          $princ->add( $img );
					$requete = "UPDATE perso SET x = ".$Trace[$perso->get_race()]['spawn_x'].", y = ".$Trace[$perso->get_race()]['spawn_y'].", pa = pa - ".$row['pa'].", mp = mp - ".$row['mp']." WHERE ID = ".$joueur->get_id();
					$db->query($requete);
				}
			}
			else
			{
        $princ->add_message('Vous êtes trop loin de la ville pour utiliser ce parchemin.', false);
			}
		break;*/
		case 'objet_quete' :
			if($this->id == 27)
			{ /// TODO: mettre en base
				?>
Journal du mage Demtros - Unité aigle -<br /> < Le journal est en très
mauvais état, certaines pages sont déchirées, ou rendues illisibles par
l'humidité et des taches de sang ><br /> <br /> 12 Dulfandal : Nous
approchons enfin de la cité de Myriandre. Je ne peux expliquer le
trouble qui me saisi depuis plusieurs jours mais je sais que cela à un
rapport avec les ruines de cette ville. Le malaise que je ressens est
presque palpable, mais impossible d'en déterminer la cause. J'en ai
fais part a Frankriss mais malgré toute la considération de ce dernier,
je sens bien qu'ils me pense juste un peu nerveux...<br /> Je vais
prendre deux tours de garde ce soir, de toute façon, je n'arriverais
pas à dormir.<br /> <br /> 13 Dulfandal : Nous voici enfin arrivés,
Achen et les autres sont partis en reconnaissance. Je scanne l'espace
astral dans l'espoir de déterminer d'où peux provenir la perturbation
que je ressens mais en vain.. Je sens malgré tout qu'il y a quelque
chose d'anormal.<br /> <br /> 14 Dulfandal : Achen est mort, Dubs est
l'agonie, dans son délire il est quand même parvenu à nous dire ce qui
c'est passé, il semblerait qu'il ai été attaqués par des goules surgie
de nulle part, Dubs a réussi à s'enfuir mais pas achen. il nous dis que
les goules ont disparues d'un coup... frankriss est préoccupé, mais il
gère la situation avec son sang froid coutumier. Nous allons former un
groupe d'assaut pour aller récupérer Achen, ou ce qu'il en reste...
L'unité aigle ne laisse jamais un compagnon derrière elle.<br /> Je ne
sais pas ce qui se passe dans cette ville mais j'ai un mauvais
pressentiment.<br /> 15 Dulfandal : le sort était camouflé, c'est pour
ça que je l'ai pas perçu, celui qui l'a lancer doit être un mage
exceptionnel pour arriver à camoufler une telle portion de terrain...
nous nous déplaçons les épées sorties et près au combat, l'ennemi peut
surgir de n'importe ou. Je n'arrive pas a démêler la trame du sort,
trop puissant pour moi ... ( taches de sang )<br /> Il faut que
j'écrive ( taches de sang ) sachent ce qui se passe ( taches de sang ),
un nécromancien ( la suite est déchirée ).<br />
				<?php
			}
		break;
    }
    if( $utilise )
    {
      if( $modif_perso or $this->pa or $this->mp )
      {
  			$perso->add_mp( -$this->mp );
  			$perso->add_pa( -$this->pa );
  			$perso->sauver();
        $princ->add_maj_perso();
      }
      return true;
    }
    return false;
  }
}
?>