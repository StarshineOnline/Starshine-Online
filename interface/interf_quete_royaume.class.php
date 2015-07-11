<?php
/**
 * @file interf_quete_royaume.class.php
 * Interface royale des quêtes
 */ 
include_once(root.'inc/ressource.inc.php');

		
//Créer l'interface de gestion des quetes pour les rois
class interf_quete_royaume extends interf_cont
{
	function __construct(&$royaume)
	{
		interf_alerte::aff_enregistres($this);
		$this->aff_tableau($royaume);
	}
	
	protected function aff_tableau(&$royaume)
	{
		global $db, $ress, $G_url, $Gtrad;
		$tbl = $this->add( new interf_data_tbl('tbl_quete', '', false, false, false, 3	) );
		$tbl->nouv_cell('Quete');
		//$tbl->nouv_cell('Type');
		$tbl->nouv_cell('Fournisseur');
		$tbl->nouv_cell('Repetable');
		$tbl->nouv_cell('Cout');
		$tbl->nouv_cell('Achat');
		
		//on charge toutes les quetes
		$requete = 'SELECT q.*, r.id as qr FROM quete AS q LEFT JOIN quete_royaume AS r ON q.id = r.id_quete AND r.id_royaume = '.$royaume->get_id().' WHERE star_royaume > 0 ';
		$req = $db->query($requete);
		
		while( $row = $db->read_assoc($req) )
		{
			$quete = new quete($row['id']);
			$G_url->add('id',$quete->get_id());
			$tbl->nouv_ligne();
			$tbl->nouv_cell( new interf_lien($quete->get_nom(), $G_url->get('action', 'voir')) );
			//$tbl->nouv_cell($quete->get_type());
			$tbl->nouv_cell($Gtrad[$quete->get_fournisseur()]);
			$tbl->nouv_cell($quete->get_repetable());
			$tbl->nouv_cell($quete->get_star_royaume());
			if( $row['qr'] === null )
				$tbl->nouv_cell( new interf_lien('acheter', $G_url->get('action', 'achat')) );
			else
				$tbl->nouv_cell('&nbsp;');
		}
	}
}

class interf_infos_quete extends interf_dialogBS
{
	function __construct($quete)
	{
		global $Gtrad, $G_url;
		$etape = quete_etape::create(array('id_quete', 'etape', 'variante'), array($quete->get_id(), 1, 0))[0];
		$G_url->add('id', $quete->get_id());
		
		parent::__construct($quete->get_nom(), true, 'quete');
		// Information pour le royaume & prérequis
		$liste = $this->add( new interf_bal_cont('ul', 'info_quete_roy') );
		$liste->add( new interf_bal_smpl('li', 'Prix : '.$quete->get_star_royaume().' star(s)') );
		$liste->add( new interf_bal_smpl('li', 'Fournisseur : '.$Gtrad[$quete->get_fournisseur()]) );
		$liste->add( new interf_bal_smpl('li', 'Nombre d\'étapes : '.$quete->get_nombre_etape()) );
		// Prérequis
		$liste_requis = new interf_bal_cont('ul', 'requis_quete');
		$requis = explode(';', $quete->get_requis());
		foreach($requis as $r)
		{
			if( !$r ) continue;
			$vals = explode('|', mb_substr($r, 1));
			foreach($vals as $val)
			{
				$req = array();
				switch($r[0])
				{
				case 'q':  // une quete doit être finies avant la présente
					$txt = 'Quête ';
					if( $val[0] == '!' )
					{
						$q = new quete(mb_substr($val, 1));
						$req[] = '"'.$q->get_nom().'" non finie';
					}
					else
					{
						$q = new quete($val);
						$req[] = '"'.$q->get_nom().'"  finie';
					}
					break;
				case 'c':  // classe
					$txt = 'Classe : ';
					if( $val[0] == '!' )
					{
						$c = new classe(mb_substr($val, 1));
						$req[] = $c->get_nom();
					}
					else
					{
						$c = new classe($val);
						$req[] = 'autre que '.$c->get_nom();
					}
					break;
				case 'r':  // race
					$txt = 'Race : ';
					if( $val[0] == '!' )
					{
						$r = new royaume(mb_substr($val, 1));
						$req[] = $Gtrad[$r->get_race()];
					}
					else
					{
						$r = new royaume($val);
						$req[] = 'autre que '.$Gtrad[$r->get_race()];
					}
					break;
				case 'n': // niveau
					$txt = 'Niveau ';
					switch($val[0])
					{
					case '>':
						$req[] = '> '.mb_substr($val, 1);
						break;
					case '<':
						$req[] = '< '.mb_substr($val, 1);
						break;
					case '=': 
						$req[] = '= '.mb_substr($val, 1);
						break;
					default:
						$req[] = '≥ '.mb_substr($val, 1);
					}
				case 'h':  // honneur
					$txt = 'Honneur ';
					switch($val[0])
					{
					case '>':
						$req[] = '> '.mb_substr($val, 1);
						break;
					case '<':
						$req[] = '< '.mb_substr($val, 1);
						break;
					case '=': 
						$req[] = '= '.mb_substr($val, 1);
						break;
					default:
						$req[] = '≥ '.mb_substr($val, 1);
					}
				case 'p':  // réputation
					$txt = 'Réputation : ';
					switch($val[0])
					{
					case '>':
						$req[] = '> '.mb_substr($val, 1);
						break;
					case '<':
						$req[] = '< '.mb_substr($val, 1);
						break;
					case '=': 
						$req[] = '= '.mb_substr($val, 1);
						break;
					default:
						$req[] = '≥ '.mb_substr($val, 1);
					}
				}
			}
			$liste_requis->add( new interf_bal_smpl('li', $txt.implode(' ou ', $req)) );
		}
		if( $liste_requis->get_fils() )
		{
			$this->add( new interf_bal_smpl('h4', 'Prérequis') );
			$this->add( $liste_requis );
		}
		// Description & information
		$this->add( new interf_bal_smpl('h4', 'Description') );
		include_once(root.'interface/interf_quetes.class.php');
		$this->add( new interf_descr_quete($quete, $etape) );
		
		// Boutons
		$this->ajout_btn('Fermer' , 'ferme');
		$this->ajout_btn('Acheter' , '$(\'#modal\').modal(\'hide\'); return charger(\''.$G_url->get('action', 'achat').'\');', 'primary');
	}
}
?>
