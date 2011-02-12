ALTER TABLE `event_partie` CHANGE `date_debut` `heure_debut` INT( 10 ) NOT NULL ,
CHANGE `date_fin` `heure_fin` INT( 10 ) NULL DEFAULT NULL;
ALTER TABLE `arenes` ADD `positions` TEXT NOT NULL