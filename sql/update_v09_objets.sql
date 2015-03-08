
-- on modifie la table arme
ALTER TABLE `arme` ADD `coefficient` INT NOT NULL DEFAULT '9' AFTER `distance`;
update arme set coefficient = `forcex`*(melee+distance);
ALTER TABLE `arme` DROP `melee`;
ALTER TABLE `arme` DROP `distance`;
ALTER TABLE `arme` DROP `image`;
ALTER TABLE `arme` ADD `encombrement` INT NOT NULL DEFAULT '10';

-- on modifie la table grimoire
ALTER TABLE `grimoire` ADD `type` VARCHAR( 15 ) NOT NULL AFTER `nom`;
ALTER TABLE `grimoire` ADD `id_apprend` INT NULL DEFAULT NULL COMMENT 'id du sort ou de la compétence apprise' AFTER `type`;
UPDATE grimoire SET type = 'comp_jeu', id_apprend = comp_jeu WHERE comp_jeu IS NOT NULL;
UPDATE grimoire SET type = 'comp_combat', id_apprend = comp_combat WHERE comp_combat IS NOT NULL;
UPDATE grimoire SET type = 'sort_jeu', id_apprend = sort_jeu WHERE sort_jeu IS NOT NULL;
UPDATE grimoire SET type = 'sort_combat', id_apprend = sort_combat WHERE sort_combat IS NOT NULL;
UPDATE grimoire SET type = 'attr_perso' WHERE comp_perso_competence IS NOT NULL;
ALTER TABLE `grimoire` DROP `comp_jeu` ,
DROP `comp_combat` ,
DROP `sort_jeu` ,
DROP `sort_combat`  ,
DROP `comp_perso_id` ;
ALTER TABLE `grimoire` CHANGE `comp_perso_competence` `attr_perso` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'nom de l\'attribut à améliorer';
ALTER TABLE `grimoire` CHANGE `comp_perso_valueadd` `ajout_attr` SMALLINT( 3 ) NULL DEFAULT NULL COMMENT 'valeur à ajouter à l\'attribut';
ALTER TABLE `grimoire` CHANGE `classe_requis` `classe_requis` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'classes donnant accès au grimoire';
ALTER TABLE `grimoire` ADD `encombrement` INT NOT NULL DEFAULT '2';

-- on modifie la table gemme
ALTER TABLE gemme ADD `prix` INT NOT NULL ;
UPDATE gemme SET prix = POW(8, niveau + 1) * 10;
ALTER TABLE `gemme` ADD `encombrement` INT NOT NULL DEFAULT '2';

-- Objets
UPDATE objet SET stack = '20' WHERE id = 2;
ALTER TABLE `objet` ADD `encombrement` INT NOT NULL DEFAULT '5';
UPDATE `objet` SET `encombrement` = '2' WHERE `type` LIKE 'objet_quete' OR id IN (14, 15, 17, 18, 19, 24, 25, 26, 48, 49, 50, 51);
UPDATE `objet` SET `encombrement` = '1' WHERE id IN (3, 10, 12, 13, 16, 20, 21, 22, 23, 56, 57);

-- Accessoires
-- On change le champ achetable par un champ lvl_batiment pour uniformiser
ALTER TABLE `accessoire` ADD `lvl_batiment` INT NOT NULL DEFAULT '9';
UPDATE accessoire SET lvl_batiment = 1 WHERE achetable = 'y';
UPDATE accessoire SET lvl_batiment = 9 WHERE achetable = 'n';
ALTER TABLE `accessoire` DROP `achetable`;
ALTER TABLE `accessoire` ADD `taille` VARCHAR( 5 ) NOT NULL DEFAULT 'grand' AFTER `nom`;
-- Ajout de la taille
UPDATE accessoire SET taille = 'moyen' WHERE id <= 9 OR id = 20;
-- On modifie certains type pour qu'il soit égal au "bonus permanent" correspondant (simplifie la gestion) 
UPDATE accessoire SET type = 'alchimie', description = "Augmente les chances de réussir la réalisation d'une recette alchimique %effet%%." WHERE type = 'fabrication';
UPDATE accessoire SET type = 'reserve' WHERE type = 'rm';
UPDATE accessoire SET type = 'regen_hp_add' WHERE type = 'regen_hp';
UPDATE accessoire SET type = 'regen_mp_add' WHERE type = 'regen_mp';
ALTER TABLE `accessoire` ADD `encombrement` INT NOT NULL DEFAULT '5';

-- Objets des créatures
ALTER TABLE `objet_pet` ADD `bonus` VARCHAR(15) NOT NULL AFTER `dressage`, ADD `valeur` INT NOT NULL AFTER `bonus`;
UPDATE `objet_pet` SET bonus='pp', valeur=PP WHERE PP > 0;
UPDATE `objet_pet` SET bonus='pm', valeur=PM WHERE PM > 0;
UPDATE `objet_pet` SET bonus='distance_tir', valeur=distance_tir WHERE distance_tir > 0;
ALTER TABLE `objet_pet` ADD `encombrement` INT NOT NULL DEFAULT '10';

-- Objets de royaume
ALTER TABLE `objet_royaume` ADD `rang_royaume` TINYINT NOT NULL DEFAULT '1';
ALTER TABLE `objet_royaume` ADD `encombrement` INT NOT NULL DEFAULT '2';
UPDATE `objet_royaume` SET `encombrement` = '10';
UPDATE `objet_royaume` SET `rang_royaume` = '2', `encombrement` = '20' WHERE `id` = 3;
UPDATE `objet_royaume` SET `rang_royaume` = '3', `encombrement` = '25' WHERE `id` = 4;
UPDATE `objet_royaume` SET `rang_royaume` = '4', `encombrement` = '30' WHERE `id` = 5;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` IN (7, 13, 17);
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` IN (8, 9, 12, 16);
UPDATE `objet_royaume` SET `encombrement` = '15' WHERE `id` = 10;
UPDATE `objet_royaume` SET `rang_royaume` = '4' WHERE `id` = 14;
UPDATE `objet_royaume` SET `rang_royaume` = '2', `encombrement` = '5' WHERE `id` = 19;
UPDATE `objet_royaume` SET `rang_royaume` = '3', `encombrement` = '5' WHERE `id` = 20;
UPDATE `objet_royaume` SET `rang_royaume` = '4', `encombrement` = '5' WHERE `id` = 21;
UPDATE `objet_royaume` SET `encombrement` = '5' WHERE `id` = 22;

-- Armure
ALTER TABLE `armure` ADD `encombrement` INT NOT NULL DEFAULT '10';

-- On ajoute l'encombrement au personnage
ALTER TABLE `perso` ADD `encombrement` INT NULL DEFAULT '0' AFTER `inventaire_slot`;