ALTER TABLE `achievement_type` ADD `secret` TINYINT NOT NULL DEFAULT '0';

UPDATE `achievement_type` SET `secret` = '1' WHERE `id` = 25;
UPDATE `achievement_type` SET `secret` = '1' WHERE `id` = 34;
UPDATE `achievement_type` SET `secret` = '1' WHERE `id` = 35;
UPDATE `achievement_type` SET `secret` = '1' WHERE `id` = 36;
UPDATE `achievement_type` SET `secret` = '1' WHERE `id` = 45;
