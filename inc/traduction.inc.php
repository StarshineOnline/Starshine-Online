<?php
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * @file traduction.inc.php
 * Gestion de la traduction. 
 */

/**
 * @name Aptitudes, sorts et compétences
 * Traduction des types de magie, sorts et compétences.
 * @{  
 */
$Gtrad['sort_vie'] = 'Magie de la vie';
$Gtrad['sort_element'] = 'Magie élémentaire';
$Gtrad['sort_mort'] = 'Nécromancie';
$Gtrad['favoris'] = 'Favoris';
$Gtrad['buff_critique'] = 'Buff';
$Gtrad['buff_evasion'] = 'Buff';
$Gtrad['buff_bouclier'] = 'Buff';
$Gtrad['buff_inspiration'] = 'Buff';
$Gtrad['buff_force'] = 'Buff';
$Gtrad['buff_barriere'] = 'Buff';
$Gtrad['buff_epine'] = 'Buff';
$Gtrad['buff_bouclier_sacre'] = 'Buff';
$Gtrad['buff_rapidite'] = 'Buff';
$Gtrad['buff_meditation'] = 'Buff';
$Gtrad['buff_rage_vampirique'] = 'Buff';
$Gtrad['buff_colere'] = 'Buff';
$Gtrad['debuff_aveuglement'] = 'Débuff';
$Gtrad['debuff_desespoir'] = 'Débuff';
$Gtrad['vie'] = 'Soin';
$Gtrad['rez'] = 'Soin';
$Gtrad['teleport'] = 'Général';
$Gtrad['body_to_mind'] = 'Général';
$Gtrad['tir_precis'] = 'Tir Précis';
$Gtrad['oeil_faucon'] = 'Oeil du faucon';
$Gtrad['coup_puissant'] = 'Coup Puissant';
//@}


/**
 * @name Royaumes
 * Traduction des noms de royaumes et de races.
 * @{  
 */
// Royaumes
$Gtrad['barbare'] = 'Barbare';
$Gtrad['elfebois'] = 'Elfe des bois';
$Gtrad['elfehaut'] = 'Haut-Elfe';
$Gtrad['humain'] = 'Humain';
$Gtrad['humainnoir'] = 'Corrompu';
$Gtrad['mortvivant'] = 'Mort-Vivant';
$Gtrad['nain'] = 'Nain';
$Gtrad['orc'] = 'Orc';
$Gtrad['scavenger'] = 'Scavenger';
$Gtrad['troll'] = 'Troll';
$Gtrad['vampire'] = 'Vampire';
// Races
$Gtrad['neutre'] = 'Neutre';
$Gtrad['pnj'] = 'PNJ';
$Gtrad['coyotte'] = 'Coyote';
//@}


/**
 * @name Emplacements
 * Traduction des emplacements des armes, armures e accessoires.
 * @{  
 */
$Gtrad['main_droite'] = 'Main droite';
$Gtrad['main_gauche'] = 'Main gauche';
$Gtrad['main_droite;main_gauche'] = 'Deux mains';
$Gtrad['tete'] = 'Tête';
$Gtrad['torse'] = 'Torse';
$Gtrad['main'] = 'Main';
$Gtrad['ceinture'] = 'Ceinture';
$Gtrad['jambe'] = 'Jambe';
$Gtrad['chaussure'] = 'Chaussure';
$Gtrad['cou'] = 'Cou';
$Gtrad['dos'] = 'Dos';
$Gtrad['doigt'] = 'Doigt';
$Gtrad['grand_accessoire'] = 'Grand accessoire';
$Gtrad['moyen_accessoire'] = 'Moyen accessoire';
$Gtrad['petit_accessoire'] = 'Petit accessoire';
$Gtrad['selle'] = 'Selle';
$Gtrad['arme_pet'] = 'Arme';
$Gtrad['cou_pet'] = 'Cou';
$Gtrad['dos_pet'] = 'Dos';
$Gtrad['torse_pet'] = 'Torse';
$Gtrad['slot_1'] = 'Slot niveau 1';
$Gtrad['slot_2'] = 'Slot niveau 2';
$Gtrad['slot_3'] = 'Slot niveau 3';
$Gtrad['vendre_marchand'] = 'Vendre au marchand';
$Gtrad['hotel_vente'] = 'Hotel des ventes';
$Gtrad['depot'] = 'Dépôt';
$Gtrad['utiliser'] = 'Utiliser';
$Gtrad['identifier'] = 'Identifier';
$Gtrad['enchasser'] = 'Enchâsser / récupérer une gemme';
//@}


