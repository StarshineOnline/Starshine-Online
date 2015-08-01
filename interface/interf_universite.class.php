<?php
/**
 * @file interf_universite.class.php
 * Classes pour l'interface de l'université.
 */

/// Classe de base pour les interfaces de l'université
class interf_universite_base extends interf_ville
{
	protected $perso;
	protected $classe;
	protected $classes_ok = array();
	
	function __construct(&$royaume)
	{
		parent::__construct($royaume);
		$this->perso = joueur::get_perso();
		
		// Icone
		$this->icone = $this->set_icone_centre('universite', 'universite.php');
		$this->icone->set_tooltip('Université');
		// Jauges
		$this->classe = new classe( $this->perso->get_classe_id() );
		$this->set_jauge_ext($this->classe->get_rang(), 4, 'avance', 'Rang : ');
		$avance = $this->calc_avance();
		$this->set_jauge_int(round($avance, 1), '%', 'avance', 'Avancement du rang : ');
	}
	protected function calc_avance()
	{
		$max = 0;
		if( $this->classe->get_rang() == 4 )
		{
			$permet = classe_permet::create('id_classe', $this->classe->get_id());
			$avanc = 0;
			$nbr = 0;
			foreach($permet as $p)
			{
				$methode = 'get_'.$p->get_competence();
				if( method_exists($this->perso, $methode) )
					$val = $this->perso->$methode();
				else
					$val = $this->perso->get_competence($p->get_competence());
				$anc = classe_requis::create(array('id_classe', 'competence'), array($this->classe->get_id(),$p->get_competence()));
				if( $anc )
					$anc = $anc[0]->get_requis();
				else
					$anc = 0;
				$avanc += min(($val - $anc) / ($p->get_permet() - $anc), 1);
				$nbr++;
			}
			return $avanc * 100 / $nbr;
		}
		else
		{
			$classes = classe::create('rang', $this->classe->get_rang()+1);
			foreach($classes as $c)
			{
				$avanc = 0;
				$nbr = 0;
				$requis = classe_requis::create('id_classe', $c->get_id());
				$ok = true;
				foreach($requis as $r)
				{
					if($r->get_competence() == 'classe')
					{
						if( $r->get_requis() != $this->classe->get_id() )
						{
							$ok = false;
							break;
						}
					}
					else
					{
						$methode = 'get_'.$r->get_competence();
						if( method_exists($this->perso, $methode) )
							$val = $this->perso->$methode();
						else
							$val = $this->perso->get_competence($r->get_competence());
						$anc = classe_requis::create(array('id_classe', 'competence'), array($this->classe->get_id(),$r->get_competence()));
						if( $anc )
							$anc = $anc[0]->get_requis();
						else
							$anc = 0;
						$avanc += min(($val - $anc) / ($r->get_requis() - $anc), 1);
						$nbr++;
					}
				}
				if( $ok )
				{
					$avanc *= 100 / $nbr;
					if( $avanc == 100 )
						$this->classes_ok[] = $c->get_id();
					$max = max($max, $avanc);
				}
			}
		}
		return $max;
	}
}

