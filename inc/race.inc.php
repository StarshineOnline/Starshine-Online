<?php
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * @file race.inc.php
 * Description des races. 
 * Pour les affinités les valeurs sont :
 * - 1 : excécrable
 * - 2 : très mauvaise
 * - 3 : mauvaise
 * - 4 : moyenne
 * - 5 : bonne
 * - 6 : très bonne
 * - 7 : superbe        
 */

/**
 * @name Barbares
 * Caractéristiques des Barbares
 */
 /// @{
$Trace['barbare']['vie'] = 16;  ///< Constitution des Barbares.
$Trace['barbare']['force'] = 18;   ///<  Force des Barbares.
$Trace['barbare']['dexterite'] = 11;  ///< Dextérité des Barbares.
$Trace['barbare']['puissance'] = 10;  ///< Puissance des Barbares.
$Trace['barbare']['volonte'] = 10;  ///< Volonté des Barbares.
$Trace['barbare']['energie'] = 11;  ///< Energie des Barbares.
$Trace['barbare']['passif'] = '+30% de Protection physique et +10 de base<br /> +40% de dégâtss physiques sur les batiments.<br />+10% de dégâtss avec les armes de siège.';  ///< Description des bonus des Barbares.

$Trace['barbare']['affinite_sort_mort'] = 4;  ///< Affinité avec la magie de la mort des Barbares.
$Trace['barbare']['affinite_sort_element'] = 4;  ///< Afinité avec la magie élémentaire des Barbares.
$Trace['barbare']['affinite_sort_vie'] = 4;  ///< Affinité avec la magie de la vie des Barbares.
$Trace['barbare']['numrace'] = 1;  ///< Numéro des Barbares. 
$Trace['barbare']['spawn_x'] = 79;
$Trace['barbare']['spawn_y'] = 137;
$Trace['barbare']['spawn_tutocx'] = 230;
$Trace['barbare']['spawn_tutocy'] = 55;
$Trace['barbare']['spawn_tutomx'] = 230;
$Trace['barbare']['spawn_tutomy'] = 55;
$Trace['barbare']['spawn_c_x'] = 63;
$Trace['barbare']['spawn_c_y'] = 140;
$Trace['barbare']['couleur'] = "#0068ff";  ///< Couleur des Barbares.
$Trace['barbare']['forum_id'] = 20;   ///< ID du forum des Barbares.
//@}


/**
 * @name Elfes des Bois
 * Caractéristiques des Elfes des Bois.
 */
 /// @{
$Trace['elfebois']['vie'] = 13;  ///< Constitution des Elfes des Bois.
$Trace['elfebois']['force'] = 11;   ///<  Force des Elfes des Bois.
$Trace['elfebois']['dexterite'] = 17;  ///< Dextérité des Elfes des Bois.
$Trace['elfebois']['puissance'] = 10;  ///< Puissance des Elfes des Bois.
$Trace['elfebois']['volonte'] = 11;  ///< Volonté des Elfes des Bois.
$Trace['elfebois']['energie'] = 14;  ///< Energie des Elfes des Bois.
$Trace['elfebois']['passif'] = '+10% en esquive<br /> +15% de chance de faire un coup critique physique';  ///< Description des bonus des Elfes des Bois.

$Trace['elfebois']['affinite_sort_mort'] = 1;  ///< Affinité avec la magie de la mort des Elfes des Bois.
$Trace['elfebois']['affinite_sort_element'] = 4;  ///< Affinité avec la magie élémentaire des Elfes des Bois.
$Trace['elfebois']['affinite_sort_vie'] = 7;  ///< Affinité avec la magie de la vie des Elfes des Bois.
$Trace['elfebois']['numrace'] = 2;  ///< Numéro des Elfes des Bois.
$Trace['elfebois']['spawn_x'] = 44;
$Trace['elfebois']['spawn_y'] = 90;
$Trace['elfebois']['spawn_tutocx'] = 203;
$Trace['elfebois']['spawn_tutocy'] = 97;
$Trace['elfebois']['spawn_tutomx'] = 203;
$Trace['elfebois']['spawn_tutomy'] = 97;
$Trace['elfebois']['spawn_c_x'] = 49;
$Trace['elfebois']['spawn_c_y'] = 79;
$Trace['elfebois']['couleur'] = "#009900";  ///< Couleur des Elfes des Bois.
$Trace['elfebois']['forum_id'] = 22;   ///< ID du forum des Elfes des Bois.
//@}


