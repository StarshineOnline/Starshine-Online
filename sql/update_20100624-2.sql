-- Cases spéciales

CREATE TABLE `map_event` (
`x` INT NOT NULL ,
`y` INT NOT NULL ,
`titre` varchar(250) NOT NULL COMMENT 'titre de la case',
`description` VARCHAR( 65000 ) NOT NULL ,
`action` VARCHAR( 250 ) NULL COMMENT 'Titre de l''action (bouton)',
`code` TEXT NULL COMMENT 'code de l''action (eval)',
`sql` TEXT NULL COMMENT 'SQL de l''action (executé tel quel)',
PRIMARY KEY ( `x` , `y` )
) ENGINE = MYISAM COMMENT = 'Cases spéciales - avec description et action' 
;
