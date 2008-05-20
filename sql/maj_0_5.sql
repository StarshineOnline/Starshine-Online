CREATE TABLE `echange` (
`id_echange` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_j1` INT UNSIGNED NOT NULL ,
`id_j2` INT UNSIGNED NOT NULL ,
`statut` VARCHAR( 20 ) NOT NULL ,
`date_debut` INT UNSIGNED NOT NULL ,
`date_fin` INT UNSIGNED NOT NULL ,
`message_j1` TEXT NOT NULL ,
`message_j2` TEXT NOT NULL
);
CREATE TABLE `echange_objet` (
`id_echange_objet` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_echange` INT UNSIGNED NOT NULL ,
`id_j` INT UNSIGNED NOT NULL ,
`type` VARCHAR( 10 ) NOT NULL ,
`objet` VARCHAR( 10 ) NOT NULL
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '3', 'survie_humanoide', '100', 'yes'
), (
NULL , 'assassin', 'survie_humanoide', '200', 'no'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '8', 'survie_bete', '100', 'yes'
), (
NULL , '11', 'survie_bete', '200', 'no'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '14', 'survie_magique', '200', 'no'
), (
NULL , '13', 'survie_magique', '200', 'no'
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '8', 'survie_magique', '100', 'yes'
), (
NULL , '15', 'survie_magique', '100', 'yes'
);
ALTER TABLE `arme` ADD `image` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `placement` ADD `debut_placement` INT UNSIGNED NOT NULL AFTER `royaume` ;
ALTER TABLE `comp_combat` ADD `duree` TINYINT UNSIGNED NOT NULL AFTER `effet2` ;
ALTER TABLE `comp_combat` ADD `effet3` TINYINT UNSIGNED NOT NULL AFTER `effet2` ;