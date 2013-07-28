CREATE TABLE IF NOT EXISTS `monster_quote` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `id_monstre` int(11) NOT NULL,
  `rarete` int(3) NOT NULL,
  `quote` varchar(4096) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Dernières paroles des monstres';
