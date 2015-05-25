<?php

include(root.'class/effets_forge.class.php');

class forge_recette extends table
{
	protected $nom;
	protected $objet;
	protected $difficulte;
	protected $type_bonus;
	protected $effet_bonus;
	protected $type_malus;
	protected $effet_malus;
	
	// Renvoie le nom de la modification
	function get_nom()
	{
		return $this->nom;
	}
	/// Modifie le nom de la modification
	function set_nom($val)
	{
		$this->nom = $val;
		$this->champs_modif[] = 'nom';
	}
	
	// Renvoie le code de l'objet modifié
	function get_objet()
	{
		return $this->objet;
	}
	/// Modifie le code de l'objet modifié
	function set_objet($val)
	{
		$this->objet = $val;
		$this->champs_modif[] = 'objet';
	}
	
	// Renvoie la difficulté de la modification
	function get_difficulte()
	{
		return $this->difficulte;
	}
	/// Modifie la difficulté de la modification
	function set_difficulte($val)
	{
		$this->difficulte = $val;
		$this->champs_modif[] = 'difficulte';
	}
	
	// Renvoie le type de bonus de la modification
	function get_type_bonus()
	{
		return $this->type_bonus;
	}
	/// Modifie le type de bonus de la modification
	function set_type_bonus($val)
	{
		$this->type_bonus = $val;
		$this->champs_modif[] = 'type_bonus';
	}
	
	// Renvoie l'effet du bonus de la modification
	function effet_bonus()
	{
		return $this->effet_bonus;
	}
	/// Modifie l'effet du bonus de la modification
	function set_effet_bonus($val)
	{
		$this->effet_bonus = $val;
		$this->champs_modif[] = 'effet_bonus';
	}
	
	// Renvoie le type de malus de la modification
	function get_type_malus()
	{
		return $this->type_malus;
	}
	/// Modifie le type de malus de la modification
	function set_type_malus($val)
	{
		$this->type_malus = $val;
		$this->champs_modif[] = 'type_malus';
	}
	
	// Renvoie l'effet du malus de la modification
	function get_effet_malus()
	{
		return $this->effet_malus;
	}
	/// Modifie l'effet du malus de la modification
	function set_effet_malus($val)
	{
		$this->effet_malus = $val;
		$this->champs_modif[] = 'effet_malus';
	}
	
	function __construct($nom='', $objet='', $difficulte=0, $type_bonus='', $effet_bonus=0, $type_malus='', $effet_malus=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->objet = $objet;
			$this->difficulte = $difficulte;
			$this->type_bonus = $type_bonus;
			$this->effet_bonus = $effet_bonus;
			$this->type_malus = $type_malus;
			$this->effet_malus = $effet_malus;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->objet = $vals['objet'];
		$this->difficulte = $vals['difficulte'];
		$this->type_bonus = $vals['type_bonus'];
		$this->effet_bonus = $vals['effet_bonus'];
		$this->type_malus = $vals['type_malus'];
		$this->effet_malus = $vals['effet_malus'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'objet'=>'s', 'difficulte'=>'i', 'type_bonus'=>'s', 'effet_bonus'=>'i', 'type_malus'=>'s', 'effet_malus'=>'i');
	}
	
	function get_ingredients()
	{
		return forge_recette_ingredient::create('id_recette', $this->id);
	}
	
