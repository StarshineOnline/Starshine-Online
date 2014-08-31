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
		return $this->nom;
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
	function __construct($conds)
	{
	}
	static function factory($action)
	{
		$conds = null;
		if( $action[0] == '#' )
			list($action, $conds) = explode('@', $action);
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
}

class action_attaque extends action
{
}
?>