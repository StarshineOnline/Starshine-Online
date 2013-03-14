<?php /* -*- mode: php -*- */
  /**
   * @file variable.inc.php
   * Définitions des constantes.
   */  

define('BASE', 'http://www.starshine-online.com/');

  /// Version actuelle
	$G_version = '0.7 beta';
	/// Révision actuelle
	$G_revision = 124;

  $G_mailto = 'masterob1@free.fr';

	$G_ligne = 1000;
	$G_colonne = 1000;
	$G_ligne_donjon = 1000;
	$G_colonne_donjon = 1000;
  
  /// Temps entre chaque régénération de PA (en secondes)
	$G_temps_PA = 10 * 60;
	/// Nombre maximal de PA
	$G_PA_max = 180;
	//$G_bonus_maj_pa = 1;
  /// Temps entre chaque augmentation des HP (en secondes)
	$G_temps_maj_hp = 7 * 24 * 60 * 60;
  /// Temps entre chaque augmentation des MP (en secondes)
	$G_temps_maj_mp = 7 * 24 * 60 * 60;
  /// Temps entre chaque régénération de HP et MP (en secondes)
	$G_temps_regen_hp = 8 * 60 * 60;
	/// Pourcentage de HP récupérés à chaque régénération
	$G_pourcent_regen_hp = 0.1;
	/// Pourcentage de MP récupérés à chaque régénération
	$G_pourcent_regen_mp = 0.35;
	//$G_bonus_maj_hp = 60 * 60;
	//$G_bonus_maj_mp = 60 * 60;

	$G_exp_victoire = 1000;
	$G_exp_defense = 2;
	$G_exp_defaite = 10;
	$G_exp_defaitemax = 350;
	$G_honneur_victoire = 10;
	$G_honneur_defense = 2;
	$G_honneur_defaite = 1;
	$G_honneur_defaitemax = 4;
	$G_range_level = 5;
	
	$G_level_xp = 8000;
	
	$G_taux_vente = 2.5;
	
	$G_nb_joueur_groupe	= 4;

	$G_facteur_vie = 8;
	$G_facteur_mana = 10;

	$G_debug = false;
	/// Indique si le jeu est en maintenance (et donc inaccessible)
	$G_maintenance = false;
	
	$G_erreur = '';
	
	/// Nombre de PA pour attaquer un monstre
	$G_PA_attaque_monstre = 7;
	/// Nombre de PA pour attaquer un joueur
	$G_PA_attaque_joueur = 12;
	/// Nombre de PA pour attaquer un bâtiment
	$G_PA_attaque_batiment = 10;
	/// Nombre de round lors d'un combat
	$G_round_total = 10;
	/// Nombre de p
	$G_place_inventaire = 20;
	/// Taux d'apparition des monstres. Plus le spawn rate est élevé, plus les mobs popent
	$G_spawn_rate = 19;
	$G_apprentissage_rate = 0.7;
	$G_drop_rate = 1;
	$G_xp_rate = 1;

  /// Facteur max des PV max
  $G_max_hp_max_factor = 120;
  /// Facteur max des PV max
  $G_max_mp_max_factor = 13;
	
	/**
	 * @name 	Cibles des sorts
	 * 
	 */
	//@{
	/// Cibles des sorts
	$G_cibles = array();
	$G_cibles[1] = 'Self';
	$G_cibles[2] = 'Ami';
	$G_cibles[4] = 'Ennemi';
	$G_cibles[5] = 'Groupe';
	$G_cibles[8] = 'Groupe';
	//@}
	
	//Buffs
	$G_buff['evasion'] = 10;
	$G_buff['critique'] = 20;
	$G_buff['bouclier'] = 10;
	$G_buff['barriere'] = 10;
	$G_debuff['accuracy'] = 20;
	$G_debuff['desespoir'] = 10;
	$G_buff['force'] = 1;
	$G_buff['coup_sournois'] = 50;
	$G_buff['bene_evasion'] = 5;
	$G_buff['bene_critique'] = 10;
	$G_buff['bene_bouclier'] = 5;
	$G_buff['bene_accuracy'] = 5;
	$G_buff['bene_degat'] = 1;
	$G_buff['berz_evasion'] = 5;
	$G_buff['berz_critique'] = 10;
	$G_buff['berz_bouclier'] = 5;
	$G_buff['berz_accuracy'] = 5;
	$G_buff['berz_degat'] = 1;
	$G_buff['berz_degat_recu'] = 1;
	$G_buff['vise_critique'] = 15;
	$G_buff['vise_accuracy'] = 20;
	$G_buff['vise_degat'] = 6;
	$G_buff['posture_degat'] = 1;
	$G_buff['posture_critique'] = 20;
	$G_buff['posture_esquive'] = 10;
	$G_buff['posture_defense'] = 1;
	$G_buff['posture_transperce'] = 10;
	$G_buff['posture_paralyse'] = 35;
	$G_buff['posture_touche'] = 10;

	$G_sort['body_to_mind'] = 1.5;
	
	$Gcouleurs = array();
	$Gcouleurs[0] = "#aaaaaa";
	$Gcouleurs[1] = "#0068ff";
	$Gcouleurs[2] = "#009900";
	$Gcouleurs[3] = "#ff0000";
	$Gcouleurs[4] = "#ffff00";
	$Gcouleurs[5] = "#0000ff";
	$Gcouleurs[6] = "#ffcccc";
	$Gcouleurs[7] = "#ffa500";
	$Gcouleurs[8] = "#5c1e00";
	$Gcouleurs[9] = "#000000";
	$Gcouleurs[10] = "#0000ff";
	$Gcouleurs[11] = "#ffffff";
	$Gcouleurs[12] = "#7F2257";
	
	$G_consider[5] = '#ff0000';
	$G_consider[4] = '#ff5900';
	$G_consider[3] = '#ff5900';
	$G_consider[2] = '#ffa500';
	$G_consider[1] = '#ffa500';
	$G_consider[0] = '#000';
	$G_consider[-1] = '#00b70c';
	$G_consider[-2] = '#00b70c';
	$G_consider[-3] = '#0072ff';
	$G_consider[-4] = '#0072ff';
	$G_consider[-5] = '#0000ff';
	
	$G_crime[127] = 11;
	$G_crime[0] = 8;
	$G_crime[1] = 6;
	$G_crime[2] = 4;
	$G_crime[3] = 2;
	$G_crime[4] = 1;

	$G_undead_checked = false;

    $G_autorisations = array('officiers' => array(1,2,3,6,9,10),
				 'capitaines' => array(1,2,3,6,10),
				 'commandants' => array(1,2,6,10),
				 'colonnels' => array(1,6,10),
				 'généraux' => array(1,6),
				 'militaires' => array(1,2,3,5,6,8,9,10));
				 
	/**
	 * @name 	Temps entre les actions sur les batiments de la ville	 
	 */
	//@{
	/// Temps entre une amélioration et la dernière action (en secondes)
	$G_tps_amelioration = 30 * 24 * 60 * 60;
	/// Temps entre une réduction et la dernière action (en secondes)
	$G_tps_reduction = 7 * 24 * 60 * 60;
	/// Temps entre une réparation et la dernière action (en secondes)
	$G_tps_reparation = 3 * 24 * 60 * 60;
	//@}
	
	/// Vie minimale que doit avoir un bâtiment "hors-ville" pour pouvoir être supprimé par ses propriétaires(en pourcent)
	$G_prct_vie_suppression = .1;
				 
				 
  /// id d'utilisateur du forum à utiliser pour les posts automatiques
  $G_id_forum = 2;

  /// utiliser les calques
  $G_use_atmosphere = true;

?>