/// Classe gérant l'interface montrant toutes les classes
class interf_universite extends interf_universite_base
{
	protected $tbl;
	private $lgn=-1;
	private $col;
	private $span_col1;
	private $span_col2;
	private $ids=array(	 0,  0,  7,  2,  0,  9,  7,  0,  4,  2,
											 0,  4,  7, 11,  9, 11,  1,  2,  0,  4,
											 7, 11,  9,  1,  6,  8, 10, 12,  10,  8,
											12,  6,  3,  5, 10,  8, 12,  6,  3,  5);
	function __construct(&$royaume)
	{
		parent::__construct($royaume);
		/// @todo se baser sur la bdd
		$quete = $royaume->get_id() == 7;
		// Classes
		$this->tbl = $this->centre->add( new interf_tableau('classes', 'table'.($quete?' reduit':'')) );
		/// @todo se baser sur la bdd
		// En tête
		$rang = $this->classe->get_rang();
		$this->tbl->nouv_cell('Rang 1', false, ($rang==1?'info':''));
		$this->tbl->nouv_cell('Rang 2', false, ($rang==2?'info':''));
		$this->tbl->nouv_cell('Rang 3', false, ($rang==3?'info':''));
		$this->tbl->nouv_cell('Rang 4', false, ($rang==4?'info':''));
		// Titan
		$this->nouv_ligne();
		$this->aff_cell(1, 7);
		$this->aff_cell(4, 2);
		$this->aff_cell(10);
		$this->aff_cell(18);
		// Paladin
		$this->nouv_ligne();
		$this->aff_cell(16);
		$this->aff_cell(23);
		// Ombre
		$this->nouv_ligne();
		$this->aff_cell(3, 2);
		$this->aff_cell(9);
		$this->aff_cell(17);
		// Paladin
		$this->nouv_ligne();
		$this->aff_cell(32);
		$this->aff_cell(38);
		// Sniper
		$this->nouv_ligne();
		$this->aff_cell(8, 2);
		$this->aff_cell(11);
		$this->aff_cell(19);
		// Prédateur
		$this->nouv_ligne();
		$this->aff_cell(33);
		$this->aff_cell(39);
		// Dresseur
		$this->nouv_ligne();
		$this->aff_cell(24);
		$this->aff_cell(31);
		$this->aff_cell(37);
		// Clerc
		$this->nouv_ligne();
		$this->aff_cell(2, 6);
		$this->aff_cell(6);
		$this->aff_cell(12);
		$this->aff_cell(20);
		// Druide
		$this->nouv_ligne();
		$this->aff_cell(25);
		$this->aff_cell(29);
		$this->aff_cell(35);
		// Elémentaliste
		$this->nouv_ligne();
		$this->aff_cell(5);
		$this->aff_cell(14);
		$this->aff_cell(22);
		// Conjurateur
		$this->nouv_ligne();
		$this->aff_cell(26);
		$this->aff_cell(28);
		$this->aff_cell(34);
		// Pestimancien
		$this->nouv_ligne();
		$this->aff_cell(15);
		$this->aff_cell(13);
		$this->aff_cell(21);
		// Démoniste
		$this->nouv_ligne();
		$this->aff_cell(27);
		$this->aff_cell(30);
		$this->aff_cell(36);
		
		// Quêtes
		if( $quete )
			$this->centre->add( new interf_lien('Bibliothèque', 'universite.php?action=quete&id=0', 'ville_bas') );
	}
	protected function aff_cell($id, $rowspan=1)
	{
		global $db;
		$classe = new classe($id);
		$class = '';
		$rang = $this->classe->get_rang();
		$lgn = $this->ids[$this->classe->get_id()];
		
		// teste les conditions
		$ok = false;
		if( $this->col == $rang + 1 )
		{
			/// @todo  passer par les objets
			$requete = "SELECT * FROM classe_requis WHERE id_classe = '".$id."'";
			$req = $db->query($requete);
			$ok = true;
			while($row = $db->read_array($req))
			{
				/// @todo loguer triche
				if($row['new'] == 'yes') $new[] = $row['competence'];
				if($row['competence'] == 'classe')
				{
					if( $this->perso->get_classe_id() != $row['requis'] )
					{
						$ok = false;
						break;
					}
				}
				else
				{
					$get = 'get_'.$row['competence'];
					if( (method_exists($this->perso, $get)  && $this->perso->$get(true) < $row['requis'])
							|| (!method_exists($this->perso, $get) && $this->perso->get_competence($row['competence']) < $row['requis']) )
					{
						$ok = false;
						break;
					}
				}
			}
		}
		
		if( $id == $this->classe->get_id() )
			$class = 'info';
		elseif( $ok )
			$class = 'success';
		else if( $rang == 1 )
		{
			if( ($lgn == 0 && $this->lgn < 7 ) || ($lgn == 7 && $this->lgn >= 7) )
				$class = 'warning';
		}
		else
		{
			//my_dump($classe->get_nom().' : '.$lgn.' VS '.$this->lgn);
			if( $this->col == 1 )
			{
				if( ($lgn < 7 && $this->lgn == 0) || ($lgn >= 7 && $this->lgn == 7) )
					$class = 'active';
			}
			if( $this->lgn == $lgn )
			{
				if( $this->col < $rang )
					$class = 'active';
				else
					$class = 'warning';
			}
			else if($rang > 2 && $this->col == 2 && $lgn < 6 && ($lgn & 1) && $this->lgn == $lgn - 1 )
			{
				$class = 'active';
			}
			else if( $lgn < 6 && ($this->lgn & 1) && $this->lgn == $lgn + 1 )
			{
				//my_dump($classe->get_nom().' : '.$rang.' VS '.$this->col);
				if( $rang == 2 && $this->col > 2 )
					$class = 'warning';
				else if( $rang > 2 && $this->col == 2 )
					$class = 'active';
				elseif( $this->col > $rang )
					$class = 'danger';
			}
			else if( $this->col > $rang )
				$class = 'danger';
		}
		$cell = $this->tbl->nouv_cell( new interf_lien($classe->get_nom(), 'universite.php?action=description&id='.$id), 'c'.$this->lgn.'-'.$this->col, $class);
		if( $rowspan > 1 )
			$cell->set_attribut('rowspan', $rowspan);
			
		if( $rowspan > 1 )
		{
			if( $this->col == 1 )
				$this->span_col1 = $rowspan;
			else
				$this->span_col2 = $rowspan;
		}
		$this->col++;
		return $cell;
	}
	protected function nouv_ligne()
	{
		$this->tbl->nouv_ligne();
		$this->lgn++;
		$this->col = 1;
		$this->span_col1--;
		$this->span_col2--;
		if( $this->span_col1 > 0 )
		{
			$this->col++;
			if( $this->span_col2 > 0 )
				$this->col++;
		}
	}
}

