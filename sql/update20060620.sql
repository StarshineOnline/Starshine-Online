ALTER TABLE `batiment` ADD `carac` INT UNSIGNED NOT NULL ;
CREATE TABLE `construction` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_batiment` TINYINT UNSIGNED NOT NULL ,
`x` TINYINT UNSIGNED NOT NULL ,
`y` TINYINT UNSIGNED NOT NULL ,
`royaume` TINYINT UNSIGNED NOT NULL ,
`hp` INT UNSIGNED NOT NULL ,
`nom` VARCHAR( 50 ) NOT NULL
);
UPDATE `royaume` SET `nom` = 'Loch''jaden' WHERE `ID` =2 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Kiel''loran' WHERE `ID` =11 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Lordaeris' WHERE `ID` =10 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Empire Vörsh' WHERE `ID` =9 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Voilebrisé' WHERE `ID` =8 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Scytä' WHERE `ID` =7 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Ouldang' WHERE `ID` =6 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Zandalyr' WHERE `ID` =4 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Dorsh''iva' WHERE `ID` =3 LIMIT 1 ;

UPDATE `royaume` SET `nom` = 'Ereth drannor' WHERE `ID` =12 LIMIT 1 ;
ALTER TABLE `royaume` ADD `capitale` VARCHAR( 50 ) NOT NULL AFTER `nom` ;
UPDATE `royaume` SET `capitale` = 'Brisebois' WHERE `ID` =1 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Keleth' WHERE `ID` =2 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Ashen''delor' WHERE `ID` =11 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Malekhive' WHERE `ID` =10 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Narcisse' WHERE `ID` =9 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Songe-éternel ( la cité des morts )' WHERE `ID` =8 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Khazad dorr ( La forge )' WHERE `ID` =7 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Aragonia' WHERE `ID` =6 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Akheva' WHERE `ID` =4 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Drön ( le donjon )' WHERE `ID` =3 LIMIT 1 ;

UPDATE `royaume` SET `capitale` = 'Ereth drannyr ( la citadelle rouge )' WHERE `ID` =12 LIMIT 1 ;

CREATE TABLE `construction_ville` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_royaume` TINYINT UNSIGNED NOT NULL ,
`id_batiment` INT UNSIGNED NOT NULL
);
CREATE TABLE `batiment_ville` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 50 ) NOT NULL ,
`cout` INT UNSIGNED NOT NULL ,
`entretien` INT UNSIGNED NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Ecole de magie', '0', '0', 'ecole_magie'
), (
NULL , 'Ecole de combat', '0', '0', 'ecole_combat'
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Armurerie', '0', '0', 'armurerie'
), (
NULL , 'Forgeron', '0', '0', 'forgeron'
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Ecole de magie avancée', '500', '50', 'ecole_magie'
), (
NULL , 'Ecole de magie expert', '1500', '150', 'ecole_magie'
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Ecole de combat avancée', '500', '50', 'ecole_combat'
), (
NULL , 'Ecole de combat expert', '1500', '150', 'ecole_combat'
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Armurerie de qualité', '1000', '100', 'armurerie'
), (
NULL , 'Armurerie de luxe', '3000', '300', 'armurerie'
);
INSERT INTO `batiment_ville` ( `id` , `nom` , `cout` , `entretien` , `type` )
VALUES (
NULL , 'Forgeron de qualité', '1500', '150', 'forgeron'
), (
NULL , 'Forgeron de luxe', '4500', '450', 'forgeron'
);
ALTER TABLE `batiment_ville` ADD `level` TINYINT UNSIGNED NOT NULL ;
UPDATE `batiment_ville` SET `level` = '1' WHERE `id` =1 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '1' WHERE `id` =2 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '1' WHERE `id` =3 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '1' WHERE `id` =4 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '2' WHERE `id` =5 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '3' WHERE `id` =6 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '2' WHERE `id` =7 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '3' WHERE `id` =8 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '2' WHERE `id` =9 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '3' WHERE `id` =10 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '2' WHERE `id` =11 LIMIT 1 ;

UPDATE `batiment_ville` SET `level` = '3' WHERE `id` =12 LIMIT 1 ;
ALTER TABLE `armure` ADD `lvl_batiment` TINYINT UNSIGNED NOT NULL ;
UPDATE arme SET lvl_batiment = 1;
UPDATE arme SET lvl_batiment = 2 WHERE prix > 1500;
UPDATE arme SET lvl_batiment = 3 WHERE prix > 20000;
UPDATE armure SET lvl_batiment = 1;
UPDATE armure SET lvl_batiment = 2 WHERE prix > 100;
UPDATE armure SET lvl_batiment = 3 WHERE prix > 1500;

UPDATE `monstre` SET `action` = '_6;!' WHERE `id` =15 LIMIT 1 ;

CREATE TABLE `objet_royaume` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 50 ) NOT NULL ,
`prix` INT UNSIGNED NOT NULL ,
`grade` TINYINT UNSIGNED NOT NULL
);

UPDATE `grade` SET `rang` = '5' WHERE `id` =1 LIMIT 1 ;

UPDATE `grade` SET `rang` = '4' WHERE `id` =2 LIMIT 1 ;

UPDATE `grade` SET `rang` = '3' WHERE `id` =3 LIMIT 1 ;

UPDATE `grade` SET `rang` = '2' WHERE `id` =4 LIMIT 1 ;

UPDATE `grade` SET `rang` = '1' WHERE `id` =5 LIMIT 1 ;

UPDATE `grade` SET `rang` = '6' WHERE `id` =6 LIMIT 1 ;

INSERT INTO `objet_royaume` ( `id` , `nom` , `prix` , `grade` )
VALUES (
NULL , 'Drapeau', '100', '2'
), (
NULL , 'Bastion', '1000', '4'
);

ALTER TABLE `objet_royaume` ADD `type` VARCHAR( 50 ) NOT NULL;
ALTER TABLE `objet_royaume` ADD `id_batiment` MEDIUMINT UNSIGNED NOT NULL ;
CREATE TABLE `depot_royaume` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_objet` INT UNSIGNED NOT NULL ,
`id_royaume` TINYINT UNSIGNED NOT NULL
);
ALTER TABLE `sort_combat` ADD `lvl_batiment` TINYINT UNSIGNED NOT NULL ;
ALTER TABLE `sort_jeu` ADD `lvl_batiment` TINYINT UNSIGNED NOT NULL ;
ALTER TABLE `comp_combat` ADD `lvl_batiment` TINYINT UNSIGNED NOT NULL ;
UPDATE sort_combat SET lvl_batiment = 1;
UPDATE sort_combat SET lvl_batiment = 2 WHERE prix > 300;
UPDATE sort_combat SET lvl_batiment = 3 WHERE prix > 1500;
UPDATE sort_jeu SET lvl_batiment = 1;
UPDATE sort_jeu SET lvl_batiment = 2 WHERE prix > 400;
UPDATE sort_jeu SET lvl_batiment = 3 WHERE prix > 2000;
UPDATE comp_combat SET lvl_batiment = 1;
UPDATE comp_combat SET lvl_batiment = 2 WHERE prix > 1500;
UPDATE comp_combat SET lvl_batiment = 3 WHERE prix > 3000;
UPDATE arme SET melee = melee * 2;