-- -*- sql -*-
ALTER TABLE `achievement_type` ADD `strong` BOOL NOT NULL DEFAULT '0',
ADD `color` VARCHAR( 20 ) NULL ;

UPDATE `achievement_type` SET `strong` = '1' WHERE `variable` = 'quest_gob';
UPDATE `achievement_type` SET `strong` = '1' WHERE `variable` = 'kill_gob_king';
UPDATE `achievement_type` SET `strong` = '1', `color` = 'red' WHERE `variable` = 'abomination_mark';
