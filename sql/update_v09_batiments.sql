-- Modification de la table
ALTER TABLE `buff_batiment` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `buff_batiment` CHANGE `date_fin` `fin` INT(11) NOT NULL;
ALTER TABLE `buff_batiment` ADD `effet2` INT(11) NOT NULL DEFAULT '0' , ADD `nom` VARCHAR(50) NOT NULL , ADD `description` TEXT NOT NULL , ADD `debuff` BOOLEAN NOT NULL ;

UPDATE comp_jeu SET cible = 7 WHERE type LIKE 'sabotage';