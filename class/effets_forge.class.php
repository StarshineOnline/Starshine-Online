<?php

include_once(root.'class/effect.class.php');

class effet_forge extends effet
{
	protected $effet;
	protected $cible_passif;
	const comp = 1;
	const sort = 2;
	const tout = 3;
	function __construct($effet, $cible_passif=true)
	{
		$this->effet = $effet;
		$this->cible_def = $cible_passif;
	}
	protected function &get_cible(&$attaque)
	{
		if( $this->cible_passif )
			return $attaque->get_passif();
		else
			return $attaque->get_actif();
	}
}

class forge_poison extends effet_forge
{
	function inflige_degats(&$attaque)
	{
		$cible = $this->get_cible($attaque);
		$chances = 10 + 5 * $this->effet;
		$chances *= 1 + $attaque->get_actif()->get_bonus_permanents('resiste_debuff') / 100;
		if( comp_sort::test_de($chances, 100) )
		{
			$attaque->get_interface()->effet(31,  $this->effet, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef31~'. $this->effet);
			$cible->etat['empoisonne']['effet'] = $this->effet;
			$cible->etat['empoisonne']['duree'] = $this->effet;
		}
	}
}

class forge_poison_rate extends effet_forge
{
	function rate(&$attaque)
	{
		$cible = $attaque->get_actif();
		$chances = 10 + 5 * $this->effet;
		if( comp_sort::test_de($chances, 100) )
		{
			$attaque->get_interface()->effet(31,  $this->effet, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef31~'. $this->effet);
			$cible->etat['empoisonne']['effet'] = $this->effet;
			$cible->etat['empoisonne']['duree'] = $this->effet;
		}
	}
}

class forge_degats extends effet_forge
{
	protected $chance;
	function __construct($chance, $effet)
	{
		$this->chance = $chance;
		$this->effet = $effet;
	}
	function calcul_degats(&$attaque)
	{
		if( comp_sort::test_de($this->chance, 100) )
			$attaque->add_degats($this->effet);
	}
	function calcul_degats_magiques(&$attaque)
	{
		if( comp_sort::test_de($this->chance, 100) )
			$attaque->add_degats($this->effet);
	}
}

class forge_blocage extends effet_forge
{
  function calcul_bloquage(&$attaque)
	{
    $passif = $attaque->get_passif();
    $blocage = $passif->get_potentiel_bloquer() * $this->effet;
    $passif->set_potentiel_bloquer( $blocage );
  }
}

class botte_enchainement extends effet_forge
{
  function debut_round(&$attaque)
  {
    $actif = $attaque->get_actif();
		if( isset($actif->precedent['touche']) && $actif->precedent['touche'] )
		{
			$actif->set_potentiel_toucher( $actif->get_potentiel_toucher() * (1 + $this->effet/100) );
			$actif->set_potentiel_critique( $actif->get_potentiel_critique() * (1 + $this->effet/100) );
		}
  }
}

class forge_vampirisme extends effet_forge
{
	function inflige_degats(&$attaque)
	{
		$cible = $attaque->get_actif();
		$chances = 10 + 10 * $this->effet;
		if( comp_sort::test_de($chances, 100) )
		{
			$attaque->get_interface()->effet(30,  $this->effet, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef30~'. $this->effet);
			$hp = min($cible->get_hp() + $this->effet, $cible->get_hp_max());
			$cible->set_hp($hp);
		}
	}
}

class forge_saignement extends effet_forge
{
	function calcul_mult_critique(&$attaque)
	{
		$cible = $this->get_cible($attaque);
		if( comp_sort::test_de($this->effet, 100) )
		{
			$cible->etat['hemorragie']['effet'] = 1;
			$cible->etat['hemorragie']['duree'] = 21;
		}
	}
}

class forge_transperce extends effet_forge
{
	function calcul_pp(&$attaque)
	{
		$cible = $this->get_cible($attaque);
		if( comp_sort::test_de($this->effet, 100) )
		{
			$attaque->valeur = 0;
		}
	}
}

class forge_toucher extends effet_forge
{
	function calcul_attaque_physique(&$attaque)
	{
		$attaque->valeur *= $this->effet;
	}
}

class forge_coup_bouclier extends effet_forge
{
	function coup_bouclier(&$attaque)
	{
		$attaque->valeur *= $this->effet;
	}
}

class forge_resiste_etourdit extends effet_forge
{
	function resite_etourdissement(&$attaque)
	{
		$attaque->valeur *= $this->effet;
	}
}

class forge_epines extends effet_forge
{
	protected $chances;
	function __construct($chances, $effet)
	{
		$this->chances = $chances;
		$this->effet = $effet;
	}
	function inflige_degats(&$attaque)
	{
		$cible = $attaque->get_actif();
		$chances = $this->effet;
		if( comp_sort::test_de($chances, 100) )
		{
			$attaque->get_interface()->effet(33,  $this->effet, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef33~'.$this->effet);
			$cible->set_hp( $cible->get_hp() - $this->effet );
		}
	}
}

class forge_rm extends effet_forge
{
	protected $comp;
	protected $chances;
	function __construct($chances, $effet, $comp=true)
	{
		$this->chances = $chances;
		$this->effet = $effet;
		$this->comp = $comp;
	}
	function calcul_mp(&$attaque)
	{
		if( !$this->comp && $attaque->get_type_degats() == 'comp' )
			return;
		if( comp_sort::test_de($this->chances, 100) )
			$attaque->valeur += $this->effet;
	}
}

class forge_anticipation extends effet_forge
{
	function anticipation(&$attaque)
	{
		$attaque->valeur += $this->effet;
	}
}

class forge_critique extends effet_forge
{
	protected $type;
	function __construct($effet, $type=3)
	{
		$this->effet = $effet;
		$this->type = $type;
	}
	function calcul_critique(&$attaque)
	{
		if( $this->type & self::comp )
			$attaque->valeur *= $this->effet;
	}
	function calcul_critique_magique(&$attaque)
	{
		if( $this->type & self::sort )
			$attaque->valeur *= $this->effet;
	}
}

class forge_mult_critique extends effet_forge
{
	protected $type;
	function __construct($effet, $type=3)
	{
		$this->effet = $effet;
		$this->type = $type;
	}
	function calcul_mult_critique(&$attaque)
	{
		if( $this->type & self::comp )
			$attaque->valeur *= $this->effet;
	}
	function calcul_mult_critique_magique(&$attaque)
	{
		if( $this->type & self::sort )
			$attaque->valeur *= $this->effet;
	}
}

class forge_division extends effet_forge
{
	protected $facteur;
	function __construct($effet, $facteur)
	{
		$this->effet = $effet;
		$this->facteur = $facteur;
	}
	function calcul_degats(&$attaque)
	{
		if( comp_sort::test_de($this->effet, 100) )
		{
			$attaque->mult_degats( $this->facteur );
		}
	}
}

class forge_etat extends effet_forge
{
	protected $etat;
	protected $type;
	function __construct($effet, $etat, $cible_passif=true, $type=3)
	{
		$this->effet = $effet;
		$this->etat = $etat;
		$this->type = $type;
		$this->cible_def = $cible_passif;
	}
	function inflige_degats(&$attaque)
	{
		$cible = $this->get_cible($attaque);
		if( $this->type & self::comp && comp_sort::test_de($this->effet, 100) )
		{
			$attaque->get_interface()->effet(14, 1, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef14~'.1);
			$cible->etat[$this->etat]['effet'] = 1;
			$cible->etat[$this->etat]['duree'] = 1;
		}
	}
	function inflige_degats_magiques(&$attaque)
	{
		$cible = $this->get_cible($attaque);
		if( $this->type & self::sort && comp_sort::test_de($this->effet, 100) )
		{
			$attaque->get_interface()->effet(14, 1, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef14~'.1);
			$cible->etat[$this->etat]['effet'] = 1;
			$cible->etat[$this->etat]['duree'] = 1;
		}
	}
}

class forge_surcharge extends effet_forge
{
	protected $type;
	protected $chances;
	function __construct($chances, $effet, $type=3)
	{
		$this->chances = $chances;
		$this->effet = $effet;
		$this->type = $type;
	}
	function calcul_mult_critique(&$attaque)
	{
		if( $this->type & self::comp && comp_sort::test_de($this->effet, 100) )
		{
			$attaque->get_interface()->effet(33,  2, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef33~'. 2);
			$cible->set_hp( $cible->get_hp() - $this->effet );
		}
	}
	function calcul_mult_critique_magique(&$attaque)
	{
		if( $this->type & self::sort && comp_sort::test_de($this->effet, 100) )
		{
			$attaque->get_interface()->effet(33,  2, '', $cible->get_nom());
			$attaque->add_log_effet_actif('&ef33~'. 2);
			$cible->set_hp( $cible->get_hp() - $this->effet );
		}
	}
}

?>