/**
 * @name Types d'arme
 * Traduction des types d'arme.
 * @{
 */
$Gtrad['epee'] = 'épée';
$Gtrad['hache'] = 'hache';
$Gtrad['dague'] = 'dague';
$Gtrad['arc'] = 'arc';
$Gtrad['baton'] = 'bâton';
$Gtrad['bouclier'] = 'bouclier';
//@}


/**
 * @name Compétences
 * Traduction des compétences.
 * @{  
 */
$Gtrad['distance'] = 'Tir à distance';
$Gtrad['melee'] = 'Mêlée';
$Gtrad['esquive'] = 'Esquive';
$Gtrad['blocage'] = 'Blocage';
$Gtrad['incantation'] = 'Incantation';
$Gtrad['maitrise_critique'] = 'Maitrise du critique';
$Gtrad['art_critique'] = 'Art du critique';
$Gtrad['maitrise_arc'] = 'Maitrise de l\'arc';
$Gtrad['maitrise_dague'] = 'Maitrise de la dague';
$Gtrad['maitrise_epee'] = 'Maitrise de l\'épée';
$Gtrad['maitrise_hache'] = 'Maitrise de la hache';
$Gtrad['maitrise_bouclier'] = 'Maitrise du bouclier';
$Gtrad['survie'] = 'Survie';
$Gtrad['survie_humanoide'] = 'Connaissance des humanoïdes';
$Gtrad['survie_magique'] = 'Connaissance des créatures magiques';
$Gtrad['survie_bete'] = 'Connaissance des bêtes';
$Gtrad['sort_groupe'] = 'Sorts de groupe';
$Gtrad['sort_groupe_sort_mort'] = 'Sorts de nécromancie de groupe';
$Gtrad['sort_groupe_sort_element'] = 'Sorts élémentaires de groupe';
$Gtrad['craft'] = 'Fabrication d\'objets';
$Gtrad['artisanat'] = 'Artisanat';
$Gtrad['forge'] = 'Forge';
$Gtrad['architecture'] = 'Architecture';
$Gtrad['alchimie'] = 'Alchimie';
$Gtrad['survie'] = 'Survie';
$Gtrad['identification'] = 'Identification d\'objets';
$Gtrad['sort_vie+'] = 'Apprend magie de la vie a';
$Gtrad['facteur_magie'] = 'Réduction PA des sorts';
$Gtrad['dressage'] = 'Dressage';
$Gtrad['max_pet'] = 'Nombre de créatures';
//@}


/**
 * @name Général
 * Traductions générales
 * @{  
 */
$Gtrad['classe'] = 'Classe';
$Gtrad['honneur'] = 'Honneur';
$Gtrad['level'] = 'Niveau';
$Gtrad['reputation'] = 'Réputation';
//@}


/**
 * @name Diplomatie
 * Traduction de la diplomatie.
 * @{  
 */
$Gtrad['diplo0'] = 'Alliance fraternelle';
$Gtrad['diplo1'] = 'Alliance';
$Gtrad['diplo2'] = 'Paix durable';
$Gtrad['diplo3'] = 'Paix';
$Gtrad['diplo4'] = 'En bons termes';
$Gtrad['diplo5'] = 'Neutre';
$Gtrad['diplo6'] = 'Mauvais termes';
$Gtrad['diplo7'] = 'Guerre';
$Gtrad['diplo8'] = 'Guerre durable';
$Gtrad['diplo9'] = 'Ennemis';
$Gtrad['diplo10'] = 'Ennemis eternels';
$Gtrad['diplo127'] = 'Votre royaume';
//@}


