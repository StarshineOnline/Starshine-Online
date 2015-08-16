<?php

class quete_perso extends table
{
	protected $id_perso;
	protected $id_quete;
	protected $id_etape;
	protected $avancement;
	protected $etape=null;
	protected $perso=null;
	protected $quete=null;
	/**
	* Constructeur
	*/
	function __construct($id_perso=0, &$id_quete=0, $id_etape=0, $avancement='')
	{
		if( func_num_args() == 1 )
		{
			$this->charger($id_perso);
		}
		else
		{
			$this->id_perso = $id_perso;
			$idq = $this->id_quete = is_object($id_quete) ? $id_quete->get_id() : $id_quete;
			if( $id_etape )
				$this->etape = new quete_etape($id_etape);
			else
				$this->etape = quete_etape::create(array('id_quete', 'etape'), array($idq, 1))[0];
			$this->id_etape = $etape ? $etape->get_id() : 0;
			if( is_object($id_quete) )
				$this->init_avancement();
			else
				$this->avancement = $avancement;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_perso = $vals['id_perso'];
		$this->id_quete = $vals['id_quete'];
		$this->id_etape = $vals['id_etape'];
		$this->avancement = $vals['avancement'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_perso'=>'i', 'id_quete'=>'i', 'id_etape'=>'i', 'avancement'=>'s');
	}
	
	protected function init_avancement()
	{
		$objectifs = $this->etape ? explode(';', $this->etape->get_objectif()) : array();
		for($i=0; $i<count($objectifs); $i++)
		{
			$obj = explode(':', $objectifs[$i]);
			$objectifs[$i] = $obj[0].':0';
		}
		$this->avancement = implode(';', $objectifs);
	}
	
	/// Modifie l'étape
	function get_id_quete()
	{
		return $this->id_quete;
	}
	/// Renvoie la quete corespondante
	function &get_quete()
	{
		if( !$this->quete )
			$this->quete = new quete($this->id_quete);
		return $this->quete;
	}
	
	/// Modifie l'étape
	function get_id_etape()
	{
		return $this->id_etape;
	}
	/// Modifie l'étape
	function set_id_etape($valeur)
	{
		$this->id_etape = $valeur;
		$this->champs_modif[] = 'id_etape';
	}
	
	/// Renvoie l'épape corespondante
	function &get_etape()
	{
		if( !$this->etape )
			$this->etape = new quete_etape($this->id_etape); 
		return $this->etape;
	}
	
	/// Renvoie le personnage corespondant
	function &get_perso()
	{
		if( !$this->perso )
			$this->perso = new perso($this->id_perso); 
		return $this->perso;
	}
	
	/// Renvoie l'avancement
	function get_avancement()
	{
		return $this->avancement;
	}
	
	/// Modifie l'avancement
	function set_avancement($valeur)
	{
		$this->avancement = $valeur;
		$this->champs_modif[] = 'avancement';
	}
	
	// Vérification si une étape est finie ou non
	function verifier()
	{
		$etape = $this->get_etape();
		
		// Avancement des objetifs
		$avancement = array();
		$avanc = explode(';', $this->get_avancement());
		foreach($avanc as $a)
		{
			$a = explode(':', $a);
			$avancement[$a[0]] = $a[1];
		}
		
		// On vérifie les objectifs
		$objectifs = explode(';', $etape->get_objectif());
		foreach($objectifs as $obj)
		{
			$type = mb_substr($obj, 1);
			$valeur = explode(':', $type);
			/// @todo passer à l'objet
			switch($obj[0])
			{
			case 'L': // trouver un objet
					interf_debug::enregistre('Vérifier trouver objet : '.$avancement[$obj[0].$valeur[0]].' VS '.$valeur[1]);
			case 'J': // tuer des perso selon la diplomatie
			case 'M':  // tuer des monstres
			case 'O': // rapporter un objet
				if( $avancement[$obj[0].$valeur[0]] < $valeur[1] )
					return false;
				break;
			case 'P': // parler à un PNJ;
			case 'C': // case
				if( $avancement[$obj[0].$valeur[0]] == 0 )
					return false;
				break;
			}
		}
		return true;
	}
	
	function avance($objectif, $valeur=1)
	{
		$avanc = explode(';', $this->avancement);
		if( is_numeric($objectif) )
		{
			$av = explode(':', $avanc[$objectif]);
			$av[1] += $valeur;
		}
		else
		{
			for($i=0; $i<count($avanc); $i++)
			{
				$av = explode(':', $avanc[$i]);
				if( $av[0] == $objectif )
				{
					$av[1] += $valeur;
					break;
				}
			}
		}
		$this->avancement = implode(';', $avanc);
		$this->champs_modif[] = 'type';
	}
	
	static function verif_action($type_cible, &$perso, $mode, $option=null)
	{
		/// Quêtes du personnage
		$quetes_perso = quete_perso::create('id_perso', $perso->get_id());
		$msg = '';
		foreach($quetes_perso as $qp)
		{
			// Avancements
			$avancements = explode(';', $qp->get_avancement());
			$a_verifier = false;
			foreach($avancements as $i=>$avanc)
			{
				// on vérifie si l'objectif correspond 
				$valeur = explode(':', $avanc);
				switch($avanc[0])
				{
				case 'L': // trouver un objet
					interf_debug::enregistre('Trouver objet : "'.$valeur[0].'" VS "'.$type_cible.'"');
				case 'M':  // tuer des monstres
				case 'J': // tuer des perso selon la diplomatie
				case 'O': // rapporter un objet
					$ok = $valeur[0] == $type_cible;
					$max = null;
					break;
				case 'P': // parler à un PNJ;
				case 'C': // case
					$cibles = explode('|', mb_substr($valeur[0], 1));
					$id_cible = substr($type_cible, 1);
					$ok = in_array($id_cible, $cibles);
					$max = 1;
				break;
				default:
					/// @todo loguer erreur
					$ok = false;
				}
				if( $ok )
				{
					// On récupère l'étape correspondante
					$etape = $qp->get_etape();
					// On vérifie la collaboration
					interf_debug::enregistre('Collaboration : '.$etape->get_collaboration().' & '.$mode);
					if( ($etape->get_collaboration() == "aucune" && $mode != "s") || ($etape->get_collaboration() != "royaume" && $mode == "r") )
						break;
					// on regarde la valeur à atteindre
					if( $max === null )
					{
						$objectifs = explode(';', $etape->get_objectif());
						foreach($objectifs as $obj)
						{
							$o = explode(':', $obj);
							if( $avanc[0] = $o[0] )
							{
								$max = $o[1];
								break;
							}
						}
					}
					interf_debug::enregistre('Maximum ('.$avanc[0].') : '.$max);
					// On regarde si on atteint le maximum du compteur
					if( $valeur[1] < $max )
					{
						$valeur[1]++;
						$avancements[$i] = implode(':', $valeur);
						$qp->set_avancement( implode(';', $avancements) );
					}
					// Si la copération est à royaume on regarde les autres membres du royaume (hors groupe)
					if( $etape->get_collaboration() == "royaume" && $mode == 's' )
					{
						/// @todo à améliorer
						$requete = 'SELECT p.* FROM perso AS p INNER JOIN quete_perso AS qp ON qp.id_perso = p.id WHERE p.race = "'.$perso->get_race().'" AND qp.id_etape = '.$qp->get_id_etape().' AND ';
						if( $perso->get_groupe() )
							$requete .= 'p.groupe != '.$perso->get_groupe();
						else
							$requete .= 'p.id != '.$perso->get_id();
						$req = $db->query($requete);
						while( $row = $db->read_assoc($req) )
						{
							$p = new perso($row);
							self::verif_action($type_cible, $p, 'r', $option);
						}
					}
					$a_verifier = true;
				}
			}
			if($a_verifier)
			{
				if( $qp->verifier() )
				{
					if( $mode == 's' )
					{
						$etape->gain_groupe($perso);
						$etape->gain_royaume($perso);
					}
					interf_debug::enregistre('Fin de quête ('.$qp->get_quete()->get_type().')');
					switch( $qp->get_quete()->get_type() )
					{
					case 'royaume':
					case 'groupe':
						if( $mode == 's' )
						{
							if( $perso->get_groupe() )
							{
								$requete = 'SELECT qp.*, p.id AS pid FROM quete_perso AS qp INNER JOIN perso AS p ON p.id = qp.id_perso WHERE p.groupe = '.$this->get_perso()->get_groupe().' AND qp.id_quete = '.$this->id_quete;
								$req = $db->query($requete);
								while( $row = $db->read_assoc($req) )
								{
									$membre = $row['pid'] == $perso->get_id() ? $perso : new perso($row['pid']);
									$qpm = new quete_perso($row);
									$msg .= $etape->fin($membre, $option == ':silencieux');
									$qpm->perso = &$membre;
									$suiv = $qpm->fin($option);
								}
							}
							else
							{
								$msg .= $etape->fin($perso, $option == ':silencieux');
								$qp->perso = &$perso;
								$suiv = $qp->fin($option);
							}
							if( $this->get_quete()->get_type() == 'royaume' )
							{
								if( $suiv )
								{ 
									// si c'est une quête de royaume on avance pour les autres (hors membres du groupe pour qui c'est déjà fait)
									if( $perso->get_groupe() )
										$requete = 'UPDATE quete_perso AS qp INNER JOIN perso AS p ON p.id = qp.id_perso SET id_etape = '.$suiv.' WHERE p.race = "'.$perso->get_race().'" AND qp.id_quete = '.$this->id_quete.' AND p.groupe != '.$perso->get_groupe();
									else
										$requete = 'UPDATE quete_perso AS qp INNER JOIN perso AS p ON p.id = qp.id_perso SET id_etape = '.$suiv.' WHERE p.race = "'.$perso->get_race().'" AND qp.id_quete = '.$this->id_quete.' AND p.id_perso != '.$this->id_perso;
									$req = $db->query($requete);
								}
								else
								{
									// si on fini une quête de royaume alors on la supprime pour tout le monde
									/// @todo mettre une ligne dans le journal
									$requete = 'DELETE FROM quete_perso WHERE id_quete = '.$this->id_quete;
									$req = $db->query($requete);
								}
							}
						}
						break;
					case 'individuel':
						$msg .= $etape->fin($perso, $option == ':silencieux');
						$qp->perso = &$perso;
						$qp->fin($option);
					}
				}
				else
					$qp->sauver();
			}
		}
		return $msg; 
	}
	protected function fin($option)
	{
		global $db;
		$etape = $this->get_etape()->get_etape();
		$nbr = $this->get_quete()->get_nombre_etape();
		if( $etape < $nbr )
		{
			interf_debug::enregistre('Etape suivante ('.$etape.' / '.$nbr.')');
			if( $option && is_numeric($option) )
				$nouv = quete_etape::create(array('id_quete', 'etape', 'variante'), array($this->id_quete, $etape+1, $option));
			else
				$nouv = quete_etape::create(array('id_quete','etape'), array($this->id_quete, $etape+1));
			switch( count($nouv) )
			{
			case 0:
				/// @todo loguer erreur
				$this->supprimer();
				return false;
			case 1:
				$nouv = $nouv[0];
				break;
			default:
				foreach($nouv as $n)
				{
					if( quete::verif_requis($n->get_requis(), $this->get_perso()) )
					{
						$nouv = $n;
						break;
					}
				}
			}
			$nouv->initialiser();
			$this->set_id_etape( $nouv->get_id() );
			$this->etape = &$nouv;
			$this->init_avancement();
			$this->sauver();
			return $nouv->get_id();
		}
		else
		{
			$this->supprimer();
			return false;
		}
	}
	function verif_inventaire()
	{
		$objets = $this->get_etape()->verif_inventaire($this->get_perso());
		if( $objets )
		{
			$avancements = explode(';', $qp->get_avancement());
			foreach($objets as $obj=>$n)
			{
				$this->perso->supprime_objet($obj, $n);
				foreach($avancements as $i=>$avanc)
				{
					$valeur = explode(':', $avanc);
					if( $valeur[0] == 'o'.$obj )
					{
						$valeur[1] = $n;
						$avancements[$i] = implode(':', $valeur);
						break;
					}
				}
			}
			$qp->set_avancement( implode(';', $avancements) );
			$this->perso->sauver();
			if( $this->verifier() )
			{
				$this->etape->fin($this->perso);
				$this->fin();
			}
		}
	}
}

?>