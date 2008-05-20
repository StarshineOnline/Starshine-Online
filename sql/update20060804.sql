ALTER TABLE `perso` CHANGE `action` `action_a` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `perso` CHANGE `action_a` `action_a` INT UNSIGNED NOT NULL;
ALTER TABLE `perso` ADD `action_d` INT UNSIGNED NOT NULL AFTER `action_a` ;
CREATE TABLE `action_perso` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 50 ) NOT NULL ,
`action` TEXT NOT NULL ,
`mode` ENUM( 's', 'a' ) NOT NULL
);
ALTER TABLE `action_perso` ADD `id_joueur` INT UNSIGNED NOT NULL AFTER `id` ;