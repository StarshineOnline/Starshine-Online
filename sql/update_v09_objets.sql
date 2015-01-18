
-- on modifie la table arme
ALTER TABLE `arme` ADD `coefficient` INT NOT NULL DEFAULT '9' AFTER `distance`;
update arme set coefficient = `forcex`*(melee+distance);
ALTER TABLE `arme` DROP `melee`;
ALTER TABLE `arme` DROP `distance`;
ALTER TABLE `arme` DROP `image`;

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

-- on modifie la table gemme
ALTER TABLE gemme ADD `prix` INT NOT NULL ;
UPDATE gemme SET prix = POW(8, niveau + 1) * 10;

-- Objets
UPDATE objet SET stack = '20' WHERE id = 2;

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

-- Objets des créatures
ALTER TABLE `objet_pet` ADD `bonus` VARCHAR(15) NOT NULL AFTER `dressage`, ADD `valeur` INT NOT NULL AFTER `bonus`;
UPDATE `objet_pet` SET bonus='pp', valeur=PP WHERE PP > 0;
UPDATE `objet_pet` SET bonus='pm', valeur=PM WHERE PM > 0;
UPDATE `objet_pet` SET bonus='distance_tir', valeur=distance_tir WHERE distance_tir > 0;

-- Objets de royaume
ALTER TABLE `objet_royaume` ADD `rang_royaume` TINYINT NOT NULL DEFAULT '1';
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 3;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` = 4;
UPDATE `objet_royaume` SET `rang_royaume` = '4' WHERE `id` = 5;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` = 7;
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 8;
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 9;
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 12;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` = 13;
UPDATE `objet_royaume` SET `rang_royaume` = '4' WHERE `id` = 14;
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 16;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` = 17;
UPDATE `objet_royaume` SET `rang_royaume` = '2' WHERE `id` = 19;
UPDATE `objet_royaume` SET `rang_royaume` = '3' WHERE `id` = 20;
UPDATE `objet_royaume` SET `rang_royaume` = '4' WHERE `id` = 21;