ALTER TABLE `buff` ADD `debuff` BINARY NOT NULL DEFAULT '0';
CREATE TABLE `candidat` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_perso` INT UNSIGNED NOT NULL ,
`date` VARCHAR( 50 ) NOT NULL ,
`royaume` TINYINT UNSIGNED NOT NULL ,
`programme` TEXT NOT NULL
);
CREATE TABLE `vote` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_perso` INT UNSIGNED NOT NULL ,
`id_candidat` INT UNSIGNED NOT NULL ,
`date` VARCHAR( 50 ) NOT NULL ,
`royaume` TINYINT UNSIGNED NOT NULL
);
ALTER TABLE `royaume` ADD `honneur_candidat` INT UNSIGNED NOT NULL ;
ALTER TABLE `candidat` ADD `nom` VARCHAR( 50 ) NOT NULL ;
UPDATE `sort_jeu` SET `description` = 'Augmente votre défense magique (+%effet%%).',
`effet` = '10' WHERE `id` =32 LIMIT 1 ;

UPDATE `sort_jeu` SET `description` = 'Augmente votre défense magique (+%effet%%).',
`effet` = '20' WHERE `id` =33 LIMIT 1 ;

UPDATE `sort_jeu` SET `description` = 'Réduit la défense magique de la cible (-%effet%%).',
`effet` = '10' WHERE `id` =34 LIMIT 1 ;

UPDATE `sort_jeu` SET `description` = 'Réduit la défense magique de la cible (-%effet%%).',
`effet` = '20' WHERE `id` =35 LIMIT 1 ;

UPDATE `sort_jeu` SET `description` = 'Réduit les chances de toucher de la cible (-%effet%%).',
`cible` = '40' WHERE `id` =38 LIMIT 1 ;

