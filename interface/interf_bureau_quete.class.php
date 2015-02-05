<?php
/**
 * @file interf_bureau_quete.class.php
 * Classes pour l'interface du bureau des quetes
 */
 
include_once(root.'interface/interf_ville.class.php');
include_once(root.'class/quete.class.php');
include_once(root.'class/quete_etape.class.php');

/// Classe gérant l'interface de l'alchimiste
class interf_bureau_quete extends interf_ville_onglets
{
	function __construct(&$royaume)
		{
			global $db;
			parent::__construct($royaume);
			
			$this->onglets->add_onglet( new interf_bal_smpl('h3', 'Bureau des quêtes') );
			
			$tbl = $this->centre->add( new interf_data_tbl('tbl_quete', '', false, false, 383, 3 ) );
			$tbl->nouv_cell('Voici les différentes Quêtes disponibles :');
			$tbl->nouv_ligne();
			$tbl->nouv_cell('Nom de la quete');
			$tbl->nouv_cell('Type de quete');
			$tbl->nouv_cell('Repetable');
			
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
												
						$tbl->nouv_ligne();
						$tbl->nouv_cell(new interf_lien($quete->get_nom(), 'bureau_quete.php?action=description&id='.$quete->get_id()));
						$tbl->nouv_cell($quete->get_type());
						$tbl->nouv_cell($quete->get_repetable());						
						
						
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
					
					
