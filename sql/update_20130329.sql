ALTER TABLE `perso` CHANGE `date_creat` `date_creation` INT( 10 ) NOT NULL DEFAULT '0';
ALTER TABLE `joueur` ADD `mdp_jabber` VARCHAR( 40 ) NOT NULL;

ALTER TABLE `batiment` ADD `temps_construction_min` INT NOT NULL AFTER `temps_construction`;
UPDATE batiment SET temps_construction_min = 43200 WHERE temps_construction > 0; -- temps par défaut
UPDATE batiment SET temps_construction_min = 86400 WHERE id = 2;
UPDATE batiment SET temps_construction_min = 172800 WHERE id = 3;
UPDATE batiment SET temps_construction_min = 345600 WHERE id = 4;
UPDATE batiment SET temps_construction_min = 86400 WHERE id = 7;
UPDATE batiment SET temps_construction_min = 86400 WHERE id = 8;
UPDATE batiment SET temps_construction_min = 86400 WHERE id = 9;
UPDATE batiment SET temps_construction_min = 86400 WHERE id = 14;
UPDATE batiment SET temps_construction_min = 172800 WHERE id = 15;
UPDATE batiment SET temps_construction_min = 345600 WHERE id = 16;
UPDATE batiment SET temps_construction_min = 14400 WHERE id = 17;
UPDATE batiment SET temps_construction_min = 21600 WHERE id = 18;
UPDATE batiment SET temps_construction_min = 14400 WHERE id = 20;