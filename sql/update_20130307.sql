-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 07 Mars 2013 à 10:31
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `starshine_jeu`
--

-- --------------------------------------------------------

--
-- Structure de la table `texte_tutoriel`
--

CREATE TABLE IF NOT EXISTS `texte_tutoriel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `etape` int(11) NOT NULL,
  `race` varchar(256) CHARACTER SET utf8 NOT NULL,
  `classe` int(10) unsigned NOT NULL DEFAULT '1',
  `titre` varchar(255) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `texte_tutoriel`
--

INSERT INTO `texte_tutoriel` (`id`, `etape`, `race`, `classe`, `titre`, `text`) VALUES
(1, 1, 'humainnoir', 1, 'L''arrivée dans Starshine', '<br/>\r\nNarcisse, le soleil vient de laisser place aux lanternes et aux lumières des maisons avoisinantes. Vous errez dans les rues sombres et étroites.  Alors que vous prenez une petite voie peu éclairée, vous entendez un murmure derrière vous. Quiconque ayant vécu à Narcisse, sait que cela n’augure rien de bon. Vous continuez votre chemin, et les murmures se tuent. \r\nC’est le signal ! Vous devez vous enfuir ! \r\n\r\n<i>Afin de vous déplacer, il faut d’abord vous repérer. Pour cela, une icône vous représente dans la ville sur la carte. Une fois trouvé, remarquez la rue qui part vers le nord. Afin de vous déplacer dans cette rue, utilisez les flèches de la rose des vents en haut à gauche. Dirigez-vous vers le nord pour échapper à vos poursuivants.\r\n\r\nVous pourrez retrouver ces indications simplement en actualisant cette page.</i>\r\n\r\n<input type=''submit'' onclick="fermePopUp(); return false;" value=''Courrir !''>'),
(2, 2, 'humainnoir', 1, 'Fuite', '<br/>\r\nVous vous mettez à courir comme jamais. Vous cherchez à les semer, mais rien n’y fait, en jetant un œil derrière vous, les deux silhouettes ne vous lâchent pas.\r\n\r\nDans votre fuite, vous remarquez  qu’une rue part vers l’est. Il faut y aller et vous cacher derrière des marchandises.\r\n\r\n\r\n<i>Déplacer vous dans cette rue jusqu’à ce qu’apparaissent des marchandises sur la carte. La carte est divisée en cases repérées dans le monde de Starshine par leurs coordonnées inscrites autour de la carte. Chaque case peut être différente. Afin de connaitre ce qu’il y a sur une case et d’interagir avec, il faut cliquer dessus. Une fois a proximité des marchandises, cliquez sur la case représentant les marchandises, puis cachez-vous.\r\n<i>\r\n\r\n<input type=''submit'' onclick="fermePopUp(); return false;" value=''Allez se cacher''>'),
(4, 3, 'humainnoir', 1, 'A la recherche d''un mentor', '<br/> Les brigands sont partis, vous devriez vous diriger vers le sud pour trouver votre maitre d''armes, lui sauras vous aider à survivre dans Narcisse et qui sait, à découvrir le monde de Starshine !');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
--
-- Structure de la table `perso`
--

ALTER TABLE `perso` ADD `tuto` INT( 10 ) NOT NULL DEFAULT '0';
