<?php
/**
 * @file interf_taverne.class.php
 * Classes pour l'interface de la taverne
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de la taverne
class interf_taverne extends interf_ville_onglets
{
	function __construct(&$royaume, &$case, $type)
	{
		global $db;
		parent::__construct($royaume, $case);
		
		// Icone & jauges
		$this->icone = $this->set_icone_centre('biere');
		//$this->recherche_batiment('taverne');
		
		// Onglets
		$this->onglets->add_onglet('Repos', 'taverne.php?type=repos', 'tab_repos', 'ecole_mag', $type=='repos');
		$this->onglets->add_onglet('Bar', 'taverne.php?type=bar', 'tab_bar', 'ecole_mag', $type=='bar');
		$this->onglets->add_onglet('Jeux', 'taverne.php?type=jeux', 'tab_jeux', 'ecole_mag', $type=='jeux');
		/// TODO: quêtes
		
		switch(	$type )
		{
		case 'repos':
			$tab = $this->onglets->get_onglet('tab_repos');
			/*$n = */interf_alerte::aff_enregistres( $tab );
			$tab->add( new interf_taverne_repos($royaume/*, $n*/) );
			break;
		}
	}
}

/// Classe  pour les listes d'achats de services de repos
class interf_taverne_repos extends interf_tableau//interf_liste_achat
{	
	//const url = 'taverne.php';
	protected $royaume;
	protected $perso;
	function __construct(&$royaume/*, $nbr_alertes=0*/)
	{
		parent::__construct(false, 'table table-striped');
		$this->perso = &joueur::get_perso();
		$this->royaume = &$royaume;
		$this->aff_titres_col();
		$services = taverne::create(null, null, 'id ASC', false, '1');
		//my_dump($services);
		foreach($services as $elt)
		{
			$requis = $elt->get_requis();
			if($requis)
			{ // Vérifier les conditions
				$cond = explode(';', $requis);
				foreach($cond as $tcond)
				{
					$ctype = substr($tcond, 0, 1);
					$cval = substr($tcond, 1);
					$cok = true;
					switch ($ctype)
					{
					case 'q': // quete
						$q = explode(';', $this->perso->get_quete_fini());
						$cok = in_array($cval, $q);
						break;
					default:
						$cok = false;
						break;
					}
					if (!$cok)
						break; // un requis pas matché : on s'arrête
				}
				if (!$cok) // un requis pas matché : on ignore la ligne
					continue;
			}
			$this->aff_cont_col($elt);
		}
		//parent::__construct($royaume, 'tbl_repos', $services, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->nouv_cell('Nom');
		$this->nouv_cell('Stars');
		$this->nouv_cell('Cout en PA');
		$this->nouv_cell('Cout en honneur');
		$this->nouv_cell('HP gagnés');
		$this->nouv_cell('MP gagnés');
		$this->nouv_cell('Achat');
	}
	
	function aff_cont_col(&$elt)
	{
		$prix = $elt->get_star() + ceil($elt->get_star() * $this->royaume->get_taxe_diplo($this->perso->get_race()) / 100);
		$stars_ok = $this->perso->get_star() >= $prix;
		$pa_ok = $this->perso->get_pa() >= $elt->get_pa();
		$achat = $stars_ok && $pa_ok;
		$this->nouv_ligne(false, $achat ? '' : 'non-achetable');
		if( $this->perso->get_bonus_shine(12) !== false &&  $this->perso->get_bonus_shine(12)->get_valeur() == 2)
			$nom = $elt->get_nom_f();
		else
			$nom = $elt->get_nom();lo;
		$lien = new interf_bal_smpl('a', $nom, 'elt'.$elt->get_id());
		$this->nouv_cell( $lien );
		$url = 'taverne.php?action=infos&type=achat&id='.$elt->get_id();
		$lien->set_attribut('onclick', 'chargerPopover(\'elt'.$elt->get_id().'\', \'info_elt'.$elt->get_id().'\', \'right\', \''.$url.'\', \''.$nom.'\');');
		$this->nouv_cell( new interf_bal_smpl('span', $prix, false, $stars_ok ? '' : 'text-danger') );
		$this->nouv_cell( new interf_bal_smpl('span', $elt->get_pa(), false, $pa_ok ? '' : 'text-danger') );
		$this->nouv_cell( $elt->get_honneur() + ceil($this->perso->get_honneur() * $elt->get_honneur_pc() / 100) );
		$this->nouv_cell( $elt->get_hp() + ceil($this->perso->get_hp_maximum() * $elt->get_hp_pc() / 100) );
		$this->nouv_cell( $elt->get_mp() + ceil($this->perso->get_mp_maximum() * $elt->get_mp_pc() / 100) );
		if( $achat )
				$this->nouv_cell( new interf_lien('Achat', 'taverne.php?action=achat&type=achat&id='.$elt->get_id()) );
			else
				$this->nouv_cell('&nbsp;');
	}
}

?>