UPDATE `sort_jeu` SET `description` = 'Réduit les chances de toucher de la cible (-%effet%%).',
`effet` = '60' WHERE `id` =39 LIMIT 1 ;
UPDATE `sort_jeu` SET `effet` = '20' WHERE `id` =25 LIMIT 1 ;
ALTER TABLE `sort_jeu` ADD `portee` TINYINT UNSIGNED NOT NULL AFTER `cible` ;
UPDATE `sort_jeu` SET portee = 1;
UPDATE sort_jeu SET portee = 2;
UPDATE sort_jeu SET portee = 3 WHERE type = 'vie';
UPDATE `sort_jeu` SET `portee` = '1' WHERE `id` =15 LIMIT 1 ;
UPDATE `sort_jeu` SET `portee` = '1' WHERE `id` =34 LIMIT 1 ;
UPDATE `sort_jeu` SET `portee` = '1' WHERE `id` =35 LIMIT 1 ;
UPDATE `sort_jeu` SET `portee` = '1' WHERE `id` =38 LIMIT 1 ;
UPDATE `sort_jeu` SET `portee` = '1' WHERE `id` =39 LIMIT 1 ;
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drops` , `spawn` , `terrain` )
VALUES (
NULL , 'ogre', 'Ogre', 'monstre', '350', '400', '200', '20', '15', '10', '10', '300', '250', '1', '1', '1', '1', '#10°berzeker@_10;_6', '9', '8000', '550', 'hg1-143;o8-200', '130', '6'
);
ALTER TABLE `classe_permet` CHANGE `permet` `permet` FLOAT( 11 ) NOT NULL ;
UPDATE `classe` SET `nom` = 'Grand Nécromancien' WHERE `id` =13 LIMIT 1 ;
UPDATE `classe_permet` SET `competence` = 'sort_element' WHERE `id` =9 LIMIT 1 ;
UPDATE `classe_requis` SET `competence` = 'sort_element' WHERE `id` =11 LIMIT 1 ;
DELETE FROM classe_permet WHERE id = 32;
INSERT INTO `classe` ( `id` , `nom` , `description` , `rang` , `type` )
VALUES (
NULL , 'Nécromancien', '', '2', 'mage'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '15', 'incantation', '400', 'no'
), (
NULL , '15', 'sort_mort', '300', 'no'
);
INSERT INTO `classe_requis` ( `id` , `id_classe` , `competence` , `requis` )
VALUES (
NULL , '15', 'honneur', '1000'
), (
NULL , '15', 'incantation', '150'
);
INSERT INTO `classe_requis` ( `id` , `id_classe` , `competence` , `requis` )
VALUES (
NULL , '15', 'sort_mort', '50'
), (
NULL , '15', 'classe', '2'
);
UPDATE `classe_requis` SET `requis` = '15' WHERE `id` =44 LIMIT 1 ;
INSERT INTO `classe` ( `id` , `nom` , `description` , `rang` , `type` )
VALUES (
NULL , 'Paladin', '', '3', 'guerrier'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '16', 'facteur_magie', '1.5', 'no'
), (
NULL , '16', 'melee', '450', 'no'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '16', 'esquive', '350', 'no'
), (
NULL , '16', 'maitrise_epee', '150', 'no'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '16', 'blocage', '450', 'no'
), (
NULL , '16', 'sort_vie+', '10', 'no'
);
INSERT INTO `classe_requis` ( `id` , `id_classe` , `competence` , `requis` )
VALUES (
NULL , '16', 'honneur', '10000'
), (
NULL , '16', 'melee', '350'
);
INSERT INTO `classe_requis` ( `id` , `id_classe` , `competence` , `requis` )
VALUES (
NULL , '16', 'esquive', '250'
), (
NULL , '16', 'classe', '4'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '16', 'sort_vie', '200', 'no'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Colere', 'Augmente les dégats des critiques (+%effet%%).', '4', '25', 'buff_colere', 'sort_vie', 'volonte', '0', '80', '40', '20', '86400', '2', '2', '', '250', '1'
), (
NULL , 'Colere 2', 'Augmente les dégats des critiques (+%effet%%).', '4', '28', 'buff_colere', 'sort_vie', 'volonte', '0', '160', '80', '35', '86400', '2', '2', '', '1000', '2'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Colere 3', 'Augmente les dégats des critiques (+%effet%%).', '4', '30', 'buff_colere', 'sort_vie', 'volonte', '0', '200', '100', '50', '86400', '2', '2', '', '1600', '2'
), (
NULL , 'Méditation', 'Augmente les chances de lancer un sort (+%effet%%).', '4', '25', 'buff_meditation', 'sort_vie', 'volonte', '0', '100', '50', '5', '86400', '2', '2', '', '200', '1'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Méditation 2', 'Augmente vos chances de lancer un sort (+%effet%%)', '4', '28', 'buff_meditation', 'sort_vie', 'volonte', '0', '200', '100', '10', '86400', '2', '2', '', '1600', '2'
), (
NULL , 'Méditation 3', 'Augmente vos chances de lancer un sort (+%effet%%)', '4', '30', 'buff_meditation', 'sort_vie', 'volonte', '0', '400', '200', '25', '86400', '2', '2', '', '6000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Rapidité', 'Réduit vos coûts en PA pour attaquer (-%effet%)', '4', '26', 'buff_rapidite', 'sort_vie', 'volonte', '0', '120', '60', '1', '86400', '2', '2', '', '500', '2'
), (
NULL , 'Rapidité 2', 'Réduit vos coûts en PA pour attaquer (-%effet%)', '4', '29', 'buff_rapidite', 'sort_vie', 'volonte', '0', '220', '110', '2', '86400', '2', '2', '', '2000', '2'
);
INSERT INTO `sort_jeu` (`id`, `nom`, `description`, `pa`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `duree`, `cible`, `portee`, `requis`, `prix`, `lvl_batiment`) VALUES (NULL, 'Rapidité 3', 'Réduit vos coûts en PA pour attaquer (-%effet%)', '4', '32', 'buff_rapidite', 'sort_vie', 'volonte', '0', '400', '200', '3', '86400', '2', '2', '', '6000', '3'), (NULL, 'Rage Vampirique', 'Redonne de la vie par dégat physique (%effet%% retournés)', '4', '30', 'buff_rage_vampirique', 'sort_mort', 'puissance', '0', '140', '70', '7', '86400', '2', '2', '', '800', '2');
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Rage Vampirique 2', 'Redonne de la vie par dégat physique (%effet%% retournés)', '4', '35', 'buff_rage_vampirique', 'sort_mort', 'puissance', '0', '260', '130', '11', '86400', '2', '2', '', '3300', '3'
), (
NULL , 'Rage Vampirique 3', 'Redonne de la vie par dégat physique (%effet%% retournés)', '4', '40', 'buff_rage_vampirique', 'sort_mort', 'puissance', '0', '500', '250', '15', '86400', '2', '2', '', '25000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Bouclier sacré', 'Augmente vos chances de bloquer au bouclier (+%effet%%)', '4', '20', 'buff_bouclier_sacre', 'sort_vie', 'volonte', '0', '120', '60', '15', '86400', '2', '2', '', '500', '2'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Bouclier sacré 2', 'Augmente vos chances de bloquer au bouclier (+%effet%%)', '4', '22', 'buff_bouclier_sacre', 'sort_vie', 'volonte', '0', '200', '100', '30', '86400', '2', '2', '', '2000', '2'
), (
NULL , 'Bouclier Sacré 3', 'Augmente vos chances de bloquer au bouclier (+%effet%%)', '4', '24', 'buff_bouclier_sacre', 'sort_vie', 'volonte', '0', '280', '140', '45', '86400', '2', '2', '', '4000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Armure en épines', 'Renvoi une partie des dégats physiques qui vous sont infligés (%effet%%)', '4', '25', 'buff_epine', 'sort_vie', 'volonte', '0', '140', '70', '15', '86400', '2', '2', '', '800', '2'
), (
NULL , 'Armure en épines 2', 'Renvoi une partie des dégats physiques qui vous sont infligés (%effet%%)', '4', '28', 'buff_epine', 'sort_vie', 'volonte', '0', '260', '130', '25', '86400', '2', '2', '', '3300', '3'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '6', 'sort_groupe', '100', 'yes'
);
UPDATE `sort_jeu` SET `cible` = '1' WHERE `id` =36 LIMIT 1 ;

UPDATE `sort_jeu` SET `cible` = '1' WHERE `id` =37 LIMIT 1 ;

INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Dissimulation', 'L''adversaire ne peut plus vous attaquer, si vous portez une attaque en étant dissimulé, les chances de toucher sont augmentées de %effet%%.', '1', 'dissimulation', 'esquive', 'dexterite', '0', '0', 'dague', '30', '1', '999', '0', '1'
), (
NULL , 'Dissimulation 2', 'L''adversaire ne peut plus vous attaquer, si vous portez une attaque en étant dissimulé, les chances de toucher sont augmentées de %effet%%.', '1', 'dissimulation', 'esquive', 'dexterite', '0', '200', 'dague', '40', '1', '', '1000', '1');
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Dissimulation 3', 'L''adversaire ne peut plus vous attaquer, si vous portez une attaque en étant dissimulé, les chances de toucher sont augmentées de %effet%%.', '1', 'dissimulation', 'esquive', 'dexterite', '0', '300', 'dague', '50', '1', '', '3000', '2'
), (
NULL , 'Coup Mortel', 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 4, et augmente les chances de critique de 70%.', '3', 'coup_mortel', 'melee', 'dexterite', '0', '0', 'dague', '90', '2', '999', '0', '1'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Coup Mortel 2', 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 4, et augmente les chances de critique de 70%.', '3', 'coup_mortel', 'melee', 'dexterite', '0', '250', 'dague', '87', '1', '', '1800', '2'
), (
NULL , 'Coup Mortel 3', 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 4, et augmente les chances de critique de 70%.', '3', 'coup_mortel', 'melee', 'dexterite', '0', '350', 'dague', '84', '1', '', '2800', '2'
);
CREATE TABLE `comp_jeu` (
`id` mediumint( 8 ) unsigned NOT NULL AUTO_INCREMENT ,
`nom` varchar( 50 ) NOT NULL default '',
`description` text NOT NULL ,
`mp` mediumint( 8 ) unsigned NOT NULL default '0',
`type` varchar( 50 ) NOT NULL default '',
`comp_assoc` varchar( 50 ) NOT NULL ,
`carac_assoc` varchar( 50 ) NOT NULL ,
`carac_requis` mediumint( 9 ) unsigned NOT NULL default '0',
`comp_requis` mediumint( 9 ) NOT NULL ,
`arme_requis` varchar( 50 ) NOT NULL ,
`effet` varchar( 50 ) NOT NULL default '',
`cible` tinyint( 4 ) NOT NULL ,
`requis` text NOT NULL ,
`prix` int( 11 ) NOT NULL default '0',
`lvl_batiment` tinyint( 3 ) unsigned NOT NULL ,
PRIMARY KEY ( `id` )
);
ALTER TABLE `perso` ADD `comp_jeu` TEXT NOT NULL AFTER `comp_combat` ;
INSERT INTO `comp_jeu` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Forteresse', 'Vous ne pouvez plus bouger. Augmente votre protection physique de %effet%% et magique de %effet2%%.', '50', 'buff_forteresse', 'melee', 'force', '0', '0', '', '50', '1', '999', '0', '1'
), (
NULL , 'Forteresse 2', 'Vous ne pouvez plus bouger. Augmente votre protection physique de %effet%% et magique de %effet2%%.', '70', 'buff_forteresse', 'melee', 'force', '0', '200', '', '80', '1', '', '1000', '1'
);
INSERT INTO `comp_jeu` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Forteresse 3', 'Vous ne pouvez plus bouger. Augmente votre protection physique de %effet%% et magique de %effet2%%.', '90', 'buff_forteresse', 'melee', 'force', '0', '300', '', '105', '1', '', '2500', '2'
);
ALTER TABLE `comp_jeu` ADD `effet2` TINYINT UNSIGNED NOT NULL AFTER `effet` ,
ADD `duree` INT UNSIGNED NOT NULL AFTER `effet2` ;
UPDATE `comp_jeu` SET `effet2` = '30',
`duree` = '86400' WHERE `id` =1 LIMIT 1 ;

UPDATE `comp_jeu` SET `effet2` = '50',
`duree` = '86400' WHERE `id` =2 LIMIT 1 ;

UPDATE `comp_jeu` SET `effet2` = '65',
`duree` = '86400' WHERE `id` =3 LIMIT 1 ;
ALTER TABLE `comp_jeu` ADD `pa` TINYINT UNSIGNED NOT NULL AFTER `mp` ;
UPDATE `comp_jeu` SET `pa` = '4' WHERE `id` =1 LIMIT 1 ;

UPDATE `comp_jeu` SET `pa` = '4' WHERE `id` =2 LIMIT 1 ;

UPDATE `comp_jeu` SET `pa` = '4' WHERE `id` =3 LIMIT 1 ;
INSERT INTO `comp_jeu` ( `id` , `nom` , `description` , `mp` , `pa` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Position favorable', 'Vous ne pouvez plus bouger. Augmente vos chances de toucher de %effet%%.', '10', '4', 'buff_position', 'distance', 'dexterite', '0', '0', '', '25', '', '86400', '1', '999', '0', '1'
), (
NULL , 'Position favorable 2', 'Vous ne pouvez plus bouger. Augmente vos chances de toucher de %effet%%.', '15', '4', 'buff_position', 'distance', 'dexterite', '0', '200', '', '45', '', '86400', '1', '', '1000', '1'
);
INSERT INTO `comp_jeu` ( `id` , `nom` , `description` , `mp` , `pa` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Position favorable 3', 'Vous ne pouvez plus bouger. Augmente vos chances de toucher de %effet%%.', '20', '4', 'buff_position', 'distance', 'dexterite', '0', '300', '', '60', '', '86400', '1', '', '2500', '2'
);
