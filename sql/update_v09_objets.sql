-- On change le champ achetable par un champ lvl_batiment pour uniformiser
ALTER TABLE `accessoire` ADD `lvl_batiment` INT NOT NULL DEFAULT '9';
UPDATE accessoire SET lvl_batiment = 1 WHERE achetable = 'y';
ALTER TABLE `accessoire` DROP `achetable`;
UPDATE accessoire SET lvl_batiment = 9 WHERE achetable = 'n';
ALTER TABLE `accessoire` DROP `achetable`;

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
ALTER TABLE `accessoire` ADD `taille` VARCHAR( 5 ) NOT NULL DEFAULT 'grand' AFTER `nom`;
UPDATE accessoire SET taille = 'moyen' WHERE id <= 9 OR id = 20;
UPDATE accessoire SET type = 'alchimie', description = "Augmente les chances de réussir la réalisation d'une recette alchimique %effet%%." WHERE type = 'fabrication';
UPDATE accessoire SET type = 'reserve' WHERE type = 'rm';
UPDATE accessoire SET type = 'regen_hp_add' WHERE type = 'regen_hp';
UPDATE accessoire SET type = 'regen_mp_add' WHERE type = 'regen_mp';
UPDATE accessoire SET lvl_batiment = 9 WHERE lvl_batiment != 1;