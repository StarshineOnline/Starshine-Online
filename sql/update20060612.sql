ALTER TABLE `royaume` ADD `diplo_time` TEXT NOT NULL ;
CREATE TABLE `diplomatie_demande` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`diplo` TINYINT UNSIGNED NOT NULL ,
`royaume_demande` VARCHAR( 50 ) NOT NULL ,
`royaume_recois` VARCHAR( 50 ) NOT NULL ,
`stars` INT UNSIGNED NOT NULL
);
a:12:{s:4:"race";s:0:"";s:7:"barbare";s:0:"";s:8:"elfebois";s:0:"";s:8:"elfehaut";s:0:"";s:6:"humain";s:0:"";s:10:"humainnoir";s:0:"";s:10:"mortvivant";s:0:"";s:4:"nain";s:0:"";s:3:"orc";s:0:"";s:9:"scavenger";s:0:"";s:5:"troll";s:0:"";s:7:"vampire";s:0:"";}