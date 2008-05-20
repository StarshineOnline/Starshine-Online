INSERT INTO `sort_jeu` (`id`, `nom`, `description`, `pa`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `duree`, `cible`, `requis`, `prix`, `lvl_batiment`) VALUES (1, '(Self) Soin mineur', 'Soigne la cible (puissance %effet%)', 6, 5, 'vie', 'sort_vie', 'energie', 10, 0, 1, '7', 0, 1, '', 5, 1),
(2, 'Soin mineur', 'Soigne la cible (puissance %effet%)', 6, 5, 'vie', 'sort_vie', 'energie', 11, 10, 5, '5', 0, 2, '1', 10, 1),
(3, '(Self) Soin léger', 'Soigne la cible (puissance %effet%)', 6, 11, 'vie', 'sort_vie', 'energie', 10, 30, 15, '8', 0, 1, '1', 100, 1),
(4, '(Self) fast soin mineur', 'Soigne rapidement la cible (puissance %effet%)', 2, 10, 'vie', 'sort_vie', 'energie', 10, 70, 35, '7', 0, 1, '1', 250, 1),
(5, 'Soin léger', 'Soigne la cible (puissance %effet%)', 6, 11, 'vie', 'sort_vie', 'energie', 11, 70, 35, '6', 0, 2, '', 120, 1),
(6, 'Fast soin mineur', 'Soigne rapidement la cible (puissance %effet%)', 2, 10, 'vie', 'sort_vie', 'energie', 13, 90, 45, '5', 0, 2, '', 310, 1),
(7, '(Self) soin', 'Soigne la cible (puissance %effet%)', 6, 16, 'vie', 'sort_vie', 'energie', 10, 70, 35, '12', 0, 1, '', 400, 1),
(8, 'Soin', 'Soigne la cible (puissance %effet%)', 6, 16, 'vie', 'sort_vie', 'energie', 11, 100, 50, '10', 0, 2, '', 430, 2),
(9, '(Selft) Focus', 'Augmente les chances de critique (+%effet%%)', 4, 8, 'buff_critique', 'sort_element', 'puissance', 10, 0, 1, '20', 86400, 1, '', 10, 1),
(10, 'Focus', 'Augmente les chances de critique (+%effet%%)', 4, 16, 'buff_critique', 'sort_element', 'puissance', 11, 30, 15, '20', 86400, 2, '9', 100, 1),
(11, '(Self) Evasion', 'Augmente l''esquive (+%effet%%)', 4, 8, 'buff_evasion', 'sort_vie', 'energie', 10, 0, 1, '10', 86400, 1, '', 10, 1),
(12, 'Evasion', 'Augmente l''esquive (+%effet%%)', 4, 16, 'buff_evasion', 'sort_vie', 'energie', 11, 30, 15, '10', 86400, 2, '11', 100, 1),
(13, '(Self) Bouclier', 'Augmente l''armure (+%effet%%)', 4, 12, 'buff_bouclier', 'sort_vie', 'energie', 10, 10, 5, '10', 86400, 1, '11', 50, 1),
(15, 'Aveuglement', 'Réduit les chances de toucher de la cible (-%effet%%)', 4, 16, 'debuff_aveuglement', 'sort_mort', 'puissance', 12, 0, 1, '20', 3600, 4, '', 10, 1),
(14, 'Bouclier', 'Augmente l''armure (+%effet%%)', 4, 24, 'buff_bouclier', 'sort_vie', 'energie', 11, 60, 30, '10', 86400, 2, '11', 250, 1),
(16, 'Resurrection', 'Réssucite la cible avec %effet% % de HP et MP', 10, 40, 'rez', 'sort_vie', 'energie', 0, 100, 50, '25', 0, 2, '', 400, 1),
(17, 'Resurrection améliorée', 'Réssucite la cible avec %effet% % de HP et MP', 10, 48, 'rez', 'sort_vie', 'energie', 0, 240, 120, '35', 0, 2, '', 2000, 2),
(18, 'Inspiration', 'Augmente votre réserve de mana de %effet%.', 4, 24, 'buff_inspiration', 'sort_vie', 'energie', 11, 120, 60, '1', 86400, 2, '', 500, 2),
(19, 'Inspiration 2', 'Augmente votre réserve de mana de %effet%.', 4, 32, 'buff_inspiration', 'sort_vie', 'energie', 11, 240, 120, '2', 86400, 2, '18', 3000, 3),
(20, 'Inspiration 3', 'Augmente votre réserve de mana de %effet%.', 4, 40, 'buff_inspiration', 'sort_vie', 'energie', 11, 400, 200, '3', 86400, 2, '19', 6000, 3),
(27, 'Force', 'Augmente les dégats infligés physiquement de %effet%.', 4, 25, 'buff_force', 'sort_vie', 'energie', 0, 140, 70, '1', 86400, 2, '', 700, 2),
(21, 'Focus 2', 'Augmente les chances de critique (+%effet%%)', 4, 20, 'buff_critique', 'sort_element', 'puissance', 11, 80, 40, '40', 86400, 2, '10', 250, 1),
(22, 'Focus 3', 'Augmente les chances de critique (+%effet%%)', 4, 24, 'buff_critique', 'sort_element', 'puissance', 11, 160, 80, '60', 86400, 2, '21', 1000, 2),
(23, 'Evasion 2', 'Augmente l''esquive (+%effet%%)', 4, 20, 'buff_evasion', 'sort_vie', 'energie', 11, 80, 40, '20', 86400, 2, '12', 250, 1),
(24, 'Evasion 3', 'Augmente l''esquive (+%effet%%)', 4, 24, 'buff_evasion', 'sort_vie', 'energie', 11, 160, 80, '30', 86400, 2, '23', 1000, 2),
(25, 'Bouclier 2', 'Augmente l''armure (+%effet%%)', 4, 32, 'buff_bouclier', 'sort_vie', 'energie', 11, 120, 60, '2', 86400, 20, '14', 500, 2),
(26, 'Bouclier 3', 'Augmente l''armure (+%effet%%)', 4, 40, 'buff_bouclier', 'sort_vie', 'energie', 11, 240, 120, '30', 86400, 2, '25', 3000, 3),
(28, 'Force 2', 'Augmente les dégats infligés physiquement de %effet%.', 4, 34, 'buff_force', 'sort_vie', 'energie', 0, 270, 135, '2', 86400, 2, '', 3500, 3),
(29, 'Force 3', 'Augmente les dégats infligés physiquement %effet%.', 4, 45, 'buff_force', 'sort_vie', 'energie', 0, 420, 210, '3', 86400, 2, '', 7000, 3),
(30, 'Retour en ville', 'Vous permet de revenir à votre capitale.', 50, 50, 'teleport', 'sort_element', 'volonte', 0, 180, 90, '1', 0, 1, '', 1700, 2),
(40, 'Soin supérieur', 'Soigne la cible (puissance %effet%)', 4, 23, 'vie', 'sort_vie', 'energie', 11, 200, 100, '15', 0, 2, '8', 1600, 2),
(32, 'Barrière magique', 'Augmente votre défense magique (effet %effet%).', 4, 20, 'buff_barriere', 'sort_vie', 'volonte', 0, 70, 35, '1', 86400, 2, '', 200, 1),
(33, 'Barrière magique 2', 'Augmente votre défense magique (effet %effet%).', 4, 25, 'buff_barriere', 'sort_vie', 'volonte', 0, 140, 70, '2', 86400, 2, '', 800, 2),
(34, 'Désespoir', 'Réduit la défense magique de la cible (effet %effet%).', 4, 20, 'debuff_desespoir', 'sort_mort', 'puissance', 0, 70, 35, '1', 3600, 4, '', 200, 1),
(35, 'Désespoir 2', 'Réduit la défense magique de la cible (effet %effet%).', 4, 25, 'debuff_desespoir', 'sort_mort', 'puissance', 0, 140, 70, '2', 3600, 4, '', 800, 2),
(36, 'Du corps à l esprit', 'Vous perdez des point de vie, pour gagner du mana (effet %effet%).', 4, 0, 'body_to_mind', 'sort_mort', 'puissance', 0, 200, 100, '20', 0, 2, '', 2000, 2),
(37, 'Du corps à l esprit 2', 'Vous perdez des point de vie, pour gagner du mana (effet %effet%).', 4, 0, 'body_to_mind', 'sort_mort', 'puissance', 0, 300, 150, '30', 0, 2, '', 4000, 3),
(38, 'Aveuglement 2', 'Réduit les chances de toucher de la cible (effet %effet%).', 4, 20, 'debuff_aveuglement', 'sort_mort', 'puissance', 0, 200, 100, '2', 3600, 4, '15', 400, 1),
(39, 'Aveuglement 3', 'Réduit les chances de toucher de la cible (effet %effet%).', 4, 25, 'debuff_aveuglement', 'sort_mort', 'puissance', 0, 300, 150, '3', 3600, 4, '37', 4000, 3),
(41, 'Soin majeur', 'Soigne la cible (puissance %effet%)', 4, 32, 'vie', 'sort_vie', 'energie', 11, 300, 150, '21', 0, 2, '40', 4000, 3),
(42, 'Soin puissant', 'Soigne la cible (puissance %effet%)', 4, 43, 'vie', 'sort_vie', 'energie', 11, 400, 200, '28', 0, 2, '41', 7000, 3);
INSERT INTO `comp_combat` VALUES (1, 'Oeil du faucon', 'Vise avec précision la cible augmentant les chances de la toucher (+%effet%%).', 5, 'tir_precis', 'distance', 'dexterite', 0, 150, 'arc', '25', 4, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (2, 'Oeil du faucon 2', 'Vise avec précision la cible augmentant les chances de la toucher (+%effet%%).', 5, 'tir_precis', 'distance', 'dexterite', 0, 200, 'arc', '40', 4, '1', 1000, 1);
INSERT INTO `comp_combat` VALUES (3, 'Oeil du faucon 3', 'Vise avec précision la cible augmentant les chances de la toucher (+%effet%%).', 5, 'tir_precis', 'distance', 'dexterite', 0, 300, 'arc', '55', 4, '2', 2000, 2);
INSERT INTO `comp_combat` VALUES (4, 'Oeil du faucon 4', 'Vise avec précision la cible augmentant les chances de la toucher (+%effet%%).', 5, 'tir_precis', 'distance', 'dexterite', 0, 400, 'arc', '70', 4, '3', 4000, 3);
INSERT INTO `comp_combat` VALUES (5, 'Coup violent', 'Frappe puissante infligeant plus de dégats à l''adversaire (+%effet%).', 4, 'coup_violent', 'melee', 'force', 0, 150, 'dague;epee;hache', '2', 4, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (6, 'Coup violent 2', 'Frappe puissante infligeant plus de dégats à l''adversaire (+%effet%).', 4, 'coup_violent', 'melee', 'force', 0, 200, 'dague;epee;hache', '3', 4, '5', 1000, 1);
INSERT INTO `comp_combat` VALUES (7, 'Tir précis', 'Vise avec précision la cible augmentant les chances de la toucher (+%effet%%).', 5, 'tir_precis', 'distance', 'dexterite', 0, 0, 'arc', '10', 4, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (8, 'Coup puissant', 'Frappe puissante infligeant plus de dégats à l''adversaire (+%effet%).', 4, 'coup_puissant', 'melee', 'force', 0, 0, 'dague;epee;hache', '1', 4, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (9, 'Berzeker', 'Berzerk vous transforme en une bête de guerre, réduisant vos capacitées défensives, mais augmentant vos capacitées offensive (effet %effet%).', 2, 'berzeker', 'melee', 'force', 0, 150, 'hache;epee', '1', 2, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (10, 'Berzerk 2', 'Berzerk vous transforme en une bête de guerre, réduisant vos capacitées défensives, mais augmentant vos capacitées offensive (effet %effet%).', 2, 'berzeker', 'melee', 'force', 0, 200, 'epee;hache', '2', 2, '9', 1000, 1);
INSERT INTO `comp_combat` VALUES (11, 'Coup sournois', 'Augmente les chances de faire un coup critique (+%effet%%).', 3, 'coup_sournois', 'melee', 'dexterite', 0, 150, 'dague', '50', 4, '999', 5, 1);
INSERT INTO `comp_combat` VALUES (12, 'Coup sournois 2', 'Augmente les chances de faire un coup critique (+%effet%%).', 3, 'coup_sournois', 'melee', 'dexterite', 0, 200, 'dague', '75', 4, '12', 1000, 1);
INSERT INTO `comp_combat` VALUES (13, 'Coup sournois 3', 'Augmente les chances de faire un coup critique (+%effet%%).', 3, 'coup_sournois', 'melee', 'dexterite', 0, 300, 'dague', '100', 2, '13', 2000, 2);
INSERT INTO `comp_combat` VALUES (14, 'Coup sounrois 4', 'Augmente les chances de faire un coup critique (+%effet%%).', 3, 'coup_sournois', 'melee', 'dexterite', 0, 400, 'dague', '125', 2, '14', 4000, 3);
INSERT INTO `comp_combat` VALUES (15, 'Tir visé', 'Vous utilisez un round de combat pour prendre le temps de viser. Augmente la précision, les dégats et les chances de critique (effet %effet%).', 5, 'tir_vise', 'distance', 'force', 0, 100, 'arc', '1', 2, '', 200, 1);
INSERT INTO `comp_combat` VALUES (16, 'Tir visé 2', 'Vous utilisez un round de combat pour prendre le temps de viser. Augmente la précision, les dégats et les chances de critique (effet %effet%).', 5, 'tir_vise', 'distance', 'force', 0, 200, 'arc', '2', 2, '15', 1000, 1);
INSERT INTO `comp_combat` VALUES (17, 'Coup de bouclier', 'Donne un coup de bouclier qui peut assomer l''adversaire (effet %effet%).', 4, 'coup_bouclier', 'melee', 'force', 0, 110, 'bouclier', '1', 4, '', 200, 1);
INSERT INTO `comp_combat` VALUES (18, 'Coup de bouclier 2', 'Donne un coup de bouclier qui peut assomer l''adversaire (effet %effet%).', 4, 'coup_bouclier', 'melee', 'force', 0, 220, 'bouclier', '2', 4, '17', 1100, 2);
INSERT INTO `comp_combat` VALUES (19, 'Posture du chat', 'Augmente vos chances d''esquiver les attaques physiques (+%effet%%).', 0, 'posture_esquive', 'melee', 'dexterite', 0, 0, '', '10', 2, '999', 1, 1);
INSERT INTO `comp_combat` VALUES (20, 'Posture du scorpion', 'Augmente vos chances de faire un coup critique (+%effet%%).', 0, 'posture_critique', 'melee', 'dexterite', 0, 0, '', '20', 2, '999', 1, 1);
INSERT INTO `comp_combat` VALUES (21, 'Posture du scarabée', 'Réduit les dégats physiques qui vous sont infligés (-%effet%).', 0, 'posture_defense', 'melee', 'force', 0, 0, '', '1', 2, '999', 1, 1);
INSERT INTO `comp_combat` VALUES (22, 'Posture du loup', 'Augmente les dégats que vous infligé (+%effet%).', 0, 'posture_degat', 'melee', 'force', 0, 0, '', '1', 2, '999', 1, 1);
INSERT INTO `comp_combat` VALUES (23, 'Posture de l''aigle', 'Vos coups ont une chance d''ignorer l''armure de l''adversaire (%effet%% de chances).', 0, 'posture_transperce', 'distance', 'dexterite', 0, 200, 'arc', '10', 2, '19', 1000, 1);
INSERT INTO `comp_combat` VALUES (24, 'Posture du serpent', 'Vos coups critiques ont une chance de paralyser l''adversaire (%effet%% de chances).', 0, 'posture_paralyse', 'melee', 'dexterite', 0, 200, 'dague', '35', 2, '20', 1000, 1);
INSERT INTO `comp_combat` VALUES (25, 'Posture du lion', 'Augmente vos chances de toucher (+%effet%%).', 0, 'posture_touche', 'melee', 'dexterite', 0, 200, '', '10', 2, '21', 1000, 1);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture du guépard', 'Augmente vos chances d''esquiver les attaques physiques (+%effet%%).', '0', 'posture_esquive', 'melee', 'dexterite', '0', '0', '', '20', '2', '999', '1', '1'
), (
NULL , 'Posture du Cobra', 'Augmente vos chances de faire un coup critique (+%effet%%).', '0', 'posture_critique', 'melee', 'dexterite', '0', '0', '', '40', '2', '999', '1', '1'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture de la tortue', 'Réduit les dégats physiques qui vous sont infligés (-%effet%).', '0', 'posture_defense', 'melee', 'force', '0', '0', '', '2', '2', '999', '1', '1'
), (
NULL , 'Posture de l''ours', 'Augmente les dégats que vous infligé (+%effet%).', '0', 'poqture_degat', 'melee', 'force', '0', '0', '', '2', '2', '999', '1', '1'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '9', '26'
), (
NULL , '9', '27'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '11', '26'
), (
NULL , '11', '27'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '10', '28'
), (
NULL , '10', '29'
);