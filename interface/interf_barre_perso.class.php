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
    $this->perso->check_perso( $this->perso->get_id_joueur() == joueur::factory()->get_id() );
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
    $this->infos_vie = $this->add( new interf_bal_cont('div', 'infos_vie', 'aide') );
    // nom
    $titre_perso = new titre($_SESSION["ID"]);
    $bonus = recup_bonus($this->perso->get_id());
    $titre = $titre_perso->get_titre_perso($bonus);
    $nom = $this->infos_vie->add( new interf_lien_cont('personnage.php', 'nom_perso') );
    $nom->set_tooltip('Accès à la fiche de votre personnage', 'bottom');
    $nom->add( new interf_bal_smpl('span', $titre[0]) );
    $nom->add( new interf_bal_smpl('span', ucwords($this->perso->get_grade()->get_nom()), 'grade') );
    //$nom->add( new interf_txt(' ') );
    $nom->add( new interf_bal_smpl('span', ucwords($this->perso->get_nom()), 'nom') );
    $nom->add( new interf_bal_smpl('span', $titre[1]) );
    // jauges
    $this->creer_jauge($this->infos_vie, 'Points de vie', $this->perso->get_hp(), floor($this->perso->get_hp_maximum()), true, /*'danger',*/ 'hp');
    $this->creer_jauge($this->infos_vie, 'Points de mana', $this->perso->get_mp(), floor($this->perso->get_mp_maximum()), true, /*false,*/ 'mp');
    $this->creer_jauge($this->infos_vie, 'Points d\'action', $this->perso->get_pa(), $G_PA_max, true, /*'success',*/ 'pa');
    //$this->creer_jauge_xp($this->perso->get_exp(), prochain_level($this->perso->get_level()), progression_level(level_courant($this->perso->get_exp())), $this->perso->get_level());
    $this->creer_jauge_xp($this->perso->get_avancement() + $this->perso->get_corr_avance_artisanat(), $this->perso->get_level());
  }
  protected function creer_infos_perso()
  {
    global $Gtrad, $db;
    // Race & classe
    $this->infos_perso = $this->add( new interf_bal_cont('div', 'infos_perso', 'aide') );
    $this->infos_perso->set_attribut('style', 'background-image:url(\'./image/interface/info_perso/fond_info_perso_'.$this->perso->get_race_a().'.png\');');
    $race_classe = $this->infos_perso->add( new interf_lien_cont('personnage.php', 'race_classe') );
    $race_classe->set_tooltip('Accès à la fiche de votre personnage', 'bottom');
    $race_classe->add( new interf_bal_smpl('span', ucwords($Gtrad[$this->perso->get_race()]), 'race') );
    $race_classe->add( new interf_bal_smpl('span', ucwords($this->perso->get_classe()), 'classe') );
    // Honneur & réputation
    $ph = $this->infos_perso->add( new interf_bal_cont('div', 'perso_ph') );
    $ph->set_tooltip('Votre honneur&nbsp;: '.$this->perso->get_honneur().' / Votre réputation&nbsp;: '.$this->perso->get_reputation(), 'bottom');
    $ph->add( new interf_bal_smpl('span', $this->perso->get_honneur(), 'honneur') );
    $ph->add( new interf_bal_smpl('br') );
    $ph->add( new interf_bal_smpl('span', $this->perso->get_reputation(), 'reputation') );
    // stars
    $stars = $this->infos_perso->add( new interf_bal_smpl('div', $this->perso->get_star(), 'perso_stars') );
    $stars->set_tooltip('Votre argent&nbsp;: '.$this->perso->get_star().' stars', 'bottom');
    // attaque
    /// @todo passer à l'objet
    if( $this->perso->get_action_a() )
    {
	    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_a();
	    $req = $db->query($requete);
	    $row = $db->read_array($req);
	    $nom_script = $row[0];
	    $texte_script = ' − Votre script d\'attaque&nbsp;: '.$nom_script;
		}
		else
		{
	    $nom_script = 'pas de script';
	    $texte_script = ' − Vous n\'avez pas de script d\'attaque';
		}
    $att = $this->infos_perso->add( new interf_bal_smpl('div', $nom_script, 'perso_attaque', 'perso_script'.($row['nom'] ? '' : ' sans_script')) );
    $arme = $this->perso->get_arme();
    if( $arme )
    {
    	$arme = new arme($arme->id);
    	$nom_arme = $arme->get_nom();
			$att->set_attribut('style', 'background-image:url(\''.$arme->get_image().'\');');
		}
		else
			$nom_arme = 'aucune';
    $att->set_tooltip('Votre arme&nbsp;: '.$nom_arme.$texte_script, 'bottom');
    // défense
    if( $this->perso->get_action_d() )
    {
	    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_d();
	    $req = $db->query($requete);
	    $row = $db->read_array($req);
	    $nom_script = $row[0];
	    $texte_script = ' − Votre scriptde défense&nbsp;: '.$nom_script;
		}
		else
		{
	    $nom_script = 'pas de script';
	    $texte_script = ' − Vous n\'avez pas de script de défense';
		}
    $def = $this->infos_perso->add( new interf_bal_smpl('div', $nom_script, 'perso_defense', 'perso_script'.($row['nom'] ? '' : ' sans_script')) );
    $bouclier = $this->perso->get_bouclier();
    if( $bouclier )
    {
    	$bouclier = new arme($bouclier->id);
    	$nom_bouclier = $bouclier->get_nom();
			$def->set_attribut('style', 'background-image:url(\''.$bouclier->get_image().'\');');
		}
		else
			$nom_bouclier = 'aucun';
    $def->set_tooltip('Votre bouclier&nbsp;: '.$nom_bouclier.$texte_script, 'bottom');
    // créature dressée
    $creature = $this->perso->get_pet();
    if( $creature )
    {
    	$creat = $this->infos_perso->add( new interf_bal_smpl('div', $creature->get_nom(), 'perso_dresse', 'perso_script') );
    	$monstre = new monstre( $creature->get_id_monstre() );
    	/// @todo utiliser une méthode pour obtenir l'image
			$creat->set_attribut('style', 'background-image:url(\'./image/monstre/'.$monstre->get_lib().'.png\');');
    	$creat->set_tooltip('Votre créature principale&nbsp;: '.$creature->get_nom().' ('.$monstre->get_nom().')', 'bottom');
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
  	global $G_interf, $Trace;
    $heure = $this->add( new interf_lien_cont('moment_jour.php', 'perso_heure', 'aide') );
    $heure->set_attribut('style', 'background-image:url(image/interface/'.moment_jour().'.png);');
    $heure->set_tooltip(moment_jour(), 'bottom');
    $span = $heure->add( new interf_bal_smpl('span', substr(date_sso(time()),0,-3), 'heure') );
    $pos = $this->add( new interf_lien_cont('carte.php', 'perso_position', 'aide') );
    $carte = $pos->add( $G_interf->creer_carte_monde() );
    $carte->aff_svg(12);
		$carte->aff_groupe( $this->perso->get_id_groupe() );
		/*$royaume = new royaume( $Trace[$this->perso->get_race()]['numrace'] );
		$carte->aff_batiments($royaume, 12);*/
  }
	protected function creer_jauge($parent, $nom, $valeur, $maximum, $grand, /*$style=false,*/ $type=null)
	{
    /*$jauge = $parent->add( new interf_bal_cont('div', $grand?'perso_'.$type:'', ($grand?'jauge_bulle':'jauge_groupe membre_'.$type).' progress') );
    $jauge->set_tooltip($nom.'&nbsp;: '.$valeur.' / '.$maximum, 'bottom');
    //$barre = $jauge->add( new interf_bal_cont('div', null, 'bulle progress-bar'.($style?' progress-bar-'.$style:'')) );
    $barre = $jauge->add( new interf_bal_cont('div', null, 'bulle jauge-'.$type) );
    $barre->set_attribut('style', 'height:'.round($valeur/$maximum*100,0).'%');
    if( $grand )
			$jauge->add( new interf_bal_smpl('div', $valeur.'/'.$maximum, $type, 'bulle_valeur') );*/
		return $parent->add( new interf_jauge_bulle($nom, $valeur, $maximum, $grand, $type, $grand?'perso_'.$type:false, $grand?'jauge_bulle':'jauge_groupe membre_'.$type) );
	}
  //protected function creer_jauge_xp($valeur, $maximum, $progression, $niv)
  protected function creer_jauge_xp($valeur, $niv)
  {
    $jauge = $this->infos_vie->add( new interf_bal_cont('div', 'perso_xp', 'jauge_barre progress') );
    //$jauge->set_tooltip('Niveau&nbsp;: '.$niv.' − Points d\'expérience&nbsp;: '.$valeur, 'bottom');
    $jauge->set_tooltip('Niveau&nbsp;: '.$niv.' − Avancement&nbsp;: '.round($valeur, 1).'%', 'bottom');
    //$barre = $jauge->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-warning') );
    $barre = $jauge->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-warning') );
    $barre->set_attribut('style', 'width:'.min($valeur, 100).'%');
    $jauge->add( new interf_bal_smpl('div', 'Niveau : '.$niv, 'xp', 'barre_valeur') );
  }
	protected function creer_jauge_mort($parent, $grand=false, $id_membre=null)
	{
		global $db;
		$id = $grand?'perso_hp':'membre_hp_'.$id_membre;
		/// @todo passer à l'objet
		$requete = 'SELECT count(*) FROM rez WHERE id_perso = '.$id_membre;
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$rez = $row[0] > 0;
    $jauge = $parent->add( new interf_bal_cont('div', $id, ($grand?'jauge_bulle':'jauge_groupe membre_hp').' progress  '.($rez?'rez':'')) );
    $jauge->set_tooltip('Ce personnage est mort.'.($rez?' Au moins un sort de résurection est actif sur lui.':''), 'bottom');
    $jauge->add( new interf_bal_cont('span', null, 'icone icone-mort') );
    if($rez)
    	$jauge->set_attribut('onclick', 'chargerPopover(\''.$id.'\', \'infos_'.$id.'\', \'bottom\', \'infoperso.php?action=infos_rez&id='.$id_membre.'\', \'Résurections\')');
	}
  protected function creer_infos_groupe()
  {
		$nombre_joueur_groupe = 1;
		$div = $this->add( new interf_bal_cont('div', 'perso_groupe', 'aide') );
		$liste = $div->add( new interf_bal_cont('ul') );
  	if( $this->perso->get_id_groupe() )
  	{
			$groupe = new groupe($this->perso->get_id_groupe());
			
			// Membres du groupe
			foreach($groupe->get_membre_joueur() as $membre)
			{
				if($this->perso->get_id() != $membre->get_id())
				{
					$membre->check_perso(false);
					$this->creer_infos_membre($liste, $membre, $groupe, $nombre_joueur_groupe);
					$nombre_joueur_groupe++;
				}
			}
		}
		else
		{
			$invitation = invitation::create('receveur', $_SESSION['ID']);
			//Si il y a une invitation pour le joueur
			for($i=0; $i < count($invitation) && $i < 4; $i++)
			{
					$this->creer_infos_invit($liste, $invitation[$i], $nombre_joueur_groupe);
					$nombre_joueur_groupe++;
			}
		}
		for(;$nombre_joueur_groupe<=4;$nombre_joueur_groupe++)
		{
			$this->creer_infos_membre($liste, null, null, $nombre_joueur_groupe);
		}
  }
  protected function creer_infos_membre($liste, $membre, $groupe, $index)
  {
  	$li = $liste->add( new interf_bal_cont('li', 'membre_'.$index, 'membre_groupe') );
  	
  	if( $membre )
  	{
  		$this->creer_activite($membre, $li);
			$classe = 'perso_groupe_nom';
	  	if( $membre->get_id() == $groupe->get_id_leader() )
	  		$classe .= ' perso_groupe_chef';
	  	$nom = $li->add( new interf_lien($membre->get_nom(), 'infoperso.php?id='.$membre->get_id(), null, $classe) );
	  	if( $membre->get_hp() > 0 )
				$this->creer_jauge($li, 'Points de vie', $membre->get_hp(), floor($membre->get_hp_maximum()), false, /*'danger',*/ 'hp');
			else
				$this->creer_jauge_mort($li, false, $membre->get_id());
	    $this->creer_jauge($li, 'Points de mana', $membre->get_mp(), floor($membre->get_mp_maximum()), false, /*false,*/ 'mp');
	    $pos = $li->add( new interf_bal_cont('div', null, 'membre_lieu') );
	    $dist = $this->perso->calcule_distance($membre);
	    $place_buff = $membre->get_grade()->get_nb_buff() - $membre->get_nb_buff();
			// Nysin
			if( 75 <= $membre->get_x() && $membre->get_x() <= 100 && 288 <= $membre->get_y() && $membre->get_y() <= 305 )
	    	$pos->add( new interf_bal_smpl('span', 'Pos. : * / *', null, 'membre_pos') );
			else
	    	$pos->add( new interf_bal_smpl('span', 'Pos. : '.$membre->get_x().' / '.$membre->get_y(), null, 'membre_pos') );
	    $pos->add( new interf_txt(' - ') );
	    if( $dist <= 7 )
	    {
	    	if( $place_buff > 0 )
	    	{
	    		$classe = 'success';
	    		$tooltip = 'Ce personnage est à portée pour des sorts de groupe ou compétences et peut encore recevoir '.$place_buff.' buffs.';
				}
				else
	    	{
	    		$classe = 'warning';
	    		$tooltip = 'Ce personnage est à portée pour des sorts de groupe ou compétences mais ne peut plus recevoir de buffs.';
				}
			}
			else
	    {
	    	$classe = 'danger';
	    	if( $place_buff > 0 )
	    		$tooltip = 'Ce personnage n\'est pas à portée (il peut néanmoins encore recevoir '.$place_buff.' buffs).';
	    	else
	    		$tooltip = 'Ce personnage n\'est pas à portée et ne peut plus recevoir de buffs.';
			}
	    $pos->add( new interf_bal_smpl('span', 'dist. : '.$dist, null, 'membre_dist text-'.$classe) )->set_tooltip($tooltip, 'left');
	    $buffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
	    $buffs->add( new interf_liste_buff($membre, false) );
	    $debuffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
	    $debuffs->add( new interf_liste_buff($membre, true) );
		}
	}
  protected function creer_infos_invit($liste, &$invitation, $index)
  {
		$perso = new perso($invitation->get_inviteur());
		$li = $liste->add( new interf_bal_cont('li', 'membre_'.$index, 'membre_groupe') );
		$li->add( new interf_bal_smpl('div', 'Vous avez reçu une invitation pour grouper avec '.$perso->get_nom(), false, 'invitation') );
		$div = $li->add( new interf_bal_cont('div') );
		$oui = $div->add( new interf_bal_cont('a', false, 'choix_invitation') );
		$oui->add( new interf_bal_smpl('span', '', false, 'icone icone-ok2') );
		$oui->add( new interf_bal_smpl('span', 'Accepter', false, 'texte') );
		$oui->set_attribut('href', 'reponseinvitation.php?id='.$invitation->get_id().'&reponse=oui');
		$oui->set_attribut('onclick', 'return charger(this.href);');
		$non = $div->add( new interf_bal_cont('a', false, 'choix_invitation') );
		$non->add( new interf_bal_smpl('span', '', false, 'icone icone-croix2') );
		$non->add( new interf_bal_smpl('span', 'Refuser', false, 'texte') );
		$non->set_attribut('href', 'reponseinvitation.php?id='.$invitation->get_id().'&reponse=non');
		$non->set_attribut('onclick', 'return charger(this.href);');
	}
	static function creer_activite(&$perso, &$parent, $classe='')
	{
		switch( $perso->get_statut() )
		{
		case 'hibern':
			$type = 'hibern';
			$message = 'Le personnage hiberne';
			break;
		case 'inactif':
			$type = 'inactif';
			$message = 'Le personnage est inactif';
			break;
		case 'ban':
			$type = 'ban';
			$message = 'Le personnage est banni jusqu\'au '.date('d/m/Y', $membre->get_fin_ban());
			break;
		case 'suppr':
			$type = 'suppr';
			$message = 'Le personnage a été supprimé';
			break;
		case 'actif':
			$duree = time() - $perso->get_dernieraction();
		  if( $duree > royaume::duree_actif )
		  {
				$type = 'actif';
				$message = 'Le personnage est moyennement actif';
			}
			elseif( $duree > 60 )
		  {
				$type = 'tres_actif';
				$message = 'Le personnage est actif';
			}
			else
		  {
				$type = 'connecte';
				$message = 'Le personnage vient de se connecter';
			}
			break;
		default:
			log_admin::log('bug', 'statut du personnage "'.$membre->get_nom().'" inconnu : '.$membre->get_statut());
			break;
		}
		$span = $parent->add( new interf_bal_smpl('span', '', null, 'groupe_activite activite_'.$type.$classe) );
		$span->set_tooltip($message, 'bottom');
	}
}

