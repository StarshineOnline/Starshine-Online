
ALTER TABLE `log_connexion` CHANGE `id_joueur` `id_perso` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `log_connexion` ADD `id_joueur` INT NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `log_connexion` ADD `cache_info` BOOLEAN NULL DEFAULT NULL;
ALTER TABLE `log_connexion` ADD `wsid` VARCHAR(50) NULL DEFAULT NULL;