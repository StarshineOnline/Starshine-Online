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
    $this->creer_jauge('hp', 'Points de vie', $this->perso->get_hp(), floor($this->perso->get_hp_maximum()), 'danger');
    $this->creer_jauge('mp', 'Points de mana', $this->perso->get_mp(), floor($this->perso->get_mp_maximum()));
    $this->creer_jauge('pa', 'Points d\'action', $this->perso->get_pa(), $G_PA_max, 'success');
    $this->creer_jauge_xp($this->perso->get_exp(), prochain_level($this->perso->get_level()), progression_level(level_courant($this->perso->get_exp())));
  }
  protected function creer_infos_perso()
  {
    global $Gtrad;
    // Race & classe
    $this->infos_perso = $this->add( new interf_bal_cont('div', 'infos_perso') );
    $this->infos_perso->set_attribut('style', 'background-image:url(\'./image/interface/fond_info_perso_'.$this->perso->get_race_a().'.png\');');
    $race_classe = $this->infos_perso->add( new interf_bal_cont('div', 'race_classe') );
    $race_classe->add( new interf_bal_smpl('span', ucwords($Gtrad[$this->perso->get_race()]), 'race') );
    $race_classe->add( new interf_bal_smpl('span', ucwords($this->perso->get_classe()), 'classe') );
    // Honneur & réputation
    $ph = $this->infos_perso->add( new interf_bal_cont('div', 'perso_ph') );
    $ph->set_attribut('titre', 'Votre honneur : '.$this->perso->get_honneur().' / Votre réputation : '.$this->perso->get_reputation());
    $ph->add( new interf_bal_smpl('span', $this->perso->get_honneur(), 'honneur') );
    $ph->add( new interf_bal_smpl('br') );
    $ph->add( new interf_bal_smpl('span', $this->perso->get_reputation(), 'reputation') );
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
    $img->set_attribut('src', 'carte_perso.php?vue=11');
  }
  protected function creer_jauge($type, $nom, $valeur, $maximum, $style=false)
  {
    $jauge = $this->infos_vie->add( new interf_bal_cont('div', 'perso_'.$type, 'jauge_bulle progress') );
    $jauge->set_tooltip($nom.' : '.$valeur.' / '.$maximum, 'right');
    /*$jauge->set_attribut('title', $nom.' : '.$valeur.' / '.$maximum);
    $jauge->set_attribut('data-toggle', 'tooltip');
    $jauge->set_attribut('data-placement', 'right');*/
    $barre = $jauge->add( new interf_bal_cont('div', null, 'bulle progress-bar'.($style?' progress-bar-'.$style:'')) );
    $barre->set_attribut('style', 'height:'.round($valeur/$maximum*100,0).'%');
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
}
?>