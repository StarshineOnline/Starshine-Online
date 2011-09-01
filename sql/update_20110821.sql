CREATE TABLE IF NOT EXISTS `maree` (
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`x`,`y`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de gestion des marées';

CREATE TABLE `calendrier` (
`date` DATETIME NOT NULL ,
`script` VARCHAR( 255 ) NULL ,
`eval` TEXT NULL ,
`sql` TEXT NULL ,
`next` DATETIME NULL
) ENGINE = INNODB COMMENT = 'Calendrier définissant diverses actions automatisées';

