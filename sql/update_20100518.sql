CREATE TABLE IF NOT EXISTS `log_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_joueur` int(10) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
);