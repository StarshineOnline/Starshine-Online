CREATE TABLE IF NOT EXISTS `objet_pet` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `dressage` mediumint(8) NOT NULL,
  `degat` tinyint(3) NOT NULL,
  `distance_tir` tinyint(3) NOT NULL,
  `mains` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `PP` mediumint(8) NOT NULL,
  `PM` mediumint(8) NOT NULL,
  `effet` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `prix` int(10) NOT NULL,
  `lvl_batiment` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `perso` ADD `inventaire_pet` TEXT NOT NULL AFTER `inventaire`;
