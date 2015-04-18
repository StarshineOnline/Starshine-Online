-- Modification de la table des buffs
ALTER TABLE `buff_batiment` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `buff_batiment` CHANGE `date_fin` `fin` INT(11) NOT NULL;
ALTER TABLE `buff_batiment` ADD `effet2` INT(11) NOT NULL DEFAULT '0' , ADD `nom` VARCHAR(50) NOT NULL , ADD `description` TEXT NOT NULL , ADD `debuff` BOOLEAN NOT NULL;
ALTER TABLE `buff_batiment` ADD `id_perso` INT NOT NULL DEFAULT '0';

-- Modification de la compétence de rang 4
UPDATE comp_jeu SET cible = 7 WHERE type LIKE 'sabotage';

-- Définitions des (de)buffs de bâtiments
CREATE TABLE IF NOT EXISTS `buff_batiment_def` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `effet` int(11) NOT NULL,
  `effet2` int(11) NOT NULL DEFAULT '0',
  `duree` int(11) NOT NULL,
  `description` text NOT NULL,
  `debuff` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- (de)buffs de bâtiments
INSERT INTO `starshine_simul`.`buff_batiment_def` (`id`, `nom`, `type`, `effet`, `effet2`, `duree`, `description`, `debuff`) VALUES ('1', 'Assiégé', 'assiege', '0', '0', '259200', 'Empêche la suppression du bâtiment tant qu''il est assiégé.', '1');