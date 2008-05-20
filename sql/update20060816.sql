ALTER TABLE `objet` ADD `achetable` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n';
ALTER TABLE `objet` ADD `stack` TINYINT UNSIGNED NOT NULL AFTER `achetable` ;
INSERT INTO `objet` ( `id` , `nom` , `type` , `prix` , `achetable` , `stack` )
VALUES (
NULL , 'Sang de lapin', 'fabrication', '3', 'n', '10'
), (
NULL , 'Potion de vie mineure', 'potion_vie', '50', 'n', '5'
);
CREATE TABLE `recette` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ingredient` TEXT NOT NULL ,
`resultat` TEXT NOT NULL ,
`difficulte` MEDIUMINT UNSIGNED NOT NULL
);
INSERT INTO `recette` ( `id` , `ingredient` , `resultat` , `difficulte` )
VALUES (
NULL , '4-3', '4-1', '3'
);
CREATE TABLE `perso_recette` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_recette` INT UNSIGNED NOT NULL ,
`id_perso` INT UNSIGNED NOT NULL
);
ALTER TABLE `perso` ADD `craft` INT UNSIGNED NOT NULL DEFAULT '1' AFTER `forge` ;
UPDATE perso SET craft =1, forge =1;
ALTER TABLE `recette` ADD `nom` VARCHAR( 50 ) NOT NULL AFTER `id` ;
ALTER TABLE `quete` ADD `reward` TEXT NOT NULL AFTER `star` ;
ALTER TABLE `objet` ADD `effet` MEDIUMINT NOT NULL ;
ALTER TABLE `objet` ADD `utilisable` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y' AFTER `stack` ;
INSERT INTO `objet` ( `id` , `nom` , `type` , `prix` , `achetable` , `stack` , `utilisable` , `effet` )
VALUES (
NULL , 'Petite Fiole', 'fiole', '5', 'y', '50', 'n', '0'
), (
NULL , 'Grande Fiole', 'fiole', '50', 'y', '30', 'n', '0'
);