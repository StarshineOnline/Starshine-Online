UPDATE `bonus` SET competence_requis = 'artisanat' WHERE competence_requis = 'craft';
CREATE TABLE `starshine_test`.`terrain_batiment` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 100 ) NOT NULL ,
`description` TEXT NOT NULL ,
`type` VARCHAR( 100 ) NOT NULL ,
`effet` VARCHAR( 100 ) NOT NULL ,
`nb_case` TINYINT UNSIGNED NOT NULL ,
`prix` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);
ALTER TABLE `royaume` ADD `alchimie` INT UNSIGNED NOT NULL ;
ALTER TABLE `craft_recette` ADD `royaume_alchimie` INT UNSIGNED NOT NULL ;
ALTER TABLE `craft_recette` ADD `prix` INT UNSIGNED NOT NULL ;