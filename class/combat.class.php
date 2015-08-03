<?php
/*
	r = round
	c = competence
	s = sort
	e = esquive
	m = manque la cible avec sort
	l = lancement sort raté
	~12 = 12 degats
	~a = anticipation
	; = fin d'un round
	, = changement de personne ou effets
	n = s'approche
	cp = paralysé
	ce = etourdi
	cg = glacé
	cs = silence
	cc = caché
	ef = effet
	sv = tir vise
*/

class combat
{
	public $id;
	public $attaquant;
	public $defenseur;
	public $combat;
	public $id_journal;
	private $journal;
	
	/**	
		*	Constructeur permettant la création d'un combat
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-combat() qui construit un etat "vide".
		*		-combat($id) qui va chercher l'etat dont l'id est $id
		*		-combat($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $attaquant = 0, $defenseur = 0, $combat = '', $id_journal = 0)
	{
		global $db;
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT attaquant, defenseur, combat, id_journal FROM combats WHERE id = '.$id);
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->attaquant, $this->defenseur, $this->combat, $this->id_journal) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->attaquant = $id['attaquant'];
			$this->defenseur = $id['defenseur'];
			$this->id_journal = $id['id_journal'];
			$this->combat = $id['combat'];
		}
		else
		{
			$this->attaquant = $attaquant;
			$this->defenseur = $defenseur;
			$this->id_journal = $id_journal;
			$this->combat = $combat;
			$this->id = $id;
		}		
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE combats SET ';
			$requete .= 'attaquant = "'.$this->attaquant.'", defenseur = "'.$this->defenseur.'", id_journal = '.$this->id_journal.', combat = "'.$this->combat.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO combats (attaquant, defenseur, combat, id_journal) VALUES(';
			$requete .= '"'.$this->attaquant.'", "'.$this->defenseur.'", "'.$this->combat.'", '.$this->id_journal.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM combats WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_combat()
	{
		return $this->combat;
	}
	
	function get_journal()
	{
		return new journal($this->id_journal);
	}
	
	function &afficher_combat(&$interf_princ)
	{
		global $db, $G_interf, $G_url;
		if ($this->combat != NULL)
		{
			$this->journal = $this->get_journal();
			if( $this->journal->get_action() == 'defense' )
			{
				$attaquant = new perso(0, 0, $this->journal->get_passif());
				$defenseur = new perso(0, 0, $this->journal->get_actif());
			}
			else
			{
				$attaquant = new perso(0, 0, $this->journal->get_actif());
				$defenseur = new perso(0, 0, $this->journal->get_passif());
			}
			$logcombat = preg_replace("#r[0-9]+:#", "", $this->combat);
			$rounds = explode(';', $logcombat);

			$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Combat VS '.$defenseur->get_nom()) );
    	$interf = $cadre->add( $G_interf->creer_combat() );
			for($i = 0; $i < count($rounds); $i++)
			{
				$interf->nouveau_round($i+1);
				
				$each_attaque = explode(',', $rounds[$i] );
				$attaque = $each_attaque[0];
				$defense = $each_attaque[1];
				$effets_attaquant = $each_attaque[2];
				$effets_defenseur = $each_attaque[3];
				
				$interf->nouvelle_passe($mode);
				$this->aff_passe($interf, $attaquant, $defenseur, $attaque, $effets_attaquant, true);
				$interf->nouvelle_passe($mode);
				$this->aff_passe($interf, $defenseur, $attaquant, $defense, $effets_defenseur, false);
			}
			
			$journal = $this->get_journal();
			
			$interf->aff_fin($attaquant, $defenseur, $journal->get_valeur(), $journal->get_valeur2(), null, null, 'perso', false);
			
			$suivant = $journal->get_suivant('action = "attaque" OR action = "defense"');
			$precedent = $journal->get_precedent('action = "attaque" OR action = "defense"');
			$pagination = $cadre->add( new interf_bal_cont('ul', false, 'pager') );
			if($precedent)
			{
				$url = $G_url->get('id', 	$precedent->get_id());
				$onclick = 'return charger(this.href);';
				$classe = '';
			}
			else
			{
				$url = '#';
				$onclick = false;
				$classe = ' disabled';
			}
			$pagination->add( new interf_elt_menu('&larr; Précédent', $url, $onclick, false, 'previous'.$classe) );
			if($suivant)
			{
				$url = $G_url->get('id', 	$suivant->get_id());
				$onclick = 'return charger(this.href);';
				$classe = '';
			}
			else
			{
				$url = '#';
				$onclick = false;
				$classe = ' disabled';
			}
			$pagination->add( new interf_elt_menu('Suivant &rarr;', $url, $onclick, false, 'next'.$classe) );
			
			return $cadre;
		}
		else
			return false;
	}
	
	function aff_passe(&$interf, &$actif, &$passif, $action, $effets, $attaquant)
	{
		preg_match("#([a-z])([0-9a-z]*)(!)?(~([0-9aelm]+))?#i", $action, $attaque);
		/*
		$attaque[1] => c // Si c'est une compétence, un sort ...
		$attaque[2] => 0 // l'id de la compétence ou du sort
		$attaque[3] => ! // critiques
		$attaque[5] => e // les degats ou esquive
		*/
		switch( $attaque[1] )
		{
		case 'c': // Une compétence
			if($attaque[2] != 0 AND is_numeric($attaque[2]))
			{
				$comp = new comp_combat($attaque[2]);
				$interf->competence($comp->get_type(), $actif->get_nom(), $comp->get_nom());
			}
			else
				$interf->special('c'.$attaque[2], $actif->get_nom(), $passif->get_nom());
			
			if($attaque[3] == "!")
				$interf->critique();
			if($attaque[5] == "e") // Si c'est une esquive
				$interf->manque($actif->get_nom());
			else if($attaque[5] != NULL)
				$interf->degats($attaque[5], $actif->get_nom());
			break;
		case 's': // Un sort
			$sort = new sort_combat($attaque[2]);
			
			if($attaque[5] == "m") // Si c'est une esquive
				$interf->manque($actif->get_nom(), $sort->get_nom());
			else if($attaque[5] == "l") // Si c'est un sort raté
				$interf->rate($actif->get_nom(), $sort->get_nom());
			else
			{
				$interf->sort($sort->get_type(), $actif->get_nom(), $sort->get_nom());
				if($attaque[3] == "!")
					$interf->critique();
				$interf->degats($attaque[5], $actif->get_nom(), $sort->get_nom());
			}
			break;
		case 'a':
			$interf->anticipe( $passif->get_nom() );
			break;
		case 'n':
			$interf->approche($actif);
			break;
		}

		// On gère les effets
		preg_match_all("#&ef([0-9]+)~([0-9]+)#i", $effets, $effects_d);
		for($i=0;$i<count($effets_d[0]);$i++)
		{
			/*
			$effects_d[1][$i] => 7 // id de l'effet
			$effects_d[2][$i] => 4 // valeur des degats 
			*/
			$interf->effet($effets_d[1][$i], $effets_d[2][$i], $actif->get_nom(), $passif->get_nom());
		}
	}
}
?>
