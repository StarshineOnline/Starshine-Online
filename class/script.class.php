<?php

abstract class script extends table
{
	protected $action;
	protected $nom;
	protected $mode;
	protected $actions;
	
	const mode_simple = 's';
	const mode_avance = 'a';
	
	function get_nom()
	{
		return $this->nom;
	}
	function set_nom($val)
	{
		$this->nom = $val;
		$this->champs_modif[] = 'nom';
	}
	function get_mode()
	{
		return $this->mode;
	}
	function set_mode($val)
	{
		$this->mode = $val;
		$this->champs_modif[] = 'mode';
	}
	
	function decode()
	{
		$actions = explode(';', $this->action);
		$this->actions = array();
		foreach($actions as $a)
		{
			if( !$a )
				continue;
			$this->actions[] = action::factory($a);
		}
	}
	function encode()
	{
		$actions = array();
		foreach($this->actions as $a)
		{
			$actions[] = $a->encode();
		}
		$this->action = implode(';', $actions);
		$this->champs_modif[] = 'action';
	}
	function &get_actions()
	{
		return $this->actions;
	}
	
	function change_position($ancien, $nouveau)
	{
		$actions = array();
		$j=0;
		for($i=0; $j<$nouveau; $i++)
		{
			if( $i == $ancien )
				continue;
			$actions[] = $this->actions[$i];
			$j++;
		}
		$actions[] = $this->actions[$ancien];
		$n = count($this->actions);
		for(; $i<$n; $i++)
		{
			if( $i == $ancien )
				continue;
			$actions[] = $this->actions[$i];
		}
		$this->actions = $actions; 
	}
	function ajout_action($action)
	{
		$this->actions[] = $action;
	}
	function suppr_action($ligne)
	{
		unset( $this->actions[$ligne] );
	}
	function copie_action($ligne)
	{
		$this->actions[] = clone $this->actions[$ligne];
	}
	function ajout_condition($ligne, $cond=null)
	{
		if( !$cond )
			$cond = new condition_util('<', 3);
		$this->actions[$ligne]->ajout_condition($cond);
	}
	function suppr_condition($ligne, $cond)
	{
		$this->actions[$ligne]->suppr_condition($cond);
	}
	function change_condition($ligne, $cond, $code)
	{
		$this->actions[$ligne]->change_condition($cond, $code);
	}
	function change_operateur($ligne, $cond, $op)
	{
		$this->actions[$ligne]->change_operateur($cond, $op);
	}
	function change_parametre($ligne, $cond, $param)
	{
		$this->actions[$ligne]->change_parametre($cond, $param);
	}
	function modifie_action($ligne, $type, $id)
	{
		$this->actions[$ligne] = $this->actions[$ligne]->nouveau($type, $id);
	}
	
	function init_simple($rounds)
	{
		for($i=0; $i<$rounds; $i++)
		{
			$this->actions[$i] = new action_attaque('#09='.$i);
		}
	}
	
	function __construct($nom='', $mode='', $action='')
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->mode = $mode;
			$this->action = $action;
		}
	}
	protected function init_tab($vals)
	{
		parent::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->mode = $vals['mode'];
		$this->action = $vals['action'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'action'=>'s', 'mode'=>'s');
	}
}

abstract class action
{
	protected $conds = array();
	function &get_conditions()
	{
		return $this->conds;
	}
	
	function encode()
	{
		if( count($this->conds) )
		{
			$conds = array();
			foreach($this->conds as $c)
			{
				$conds[] = $c->encode();
			}
			return implode('µ', $conds).'@';
		}
		else
			return '';
	}
	
