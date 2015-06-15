
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

-- Nouveaux accessoires
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre bleue", "grand", "Donne +%effet% MP a chaque régénération de point de mana.", 0, "regen_mp_add", 6, 15000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre bleue polie", "grand", "Donne +%effet% MP a chaque régénération de point de mana.", 13, "regen_mp_add", 9, 15000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre bleue taillée", "grand", "Donne +%effet% MP a chaque régénération de point de mana.", 14, "regen_mp_add", 12, 15000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre de feu", "petit", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 10, "chances_feu", 2, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre de feu polie", "petit", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 13, "chances_feu", 4, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre de feu taillée", "petit", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 16, "chances_feu", 6, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu", "moyen", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 10, "chances_feu", 5, 1300, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu polie", "moyen", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 13, "chances_feu", 10, 1300, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu taillée", "moyen", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 16, "chances_feu", 15, 1300, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre de feu", "grand", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 10, "chances_feu", 10, 6500, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre de feu polie", "grand", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 13, "chances_feu", 20, 6500, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre de feu taillée", "grand", "Augmente de %effet%% les chances de lancer et toucher avec des sorts de feu.", 16, "chances_feu", 30, 6500, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu rougeoyante", "grand", "Augmente de %effet les dégâts des sorts de feu.", 8, "degats_feu", 1, 30000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu incandescante", "grand", "Augmente de %effet les dégâts des sorts de feu.", 13, "degats_feu", 2, 30000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre de feu flamboyante", "grand", "Augmente de %effet les dégâts des sorts de feu.", 16, "degats_feu", 3, 30000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur de sort", "petit", "Augmente les chances de critiques magiques de %effet%%.", 8, "pot_critique_magique", 15, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur de sort supérieur", "petit", "Augmente les chances de critiques magiques de %effet%%.", 11, "pot_critique_magique", 23, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur de sort parfait", "petit", "Augmente les chances de critiques magiques de %effet%%.", 14, "pot_critique_magique", 30, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Catalyseur de sort", "moyen", "Augmente les chances de critiques magiques de %effet%%.", 8, "pot_critique_magique", 25, 5000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Catalyseur de sort supérieur", "moyen", "Augmente les chances de critiques magiques de %effet%%.", 11, "pot_critique_magique", 40, 5000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Catalyseur de sort parfait", "moyen", "Augmente les chances de critiques magiques de %effet%%.", 14, "pot_critique_magique", 50, 5000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur de sort", "grand", "Augmente les chances de critiques magiques de %effet%%.", 8, "pot_critique_magique", 35, 10000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur de sort supérieur", "grand", "Augmente les chances de critiques magiques de %effet%%.", 11, "pot_critique_magique", 55, 10000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur de sort parfait", "grand", "Augmente les chances de critiques magiques de %effet%%.", 14, "pot_critique_magique", 70, 10000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit symbole magique en bronze", "petit", "Aumgmente les dégâts de critiques magiques de %effet%%.", 10, "mult_critique_magique", 4, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit symbole magique en argent", "petit", "Aumgmente les dégâts de critiques magiques de %effet%%.", 13, "mult_critique_magique", 8, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit symbole magique en or", "petit", "Aumgmente les dégâts de critiques magiques de %effet%%.", 16, "mult_critique_magique", 12, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Symbole magique en bronze", "moyen", "Aumgmente les dégâts de critiques magiques de %effet%%.", 10, "mult_critique_magique", 10, 2500, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Symbole magique en argent", "moyen", "Aumgmente les dégâts de critiques magiques de %effet%%.", 13, "mult_critique_magique", 20, 2500, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Symbole magique en or", "moyen", "Aumgmente les dégâts de critiques magiques de %effet%%.", 16, "mult_critique_magique", 30, 2500, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand symbole magique en bronze", "grand", "Aumgmente les dégâts de critiques magiques de %effet%%.", 10, "mult_critique_magique", 20, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand symbole magique en argent", "grand", "Aumgmente les dégâts de critiques magiques de %effet%%.", 13, "mult_critique_magique", 30, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand symbole magique en or", "grand", "Aumgmente les dégâts de critiques magiques de %effet%%.", 16, "mult_critique_magique", 40, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur", "petit", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 0, "alchimie", 2, 200, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur de qualité", "petit", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 12, "alchimie", 3, 200, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit catalyseur parfait", "petit", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 14, "alchimie", 5, 200, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur", "grand", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 0, "alchimie", 10, 1000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur de qualité", "grand", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 12, "alchimie", 20, 1000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand catalyseur parfait", "grand", "Augmente les chances de réussir la réalisation d\'une recette alchimique %effet%%.", 14, "alchimie", 25, 1000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Tisonnier", "petit", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 0, "forge", 2, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Tisonnier de qualité", "petit", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 10, "forge", 3, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Tisonnier parfait", "petit", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 13, "forge", 5, 500, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Soufflet", "moyen", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 0, "forge", 5, 2000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Soufflet de qualité", "moyen", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 10, "forge", 10, 2000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Soufflet parfait", "moyen", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 13, "forge", 15, 2000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Marteau", "grand", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 0, "forge", 10, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Marteau de qualité", "grand", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 10, "forge", 20, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Marteau parfait", "grand", "Augmente les chances de réussir la réalisation une action de forge de %effet%%.", 13, "forge", 25, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Équerre", "petit", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 0, "", 2, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Équerre de qualité", "petit", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 12, "", 3, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Équerre parfait", "petit", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 16, "", 5, 1000, 2, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Truelle", "moyen", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 0, "", 5, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Truelle de qualité", "moyen", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 12, "", 10, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Truelle parfait", "moyen", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 16, "", 15, 5000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Maillet", "grand", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 0, "", 10, 20000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Maillet de qualité", "grand", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 12, "", 15, 20000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Maillet parfait", "grand", "Augmente les chances de réussir la réalisation une action d\'architecture de %effet%%.", 16, "", 20, 20000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit rubis", "petit", "Augmente les points de vie maximaux de %effet%.", 0, "hp_max", 2, 100, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit rubis polie", "petit", "Augmente les points de vie maximaux de %effet%.", 11, "hp_max", 3, 100, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit rubis taillé", "petit", "Augmente les points de vie maximaux de %effet%.", 14, "hp_max", 5, 100, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rubis", "moyen", "Augmente les points de vie maximaux de %effet%.", 8, "hp_max", 10, 300, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rubis polie", "moyen", "Augmente les points de vie maximaux de %effet%.", 12, "hp_max", 20, 300, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rubis taillé", "moyen", "Augmente les points de vie maximaux de %effet%.", 16, "hp_max", 30, 300, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand rubis", "grand", "Augmente les points de vie maximaux de %effet%.", 0, "hp_max", 20, 6000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand rubis polie", "grand", "Augmente les points de vie maximaux de %effet%.", 13, "hp_max", 40, 6000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand rubis taillé", "grand", "Augmente les points de vie maximaux de %effet%.", 16, "hp_max", 60, 6000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit spahir", "petit", "Augmente les points de mana maximaux de %effet%.", 0, "mp_max", 2, 300, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit spahir polie", "petit", "Augmente les points de mana maximaux de %effet%.", 12, "mp_max", 3, 300, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit spahir taillé", "petit", "Augmente les points de mana maximaux de %effet%.", 14, "mp_max", 5, 300, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Spahir", "moyen", "Augmente les points de mana maximaux de %effet%.", 8, "mp_max", 5, 900, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Spahir polie", "moyen", "Augmente les points de mana maximaux de %effet%.", 13, "mp_max", 15, 900, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Spahir taillé", "moyen", "Augmente les points de mana maximaux de %effet%.", 17, "mp_max", 25, 900, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand spahir", "grand", "Augmente les points de mana maximaux de %effet%.", 9, "mp_max", 15, 9000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand spahir polie", "grand", "Augmente les points de mana maximaux de %effet%.", 13, "mp_max", 25, 9000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand spahir taillé", "grand", "Augmente les points de mana maximaux de %effet%.", 16, "mp_max", 35, 9000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau", "petit", "Augmente les chances de dresser une bête de %effet%%.", 8, "dressage_bete", 2, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau de qualité", "petit", "Augmente les chances de dresser une bête de %effet%%.", 12, "dressage_bete", 3, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau parfait", "petit", "Augmente les chances de dresser une bête de %effet%%.", 16, "dressage_bete", 4, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Friandises", "moyen", "Augmente les chances de dresser une bête de %effet%%.", 0, "dressage_bete", 5, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Friandises appaitissantes", "moyen", "Augmente les chances de dresser une bête de %effet%%.", 11, "dressage_bete", 7, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Fraindises de qualité", "moyen", "Augmente les chances de dresser une bête de %effet%%.", 13, "dressage_bete", 10, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Leurre", "grand", "Augmente les chances de dresser une bête de %effet%%.", 7, "dressage_bete", 8, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Leurre de qualité", "grand", "Augmente les chances de dresser une bête de %effet%%.", 12, "dressage_bete", 12, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Leurre parfait", "grand", "Augmente les chances de dresser une bête de %effet%%.", 14, "dressage_bete", 15, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Verroterie\n", "petit", "Augmente les chances de dresser un humanoïde de %effet%%.", 7, "dressage_humanoide", 2, 700, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Verroterie de qualité", "petit", "Augmente les chances de dresser un humanoïde de %effet%%.", 11, "dressage_humanoide", 3, 700, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Verroterie parfait", "petit", "Augmente les chances de dresser un humanoïde de %effet%%.", 13, "dressage_humanoide", 4, 700, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rations\n", "moyen", "Augmente les chances de dresser un humanoïde de %effet%%.", 0, "dressage_humanoide", 5, 4000, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rations de qualité", "moyen", "Augmente les chances de dresser un humanoïde de %effet%%.", 12, "dressage_humanoide", 7, 4000, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Rations parfait", "moyen", "Augmente les chances de dresser un humanoïde de %effet%%.", 16, "dressage_humanoide", 10, 4000, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Jouet\n", "grand", "Augmente les chances de dresser un humanoïde de %effet%%.", 7, "dressage_humanoide", 8, 15000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Jouet attrayant\n", "grand", "Augmente les chances de dresser un humanoïde de %effet%%.", 12, "dressage_humanoide", 12, 15000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Jouet addictif", "grand", "Augmente les chances de dresser un humanoïde de %effet%%.", 14, "dressage_humanoide", 15, 15000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau magique", "petit", "Augmente les chances de dresser une créature magique de %effet%%.", 7, "dressage_magique", 2, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau magique de qualité", "petit", "Augmente les chances de dresser une créature magique de %effet%%.", 11, "dressage_magique", 3, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Appeau magique parfait", "petit", "Augmente les chances de dresser une créature magique de %effet%%.", 14, "dressage_magique", 4, 1000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Glyphe de controle", "moyen", "Augmente les chances de dresser une créature magique de %effet%%.", 8, "dressage_magique", 5, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Glyphe de controle supérieur", "moyen", "Augmente les chances de dresser une créature magique de %effet%%.", 12, "dressage_magique", 7, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Glyphe de controle parfait", "moyen", "Augmente les chances de dresser une créature magique de %effet%%.", 14, "dressage_magique", 10, 5000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Attracteur magique", "grand", "Augmente les chances de dresser une créature magique de %effet%%.", 8, "dressage_magique", 8, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Attracteur magique supérieur", "grand", "Augmente les chances de dresser une créature magique de %effet%%.", 13, "dressage_magique", 12, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Attracteur magique parfait", "grand", "Augmente les chances de dresser une créature magique de %effet%%.", 17, "dressage_magique", 15, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sachet en lin", "petit", "Augmente l\'encoimbrement maximal de %effet", 8, "encombrement", 5, 400, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sachet en peau", "petit", "Augmente l\'encoimbrement maximal de %effet", 12, "encombrement", 10, 400, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sachet en cuir", "petit", "Augmente l\'encoimbrement maximal de %effet", 17, "encombrement", 15, 400, 1, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sacoche en lin", "moyen", "Augmente l\'encoimbrement maximal de %effet", 0, "encombrement", 20, 2000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sacoche en peau", "moyen", "Augmente l\'encoimbrement maximal de %effet", 13, "encombrement", 30, 2000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sacoche en cuir", "moyen", "Augmente l\'encoimbrement maximal de %effet", 16, "encombrement", 40, 2000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sac en lin", "grand", "Augmente l\'encoimbrement maximal de %effet", 0, "encombrement", 40, 15000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sac en peau", "grand", "Augmente l\'encoimbrement maximal de %effet", 12, "encombrement", 60, 15000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Sac en cuir", "grand", "Augmente l\'encoimbrement maximal de %effet", 14, "encombrement", 80, 15000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre à aiguiser", "petit", "Aumgmente les dégâts de critiques physiques de %effet%%.", 0, "mult_crit_phys", 1, 600, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre à aiguiser de qualité", "petit", "Aumgmente les dégâts de critiques physiques de %effet%%.", 12, "mult_crit_phys", 2, 600, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petite pierre à aiguiser parfaite", "petit", "Aumgmente les dégâts de critiques physiques de %effet%%.", 16, "mult_crit_phys", 3, 600, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre à aiguiser", "moyen", "Aumgmente les dégâts de critiques physiques de %effet%%.", 0, "mult_crit_phys", 2, 4000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre à aiguiser de qualité", "moyen", "Aumgmente les dégâts de critiques physiques de %effet%%.", 12, "mult_crit_phys", 4, 4000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Pierre à aiguiser parfaite", "moyen", "Aumgmente les dégâts de critiques physiques de %effet%%.", 16, "mult_crit_phys", 6, 4000, 4, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre à aiguiser", "grand", "Aumgmente les dégâts de critiques physiques de %effet%%.", 0, "mult_crit_phys", 3, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre à aiguiser de qualité", "grand", "Aumgmente les dégâts de critiques physiques de %effet%%.", 12, "mult_crit_phys", 6, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grande pierre à aiguiser parfaite", "grand", "Aumgmente les dégâts de critiques physiques de %effet%%.", 16, "mult_crit_phys", 9, 20000, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit topaze", "petit", "Donne %effet% de chances de réduire le blocage adverse de 1.", 0, "red_blocage", 1, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit topaze polie", "petit", "Donne %effet% de chances de réduire le blocage adverse de 1.", 13, "red_blocage", 2, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Petit topaze taillé", "petit", "Donne %effet% de chances de réduire le blocage adverse de 1.", 16, "red_blocage", 3, 150, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Topaze", "moyen", "Donne %effet% de chances de réduire le blocage adverse de 1.", 0, "red_blocage", 5, 900, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Topaze polie", "moyen", "Donne %effet% de chances de réduire le blocage adverse de 1.", 13, "red_blocage", 7, 900, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Topaze taillé", "moyen", "Donne %effet% de chances de réduire le blocage adverse de 1.", 16, "red_blocage", 9, 900, 9, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand topaze", "grand", "Donne %effet% de chances de réduire le blocage adverse de 1.", 0, "red_blocage", 10, 3000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand topaze polie", "grand", "Donne %effet% de chances de réduire le blocage adverse de 1.", 13, "red_blocage", 15, 3000, 3, 5);
INSERT INTO accessoire (nom, taille, description, puissance, type, effet, prix, lvl_batiment, encombrement) VALUES ("Grand topaze taillé", "grand", "Donne %effet% de chances de réduire le blocage adverse de 1.", 16, "red_blocage", 20, 3000, 3, 5);