CREATE TABLE IF NOT EXISTS `arenes_oldbuff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT '',
  `effet` float NOT NULL DEFAULT '0',
  `effet2` int(11) NOT NULL DEFAULT '0',
  `id_perso` int(11) NOT NULL DEFAULT '0',
  `fin` int(11) NOT NULL DEFAULT '0',
  `duree` int(10) unsigned NOT NULL,
  `nom` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `debuff` binary(1) NOT NULL DEFAULT '0',
  `supprimable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
);
