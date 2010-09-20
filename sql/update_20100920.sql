-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Serveur: 127.0.0.1
-- Généré le : Lun 20 Septembre 2010 à 12:37
-- Version du serveur: 5.1.49
-- Version de PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `starshine`
--

-- --------------------------------------------------------

--
-- Structure de la table `bataille_repere_type`
--

DROP TABLE IF EXISTS `bataille_repere_type`;
CREATE TABLE IF NOT EXISTS `bataille_repere_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ajout_groupe` tinyint(3) unsigned NOT NULL,
  `image` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Contenu de la table `bataille_repere_type`
--

INSERT INTO `bataille_repere_type` (`id`, `nom`, `description`, `ajout_groupe`, `image`) VALUES
(5, 'Attaque', 'Ordonner à un ou plusieurs groupe d''attaquer une position, des ennemis, des batiments...', 0, ''),
(6, 'Défense', 'Ordonner à un ou plusieurs groupe de défendre une position, des alliés, des bâtiments...', 0, ''),
(7, 'Déplacement', 'Ordonner à un ou plusieurs groupe de se rendre à la position indiquée.', 0, ''),
(8, 'Réparation', 'Ordonner à un ou plusieurs groupe de réparer un bâtiment.', 0, ''),
(9, 'Construction', 'Ordonner à un ou plusieurs groupe de construire un batiment, une arme de siège, à la position indiquée.', 0, '');


ALTER TABLE `bataille_groupe` ADD `id_thread` INT NOT NULL