class interf_barre_perso_shine extends interf_barre_perso
{
	protected function creer_jauge($parent, $nom, $valeur, $maximum, $grand, /*$style=false,*/ $type=null)
	{
		if( $grand )
		{
			$div = $parent->add( new interf_bal_smpl('div', $valeur.' / '.$maximum, 'perso_'.$type, 'jauge_shine') );
			$img = '';
		}
		else
		{
			$div = $parent->add( new interf_bal_smpl('div', false, false, 'jauge_shine_groupe membre_'.$type) );
			$img = 'g_';
		}
		$img .= $type == 'hp' ? 'vie' : $type;
		$img .= round($valeur/$maximum*10);
		$div->set_attribut('style', 'background:url(./image/barre/'.$img.'.png) no-repeat scroll center center transparent;');
		$div->set_tooltip($nom.' : '.$valeur.' / '.$maximum);
		return $div;
	}
  //protected function creer_jauge_xp($valeur, $maximum, $progression, $niv)
  protected function creer_jauge_xp($valeur, $niv)
  {
		$div = $this->infos_vie->add( new interf_bal_smpl('div', 'Niv. : '.$niv, 'perso_'.$type, 'jauge_shine') );
		$img .= 'exp'.round(min($valeur, 100)/10);
		$div->set_attribut('style', 'background:url(./image/barre/'.$img.'.png) no-repeat scroll center center transparent;');
		$div->set_tooltip('Avancement : '.$valeur.'%');
  }
  protected function creer_infos_membre($liste, $membre, $groupe, $index)
  {
  	$li = $liste->add( new interf_bal_cont('li', 'membre_'.$index, 'membre_groupe') );
  	
  	if( $membre )
  	{
  		$mort =  $membre->get_hp() <= 0 ? ' mort' : '';
  		$this->creer_activite($membre, $li, $mort);
			$classe = 'perso_groupe_nom';
	  	if( $membre->get_id() == $groupe->get_id_leader() )
	  		$classe .= ' perso_groupe_chef';
	  	$nom = $li->add( new interf_lien($membre->get_nom(), 'infoperso.php?id='.$membre->get_id(), null, $classe) );
			$hp = $this->creer_jauge($li, 'Points de vie', $membre->get_hp(), floor($membre->get_hp_maximum()), false, 'hp');
	    $mp = $this->creer_jauge($li, 'Points de mana', $membre->get_mp(), floor($membre->get_mp_maximum()), false, 'mp');
	    $dist = $this->perso->calcule_distance($membre);
	  	if( $membre->get_hp() <= 0 )
	  	{
				$this->creer_jauge_mort($li, false, $membre->get_id());
				$hp->set_attribut('class', $hp->get_attribut('class').' mort' );
				$mp->set_attribut('class', $mp->get_attribut('class').' mort' );
			}
	    if( $dist <= 7 )
	    {
	    	if( $place_buff > 0 )
	    	{
	    		$classe = 'success';
	    		$tooltip = 'Ce personnage est à portée pour des sorts de groupe ou compétences et peut encore recevoir '.$place_buff.' buffs.';
				}
				else
	    	{
	    		$classe = 'warning';
	    		$tooltip = 'Ce personnage est à portée pour des sorts de groupe ou compétences mais ne peut plus recevoir de buffs.';
				}
			}
			else
	    {
	    	$classe = 'danger';
	    	if( $place_buff > 0 )
	    		$tooltip = 'Ce personnage n\'est pas à portée (il peut néanmoins encore recevoir '.$place_buff.' buffs).';
	    	else
	    		$tooltip = 'Ce personnage n\'est pas à portée et ne peut plus recevoir de buffs.';
			}
			// Nysin ?
			if( 75 <= $membre->get_x() && $membre->get_x() <= 100 && 288 <= $membre->get_y() && $membre->get_y() <= 305 )
	    	$pos->add( new interf_bal_smpl('span', 'Pos. : * / *', null, 'membre_pos') );
			else
	    	$pos = $li->add( new interf_bal_smpl('div', 'Pos. : '.$membre->get_x().' / '.$membre->get_y(), false, 'membre_lieu') );
	    $pos = $li->add( new interf_bal_smpl('div', 'Dist. : '.$dist, false, 'membre_dist text-'.$classe) )->set_tooltip($tooltip, 'left');
	    $buffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
	    $buffs->add( new interf_liste_buff($membre, false) );
	    $debuffs = $li->add( new interf_bal_cont('div', null, 'membre_buffs') );
	    $debuffs->add( new interf_liste_buff($membre, true) );
		}
	}
	protected function creer_jauge_mort($parent, $grand=false, $id_membre=null)
	{
		global $db;
		$id = $grand?'perso_hp':'membre_hp_'.$id_membre;
		/// @todo passer à l'objet
		$requete = 'SELECT count(*) FROM rez WHERE id_perso = '.$id_membre;
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$rez = $row[0] > 0;
		$img = $parent->add( new interf_img('image/interface/mort.png', 'Mort', false, 'membre_mort'.($rez?' rez':'')) );;
    $img->set_tooltip('Ce personnage est mort.'.($rez?' Au moins un sort de résurection est actif sur lui.':''), 'bottom');
    if($rez)
    	$img->set_attribut('onclick', 'chargerPopover(\''.$id.'\', \'infos_'.$id.'\', \'bottom\', \'infoperso.php?action=infos_rez&id='.$id_membre.'\', \'Résurections\')');
	}
}
?>