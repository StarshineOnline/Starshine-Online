CREATE TABLE IF NOT EXISTS `map_sound_zone` (
  `type` varchar(50) DEFAULT NULL,
  `x1` int(11) DEFAULT NULL,
  `y1` int(11) DEFAULT NULL,
  `x2` int(11) DEFAULT NULL,
  `y2` int(11) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  UNIQUE KEY `x1` (`x1`,`y1`,`x2`,`y2`),
  KEY `x1_2` (`x1`),
  KEY `y1` (`y1`),
  KEY `x2` (`x2`),
  KEY `y2` (`y2`),
  KEY `x1_3` (`x1`,`y1`),
  KEY `x2_2` (`x2`,`y2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

