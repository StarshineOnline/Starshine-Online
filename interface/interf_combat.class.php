<?php
/**
 * @file interf_combat.class.php
 * Affichage des combats
 */
 
/**
 * Classe gérant l'affichage des combats
 */
class interf_combat extends interf_bal_cont
{
	protected $round;
	protected $passe;
	protected $fin;
	function __construct()
	{
		parent::__construct('div', 'combat');
	}
	
	function nouveau_round($num)
	{
		$this->round = $this->add( new interf_bal_cont('div', 'round'.$num, 'round') );
		$this->round->add( new interf_bal_smpl('div', 'Round '.$num, false, 'round_num') );
	}
	
	function nouvelle_passe($mode)
	{
		$this->passe = $this->round->add( new interf_bal_cont('div', false, 'passe '.$mode) );
	}
	
	function approche(&$perso)
	{
		$this->passe->add( new interf_bal_smpl('p', $perso->get_nom().' s\'approche', false, 'approche') );
	}
	
	function aff_debug()
	{
		interf_debug::aff_enregistres($this->passe);
	}
	
	function aff_fin(&$attaquant, &$defenseur, $degats_att, $degats_def, $longueur, $fiabilite, $type, $action)
	{
		global $G_url;
		interf_debug::aff_enregistres($this);
		$this->fin = $this->add( new interf_bal_cont('div', 'fin_combat') );
		$res = $this->fin->add( new interf_bal_cont('div', 'resultat_combat') );
		if( $action )
		{
			$liens = $res->add( new interf_bal_cont('div', 'liens_combat') );
			if( $defenseur->get_hp() > 0 )
			{
				$att = $liens->add( new interf_lien('', $G_url->get(), false, 'icone icone-attaque') );
				$att->set_tooltip('Attaquer à nouveau');
			}
			$case = convert_in_pos($defenseur->get_x(), $defenseur->get_y());
			$retour = $liens->add( new interf_lien('', 'informationcase.php?case='.$case, false, 'icone icone-retour') );
			$retour->set_tooltip('Retour à la case');
		}
		$tbl = $res->add( new interf_tableau(/*false, false, false, false, false*/) );
		$tbl->nouv_cell( '&nbsp;' );
		$tbl->nouv_cell( 'Perte' );
		$tbl->nouv_cell( 'Restant' );
		$tbl->nouv_ligne();
		$tbl->set_entete(true);
		$tbl->nouv_cell( $attaquant->get_nom() );
		$tbl->set_entete(false);
		$deg = $tbl->nouv_cell( $this->formate_degats($degats_def) );
		$deg->set_tooltip('Quantité de HP que vous avez perdu');
		if( $action )
		{
			$hp = $tbl->nouv_cell( $attaquant->get_hp().' HP' );
			$hp->set_tooltip('Vos HP après le combat');
		}
		$tbl->nouv_ligne();
		$tbl->set_entete(true);
		$tbl->nouv_cell( $defenseur->get_nom() );
		$tbl->set_entete(false);
		$deg = $tbl->nouv_cell( $this->formate_degats($degats_att) );
		$deg->set_tooltip('Quantité de HP  que votre adversaire a perdu');
		if( $action )
		{
			if( $defenseur->get_hp() > 0 )
			{
				$hp = $tbl->nouv_cell( new interf_jauge_bulle(null, $longueur, 100, false, 'hp', false, 'jauge_case') );
				$hp->set_tooltip('Estimation des HP : '.$longueur.'% ± '.$fiabilite.'%');
			}
			else if( $type == 'joueur' || $type == 'monstre' )
				$tbl->nouv_cell( 'mort' );
			else
				$tbl->nouv_cell( 'détruit' );
		}
		// test
		interf_debug::aff_enregistres($this);
	}
	
	function formate_degats($degats)
	{
		if( $degats < 0 )
			return '+'.(-$degats).' HP';
		else if( $degats > 0 )
			return '-'.$degats.' HP';
		else
			return '-0 HP';
	}
	
