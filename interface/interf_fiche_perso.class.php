<?php
/**
 * @file interf_fiche_perso.class.php
 * Permet la fiche d'un personnage
 */
         
/**
 * Affichage la fiche d'un personnages
 */
class interf_fiche_perso extends interf_onglets
{
	function __construct(&$perso, $actions=false, $onglet='carac')
	{
		global $G_url;
		parent::__construct('onglets_perso', 'fiche_perso');
		$G_url->add('ajax', 2);
		$carac = $this->add_onglet('Caractéristiques', $G_url->get('onglet', 'carac'), 'onglet_carac', 'invent', $onglet=='carac');
		$apt = $this->add_onglet('Aptitudes', $G_url->get('onglet', 'apt'), 'onglet_apt', 'invent', $onglet=='apt');
		$stat = $this->add_onglet('Divers', $G_url->get('onglet', 'stat'), 'onglet_stat', 'invent', $onglet=='stat');
		$achiev = $this->add_onglet('Achievements', $G_url->get('onglet', 'achiev'), 'onglet_achiev', 'invent', $onglet=='achiev');
		switch($onglet)
		{
		case 'carac':
			$carac->add( new interf_fiche_perso_carac($perso, $actions) );
			break;
		case 'apt':
			$apt->add( new interf_fiche_perso_apt($perso) );
			break;
		case 'stat':
			$stat->add( new interf_fiche_perso_stat($perso) );
			break;
		case 'achiev':
			$achiev->add( new interf_fiche_perso_achiev($perso) );
			break;
		}
	}
}

class interf_fiche_perso_carac extends interf_cont
{
	protected $perso;
	protected $caracs;
	protected $temps;
	function __construct(&$perso, $actions=false)
	{
		global $Gtrad, $Tclasse, $G_temps_PA, $G_temps_regen_hp, $G_temps_maj_hp, $G_PA_max, $G_pourcent_regen_hp, $G_pourcent_regen_mp;
		$this->perso = $perso;
		// Info perso
		$infos = $this->add( new interf_bal_cont('div', false, 'info_case') );
		if($perso->get_teleport_roi() != 'true' && $actions)
		{
			$lien = $infos->add( new interf_bal_smpl('a', '', false, 'icone icone-teleportation') );
			$lien->set_attribut('href', 'personnage.php?action=teleport');
			$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez-vous vraiment vous téléportez sur votre capitale (la possibilité sera perdue) ?\');');
			$lien->set_tooltip('Se téléporter dans votre capitale');
		}
		$img = 'image/personnage/'.$perso->get_race().'/'.$perso->get_race().'_'.$Tclasse[$perso->get_classe()]['type'].'.png';
		$infos->add( new interf_img($img, $perso->get_nom()) );
		$infos->add( new interf_bal_smpl('span', $Gtrad[$perso->get_race()].' - '.$perso->get_classe()) );
		// Caractéristiques
		$this->caracs = $this->add( new interf_tableau(false, 'table table-striped') );
		$this->caracs->nouv_cell('Caractéristique');
		$this->caracs->nouv_cell('Base');
		$this->caracs->nouv_cell('');
		$this->caracs->nouv_cell('Total');
		$this->aff_carac('constitution');
		$this->aff_carac('force');
		$this->aff_carac('dexterite');
		$this->aff_carac('puissance');
		$this->aff_carac('volonte');
		$this->aff_carac('energie');
		
