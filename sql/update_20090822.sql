ALTER TABLE `batiment_ville` ADD `hp` INT UNSIGNED NOT NULL ;
ALTER TABLE `royaume` ADD `roi` INT UNSIGNED NOT NULL ,
ADD `ministre_economie` INT UNSIGNED NOT NULL ,
ADD `ministre_militaire` INT UNSIGNED NOT NULL ,
ADD `capitale_hp` INT UNSIGNED NOT NULL ;
ALTER TABLE `construction_ville` ADD `hp` INT UNSIGNED NOT NULL ;
UPDATE `batiment_ville` SET `hp` = '1000' WHERE `batiment_ville`.`id` = 1 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '1000' WHERE `batiment_ville`.`id` = 2 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '1000' WHERE `batiment_ville`.`id` = 3 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '1000' WHERE `batiment_ville`.`id` = 4 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '2000' WHERE `batiment_ville`.`id` = 5 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '2000' WHERE `batiment_ville`.`id` = 6 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '2000' WHERE `batiment_ville`.`id` = 7 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '4000' WHERE `batiment_ville`.`id` = 8 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '2000' WHERE `batiment_ville`.`id` = 9 LIMIT 1; UPDATE `batiment_ville` SET `hp` = '4000' WHERE `batiment_ville`.`id` = 10 LIMIT 1;
ALTER TABLE `construction` ADD `point_victoire` TINYINT UNSIGNED NOT NULL ;
ALTER TABLE `batiment` ADD `point_victoire` TINYINT UNSIGNED NOT NULL ;
ALTER TABLE `placement` ADD `point_victoire` TINYINT UNSIGNED NOT NULL ;
UPDATE `batiment` SET `point_victoire` = '1' WHERE `batiment`.`id` =1 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '2' WHERE `batiment`.`id` =2 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '4' WHERE `batiment`.`id` =3 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '8' WHERE `batiment`.`id` =4 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '2' WHERE `batiment`.`id` =10 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '4' WHERE `batiment`.`id` =11 LIMIT 1 ;
UPDATE `batiment` SET `point_victoire` = '8' WHERE `batiment`.`id` =12 LIMIT 1 ;
CREATE TABLE `elections` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_royaume` TINYINT UNSIGNED NOT NULL ,
`date` INT UNSIGNED NOT NULL ,
`type` ENUM( 'universel', 'nomination' ) NOT NULL ,
PRIMARY KEY ( `id` )
);
CREATE TABLE `revolution` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_royaume` TINYINT UNSIGNED NOT NULL ,
`date` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);
ALTER TABLE `candidat` ADD `id_election` INT UNSIGNED NOT NULL ,
ADD `id_ministre_economie` INT UNSIGNED NOT NULL ,
ADD `id_ministre_militaire` INT UNSIGNED NOT NULL ,
ADD `duree` INT UNSIGNED NOT NULL ,
ADD `type` ENUM( 'universel', 'nomination' ) NOT NULL ;
ALTER TABLE `vote` ADD `id_election` INT UNSIGNED NOT NULL ;