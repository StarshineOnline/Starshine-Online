CREATE TABLE `craft_ingredient` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 50 ) NOT NULL ,
`description` TEXT NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `craft_instrument` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 50 ) NOT NULL ,
`description` TEXT NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `craft_recette` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 50 ) NOT NULL ,
`description` TEXT NOT NULL ,
`pa` TINYINT UNSIGNED NOT NULL ,
`mp` MEDIUMINT UNSIGNED NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`difficulte` MEDIUMINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `craft_recette_ingredient` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_recette` INT UNSIGNED NOT NULL ,
`id_ingredient` INT UNSIGNED NOT NULL ,
`nombre` TINYINT UNSIGNED NOT NULL ,
`secret` BOOL NOT NULL ,
`effet` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `starshine_test`.`craft_recette_instrument` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_recette` INT UNSIGNED NOT NULL ,
`id_instrument` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);