		// Temps
		if( $actions )
		{
			$this->temps = $this->add( new interf_tableau(false, 'table table-striped') );
			$this->temps->nouv_cell('Évènement');
			$this->temps->nouv_cell('Durée');
			$this->temps->nouv_cell('Date / heure');
			$this->aff_temps('Prochain PA', $perso->get_dernieraction() + $G_temps_PA);
			// Gemme du troll
			/// @todo à centraliser
			if ($perso->is_enchantement('regeneration'))
			{
				$bonus_regen = $perso->get_enchantement('regeneration', 'effet') * 60;
				if ($G_temps_regen_hp <= $bonus_regen)
				{
					$bonus_regen = $G_temps_regen_hp - 1;
				}
			} else $bonus_regen = 0;
			$tps_regen = $G_temps_regen_hp - $bonus_regen;
			$this->aff_temps('Proch. régén. HP / MP ', $perso->get_regen_hp() + $tps_regen);
			$this->aff_temps('Proch. augment. HP / MP ', $perso->get_maj_hp() + $G_temps_maj_hp);
			$this->aff_temps('Plein de PAs', null, ($G_PA_max - $perso->get_pa())*$G_temps_PA);
			/// @todo à centraliser
			$regen_hp = $G_pourcent_regen_hp * $perso->get_hp_maximum();
			$regen_mp = $G_pourcent_regen_mp * $perso->get_mp_maximum();
			if($perso->is_buff('preparation_camp'))
			{
				$nbr_bonus = floor( ($this->get_buff('preparation_camp', 'fin') - time()) / $tps_regen );
				$bonus = 1 + $this->get_buff('preparation_camp', 'effet') / 100;
			}
			else
				$nbr_bonus = $bonus = 0;
			$hp_manquant = $perso->get_hp_maximum() - $perso->get_hp();
			$regen_bonus = $regen_hp * $bonus * $nbr_bonus;
			if( $regen_bonus >= $hp_manquant )
				$nbr = ceil(($hp_manquant - $regen_bonus) / $regen_hp);
			else
				$nbr = $nbr_bonus + ceil($hp_manquant / ($regen_hp ));
			$this->aff_temps('Plein de HPs', null, $nbr * $tps_regen);
			$mp_manquant = $perso->get_mp_maximum() - $perso->get_mp();
			$regen_bonus = $regen_mp * $bonus * $nbr_bonus;
			if( $regen_bonus >= $mp_manquant )
				$nbr = ceil(($mp_manquant - $regen_bonus) / $regen_mp);
			else
				$nbr = $nbr_bonus + ceil($mp_manquant / ($regen_mp ));
			$this->aff_temps('Plein de MPs', null, $nbr * $tps_regen);
		}
	}
	protected function aff_carac($nom)
	{
		global $Gtrad;
		$this->caracs->nouv_ligne();
		$meth = 'get_'.$nom;
		$val = $this->perso->$meth(true);
		$val_tot = $this->perso->$meth(false);
		$this->caracs->nouv_cell( $Gtrad[$nom] );
		if( $val_tot > $val )
			$classe = 'text-success';
		else if( $val_tot < $val )
			$classe = 'text-danger';
		else
			$classe = '';
		$this->caracs->nouv_cell( $val );
		$this->caracs->nouv_cell('&rarr;');
		$this->caracs->nouv_cell($val_tot, false, $classe);
	}
	protected function aff_temps($nom, $date, $duree=null)
	{
		$jours = array('dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.');
		$this->temps->nouv_ligne();
		if( !$duree )
			$duree = max($date - time(), 0);
		else if( !$date )
			$date = time() + $duree;
		if( $date <= time() )
			$date = 'maintenant';
		else if( date('d') == date('d', $date) )
			$date = ''.date('H:i:s', $date);
		else if( date('d') == date('d', $date-24*3600) )
			$date = 'demain '.date('H:i:s', $date);
		else
			$date = $jours[date('w', $date)].' '.date('H:i:s', $date);
		$duree = transform_sec_temp($duree);
		if( !$duree )
			$duree = '0s';
		$this->temps->nouv_cell( $nom );
		$this->temps->nouv_cell( $duree );
		$this->temps->nouv_cell( $date );
	}
}

class interf_fiche_perso_apt extends interf_cont
{
	protected $perso;
	protected $aptitudes;
	function __construct(&$perso)
	{
		$this->perso = $perso;
		$this->aptitudes = $this->add( new interf_tableau(false, 'table table-striped') );
		$this->aptitudes->nouv_cell('');
		$this->aptitudes->nouv_cell('Aptitude');
		$this->aptitudes->nouv_cell('Base');
		$this->aptitudes->nouv_cell('');
		$this->aptitudes->nouv_cell('Max.');
		$this->aptitudes->nouv_cell('');
		$this->aptitudes->nouv_cell('Total');
		$this->aff_apt('melee');
		$this->aff_apt('distance');
		$this->aff_apt('esquive');
		$this->aff_apt('blocage');
		$this->aff_apt('incantation');
		$this->aff_apt('sort_element');
		$this->aff_apt('sort_mort');
		$this->aff_apt('sort_vie');
		$this->aff_apt('dressage');
		$this->aff_apt('survie');
		$this->aff_apt('artisanat');
		$this->aff_apt('architecture');
		$this->aff_apt('alchimie');
		$this->aff_apt('forge');
		foreach($perso->get_competence() as $apt)
		{
			$this->aff_apt($apt);
		}
	}
	protected function aff_apt($nom)
	{
		global $Gtrad;
		$this->aptitudes->nouv_ligne();
		if( is_string($nom) )
		{
			$meth = 'get_'.$nom;
			$val = $this->perso->$meth(true);
			$val_tot = $this->perso->$meth(false);
		}
		else
		{
			$val_tot = $val = $nom->get_valeur();
			$nom = $nom->get_competence();
		}
		$max = $nom == 'artisanat' ? 141 : recup_max_comp($nom, $this->perso->get_classe_id());
		$this->aptitudes->nouv_cell( new interf_jauge_bulle('Valeur', $val, $max, false, 'avance', false, 'jauge_case') );
		$this->aptitudes->nouv_cell( $Gtrad[$nom] );
		$this->aptitudes->nouv_cell( $val );
		$this->aptitudes->nouv_cell('/');
		$this->aptitudes->nouv_cell( $max );
		$this->aptitudes->nouv_cell('&rarr;');
		if( $val_tot > $val )
			$classe = 'text-success';
		else if( $val_tot < $val )
			$classe = 'text-danger';
		else
			$classe = '';
		$this->aptitudes->nouv_cell($val_tot, false, $classe);
	}
}