	function aff_messages_fin($messages)
	{
		$this->fin->add( new interf_bal_smpl('div', $messages, 'messages_fin') );
	}
	
	function special($type, $nom_att, $nom_def=null)
	{
		switch($type)
		{
		case 'cc':
			$texte = $nom_def.' est caché, '.$nom_att.' ne peut pas attaquer';
			break;
		case 'cp':
			$texte = $nom_att.' est paralysé';
			break;
		case 'ce':
			$texte = $nom_att.' est étourdit';
			break;
		case 'dp':
			$texte = $nom.' se défait de la paralysie';
			break;
		case 'cg':
			$texte = $nom_att.' est glacé';
			break;
		case 'cs':
			$texte = $nom_att.' est sous silence';
			break;
		case 'sv':
			$texte = $nom_att.' décoche une terrible flèche';
			break;
		case 'sv':
			$texte = 'Le coup porté est tellement violent qu\'il transperce l\'armure !';
			break;
		case 'sh':
			$texte = 'L\'attaque inflige une hémorragie !';
			break;
		}
		$this->passe->add( new interf_bal_smpl('p', $texte, false, 'special') );
	}
	
	function anticipe($nom)
	{
		$this->passe->add( new interf_bal_smpl('p', $nom.' anticipe l\'attaque, et elle échoue !', false, 'anticipe') );
	}
	
	function manque($nom_perso, $nom_sort=false)
	{
		$this->passe->add( new interf_bal_smpl('p', $nom_perso.' manque la cible'.($nom_sort ? ' avec '.$nom_sort : ''), false, 'manque') );
	}
	
	function rate($nom_perso, $nom_sort)
	{
		$this->passe->add( new interf_bal_smpl('p', $nom_perso.' rate le lancement de '.$nom_sort, false, 'manque') );
	}
	
	function degats($degats, $nom_perso, $nom_sort=false)
	{
		$this->passe->add( new interf_bal_smpl('p', '<strong>'.$nom_perso.'</strong> inflige <strong>'.$degats.'</strong> dégâts'.($nom_sort ? ' avec '.$nom_sort : ''), false, 'degat') );
	}
	
