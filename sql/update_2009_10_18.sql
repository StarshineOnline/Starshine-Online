-- Modification de batiment : ajout de la colone upgade
ALTER TABLE `batiment` ADD `upgrade` INT NOT NULL DEFAULT '0' AFTER `bonus7` ;

-- bourgs, il se suivent
UPDATE `batiment` SET `upgrade` = '11' WHERE `batiment`.`id` =10 LIMIT 1 ;
UPDATE `batiment` SET `upgrade` = '12' WHERE `batiment`.`id` =11 LIMIT 1 ;

-- extracteurs, ils se suivent
UPDATE `batiment` SET `upgrade` = '25' WHERE `id` =24 LIMIT 1 ;
UPDATE `batiment` SET `upgrade` = '26' WHERE `id` =25 LIMIT 1 ;

-- mines 1, ils sont dans l'ordre
UPDATE `batiment` SET `upgrade` = `id` + 8 WHERE `id` >= 27 and `id` <= 34 ;

-- mines 2, ils sont dans l'ordre inverse
UPDATE `batiment` SET `upgrade` = `id` + 2 * (43 - `id`) - 1 WHERE `id` >= 35 and `id` <= 42 ;
