CREATE TABLE `echange_royaume` (
`id_echange` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_r1` TINYINT NOT NULL ,
`id_r2` TINYINT NOT NULL ,
`statut` VARCHAR( 20 ) NOT NULL ,
`date_fin` INT( 10 ) NOT NULL
);

CREATE TABLE `echange_ressource_royaume` (
`id_echange_ressource` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_echange` INT( 10 ) NOT NULL ,
`id_r` TINYINT NOT NULL ,
`type` VARCHAR( 20 ) NOT NULL ,
`nombre` INT( 10 ) NOT NULL
);