/**
 * @name Hauts Elfes
 * Caractéristiques des Hauts Elfes.
 */
 /// @{
$Trace['elfehaut']['vie'] = 11;  ///< Constitution des Hauts Elfes.
$Trace['elfehaut']['force'] = 10;   ///<  Force des Hauts Elfes.
$Trace['elfehaut']['dexterite'] = 12;  ///< Dextérité des Hauts Elfes.
$Trace['elfehaut']['puissance'] = 13;  ///< Puissance des Hauts Elfes.
$Trace['elfehaut']['volonte'] = 18;  ///< Volonté des Hauts Elfes.
$Trace['elfehaut']['energie'] = 12;  ///< Energie des Hauts Elfes.
$Trace['elfehaut']['passif'] = '+10% regen MP<br /> +1 Dex, +1 Volonté, +2 Réserve de mana, la nuit';  ///< Description des bonus des Hauts Elfes. 

$Trace['elfehaut']['affinite_sort_mort'] = 1;  ///< Affinité avec la magie de la mort des Hauts Elfes.
$Trace['elfehaut']['affinite_sort_element'] = 7;  ///< Affinité avec la magie élémentaire des Hauts Elfes.
$Trace['elfehaut']['affinite_sort_vie'] = 4;  ///< Affinité avec la magie de la vie des Hauts Elfes.
$Trace['elfehaut']['numrace'] = 11;  ///< Numéro des Hauts Elfes.
$Trace['elfehaut']['spawn_x'] = 72;
$Trace['elfehaut']['spawn_y'] = 12;
$Trace['elfehaut']['spawn_tutocx'] = 200;
$Trace['elfehaut']['spawn_tutocy'] = 111;
$Trace['elfehaut']['spawn_tutomx'] = 200;
$Trace['elfehaut']['spawn_tutomy'] = 111;
$Trace['elfehaut']['spawn_c_x'] = 66;
$Trace['elfehaut']['spawn_c_y'] = 1;
$Trace['elfehaut']['couleur'] = "#ffffff";  ///< Couleur des Hauts Elfes.
$Trace['elfehaut']['forum_id'] = 23;   ///< ID du forum des Hauts Elfes.
//@}


/**
 * @name Humains
 * Caractéristiques des Humains.
 */
 /// @{
$Trace['humain']['vie'] = 13;  ///< Constitution des Humains.
$Trace['humain']['force'] = 12;   ///<  Force des Humains.
$Trace['humain']['dexterite'] = 13;  ///< Dextérité des Humains.
$Trace['humain']['puissance'] = 12;  ///< Puissance des Humains.
$Trace['humain']['volonte'] = 13;  ///< Volonté des Humains.
$Trace['humain']['energie'] = 13;  ///< Energie des Humains.
$Trace['humain']['passif'] = '+10% en apprentissage des compétences<br /> +30% de chance de droper des objets sur les monstres';  ///< Description des bonus desHumains. 

$Trace['humain']['affinite_sort_mort'] = 3;  ///< Affinité avec la magie de la mort des Humains.
$Trace['humain']['affinite_sort_element'] = 5;  ///< Affinité avec la magie élémentaire des Humains.
$Trace['humain']['affinite_sort_vie'] = 4;  ///< Affinité avec la magie de la vie des Humains.
$Trace['humain']['numrace'] = 10;  ///< Numéro des Humains.
$Trace['humain']['spawn_x'] = 26;
$Trace['humain']['spawn_y'] = 137;
$Trace['humain']['spawn_tutocx'] = 249;
$Trace['humain']['spawn_tutocy'] = 56;
$Trace['humain']['spawn_tutomx'] = 259;
$Trace['humain']['spawn_tutomy'] = 156;
$Trace['humain']['spawn_c_x'] = 140;
$Trace['humain']['spawn_c_y'] = 9;
$Trace['humain']['couleur'] = "#0000ff";  ///< Couleur des Humains.
$Trace['humain']['forum_id'] = 26;   ///< ID du forum des Humains.
//@}


/**
 * @name Corrompus
 * Caractéristiques des Corrompus.
 */
 /// @{
