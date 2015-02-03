-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2015 at 03:30 PM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `starshine_preprod`
--

-- --------------------------------------------------------

--
-- Table structure for table `quete`
--

CREATE TABLE IF NOT EXISTS `quete` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `fournisseur` enum('bureau_quete','ecole_combat','taverne','alchimiste','université','bar','royaume','special') NOT NULL,
  `type` text NOT NULL,
  `repetable` enum('oui','non') NOT NULL DEFAULT 'non',
  `royaume` varchar(100) NOT NULL,
  `requis` text NOT NULL,
  `star_royaume` int(10) unsigned NOT NULL,
  `nombre_etape` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `quete`
--

INSERT INTO `quete` (`id`, `nom`, `fournisseur`, `type`, `repetable`, `royaume`, `requis`, `star_royaume`, `nombre_etape`, `description`) VALUES
(1, 'Le ragoût de lapin', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(2, 'Sérum à base de venin de serpent', 'bureau_quete', 'groupe', 'non', '', '', 200, 1, ''),
(3, 'Ours noirs', 'bureau_quete', 'groupe', 'non', '', '', 1000, 1, ''),
(4, 'Araignées géantes', 'bureau_quete', 'groupe', 'non', '', '', 2100, 1, ''),
(5, 'Le ragoût de lapin', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(6, 'Sérum à base de venin de serpent', 'bureau_quete', 'groupe', 'non', '', '', 200, 1, ''),
(7, 'Ours noirs', 'bureau_quete', 'groupe', 'non', '', '', 1000, 1, ''),
(8, 'Araignées géantes', 'bureau_quete', 'groupe', 'non', '', '', 2100, 1, ''),
(9, 'L''invasion des belettes', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(10, 'Chasse aux renards', 'bureau_quete', 'groupe', 'non', '', '', 20, 1, ''),
(11, 'Chasse aux Guépards', 'ecole_combat', 'groupe', 'non', '', '', 250, 1, ''),
(12, 'D''effrayants Loup-garou', 'ecole_combat', 'solo', 'non', '', '', 3000, 1, ''),
(13, 'Besoin d''eau de vie urgent !', 'bureau_quete', 'groupe', 'non', '', '', 2800, 1, ''),
(14, 'Le sérum', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(15, 'Chasse aux Pingouins', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(16, 'Je vous donne du monsieur', 'bureau_quete', 'groupe', 'non', '', '', 9300, 1, ''),
(17, 'Chasse aux Worgs', 'ecole_combat', 'groupe', 'non', '', '', 250, 1, ''),
(18, 'Lézard', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(19, 'Salamandre', 'bureau_quete', 'groupe', 'non', '', '', 1, 1, ''),
(20, 'Les renards gris', 'bureau_quete', 'groupe', 'non', '', '', 20, 1, ''),
(21, 'Puma', 'bureau_quete', 'groupe', 'non', '', '', 250, 1, ''),
(22, 'Goblins', 'bureau_quete', 'groupe', 'non', '', '', 1000, 1, ''),
(23, 'Zombie', 'bureau_quete', 'groupe', 'non', '', '', 500, 1, ''),
(24, 'Dryade', 'alchimiste', 'groupe', 'non', '', '', 250, 1, ''),
(25, 'Renard du désert', 'bureau_quete', 'groupe', 'non', '', '', 250, 1, ''),
(26, 'Fourmis soldats', 'bureau_quete', 'groupe', 'non', '', '', 1500, 1, ''),
(27, 'Faucons', 'bureau_quete', 'groupe', 'non', '', '', 20, 1, ''),
(28, 'Fourmis ouvrières', 'bureau_quete', 'groupe', 'non', '', '', 200, 1, ''),
(29, 'Chameaux', 'bureau_quete', 'groupe', 'non', '', '', 15, 1, ''),
(30, 'Crocodile', 'bureau_quete', 'groupe', 'non', '', '', 1500, 1, ''),
(31, 'Araignée des glaces', 'bureau_quete', 'groupe', 'non', '', '', 1500, 1, ''),
(32, 'Cockatrice', 'bureau_quete', 'groupe', 'non', '', '', 2800, 1, ''),
(33, 'Ogre', 'bureau_quete', 'groupe', 'non', '', '', 3000, 1, ''),
(34, 'Roc', 'bureau_quete', 'groupe', 'non', '', '', 2800, 1, ''),
(35, 'Hydre à trois têtes', 'bureau_quete', 'groupe', 'non', '', '', 2800, 1, ''),
(36, 'Cyclopes', 'bureau_quete', 'groupe', 'non', '', '', 5500, 1, ''),
(37, 'Hydre à 5 têtes', 'bureau_quete', 'groupe', 'non', '', '', 6600, 1, ''),
(38, 'Yeti', 'bureau_quete', 'groupe', 'non', '', '', 6600, 1, ''),
(39, 'momie', 'bureau_quete', 'groupe', 'non', '', '', 8000, 1, ''),
(40, 'Ogre à 2 têtes', 'bureau_quete', 'groupe', 'non', '', '', 10000, 1, ''),
(41, 'Elémentaire d''air', 'ecole_combat', 'groupe', 'non', '', '', 2500, 1, ''),
(42, 'Elementaire de terre', 'ecole_combat', 'groupe', 'non', '', '', 2500, 1, ''),
(43, 'Elementaire de feu', 'ecole_combat', 'groupe', 'non', '', '', 2500, 1, ''),
(44, 'Elementaire d''eau', 'ecole_combat', 'groupe', 'non', '', '', 2500, 1, ''),
(45, 'Tigre', 'bureau_quete', 'groupe', 'non', '', '', 2500, 1, ''),
(46, 'Succubus', 'bureau_quete', 'groupe', 'non', '', '', 2000, 1, ''),
(47, 'Prouve ta valeur', 'ecole_combat', 'solo', 'non', '', '', 5000, 1, ''),
(48, 'Vautours', 'bureau_quete', 'groupe', 'non', '', '', 1500, 1, ''),
(49, 'Scarabés', 'bureau_quete', 'groupe', 'non', '', '', 200, 1, ''),
(50, 'Sworling le téléporteur', 'bureau_quete', 'solo', 'non', '', '', 0, 1, ''),
(51, 'Le réveil des ents', 'bureau_quete', 'groupe', 'non', '', '', 2800, 1, ''),
(52, 'A la recherche d''informations', '', 'solo', 'non', '', '', 0, 1, ''),
(53, 'Parler à Dryasaïde', '', 'solo', 'non', '', '', 0, 1, ''),
(54, 'De quoi tu es capable', '', 'solo', 'non', '', '', 0, 1, ''),
(55, 'De quoi tu es capable suite', '', 'groupe', 'non', '', '', 1, 1, ''),
(56, 'De quoi tu es capable fin', '', 'solo', 'non', '', '', 0, 1, ''),
(57, 'Chasse aux Basilics', 'bureau_quete', 'groupe', 'non', '', '', 10240, 1, ''),
(58, 'Ramener mes Lampes !', 'alchimiste', 'groupe', 'non', '', '', 12960, 1, ''),
(59, 'une étrange mutation', 'bureau_quete', 'groupe', 'non', '', '', 4840, 1, ''),
(60, 'bandits de grand chemin', 'bureau_quete', 'groupe', 'non', '', '', 4840, 1, ''),
(61, 'la révolte gronde ', 'bureau_quete', 'groupe', 'non', '', '', 4840, 1, ''),
(62, 'une mission facile', 'bureau_quete', 'groupe', 'non', '', '', 5760, 1, ''),
(63, 'les éclaireurs', 'bureau_quete', 'groupe', 'non', '', '', 6760, 1, ''),
(64, 'la révolte gronde ( suite )', 'bureau_quete', 'groupe', 'non', '', '', 6760, 1, ''),
(65, 'les légions démoniaques', 'bureau_quete', 'groupe', 'non', '', '', 7840, 1, ''),
(66, 'les âmes damnées', 'bureau_quete', 'groupe', 'non', '', '', 9000, 1, ''),
(67, 'les maïtres de la fourmillière', 'bureau_quete', 'groupe', 'non', '', '', 8000, 1, ''),
(68, 'réalité d''un cauchemar', 'bureau_quete', 'groupe', 'non', '', '', 9000, 1, ''),
(69, 'Chasseurs de peaux', 'bureau_quete', 'groupe', 'non', '', '', 9000, 1, ''),
(70, 'l''aiguille écarlate', 'bureau_quete', 'groupe', 'non', '', '', 10240, 1, ''),
(71, 'mefiez vous de l''eau qui dort', 'bureau_quete', 'groupe', 'non', '', '', 10240, 1, ''),
(72, 'la menace volante', 'bureau_quete', 'groupe', 'non', '', '', 11560, 1, ''),
(73, 'l''appel de la mort', 'bureau_quete', 'groupe', 'non', '', '', 11560, 1, ''),
(74, 'corruption elfique', 'bureau_quete', 'groupe', 'non', '', '', 12960, 1, ''),
(75, 'Chair de chitineux', 'bureau_quete', 'groupe', 'non', '', '', 12960, 1, ''),
(76, 'Licornes', 'bureau_quete', 'groupe', 'non', '', '', 36000, 1, ''),
(77, 'Les sabots de l''enfer', 'bureau_quete', 'groupe', 'non', '', '', 36000, 1, ''),
(78, 'Gorgone ', 'bureau_quete', 'groupe', 'non', '', '', 45000, 1, ''),
(79, 'anciens de la forêt', 'bureau_quete', 'groupe', 'non', '', '', 45000, 1, ''),
(80, 'Martichoras', 'bureau_quete', 'groupe', 'non', '', '', 40000, 1, ''),
(81, 'Succubus ', 'bureau_quete', 'groupe', 'non', '', '', 40000, 1, ''),
(82, 'Serpent a plumes', 'bureau_quete', 'groupe', 'non', '', '', 40000, 1, ''),
(83, 'Abomination', 'bureau_quete', 'groupe', 'non', '', '', 40000, 1, ''),
(84, 'un cri dans la nuit', 'bureau_quete', 'groupe', 'non', '', '', 50000, 1, ''),
(85, 'Les harpies', 'bureau_quete', 'groupe', 'non', '', '', 50000, 1, ''),
(86, 'Lézards Voltaïque', 'bureau_quete', 'groupe', 'non', '', '', 50000, 1, ''),
(87, 'Nouvelle carte', 'taverne', 'solo', 'non', '', '', 1, 1, ''),
(88, 'Exploration des profondeurs', '', 'solo', 'non', '', '', 5000, 1, ''),
(89, 'Recherche d''une substance rare', '', 'solo', 'non', '', '', 5000, 1, ''),
(90, 'Améliorations des relations diplomatiques', '', 'solo', 'non', '', '', 5000, 1, ''),
(91, 'Exploration des strates inférieures', '', 'solo', 'non', '', '', 5000, 1, ''),
(92, 'Cache-cache', '', 'solo', 'non', '', '', 0, 1, ''),
(93, 'Une pêche dangereuse', '', 'solo', 'non', '', '', 1000, 1, ''),
(94, 'Au nom du handicap !', '', 'groupe', 'non', '', '', 50000, 1, ''),
(95, 'L''expulsion générale', '', 'groupe', 'non', '', '', 500, 1, ''),
(96, 'Chez Truffaut', '', 'groupe', 'non', '', '', 1000, 1, ''),
(97, 'Les retrouvailles', '', 'groupe', 'non', '', '', 5000, 1, ''),
(98, 'A bas la dictature Kesalys', 'bureau_quete', 'groupe', 'non', '', '', 10000, 1, ''),
(99, 'Des escarres anciens', '', 'solo', 'non', '', '', 10000, 1, ''),
(100, 'Première suite de quete', 'bureau_quete', 'groupe', 'non', '', 'n>|12', 1, 4, 'Ceci est la première suite de quete');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
