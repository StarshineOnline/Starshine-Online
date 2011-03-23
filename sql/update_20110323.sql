ALTER TABLE `sort_jeu` ADD `special` BOOLEAN NOT NULL COMMENT 'Si c''est un sort special, l''affinité ne joue pas';

UPDATE `sort_jeu` SET `special` = '1' WHERE `sort_jeu`.`requis` LIKE 'classe:%';
