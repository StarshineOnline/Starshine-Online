-- -*- sql -*-
-- Passage de map en X/Y
-- Ajout des colonnes x et y et peuplement
ALTER TABLE `map` ADD `x` INT( 5 ) NOT NULL FIRST ,
									ADD `y` INT( 5 ) NOT NULL AFTER `x` ;
UPDATE `map` SET `x` = `id` - (FLOOR(`id` / 1000) * 1000),
								 `y` = FLOOR(`id` / 1000);
-- Virer l'auto_increment
ALTER TABLE `map` CHANGE `id` `id` MEDIUMINT( 9 ) NOT NULL  ;
-- Virer la Primary key et la redefinir
ALTER TABLE `map` DROP PRIMARY KEY ;
ALTER TABLE `map` ADD PRIMARY KEY(`x`, `y`);
-- Virer id
-- TODO ALTER TABLE `map` DROP `id`
-- Mettre des index sur X et Y
ALTER TABLE `map` ADD INDEX ( `x` ) ;
ALTER TABLE `map` ADD INDEX ( `y` ) ;

-- Ajouts d'infos au zonage atmosphérique
ALTER TABLE `map_zone` ADD `dx` INT NOT NULL COMMENT 'décalage X',
ADD `dy` INT NOT NULL COMMENT 'décalage Y';
-- On indexe a fond, c'est pas une table qui bouge
ALTER TABLE `map_zone` ADD INDEX `x` ( `x1` , `x2` ) ;
ALTER TABLE `map_zone` ADD INDEX `y` ( `y1` , `y2` ) ;
ALTER TABLE `map_zone` ADD INDEX ( `x1` ) ;
ALTER TABLE `map_zone` ADD INDEX ( `y1` ) ;
ALTER TABLE `map_zone` ADD INDEX ( `x2` ) ;
ALTER TABLE `map_zone` ADD INDEX ( `y2` ) ;
