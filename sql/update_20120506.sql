
CREATE TABLE IF NOT EXISTS `joueur` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `mdp` varchar(50) DEFAULT NULL,
  `pseudo` varchar(50) DEFAULT '',
  `droits` int(11) NOT NULL DEFAULT '0',
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- elettar a oublie de commit sa table joueur on dirait :D
