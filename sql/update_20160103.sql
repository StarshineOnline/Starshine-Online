ALTER TABLE `messagerie_message`
  DROP `nom_auteur`,
  DROP `nom_dest`,
  DROP `titre`;
  
ALTER TABLE `messagerie_thread` ADD `categorie` VARCHAR( 20 ) NOT NULL ;

CREATE TABLE IF NOT EXISTS `messagerie_lus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_thread` int(11) NOT NULL,
  `id_debut` int(11) NOT NULL,
  `id_fin` int(11) NOT NULL,
  `id_der_lu` int(11) NOT NULL,
  `id_dest` int(11) NOT NULL,
  `type_dest` enum('perso','roi','eco','mil') NOT NULL DEFAULT 'perso',
  `nbr_msg` int(11) NOT NULL DEFAULT '0',
  `nbr_non_lu` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;