
-- table contenant l'aide de l'interface
CREATE TABLE IF NOT EXISTS `aide` (
  `id` varchar(20) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `texte` text NOT NULL,
  `position` varchar(6) NOT NULL DEFAULT 'bottom',
  `index_tuto` tinyint(4) NOT NULL DEFAULT '0',
  `total_tuto` tinyint(4) NOT NULL DEFAULT '0',
  `precedant_tuto` varchar(20) NOT NULL,
  `suivant_tuto` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Contenu de l'aide (à remplir)
INSERT INTO `aide` (`id`, `titre`, `texte`, `position`, `index_tuto`, `total_tuto`, `precedant_tuto`, `suivant_tuto`) VALUES ('barre_menu', 'Titre à trouver', 'Aide à rédiger', 'bottom', 1, 2, '', 'depl_disque');
INSERT INTO `aide` (`id`, `titre`, `texte`, `position`, `index_tuto`, `total_tuto`, `precedant_tuto`, `suivant_tuto`) VALUES ('depl_disque', 'Titre à trouver', 'Aide à rédiger', 'right', 2, 2, 'barre_menu', '');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('infos_vie', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('infos_perso', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('perso_heure', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('perso_position', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('perso_groupe', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('menu_carte', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('menu_panneaux', 'Titre à trouver', 'Aide à rédiger', 'bottom');
INSERT INTO `aide` (`titre`, `texte`, `position`) VALUES ('carte', 'Titre à trouver', 'Aide à rédiger', 'bottom');