$Trace['humainnoir']['vie'] = 12;  ///< Constitution des Corrompus.
$Trace['humainnoir']['force'] = 13;   ///<  Force des Corrompus.
$Trace['humainnoir']['dexterite'] = 12;  ///< Dextérité des Corrompus.
$Trace['humainnoir']['puissance'] = 13;  ///< Puissance des Corrompus.
$Trace['humainnoir']['volonte'] = 12;  ///< Volonté des Corrompus.
$Trace['humainnoir']['energie'] = 14;  ///< Energie des Corrompus.
$Trace['humainnoir']['passif'] = '+10% en apprentissage des compétences<br /> +10% de chance de toucher physiquement le jour et le matin, +10% de chance de toucher magiquement la nuit';  ///< Description des bonus des Corrompus.

$Trace['humainnoir']['affinite_sort_mort'] = 4;  ///< Affinité avec la magie de la mort des Corrompus.
$Trace['humainnoir']['affinite_sort_element'] = 5;  ///< Affinité avec la magie élémentaire des Corrompus.
$Trace['humainnoir']['affinite_sort_vie'] = 3;  ///< Affinité avec la magie de la vie des Corrompus.
$Trace['humainnoir']['numrace'] = 9;  ///< Numéro des Corrompus.
$Trace['humainnoir']['spawn_x'] = 103;
$Trace['humainnoir']['spawn_y'] = 97;
$Trace['humainnoir']['spawn_tutocx'] = 244;
$Trace['humainnoir']['spawn_tutocy'] = 174;
$Trace['humainnoir']['spawn_tutomx'] = 244;
$Trace['humainnoir']['spawn_tutomy'] = 174;
$Trace['humainnoir']['spawn_c_x'] = 95;
$Trace['humainnoir']['spawn_c_y'] = 90;
$Trace['humainnoir']['couleur'] = "#000000";  ///< Couleur des Corrompus.
$Trace['humainnoir']['forum_id'] = 27;   ///< ID du forum des Corrompus.
//@}


/**
 * @name Morts-Vivants
 * Caractéristiques des Morts-Vivants.
 */
 /// @{
$Trace['mortvivant']['vie'] = 13;  ///< Constitution des Morts-Vivants.
$Trace['mortvivant']['force'] = 12;   ///<  Force des Morts-Vivants.
$Trace['mortvivant']['dexterite'] = 11;  ///< Dextérité des Morts-Vivants.
$Trace['mortvivant']['puissance'] = 13;  ///< Puissance des Morts-Vivants.
$Trace['mortvivant']['volonte'] = 13;  ///< Volonté des Morts-Vivants.
$Trace['mortvivant']['energie'] = 14;  ///< Energie des Morts-Vivants.
$Trace['mortvivant']['passif'] = '+10% HP / MP lorsqu\'ils ressucitent<br /> +30% Protection physique et +15% magique le matin et soir';  ///< Description des bonus des Morts-Vivants.

$Trace['mortvivant']['affinite_sort_mort'] = 7;  ///< Affinité avec la magie de la mort des Morts-Vivants.
$Trace['mortvivant']['affinite_sort_element'] = 4;  ///< Affinité avec la magie élémentaire des Morts-Vivants.
$Trace['mortvivant']['affinite_sort_vie'] = 1;  ///< Affinité avec la magie de la vie des Morts-Vivants.
$Trace['mortvivant']['numrace'] = 8;  ///< Numéro des Morts-Vivants.
$Trace['mortvivant']['spawn_x'] = 11;
$Trace['mortvivant']['spawn_y'] = 68;
$Trace['mortvivant']['spawn_tutocx'] = 203;
$Trace['mortvivant']['spawn_tutocy'] = 77;
$Trace['mortvivant']['spawn_tutomx'] = 203;
$Trace['mortvivant']['spawn_tutomy'] = 77;
$Trace['mortvivant']['spawn_c_x'] = 57;
$Trace['mortvivant']['spawn_c_y'] = 3;
$Trace['mortvivant']['couleur'] = "#5c1e00";  ///< Couleur des Morts-Vivants.
$Trace['mortvivant']['forum_id'] = 30;   ///< ID du forum des Morts-Vivants.
//@}


/**
 * @name Nains
 * Caractéristiques des Nains.
 */
 /// @{
$Trace['nain']['vie'] = 18;  ///< Constitution des Nains.
$Trace['nain']['force'] = 17;   ///<  Force des Nains.
$Trace['nain']['dexterite'] = 13;  ///< Dextérité des Nains.
$Trace['nain']['puissance'] = 7;  ///< Puissance des Nains.
$Trace['nain']['volonte'] = 13;  ///< Volonté des Nains.
$Trace['nain']['energie'] = 9;  ///< Energie des Nains.
$Trace['nain']['passif'] = '+10% de Protection magique et +10 de base<br /> +10% d\'or ramassé sur les monstres et +5% sur les quêtes';  ///< Description des bonus des Nains.

