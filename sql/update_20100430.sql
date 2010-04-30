 CREATE TABLE `map_zone` (
`type` VARCHAR( 50 ) NOT NULL ,
`x1` INT NOT NULL ,
`y1` INT NOT NULL ,
`x2` INT NOT NULL ,
`y2` INT NOT NULL ,
PRIMARY KEY ( `x1` , `y1` , `x2` , `y2` )
) ENGINE = MYISAM COMMENT = 'Zones de temps sur la map' ;
