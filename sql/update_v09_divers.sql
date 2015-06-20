ALTER TABLE `motd` ADD `date` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `echange` ADD `nouveau` BOOLEAN NOT NULL DEFAULT FALSE ;

-- On distingue les sorts ayant le même effets
UPDATE sort_combat SET type = 'degat_feu-1' WHERE nom LIKE 'Toucher de feu%';
UPDATE sort_combat SET type = 'degat_feu-2' WHERE nom LIKE 'Trait de feu%';
UPDATE sort_combat SET type = 'degat_feu-3' WHERE nom LIKE 'Boule de feu%';
UPDATE sort_combat SET type = 'degat_feu-4' WHERE nom LIKE 'Fournaise%';
UPDATE sort_combat SET type = 'degat_mort-1' WHERE nom LIKE 'Trait de mort%';
UPDATE sort_combat SET type = 'degat_mort-2' WHERE nom LIKE 'Destruction Mentale%';
UPDATE sort_combat SET type = 'degat_nature-1' WHERE nom LIKE 'Feuilles tranchantes%';
UPDATE sort_combat SET type = 'degat_nature-2' WHERE nom LIKE 'Epines Géantes%';

-- On désactive la possibilité d'avoir son propre css dans la description du personnage (bonus shine)
UPDATE `bonus` SET `id_categorie` = '0' WHERE `bonus`.`id_bonus` = 27;

-- Options
ALTER TABLE `options` CHANGE `valeur` `valeur` MEDIUMINT(16) UNSIGNED NOT NULL;


-- Bourse des royaumes
ALTER TABLE `bourse_royaume` ADD `type` ENUM('achat','vente') NOT NULL DEFAULT 'vente';
ALTER TABLE `bourse_royaume` CHANGE `id_royaume_acheteur` `id_royaume_acheteur` MEDIUMINT( 10 ) UNSIGNED NOT NULL;


-- Journal des royaumes
CREATE TABLE IF NOT EXISTS `journal_royaume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(20) NOT NULL,
  `time` date NOT NULL,
  `id_perso` int(11) NOT NULL,
  `actif` varchar(50) NOT NULL,
  `id_passif` int(11) NOT NULL,
  `passif` varchar(50) NOT NULL,
  `valeur` varchar(50) NOT NULL,
  `valeur2` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `id_royaume` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Systèmes d'annonce
CREATE TABLE IF NOT EXISTS `annonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `message` varchar(140) NOT NULL,
  `auteur` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;