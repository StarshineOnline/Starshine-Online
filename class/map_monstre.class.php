<?php // -*- tab-width:2; mode: php -*-
/**
 * @file map_monstre.class.php
 * Définition de la classe map_monstre représentant un monstre sur la carte
 */

/**
 * Classe map_monstre
 * Classe représentant un monstre sur la carte
 */
class map_monstre extends entnj_incarn
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $id_monstre; ///< id de la définition dans la table "monstre"
	protected $level; ///< niveau du monstre
	protected $lib;
	protected $mort_naturelle; ///< Date de la mort naturelle du monstre
	public $nonexistant = false;


	/// Renvoie l'id de la définition du monstre
	function get_id_monstre()
	{
		return $this->id_monstre;
	}
	/**
	 * Retourne l'id de la définition dans la table "monstre"
	 * @deprecated   Ne plus utiliser, méthode à remplacer par get_id_monstre()
	 */
	function get_type()
	{
		return $this->id_monstre;
	}
	/// Renvoie l'objet représentant la définition
	function get_def()
	{
		return new monstre($this->id_monstre);
	}
	/// Modifie l'id de la définition du monstre
	function set_id_monstre($id_monstre)
	{
		$this->id_monstre = $id_monstre;
		$this->champs_modif[] = 'type';
	}
	
	/// Retourne le niveau du monstre
	function get_level()
	{
		return $this->level;
	}
	/// Modifie le niveau du monstre
	function set_level($level)
	{
		$this->level = $level;
 }
	/// Retourne la valeur de l'attribut "lib"
	function get_lib()
	{
		return $this->lib;
	}
	/// Modifie la valeur de l'attribut "lib"
	function set_lib($lib)
	{
		$this->lib = $lib;
	}
	/// Retourne la date de la mort naturelle du monstre
	function get_mort_naturelle()
	{
		return $this->mort_naturelle;
	}
	/// Modifie la date de la mort naturelle du monstre
	function set_mort_naturelle($mort_naturelle)
	{
		$this->mort_naturelle = $mort_naturelle;
		$this->champs_modif[] = 'mort_naturelle';
	}
	function get_race()
	{
		return 'neutre';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	* Constructeur
	* @param id                Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	* @param $id_monstre       Id de la définition du monstre
	* @param x                 Position x du monstre
	* @param y                 Position y du monstre
	* @param hp                HP¨actuels du monstre
	* @param level             Niveau du monstre
	* @param nom               Nom d monstre
	* @param lib
	* @param mort_naturelle    Date de la mort naturelle du monstre
	*/
	function __construct($id = 0, $id_monstre = 0, $x = 0, $y = 0, $hp = 0, $level = 0, $nom = '', $lib = '', $mort_naturelle = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT mm.type, mm.x, mm.y, mm.hp, m.level, m.nom, m.lib, mm.mort_naturelle FROM map_monstre mm, monstre m WHERE mm.type = m.id AND mm.id = '.$id);
      if( $db->num_rows($requeteSQL) > 0 )
			{
				$this->init_tab( $db->read_array($requeteSQL) );
			}
			else
			{
				$this->__construct();
				$this->nonexistant = true;
			}
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
      $this->init_tab($id);
		}
		else
		{
      entnj_incarn::__construct($nom, $x, $y, $hp, $id);
			$this->id_monstre = $id_monstre;
			$this->level = $level;
			$this->lib = $lib;
			$this->mort_naturelle = $mort_naturelle;
		}
	}
	
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    entnj_incarn::init_tab($vals);
		$this->id_monstre = $vals['type'];
		$this->level = $vals['level'];
		$this->lib = $vals['lib'];
		$this->mort_naturelle = $vals['mort_naturelle'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'x, y, hp, type, mort_naturelle';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->x.', '.$this->y.', '.$this->hp.', '.$this->id_monstre.', '.$this->mort_naturelle;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', type = '.$this->id_monstre.', mort_naturelle = '.$this->mort_naturelle;
	}
  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    if( $champ == 'type' )
      return $this->id_monstre;
    else
      return $this->{$champ};
  }
	// @}

  /**
   * @name  Buffs
   * Données et méthodes ayant trait aux buffs et débuffs actifs sur le monstre.
   */
  // @{
	protected $buff;  ///< Liste des buffs actifs sur le monstre
	/**
	 * Renvoie une propriété d'un buff / débuff particulier actif sur le personnage ou l'ensemble de ceux-ci.
	 * @param  $nom      Nom (type) du (dé)buff recherché, renvoie tous les buffs actifs si vaut false.
	 * @param  $champ    Propriété recherchée (correspond à un champ dans la bdd).
	 * @param  $type	   Si false on prend le premier buff, si true celui dont le type correspond à $nom.
	 * @return     Tableau des buffs ou valeur demandée.
	 */
	function get_buff($nom = false, $champ = false, $type = true)
	{
		if(!$nom)
		{
			$this->buff = buff_monstre::create('id_monstre', $this->id, 'id ASC', 'type');
			return $this->buff;
		}
		else
		{
			if(!isset($this->buff)) $this->get_buff();
			if(!$type)
			{
				$get = 'get_'.$champ;
				return $this->buff[0]->$get();
			}
			else
				foreach($this->buff as $buff)
				{
					if($buff->get_type() == $nom)
					{
						$get = 'get_'.$champ;
						return $buff->$get();
					}
				}
		}
	}

	/**
	 * Permet de savoir si le joueur est sous le buff nom
	 * @param $nom le nom du buff
	 * @param $type si le nom est le type du buff
	 * @return true si le perso est sous le buff false sinon.
 	*/
	function is_buff($nom = '', $type = true)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buffe = false;

		if(is_array($this->buff))
		{
			if(!empty($nom))
			{
				foreach($this->buff as $key => $buff)
				{
					if($type)
					{
						if($key == $nom) $buffe = true;
					}
					else if($buff->get_nom() ==  $nom)
					{
						$buffe = true;
					}
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;

		return $buffe;
	}
	// @}

  /**
   * @name  Spécifiques aux montres
   * Données et méthodes spécifiques aux monstres.
   */
  // @{
  private $affiche = null;
  function get_affiche() {
    if (!$this->affiche) {
      $m = new monstre($this->id_monstre);
      $this->affiche = $m->get_affiche();
    }
    return $this->affiche;
  }

  /// Supprime les buffs périmés actifs sur le monstre
	function check_monstre()
	{
		global $db;
		// On supprime tous les buffs périmés
		$requete = "DELETE FROM buff_monstre WHERE fin <= ".time();
		$req = $db->query($requete);
	}

  /// Gère les actions spéciales à effectuer lorsqu'un mosntre dedonjon a été tué
	function kill_monstre_de_donjon()
	{
		global $db;
    $log = new log_admin();
		switch ($this->id_monstre)
		{
		case 64: //Si c'est Devorsis on fait pop le fossoyeur
			$requete = "INSERT INTO map_monstre VALUES(NULL, '65','3','212','4800',"
        .(time() + 2678400).")";
			$db->query($requete);
			echo '<strong>Rha, tu me détruis aujourdhui mais le fossoyeur saura saisir ton âme... tu es déja mort !</strong><br/>';
      $log->send(0, 'donjon', "devoris tué, pop du fossoyeur");
			break;
			
		case 65: //Si c'est le fossoyeur on fait pop finwir
			$requete = "INSERT INTO map_monstre VALUES(NULL, '75','24','209','8000',"
        .(time() + 2678400).")";
			$db->query($requete);
			echo '<strong>Tu ne fait que retarder l\'inévitable, Le maître saura te faire payer ton insolence !</strong><br/>';
      $log->send(0, 'donjon', "fossoyeur tué, pop de finwir");
			break;
			
		case 75: //Si c'est Finrwirr on fait pop le gros monstre
			echo '<strong>Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!</strong><br/>';
      $req = $db->query("select decor from map where x = 20 and y = 227");
      $row = $db->read_assoc($req);
			if ($row['decor'] == 1601) // Si le gros monstre n'a pas ete vaincu, le passage vers le portail est encore un mur
			{
				$requete = "INSERT INTO map_monstre VALUES(NULL,116,18,227,10000,"
          .(time() + 2678400).")";
				$db->query($requete);
				// Ouverture du passage vers le gros monstre
				$requete = "UPDATE map set decor = 1539, info = 15 where x = 4 and (y >= 216 and y <= 219)";
				$db->query($requete);
        $log->send(0, 'donjon', "finwir tué, pop d'adénaïos");
			}
			else
			{
				echo '<em>Seul le silence répond à cet appel, Adenaïos le nécromant a déjà été vaincu ...</em><br/>';
        $log->send(0, 'donjon', "finwir tué, mais PAS de pop d'adénaïos");
			}
			break;

    case 116: // Si c'est le gros monstre, on ouvre le chemin vers le portail
    {
      echo "Maitre Aâzgruth reprend mon âme, ahhharghh, vous ne savez rien de ce qui vous attends... <em>Le squelette du nécromant se brise sous vos yeux. Une silhouette noir s'en dégage pendant quelque secondes avant d'être subitement avalée par le mur situé en face de vous ... qui eclate comme un miroir.</em><br/>";
      $requete = "update map set decor = 1539, info = 15 where y = 227 and (x = 20 or x = 21)";
      $db->query($requete);
      $requete = "update map set decor = 1676 where y = 226 and (x = 20 or x = 21)";
      $db->query($requete);
      $log->send(0, 'donjon', "adénaïos tué, ouverture du portail");
    }
			
		case 125:	//Si c'est un draconide
		case 126:
			//Si les 2 sont morts, on fait pop le roi gobelin
			$requete = "SELECT type FROM map_monstre WHERE type IN (125, 126)";
			$req_d = $db->query($requete);
			//Si il n'est pas là on le fait pop
			if($db->num_rows($req_d) == 0)
			{
				$requete = "INSERT INTO map_monstre VALUES(NULL,123,44,293,5800,"
          .(time() + 2678400).")";
				$db->query($requete);
				echo '<strong>Un bruit de mécanisme eveille votre attention, mais il vous est impossible de savoir d\'où provient ce son.</strong>';
        $log->send(0, 'donjon', "plus de draconides, ouverture du portail");
			}
      else
        $log->send(0, 'donjon', "un draconide tué, reste un");
			break;

		case 123: //Le roi des gobs fait pop le second roi des gobs
			$requete = "INSERT INTO map_monstre select NULL, id, 17, 292, hp, ".(time() + 2678400)." from monstre where lib = 'roi_goblin_2' limit 1";
			$db->query($requete);
			echo '<strong>Le roi gobelin Ziwek Rustog pousse un cri d\'une frénésie grotesque, se mettant à lancer tout un tas de babioles aux les quatre coins de la pièce. Vous regardez les objets voler tout autour de vous, tentant de les éviter ou les laissant ricocher sur vos armures. Cela devient presque un jeu. Vous reprenez peu à peu vos esprits, revenant vers le roi narquois, et vous comprenez que ce dernier vous a ensorcelé et s\'est carapaté. Devant vous, vous apercevez un petit passage avec des traces fraîches.</strong>';
      $log->send(0, 'donjon', "roi gob I tué, pop du roi gob II");
			break;

			// Les maraudeurs goeliers
		case 162:
		case 176:
		case 177:
		case 178:
      $duree = $this->id_monstre == 162 ? 8 : 1;
			echo '<strong>Sur le corps du geôlier, vous trouvez la clef de la porte et l\'ouvrez. La clef tombe en poussière après usage.</strong><br />';
			print_reload_area('deplacement.php?deplacement=centre', 'centre');
			ouvrePorteMaraudeurGeolier($this->x, $duree);
			break;
			
		default:
			// Rien à faire
		}
	}

  /// Cherche dans la bdd s'il y a une action spéciale à effectuer lors de la mort d'un mosntre de donjon et l'effectue si besoin
	function kill_monstre_de_donjon2()
	{
		global $db;
		$requete = "select * from monstre_special where type = $this->id_monstre";
		$req_d = $db->query($requete);
		if ($db->num_rows($req_d))
		{
			$row = $db->read_assoc($req);
			my_dump($row);
			// On va vérifier les prérequis
			$prerequis = true;
			if ($row['condition_sql'] !== null)
			{
				$req_p = $db->query($row['condition_sql']);
				if ($db->num_rows($req_d) == 0)
					$prerequis = false;
			}
			if ($row['non_condition_sql'] !== null)
			{
				$req_p = $db->query($row['non_condition_sql']);
				if ($db->num_rows($req_d) != 0)
					$prerequis = false;
			}
			if ($row['eval_condition'] !== null)
				$prerequis = $prerequis || eval($row['eval_condition']);
			if ($prerequis == true)
			{
				if ($row['texte'] !== null)
					echo "<strong>$row[texte]</strong>\n";
				if ($row['eval_action'] !== null)
					eval($row['eval_action']);
				if ($row['pop_type'] !== null)
				{
					$mort_naturelle = time() + 2678400;
					$x = $row['pop_x'];
					$y = $row['pop_y'];
					if ($x === null) $x = rand(1, 150);
					if ($y === null) $y = rand(1, 150);
					$requete = "INSERT INTO map_monstre SELECT null, id, ".
						"$x, $y, hp, $mort_naturelle ".
						"FROM monstre WHERE id = $row[pop_type]";
					$db->query($requete);
				}
			}
		}
	}

	function check_boss_loot(&$perso, $groupe)
	{
		global $db;
		$req = $db->query("select * from boss_loot where id_monstre = $this->id_monstre");
		if ($req && $db->num_rows($req)) {
			// récupère les loots possibles
			$tloot = array();
			while ($row = $db->read_object($req))
				$tloot[] = $row;

			// ajuste le taux de drop
			$taux = 1;
			if($perso->get_race() == 'humain')
				$taux *= 1.3;
			if($perso->is_buff('fouille_gibier')) 
				$taux *= (1 + ($perso->get_buff('fouille_gibier', 'effet') / 100));
			if ($taux > 1)
				foreach ($tloot as &$l)
					$l->chance = floor($l->chance * $taux);

			$grosbill = array();
			$loot = array();
			foreach ($tloot as $l) {
				if ($l->level == 0)
					$loot[] = $l;
				else
					$grosbill[] = $l;
			}
			// récupère les loots des joueurs sur le boss
			if ($groupe) {
				$ids = array();
				$mbr = $groupe->get_membre();
				$nb_joueurs = count($mbr);
				foreach ($mbr as $m)
					$ids[] = $m->get_id_joueur();
			} else {
				$nb_joueurs = 1;
				$ids = array($perso->get_id());
			}
			$id = implode(',', $ids);
			$req = $db->query("select * from joueur_loot where id_joueur in ($id) and id_monstre = $this->id_monstre");
			$old = $db->num_rows($req);
			// S'il y a déjà eu loot, on ne drop pas d'item grosbill
			if ($old > 0) $grosbill = array();
			// mélange les items
			shuffle($grosbill);
			shuffle($loot);

			$has_loot = false;
			if (count($grosbill)) {
				$range = 0;
				foreach ($grosbill as $l)
					$range += $l->chance;
				$tirage = mt_rand(1, $range);
				foreach ($grosbill as $l) {
					$tirage -= $l->chance;
					if ($tirage < 1) {
						$has_loot = true;
						loot_item($perso, $groupe, $l->item);
						$old++;
						break;
					}
				}
			}

			while (count($loot) && $nb_joueurs > $old) {
				$range = 0;
				foreach ($loot as $l)
					$range += $l->chance;
				$tirage = mt_rand(1, $range);
				foreach ($loot as $k => $l) {
					$tirage -= $l->chance;
					if ($tirage < 1) {
						$has_loot = true;
						loot_item($perso, $groupe, $l->item);
						$old++;
						break;
					}
				}
			}

			// enregistre les loots	
			if ($has_loot)
				$db->query('insert ignore into joueur_loot(id_joueur, id_monstre) '.
									 "select id, $this->id_monstre from perso where id in ($id)");
		}
	}
	// @}
	
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, &$def)
  {
    global $db, $G_xp_rate, $G_drop_rate, $G_range_level;
		//Le défenseur est mort !
		if ($this->get_hp() <= 0)
		{
			$coeff = 0.5;
			//Différence de level
			$diff_level = abs($perso->get_level() - $this->get_level());
			//Perde d'honneur
			$coeff = 1 - ($diff_level * 0.02);

			$gains_xp = true;
			$coef = 1;
			$gains_drop = true;
			$gains_star = true;

			// On gere les monstres de donjon
			$this->kill_monstre_de_donjon();

			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('kill_monstres');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();

			//Si c'est un Seigneur loup-garou on debloque l'achievement
			if($this->get_type() == 115)
				$perso->unlock_achiev('seigneur_loup_garou');
			if($this->get_type() == 56)
				$perso->unlock_achiev('sworling');
		}
		elseif ($perso->get_hp() <= 0 && !$pet) //L'attaquant est mort !
		{
			$coeff = 0.5;
			//Différence de level
			$diff_level = abs($attaquant->get_level() - $this->get_level());
			//Perde d'honneur
			$coeff = 1 - ($diff_level * 0.02);
			if ($coeff != 1)
			{
				echo 'Vous perdez '.
					($perso->get_honneur() - $perso->get_honneur() * $coeff).
					' honneur en mourrant.<br />';
				$perso->set_honneur($perso->get_honneur() * $coeff);
			}
			$perso->set_mort($perso->get_mort() + 1);
			$perso->sauver();

			//Si c'est Dévorsis
			if($this->get_type() == 64)
			{
				$gain_hp = floor($attaquant->get_hp_max() * 0.1);
				$this->set_hp($this->get_hp() + $gain_hp);
				$this->sauver();
				echo 'Dévorsis regagne '.$gain_hp.' HP en vous tuant.<br />';
			}

			// achievement
			if($this->get_type() == 1)
			{
				$perso->unlock_achiev('killer_rabbit');
			}
		}
		elseif($degats > 0)
		{
			$gains_xp = true;
			$coef = 0.5 * $degats / $def->get_hp();
		}
		if($gains_xp)
		{
			//Niveau du groupe
			if($perso->get_groupe() == 0)
			{
				$groupe = new groupe();
				$groupe->level_groupe = $perso->get_level();
				$groupe->somme_groupe = $perso->get_level();
				$groupe->set_share_xp(100);
				$groupe->membre_joueur[0] = $perso;
				$groupe->membre_joueur[0]->share_xp = 100;
			}
			else
			{
				$groupe = new groupe($perso->get_groupe());
				$groupe->get_membre();
			}
			//Gain d'expérience
			$requete = "SELECT xp, star, drops FROM monstre WHERE id = '".$this->get_type()."'";
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$xp = $row[0] * $G_xp_rate * $coef;
		}
		if($gains_drop)
		{
				$drop = $row[2];
		}
		if($gains_star)
		{
			$starmax = $row[1];
			$starmin = floor($row[1] / 2);
			$star = rand($starmin, $starmax) * $G_drop_rate;
			if($perso->get_race() == 'nain') $star = floor($star * 1.1);
			if($perso->is_buff('recherche_precieux')) $star = $star * (1 + ($perso->get_buff('recherche_precieux', 'effet') / 100));
			$star = ceil($star);
			$taxe = floor($star * $royaume->get_taxe_diplo($perso->get_race()) / 100);
			$star = $star - $taxe;
			//Récupération de la taxe
			if($taxe > 0)
			{
				$royaume->gain_star($taxe, 'monstre');
				$royaume->sauver();
			}
		}

		if($gains_drop)
		{
			$this->check_boss_loot($perso, $groupe);

			//Drop d'un objet ?
			$drops = explode(';', $drop);
			if($drops[0] != '')
			{
				$count = count($drops);
				$i = 0;
				while($i < $count)
				{
					$share = explode('-', $drops[$i]);
					$objet = $share[0];
					$taux = ceil($share[1] / $G_drop_rate);
					if($perso->get_race() == 'humain') $taux = $taux / 1.3;
					if($perso->is_buff('fouille_gibier')) $taux = $taux / (1 + ($perso->get_buff('fouille_gibier', 'effet') / 100));
					if ($taux < 2) $taux = 2; // Comme ca, pas de 100%
					$tirage = rand(1, floor($taux));
					//Si c'est un objet de quête :
					if($objet[0] == 'q')
					{
						$check = false;
						$i_quete = 0;
						$liste_quete = $perso->get_liste_quete();
						$count_quete = count($liste_quete);
						while(!$check AND $i_quete < $count_quete)
						{
							if($liste_quete[$i_quete]['id_quete'] == $share[1])
								$check = true;
							$i_quete++;
						}
						if($check) $tirage = 1;
						else $tirage = 2;
					}
					if($tirage == 1)
						loot_item($perso, $groupe, $objet);
					$i++;
				}
			}
		}

		if($gains_xp)
		{
			//Partage de l'xp au groupe
			if ($xp < 0) $xp = 0;

			$groupe->get_share_xp($perso->get_pos());
			foreach($groupe->membre_joueur as $membre)
			{
				//XP Final
				$xp_perso = $xp * (1 + (($this->get_level() - $membre->get_level()) / $G_range_level));
				$xp_perso = floor($xp_perso * $membre->share_xp / $groupe->get_share_xp($perso->get_pos()));
				if($xp_joueur < 0) $xp_perso = 0;
				$membre->set_exp($membre->get_exp() + $xp_perso);
				if($gains_star)
				{
					$star_perso = floor($star * $membre->share_xp / $groupe->get_share_xp($perso->get_pos()));
					$membre->set_star($membre->get_star() + $star_perso);
				}
				else $star_perso = 0;
				$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_perso.' XP</strong> et <strong class="reward">'.$star_perso.' Stars</strong><br />';
				//Vérification de l'avancement des quêtes solo pour le tueur, groupe pour les autres
				if($this->get_hp() <= 0)
				{
					if($membre->get_id() == $perso->get_id()) verif_action('M'.$this->get_type(), $membre, 's');
					else verif_action('M'.$this->get_type(), $membre, 'g');
				}
				$membre->sauver();
			}
		}
		return $msg_xp;
	}
}