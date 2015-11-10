<?php
/**
 * @file objet.class.php
 * Gestion des objets alchimiques, ingrédients, de quêtes, et autrs objets divers
 */

/**
 * Classe gérant les objets alchimiques, ingrédients, de quêtes, et autrs objets divers.
 * Correspond à la table du même nom dans la bdd.
 */
class objet extends objet_invent
{
	protected $stack;  ///< nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	protected $utilisable;  ///< indique si l'objet est utilisable.
	protected $description;  ///< description de l'objet.
	protected $effet;  ///< Valeur de l'effet de l'objet
	protected $pa;  ///< pa nécessaires pour utiliser l'objet.
	protected $mp;  ///< mp nécessaires pour utiliser l'objet.
  private $nombre;  ///< nombre d'exmplaires disponibles.
	const code = 'o';   ///< Code de l'objet.

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
	function get_description($format=false)
	{
    if($format)
    {
      $texte = $this->description;
    	while(preg_match("`%([a-z0-9]*)%`i",$texte, $regs))
    	{
    		$get = 'get_'.$regs[1];
    		$texte = str_replace('%'.$regs[1].'%', $this->$get(), $texte);
    	}
      return $texte;
    }
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


	/// Retourne la valeur de l'effet de l'objet
	function get_effet()
	{
		return $this->effet;
	}
	/// Modifie la valeur de l'effet de l'objet
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
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
	function get_mp()
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
		parent::init_tab($vals);
		$this->stack = $vals['stack'];
		$this->utilisable = $vals['utilisable'];
		$this->description = $vals['description'];
		$this->effet = $vals['effet'];
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
    $tbl['effet']='i';
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
    $vals = array($this->stack, $this->get_description(true), $this->prix);
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

	function get_colone_int($partie)
  {
    switch( $this->type )
    {
    case 'fabrication':
      return ($partie == 'artisanat') ? 0 : false;
    case 'recipient':
    case 'identification':
      return ($partie == 'artisanat') ? 1 : false;
    case 'globe_pa':
    case 'parchemin_pa':
    case 'parchemin_tp':
    case 'potion_guerison':
    case 'potion_pm':
    case 'potion_vie':
    case 'globe_buff':
    case 'globe_invocation':
    case 'globe_redist_mana':
    case 'globe_redist_vie':
    case 'globe_tp':
    case 'grand_parchemin_comp':
    case 'grand_parchemin_sort':
    case 'parchemin_comp':
    case 'parchemin_sort':
    case 'petit_parchemin_comp':
    case 'petit_parchemin_sort':
    case 'potion_buff':
    case 'potion_mana':
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
    case 'potion_guerison':
    case 'potion_pm':
    case 'potion_vie':
    case 'objet_quete':
    case 'repaation_canalisation':
    case 'globe_buff':
    case 'globe_invocation':
    case 'globe_redist_mana':
    case 'globe_redist_vie':
    case 'globe_tp':
    case 'grand_parchemin_comp':
    case 'grand_parchemin_sort':
    case 'parchemin_comp':
    case 'parchemin_sort':
    case 'petit_parchemin_comp':
    case 'petit_parchemin_sort':
    case 'potion_buff':
    case 'potion_mana':
      return true;
    default:
      return false;
    }
  }

  /// Indique si l'objet est slotable
  function est_slotable() { return false; }

  /// Indique si l'objet est slotable
  function est_enchassable() { return false; }

  function utiliser(&$perso, &$princ)
  {
  	global $Trace, $interf_princ;
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
      $princ->add( new interf_alerte('success', true) )->add_message('Vous utilisez une '.$this->nom.' elle vous redonne '.$this->effet.' points de vie');
      $perso->add_hp($this->get_effet());
      $utilise = true;
      $modif_perso = true;

			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('use_potion');
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
        $princ->add( new interf_alerte('success', true) )->add_message('Vous utilisez un '.$this->nom);
        $utilise = true;
        $modif_perso = true;
			break;
    case 'globe_tp':
			$W_case = convertd_in_pos($perso->get_x(), $perso->get_y());
			//Calcul de la distance entre le point où est le joueur et sa ville natale
			$distance = $perso->calcule_distance($Trace[$perso->get_race()]['spawn_x'], $Trace[$perso->get_race()]['spawn_y']); 
			if($this->effet >= $distance)
			{
				if($perso->get_pa() >= $this->get_pa() && $perso->get_mp() >= $this->get_mp())
				{
					$perso->supprime_objet($this->get_texte_id(), 1);
					//Téléportation du joueur
          $princ->add( new interf_txt('Vous utilisez un '.$row['nom']) );
          $princ->add( new interf_bal_smpl('br') );
          $img = new interf_bal_smpl('img');
          $img->set_attribut('src', 'image/pixel.gif');
          $img->set_attribut('onLoad', 'envoiInfo(\'infoperso.php?javascript=oui\', \'perso\');');
          $princ->add( $img );
					$perso->add_pa( -$this->get_pa() );
					$perso->add_mp( -$this->get_mp() );
					$perso->set_x( $Trace[$perso->get_race()]['spawn_x'] );
					$perso->set_y( $Trace[$perso->get_race()]['spawn_y'] );
					$perso->sauver();
				}
			}
			else
			{
        interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes trop loin de la ville pour utiliser ce globe.');
			}
		break;
		case 'potion_buff':
		case 'globe_buff':
			$sort = new sort_jeu($this->effet);
  		$utilise = $compsort->lance($perso, $perso, false, '', 'perso');
  		break;
		case 'petit_parchemin_sort':
			$utilise = $this->parchemin($perso, new sort_jeu($this->effet), false);
			break;
		case 'parchemin_sort':
			$utilise = $this->parchemin($perso, new sort_jeu($this->effet), 3);
			break;
		case 'grand_parchemin_sort':
			$utilise = $this->parchemin($perso, new sort_jeu($this->effet), true);
			break;
		case 'petit_parchemin_comp':
			$utilise = $this->parchemin($perso, new comp_jeu($this->effet), false);
			break;
		case 'parchemin_comp':
			$utilise = $this->parchemin($perso, new comp_jeu($this->effet), 3);
			break;
		case 'grand_parchemin_comp':
			$utilise = $this->parchemin($perso, new comp_jeu($this->effet), true);
			break;
		case 'potion_mana':
      $princ->add( new interf_alerte('success', true) )->add_message('Vous utilisez une '.$this->nom.' elle vous redonne '.$this->effet.' points de mana.');
      $perso->add_mp($this->get_effet());
      $utilise = true;
      $modif_perso = true;
			break;
    case 'globe_invocation':
    	$monstre = new monstre($this->effet );
			if( !$perso->can_dresse($monstre) )
			{
		    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez en dressage pour dresser ce monstre.');
		    break;
			}
			if($perso->nb_pet() >= $perso->get_comp('max_pet'))
			{
		    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas dresser plus de '.$perso->get_comp('max_pet').' créatures.');
		    break;
			}
			$utilise = $perso->add_pet($monstre->get_id(), $monstre->get_hp(), $monstre->get_energie() * 10);
			break;
    case 'globe_redist_mana':
    	if( $perso->get_mp() < $this->effet + $this->mp )
    	{
		    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de points de mana.');
		    break;
			}
			$groupe = new groupe( $perso->get_groupe() );
			$gain = round($this->effet / (count($groupe->get_membre()) - 1));
			foreach($groupe->get_membre() as $membre)
			{
				if( $membre->get_id() == $perso->get_id() )
					continue;
				$membre->add_mp( $gain );
				$membre->sauver();
			}
			$perso->add_mp( -$this->effet );
      $utilise = true;
      $modif_perso = true;
			break;
    case 'globe_redist_vie':
    	if( $perso->get_hp() <= $this->effet )
    	{
		    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de points de vie.');
		    break;
			}
			$groupe = new groupe( $perso->get_groupe() );
			$gain = round($this->effet / (count($groupe->get_membre()) - 1));
			foreach($groupe->get_membre() as $membre)
			{
				if( $membre->get_id() == $perso->get_id() )
					continue;
				$membre->add_hp( $gain );
				$membre->sauver();
			}
			$perso->add_hp( -$this->effet );
      $utilise = true;
      $modif_perso = true;
			break;
		case 'objet_quete' :
			if($this->id == 27)
			{ /// @todo mettre en base
$txt = "Journal du mage Demtros - Unité aigle -<br /> < Le journal est en très
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
un nécromancien ( la suite est déchirée ).<br />";
				$dlg = $interf_princ->set_dialogue( new interf_dialogBS($this->nom, true) );
				$dlg->add( new interf_txt($txt) );
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
      $perso->supprime_objet($this->get_texte(), 1);
      return true;
    }
    return false;
  }
  protected function parchemin(&$perso, &$compsort, $groupe)
  {
  	$compsort->set_duree( $compsort->get_duree() * 2 );
  	return $compsort->lance($perso, $perso, $groupe, '', 'perso');
	}

  /**
   * Mettre un slot
   */
  function mettre_slot(&$perso, &$princ, $niveau) { return false; }

  /**
   * Mettre une gemme ou en retirer une
   */
  function enchasser(&$perso, &$princ, $niveau) { return false; }

  /**
   * Retirer une gemme
   */
  function recup_gemme(&$perso, &$princ) { return false; }
}
?>