	function ajout_condition($cond)
	{
		$this->conds[] = $cond;
	}
	function suppr_condition($cond)
	{	
		unset( $this->conds[$cond] );
	}
	function change_condition($cond, $code)
	{
		$op = $this->conds[$cond]->a_operateur();
		$param = $this->conds[$cond]->get_type_parametre();
		$txt = substr_replace($this->conds[$cond]->encode(), $code, 1, 2);
		$this->conds[$cond] = condition::factory($txt);
		if( $this->conds[$cond]->a_operateur() && !$op )
			$this->conds[$cond]->set_operateur('<');
		if( $this->conds[$cond]->get_type_parametre() != $param )
		{
			switch( $this->conds[$cond]->get_type_parametre() )
			{
			case condition::param_etat:
				$this->conds[$cond]->set_parametre( array_keys(condition::get_etats())[0] );
				break;
			case condition::param_valeur:
				$this->conds[$cond]->set_parametre( 3 );
				break;
			case condition::param_action:
				$this->conds[$cond]->set_parametre( array_keys(condition_action::get_actions())[0] );
				break;
			}
		}
	}
	function change_operateur($cond, $op)
	{
		$this->conds[$cond]->set_operateur($op);
	}
	function change_parametre($cond, $param)
	{
		$this->conds[$cond]->set_parametre($param);
	}
	function nouveau($type, $id)
	{
		$nouv = action::creer($type, $id);
		$nouv->conds = $this->conds;
		return $nouv;
	}
	
	function __construct($conds)
	{
		foreach(explode('µ', $conds) as $c)
		{
			if( !$c || $c == '#>' )
				continue;
			$this->conds[] = condition::factory($c);
		}
	}
	static function factory($action)
	{
		$conds = null;
		if( $action[0] == '#' )
			list($conds, $action) = explode('@', $action);
		switch($action[0])
		{
		case '!':
			return new action_attaque($conds);
		case '~':
			return new action_sort(mb_substr($action, 1), $conds);
		case '_':
			return new action_comp(mb_substr($action, 1), $conds);
		}
	}
	static function creer($type, $id, $cond=false)
	{
		switch($type)
		{
		case 'attaque':
			return new action_attaque('');
		case 'sort':
			return new action_sort($id, $cond);
		case 'comp':
			return new action_comp($id, $cond);
		default:
			return null;
		}
	}
}

class action_attaque extends action
{
	function get_nom() { return 'Attaquer'; }
	function get_info() { return 'Coûte 0 RM.'; }
	
	function encode()
	{
		return parent::encode().'!';
	}
}

abstract class action_comp_sort extends action
{
	protected $id;
	protected $comp_sort=null;
	function __construct($id, $conds)
	{
		parent::__construct(is_string($conds) ? $conds : '');
		$this->id = $id;
		if( $conds === true )
		{
			$comp_sort = $this->get_comp_sort();
			$etat = $comp_sort->get_etat_lie();
			if( $etat )
				$this->conds[] = condition::creer_etat($etat);
		}
	}
	function get_nom()
	{
		return $this->get_comp_sort()->get_nom();
	}
	function get_info()
	{
		$texte = $this->get_comp_sort()->get_description(true);
		$texte .= 'Coûte '.$this->get_comp_sort()->get_mp().' RM.';
		return $texte;
	}
}

class action_sort extends action_comp_sort
{
	function &get_comp_sort()
	{
		if( !$this->comp_sort )
			$this->comp_sort = new sort_combat($this->id);
		return $this->comp_sort;
	}
	
	function encode()
	{
		return parent::encode().'~'.$this->id;
	}
}

class action_comp extends action_comp_sort
{
	function &get_comp_sort()
	{
		if( !$this->comp_sort )
			$this->comp_sort = new comp_combat($this->id);
		return $this->comp_sort;
	}
	
	function encode()
	{
		return parent::encode().'_'.$this->id;
	}
}

abstract class condition
{
	protected $parametre;
	const param_etat = 1;
	const param_valeur = 2;
	const param_action = 3;
	function __construct($parametre)
	{
		$this->parametre = $parametre;
	}
	function get_parametre()
	{
		return $this->parametre;
	}
	function set_parametre($parametre)
	{
		$this->parametre = $parametre;
	}
	function a_operateur() { return false; }
	function get_operateur() { return '°'; }
	function set_operateur() {}
	function get_type_parametre() { return self::param_etat; }
	
