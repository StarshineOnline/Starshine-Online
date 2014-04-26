ALTER TABLE `perso` DROP INDEX `ID`;

ALTER TABLE `achievement` ADD INDEX ( `id_perso` ) ;

ALTER TABLE `achievement` ADD INDEX ( `id_achiev` ) ;

ALTER TABLE `achievement` CHANGE `id_perso` `id_perso` INT( 11 ) UNSIGNED NOT NULL ;