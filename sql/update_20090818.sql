ALTER TABLE `construction` ADD `date_construction` INT UNSIGNED NOT NULL ;
UPDATE `batiment` SET `cond1` = '2678400' WHERE `batiment`.`id` =11 LIMIT 1 ;
UPDATE `batiment` SET `cond1` = '8035200' WHERE `batiment`.`id` =12 LIMIT 1 ;
ALTER TABLE `royaume` ADD `point_victoire` INT UNSIGNED NOT NULL AFTER `star` ,
ADD `point_victoire_total` INT UNSIGNED NOT NULL AFTER `point_victoire` ;
ALTER TABLE `perso` ADD `reputation` INT UNSIGNED NOT NULL AFTER `honneur` ;