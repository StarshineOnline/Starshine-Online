--
-- Structure de la table `map_type_calque`
--

CREATE TABLE IF NOT EXISTS `map_type_calque` (
  `type` int(11) NOT NULL,
  `calque` varchar(100) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `decalage_x` int(11) NOT NULL default '0',
  `decalage_y` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `map_type_calque`
--

INSERT INTO `map_type_calque` (`type`, `calque`, `nom`, `decalage_x`, `decalage_y`) VALUES
(4, 'donjon_aquatique/calque_eau_nord_donjon_aqua_120x120.png', 'Eau Nord', 0, 0),
(5, 'donjon_aquatique/calque_eau_centre_donjon_aqua_120x120.png', 'Eau Centre', 0, 0),
(6, 'donjon_aquatique/calque_eau_sud_donjon_aqua_120x120.png', 'Eau Sud', 0, 0);