	function encode()
	{
		return '#'.$this->get_code().$this->get_operateur().$this->get_parametre();
	}
	static function factory($cond)
	{
		switch( mb_substr($cond, 1, 2) )
		{
		case '00':
			return new condition_hp($cond[3], mb_substr($cond, 4));
		case '01':
			return new condition_rm($cond[3], mb_substr($cond, 4));
		case '09':
			return new condition_round($cond[3], mb_substr($cond, 4));
		case '10':
			return new condition_netat_soi(mb_substr($cond, 4));
		case '11':
			return new condition_netat_adv(mb_substr($cond, 4));
		case '12':
			return new condition_etat_soi(mb_substr($cond, 4));
		case '13':
			return new condition_etat_adv(mb_substr($cond, 4));
		case '14':
			return new condition_util($cond[3], mb_substr($cond, 4));
		case '15':
			return new condition_action(mb_substr($cond, 4));
		}
	}
	static function creer_etat($etat_lie)
	{
		$etat = explode('-', $etat_lie);
		switch($etat[0])
		{
		case 've':
			return new condition_etat_soi($etat[1]);
		case 'vne':
			return new condition_netat_soi($etat[1]);
		case 'ae':
			return new condition_etat_adv($etat[1]);
		case 'ane':
			return new condition_netat_adv($etat[1]);
		}
	}
	static function get_codes()
	{
		return array('14'=>'Nombre d\'utilisations', '09'=>'Rounds', '01'=>'Réserve de mana', '00'=>'HP', '10'=>'Vous n\'êtes pas', '11'=>'L\'ennemi n\'est pas', '12'=>'Vous êtes', '13'=>'L\'ennemi est', '15'=>'Vous venez de');
	}
	static function get_etats()
	{
		$etats = array();
		$etats['poison'] = 'Empoisonné';
		$etats['paralysie'] = 'Paralysé';
		$etats['etourdit'] = 'Etourdit';
		$etats['silence'] = 'sous Silence';
		$etats['dissimulation'] = 'Dissimulé';
		$etats['glace'] = 'Glacé';
		$etats['posture'] = 'En posture / Aura';
		$etats['berzeker'] = 'Berzerk';
		$etats['appel_foret'] = 'Appel de la forêt';
		$etats['appel_tenebre'] = 'Appel des ténêbres';
		$etats['recuperation'] = 'sous Récupération';
		$etats['benediction'] = 'Béni';
		$etats['lien_sylvestre'] = 'sous Lien Sylvestre';
		$etats['tellurique'] = 'Frappe Tellurique';
		$etats['glace_anticipe'] = 'Orbe de Glace';
		$etats['tir_vise'] = 'Tir visé';
		$etats['fleche_sable'] = 'Flèche de sable';
		$etats['fleche_poison'] = 'Flèche Empoisonnée';
		$etats['fleche_debilitante'] = 'Flèche Débilitante';
		$etats['derniere_chance'] = 'Dernière Chance';
		$etats['bouclier_protecteur'] = 'Bouclier Protecteur';
		$etats['embraser'] = 'Embrasé';
		$etats['desarme'] = 'Désarmé';
		return $etats;
	}
}

abstract class condition_op extends condition
{
	protected $operateur;
	function __construct($operateur, $parametre)
	{
		parent::__construct($parametre);
		$this->operateur = $operateur;
	}
	function get_operateur()
	{
		return $this->operateur;
	}
	function set_operateur($operateur)
	{
		$this->operateur = $operateur;
	}
	function a_operateur() { return true; }
	function get_type_parametre() { return self::param_valeur; }
}

class condition_hp extends condition_op
{
	function get_code() { return '00'; }
}
class condition_rm extends condition_op
{
	function get_code() { return '01'; }
}

class condition_round extends condition_op
{
	function get_code() { return '09'; }
}

class condition_util extends condition_op
{
	function get_code() { return '14'; }
}

class condition_netat_soi extends condition
{
	function get_code() { return '10'; }
}

class condition_netat_adv extends condition
{
	function get_code() { return '11'; }
}

class condition_etat_soi extends condition
{
	function get_code() { return '12'; }
}

class condition_etat_adv extends condition
{
	function get_code() { return '13'; }
}

class condition_action extends condition
{
	function get_code() { return '15'; }
	function get_type_parametre() { return self::param_action; }
	function get_actions()
	{
		return array('E'=>'esquiver', 'C'=>'faire un critique', 'B'=>'bloquer', 'T'=>'toucher');
	}
}
?>