/// Classe gérant l'interface montrant la description d'un classe
class interf_descr_classe extends interf_universite_base
{
	protected $requis;
	protected $permet;
	protected $donne;
	function __construct(&$royaume, $id)
	{
		parent::__construct($royaume);
		$classe = new classe($id);
		$p = $this->centre->add( new interf_bal_smpl('h3', $classe->get_nom()) );
		$peut_prendre = in_array($id, $this->classes_ok);
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ', ($peut_prendre?'reduit':'')) );
		interf_alerte::aff_enregistres($div);
		// Requis
		$requis = classe_requis::create('id_classe', $classe->get_id());
		if( $requis )
		{
			$p_requis = $div->add( new interf_bal_cont('div') );
			$p_requis->add( new interf_bal_smpl('h4', 'Requis') );
			$this->requis = $p_requis->add( new interf_bal_cont('ul') );
			foreach($requis as $r)
			{
				$this->aff_requis($r);
			}
		}
		// Maximums des attributs
		$p_permet = $div->add( new interf_bal_cont('div') );
		$p_permet->add( new interf_bal_smpl('h4', 'Permet') );
		$this->permet = $p_permet->add( new interf_bal_cont('ul') );
		$permet = classe_permet::create('id_classe', $classe->get_id());
		foreach($permet as $p)
		{
			$this->aff_permet($p);
		}
		// Nouvelles compétences & sorts
		$donne = classe_comp_permet::create('id_classe', $classe->get_id());
		if( $donne )
		{
			$p_donne = $div->add( new interf_bal_cont('div') );
			$p_donne->add( new interf_bal_smpl('h4', 'Donne') );
			$this->donne = $p_donne->add( new interf_bal_cont('ul') );
			foreach($donne as $d)
			{
				$this->aff_donne($d);
			}
		}
		// lien pour prendre cette classe
		if( $peut_prendre )
		{
			 $this->centre->add( new interf_lien('Suivre la voie du '.$classe->get_nom(), 'universite.php?action=prendre&id='.$id, 'ville_bas') );
		}
	}
	function aff_requis($requis)
	{
		global $Gtrad;
		$valeur = $requis->get_requis();
		if( $requis->get_competence() == 'classe' )
		{
			$classe_req = new classe($valeur);
			$valeur = $classe_req->get_nom();
			if( $requis->get_requis() == $this->classe->get_id() )
				$classe = 'text-success';
			else
			{
				$ok = false;
				while( $classe_req->get_rang() > 1 )
				{
					$req = classe_requis::create(array('id_classe', 'competence'), array($classe_req->get_id(), 'classe'));
					if( $req[0]->get_requis() == $this->classe->get_id() )
					{
						$ok = true;
						break;
					}
					$classe_req = new classe($req[0]->get_requis());
				}
				$classe = $ok ? 'text-warning' : 'text-danger';
			}
		}
		else
		{
			$methode = 'get_'.$requis->get_competence();
			if( method_exists($this->perso, $methode) )
				$val = $this->perso->$methode();
			else
				$val = $this->perso->get_competence($requis->get_competence());
			if( $val >= $valeur )
				$classe = 'text-success';
			else if( $requis->get_competence() == 'reputation' )
				$classe = 'text-warning';
			else
			{
				$max = classe_permet::create(array('id_classe', 'competence'), array($this->classe->get_id(), $requis->get_competence()) );
				if( $max )
					$max = $max[0]->get_permet();
				else
					$max = 100;
				$classe = $max >= $valeur ? 'text-warning' : 'text-danger';
			}
		}
		$li = $this->requis->add( new interf_bal_cont('li', false, $classe) );
		$li->add( new interf_bal_smpl('span', $Gtrad[$requis->get_competence()].' : '.$valeur) );
		if( $requis->get_competence() != 'classe' && $classe == 'text-warning' )
			$li->add( new interf_bal_smpl('span', '(il vous manque '.($valeur-$val).')', false, 'xsmall') );
	}
	function aff_permet($permet)
	{
		global $Gtrad;
		$li = $this->permet->add( new interf_bal_cont('li') );
		$li->add( new interf_bal_smpl('span', $Gtrad[$permet->get_competence()].' : '.$permet->get_permet()) );
		if( $permet->get_new() == 'yes' )
			$li->add(  new interf_bal_smpl('span', 'nouveau', false, 'label label-default') );
	}
	function aff_donne($donne)
	{
		global $Gtrad;
		$compsort = comp_sort::factory_gen($donne->get_type(), $donne->get_competence());
		$id = $donne->get_type().$donne->get_competence();
		$li = $this->donne->add(new interf_bal_cont('li'));
		$lien = $li->add( new interf_bal_smpl('a', $compsort->get_nom(), $id) );
		$url = 'ecole.php?action=infos&type='.$donne->get_type().'&id='.$donne->get_competence();
		$lien->set_attribut('onclick', 'chargerPopover(\''.$id.'\', \'info_'.$id.'\', \'right\', \''.$url.'\', \''. $compsort->get_nom().'\');');
	}
}

