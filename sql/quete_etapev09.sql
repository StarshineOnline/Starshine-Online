-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2015 at 03:31 PM
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
-- Table structure for table `quete_etape`
--

CREATE TABLE IF NOT EXISTS `quete_etape` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `id_quete` int(10) DEFAULT NULL,
  `etape` int(10) NOT NULL DEFAULT '1',
  `variante` int(11) DEFAULT '0',
  `description` text NOT NULL,
  `niveau` int(10) NOT NULL,
  `objectif` text NOT NULL,
  `collaboration` enum('aucune','groupe', 'royaume') NOT NULL,
  `gain_perso` text,
  `gain_groupe` text,
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `quete_etape`
--

INSERT INTO `quete_etape` (`id`, `id_quete`, `etape`, `variante`, `description`, `niveau`, `objectif`, `type`, `gain_perso`, `gain_groupe`) VALUES
(1, 100, 1, NULL, 'premiere quete de la suite', 1, '', 'g', NULL, NULL),
(2, 100, 1, NULL, 'deuxieme quete de la suite', 1, '', 'g', NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
