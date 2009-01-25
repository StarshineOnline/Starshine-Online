ALTER TABLE `perso` CHANGE `craft` `craft` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `messagerie_thread` ADD `dernier_message` DATETIME NOT NULL ;