/// Classe gérant l'interface pour les quêtes de donjons à l'université
class interf_bibliotheque extends interf_universite_base
{
	function __construct(&$royaume, $id)
	{
		parent::__construct($royaume);
		/// @todo se baser sur la bdd
		if( $royaume->get_id() != 7 )
			return;
		$this->centre->add( new interf_bal_smpl('h3', 'Journal de Frankriss hawkeye') );
		$div = $this->centre->add( new interf_bal_cont('div', 'ville_princ') );
		$div->add( new interf_bal_smpl('em', 'le journal est en très mauvais état, maculé de sang, certaines pages sont partiellement ou entièrement déchirées.') );
		$div->add( new interf_bal_smpl('br') );
		$p1 = $div->add( new interf_bal_cont('p') );
		$p1->add( new interf_bal_smpl('strong', '12 Dulfandal : ') );
		$p1->add( new interf_txt('" Au terme de plusieurs jours de voyages, nous voila enfin parvenus jusqu\'aux ruines de la cité humaine de Myriandre. La cité à du être majestueuse, mais les ravages provoqués par ce maudit dragon s\'aperçoivent à des kilomètres à la ronde. Les hauts remparts ont été éventres, et la noirceur des bâtiments est le signe flagrant de la puissance de souffle de flamme du dragon fou.') );
		$p1->add( new interf_bal_smpl('br') );
		$p1->add( new interf_txt('J\'ai ordonné à la compagnie de se disperser afin de me faire un rapport des plus précis. Je souhaite surtout avoir des informations sur les groupes de pillards qui auront immanquablement élu domicile dans les parages.') );
		$p1->add( new interf_bal_smpl('br') );
		$p1->add( new interf_txt('Demetros est anormalement nerveux."') );
		$p2 = $div->add( new interf_bal_cont('p') );
		$p2->add( new interf_bal_smpl('strong', '13 Dulfandal : ') );
		$p2->add( new interf_txt('" mes éclaireurs me rapportent des faits étranges, aucun groupe de pillards à l\'horizon. Je ne peux pas croire que ces charognes auraient manqué l\'occasion de venir piller cette cité en ruines... ce qui demanderait des semaines... je vais envoyer quelques hommes explorer les restes de la ville.') );
		$p2->add( new interf_bal_smpl('br') );
		$p2->add( new interf_txt('demetros m\'a demandé a procéder a certains rituels afin de vérifier') );
		$p2->add( new interf_bal_smpl('em', 'Une tache de sang empêche de lire la suite.') );
		$p3 = $div->add( new interf_bal_cont('p') );
		$p3->add( new interf_bal_smpl('em', 'Plusieurs pages ont été arrachées.') );
		$p3->add( new interf_bal_smpl('br') );
		$p3->add( new interf_bal_smpl('em', 'l\'écriture saccadée semble indiquer que les lignes on été écrites à la va-vite') );
		$p3->add( new interf_bal_smpl('br') );
		$p3->add( new interf_txt('... Ils ont surgit de nulle part ...') );
		$p3->add( new interf_bal_smpl('em', '(tache de sang)') );
		$p3->add( new interf_bal_smpl('br') );
		$p3->add( new interf_txt('Il faut prévenir Scytä') );
		$p3->add( new interf_bal_smpl('em', '(tache de sang)') );
		$p3->add( new interf_bal_smpl('br') );
		$p3->add( new interf_txt('que Dulfandal nous protège tous, nous sommes perdus') );
		$p3->add( new interf_bal_smpl('em', '(tache de sang)') );
	}
}

?>