/**
 * @name Affinités
 * Traduction des affinités.
 * @{  
 */
$Gtrad['affinite1'] = 'Execrable';
$Gtrad['affinite2'] = 'Très mauvaise';
$Gtrad['affinite3'] = 'Mauvaise';
$Gtrad['affinite4'] = 'Moyenne';
$Gtrad['affinite5'] = 'Bonne';
$Gtrad['affinite6'] = 'Très bonne';
$Gtrad['affinite7'] = 'Superbe';
//@}


/**
 * @name Cibles
 * Traduction des cibles.
 * @{  
 */
$Gtrad['cible1'] = 'personnel';
$Gtrad['cible2'] = 'un personnage';
$Gtrad['cible3'] = 'votre groupe';
$Gtrad['cible4'] = 'un autre personnage';
$Gtrad['cible5'] = 'un autre groupe';
$Gtrad['cible6'] = 'les personnages sur votre case';
$Gtrad['cible7'] = 'les bâtiments sur votre case';
$Gtrad['cible8'] = 'les personnages autour de vous';
$Gtrad['cible_ex1'] = 'vous';
$Gtrad['cible_ex2'] = '';
$Gtrad['cible_ex3'] = 'votre groupe';
$Gtrad['cible_ex4'] = '';
$Gtrad['cible_ex5'] = 'le groupe de ';
$Gtrad['cible_ex6'] = 'les personnages de votre cases';
$Gtrad['cible_ex7'] = 'les bâtiments sur votre case';
$Gtrad['cible_ex8'] = 'les personnages autour de vous';
//@}


/**
 * @name Objets royaume
 * Traduction des objets liés au royaume.
 * @{
 */
$Gtrad['arme_de_siege'] = 'Armes de siège';
$Gtrad['bourg'] = 'Bourgades';
$Gtrad['drapeau'] = 'Drapeaux';
$Gtrad['fort'] = 'Forts';
$Gtrad['mur'] = 'Murs';
$Gtrad['tour'] = 'Tours';
//@}


/**
 * @name Ressources
 * Traduction des ressources.
 * @{
 */
$Gtrad['pierre'] = 'Pierre';
$Gtrad['bois'] = 'Bois';
$Gtrad['eau'] = 'Eau';
$Gtrad['sable'] = 'Sable';
$Gtrad['food'] = 'Nourriture';
$Gtrad['charbon'] = 'Charbon';
$Gtrad['essence'] = 'Essence Magique';
//@}


/**
 * @name Classes
 * Traduction des classes.
 * @{  
 */
$GPluriels = array();
$GPluriels['combattant'] = 'combattants';
$GPluriels['guerrier'] = 'guerriers';
$GPluriels['archer'] = 'archers';
$GPluriels['voleur'] = 'voleurs';
$GPluriels['paladin'] = 'paladins';
$GPluriels['champion'] = 'champions';
$GPluriels['assassin'] = 'assassins';
$GPluriels['archer d élite'] = 'archers d élite';
$GPluriels['paladin+'] = 'paladins+';
$GPluriels['champion+'] = 'champions+';
$GPluriels['assassin+'] = 'assassins+';
$GPluriels['archer d élite+'] = 'archers d élite+';
$GPluriels['mage'] = 'mages';
$GPluriels['sorcier'] = 'sorciers';
$GPluriels['nécromancien'] = 'nécromanciens';
$GPluriels['clerc'] = 'clercs';
$GPluriels['grand sorcier'] = 'grands sorciers';
$GPluriels['grand nécromancien'] = 'grands nécromanciens';
$GPluriels['prêtre'] = 'prêtres';
$GPluriels['sage'] = 'sages';
$GPluriels['elémentaliste'] = 'elémentalistes';
$GPluriels['pestimancien'] = 'pestimanciens';
//@}

function pluriel($mot) {
  global $GPluriels;
  if (isset($GPluriels[$mot]))
    return $GPluriels[$mot];
  return $mot;
}

function traduit($mot) {
  global $Gtrad;
  if (isset($Gtrad[$mot]))
    return $Gtrad[$mot];
  return $mot;
}
?>