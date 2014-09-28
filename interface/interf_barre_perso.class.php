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
    $this->creer_jauge_xp($this->perso->get_exp(), prochain_level($this->perso->get_level()), progression_level(level_courant($this->perso->get_exp())), $this->perso->get_level());
  }
  protected function creer_infos_perso()
  {
    global $Gtrad, $db;
    // Race & classe
    $this->infos_perso = $this->add( new interf_bal_cont('div', 'infos_perso') );
    $this->infos_perso->set_attribut('style', 'background-image:url(\'./image/interface/fond_info_perso_'.$this->perso->get_race_a().'.png\');');
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
    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_a();
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $att = $this->infos_perso->add( new interf_bal_smpl('div', $row['nom'] ? $row['nom'] : 'pas de script', 'perso_attaque', 'perso_script'.($row['nom'] ? '' : ' sans_script')) );
    $arme = $this->perso->get_arme();
    if( $arme )
    {
    	$arme = new arme($arme->id);
    	$nom_arme = $arme->get_nom();
			$att->set_attribut('style', 'background-image:url(\''.$arme->get_image().'\');');
		}
		else
			$nom_arme = 'aucune';
    $att->set_tooltip('Votre arme&nbsp;: '.$nom_arme.($row['nom'] ? ' − Votre script d\'attaque&nbsp;: '.$row['nom'] : ' − Vous n\'avez pas de script d\'attaque'), 'bottom');
    // défense
    $requete = 'SELECT nom FROM action_perso WHERE id = '.$this->perso->get_action_d();
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $def = $this->infos_perso->add( new interf_bal_smpl('div', $row['nom'] ? $row['nom'] : 'pas de script', 'perso_defense', 'perso_script'.($row['nom'] ? '' : ' sans_script')) );
    $bouclier = $this->perso->get_bouclier();
    if( $bouclier )
    {
    	$bouclier = new arme($bouclier->id);
    	$nom_bouclier = $bouclier->get_nom();
			$def->set_attribut('style', 'background-image:url(\''.$bouclier->get_image().'\');');
		}
		else
			$nom_bouclier = 'aucun';
    $def->set_tooltip('Votre bouclier&nbsp;: '.$nom_bouclier.($row['nom'] ? ' − Votre script de défense&nbsp;: '.$row['nom'] : ' − Vous n\'avez pas de script de défense'), 'bottom');
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
    $heure = $this->add( new interf_bal_cont('a', 'perso_heure') );
    $heure->set_attribut('style', 'background-image:url(image/interface/'.moment_jour().'.png);');
    $heure->set_tooltip(moment_jour(), 'bottom');
    $span = $heure->add( new interf_bal_smpl('span', substr(date_sso(time()),0,-3), 'heure') );
    $pos = $this->add( new interf_bal_cont('a', 'perso_position') );
    $img = $pos->add( new interf_bal_smpl('img') );
    $img->set_attribut('src', 'carte_perso.php?vue=12');
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
		$parent->add( new interf_jauge_bulle($nom, $valeur, $maximum, $grand, $type, $grand?'perso_'.$type:false, $grand?'jauge_bulle':'jauge_groupe membre_'.$type) );
	}
  protected function creer_jauge_xp($valeur, $maximum, $progression, $niv)
  {
    $jauge = $this->infos_vie->add( new interf_bal_cont('div', 'perso_xp', 'jauge_barre progress') );
    $jauge->set_tooltip('Niveau&nbsp;: '.$niv.' − Points d\'expérience&nbsp;: '.$valeur, 'bottom');
    //$barre = $jauge->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-warning') );
    $barre = $jauge->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-warning') );
    $barre->set_attribut('style', 'width:'.$progression.'%');
    $jauge->add( new interf_bal_smpl('div', $valeur.' / '.$maximum.' − niv. '.$niv, 'xp', 'barre_valeur') );
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
    $jauge->set_tooltip('Ce personnage est mort.', 'bottom');
    $jauge->add( new interf_bal_cont('span', null, 'icone icone-mort') );
    if($rez)
    	$jauge->set_attribut('onclick', 'chargerPopover(\''.$id.'\', \'infos_'.$id.'\', \'bottom\', \'infoperso.php?action=infos_rez&id='.$id_membre.'\', \'Résurections\')');
	}
  protected function creer_infos_groupe()
  {
		$nombre_joueur_groupe = 1;
		$div = $this->add( new interf_bal_cont('div', 'perso_groupe') );
		$liste = $div->add( new interf_bal_cont('ul') );
  	if( $this->perso->get_groupe() )
  	{
			$groupe = new groupe($this->perso->get_groupe());
			
			// Membres du groupe
			foreach($groupe->get_membre_joueur() as $membre)
			{
				if($this->perso->get_id() != $membre->get_id())
				{
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
	    $dist = calcul_distance(convert_in_pos($membre->get_x(), $membre->get_y()), convert_in_pos($this->perso->get_x(), $this->perso->get_y()));
			/// @todo gérer les coordonnées cachées
	    $txt = 'Pos. : '.$membre->get_x().' / '.$membre->get_y();
	    $txt .= ' - '.'dist. : '.$dist;
	    $pos->add( new interf_bal_smpl('span', $txt, null, 'membre_pos'.($dist>7?' trop_loin':'')) );
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
		$oui->set_attribut('href', 'reponseinvitation.php?id='.$invitation->get_id().'&reponse=oui&groupe='.$invitation->get_groupe());
		$oui->set_attribut('onclick', 'charger(this.href);');
		$non = $div->add( new interf_bal_cont('a', false, 'choix_invitation') );
		$non->add( new interf_bal_smpl('span', '', false, 'icone icone-croix2') );
		$non->add( new interf_bal_smpl('span', 'Refuser', false, 'texte') );
		$non->set_attribut('href', 'reponseinvitation.php?id='.$invitation->get_id().'&reponse=non&groupe='.$invitation->get_groupe());
		$non->set_attribut('onclick', 'charger(this.href);');
	}
	static function creer_activite(&$perso, &$parent)
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
		$span = $parent->add( new interf_bal_smpl('span', '', null, 'groupe_activite activite_'.$type) );
		$span->set_tooltip($message, 'bottom');
	}
}
?>