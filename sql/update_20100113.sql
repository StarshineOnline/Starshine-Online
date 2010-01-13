ALTER TABLE `arenes` ADD `donj` TINYINT( 1 ) NOT NULL DEFAULT '0';
UPDATE `arenes` SET `nom` = 'Arène de donjon', `donj` = '1' WHERE CONVERT( `arenes`.`nom` USING utf8 ) = 'Arène 1' LIMIT 1 ;
