<?php
/**
 * @file interf_bureau_quete.class.php
 * Classes pour l'interface du bureau des quetes
 */
 
include_once(root.'interface/interf_ville.class.php');
include_once(root.'class/quete.class.php');
include_once(root.'class/quete_etape.class.php');

/// Classe gérant l'interface du bureau des quêtes
class interf_bureau_quete extends interf_ville_onglets
{
	function __construct(&$royaume, $type='autre')
	{
		global $G_url;
		parent::__construct($royaume);
		
		// Icone & jauges
		$this->icone = $this->set_icone_centre('quetes');
		$this->icone->set_tooltip('Bureau des quêtes');
		
		// Onglets
		$this->onglets->add_onglet('Plaine', $G_url->get('type', 'plaine'), 'tab_plaine', 'ecole_mag', $type=='plaine');
		$this->onglets->add_onglet('Forêt', $G_url->get('type', 'foret'), 'tab_foret', 'ecole_mag', $type=='foret');
		$this->onglets->add_onglet('Désert', $G_url->get('type', 'desert'), 'tab_desert', 'ecole_mag', $type=='desert');
		$this->onglets->add_onglet('Neige', $G_url->get('type', 'neige'), 'tab_neige', 'ecole_mag', $type=='neige');
		$this->onglets->add_onglet('Montagne', $G_url->get('type', 'montagne'), 'tab_montagne', 'ecole_mag', $type=='montagne');
		$this->onglets->add_onglet('Marais / TM', $G_url->get('type', 'marais'), 'tab_marais', 'ecole_mag', $type=='marais');
		$this->onglets->add_onglet('Autres', $G_url->get('type', 'autre'), 'tab_autre', 'ecole_mag', $type=='autre');
		
		$this->onglets->get_onglet('tab_'.$type)->add( new interf_tbl_quetes($royaume, $type) );
	}
}

/// Classe affichant les quêtes à prendre au bureau des quêtes
class interf_tbl_quetes extends interf_data_tbl
{
	protected $perso;
	function __construct(&$royaume, $type)
		{
			global $db;
			parent::__construct('tbl_'.$type, '', false, false, 383, 3 );
			$this->perso = &joueur::get_perso();
			
			$this->nouv_cell('Nom de la quete');
			$this->nouv_cell('Type de quete');
			$this->nouv_cell('Repetable');
			
			$return = array();
			$quetes = array();
			$liste_quete = $this->perso->get_liste_quete();
			if(is_array($liste_quete))
			{
				foreach($liste_quete as $quete)
				{
					if ($quete['id_quete']!='')
					{
						$quetes[] = $quete['id_quete'];
					}
				
				}
				if(count($quetes) > 0) $notin = "AND quete.id NOT IN (".implode(',', $quetes).")";
				else $notin = '';
			}
			else $notin = '';
			$where = "";
			$id_royaume = $royaume->get_id();
			if($id_royaume < 10) '0'.$id_royaume;
			$requete = "SELECT *, quete.id as idq FROM quete LEFT JOIN quete_royaume ON quete.id = quete_royaume.id_quete WHERE ((quete_royaume.id_royaume = ".$royaume->get_id().") OR ( royaume LIKE '%".$id_royaume."%')) AND quete.fournisseur = 'bureau_quete'".$where." ".$notin."";
			$req = $db->query($requete);
			
			$nombre_quete = 0;
			while($row = $db->read_array($req))
			{
				$quete = new quete($row['idq']);
				
				$quete_fini = explode(';', $this->perso->get_quete_fini());
				//On affiche si c'est répétable
				if($quete->get_repetable() == 'oui' OR !in_array($quete->get_id(), $quete_fini))
				{
					$check = true;
					$quete_requis = explode(';', $quete->get_requis());

					foreach($quete_requis as $requis)
					{

						if( !$requis ) continue;
						$val = mb_substr($requis, 1);
						switch ($requis[0]) 
						{
							//une quete doit etre finies avant la présente
							case 'q' : 	if( !in_array($val, $quete_fini) )
										{
											$check = false;
											break;
										}
							//c'est une quete de tuto
							case 't' : 	if( $this->perso->get_tuto() != $val)
										{
											$check = false;
											break;
										}
							//c'est une quete de classe
							case 'c' :	$classes = explode('-', $val);
										if( !in_array($this->perso->get_classe_id(), $classes) )
										{
											$check = false;
											break;
										}
							//c'est une quete de race
							case 'r' :	if( $this->perso->get_race() != $val )
										{
											$check = false;
											break;
										}
							//c'est une quete de niveau
							case 'n' :	$operateurs = '>=';
										$operateurs = explode('|', $val);
										foreach($operateurs as $operateur)
										{	
											switch ($operateur['0']) {
												case '>' : if( $this->perso->get_level() <= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '<' : if( $this->perso->get_level() >= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '=' : if( $this->perso->get_level() <> $operateur['1'] )
															{
																$check = false;
																break;
															}
														}
										}
							//c'est une quete d'honneur
							case 'h' :	$operateurs = '>=';
										$operateurs = explode('|', $val);
										foreach($operateurs as $operateur)
										{
											switch ($operateur['0']) {
												case '>' : if( $this->perso->get_honneur() <= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '<' : if( $this->perso->get_honneur() >= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '=' : if( $this->perso->get_honneur() <> $operateur['1'] )
															{
																$check = false;
																break;
															}
														}
										}
							//c'est une quete de reputation
							case 'r' :	$operateurs = '>=';
										$operateurs = explode('|', $val);
										foreach($operateurs as $operateur)
										{
											switch ($operateur['0']) {
												case '>' : if( $this->perso->get_reputation() <= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '<' : if( $this->perso->get_reputation() >= $operateur['1'] )
															{
																$check = false;
																break;
															}
												case '=' : if( $this->perso->get_reputation() <> $operateur['1'] )
															{
																$check = false;
																break;
															}
														}
										}
						}
					}
					if($check)
					{
						$nombre_quete++;
												
						$this->nouv_ligne();
						$this->nouv_cell(new interf_lien($quete->get_nom(), 'bureau_quete.php?action=description&id='.$quete->get_id()));
						$this->nouv_cell($quete->get_type());
						$this->nouv_cell($quete->get_repetable());						
						
						
						//$html .= '<li><a href="bureau_quete.php?action=description&amp;id='.$quete->get_id().'" onclick="return envoiInfo(this.href, \'carte\')">'.$quete->get_nom().'</a> </li>';
					}
				}
			}
			if($nombre_quete > 0)
			{
				$return[0] =  '<ul class="ville">'.$html.'</ul>';
			}
			$return[1] = $nombre_quete;
		}
		
	// on formate la description de la quete pour l'affichage
	function get_description(&$quete)
	{
		global $db;
		
		$this->centre->add( new interf_bal_smpl('h3', 'Bureau des quêtes') );
	}
}
					
					