$Trace['nain']['affinite_sort_mort'] = 1;  ///< Affinité avec la magie de la mort des Nains.
$Trace['nain']['affinite_sort_element'] = 3;  ///< Affinité avec la magie élémentaire des Nains.
$Trace['nain']['affinite_sort_vie'] = 6;  ///< Affinité avec la magie de la vie des Nains.
$Trace['nain']['numrace'] = 7;  ///< Numéro des Nains.
$Trace['nain']['spawn_x'] = 16;
$Trace['nain']['spawn_y'] = 12;
$Trace['nain']['spawn_tutocx'] = 215;
$Trace['nain']['spawn_tutocy'] = 152;
$Trace['nain']['spawn_tutomx'] = 232;
$Trace['nain']['spawn_tutomy'] = 159;
$Trace['nain']['spawn_c_x'] = 5;
$Trace['nain']['spawn_c_y'] = 6;
$Trace['nain']['couleur'] = "#ffa500";  ///< Couleur des Nains.
$Trace['nain']['forum_id'] = 32;   ///< ID du forum des Nains.
//@}


/**
 * @name Orcs
 * Caractéristiques des Orcs.
 */
 /// @{
$Trace['orc']['vie'] = 15;  ///< Constitution des Orcs.
$Trace['orc']['force'] = 18;   ///<  Force des Orcs.
$Trace['orc']['dexterite'] = 14;  ///< Dextérité des Orcs.
$Trace['orc']['puissance'] = 10;  ///< Puissance des Orcs.
$Trace['orc']['volonte'] = 10;  ///< Volonté des Orcs.
$Trace['orc']['energie'] = 10;  ///< Energie des Orcs.
$Trace['orc']['passif'] = 'Chaque combat dure un round de plus<br /> +5% de dégâts sur les critiques physiques';  ///< Description des bonus des Orcs.

$Trace['orc']['affinite_sort_mort'] = 3;  ///< Affinité avec la magie de la mort des Orcs.
$Trace['orc']['affinite_sort_element'] = 5;  ///< Affinité avec la magie élémentaire des Orcs.
$Trace['orc']['affinite_sort_vie'] = 2;  ///< Affinité avec la magie de la vie des Orcs.
$Trace['orc']['numrace'] = 6;  ///< Numéro des Orcs.
$Trace['orc']['spawn_x'] = 121;
$Trace['orc']['spawn_y'] = 56;
$Trace['orc']['spawn_tutocx'] = 260;
$Trace['orc']['spawn_tutocy'] = 75;
$Trace['orc']['spawn_tutomx'] = 271;
$Trace['orc']['spawn_tutomy'] = 74;
$Trace['orc']['spawn_c_x'] = 135;
$Trace['orc']['spawn_c_y'] = 55;
$Trace['orc']['couleur'] = "#ffcccc";  ///< Couleur des Orcs.
$Trace['orc']['forum_id'] = 33;   ///< ID du forum des Scavengers.
//@}


/**
 * @name Scavengers
 * Caractéristiques des Scavengers.
 */
 /// @{
$Trace['scavenger']['vie'] = 13;  ///< Constitution des Scavengers.
$Trace['scavenger']['force'] = 11;   ///<  Force des Scavengers.
$Trace['scavenger']['dexterite'] = 16;  ///< Dextérité des Scavengers.
$Trace['scavenger']['puissance'] = 12;  ///< Puissance des Scavengers.
$Trace['scavenger']['volonte'] = 12;  ///< Volonté des Scavengers.
$Trace['scavenger']['energie'] = 12;  ///< Energie des Scavengers.
$Trace['scavenger']['passif'] = '+15% Protection physique et +5% protection magique<br /> +40% de chance de réussite en forge et alchimie, +20% en architecture<br />+20% pour soigner ses créatures.';  ///< Description des bonus des Scavengers.