	/// Applique les modifications de l'objet au personnage (sauf dégâts, PP, PM, coeff)
	function applique(&$perso)
	{
		switch($this->type_bonus)
		{
		case 'blocage': // +X% de potentiel bloquer
			$perso->add_bonus_permanents('mult_blocage', $this->effet_bonus);
			break;
		case 'poison': // 10+5*X% d'empoisonner niveau X quand une attaque physique touche
			$perso->add_effet_permanent('attaquant', new forge_poison($this->effet_bonus));
			break;
		case 'reduction': // X% de réduire les dégats de 2 (avant critique)
			$perso->add_effet_permanent('defenseur', new forge_degats($this->effet_bonus, -2));
			break;
		case 'degats_supp': // X% d'infliger 2 dégât en plus (avant critique)
			$perso->add_effet_permanent('attaquant', new forge_degats($this->effet_bonus, 2));
			break;
		case 'blocage_adv': // -X% au potentiel bloquer adverse
			$perso->add_effet_permanent('attaquant', new forge_blocage(1 / (1 + $this->effet_bonus/100)));
			break;
		case 'enchainement': // +X% au potentiel toucher et critique après une attaque réussie
			$perso->add_effet_permanent('attaquant', new forge_enchainement($this->effet_bonus));
			break;
		case 'vampirisme': // 10+10*X% de gagner X PV sur une attaque réussie
			$perso->add_effet_permanent('attaquant', new forge_vampirisme($this->effet_bonus));
			break;
		case 'saignement': // X% de chance d'infliger 1 dégat à la fin de chaque round jusqu'à la fin du combat après un critique.
			$perso->add_effet_permanent('attaquant', new forge_saignement($this->effet_bonus));
			break;
		case 'mult_critique': // + X% au multiplicateur critique
			$perso->add_bonus_permanents('mult_critique', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_critique_magique', $this->effet_bonus);
			break;
		case 'critique': // + X% au potentiel critique
			$perso->add_bonus_permanents('critique', $this->effet_bonus);
			$perso->add_bonus_permanents('critique_magique', $this->effet_bonus);
			break;
		case 'eguise': // + 5*X% au potentiel critique, + X% au multiplicateur critique
			$perso->add_bonus_permanents('mult_critique', $this->effet_bonus);
			$perso->add_bonus_permanents('critique', $this->effet_bonus*5);
			break;
		case 'traverser': // +X aux chances de traverser l'armure
			$perso->add_effet_permanent('attaquant', new forge_transperce($this->effet_bonus));
			break;
		case 'toucher_adv': // -X% au potentiel toucher adverse
			$perso->add_effet_permanent('defenseur', new forge_toucher( 1 / (1 + $this->effet_bonus/100)));
			break;
		case 'toucher': // +X% au potentiel toucher adverse
			$perso->add_bonus_permanents('mult_melee', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_distance', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_incantation', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_vie', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_element', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_mort', $this->effet_bonus);
			break;
		case 'coup_bouclier': // +X% au potentiel assomer de coup de bouclier
			$perso->add_effet_permanent('attaquant', new forge_toucher( 1 + $this->effet_bonus/100));
			break;
		case 'epines': // +X% d'infliger 2 de dégats quand touché par une attaque physique
			$perso->add_effet_permanent('defenseur', new forge_toucher($this->effet_bonus, 2));
			break;
		case 'rm_mag': // X% de réduire de 1 le coût des sorts en RM
			$perso->add_effet_permanent('attaquant', new forge_rm($this->effet_bonus, -1, false));
			break;
		case 'lancer': // +X% au potentiel lancer et toucher magique
			$perso->add_bonus_permanents('mult_incantation', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_vie', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_element', $this->effet_bonus);
			$perso->add_bonus_permanents('mult_sort_mort', $this->effet_bonus);
			break;
		case 'resiste_etourdit': // X% de résister à coup de bouclier et flèche étourdissante
			$perso->add_effet_permanent('attaquant', new forge_resiste_etourdit( 1 + $this->effet_bonus/100));
			break;
		case 'critique_mag': // + X% au potentiel critique magique
			$perso->add_bonus_permanents('critique_magique', $this->effet_bonus);
			break;
		case 'critique_phys': // + X% au potentiel critique physique
			$perso->add_bonus_permanents('critique', $this->effet_bonus);
			break;
		case 'resiste_debuff': // +X% de résister à un debuff
			$perso->add_bonus_permanents('resiste_debuff', $this->effet_bonus);
			break;
		case 'mult_critique_mag': // + X% au multiplicateur critique magique
			$perso->add_bonus_permanents('mult_critique_magique', $this->effet_bonus);
			break;
		case 'empoisonner': // +X% au chances d'empoisonner (valable sur flèche empoisonnée et les effets de poison des enchantements d'arme)
			$perso->add_bonus_permanents('empoisonner', $this->effet_bonus);
			break;
		case 'anticipation': // -X chances de se faire anticiper
			$perso->add_effet_permanent('attaquant', new forge_anticipation( -$this->effet_bonus ));
			break;
		case 'critique_adv': // -X% chances de critique à l'adversaire 
			$perso->add_effet_permanent('defenseur', new forge_critique( 1 / (1 + $this->effet_bonus / 100) ));
			break;
		case 'mult_critique_adv': // - X% au multiplicateur critique de l'adversaire
			$perso->add_effet_permanent('defenseur', new forge_mult_critique( 1 / (1 + $this->effet_bonus / 100) ));
			break;
		case 'divise': // X% de diviser les dégats subis de 50%
			$perso->add_effet_permanent('defenseur', new forge_division($this->effet_bonus, .5) );
			break;
		case 'divise_mineur': // X% de diviser les dégats subis de 20%
			$perso->add_effet_permanent('defenseur', new forge_division($this->effet_bonus, .8) );
			break;
		case 'paralysie': // X% de paralyser l'adversaire pendant un round quand on subit des dégats 
			$perso->add_effet_permanent('defenseur', new forge_etat($this->effet_bonus, 'paralysie', false) );
			break;
		case 'silence': // X% de silence l'adversaire quand subi des dégats d'un sort
			$perso->add_effet_permanent('attaquant', new forge_etat($this->effet_bonus, 'paralysie', true, forge_etat::sort) );
			break;
		case 'resiste_paralysie': // + X% au potentiel pour sortir de la paralysie
			$perso->add_bonus_permanents('resistance_para', $this->effet_bonus);
			break;
		}
		switch($this->type_malus)
		{
		case 'toucher': // -X% au potentiel toucher
			$perso->add_bonus_permanents('div_melee', $this->effet_bonus);
			$perso->add_bonus_permanents('div_distance', $this->effet_bonus);
			$perso->add_bonus_permanents('div_incantation', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_vie', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_element', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_mort', $this->effet_bonus);
			break;
		case 'esquive': // -X% au potentiel parer physique
			$perso->add_bonus_permanents('div_esquive', $this->effet_bonus);
			break;
		case 'blocage_adv': // +X% au potentiel bloquer adverse
			$perso->add_effet_permanent('attaquant', new forge_blocage(1 + $this->effet_bonus/100));
			break;
		case 'epines': // +X% de subir 2 de dégats quand une attaque touche
			$perso->add_effet_permanent('attaquant', new forge_toucher($this->effet_bonus, 2));
			break;
		case 'parer': // -X% au potentiel parer physique et magique
			$perso->add_bonus_permanents('div_esquive', $this->effet_bonus);
			$perso->add_bonus_permanents('div_parer_magique', $this->effet_bonus);
			break;
		case 'poison': // 10+5*X% d'être empoisonné niveau X quand l'attaque rate
			$perso->add_effet_permanent('attaquant', new forge_poison_rate($this->effet_bonus));
			break;
		case 'blocage': // -X% au potentiel bloquer
			$perso->add_bonus_permanents('div_blocage', $this->effet_bonus);
			break;
		case 'critique_adv': // +X% chances de critique à l'adversaire 
			$perso->add_effet_permanent('defenseur', new forge_critique(1 + $this->effet_bonus / 100));
			break;
		case 'critique': // -X% aux chances de 
			$perso->add_bonus_permanents('div_pot_critique', $this->effet_bonus);
			$perso->add_bonus_permanents('div_pot_critique_magique', $this->effet_bonus);
			break;
		case 'reduction': // X% d'avoir les dégâts réduits de 2 (avant critique)
			$perso->add_effet_permanent('attaquant', new forge_degats($this->effet_bonus, -2));
			break;
		case 'degats_supp': // 20+5*X% de subir X de dégat quand touché par l'adversaire (magie ou physique)
			$perso->add_effet_permanent('defenseur', new forge_degats(20+5*$this->effet_bonus, $this->effet_bonus));
			break;
		case 'surcharge': // 10+10*X% de subir 5*X à chaque critique infligé à l'adversaire
			$perso->add_effet_permanent('defenseur', new forge_surcharge(10+10*$this->effet_bonus, $this->effet_bonus*5));
			break;
		case 'degats_red': // 20+5*X%  de réduire les dégâts infligés de X
			$perso->add_effet_permanent('attaquant', new forge_degats(20+5*$this->effet_bonus, -$this->effet_bonus));
			break;
		case 'anticipation': // +X au chances de se faire anticiper
			$perso->add_effet_permanent('attaquant', new forge_anticipation( $this->effet_bonus ));
			break;
		case 'mult_critique': // - X% au multiplicateur 
			$perso->add_bonus_permanents('div_mult_critique', $this->effet_bonus);
			$perso->add_bonus_permanents('div_mult_critique_magique', $this->effet_bonus);
			break;
		case 'retour': // 10+5*X% de chance de subir X lors d'une attaque réussie
			$perso->add_effet_permanent('attaquant', new forge_toucher(10+5*$this->effet_bonus, $this->effet_bonus));
			break;
		case 'malediction': // 10+10*X% de chance de subir X lors d'une attaque ratée
			$perso->add_effet_permanent('attaquant', new forge_malediction(10+10*$this->effet_bonus, $this->effet_bonus));
			break;
		case 'blocage_etourdissant': // X% de chance d'être étourdi un round après avoir bloqué
			$perso->add_effet_permanent('attaquant', new forge_malediction($this->effet_bonus, false));
			break;
		case 'armure': // -X% PP et PM
			$perso->add_bonus_permanents('div_pp', $this->effet_bonus);
			$perso->add_bonus_permanents('div_pm', $this->effet_bonus);
			break;
		case 'enflamme': // X% de chance de subir 1 dégat chaque round
			$perso->add_effet_permanent('attaquant', new forge_enflame($this->effet_bonus));
			break;
		case 'rm_mag': // X% de perdre 1 RM à chaque sort lancé
			$perso->add_effet_permanent('attaquant', new forge_rm($this->effet_bonus, 1, false));
			break;
		case 'magie_etourdissante': // X% d'être étourdi 1 round après avoir infligé des dégâts avec un sort
			$perso->add_effet_permanent('attaquant', new forge_malediction($this->effet_bonus, false, effet_forge::sort));
			break;
		case 'lancer': // -X% au potentiel lancer et toucher magique
			$perso->add_bonus_permanents('div_incantation', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_vie', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_element', $this->effet_bonus);
			$perso->add_bonus_permanents('div_sort_mort', $this->effet_bonus);
			break;
		case 'mirroir_critique': // X% de s'infliger autant de dégât qu'à l'adversaire en cas de critique
			$perso->add_effet_permanent('attaquant', new forge_mirroir_critique($this->effet_bonus));
			break;
		case 'maladresse': // X% de chance d'être désarmé après avoir raté une attaque
			$perso->add_effet_permanent('attaquant', new forge_maladresse($this->effet_bonus));
			break;
		case 'critique_mag_adv': // +X% chances de critique magique à l'adversaire 
			$perso->add_effet_permanent('defenseur', new forge_critique(1 + $this->effet_bonus / 100, forge_critique::sort ));
			break;
		case 'rm': // X% de chance que les compétences et sorts coutent 1 RM de plus
			$perso->add_effet_permanent('attaquant', new forge_rm($this->effet_bonus, 1, true));
			break;
		case 'duree_debuff_subis': // +X% à la durée des débuffs subis
			$perso->add_bonus_permanents('duree_debuff', $this->effet_bonus);
			break;
		case 'double_debuff': // X% d'avoir un buff supprimé en plus de subir le débuff
			$perso->add_bonus_permanents('double_debuff', $this->effet_bonus);
			break;
		case 'debuf_mana': // X% de perdre 20 de mana quand un débuff est subi
			$perso->add_bonus_permanents('debuf_mana', $this->effet_bonus);
			break;
		case 'attaque_etourdissante': // X% d'être assomé un round après une attaque ou un sort réussi
			$perso->add_effet_permanent('attaquant', new forge_malediction($this->effet_bonus, false));
			break;
		case 'surcharge_mag': // X% de subir 10 dégat après un critique magique
			$perso->add_effet_permanent('defenseur', new forge_surcharge(10+10*$this->effet_bonus, $this->effet_bonus*5, forge_surcharge::sort));
			break;
		case 'critique_mag': // -X% aux chances de critique magique
			$perso->add_bonus_permanents('div_pot_critique_magique', $this->effet_bonus);
			break;
		case 'mult_critique_adv': // + X% au multiplicateur critique de l'adversaire
			$perso->add_effet_permanent('defenseur', new forge_mult_critique(1 + $this->effet_bonus / 100));
			break;
		case 'toucher_phys': // -X% au potentiel toucher physique
			$perso->add_bonus_permanents('div_melee', $this->effet_bonus);
			$perso->add_bonus_permanents('div_distance', $this->effet_bonus);
			break;
		case 'degats_etourdissants': // X% de chances d'être assomé un round quand on subit des dégats physiques
			$perso->add_effet_permanent('defenseur', new forge_malediction($this->effet_bonus));
			break;
		case 'lancer_adv': // + X% au potentiel lancer et toucher magique de l'adversaire
			$perso->add_effet_permanent('defenseur', new forge_lancer( 1 + $this->effet_bonus/100 ));
			break;
		case 'anticipation_adv': // -X au chances d'anticiper de l'adversaire
			$perso->add_effet_permanent('defenseur', new forge_anticipation( -$this->effet_bonus ));
			break;
		}
	}
	
	function get_modif_pp()
	{
		$modif = 0;
		if( $this->type_bonus == 'pp' )
			$modif += $this->effet_bonus;
		if( $this->type_malus == 'pp' )
			$modif -= $this->effet_malus;
	}
	
	function get_modif_pm()
	{
		$modif = 0;
		if( $this->type_bonus == 'pm' )
			$modif += $this->effet_bonus;
		if( $this->type_malus == 'pm' )
			$modif -= $this->effet_malus;
	}
	
	function get_modif_degats()
	{
		$modif = 0;
		if( $this->type_bonus == 'degats' )
			$modif += $this->effet_bonus;
		if( $this->type_malus == 'degats' )
			$modif -= $this->effet_malus;
	}
	
	function get_modif_coeff()
	{
		$modif = 0;
		if( $this->type_bonus == 'coefficient' )
			$modif -= $this->effet_bonus;
		if( $this->type_malus == 'coefficient' )
			$modif += $this->effet_malus;
	}
	
	function get_descr_bonus()
	{
		/// @todo améliorer descriptions
		switch($this->type_bonus)
		{
		case 'blocage':
			return '+'.$this->effet_bonus.'% de potentiel bloquer';
		case 'poison':
			return (10+5*$this->effet_bonus).'% d\'empoisonner niveau '.$this->effet_bonus.' quand une attaque physique touche';
		case 'reduction':
			return $this->effet_bonus.'% de réduire les dégats de 2';
		case 'degats_supp':
			return $this->effet_bonus.'% d\'infliger 2 dégât en plus';
		case 'blocage_adv':
			return '-'.$this->effet_bonus.'% au potentiel bloquer adverse';
		case 'enchainement':
			return '+'.$this->effet_bonus.'% au potentiel toucher et critique après une attaque réussie';
		case 'vampirisme':
			return (10+10*$this->effet_bonus).'% de gagner '.$this->effet_bonus.' PV sur une attaque réussie';
		case 'saignement':
			return $this->effet_bonus.'% de chance d\'infliger 1 dégat à la fin de chaque round jusqu\'à la fin du combat après un critique.';
		case 'mult_critique':
			return '+ '.$this->effet_bonus.'% au multiplicateur critique';
		case 'critique':
			return '+ '.$this->effet_bonus.'% au potentiel critique';
		case 'eguise':
			return '+ '.(5*$this->effet_bonus).'% au potentiel critique, + '.$this->effet_bonus.'% au multiplicateur critique';
		case 'traverser':
			return '+'.$this->effet_bonus.' aux chances de traverser l\'armure';
		case 'toucher_adv':
			return '-'.$this->effet_bonus.'% au potentiel toucher adverse';
		case 'toucher':
			return '+'.$this->effet_bonus.'% au potentiel toucher adverse';
		case 'coup_bouclier':
			return '+'.$this->effet_bonus.'% au potentiel assomer de coup de bouclier';
		case 'epines':
			return '+'.$this->effet_bonus.'% d\'infliger 2 de dégats quand touché par une attaque physique';
		case 'rm_mag':
			return $this->effet_bonus.'% de réduire de 1 le coût des sorts en RM';
		case 'lancer':
			return '+'.$this->effet_bonus.'% au potentiel lancer et toucher magique';
		case 'resiste_etourdit':
			return $this->effet_bonus.'% de résister à coup de bouclier et flèche étourdissante';
		case 'critique_mag':
			return '+ '.$this->effet_bonus.'% au potentiel critique magique';
		case 'critique_phys':
			return '+ '.$this->effet_bonus.'% au potentiel critique physique';
		case 'resiste_debuff':
			return '+'.$this->effet_bonus.'% de résister à un debuff';
		case 'mult_critique_mag':
			return '+'.$this->effet_bonus.'% au multiplicateur critique magique';
		case 'empoisonner':
			return '+'.$this->effet_bonus.'% au chances d\'empoisonner';
		case 'anticipation':
			return '-'.$this->effet_bonus.' chances de se faire anticiper';
		case 'critique_adv':
			return '-'.$this->effet_bonus.'% chances de critique à l\'adversaire';
		case 'mult_critique_adv':
			return '- '.$this->effet_bonus.'% au multiplicateur critique de l\'adversaire';
		case 'divise':
			return $this->effet_bonus.'% de diviser les dégats subis de 50%';
		case 'divise_mineur':
			return $this->effet_bonus.'% de diviser les dégats subis de 20%';
		case 'paralysie':
			return $this->effet_bonus.'% de paralyser l\'adversaire pendant un round quand on subit des dégats';
		case 'silence':
			return $this->effet_bonus.'% de silence l\'adversaire quand subi des dégats d\'un sort';
		case 'resiste_paralysie':
			return '+ '.$this->effet_bonus.'% au potentiel pour sortir de la paralysie';
		}
	}
	
	function get_descr_malus()
	{
		/// @todo améliorer descriptions
		switch($this->type_malus)
		{
		case 'toucher':
			return '-'.$this->effet_bonus.'% au potentiel toucher';
		case 'esquive':
			return '-'.$this->effet_bonus.'% au potentiel parer physique';
		case 'blocage_adv':
			return '+'.$this->effet_bonus.'% au potentiel bloquer adverse';
		case 'epines':
			return '+'.$this->effet_bonus.'% de subir 2 de dégats quand une attaque touche';
		case 'parer':
			return '-'.$this->effet_bonus.'% au potentiel parer physique et magique';
		case 'poison':
			return (10+5*$this->effet_bonus).'% d\'être empoisonné niveau '.$this->effet_bonus.' quand l\'attaque rate';
		case 'blocage':
			return '-'.$this->effet_bonus.'% au potentiel bloquer';
		case 'critique_adv':
			return '+'.$this->effet_bonus.'% chances de critique à l\'adversaire';
		case 'critique':
			return '-'.$this->effet_bonus.'% aux chances de critique';
		case 'reduction':
			return $this->effet_bonus.'% d\'avoir les dégâts réduits de 2';
		case 'degats_supp':
			return (20+5*$this->effet_bonus).'% de subir '.$this->effet_bonus.' de dégat quand touché par l\'adversaire';
		case 'surcharge':
			return (10+10*$this->effet_bonus).'% de subir '.(5*$this->effet_bonus).' à chaque critique infligé à l\'adversaire';
		case 'degats_red':
			return (20+5*$this->effet_bonus).'%  de réduire les dégâts infligés de '.$this->effet_bonus;
		case 'anticipation':
			return '+'.$this->effet_bonus.' au chances de se faire anticiper';
		case 'mult_critique':
			return '- '.$this->effet_bonus.'% au multiplicateur';
		case 'retour':
			return (10+5*$this->effet_bonus).'% de chance de subir '.$this->effet_bonus.' lors d\'une attaque réussie';
		case 'malediction':
			return (10+10*$this->effet_bonus).'% de chance de subir '.$this->effet_bonus.' lors d\'une attaque ratée';
		case 'blocage_etourdissant':
			return $this->effet_bonus.'% de chance d\'être étourdi un round après avoir bloqué';
		case 'armure':
			return '-'.$this->effet_bonus.'% PP et PM';
		case 'enflamme':
			return $this->effet_bonus.'% de chance de subir 1 dégat chaque round';
		case 'rm_mag':
			return $this->effet_bonus.'% de perdre 1 RM à chaque sort lancé';
		case 'magie_etourdissante':
			return $this->effet_bonus.'% d\'être étourdi 1 round après avoir infligé des dégâts avec un sort';
		case 'lancer':
			return '-'.$this->effet_bonus.'% au potentiel lancer et toucher magique';
		case 'mirroir_critique':
			return $this->effet_bonus.'% de s\'infliger autant de dégât qu\'à l\'adversaire en cas de critique';
		case 'maladresse':
			return $this->effet_bonus.'% de chance d\'être désarmé après avoir raté une attaque';
		case 'critique_mag_adv':
			return '+'.$this->effet_bonus.'% chances de critique magique à l\'adversaire';
		case 'rm':
			return $this->effet_bonus.'% de chance que les compétences et sorts coutent 1 RM de plus';
		case 'duree_debuff_subis':
			return '+'.$this->effet_bonus.'% à la durée des débuffs subis';
		case 'double_debuff':
			return $this->effet_bonus.'% d\'avoir un buff supprimé en plus de subir le débuff';
		case 'debuf_mana':
			return $this->effet_bonus.'% de perdre 20 de mana quand un débuff est subi';
		case 'attaque_etourdissante':
			return $this->effet_bonus.'% d\'être assomé un round après une attaque ou un sort réussi';
		case 'surcharge_mag':
			return $this->effet_bonus.'% de subir 10 dégat après un critique magique';
		case 'critique_mag':
			return '-'.$this->effet_bonus.'% aux chances de critique magique';
		case 'mult_critique_adv':
			return '+ '.$this->effet_bonus.'% au multiplicateur critique de l\'adversaire';
		case 'toucher_phys':
			return '-'.$this->effet_bonus.'% au potentiel toucher physique';
		case 'degats_etourdissants':
			return $this->effet_bonus.'% de chances d\'être assomé un round quand on subit des dégats physiques';
		case 'lancer_adv':
			return '+ '.$this->effet_bonus.'% au potentiel lancer et toucher magique de l\'adversaire';
		case 'anticipation_adv':
			return '-'.$this->effet_bonus.' au chances d\'anticiper de l\'adversaire';
		}
	}
}
?>