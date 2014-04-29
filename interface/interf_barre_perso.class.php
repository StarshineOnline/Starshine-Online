<?php
/**
 * @file interf_barre_perso.class.php
 * Entête contenant les information sur le personnage et le groupe
 */

/**
 * Classe affichant l'entête contenant les information sur le personnage et le groupe
 */
class interf_barre_perso extends interf_bal_cont
{
  protected $perso;
  protected $infos_vie;
  protected $infos_perso;
  function __construct()
  {
    $this->perso = joueur::get_perso();
    interf_bal_cont::__construct('div', 'perso_contenu');
    // Panneau de gauche
    $this->creer_infos_vie();
    $this->creer_infos_perso();
    $this->creer_infos_pos();
    $this->creer_infos_groupe();
  }
  protected function creer_infos_vie()
  {
    global $G_PA_max;
    $this->infos_vie = $this->add( new interf_bal_cont('div', 'infos_vie') );
    // nom
    $titre_perso = new titre($_SESSION["ID"]);
    $bonus = recup_bonus($this->perso->get_id());
    $titre = $titre_perso->get_titre_perso($bonus);
    $nom = $this->infos_vie->add( new interf_bal_cont('a', 'nom_perso') );
    $nom->set_attribut('href', 'personnage.php');
    $nom->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
    $nom->set_attribut('title', 'Accès à la fiche de votre personnage');
    $nom->set_attribut('data-toggle', 'tooltip');
    $nom->set_attribut('data-placement', 'bottom');
    $nom->add( new interf_bal_smpl('span', $titre[0]) );
    $nom->add( new interf_bal_smpl('span', ucwords($this->perso->get_grade()->get_nom()), 'grade') );
    //$nom->add( new interf_txt(' ') );
    $nom->add( new interf_bal_smpl('span', ucwords($this->perso->get_nom()), 'nom') );
    $nom->add( new interf_bal_smpl('span', $titre[1]) );
    // jauges
    $this->creer_jauge($this->infos_vie, 'Points de vie', $this->perso->get_hp(), floor($this->perso->get_hp_maximum()), true, 'danger', 'hp');
    $this->creer_jauge($this->infos_vie, 'Points de mana', $this->perso->get_mp(), floor($this->perso->get_mp_maximum()), true, false, 'mp');
    $this->creer_jauge($this->infos_vie, 'Points d\'action', $this->perso->get_pa(), $G_PA_max, true, 'success', 'pa');
    $this->creer_jauge_xp($this->perso->get_exp(), prochain_level($this->perso->get_level()), progression_level(level_courant($this->perso->get_exp())));
  }
  protected function creer_infos_perso()
  {
    global $Gtrad, $db;
    // Race & classe
    $this->infos_perso = $this->add( new interf_bal_cont('div', 'infos_perso') );
    $this->infos_perso->set_attribut('style', 'background-image:url(\'./image/interface/fond_info_perso_'.$this->perso->get_race_a().'.png\');');
    $race_classe = $this->infos_perso->add( new interf_bal_cont('div', 'race_classe') );
    $race_classe->add( new interf_bal_smpl('span', ucwords($Gtrad[$this->perso->get_race()]), 'race') );
    $race_classe->add( new interf_bal_smpl('span', ucwords($this->perso->get_classe()), 'classe') );
    // Honneur & réputation
    $ph = $this->infos_perso->add( new interf_bal_cont('div', 'perso_ph') );
    $ph->set_attribut('title', 'Votre honneur : '.$this->perso->get_honneur().' / Votre réputation : '.$this->perso->get_reputation());
    $ph->add( new interf_bal_smpl('span', $this->perso->get_honneur(), 'honneur') );
    $ph->add( new interf_bal_smpl('br') );
    $ph->add( new interf_bal_smpl('span', $this->perso->get_reputation(), 'reputation') );
    // stars
    $stars = $this->infos_perso->add( new interf_bal_smpl('div', $this->perso->get_star(), 'perso_stars') );
    $stars->set_attribut('title', 'Votre argent : '.$this->perso->get_star().' stars');
    //$stars->add( new interf_bal_smpl('span', $this->perso->get_star()) );
    // attaque
    /// TODO: passer à l'objet
    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_a();
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $att = $this->infos_perso->add( new interf_bal_smpl('div', $row['nom'], 'perso_attaque', 'perso_script') );
    $arme = $this->perso->get_arme();
    if( $arme )
    {
    	$arme = new arme($arme->id);
    	$nom_arme = $arme->get_nom();
			$att->set_attribut('style', 'background-image:url(\''.$arme->get_image().'\');');
		}
		else
			$nom_arme = 'aucune';
    $att->set_attribut('title', 'Votre arme : '.$nom_arme.' − Votre script d\'attaque : '.$row['nom']);
    // défense
    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_d();
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $def = $this->infos_perso->add( new interf_bal_smpl('div', $row['nom'], 'perso_defense', 'perso_script') );
    $bouclier = $this->perso->get_bouclier();
    if( $bouclier )
    {
    	$bouclier = new arme($bouclier->id);
    	$nom_bouclier = $bouclier->get_nom();
			$def->set_attribut('style', 'background-image:url(\''.$bouclier->get_image().'\');');
		}
		else
			$nom_arme = 'aucun';
    $def->set_attribut('title', 'Votre bouclier : '.$nom_bouclier.' − Votre script de défense : '.$row['nom']);
    // créature dressée
    $creature = $this->perso->get_pet();
    if( $creature )
    {
    	$creat = $this->infos_perso->add( new interf_bal_smpl('div', $creature->get_nom(), 'perso_dresse', 'perso_script') );
    	$monstre = new monstre( $creature->get_id_monstre() );
    	/// TODO: utiliser une méthode pour obtenir l'image
			$creat->set_attribut('style', 'background-image:url(\'./image/monstre/'.$monstre->get_lib().'.png\');');
    	$creat->set_attribut('title', 'Votre créature principale : '.$creature->get_nom().' ('.$monstre->get_nom().')');
		}
    // Buffs & debuffs
    $liste = $this->infos_perso->add( new interf_bal_cont('div', 'perso_buffs') );
    $buffs = $liste->add( new interf_bal_cont('div', 'liste_buffs') );
    $buffs->add( new interf_liste_buff($this->perso, false, true) );
    $debuffs = $liste->add( new interf_bal_cont('div', 'liste_debuffs') );
    $debuffs->add( new interf_liste_buff($this->perso, true) );
  }
  protected function creer_infos_pos()
  {
    $heure = $this->add( new interf_bal_cont('a', 'perso_heure') );
    $heure->set_attribut('style', 'background-image:url(image/interface/'.moment_jour().'.png);');
    /*$img = $heure->add( new interf_bal_smpl('img') );
    $img->set_attribut('src', 'image/interface/'.moment_jour().'.png');*/
    $heure->set_tooltip(moment_jour(), 'bottom');
    $span = $heure->add( new interf_bal_smpl('span', substr(date_sso(time()),0,-3), 'heure') );
    $pos = $this->add( new interf_bal_cont('a', 'perso_position') );
    $img = $pos->add( new interf_bal_smpl('img') );
    $img->set_attribut('src', 'carte_perso.php?vue=12');
  }
	protected function creer_jauge($parent, $nom, $valeur, $maximum, $grand, $style=false, $type=null)
	{
    $jauge = $parent->add( new interf_bal_cont('div', $grand?'perso_'.$type:'', ($grand?'jauge_bulle':'jauge_groupe membre_'.$type).' progress') );
    $jauge->set_tooltip($nom.' : '.$valeur.' / '.$maximum, 'right');
    /*$jauge->set_attribut('title', $nom.' : '.$valeur.' / '.$maximum);
    $jauge->set_attribut('data-toggle', 'tooltip');
    $jauge->set_attribut('data-placement', 'right');*/
    $barre = $jauge->add( new interf_bal_cont('div', null, 'bulle progress-bar'.($style?' progress-bar-'.$style:'')) );
    $barre->set_attribut('style', 'height:'.round($valeur/$maximum*100,0).'%');
    if( $grand )
			$jauge->add( new interf_bal_smpl('div', $valeur.'/'.$maximum, $type, 'bulle_valeur') );
	}
  protected function creer_jauge_xp($valeur, $maximum, $progression)
  {
    $jauge = $this->infos_vie->add( new interf_bal_cont('div', 'perso_xp', 'jauge_barre progress') );
    $jauge->set_tooltip('Points d\'expérience : '.$valeur, 'bottom');
    /*$jauge->set_attribut('title', 'Points d\'expérience : '.$valeur);
    $jauge->set_attribut('data-toggle', 'tooltip');
    $jauge->set_attribut('data-placement', 'right');*/
    $barre = $jauge->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-warning') );
    $barre->set_attribut('style', 'width:'.$progression.'%');
    $jauge->add( new interf_bal_smpl('div', $valeur.' / '.$maximum, 'xp', 'barre_valeur') );
  }
	protected function creer_jauge_mort($parent, $grand=false)
	{
    $jauge = $parent->add( new interf_bal_cont('div', $grand?'perso_hp':'', ($grand?'jauge_bulle':'jauge_groupe membre_hp').' progress') );
    $jauge->set_tooltip('Ce personnage est mort.');
    $jauge->add( new interf_bal_cont('span', null, 'icone icone-mort') );
	}
  protected function creer_infos_groupe()
  {
  	if( $this->perso->get_groupe() != 0 )
		{
			$groupe = new groupe($this->perso->get_groupe());
			$div = $this->add( new interf_bal_cont('div', 'perso_groupe') );
			$liste = $div->add( new interf_bal_cont('ul') );
			
			// Membres du groupe
			$nombre_joueur_groupe = 1;
			foreach($groupe->get_membre_joueur() as $membre)
			{
				if($this->perso->get_id() != $membre->get_id())
				{
					$this->creer_infos_membre($liste, $membre, $groupe, $nombre_joueur_groupe);
					$nombre_joueur_groupe++;
				}
			}
		}
  }
  protected function creer_infos_membre($liste, $membre, $groupe, $index)
  {
  	$li = $liste->add( new interf_bal_cont('li', 'membre_'.$index, 'membre_groupe') );
  	
		switch( $membre->get_statut() )
		{
		case 'hibern':
			$this->creer_activite('hibern', 'Le personnage hiberne', $li);
			break;
		case 'inactif':
			$this->creer_activite('inactif', 'Le personnage est inactif', $li);
			break;
		case 'ban':
			$this->creer_activite('ban', 'Le personnage est banni jusqu\'au '.date('d/m/Y', $membre->get_fin_ban()), $li);
			break;
		case 'suppr':
			$this->creer_activite('suppr', 'Le personnage a été supprimé', $li);
			break;
		case 'actif':
			$duree = time() - $membre->get_dernieraction();
		  if( $duree > royaume::duree_actif )
				$this->creer_activite('actif', 'Le personnage est moyennement actif', $li);
			elseif( $duree > 60 )
				$this->creer_activite('tres_actif', 'Le personnage est actif', $li);
			else
				$this->creer_activite('connecte', 'Le personnage vient de se connecter', $li);
			break;
		default:
			log_admin::log('bug', 'statut du personnage "'.$membre->get_nom().'" inconnu : '.$membre->get_statut());
			break;
		}
		$classe = 'perso_groupe_nom';
  	if( $membre->get_id() == $groupe->get_id_leader() )
  		$classe .= ' perso_groupe_chef';
  	$nom = $li->add( new interf_bal_smpl('a', $membre->get_nom(), null, $classe) );
  	if( $membre->get_hp() > 0 )
			$this->creer_jauge($li, 'Points de vie', $membre->get_hp(), floor($membre->get_hp_maximum()), false, 'danger', 'hp');
		else
			$this->creer_jauge_mort($li);
    $this->creer_jauge($li, 'Points de mana', $membre->get_mp(), floor($membre->get_mp_maximum()), false, false, 'mp');
    $pos = $li->add( new interf_bal_cont('div', null, 'membre_lieu') );
    $pos->add( new interf_bal_smpl('span', 'Pos. : '.$membre->get_x().' / '.$membre->get_y(), null, 'membre_pos') );
    $pos->add( new interf_txt(' - ') );
    $pos->add( new interf_bal_smpl('span', 'dist. : '.calcul_distance(convert_in_pos($membre->get_x(), $membre->get_y()), convert_in_pos($this->perso->get_x(), $this->perso->get_y())), null, 'membre_pos') );
    $buffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
    $buffs->add( new interf_liste_buff($membre, false) );/**/
    $debuffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
    $debuffs->add( new interf_liste_buff($membre, true) );
	}
	protected function creer_activite($type, $message, $li)
	{
		$span = $li->add( new interf_bal_smpl('span', '', null, 'groupe_activite activite_'.$type) );
		$span->set_attribut('title', $message);
	}
}
?>