CREATE TABLE IF NOT EXISTS `combats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attaquant` int(11) NOT NULL,
  `defenseur` int(11) NOT NULL,
  `combat` text COLLATE utf8_unicode_ci NOT NULL,
  `id_journal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);