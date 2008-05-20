-- On ajoute une colonne image à batiment
ALTER TABLE `batiment` ADD `image` VARCHAR( 50 ) NOT NULL ;
-- On y mets ce qu'on peut (suivant ce qui a été construit)
UPDATE `batiment`, `construction` SET `batiment`.`image`=`construction`.`image` WHERE `batiment`.`id`=`construction`.`id_batiment` ;
-- On y mets à jour des trucs à la main, quelque fois que
UPDATE `batiment` SET `image` = 'drapeau' WHERE `batiment`.`id` = 5 LIMIT 1;
UPDATE `batiment` SET `image` = 'tour_de_guet' WHERE `batiment`.`id` = 6 LIMIT 1;
UPDATE `batiment` SET `image` = 'tour_de_garde' WHERE `batiment`.`id` = 7 LIMIT 1;
UPDATE `batiment` SET `image` = 'tour_de_mage' WHERE `batiment`.`id` = 8 LIMIT 1;
UPDATE `batiment` SET `image` = 'tour_archer' WHERE `batiment`.`id` = 9 LIMIT 1;
UPDATE `batiment` SET `image` = 'bourgade' WHERE `batiment`.`id` = 10 LIMIT 1;
UPDATE `batiment` SET `image` = 'petit_bourg' WHERE `batiment`.`id` = 11 LIMIT 1;
UPDATE `batiment` SET `image` = 'bourg' WHERE `batiment`.`id` = 12 LIMIT 1;
UPDATE `batiment` SET `image` = 'palissade' WHERE `batiment`.`id` = 13 LIMIT 1;
UPDATE `batiment` SET `image` = 'mur' WHERE `batiment`.`id` = 14 LIMIT 1;
UPDATE `batiment` SET `image` = 'muraille' WHERE `batiment`.`id` = 15 LIMIT 1;
UPDATE `batiment` SET `image` = 'grande_muraille' WHERE `batiment`.`id` = 16 LIMIT 1;
UPDATE `batiment` SET `image` = 'belier' WHERE `batiment`.`id` = 17 LIMIT 1;
UPDATE `batiment` SET `image` = 'catapulte' WHERE `batiment`.`id` = 18 LIMIT 1;
UPDATE `batiment` SET `image` = 'trebuchet' WHERE `batiment`.`id` = 19 LIMIT 1;
UPDATE `batiment` SET `image` = 'baliste' WHERE `batiment`.`id` = 20 LIMIT 1;
-- On enlève la colonne image de construction
ALTER TABLE `construction` DROP `image` ;
