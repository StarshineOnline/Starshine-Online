ALTER TABLE `construction` ADD INDEX `xy` ( `x` , `y` ) ;
ALTER TABLE `construction` ADD INDEX ( `id_batiment` ) ;
ALTER TABLE `construction` ADD INDEX ( `royaume` ) ;

ALTER TABLE `placement` ADD INDEX `xy` ( `x` , `y` ) ;
ALTER TABLE `placement` ADD INDEX ( `id_batiment` ) ;
ALTER TABLE `placement` ADD INDEX ( `royaume` ) ;

ALTER TABLE `perso` ADD INDEX `xy` ( `x` , `y` ) ;

ALTER TABLE `pnj` ADD INDEX `xy` ( `x` , `y` ) ;

ALTER TABLE `map_monstre` DROP INDEX `x` ;
ALTER TABLE `map_monstre` DROP INDEX `y` ;
ALTER TABLE `map_monstre` DROP INDEX `x_2` , ADD INDEX `xy` ( `x` , `y` ) ;