class interf_fiche_perso_stat extends interf_cont
{
	protected $perso;
	protected $stats;
	protected $coeffs;
	protected $affinites;
	function __construct(&$perso)
	{
		$this->perso = $perso;
		// Stats diverses
		$this->stats = $this->add( new interf_tableau(false, 'table table-striped') );
		$this->stats->nouv_cell('Nom');
		$this->stats->nouv_cell('Base');
		$this->stats->nouv_cell('');
		$this->stats->nouv_cell('Total');
		$this->stats->nouv_cell('Réduction');
		$this->aff_stat('RM', $perso->get_reserve(true), $perso->get_reserve_bonus() );
		$PP = $perso->get_pp();
		$reduc = (round(1 - calcul_pp($PP), 1) * 100).' %';
		$this->aff_stat('PP', $perso->get_pp(true), $PP, $reduc, 'Réduction des dégâts physiques dû à l\'armure');
		$PM = $perso->get_pm();
		$reduc = (round(1 - calcul_pp($PM * $perso->get_puissance() / 12), 1) * 100).' %';
		$this->aff_stat('PM', $perso->get_pm(true), $PM, $reduc, 'Réduction des dégâts magiques dû à l\'armure');
		$this->aff_stat('Dégâts', $this->calc_des(), $this->calc_des( $perso->get_arme_degat() ), $perso->get_bouclier_degat(), 'Réduction des dégâts en cas de blocage');
		
		// Coefficients
		$this->coeffs = $this->add( new interf_tableau(false, 'table table-striped') );
		$this->coeffs->nouv_cell('Coefficient');
		$this->coeffs->nouv_cell('Valeur');
		$this->aff_coeff('melee');
		$this->aff_coeff('distance');
		$this->aff_coeff('blocage');
		$this->aff_coeff('incantation');
		
		// Affinités
		$this->affinites = $this->add( new interf_tableau(false, 'table table-striped') );
		$this->affinites->nouv_cell('Magie');
		$this->affinites->nouv_cell('Affinité');
		$this->aff_affinite('sort_vie');
		$this->aff_affinite('sort_element');
		$this->aff_affinite('sort_mort');
	}
	protected function aff_stat($nom, $val, $val_tot, $reduc='', $tooltip_reduc=false)
	{
		$this->stats->nouv_ligne();
		$this->stats->nouv_cell( $nom );
		$this->stats->nouv_cell( $val );
		$this->stats->nouv_cell('&rarr;');
		if( $val_tot > $val )
			$classe = 'text-success';
		else if( $val_tot < $val )
			$classe = 'text-danger';
		else
			$classe = '';
		$this->stats->nouv_cell($val_tot, false, $classe);
		$cell = $this->stats->nouv_cell( $reduc );
		if( $tooltip_reduc )
			$cell->set_tooltip($tooltip_reduc, 'left');
	}
	protected function calc_des($bonus=0)
	{
		$de_degat = de_degat($this->perso->get_force(), $bonus);
		$texte = '';
		$i = 0;
		while($i < count($de_degat))
		{
			if ($i > 0)
				$texte .= ' + ';
			$texte .= '1D'.$de_degat[$i];
			$i++;
		}
		return $texte;
	}
	protected function aff_coeff($nom)
	{
		global $Gtrad;
		$this->coeffs->nouv_ligne();
		$meth = 'get_coef_'.$nom;
		$this->coeffs->nouv_cell( $Gtrad[$nom] );
		$this->coeffs->nouv_cell( $this->perso->$meth() );
	}
	protected function aff_affinite($nom)
	{
		global $Gtrad, $Trace;
		$this->affinites->nouv_ligne();
		$meth = 'get_coef_'.$nom;
		$this->affinites->nouv_cell( $Gtrad[$nom] );
		$this->affinites->nouv_cell( $Gtrad['affinite'.$Trace[$this->perso->get_race()]['affinite_'.$nom]] );
	}
}

class interf_fiche_perso_achiev extends interf_cont
{
	function __construct(&$perso)
	{
		interf_alerte::aff_enregistres($this);
		$tbl = $this->add( new interf_tableau(false, 'table table-striped') );
		$tbl->nouv_cell('Nom');
		$tbl->nouv_cell('Description');
		
		$achievements = $perso->get_achievement();
		if($achievements)
		{
			foreach($achievements as $achiev)
			{
				$tbl->nouv_ligne();
				$tbl->nouv_cell( $achiev['nom'] );
				$tbl->nouv_cell( description($achiev['description'], $achiev) );
			}
		}
	}
}

?>