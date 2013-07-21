CREATE TABLE IF NOT EXISTS `comp_favoris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_comp` int(11) NOT NULL,
  `id_perso` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