$Trace['scavenger']['affinite_sort_mort'] = 4;  ///< Affinité avec la magie de la mort des Scavengers.
$Trace['scavenger']['affinite_sort_element'] = 4;  ///< Affinité avec la magie élémentaire des Scavengers.
$Trace['scavenger']['affinite_sort_vie'] = 4;  ///< Affinité avec la magie de la vie des Scavengers.
$Trace['scavenger']['numrace'] = 4;  ///< Numéro des Scavengers.
$Trace['scavenger']['spawn_x'] = 141;
$Trace['scavenger']['spawn_y'] = 22;
$Trace['scavenger']['spawn_tutocx'] = 260;
$Trace['scavenger']['spawn_tutocy'] = 88;
$Trace['scavenger']['spawn_tutomx'] = 260;
$Trace['scavenger']['spawn_tutomy'] = 88;
$Trace['scavenger']['spawn_c_x'] = 145;
$Trace['scavenger']['spawn_c_y'] = 10;
$Trace['scavenger']['couleur'] = "#ffff00";  ///< Couleur des Scavengers.
$Trace['scavenger']['forum_id'] = 36;   ///< ID du forum des Scavengers.
//@}


/**
 * @name Trolls
 * Caractéristiques des Trolls.
 */
 /// @{
$Trace['troll']['vie'] = 17;  ///< Constitution des Trolls.
$Trace['troll']['force'] = 19;   ///<  Force des Trolls.
$Trace['troll']['dexterite'] = 11;  ///< Dextérité des Trolls.
$Trace['troll']['puissance'] = 10;  ///< Puissance des Trolls.
$Trace['troll']['volonte'] = 9;  ///< Volonté des Trolls.
$Trace['troll']['energie'] = 11;  ///< Energie des Trolls.
$Trace['troll']['passif'] = '+20% de regen HP<br /> Réduction de 20% des coups critiques';  ///< Description des bonus des Trolls.

$Trace['troll']['affinite_sort_mort'] = 2;  ///< Affinité avec la magie de la mort des Trolls.
$Trace['troll']['affinite_sort_element'] = 4;  ///< Affinité avec la magie élémentaire des Trolls.
$Trace['troll']['affinite_sort_vie'] = 4;  ///< Affinité avec la magie de la vie des Trolls.
$Trace['troll']['numrace'] = 3;  ///< Numéro des Trolls.
$Trace['troll']['spawn_x'] = 62;
$Trace['troll']['spawn_y'] = 48;
$Trace['troll']['spawn_tutocx'] = 218;
$Trace['troll']['spawn_tutocy'] = 177;
$Trace['troll']['spawn_tutomx'] = 230;
$Trace['troll']['spawn_tutomy'] = 178;
$Trace['troll']['spawn_c_x'] = 54;
$Trace['troll']['spawn_c_y'] = 30;
$Trace['troll']['couleur'] = "#ff0000";  ///< Couleur des Trolls.
$Trace['troll']['forum_id'] = 37;   ///< ID du forum des Trolls.
//@}


/**
 * @name Vampires
 * Caractéristiques des Vampires.
 */
 /// @{
$Trace['vampire']['vie'] = 13;  ///< Constitution des Vampires.
$Trace['vampire']['force'] = 11;   ///<  Force des Vampires.
$Trace['vampire']['dexterite'] = 13;  ///< Dextérité des Vampires.
$Trace['vampire']['puissance'] = 16;  ///< Puissance des Vampires.
$Trace['vampire']['volonte'] = 11;  ///< Volonté des Vampires.
$Trace['vampire']['energie'] = 11;  ///< Energie des Vampires.
$Trace['vampire']['passif'] = '+2 en réserve de mana<br /> +2 Dex, +2 Volonté, +2 Réserve Mana la nuit<br /> -1 Dex, -1 Volonté, -1 Réserve de mana le jour';  ///< Description des bonus des

$Trace['vampire']['affinite_sort_mort'] = 6;  ///< Affinité avec la magie de la mort des Vampires.
$Trace['vampire']['affinite_sort_element'] = 6;  ///< Affinité avec la magie élémentaire des Vampires.
$Trace['vampire']['affinite_sort_vie'] = 3;  ///< Affinité avec la magie de la vie des Vampires.
$Trace['vampire']['numrace'] = 12;  ///< Numéro des Vampires.
$Trace['vampire']['spawn_x'] = 135;
$Trace['vampire']['spawn_y'] = 133;
$Trace['vampire']['spawn_tutocx'] = 213;
$Trace['vampire']['spawn_tutocy'] = 144;
$Trace['vampire']['spawn_tutomx'] = 213;
$Trace['vampire']['spawn_tutomy'] = 144;
$Trace['vampire']['spawn_c_x'] = 135;
$Trace['vampire']['spawn_c_y'] = 133;
$Trace['vampire']['couleur'] = "#cccccc";  ///< Couleur des Vampires.
$Trace['vampire']['forum_id'] = 40;   ///< ID du forum des Vampires.
//@}
?>
