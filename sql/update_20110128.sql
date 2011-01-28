CREATE TABLE IF NOT EXISTS `event` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  `statut` SMALLINT NOT NULL ,
  `date_debut` DATE NOT NULL ,
  `date_fin` DATE NULL DEFAULT NULL ,
  `donnees` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE IF NOT EXISTS `event_equipe` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `event` INT( 10 ) NOT NULL ,
  `nom` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  `royaume` INT( 10 ) NULL DEFAULT NULL ,
  `donnees` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE IF NOT EXISTS `event_participant` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `event` INT( 10 ) NOT NULL ,
  `id_perso` INT( 10 ) NOT NULL ,
  `equipe` INT( 10 ) NULL DEFAULT NULL ,
  `donnees` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE IF NOT EXISTS `event_partie` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `event` INT( 10 ) NOT NULL ,
  `statut` SMALLINT NOT NULL ,
  `arene` INT( 10 ) NULL DEFAULT NULL ,
  `heure_sso` SMALLINT NOT NULL ,
  `date_debut` DATE NOT NULL ,
  `date_fin` DATE NULL DEFAULT NULL ,
  `gagnant` INT( 10 ) NOT NULL ,
  `donnees` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
);

ALTER TABLE `arenes_joueurs` CHANGE `id` `id_perso` INT( 11 ) NOT NULL;
ALTER TABLE `arenes_joueurs` DROP PRIMARY KEY;
ALTER TABLE `arenes_joueurs` ADD `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `arenes_joueurs` ADD `event` INT( 10 ) NOT NULL ,
  ADD `partie` INT( 10 ) NOT NULL ,
  ADD `arene` INT( 10 ) NOT NULL ,
  ADD `statut` SMALLINT NOT NULL ,
  ADD `hp` MEDIUMINT( 9 ) NOT NULL ,
  ADD `donnees` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
  
ALTER TABLE `arenes` DROP PRIMARY KEY;
ALTER TABLE `arenes` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;