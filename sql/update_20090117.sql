 ALTER TABLE `craft_recette_instrument` CHANGE `id_instrument` `type` VARCHAR( 50 ) NOT NULL;
 CREATE TABLE `craft_recipient` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_recette` INT UNSIGNED NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `terrain_chantier` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_terrain` INT UNSIGNED NOT NULL ,
`id_batiment` INT UNSIGNED NOT NULL ,
`point` INT UNSIGNED NOT NULL ,
`star_point` INT UNSIGNED NOT NULL,
`upgrade_id_construction` INT UNSIGNED NOT NULL,
PRIMARY KEY ( `id` )
);
ALTER TABLE `terrain_batiment` ADD `point_structure` INT UNSIGNED NOT NULL ;