	function reduction($reduction, $par='l\'armure')
	{
		$this->passe->add( new interf_bal_smpl('p', '(réduits de '.$reduction.' par '.$par.')', false, 'small') );
	}
	function effet($type, $effet, $nom_actif, $nom_passif='')
	{
		switch($type)
		{
		case 1:  // Perte de HP par le poison
			$texte = $nom_actif.' perd '.$effet.' HP par le poison';
			$classe = 'degat';
			break;
		case 2:  // Perte de HP par hémorragie
			$texte = $nom_actif.' perd '.$effet.' HP par hémorragie';
			$classe = 'degat';
			break;
		case 3:  // Perte de HP par embrasement
			$texte = $nom_actif.' perd '.$effet.' HP par embrasement';
			$classe = 'degat';
			break;
		case 4:  // Perte de HP par acide
			$texte = $nom_actif.' perd '.$effet.' HP par acide';
			$classe = 'degat';
			break;
		case 5:  // Perte de HP par lien sylvestre
			$texte = $nom_actif.' perd '.$effet.' HP par le lien sylvestre';
			$classe = 'degat';
			break;
		case 6:  // Récupération
			$texte = $nom_actif.' gagne '.$effet.' HP par récupération';
			$classe = 'soin';
			break;
		case 7:  // Fleche Debilisante
			$texte = $nom_actif.' est sous l\'effet de Flêche Débilisante';
			$classe = 'effet';
			break;
		case 8:  // Rage vampirique
			$texte = $nom_actif.' gagne '.$effet.' HP par la rage vampirique';
			$classe = 'soin';
			break;
		case 9:  // Armure d'epine
			$texte = $nom_passif.' renvoie '.$effet.' dégâts grâce à l\' Armure en épine';
			$classe = 'degat';
			break;
		case 10:  // Pacte de sang
			$texte = 'et sacrifie '.$effet.' hp';
			$classe = 'effet';
			break;
		case 11:  // 
			$texte = $nom_passif.' glace '.$nom_actif.' avec son armure de glace';
			$classe = 'effet';
			break;
		case 12:  // 
			$texte = $nom_passif.' inflige '.$effet.' dégâts grâce au bouclier de feu';
			$classe = 'degat';
			break;
		case 13:  // 
			$texte = $nom_passif.' glace '.$nom_actif;
			$classe = 'effet';
			break;
		case 14:  // 
			$texte =  $nom_passif.' est paralysé par ce coup !';
			$classe = 'effet';
			break;
		case 15:  // 
			$texte =  $nom_actif.' se ressaisi et gagne '.$effet.' RM';
			$classe = 'effet';
			break;
		case 16:  // flèche étourdissante
			$texte =  $nom_actif.' est étourdi par la flêche !';
			$classe = 'effet';
			break;
		case 17:  // coup de bouclier
			$texte =  'Le coup de bouclier étourdit '.$nom_passif.' pour '.$effet.' rounds !';
			$classe = 'effet';
			break;
		case 18:  // 
			$texte =  '<strong>'.$nom_passif.'</strong> perd <strong>'.$effet. '</strong> HP à cause du feu';
			$classe = 'degat';
			break;
		case 19:  // 
			$texte =  '<strong>'.$nom_actif.'</strong> gagne '.$effet.' PA';
			$classe = 'effet';
			break;
		case 20:  // drain de points de vie
			$texte =  'Et gagne <strong>'.$effet.'</strong> HP grâce au drain';
			$classe = 'soin';
			break;
		case 21:  // drain de RM
			$texte =  'Et gagne <strong>'.$effet.'</strong> RM grâce au drain';
			$classe = 'effet';
			break;
		case 22:  // retire RM (brûlure de mana)
			$texte =  'Et retire '.$effet.' réserve de mana à '.$nom_passif;
			$classe = 'degat';
			break;
		case 23:  // 
			$texte =  $nom_passif.' est projeté contre un mur et perds <strong>'.$effet.'</strong> points de vie!';
			$classe = 'degat';
			break;
		case 24:  // 
			$texte =  'Une &eacute;pine jaillit de <strong>'.$nom_actif.'</strong> infligeant <strong>'.$effet.'</strong> dégâts, et transpercant '.$nom_passif;
			$classe = 'degat';
			break;
		case 25:  // 
			$texte =  'La riposte furtive de '.$nom_passif.' inflige '.$effet.' dégâts à '.$nom_actif;
			$classe = 'degat';
			break;
		case 26:  // 
			$texte =  'La carapace incisve de '.$nom_passif.' inflige '.$effet.' dégâts à '.$nom_actif;
			$classe = 'degat';
			break;
		case 27:  // 
			$texte =  'L\'arc Tung de '.$actif->get_nom().' lui fait perdre '.$effet.' HP !';
			$classe = 'degat';
			break;
		case 28:  // mirroir_eclatant
			$texte =  'Votre bouclier bloque '.$effet.' dégâts et les infliges à '.$nom_actif;
			$classe = 'degat';
			break;
		case 29:  // anneau_resistance
			$texte =  'L\'anneau de resistance de '.$nom_passif.' reduit les degats de '.$reduction;
			$classe = 'effet';
			break;
		case 30:  // effet_vampirisme
			$texte =  $nom_actif.' gagne '.$effet.' HP par vampirisme';
			$classe = 'soin';
			break;
		case 31:  // fleche_poison et poison des modification de forge
			$texte =  '<strong>'.$nom_passif.'</strong> est empoisonné pour '.$effet.' rounds !';
			$classe = 'degat';
			break;
		case 32:  // fleche_sable
			$texte =  $nom_passif.' est ensablé pour '.$effet.' rounds';
			$classe = 'effet';
			break;
		case 33: // retour
			$texte =  $nom_passif.' subit '.$effet.' dégâts en retour.';
			$classe = 'degat';
			break;
		case 34:  // étourdissement
			$texte =  'Le coup étourdit '.$nom_passif.' pour '.$effet.' round'.($effet>1?'s':'').' !';
			$classe = 'effet';
			break;
		case 35:  // désarmement
			$texte =  $nom_passif.' est désarmé !';
			$classe = 'effet';
			break;
		default:
			return;
		}
		$this->passe->add( new interf_bal_smpl('p', $texte, false, $classe) );
	}
	
