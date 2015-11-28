<?php
/**
 * @file interf_arme_siege.class.php
 * Classe permettant l'accès aux armes de sièges.
 */
 
         
/// Classe permettant l'accès aux armes de sièges.
class interf_arme_siege extends interf_cont
{
	protected $perso;
	protected $arme;
	protected $peut_tirer = false;
	protected $cout_pa;
	
	function __construct(&$arme)
	{
		global $db, $Trace;
		$this->arme = $arme;
		$this->perso = joueur::get_perso();
		$batiment = new batiment($arme->get_id_batiment());
		$tps_restant = transform_sec_temp($arme->get_rechargement() - time());
		$tps_tot = $batiment->get_bonus('rechargement');
		
		// Informations
		$infos = $this->add( new interf_bal_cont('div') );
		$infos->add( new interf_bal_smpl('span', 'Position - X : '.$arme->get_x().' - Y : '.$arme->get_y()) );
		$infos->add( new interf_bal_smpl('br') );
		$infos->add( new interf_bal_smpl('span', 'Distance de tir : '.$batiment->get_bonus('portee').' cases.') );
		$infos->add( new interf_bal_smpl('br') );
		if( $arme->peut_attaquer() )
		{
			$tps = 'Pret à tirer !';
			$this->peut_tirer = true;
		}
		else
			$tps = transform_sec_temp($tps_restant);
		$infos->add( new interf_bal_smpl('span', 'Temps avant de pouvoir tirer : '.$tps) );
		
		// On vérifie si on peut tirer
		if( $this->peut_tirer )
		{
			$this->cout_pa = $arme->get_cout_attaque($this->perso);
		  if( $this->perso->get_grade()->get_rang() < $batiment->get_bonus('rang_manip') )
			 $this->peut_tirer = false;
		  if( $this->perso->get_pa() < $cout_pa )
			 $this->peut_tirer = false;
		  if( $this->perso->grade->get_rang() < $batiment->get_bonus('rang_manip') )
			 $this->peut_tirer = false;
		}
		
		// Liste des cibles
		$div = $this->add( new interf_bal_cont('div') );
		$x_min = $this->perso->get_x() - $batiment->get_bonus('portee');
		$x_max = $this->perso->get_x() + $batiment->get_bonus('portee');
		$y_min = $this->perso->get_y() - $batiment->get_bonus('portee');
		$y_max = $this->perso->get_y() + $batiment->get_bonus('portee');
		// Bâtiments déjà construits
		$order = 'ABS(CAST(x AS SIGNED)-'.$this->perso->get_x().') + ABS(CAST(y AS SIGNED)-'.$this->perso->get_y().')';
		$cond = 'x >= '.$x_min.' AND x <= '.$x_max.' AND y >= '.$y_min.' AND y <= '.$y_max;
		$cibles = construction::create(false, false, $order, false, $cond);
		if( count($cibles) )
		{
			$div->add( new interf_bal_smpl('h4', 'Bâtiments à portée') );
			$liste = $div->add( new interf_bal_cont('div') );
			foreach($cibles as $c)
			{
				if($c->get_royaume() != $Trace[$this->perso->get_race()]['numrace'] && $c->get_id() != $arme->get_id())
				{
					$this->aff_cible($liste, $c);
				}
			}
		}
		// Bâtiments en construction
		$order = 'ABS(x-'.$this->perso->get_x().') + ABS(x-'.$this->perso->get_y().')';
		$cond = 'x >= '.$x_min.' AND x <= '.$x_max.' AND y >= '.$y_min.' AND y <= '.$y_max;
		$cibles = placement::create(false, false, $order, false, $cond);
		if( count($cibles) )
		{
			$div->add( new interf_bal_smpl('h4', 'Bâtiments en construction à portée') );
			$liste = $div->add( new interf_bal_cont('div') );
			foreach($cibles as $c)
			{
				if($c->get_royaume() != $Trace[$this->perso->get_race()]['numrace'] && $c->get_id() != $arme->get_id())
				{
					$this->aff_cible($liste, $c);
				}
			}
		}
		// Villes
		/// @todo passer à l'objet
		$requete = 'SELECT map.x as x, map.y as y, nom, race, map.royaume FROM map LEFT JOIN royaume ON map.royaume = royaume.id WHERE map.x >= '.$x_min.' AND map.x <= '.$x_max.' AND map.y >= '.$y_min.' AND map.y <= '.$y_max.' AND type = 1 AND royaume.fin_raz_capitale = 0 AND royaume.race != "'.$this->perso->get_race().'"';
		$req_v = $db->query($requete);
		$row_v = $db->read_assoc($req_v);
		if($db->num_rows > 0)
		{
	    $roy_perso = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
	    $diplo = $roy_perso->get_diplo($row_v['race']);
	    if( $diplo >= 5 )
	    {
				$div->add( new interf_bal_smpl('h4', 'Ville à portée') );
				$liste = $div->add( new interf_bal_cont('div') );
	  		$id = convert_in_pos($row_v['x'], $row_v['y']);
				$this->aff_ville($liste,  $row_v['nom'], $id, $row_v['royaume']);
			}
		}
	}
	function aff_cible(&$liste, &$cible)
	{
		global $Gtrad;
		$royaume = new royaume($cible->get_royaume());
		$batiment = $cible->get_def();
		$rel = position_relative(array($this->perso->get_x(),$this->perso->get_y()), array($cible->get_x(), $cible->get_y()));
		$pos = 'rel_'.$rel[0].'_'.$rel[1];
		$li = $liste->add( new interf_bal_cont('div', false, 'info_case') );
		$li->set_attribut('onmouseover', '$(\'#pos_'.$pos.'\').addClass(\'pos_over\');');
		$li->set_attribut('onmouseout', '$(\'#pos_'.$pos.'\').removeClass(\'pos_over\');');
		if( $this->peut_tirer )
		{
			$url = 'attaque.php?type=siege&table='.$cible->get_table().'&id_arme_de_siege='.$this->arme->get_id().'&id_batiment='.$cible->get_id();
			$att = $li->add( new interf_lien_cont($url, false, 'icone') );
			$att->add( new interf_bal_smpl('span', '', false, 'icone icone-attaque') );
			$att->add( new interf_bal_smpl('span', $this->cout_pa.' PA', false, 'xsmall') );
			$att->set_tooltip('Attaquer ('.$this->cout_pa.' PA)', 'bottom', '#information');
		}
		$lien = $li->add( new interf_bal_cont('div', false, 'info_elt') );
		$hp = $lien->add( new interf_jauge_bulle('HP', $cible->get_hp(), $batiment->get_hp(), false, 'hp', false, 'jauge_case') );
		$hp->add( new interf_bal_smpl('div', round($cible->get_hp() / $batiment->get_hp() * 100).'%', false, 'bulle_valeur') );
		$lien->add( new interf_img($cible->get_image()) );
		$nom = $lien->add( new interf_bal_cont('span') );
		$nom->add( new interf_bal_smpl('span', $cible->get_nom(), false, 'nom') );
		if( $cible->get_royaume() )
		{
			$nom->add( new interf_txt(' − ') );
			$diplo = 'diplo'.$royaume->get_diplo( $this->perso->get_race() );
			$nom->add( new interf_bal_smpl('span', $Gtrad[$royaume->get_race()], false, $diplo) );
			$nom->set_tooltip($batiment->get_nom().' − '.$Gtrad[$diplo], 'bottom');
		}
		$nom->add( new interf_bal_smpl('span', ' − X : '.$cible->get_x().' / Y : '.$cible->get_y(), false, 'xsmall') );
		$li->add( new interf_liste_buff($cible, false) );
		$li->add( new interf_liste_buff($cible, true) );
	}
	function aff_ville(&$liste, $nom, $id, $id_royaume)
	{
		global $Gtrad;
		$royaume = new royaume($id_royaume);
		$li = $liste->add( new interf_bal_cont('div', false, 'info_case') );
		if( $this->peut_tirer )
		{
			$url = 'attaque.php?type=ville&id_arme_de_siege='.$this->arme->get_id().'&id_ville='.$id;
			$att = $li->add( new interf_lien_cont($url, false, 'icone') );
			$att->add( new interf_bal_smpl('span', '', false, 'icone icone-attaque') );
			$att->add( new interf_bal_smpl('span', $this->cout_pa.' PA', false, 'xsmall') );
			$att->set_tooltip('Attaquer ('.$this->cout_pa.' PA)', 'bottom', '#information');
		}
		$lien = $li->add( new interf_bal_cont('div', false, 'info_elt') );
		$lien->add( new interf_img('image/ville/icone_'.$royaume->get_race().'.png') );
		$span_nom = $lien->add( new interf_bal_cont('span') );
		$span_nom->add( new interf_bal_smpl('span', $nom, false, 'nom') );
		$span_nom->add( new interf_txt(' − ') );
		$diplo = 'diplo'.$royaume->get_diplo( $this->perso->get_race() );
		$span_nom->add( new interf_bal_smpl('span', $Gtrad[$royaume->get_race()], false, $diplo) );
		$span_nom->set_tooltip('Capitale − '.$Gtrad[$diplo], 'bottom');
	}
}

?>