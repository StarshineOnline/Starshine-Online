ALTER TABLE `craft_instrument` ADD `requis` INT UNSIGNED NOT NULL ;
ALTER TABLE `craft_instrument` ADD `pa` INT UNSIGNED NOT NULL ,
ADD `mp` INT UNSIGNED NOT NULL ,
ADD `prix` INT UNSIGNED NOT NULL ,
ADD `alchimie` INT UNSIGNED NOT NULL ;
ALTER TABLE `craft_instrument` ADD `type` VARCHAR( 50 ) NOT NULL AFTER `nom` ;
CREATE TABLE `terrain_laboratoire` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_laboratoire` INT UNSIGNED NOT NULL ,
`id_instrument` INT UNSIGNED NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
);