	function bloque($degat_bloque, $nom)
	{
		$this->passe->add( new interf_bal_smpl('p', $nom.' bloque le coup et absorbe '.$degat_bloque.' dégâts', false, 'manque') );
	}
	
	function critique()
	{
		$this->passe->add( new interf_bal_smpl('p', 'COUP CRITIQUE !', false, 'coupcritique') );
	}
	
	function tentative($type, $resultat, $nom_perso, $nom_sort)
	{
		switch($type)
		{
		case 'd':
			$texte = $nom_perso.' tente de se dissimuler…';
			break;
		case 's':
			$texte = '<strong>'.$nom_perso.'</strong> lance le sort '.$nom_sort;
			break;
		default:
			return;
		}
		$texte = $resultat ? ' et réussit !' : ' et échoue';
		$this->passe->add( new interf_bal_smpl('p', $texte, false, 'special') );
	}
	
	function debuff($nom_perso, $nom_debuff)
	{
		$this->passe->add( new interf_bal_smpl('p', '<strong>'.$nom_perso.'</strong> est affecté par le debuff '.$nom_debuff, false, 'special') );
	}
	
	function texte($texte, $classe=false)
	{
		$this->passe->add( new interf_bal_smpl('p', $texte, false, $classe) );
	}
	
	function sort($type, $nom_perso, $nom_sort)
	{
		switch($type)
		{
		case 'aura_feu':
			$texte = 'Une enveloppe de feu entoure <strong>'.$nom_perso.'</strong> !';
			break;
		case 'aura_glace':
			$texte = 'Une enveloppe de glace entoure <strong>'.$nom_perso.'</strong> !';
			break;
		case 'aura_vent':
			$texte = 'Des tourbillons d\'air entourent <strong>'.$nom_perso.'</strong> !';
			break;
		case 'aura_pierre':
			$texte = 'De solides pierres volent autour de <strong>'.$nom_perso.'</strong> !';
			break;
		case 'benediction':
		case 'recuperation':
			$texte = '<strong>'.$nom_perso.'</strong> se lance le sort '.$nom_sort;
			break;
		case 'sacrifice_morbide':
			$texte = '<strong>'.$nom_perso.'</strong> se suicide avec '.$nom_sort;
			break;
		default:
			$texte = '<strong>'.$nom_perso.'</strong> lance le sort '.$nom_sort;
		}
		$this->passe->add( new interf_bal_smpl('p', $texte, false, 'utilise') );
	}
	
	function competence($type, $nom_perso, $nom_comp)
	{
		switch($type)
		{
		case '':
		case 'attaque':
			return;
		case 'berzeker':
			$texte = '<strong>'.$nom_perso.'</strong> passe en mode '.$nom_comp;
			break;
		case 'bouclier_protecteur':
			$texte = '<strong>'.$nom_perso.'</strong> intensifie sa protection magique grace à son bouclier !';
			break;
		case 'tir_vise':
			$texte = '<strong>'.$nom_perso.'</strong> se concentre pour viser !';
			break;
    case 'posture_critique':
    case 'posture_esquive':
    case 'posture_defense':
    case 'posture_degat':
    case 'posture_transperce':
    case 'posture_paralyse':
    case 'posture_touche':
			$texte = '<strong>'.$nom_perso.'</strong> se met en '.$nom_comp.' !';
			break;
		default:
			$texte = '<strong>'.$nom_perso.'</strong> utilise '.$nom_comp;
		}
		$this->passe->add( new interf_bal_smpl('p', $texte, false, 'utilise') );
	}
}

?>