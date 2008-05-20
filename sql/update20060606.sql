UPDATE sort_jeu SET incantation = comp_requis * 2;
ALTER TABLE `perso` ADD `facteur_magie` DOUBLE UNSIGNED NOT NULL DEFAULT '1' AFTER `sort_mort` ,
ADD `facteur_sort_vie` DOUBLE UNSIGNED NOT NULL DEFAULT '1' AFTER `facteur_magie` ,
ADD `facteur_sort_mort` DOUBLE UNSIGNED NOT NULL DEFAULT '1' AFTER `facteur_sort_vie` ,
ADD `facteur_sort_element` DOUBLE UNSIGNED NOT NULL DEFAULT '1' AFTER `facteur_sort_mort` ;
UPDATE perso SET facteur_magie = 2 WHERE (classe = 'combattant' OR classe = 'guerrier' OR classe = 'archer' OR classe = 'voleur');
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Focus 2', 'Augmente les chances de critique (effet %effet%)', '4', '25', 'buff_critique', 'sort_element', 'puissance', '11', '80', '40', '2', '86400', '2', '10', '250'
), (
NULL , 'Focus 3', 'Augmente les chances de critique (effet %effet%)', '4', '30', 'buff_critique', 'sort_element', 'puissance', '11', '160', '80', '3', '86400', '2', '21', '1000'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Evasion 2', 'Augmente l''esquive (effet %effet%)', '4', '25', 'buff_evasion', 'sort_vie', 'energie', '11', '80', '40', '2', '86400', '2', '12', '250'
), (
NULL , 'Evasion 3', 'Augmente l''esquive (effet %effet%)', '4', '30', 'buff_evasion', 'sort_vie', 'energie', '11', '160', '80', '3', '86400', '2', '23', '1000'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Bouclier 2', 'Augmente l''armure (effet %effet%)', '4', '40', 'buff_bouclier', 'sort_vie', 'energie', '11', '120', '60', '2', '86400', '2', '14', '500'
), (
NULL , 'Bouclier 3', 'Augmente l''armure (effet %effet%)', '4', '50', 'buff_bouclier', 'sort_vie', 'energie', '11', '240', '120', '3', '86400', '2', '25', '3000'
);
INSERT INTO `classe` VALUES (9, 'Assassin', '', 3);
INSERT INTO `classe` VALUES (10, 'Champion', '', 3);
INSERT INTO `classe` VALUES (11, 'Archer d''élite', '', 3);
INSERT INTO `classe` VALUES (12, 'Prètre', '', 3);
INSERT INTO `classe` VALUES (13, 'Nécromancien', '', 3);
INSERT INTO `classe` VALUES (14, 'Grand sorcier', '', 3);
INSERT INTO `classe_permet` VALUES (32, 5, 'sort_element', 300, 'no');
INSERT INTO `classe_permet` VALUES (33, 9, 'melee', 400, 'no');
INSERT INTO `classe_permet` VALUES (34, 9, 'maitrise_critique', 200, 'no');
INSERT INTO `classe_permet` VALUES (35, 9, 'esquive', 500, 'no');
INSERT INTO `classe_permet` VALUES (36, 9, 'maitrise_dague', 200, 'no');
INSERT INTO `classe_permet` VALUES (37, 10, 'maitrise_epee', 200, 'no');
INSERT INTO `classe_permet` VALUES (38, 10, 'blocage', 500, 'no');
INSERT INTO `classe_permet` VALUES (39, 10, 'maitrise_hache', 200, 'no');
INSERT INTO `classe_permet` VALUES (40, 10, 'esquive', 350, 'no');
INSERT INTO `classe_permet` VALUES (41, 10, 'melee', 500, 'no');
INSERT INTO `classe_permet` VALUES (42, 11, 'distance', 500, 'no');
INSERT INTO `classe_permet` VALUES (43, 11, 'maitrise_arc', 200, 'no');
INSERT INTO `classe_permet` VALUES (44, 11, 'maitrise_critique', 150, 'no');
INSERT INTO `classe_permet` VALUES (45, 11, 'esquive', 350, 'no');
INSERT INTO `classe_permet` VALUES (46, 12, 'sort_vie', 400, 'no');
INSERT INTO `classe_permet` VALUES (47, 12, 'incantation', 500, 'no');
INSERT INTO `classe_permet` VALUES (48, 12, 'esquive', 200, 'no');
INSERT INTO `classe_permet` VALUES (49, 13, 'sort_mort', 400, 'no');
INSERT INTO `classe_permet` VALUES (50, 13, 'incantation', 500, 'no');
INSERT INTO `classe_permet` VALUES (51, 13, 'esquive', 200, 'no');
INSERT INTO `classe_permet` VALUES (52, 14, 'sort_element', 400, 'no');
INSERT INTO `classe_permet` VALUES (53, 14, 'incantation', 500, 'no');
INSERT INTO `classe_permet` VALUES (54, 14, 'esquive', 200, 'no');
INSERT INTO `classe_requis` VALUES (22, 9, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (23, 9, 'maitrise_dague', 50);
INSERT INTO `classe_requis` VALUES (24, 9, 'esquive', 350);
INSERT INTO `classe_requis` VALUES (25, 10, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (26, 10, 'melee', 350);
INSERT INTO `classe_requis` VALUES (27, 10, 'esquive', 250);
INSERT INTO `classe_requis` VALUES (28, 11, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (29, 11, 'distance', 350);
INSERT INTO `classe_requis` VALUES (30, 11, 'maitrise_arc', 50);
INSERT INTO `classe_requis` VALUES (31, 12, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (32, 12, 'incantation', 300);
INSERT INTO `classe_requis` VALUES (33, 12, 'sort_vie', 250);
INSERT INTO `classe_requis` VALUES (34, 13, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (35, 13, 'incantation', 300);
INSERT INTO `classe_requis` VALUES (36, 13, 'sort_mort', 250);
INSERT INTO `classe_requis` VALUES (37, 14, 'honneur', 10000);
INSERT INTO `classe_requis` VALUES (38, 14, 'incantation', 300);
INSERT INTO `classe_requis` VALUES (39, 14, 'sort_element', 250);
INSERT INTO `classe_requis` VALUES (40, 9, 'classe', 3);
INSERT INTO `classe_requis` VALUES (41, 10, 'classe', 4);
INSERT INTO `classe_requis` VALUES (42, 11, 'classe', 8);
INSERT INTO `classe_requis` VALUES (43, 12, 'classe', 6);
INSERT INTO `classe_requis` VALUES (44, 13, 'classe', 5);
INSERT INTO `classe_requis` VALUES (45, 14, 'classe', 5);
UPDATE `sort_jeu` SET `incantation` = '0' WHERE `id` =1 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '0' WHERE `id` =9 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '0' WHERE `id` =11 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '0' WHERE `id` =15 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '100', `comp_requis` = '50', `prix` = '400' WHERE `id` =16 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '240', `comp_requis` = '120', `prix` = '2000' WHERE `id` =17 LIMIT 1 ;
CREATE TABLE `placement` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` VARCHAR( 50 ) NOT NULL ,
`x` TINYINT UNSIGNED NOT NULL ,
`y` TINYINT UNSIGNED NOT NULL ,
`royaume` TINYINT UNSIGNED NOT NULL ,
`fin_placement` INT UNSIGNED NOT NULL,
`id_batiment` INT UNSIGNED NOT NULL,
`hp` INT UNSIGNED NOT NULL 
);
ALTER TABLE `batiment` ADD `hp` INT UNSIGNED NOT NULL ,
ADD `PP` INT UNSIGNED NOT NULL,
ADD `type` VARCHAR( 50 ) NOT NULL AFTER `description` ;
UPDATE sort_jeu SET mp = ROUND( mp * 0.8 );
UPDATE `sort_jeu` SET `incantation` = '30', `comp_requis` = '15', `prix` = '100' WHERE `id` =10 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '30', `comp_requis` = '15', `prix` = '100' WHERE `id` =12 LIMIT 1 ;
UPDATE `sort_jeu` SET `incantation` = '60', `comp_requis` = '30', `prix` = '250' WHERE `id` =14 LIMIT 1 ;

INSERT INTO `sort_jeu` VALUES (27, 'Force', 'Augmente les dégats infligés physiquement (effet %effet%).', 4, 25, 'buff_force', 'sort_vie', 'energie', 0, 140, 70, '1', 86400, 2, '', 700);
INSERT INTO `sort_jeu` VALUES (28, 'Force 2', 'Augmente les dégats infligés physiquement (effet %effet%).', 4, 34, 'buff_force', 'sort_vie', 'energie', 0, 270, 135, '2', 86400, 2, '', 3500);
INSERT INTO `sort_jeu` VALUES (29, 'Force 3', 'Augmente les dégats infligés physiquement (effet %effet%).', 4, 45, 'buff_force', 'sort_vie', 'energie', 0, 420, 210, '3', 86400, 2, '', 7000);

UPDATE `sort_jeu` SET `mp` = '16', `effet` = '9' WHERE `id` =8 LIMIT 1 ;
UPDATE `sort_jeu` SET `mp` = '11', `effet` = '6' WHERE `id` =5 LIMIT 1 ;
UPDATE `sort_jeu` SET `mp` = '16', `effet` = '12' WHERE `id` =7 LIMIT 1 ;
UPDATE `sort_jeu` SET `mp` = '11', `effet` = '8' WHERE `id` =3 LIMIT 1 ;

CREATE TABLE `objet` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nom` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `prix` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `objet`
-- 

INSERT INTO `objet` VALUES (1, 'Drapeau', 'drapeau', 10);